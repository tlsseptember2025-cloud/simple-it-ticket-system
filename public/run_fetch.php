<?php
session_start();

require __DIR__ . '/../cron/fetch_emails.php';

/* Flash message for dashboard */
$_SESSION['fetch_success'] = 'Tickets updated successfully.';

header('Location: dashboard.php');
exit;
