<?php

require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/mailer.php';

/* ================= CONFIG ================= */

$allowedDomain = '@loopsautomation.com';

$imapHost = '{imap.gmail.com:993/imap/ssl}INBOX';
$imapUser = 'rami.wahdan@loopsautomation.com';
$imapPass = 'svyh dqhe rlif dygv'; // app password

/* ============== BODY EXTRACTION ================= */

function extractBodyRecursive($mailbox, $emailNumber, $structure, $partNumber = '')
{
    if ($structure->type == 0) {

        $body = imap_fetchbody($mailbox, $emailNumber, $partNumber ?: 1);

        if ($structure->encoding == 3) $body = base64_decode($body);
        if ($structure->encoding == 4) $body = quoted_printable_decode($body);

        if (strtolower($structure->subtype) === 'plain') {
            return trim($body);
        }

        if (strtolower($structure->subtype) === 'html') {
            return trim(strip_tags($body));
        }
    }

    if (isset($structure->parts)) {
        foreach ($structure->parts as $i => $sub) {
            $result = extractBodyRecursive(
                $mailbox,
                $emailNumber,
                $sub,
                $partNumber ? $partNumber . '.' . ($i + 1) : ($i + 1)
            );
            if ($result !== '') return $result;
        }
    }

    return '';
}

function cleanEmailBody($text)
{
    $text = preg_replace('/\[image:.*?\]/i', '', $text);
    $text = preg_split('/\ROn .* wrote:\R/i', $text)[0];
    $text = preg_split('/\R--\R/', $text)[0];

    $signatures = [
        '/best regards.*/is',
        '/kind regards.*/is',
        '/regards.*/is',
        '/thanks.*/is',
        '/thank you.*/is',
        '/sent from my.*/is',
    ];

    foreach ($signatures as $s) {
        $text = preg_replace($s, '', $text);
    }

    return trim(preg_replace("/\n{3,}/", "\n\n", $text));
}

/* ============== ATTACHMENTS ================= */

function extractAttachments($mailbox, $emailNumber, $structure, $partNumber, $ticketId, $pdo)
{
    $filename = null;

    foreach (['dparameters','parameters'] as $paramType) {
        if (isset($structure->$paramType)) {
            foreach ($structure->$paramType as $param) {
                if (in_array(strtolower($param->attribute), ['filename','name'])) {
                    $filename = $param->value;
                }
            }
        }
    }

    if ($filename) {
        $body = imap_fetchbody($mailbox, $emailNumber, $partNumber ?: 1);

        if ($structure->encoding == 3) $body = base64_decode($body);
        if ($structure->encoding == 4) $body = quoted_printable_decode($body);

        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','pdf'];

        if ($body && in_array($ext, $allowed)) {
            $dir = __DIR__ . '/../uploads/tickets/';
            if (!is_dir($dir)) mkdir($dir, 0777, true);

            $safe = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
            file_put_contents($dir . $safe, $body);

            $pdo->prepare("
                INSERT INTO attachments (ticket_id, filename, filepath)
                VALUES (?, ?, ?)
            ")->execute([$ticketId, $filename, 'uploads/tickets/' . $safe]);
        }
    }

    if (isset($structure->parts)) {
        foreach ($structure->parts as $i => $sub) {
            extractAttachments(
                $mailbox,
                $emailNumber,
                $sub,
                $partNumber ? $partNumber . '.' . ($i + 1) : ($i + 1),
                $ticketId,
                $pdo
            );
        }
    }
}

/* ============== PROCESS MAIL ================= */

$mailbox = imap_open($imapHost, $imapUser, $imapPass);
if (!$mailbox) exit;

$emails = imap_search($mailbox, 'ALL');
if (!$emails) exit;

foreach ($emails as $emailNumber) {

    $overview = imap_fetch_overview($mailbox, $emailNumber, 0)[0];
    $headers  = imap_rfc822_parse_headers(imap_fetchheader($mailbox, $emailNumber));

    $messageId = $headers->message_id ?? null;
    if ($messageId) {
        $chk = $pdo->prepare("SELECT 1 FROM processed_emails WHERE message_id = ?");
        $chk->execute([$messageId]);
        if ($chk->fetch()) continue;
    }

    $fromEmail = strtolower($headers->from[0]->mailbox . '@' . $headers->from[0]->host);

    if (!str_ends_with($fromEmail, $allowedDomain)) continue;
    if ($fromEmail === strtolower($imapUser)) continue;

    $subject   = $overview->subject ?? '(No Subject)';
    $structure = imap_fetchstructure($mailbox, $emailNumber);

    $rawBody = extractBodyRecursive($mailbox, $emailNumber, $structure);
    $body    = cleanEmailBody($rawBody);

    // 🔑 Find ticket number in subject or body
    $ticketNumber = null;
    if (preg_match('/IT-\d{4}-[A-Z0-9]+/', $subject, $m)) {
        $ticketNumber = $m[0];
    } elseif (preg_match('/IT-\d{4}-[A-Z0-9]+/', $rawBody, $m)) {
        $ticketNumber = $m[0];
    }

    /* ========= REPLY ========= */
    if ($ticketNumber) {

        $stmt = $pdo->prepare("SELECT id, status FROM tickets WHERE ticket_number = ?");
        $stmt->execute([$ticketNumber]);
        $ticket = $stmt->fetch();

        if ($ticket) {

            // 🚫 CLOSED ticket → reminder only
            if ($ticket['status'] === 'Closed') {

                $emailBody  = "This ticket is already CLOSED.\n\n";
                $emailBody .= "Ticket Number: {$ticketNumber}\n\n";
                $emailBody .= "If you still need assistance, please OPEN A NEW TICKET by sending a new email.";

                sendMail(
                    $fromEmail,
                    "Ticket Closed - Please Open a New Ticket ({$ticketNumber})",
                    $emailBody
                );

                if ($messageId) {
                    $pdo->prepare("
                        INSERT IGNORE INTO processed_emails (message_id)
                        VALUES (?)
                    ")->execute([$messageId]);
                }

                continue; // 🔥 HARD STOP
            }

            // ✅ OPEN ticket → add update
            $pdo->prepare("
                INSERT INTO updates (ticket_id, message, sent_to_user)
                VALUES (?, ?, 0)
            ")->execute([$ticket['id'], $body]);

            extractAttachments($mailbox, $emailNumber, $structure, '', $ticket['id'], $pdo);

            if ($messageId) {
                $pdo->prepare("
                    INSERT IGNORE INTO processed_emails (message_id)
                    VALUES (?)
                ")->execute([$messageId]);
            }

            continue; // 🔥 PREVENT NEW TICKET
        }
    }

    /* ========= NEW TICKET ========= */

    $newTicketNumber = 'IT-' . date('Y') . '-' . strtoupper(uniqid());

    $pdo->prepare("
        INSERT INTO tickets (ticket_number, sender_email, subject, message)
        VALUES (?, ?, ?, ?)
    ")->execute([$newTicketNumber, $fromEmail, $subject, $body]);

    $ticketId = $pdo->lastInsertId();

    extractAttachments($mailbox, $emailNumber, $structure, '', $ticketId, $pdo);

    sendMail(
        $fromEmail,
        "Ticket Created: $newTicketNumber",
        "Your request has been received.\n\nTicket Number: $newTicketNumber"
    );

    if ($messageId) {
        $pdo->prepare("
            INSERT IGNORE INTO processed_emails (message_id)
            VALUES (?)
        ")->execute([$messageId]);
    }
}

imap_close($mailbox);
