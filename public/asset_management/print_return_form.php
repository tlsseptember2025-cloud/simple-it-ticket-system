<?php
require('../../config/db.php');

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT e.name, a.asset_tag, a.type, a.brand, a.model, aa.returned_at
    FROM asset_assignments aa
    JOIN employees e ON aa.employee_id = e.id
    JOIN assets a ON aa.asset_id = a.id
    WHERE aa.id=?
");
$stmt->execute([$id]);
$data = $stmt->fetch();

if(!$data){
    echo "Invalid request";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Asset Return Form</title>
    <style>
    body { font-family: Arial; padding: 40px; }

    .header {
        text-align: center;
        margin-bottom: 25px;
    }

    .logo {
        height: 120px;
        display: block;
        margin: 0 auto;
    }

    .title {
        font-size: 22px;
        margin-top: 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th, td {
        border: 1px solid #000;
        padding: 10px;
    }

    th {
        background: #f0f0f0;
    }

    .sign {
        margin-top: 60px;
        display: flex;
        justify-content: space-between;
    }
</style>
</head>
<body>

<div class="header">
    <img src="../assets/company-logo.png" class="logo">
    <div class="title"><b>Asset Return Form</b></div>
    <div style="font-size:14px; margin-top:5px;">
            Loops Automation LLC <br>
            IT Department
    </div>
    <hr>
</div>

<table>
    <tr><th>Employee</th><td><?= $data['name'] ?></td></tr>
    <tr><th>Asset</th><td><?= $data['asset_tag'] ?></td></tr>
    <tr><th>Type</th><td><?= $data['type'] ?></td></tr>
    <tr><th>Brand / Model</th><td><?= $data['brand'] ?> - <?= $data['model'] ?></td></tr>
    <tr><th>Return Date</th><td><?= $data['returned_at'] ?></td></tr>
</table>

<p class="section">
    I confirm that I have returned the above asset.
</p>

<div class="sign">
    <div>
        Employee Signature: ____________________
    </div>
    <div>
        IT Signature: ____________________
    </div>
</div>

<script>
window.print();
</script>

<div style="margin-top:40px; font-size:12px;">
    Generated on: <?= date('Y-m-d H:i') ?>
</div>

</body>
</html>