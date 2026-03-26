<?php
require('../../config/db.php');

$stmt = $pdo->query("
    SELECT e.name, a.asset_tag, aa.returned_at
    FROM asset_assignments aa
    JOIN employees e ON aa.employee_id = e.id
    JOIN assets a ON aa.asset_id = a.id
    WHERE aa.returned_at IS NOT NULL
");

$rows = $stmt->fetchAll();
?>

<h2>Returned Assets</h2>

<table border="1" cellpadding="10">
<tr>
    <th>Employee</th>
    <th>Asset</th>
    <th>Returned</th>
</tr>

<?php foreach($rows as $r){ ?>
<tr>
    <td><?= $r['name'] ?></td>
    <td><?= $r['asset_tag'] ?></td>
    <td><?= $r['returned_at'] ?></td>
</tr>
<?php } ?>

</table>

<script>
window.print();
</script>