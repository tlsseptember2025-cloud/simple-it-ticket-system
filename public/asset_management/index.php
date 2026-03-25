<?php
require('../auth.php');
require('../../config/db.php');
?>

<h2>Assets List</h2>

<a href="create.php">+ Add Asset</a>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Asset Tag</th>
        <th>Type</th>
        <th>Brand</th>
        <th>Status</th>
    </tr>

<?php
$stmt = $pdo->query("SELECT * FROM assets");
$assets = $stmt->fetchAll();

foreach($assets as $row){
?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo $row['asset_tag']; ?></td>
    <td><?php echo $row['type']; ?></td>
    <td><?php echo $row['brand']; ?></td>
    <td><?php echo $row['status']; ?></td>
</tr>
<?php } ?>

</table>