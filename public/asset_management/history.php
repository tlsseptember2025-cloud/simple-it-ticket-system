<?php
require('../auth.php');
require('../../config/db.php');
include('header.php');

$stmt = $pdo->query("
    SELECT e.name, a.asset_tag, aa.assigned_at, aa.returned_at
    FROM asset_assignments aa
    JOIN employees e ON aa.employee_id = e.id
    JOIN assets a ON aa.asset_id = a.id
    ORDER BY aa.assigned_at DESC
");

$rows = $stmt->fetchAll();
?>

<h2 class="mb-4">Asset History</h2>

<a class="btn btn-secondary mb-3" target="_blank" href="print_history.php">
    Print History
</a>

<table class="table table-bordered table-striped">
<tr>
    <th>Employee</th>
    <th>Asset</th>
    <th>Assigned</th>
    <th>Returned</th>
</tr>

<?php foreach($rows as $r){ ?>
<tr>
    <td><?= $r['name'] ?></td>
    <td><?= $r['asset_tag'] ?></td>
    <td><?= $r['assigned_at'] ?></td>
    <td>
        <?php if($r['returned_at']){ ?>
            <span class="badge bg-secondary"><?= $r['returned_at'] ?></span>
        <?php } else { ?>
            <span class="badge bg-warning text-dark">Still Assigned</span>
        <?php } ?>
    </td>
</tr>
<?php } ?>

</table>

<?php
include('footer.php');
?>