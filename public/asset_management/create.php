<?php
require('../auth.php');
require('../../config/db.php');
include('header.php');

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $asset_tag = $_POST['asset_tag'];
    $type = $_POST['type'];
    $brand = $_POST['brand'];
    $model = $_POST['model'];

    $stmt = $pdo->prepare("
        INSERT INTO assets (asset_tag, type, brand, model)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->execute([$asset_tag, $type, $brand, $model]);

    header("Location: index.php");
    exit;
}
?>

<h2>Add Asset</h2>

<form method="POST">
    Asset Tag: <input type="text" name="asset_tag"><br><br>
    Type: <input type="text" name="type"><br><br>
    Brand: <input type="text" name="brand"><br><br>
    Model: <input type="text" name="model"><br><br>

    <button type="submit">Save</button>
</form>

<?php
include('footer.php');
?>