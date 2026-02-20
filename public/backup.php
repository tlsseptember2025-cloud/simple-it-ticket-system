<?php
require_once '../config/db.php';

if (!isset($_GET['backup'])) {
    exit;
}

// Detect XAMPP root dynamically
$xamppRoot = dirname(dirname(dirname(__DIR__)));

// mysqldump path (inside XAMPP)
$mysqldumpPath = $xamppRoot . '/mysql/bin/mysqldump.exe';

// Backup directory (inside your project)
$backupDir = __DIR__ . '/../database/';

//$mysqldumpPath = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
//$backupDir     = 'C:\\xampp\\htdocs\\simple-it-ticket-system\\database\\';

if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}

$fileName   = $db . '_' . date('Y-m-d_H-i-s') . '.sql';
$backupFile = $backupDir . $fileName;

$command = "\"$mysqldumpPath\" "
         . "--host=$host "
         . "--port=$port "
         . "--protocol=tcp "
         . "--user=$user "
         . "--password=\"$pass\" "
         . "$db "
         . "--result-file=\"$backupFile\"";

exec($command . " 2>&1", $output, $resultCode);

if ($resultCode === 0 && file_exists($backupFile) && filesize($backupFile) > 0) {
    echo "✅ Backup created successfully<br>";
    echo "<strong>$fileName</strong>";
} else {
    echo "❌ Backup failed<br>";
    echo "<pre>Return code: $resultCode\n" . implode("\n", $output) . "</pre>";
}

echo "<p>You will be redirected in 2 seconds...</p>";
echo "<script>
    setTimeout(function () {
        window.location.href = 'dashboard.php';
    }, 2000);
</script>";
