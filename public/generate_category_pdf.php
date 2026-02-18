<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../public/lib/fpdf/fpdf.php';

function generateCategoryPDF($from, $to, $category, $pdo)
{
    $where = "DATE(created_at) BETWEEN :from AND :to";
    $params = [
        ':from' => $from,
        ':to'   => $to
    ];

    if (!empty($category)) {
        $where .= " AND category = :category";
        $params[':category'] = $category;
    }

    $stmt = $pdo->prepare("
        SELECT ticket_number, sender_email, subject, category, status, created_at
        FROM tickets
        WHERE $where
        ORDER BY category ASC, created_at DESC
    ");

    $stmt->execute($params);
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    /* ===== CREATE PDF ===== */

    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);

    $pdf->Cell(0, 10, "IT Support Category Report", 0, 1, 'C');
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(0, 8, "From $from To $to", 0, 1, 'C');
    $pdf->Ln(5);

    if (empty($tickets)) {
        $pdf->Cell(0, 10, "No records found.", 0, 1);
    } else {

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(35, 8, "Ticket #", 1);
        $pdf->Cell(40, 8, "Category", 1);
        $pdf->Cell(35, 8, "Status", 1);
        $pdf->Cell(0, 8, "Created", 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 8);

        foreach ($tickets as $row) {

            $pdf->Cell(35, 8, $row['ticket_number'], 1);
            $pdf->Cell(40, 8, $row['category'], 1);
            $pdf->Cell(35, 8, $row['status'], 1);
            $pdf->Cell(0, 8, date('Y-m-d', strtotime($row['created_at'])), 1);
            $pdf->Ln();
        }
    }

    /* ===== SAVE FILE ===== */

    $fileName = "Category_Report_" . date('Ymd_His') . ".pdf";
    $filePath = __DIR__ . "/../uploads/reports/" . $fileName;

    if (!is_dir(__DIR__ . "/../uploads/reports/")) {
        mkdir(__DIR__ . "/../uploads/reports/", 0777, true);
    }

    $pdf->Output('F', $filePath);

    return $fileName;
}
