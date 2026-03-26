<?php
require('../auth.php');
require('../../config/db.php');
include('header.php');

// HANDLE RETURN
if(isset($_GET['id'])){
    $id = (int) $_GET['id'];

    // get asset id from assignment
    $stmt = $pdo->prepare("SELECT asset_id FROM asset_assignments WHERE id=?");
    $stmt->execute([$id]);
    $assignment = $stmt->fetch();

    if($assignment){

        // update assignment
        $pdo->prepare("
            UPDATE asset_assignments 
            SET returned_at = NOW() 
            WHERE id=?
        ")->execute([$id]);

        // update asset status
        $pdo->prepare("
            UPDATE assets SET status='available' WHERE id=?
        ")->execute([$assignment['asset_id']]);

        echo "<div class='alert alert-success'>Asset returned successfully</div>";

    } else {
        echo "<div class='alert alert-danger'>Invalid request</div>";
    }
}

// LOAD ACTIVE ASSIGNMENTS
$stmt = $pdo->query("
    SELECT aa.id, e.name, a.asset_tag, a.type
    FROM asset_assignments aa
    JOIN employees e ON aa.employee_id = e.id
    JOIN assets a ON aa.asset_id = a.id
    WHERE aa.returned_at IS NULL
");
$rows = $stmt->fetchAll();
?>

<h2 class="mb-4">Return Assets</h2>

<a class="btn btn-secondary mb-3" target="_blank" href="print_return.php">
    Print All Returned
</a>

<table class="table table-bordered table-striped">
<tr>
    <th>Employee</th>
    <th>Asset</th>
    <th>Type</th>
    <th>Actions</th>
</tr>

<?php foreach($rows as $r){ ?>
<tr>
    <td><?= $r['name'] ?></td>
    <td><?= $r['asset_tag'] ?></td>
    <td><?= $r['type'] ?></td>
    <td>

        <!-- Return button -->
        <a class="btn btn-sm btn-danger"
           onclick="return confirm('Confirm return?')"
           href="?id=<?= $r['id'] ?>">
           Return
        </a>

        <!-- Print form button -->
        <a class="btn btn-sm btn-secondary"
           target="_blank"
           href="print_return_form.php?id=<?= $r['id'] ?>">
           Print Form
        </a>

    </td>
</tr>
<?php } ?>

</table>

<?php include('footer.php'); ?>