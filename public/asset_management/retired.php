<?php
require('../../config/db.php');
include('header.php');

$stmt = $pdo->query("SELECT * FROM assets WHERE status='retired'");
$assets = $stmt->fetchAll();
?>

<h2 class="mb-4">Retired Assets</h2>

<table class="table table-bordered">
<tr>
    <th>Asset Tag</th>
    <th>Type</th>
    <th>Brand</th>
    <th> Action </th>
</tr>

<?php foreach($assets as $row){ ?>
<tr>
    <td><?= $row['asset_tag'] ?></td>
    <td><?= $row['type'] ?></td>
    <td><?= $row['brand'] ?></td>
    <td><a class="btn btn-sm btn-success" href="restore.php?id=<?= $row['id'] ?>">Restore</a></td>
</tr>
<?php } ?>

</table>

<?php include('footer.php'); ?>