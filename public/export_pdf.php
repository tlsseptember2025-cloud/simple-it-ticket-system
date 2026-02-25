<?php
require 'auth.php';
require '../config/db.php';
require 'lib/fpdf/fpdf.php';

/* ================= FETCH SUMMARY ================= */

$summary = $pdo->query("
    SELECT
        COUNT(*) AS total,
        SUM(status = 'Open') AS open,
        SUM(status = 'In Progress') AS in_progress,
        SUM(status = 'Waiting') AS waiting,
        SUM(status = 'Closed') AS closed
    FROM tickets
")->fetch(PDO::FETCH_ASSOC);

/* ================= FETCH TICKETS ================= */

$tickets = $pdo->query("
    SELECT ticket_number, sender_email, subject, category, status, created_at
    FROM tickets
    ORDER BY category DESC
")->fetchAll();

/* ================= CREATE PDF ================= */

// Landscape for wide tables
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();

// Page width helper
$pageWidth = $pdf->GetPageWidth();

/* ================= HEADER ================= */

// Company Logo
$pdf->Image(
    __DIR__ . '/assets/company-logo.png',
    10,
    10,
    30
);

// Title
$pdf->SetFont('Helvetica', 'B', 16);
$pdf->SetXY(45, 12);
$pdf->Cell(0, 8, 'IT Support Report', 0, 1);

// Report Date
$pdf->SetFont('Helvetica', '', 10);
$pdf->SetX(45);
$pdf->Cell(0, 6, 'Report Date: ' . date('l, F j, Y'), 0, 1);

$pdf->Ln(15);

/* ================= REPORT TITLE ================= */

$pdf->SetFont('Helvetica', 'B', 16);
$pdf->Cell(0, 10, 'IT Support Ticket Report', 0, 1, 'C');
$pdf->Ln(3);

/* ================= SUMMARY ================= */

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

/* ================= TABLE ================= */

// Column widths (sum ≈ 277mm usable width in landscape)
$wTicket  = 45;
$wFrom    = 70;
$wCat = 95;
$wStatus  = 20;
$wCreated = 45;

/* ---- Header ---- */
$pdf->SetFont('Helvetica', 'B', 10);
$pdf->Cell($wTicket, 8, 'Ticket #', 1);
$pdf->Cell($wFrom, 8, 'From', 1);
$pdf->Cell($wCat, 8, 'Category', 1);
$pdf->Cell($wStatus, 8, 'Status', 1);
$pdf->Cell($wCreated, 8, 'Created', 1);
$pdf->Ln();

/* ---- Rows ---- */
$pdf->SetFont('Helvetica', '', 9);

foreach ($tickets as $t) {

    $pdf->Cell($wTicket, 8, $t['ticket_number'], 1);
    $pdf->Cell($wFrom, 8, substr($t['sender_email'], 0, 40), 1);
    $pdf->Cell($wCat, 8, substr($t['category'], 0, 60), 1);
    $pdf->Cell($wStatus, 8, $t['status'], 1);
    $pdf->Cell(
        $wCreated,
        8,
        date('l, M j, Y', strtotime($t['created_at'])),
        1
    );

    $pdf->Ln();
}

/* ================= OUTPUT ================= */

$pdf->Output('D', 'IT_Support_Ticket_Report.pdf');
exit;