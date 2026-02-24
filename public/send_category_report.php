<?php
require __DIR__ . '/../config/mailer.php';

if (empty($_GET['file'])) {
    die('No file specified.');
}

$fileName = basename($_GET['file']);
$filePath = __DIR__ . '/../uploads/reports/' . $fileName;

if (!file_exists($filePath)) {
    die('File not found.');
}

sendMail(
    'rami.wahdan@loopsautomation.com',
    'IT Support Category Report',
    'Please find attached the requested category report.',
    $filePath
);

$query = http_build_query([
    'from_date' => $_GET['from_date'] ?? '',
    'to_date' => $_GET['to_date'] ?? '',
    'category_filter' => $_GET['category_filter'] ?? '',
    'generated_category_pdf' => $fileName,
    'category_email_sent' => 1
]);

header("Location: reports.php?$query");
exit;
