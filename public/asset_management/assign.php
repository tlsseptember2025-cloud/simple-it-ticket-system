<?php
require('../auth.php');
require('../../config/db.php');

// ASSIGN
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $employee_id = $_POST['employee_id'];
    $asset_id = $_POST['asset_id'];

    // check if asset already assigned
    $check = $pdo->prepare("
        SELECT * FROM asset_assignments 
        WHERE asset_id = ? AND returned_at IS NULL
    ");
    $check->execute([$asset_id]);

    if($check->rowCount() > 0){
        echo "❌ Asset already assigned!";
        exit;
    }

    // assign
    $stmt = $pdo->prepare("
        INSERT INTO asset_assignments (employee_id, asset_id, assigned_at)
        VALUES (?, ?, NOW())
    ");
    $stmt->execute([$employee_id, $asset_id]);

    // update asset status
    $pdo->prepare("UPDATE assets SET status='assigned' WHERE id=?")
        ->execute([$asset_id]);

    echo "✅ Assigned successfully";
}

// LOAD DATA
$employees = $pdo->query("SELECT * FROM employees")->fetchAll();
$assets = $pdo->query("SELECT * FROM assets WHERE status='available'")->fetchAll();
?>

<h2>Assign Asset</h2>

<form method="POST">
    Employee:
    <select name="employee_id">
        <?php foreach($employees as $e){ ?>
            <option value="<?= $e['id'] ?>"><?= $e['name'] ?></option>
        <?php } ?>
    </select>
    <br><br>

    Asset:
    <select name="asset_id">
        <?php foreach($assets as $a){ ?>
            <option value="<?= $a['id'] ?>">
                <?= $a['asset_tag'] ?> (<?= $a['type'] ?>)
            </option>
        <?php } ?>
    </select>
    <br><br>

    <button type="submit">Assign</button>
</form>