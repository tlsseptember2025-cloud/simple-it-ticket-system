<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/mailer.php';
require __DIR__ . '/../public/lib/fpdf/fpdf.php';

/* ================= LOAD MAIL CONFIG ================= */

$config = require __DIR__ . '/../config/mail.php';

$reportRecipients = $config['report_recipients'] ?? [];

if (empty($reportRecipients)) {
    exit("No recipients configured\n");
}

/* ================= LAST MONTH ================= */

$startDate  = date('Y-m-01', strtotime('first day of last month'));
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
$logo = __DIR__ . '/../public/assets/company-logo.png';
if (file_exists($logo)) {
    $pdf->Image($logo, 10, 10, 30);
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

/* Table */
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell(45, 8, 'Ticket #', 1);
$pdf->Cell(70, 8, 'From', 1);
$pdf->Cell(95, 8, 'Subject', 1);
$pdf->Cell(30, 8, 'Status', 1);
$pdf->Cell(37, 8, 'Created', 1);
$pdf->Ln();

$pdf->SetFont('Helvetica', '', 9);

if (!$tickets) {
    $pdf->Cell(277, 10, 'No tickets found.', 1, 1, 'C');
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

/* Save */
$dir = __DIR__ . '/../uploads/reports/';
if (!is_dir($dir)) mkdir($dir, 0777, true);

$filename = "IT_Monthly_Report_" . date('F_Y', strtotime($startDate)) . ".pdf";
$path = $dir . $filename;

$pdf->Output('F', $path);

/* ================= EMAIL ================= */

$subject = "IT Support Monthly Report - $monthLabel";

$body = "
Hello Team,

Please find attached the IT Support Monthly Report for $monthLabel.

Regards,
IT Support System
";

foreach ($reportRecipients as $email) {
    sendMail($email, $subject, $body, $path);
}

exit("Monthly report sent\n");