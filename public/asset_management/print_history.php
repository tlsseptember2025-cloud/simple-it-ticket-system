<?php
require('../../config/db.php');

// fetch history
$stmt = $pdo->query("
    SELECT e.name, a.asset_tag, a.type, a.brand,
           aa.assigned_at, aa.returned_at
    FROM asset_assignments aa
    JOIN employees e ON aa.employee_id = e.id
    JOIN assets a ON aa.asset_id = a.id
    ORDER BY aa.assigned_at DESC
");

$rows = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Asset History Report</title>
    <style>
        body { font-family: Arial; padding: 40px; }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo { height: 80px; }
        .title { font-size: 22px; margin-top: 10px; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #f0f0f0;
        }

        .logo { 
            height: 120px;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body>

<div class="header">
    <img src="../assets/company-logo.png" class="logo">
    <div class="title"><b>Asset History Report</b></div>
    <div style="font-size:14px; margin-top:5px;">
            Loops Automation LLC <br>
            IT Department
    </div>
    <hr>
</div>

<table>
<tr>
    <th>Employee</th>
    <th>Asset</th>
    <th>Type</th>
    <th>Brand</th>
    <th>Assigned Date</th>
    <th>Returned Date</th>
</tr>

<?php foreach($rows as $r){ ?>
<tr>
    <td><?= $r['name'] ?></td>
    <td><?= $r['asset_tag'] ?></td>
    <td><?= $r['type'] ?></td>
    <td><?= $r['brand'] ?></td>
    <td><?= $r['assigned_at'] ?></td>
    <td>
        <?= $r['returned_at'] ? $r['returned_at'] : 'Still Assigned' ?>
    </td>
</tr>
<?php } ?>

</table>

<script>
window.print();
</script>

<div style="margin-top:40px; font-size:12px;">
    Generated on: <?= date('Y-m-d H:i') ?>
</div>

</body>
</html>