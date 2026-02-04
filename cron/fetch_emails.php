
<?php

//file_put_contents(__DIR__ . '/debug.log', "SCRIPT START\n", FILE_APPEND);

require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/mailer.php';

/* ================= CONFIG ================= */

$allowedDomain = '@loopsautomation.com';

$imapHost = '{imap.gmail.com:993/imap/ssl}INBOX';
$imapUser = 'rami.wahdan@loopsautomation.com';
$imapPass = 'svyh dqhe rlif dygv'; // replace safely

/* ============== HELPERS =================== */

/**
 * Get clean reply body (first message only)
 */
function getCleanBody($mailbox, $emailNumber)
{
    $structure = imap_fetchstructure($mailbox, $emailNumber);

    if (!isset($structure->parts)) {
        return trim(imap_body($mailbox, $emailNumber));
    }

    foreach ($structure->parts as $i => $part) {
        if ($part->type == 0 && strtolower($part->subtype) === 'plain') {

            $body = imap_fetchbody($mailbox, $emailNumber, $i + 1);

            if ($part->encoding == 3) $body = base64_decode($body);
            if ($part->encoding == 4) $body = quoted_printable_decode($body);

            // Remove quoted replies
            $body = preg_split('/\ROn .* wrote:\R/i', $body)[0];
            $body = preg_split('/\RFrom:.*\R/i', $body)[0];

            return trim($body);
        }
    }

    return '';
}

/**
 * Recursively extract inline screenshots & image attachments
 */

function extractAttachments($mailbox, $emailNumber, $structure, $partNumber, $ticketId, $pdo)
{
    $isAttachment = false;
    $filename = null;

    // Filename from headers
    if (isset($structure->dparameters)) {
        foreach ($structure->dparameters as $param) {
            if (strtolower($param->attribute) === 'filename') {
                $filename = $param->value;
                $isAttachment = true;
            }
        }
    }

    if (isset($structure->parameters)) {
        foreach ($structure->parameters as $param) {
            if (strtolower($param->attribute) === 'name') {
                $filename = $param->value;
                $isAttachment = true;
            }
        }
    }

    // Inline images without filename
    if (!$filename && $structure->type == 5 && isset($structure->subtype)) {
        $filename = 'image_' . uniqid() . '.' . strtolower($structure->subtype);
        $isAttachment = true;
    }

    if ($isAttachment) {

        $body = imap_fetchbody($mailbox, $emailNumber, $partNumber);

        if ($structure->encoding == 3) {
            $body = base64_decode($body);
        } elseif ($structure->encoding == 4) {
            $body = quoted_printable_decode($body);
        }

        if ($body && strlen($body) > 5 * 1024) {

            $uploadDir = __DIR__ . '/../uploads/tickets/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $safeName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
            file_put_contents($uploadDir . $safeName, $body);

            $pdo->prepare("
                INSERT INTO attachments (ticket_id, filename, filepath)
                VALUES (?, ?, ?)
            ")->execute([
                $ticketId,
                $filename,
                'uploads/tickets/' . $safeName
            ]);
        }
    }

    // Recurse
    if (isset($structure->parts)) {
        foreach ($structure->parts as $i => $subPart) {
            extractAttachments(
                $mailbox,
                $emailNumber,
                $subPart,
                $partNumber . '.' . ($i + 1),
                $ticketId,
                $pdo
            );
        }
    }
}

/* ============== PROCESS MAIL ================= */

$mailbox = imap_open($imapHost, $imapUser, $imapPass);
if (!$mailbox) exit;

// IMPORTANT: Fetch ALL emails, not UNSEEN
$emails = imap_search($mailbox, 'ALL');
if (!$emails) {
    imap_close($mailbox);
    exit;
}

foreach ($emails as $emailNumber) {

    $overview = imap_fetch_overview($mailbox, $emailNumber, 0)[0];
    $headers  = imap_rfc822_parse_headers(imap_fetchheader($mailbox, $emailNumber));

    // ===== MESSAGE-ID DEDUPLICATION =====
    $messageId = $headers->message_id ?? null;

    if ($messageId) {
        $stmt = $pdo->prepare("
            SELECT 1 FROM processed_emails WHERE message_id = ?
        ");
        $stmt->execute([$messageId]);
        if ($stmt->fetch()) {
            continue; // already handled
        }
    }

    $fromEmail = strtolower(
        $headers->from[0]->mailbox . '@' . $headers->from[0]->host
    );

    // Ignore non-company emails
    if (!str_ends_with($fromEmail, $allowedDomain)) {
        continue;
    }

    // Ignore system emails (prevents loops)
    if ($fromEmail === strtolower($imapUser)) {
        continue;
    }

    $subject = $overview->subject ?? '(No Subject)';
    $body    = getCleanBody($mailbox, $emailNumber);

    // Detect existing ticket
    preg_match('/IT-\d{4}-[A-Z0-9]+/', $subject, $m);
    $ticketNumber = $m[0] ?? null;

    /* ---------- REPLY ---------- */
    if ($ticketNumber) {
        $stmt = $pdo->prepare("SELECT id FROM tickets WHERE ticket_number = ?");
        $stmt->execute([$ticketNumber]);
        $ticket = $stmt->fetch();

        if ($ticket) {
            $pdo->prepare("
                INSERT INTO updates (ticket_id, message, sent_to_user)
                VALUES (?, ?, 0)
            ")->execute([$ticket['id'], $body]);

            $structure = imap_fetchstructure($mailbox, $emailNumber);
            extractAttachments($mailbox, $emailNumber, $structure, '1', $ticket['id'], $pdo);

            // Mark processed
            if ($messageId) {
                $pdo->prepare("
                    INSERT IGNORE INTO processed_emails (message_id)
                    VALUES (?)
                ")->execute([$messageId]);
            }

            continue;
        }
    }

    /* ---------- NEW TICKET ---------- */
    $newTicketNumber = 'IT-' . date('Y') . '-' . strtoupper(uniqid());

    $pdo->prepare("
        INSERT INTO tickets (ticket_number, sender_email, subject, message)
        VALUES (?, ?, ?, ?)
    ")->execute([$newTicketNumber, $fromEmail, $subject, $body]);

    $ticketId = $pdo->lastInsertId();

    $structure = imap_fetchstructure($mailbox, $emailNumber);
    extractAttachments($mailbox, $emailNumber, $structure, '1', $ticketId, $pdo);

    sendMail(
        $fromEmail,
        "Ticket Created: $newTicketNumber",
        "Your request has been received.\n\nTicket Number: $newTicketNumber"
    );

    // Mark processed
    if ($messageId) {
        $pdo->prepare("
            INSERT IGNORE INTO processed_emails (message_id)
            VALUES (?)
        ")->execute([$messageId]);
    }
}

imap_close($mailbox);