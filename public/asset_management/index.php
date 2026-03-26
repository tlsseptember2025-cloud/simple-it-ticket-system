<?php
require('../auth.php');
require('../../config/db.php');
include('header.php');

// Get search input
$search = trim($_GET['search'] ?? '');

// Fetch assets
if($search != ''){
    $stmt = $pdo->prepare("
        SELECT * FROM assets
        WHERE asset_tag LIKE ?
        OR brand LIKE ?
        OR type LIKE ?
        OR model LIKE ?
    ");
    $stmt->execute([
        "%$search%",
        "%$search%",
        "%$search%",
        "%$search%"
    ]);
} else {
    $stmt = $pdo->query("SELECT * FROM assets WHERE status != 'retired'");
}

$assets = $stmt->fetchAll();
?>

<h2 class="mb-3">Assets List</h2>

<a href="create.php" class="btn btn-success mb-3">+ Add Asset</a>
<a href="retired.php" class="btn btn-dark mb-3">View Retired Assets</a>

<form method="GET" class="mb-3">
    <div class="row">
        <div class="col-md-6">
            <input 
                class="form-control" 
                type="text" 
                name="search" 
                placeholder="Search by tag, brand, type, model"
                value="<?= htmlspecialchars($search) ?>"
            >
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary">Search</button>
        </div>
    </div>
</form>

<table class="table table-bordered table-striped">
    <tr>
        <th>ID</th>
        <th>Asset Tag</th>
        <th>Type</th>
        <th>Brand</th>
        <th>Model</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>

<?php foreach($assets as $row){ ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['asset_tag'] ?></td>
    <td><?= $row['type'] ?></td>
    <td><?= $row['brand'] ?></td>
    <td><?= $row['model'] ?></td>
    <td>
        <?php if($row['status'] == 'available'){ ?>
            <span class="badge bg-success">Available</span>
        <?php } elseif($row['status'] == 'assigned'){ ?>
            <span class="badge bg-warning text-dark">Assigned</span>
        <?php } else { ?>
            <span class="badge bg-secondary"><?= $row['status'] ?></span>
        <?php } ?>
    </td>
    <td>
        <a class="btn btn-sm btn-warning" href="edit.php?id=<?= $row['id'] ?>">Edit</a>
        <a class="btn btn-sm btn-secondary" onclick="return confirm('Retire this asset?')" href="delete.php?id=<?= $row['id'] ?>">Retire</a>
    </td>
</tr>
<?php } ?>

</table>

<?php include('footer.php'); ?>