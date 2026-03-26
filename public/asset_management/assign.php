<?php
require('../auth.php');
require('../../config/db.php');
include('header.php');
include('helpers.php');
$errors = [];
showErrors($errors);

// ASSIGN


if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $employee_id = (int) $_POST['employee_id'];
    $asset_id = (int) $_POST['asset_id'];

    if($employee_id <= 0) $errors[] = "Invalid employee";
    if($asset_id <= 0) $errors[] = "Invalid asset";

    // check if asset already assigned
    $check = $pdo->prepare("
        SELECT id FROM asset_assignments
        WHERE asset_id=? AND returned_at IS NULL
    ");
    $check->execute([$asset_id]);

    if($check->rowCount() > 0){
        $errors[] = "Asset already assigned";
    }

    if(empty($errors)){
        $stmt = $pdo->prepare("
            INSERT INTO asset_assignments (employee_id, asset_id, assigned_at)
            VALUES (?, ?, NOW())
        ");
        $stmt->execute([$employee_id, $asset_id]);

        $pdo->prepare("UPDATE assets SET status='assigned' WHERE id=?")
            ->execute([$asset_id]);

        echo "<div class='alert alert-success'>Assigned successfully</div>";
    }
}

// LOAD DATA
$employees = $pdo->query("SELECT * FROM employees")->fetchAll();
$assets = $pdo->query("SELECT * FROM assets WHERE status='available'")->fetchAll();
?>

<h2 class="mb-4">Assign Asset</h2>

<form method="POST" class="row g-3">

    <div class="col-md-6">
        <label class="form-label">Employee</label>
        <select class="form-control" name="employee_id">
            <?php foreach($employees as $e){ ?>
                <option value="<?= $e['id'] ?>"><?= $e['name'] ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Asset</label>
        <select class="form-control" name="asset_id">
            <?php foreach($assets as $a){ ?>
                <option value="<?= $a['id'] ?>">
                    <?= $a['asset_tag'] ?> (<?= $a['type'] ?>)
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="col-12">
        <button class="btn btn-primary">Assign</button>
    </div>

</form>

<?php
include('footer.php');
?>