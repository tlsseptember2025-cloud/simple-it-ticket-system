<?php
require('../auth.php');
require('../../config/db.php');
include('header.php');
include('helpers.php');
$errors = [];
showErrors($errors);

// GET active assignments
$stmt = $pdo->query("
    SELECT aa.id, e.name, a.asset_tag, a.id as asset_id
    FROM asset_assignments aa
    JOIN employees e ON aa.employee_id = e.id
    JOIN assets a ON aa.asset_id = a.id
    WHERE aa.returned_at IS NULL
");
$rows = $stmt->fetchAll();

// RETURN
if(isset($_GET['id'])){
    $id = (int) $_GET['id'];

    // check assignment exists
    $stmt = $pdo->prepare("SELECT asset_id FROM asset_assignments WHERE id=?");
    $stmt->execute([$id]);
    $assignment = $stmt->fetch();

    if(!$assignment){
        echo "<div class='alert alert-danger'>Invalid request</div>";
    } else {
        // update safely
        $pdo->prepare("
            UPDATE asset_assignments 
            SET returned_at = NOW()
            WHERE id=?
        ")->execute([$id]);

        $pdo->prepare("
            UPDATE assets SET status='available' WHERE id=?
        ")->execute([$assignment['asset_id']]);

        header("Location: return.php");
        exit;
    }
}
?>

<h2 class="mb-4">Return Assets</h2>

<table class="table table-bordered table-striped">
<tr>
    <th>Employee</th>
    <th>Asset</th>
    <th>Action</th>
</tr>

<?php foreach($rows as $r){ ?>
<tr>
    <td><?= $r['name'] ?></td>
    <td><?= $r['asset_tag'] ?></td>
    <td>
        <a class="btn btn-sm btn-danger" href="?id=<?= $r['id'] ?>">
            Return
        </a>
    </td>
</tr>
<?php } ?>

</table>

<?php
include('footer.php');
?>