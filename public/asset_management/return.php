<?php
require('../auth.php');
require('../../config/db.php');
include('header.php');

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
    $id = $_GET['id'];

    // get asset id
    $stmt = $pdo->prepare("SELECT asset_id FROM asset_assignments WHERE id=?");
    $stmt->execute([$id]);
    $asset = $stmt->fetch();

    // update assignment
    $pdo->prepare("
        UPDATE asset_assignments 
        SET returned_at = NOW() 
        WHERE id=?
    ")->execute([$id]);

    // update asset
    $pdo->prepare("
        UPDATE assets SET status='available' WHERE id=?
    ")->execute([$asset['asset_id']]);

    header("Location: return.php");
}
?>

<h2>Return Assets</h2>

<table border="1" cellpadding="10">
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
        <a href="?id=<?= $r['id'] ?>">Return</a>
    </td>
</tr>
<?php } ?>

</table>

<?php
include('footer.php');
?>