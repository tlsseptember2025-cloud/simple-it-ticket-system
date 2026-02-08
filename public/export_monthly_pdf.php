<?php
require 'auth.php';
require '../config/db.php';
require '../config/mailer.php';
require 'lib/fpdf/fpdf.php';

/* ================= LOAD MAIL CONFIG ================= */

$config = require '../config/mail.php';

$reportRecipients = $config['report_recipients'] ?? [];

if (empty($reportRecipients)) {
    die('No report recipients configured.');
}

/* ================= INPUT ================= */

$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
$year  = isset($_GET['year'])  ? (int)$_GET['year']  : date('Y');

$startDate  = date('Y-m-d', strtotime("$year-$month-01"));
$endDate    = date('Y-m-t', strtotime($startDate));
$monthLabel = date('F Y', strtotime($startDate));

/* ================= SUMMARY ================= */

$summaryStmt = $pdo->prepare("
    SELECT
        COUNT(*) AS total,
        SUM(status='Open') AS open,
        SUM(status='In Progress') AS in_progress,
        SUM(status='Waiting') AS waiting,
        SUM(status='Closed') AS closed
    FROM tickets
    WHERE DATE(created_at) BETWEEN ? AND ?
");
$summaryStmt->execute([$startDate, $endDate]);
$summary = $summaryStmt->fetch(PDO::FETCH_ASSOC);

/* ================= TICKETS ================= */

$ticketsStmt = $pdo->prepare("
    SELECT ticket_number, sender_email, subject, status, created_at
    FROM tickets
    WHERE DATE(created_at) BETWEEN ? AND ?
    ORDER BY created_at ASC
");
$ticketsStmt->execute([$startDate, $endDate]);
$tickets = $ticketsStmt->fetchAll();

/* ================= CREATE PDF ================= */

$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();

/* Logo */
$logoPath = __DIR__ . '/assets/company-logo.png';
if (file_exists($logoPath)) {
    $pdf->Image($logoPath, 10, 10, 30);
}

/* Header */
$pdf->SetFont('Helvetica', 'B', 16);
$pdf->SetXY(45, 12);
$pdf->Cell(0, 8, 'IT Support Monthly Report', 0, 1);

$pdf->SetFont('Helvetica', '', 10);
$pdf->SetX(45);
$pdf->Cell(0, 6, "Period: $monthLabel", 0, 1);

$pdf->Ln(15);

/* Summary */
$pdf->SetFont('Helvetica', '', 11);
$pdf->Cell(
    0,
    8,
    "Total: {$summary['total']} | Open: {$summary['open']} | In Progress: {$summary['in_progress']} | Waiting: {$summary['waiting']} | Closed: {$summary['closed']}",
    0,
    1,
    'C'
);
$pdf->Ln(6);

/* Table Header */
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(45, 8, 'Ticket #', 1);
$pdf->Cell(70, 8, 'From', 1);
$pdf->Cell(95, 8, 'Subject', 1);
$pdf->Cell(30, 8, 'Status', 1);
$pdf->Cell(37, 8, 'Created', 1);
$pdf->Ln();

/* Table Rows */
$pdf->SetFont('Helvetica', '', 9);

if (empty($tickets)) {
    $pdf->Cell(277, 10, 'No tickets found for this month.', 1, 1, 'C');
} else {
    foreach ($tickets as $t) {
        $pdf->Cell(45, 8, $t['ticket_number'], 1);
        $pdf->Cell(70, 8, substr($t['sender_email'], 0, 40), 1);
        $pdf->Cell(95, 8, substr($t['subject'], 0, 60), 1);
        $pdf->Cell(30, 8, $t['status'], 1);
        $pdf->Cell(37, 8, date('D, M j, Y', strtotime($t['created_at'])), 1);
        $pdf->Ln();
    }
}

/* ================= SAVE PDF ================= */

$reportsDir = __DIR__ . '/../uploads/reports/';
if (!is_dir($reportsDir)) {
    mkdir($reportsDir, 0777, true);
}

$filename = "IT_Monthly_Report_" . date('F_Y', strtotime($startDate)) . ".pdf";
$filePath = $reportsDir . $filename;

$pdf->Output('F', $filePath);

/* ================= EMAIL REPORT ================= */

$emailSubject = "IT Support Monthly Report - $monthLabel";

$emailBody = "
Hello Team,

Please find attached the IT Support Monthly Report for $monthLabel.

Summary:
- Total Tickets: {$summary['total']}
- Open: {$summary['open']}
- In Progress: {$summary['in_progress']}
- Waiting: {$summary['waiting']}
- Closed: {$summary['closed']}

Regards,
IT Support System
";

foreach ($reportRecipients as $recipient) {
    sendMail(
        $recipient,
        $emailSubject,
        $emailBody,
        $filePath
    );
}

/* ================= REDIRECT BACK ================= */

header("Location: reports.php?sent=1");
exit;