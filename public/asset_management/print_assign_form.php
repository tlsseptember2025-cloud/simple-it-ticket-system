<?php
require('../../config/db.php');

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT e.name, e.department, a.asset_tag, a.type, a.brand, a.model, aa.assigned_at
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
    <title>Asset Assignment Form</title>
    <style>
        body { font-family: Arial; padding: 40px; }
        .header { text-align: center; }
        .logo { height: 80px; }
        .title { font-size: 22px; margin-top: 10px; }
        .section { margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; }
        td, th { border: 1px solid #000; padding: 10px; }
        .sign { margin-top: 60px; display: flex; justify-content: space-between; }
    </style>
</head>
<body>

<div class="header">
    <img src="../assets/company-logo.png" class="logo">
    <div class="title"><b>Asset Assignment Form</b></div>
</div>

<div class="section">
    <table>
        <tr><th>Employee Name</th><td><?= $data['name'] ?></td></tr>
        <tr><th>Department</th><td><?= $data['department'] ?></td></tr>
        <tr><th>Asset Tag</th><td><?= $data['asset_tag'] ?></td></tr>
        <tr><th>Type</th><td><?= $data['type'] ?></td></tr>
        <tr><th>Brand / Model</th><td><?= $data['brand'] ?> - <?= $data['model'] ?></td></tr>
        <tr><th>Assigned Date</th><td><?= $data['assigned_at'] ?></td></tr>
    </table>
</div>

<div class="section">
    <p>
        I confirm that I have received the above asset in good condition and I am responsible for it.
    </p>
</div>

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

</body>
</html>