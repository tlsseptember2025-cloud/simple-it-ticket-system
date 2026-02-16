<?php

require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/mailer.php';

/* ================= CONFIG ================= */

$allowedDomain = '@loopsautomation.com';

$imapHost = '{imap.gmail.com:993/imap/ssl}INBOX';
$imapUser = 'rami.wahdan@loopsautomation.com';
$imapPass = 'svyh dqhe rlif dygv';

/* ================= HELPERS ================= */

function normalizeSubject(string $subject): string
{
    return trim(preg_replace('/^(re|fw|fwd)\s*:\s*/i', '', $subject));
}

function cleanMessage(string $text): string
{
    $text = str_replace(["\r\n", "\r"], "\n", $text);
    $text = preg_replace('/\[image:.*?\]/i', '', $text);
    $text = preg_replace('/^>.*$/m', '', $text);
    $text = preg_split('/\ROn .* wrote:\R/i', $text)[0];
    $text = preg_split('/\R--\R/', $text)[0];

    $signatures = [
        '/best regards.*/is',
        '/kind regards.*/is',
        '/regards.*/is',
        '/thanks.*/is',
        '/thank you.*/is',
        '/sent from my.*/is',
        '/www\..*/is',
    ];

    foreach ($signatures as $s) {
        $text = preg_replace($s, '', $text);
    }

    return trim(preg_replace("/\n{3,}/", "\n\n", $text));
}

/* ================= BODY ================= */

function extractBodyRecursive($mailbox, $emailNumber, $structure, $partNumber = '')
{
    if ($structure->type == 0) {
        $body = imap_fetchbody($mailbox, $emailNumber, $partNumber ?: 1);
        if ($structure->encoding == 3) $body = base64_decode($body);
        if ($structure->encoding == 4) $body = quoted_printable_decode($body);
        return trim(strip_tags($body));
    }

    if (!empty($structure->parts)) {
        foreach ($structure->parts as $i => $sub) {
            $res = extractBodyRecursive(
                $mailbox,
                $emailNumber,
                $sub,
                $partNumber ? "$partNumber.".($i+1) : ($i+1)
            );
            if ($res !== '') return $res;
        }
    }

    return '';
}

/* ================= ATTACHMENTS ================= */

function extractAttachments($mailbox, $emailNumber, $structure, $partNumber, $ticketId, $pdo)
{
    $filename = null;

    // Try to get filename normally
    foreach (['dparameters','parameters'] as $ptype) {
        if (!empty($structure->$ptype)) {
            foreach ($structure->$ptype as $p) {
                if (in_array(strtolower($p->attribute), ['filename','name'])) {
                    $filename = $p->value;
                }
            }
        }
    }

    // If no filename but it's an image → generate one
    if (!$filename && $structure->type == 5) { // TYPE IMAGE
        $ext = strtolower($structure->subtype ?? 'jpg');
        $filename = 'inline_' . uniqid() . '.' . $ext;
    }

    if ($filename) {

        $body = imap_fetchbody($mailbox, $emailNumber, $partNumber ?: 1);

        if ($structure->encoding == 3) $body = base64_decode($body);
        if ($structure->encoding == 4) $body = quoted_printable_decode($body);

        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $allowed = [
            'jpg','jpeg','png','gif','pdf',
            'doc','docx',
            'xls','xlsx',
            'csv',
            'txt',
            'zip'
        ];

        if ($body && in_array($ext, $allowed)) {

            $dir = __DIR__ . '/../uploads/tickets/';
            if (!is_dir($dir)) mkdir($dir, 0777, true);

            $safe = uniqid().'_'.preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
            file_put_contents($dir.$safe, $body);

            $pdo->prepare("
                INSERT INTO attachments (ticket_id, filename, filepath)
                VALUES (?, ?, ?)
            ")->execute([$ticketId, $filename, 'uploads/tickets/'.$safe]);
        }
    }

    if (!empty($structure->parts)) {
        foreach ($structure->parts as $i => $sub) {
            extractAttachments(
                $mailbox,
                $emailNumber,
                $sub,
                $partNumber ? "$partNumber.".($i+1) : ($i+1),
                $ticketId,
                $pdo
            );
        }
    }
}

/* ================= PROCESS MAIL ================= */

$mailbox = imap_open($imapHost, $imapUser, $imapPass);
if (!$mailbox) return;

$emails = imap_search($mailbox, 'ALL');
if (!$emails) {
    imap_close($mailbox);
    return;
}

foreach ($emails as $emailNumber) {

    $headers  = imap_rfc822_parse_headers(imap_fetchheader($mailbox, $emailNumber));
    $overview = imap_fetch_overview($mailbox, $emailNumber, 0)[0];

    $messageId = $headers->message_id ?? null;
    if ($messageId) {
        $chk = $pdo->prepare("SELECT 1 FROM processed_emails WHERE message_id=?");
        $chk->execute([$messageId]);
        if ($chk->fetch()) continue;
    }

    $fromEmail = strtolower($headers->from[0]->mailbox.'@'.$headers->from[0]->host);
    if (!str_ends_with($fromEmail, $allowedDomain)) continue;
    if ($fromEmail === strtolower($imapUser)) continue;

    $toMatch = false;
    if (!empty($headers->to)) {
        foreach ($headers->to as $to) {
            $toEmail = strtolower($to->mailbox . '@' . $to->host);
            if ($toEmail === strtolower($imapUser)) {
                $toMatch = true;
                break;
            }
        }
    }

    if (!$toMatch) continue;

    $subjectClean = normalizeSubject($overview->subject ?? '(No Subject)');
    $originalSubject = $overview->subject ?? '';
    $structure = imap_fetchstructure($mailbox, $emailNumber);

    $body = cleanMessage(
        extractBodyRecursive($mailbox, $emailNumber, $structure)
    );

    /* ===== EXISTING TICKET ===== */
    if (preg_match('/LA-Support-\d{4}-[A-Z0-9]+/', $subjectClean, $m)) {

        $stmt = $pdo->prepare("SELECT id,status FROM tickets WHERE ticket_number=?");
        $stmt->execute([$m[0]]);
        $ticket = $stmt->fetch();

        if ($ticket) {

            if ($ticket['status'] === 'Closed') {
                sendMail(
                    $fromEmail,
                    "Ticket Closed - {$m[0]}",
                    "This ticket is already CLOSED.\n\nPlease open a new ticket."
                );
            } else {
                $pdo->prepare("
                    INSERT INTO updates (ticket_id,message,sent_to_user)
                    VALUES (?, ?, 0)
                ")->execute([$ticket['id'], $body]);

                extractAttachments($mailbox, $emailNumber, $structure, '', $ticket['id'], $pdo);
            }

            $pdo->prepare("INSERT IGNORE INTO processed_emails (message_id) VALUES (?)")
                ->execute([$messageId]);

            continue;
        }
    }

    /* ===== IGNORE NON-TICKET REPLIES ===== */
    if (preg_match('/^(re|fw|fwd)\s*:/i', $originalSubject)) {
        continue;
    }

    /* ===== NEW TICKET ===== */

    $ticketNumber = 'LA-Support-'.date('Y').'-'.strtoupper(bin2hex(random_bytes(4)));
    $statusToken  = bin2hex(random_bytes(16));

    $pdo->prepare("
        INSERT INTO tickets (
            ticket_number,
            sender_email,
            subject,
            message,
            status,
            status_token,
            status_updated_at
        )
        VALUES (?, ?, ?, ?, 'Open', ?, NOW())
    ")->execute([
        $ticketNumber,
        $fromEmail,
        $subjectClean,
        $body,
        $statusToken
    ]);

    $ticketId = $pdo->lastInsertId();

    extractAttachments($mailbox, $emailNumber, $structure, '', $ticketId, $pdo);

    sendMail(
        $fromEmail,
        "Ticket Created: $ticketNumber",
        "Your request has been received.\n\nTicket Number: $ticketNumber"
    );

    $pdo->prepare("INSERT IGNORE INTO processed_emails (message_id) VALUES (?)")
        ->execute([$messageId]);
}

imap_close($mailbox);