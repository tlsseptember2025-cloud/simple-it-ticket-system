<?php
require('../../config/db.php');
include('header.php');

$stmt = $pdo->query("
    SELECT e.name, a.asset_tag
    FROM asset_assignments aa
    JOIN employees e ON aa.employee_id = e.id
    JOIN assets a ON aa.asset_id = a.id
    WHERE aa.returned_at IS NULL
");

$rows = $stmt->fetchAll();
?>

<h2>Assets Not Returned</h2>

<table class="table">
<tr>
    <th>Employee</th>
    <th>Asset</th>
</tr>

<?php foreach($rows as $r){ ?>
<tr>
    <td><?= $r['name'] ?></td>
    <td><?= $r['asset_tag'] ?></td>
</tr>
<?php } ?>

</table>

<?php include('footer.php'); ?>