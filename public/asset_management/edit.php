<?php
require('../auth.php');
require('../../config/db.php');
include('header.php');

$id = (int) ($_GET['id'] ?? 0);

// fetch asset
$stmt = $pdo->prepare("SELECT * FROM assets WHERE id=?");
$stmt->execute([$id]);
$asset = $stmt->fetch();

if(!$asset){
    echo "Asset not found";
    exit;
}

// update
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $asset_tag = $_POST['asset_tag'];
    $type = $_POST['type'];
    $brand = $_POST['brand'];
    $model = $_POST['model'];

    $stmt = $pdo->prepare("
        UPDATE assets 
        SET asset_tag=?, type=?, brand=?, model=?
        WHERE id=?
    ");
    $stmt->execute([$asset_tag, $type, $brand, $model, $id]);

    header("Location: index.php");
    exit;
}
?>

<h2 class="mb-4">Edit Asset</h2>

<form method="POST" class="row g-3">

    <div class="col-md-6">
        <label>Asset Tag</label>
        <input class="form-control" name="asset_tag" value="<?= $asset['asset_tag'] ?>">
    </div>

    <div class="col-md-6">
        <label>Type</label>
        <input class="form-control" name="type" value="<?= $asset['type'] ?>">
    </div>

    <div class="col-md-6">
        <label>Brand</label>
        <input class="form-control" name="brand" value="<?= $asset['brand'] ?>">
    </div>

    <div class="col-md-6">
        <label>Model</label>
        <input class="form-control" name="model" value="<?= $asset['model'] ?>">
    </div>

    <div class="col-12">
        <button class="btn btn-primary">Update</button>
        <a href="index.php" class="btn btn-secondary">Back</a>
    </div>

</form>

<?php include('footer.php'); ?>