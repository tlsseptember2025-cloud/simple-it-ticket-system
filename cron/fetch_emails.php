<?php

require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/mailer.php';

/* ================= CONFIG ================= */

$allowedDomain = '@loopsautomation.com';

$imapHost = '{imap.gmail.com:993/imap/ssl}INBOX';
$imapUser = 'rami.wahdan@loopsautomation.com';
$imapPass = 'svyh dqhe rlif dygv'; // app password

/* ================= HELPERS ================= */

function normalizeSubject(string $subject): string
{
    return trim(preg_replace('/^(re|fw|fwd)\s*:\s*/i', '', $subject));
}

/* ================= BODY ================= */

function extractBodyRecursive($mailbox, $emailNumber, $structure, $partNumber = '')
{
    if ($structure->type == 0) {
        $body = imap_fetchbody($mailbox, $emailNumber, $partNumber ?: 1);
        if ($structure->encoding == 3) $body = base64_decode($body);
        if ($structure->encoding == 4) $body = quoted_printable_decode($body);

        if (in_array(strtolower($structure->subtype), ['plain','html'])) {
            return trim(strip_tags($body));
        }
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

function cleanEmailBody(string $text): string
{
    $text = preg_split('/\ROn .* wrote:\R/i', $text)[0];
    $text = preg_split('/\R--\R/', $text)[0];
    return trim($text);
}

/* ================= ATTACHMENTS ================= */

function extractAttachments($mailbox, $emailNumber, $structure, $partNumber, $ticketId, $pdo)
{
    $filename = null;

    foreach (['dparameters','parameters'] as $ptype) {
        if (!empty($structure->$ptype)) {
            foreach ($structure->$ptype as $p) {
                if (in_array(strtolower($p->attribute), ['filename','name'])) {
                    $filename = $p->value;
                }
            }
        }
    }

    if ($filename) {
        $body = imap_fetchbody($mailbox, $emailNumber, $partNumber ?: 1);
        if ($structure->encoding == 3) $body = base64_decode($body);
        if ($structure->encoding == 4) $body = quoted_printable_decode($body);

        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if ($body && in_array($ext, ['jpg','jpeg','png','gif','pdf'])) {

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

if (!$mailbox) {
    // imap_open failed → nothing to close
    return;
}


$emails = imap_search($mailbox, 'ALL');
//if (!$emails) exit;
if (!$emails) {
    imap_close($mailbox);
    return;
}

foreach ($emails as $emailNumber) {

    $headers = imap_rfc822_parse_headers(imap_fetchheader($mailbox, $emailNumber));
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

    $subjectClean = normalizeSubject($overview->subject ?? '(No Subject)');
    $structure = imap_fetchstructure($mailbox, $emailNumber);

    $body = cleanEmailBody(
        extractBodyRecursive($mailbox, $emailNumber, $structure)
    );

    /* ===== FIND TICKET NUMBER ===== */
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

    /* ===== NEW TICKET ===== */
    $ticketNumber = 'LA-Support-'.date('Y').'-'.strtoupper(bin2hex(random_bytes(4)));

    $pdo->prepare("
        INSERT INTO tickets (ticket_number,sender_email,subject,message)
        VALUES (?, ?, ?, ?)
    ")->execute([$ticketNumber,$fromEmail,$subjectClean,$body]);

    $ticketId = $pdo->lastInsertId();

    extractAttachments($mailbox, $emailNumber, $structure, '', $ticketId, $pdo);

    sendMail(
        $fromEmail,
        "Ticket Created: $ticketNumber",
        "Your request has been received.\nTicket Number: $ticketNumber"
    );

    $pdo->prepare("INSERT IGNORE INTO processed_emails (message_id) VALUES (?)")
        ->execute([$messageId]);
}

imap_close($mailbox);