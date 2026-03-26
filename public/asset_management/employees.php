<?php
require('../auth.php');
require('../../config/db.php');
include('header.php');
include('helpers.php');
$errors = [];

showErrors($errors);


// ADD EMPLOYEE

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $department = trim($_POST['department']);

    if(empty($name)) $errors[] = "Name is required";
    if(empty($email)) $errors[] = "Email is required";

    // email format
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors[] = "Invalid email format";
    }

    // duplicate email
    $check = $pdo->prepare("SELECT id FROM employees WHERE email=?");
    $check->execute([$email]);
    if($check->rowCount() > 0){
        $errors[] = "Email already exists";
    }

    if(empty($errors)){
        $stmt = $pdo->prepare("
            INSERT INTO employees (name, email, department)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$name, $email, $department]);

        header("Location: employees.php");
        exit;
    }
}
// GET EMPLOYEES
$stmt = $pdo->query("SELECT * FROM employees");
$employees = $stmt->fetchAll();
?>

<h2 class="mb-4">Employees</h2>

<form method="POST" class="mb-4">
    <div class="row mb-2">
        <div class="col-md-4">
            <input class="form-control" type="text" name="name" placeholder="Name">
        </div>
        <div class="col-md-4">
            <input class="form-control" type="text" name="email" placeholder="Email">
        </div>
        <div class="col-md-4">
            <input class="form-control" type="text" name="department" placeholder="Department">
        </div>
    </div>

    <button class="btn btn-success">+ Add Employee</button>
</form>

<table class="table table-bordered table-striped">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Department</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php foreach($employees as $row){ ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['name'] ?></td>
    <td><?= $row['email'] ?></td>
    <td><?= $row['department'] ?></td>
    <td>
        <span class="badge bg-success"><?= $row['status'] ?></span>
    </td>
    <td>
        <a class="btn btn-sm btn-info" href="employee_view.php?id=<?= $row['id'] ?>">
            View
        </a>
    </td>
</tr>
<?php } ?>

</table>

<?php
include('footer.php');
?>