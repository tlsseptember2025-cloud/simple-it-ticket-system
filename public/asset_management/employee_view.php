<?php
require('../../config/db.php');
include('header.php');

$id = $_GET['id'];

// employee
$stmt = $pdo->prepare("SELECT * FROM employees WHERE id=?");
$stmt->execute([$id]);
$employee = $stmt->fetch();

// assets
$stmt = $pdo->prepare("
    SELECT a.asset_tag, aa.assigned_at, aa.returned_at
    FROM asset_assignments aa
    JOIN assets a ON aa.asset_id = a.id
    WHERE aa.employee_id=?
");
$stmt->execute([$id]);
$assets = $stmt->fetchAll();
?>

<h2 class="mb-4"><?= $employee['name'] ?></h2>

<table class="table table-bordered table-striped">
<tr>
    <th>Asset</th>
    <th>Assigned</th>
    <th>Returned</th>
</tr>

<?php foreach($assets as $a){ ?>
<tr>
    <td><?= $a['asset_tag'] ?></td>
    <td><?= $a['assigned_at'] ?></td>
    <td>
        <?php if($a['returned_at']){ ?>
            <span class="badge bg-secondary"><?= $a['returned_at'] ?></span>
        <?php } else { ?>
            <span class="badge bg-warning text-dark">Still Assigned</span>
        <?php } ?>
    </td>
</tr>
<?php } ?>

</table>

<?php include('footer.php'); ?>