<?php
require 'auth.php';
require '../config/db.php';

/* ===== FORCE EXCEL DOWNLOAD ===== */
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=it_tickets_report.xls");
header("Pragma: no-cache");
header("Expires: 0");

/* ===== COLUMN HEADERS ===== */
echo "Ticket Number\tFrom\tSubject\tStatus\tCreated Date\n";

/* ===== FETCH DATA ===== */
$stmt = $pdo->query("
    SELECT
        ticket_number,
        sender_email,
        subject,
        status,
        created_at
    FROM tickets
    ORDER BY created_at DESC
");

/* ===== OUTPUT ROWS ===== */
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo
        $row['ticket_number'] . "\t" .
        $row['sender_email'] . "\t" .
        str_replace(["\t", "\n"], ' ', $row['subject']) . "\t" .
        $row['status'] . "\t" .
        date('Y-m-d H:i', strtotime($row['created_at'])) .
        "\n";
}

exit;
