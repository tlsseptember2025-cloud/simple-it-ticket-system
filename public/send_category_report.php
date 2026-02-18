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

header("Location: reports.php?category_email_sent=1");
exit;
