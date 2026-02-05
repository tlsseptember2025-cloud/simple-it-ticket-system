<?php
session_start();

require __DIR__ . '/../cron/fetch_emails.php';

/* Flash message for dashboard */
$_SESSION['fetch_success'] = 'Emails fetched successfully.';

header('Location: dashboard.php');
exit;
