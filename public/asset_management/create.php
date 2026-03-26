<?php
require('../auth.php');
require('../../config/db.php');
include('header.php');

$errors = [];

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $asset_tag = trim($_POST['asset_tag']);
    $type = trim($_POST['type']);
    $brand = trim($_POST['brand']);
    $model = trim($_POST['model']);

    if(empty($asset_tag)) $errors[] = "Asset tag is required";
    if(empty($type)) $errors[] = "Type is required";
    if(empty($brand)) $errors[] = "Brand is required";

    if(empty($errors)){
        $stmt = $pdo->prepare("
            INSERT INTO assets (asset_tag, type, brand, model)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$asset_tag, $type, $brand, $model]);

        header("Location: index.php");
        exit;
    }
}
?>

<h2 class="mb-4">Add Asset</h2>

<?php if(!empty($errors)){ ?>
    <div class="alert alert-danger">
        <?php foreach($errors as $e){ echo "<div>$e</div>"; } ?>
    </div>
<?php } ?>

<form method="POST" class="row g-3">

    <div class="col-md-6">
        <label class="form-label">Asset Tag</label>
        <input class="form-control" type="text" name="asset_tag">
    </div>

    <div class="col-md-6">
        <label class="form-label">Type</label>
        <input class="form-control" type="text" name="type">
    </div>

    <div class="col-md-6">
        <label class="form-label">Brand</label>
        <input class="form-control" type="text" name="brand">
    </div>

    <div class="col-md-6">
        <label class="form-label">Model</label>
        <input class="form-control" type="text" name="model">
    </div>

    <div class="col-12">
        <button class="btn btn-success">Save Asset</button>
        <a href="index.php" class="btn btn-secondary">Back</a>
    </div>

</form>

<?php include('footer.php'); ?>