<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/mailer.php';

$allowedDomain = '@loopsautomation.com';

// ===== IMAP CONFIG =====
$imapHost = '{imap.gmail.com:993/imap/ssl}INBOX';
$imapUser = 'rami.wahdan@loopsautomation.com';
$imapPass = 'aeel yygl yjiz lgfs';

// Open mailbox
$mailbox = imap_open($imapHost, $imapUser, $imapPass);

if (!$mailbox) {
    die('IMAP connection failed');
}

// Search for unread emails
$emails = imap_search($mailbox, 'UNSEEN');

if ($emails) {
    foreach ($emails as $emailNumber) {

        // Get email overview
        $overview = imap_fetch_overview($mailbox, $emailNumber, 0)[0];

        // Get headers
        $headers = imap_rfc822_parse_headers(
            imap_fetchheader($mailbox, $emailNumber)
        );

        // Sender email
        $fromEmail = $headers->from[0]->mailbox . '@' . $headers->from[0]->host;

        // Allow only internal company emails
        if (!str_ends_with(strtolower($fromEmail), $allowedDomain)) {
        // Mark email as read and skip it
            imap_setflag_full($mailbox, $emailNumber, "\\Seen");
            continue;
}
        // Subject
        $subject = $overview->subject ?? '(No Subject)';

        // Body (plain text)
        $body = imap_fetchbody($mailbox, $emailNumber, 1);
        $body = trim(strip_tags($body));

        // Generate ticket number
        $ticketNumber = 'IT-' . date('Y') . '-' . strtoupper(uniqid());

        // Insert ticket
        $stmt = $pdo->prepare("
            INSERT INTO tickets (ticket_number, sender_email, subject, message)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$ticketNumber, $fromEmail, $subject, $body]);

        // Auto-reply
        sendMail(
            $fromEmail,
            "Ticket Created: $ticketNumber",
            "Your request has been received.\n\nTicket Number: $ticketNumber\n\nWe will update you by email."
        );

        // Mark email as read
        imap_setflag_full($mailbox, $emailNumber, "\\Seen");
    }
}

// Close mailbox
imap_close($mailbox);
