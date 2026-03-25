<?php
require('../auth.php');
require('../../config/db.php');

// ADD EMPLOYEE
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $department = $_POST['department'];

    $stmt = $pdo->prepare("
        INSERT INTO employees (name, email, department)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$name, $email, $department]);

    header("Location: employees.php");
    exit;
}

// GET EMPLOYEES
$stmt = $pdo->query("SELECT * FROM employees");
$employees = $stmt->fetchAll();
?>

<h2>Employees</h2>

<form method="POST">
    Name: <input type="text" name="name"><br><br>
    Email: <input type="text" name="email"><br><br>
    Department: <input type="text" name="department"><br><br>
    <button type="submit">Add</button>
</form>

<hr>

<table border="1" cellpadding="10">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Department</th>
    <th>Status</th>
</tr>

<?php foreach($employees as $row){ ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['name'] ?></td>
    <td><?= $row['email'] ?></td>
    <td><?= $row['department'] ?></td>
    <td><?= $row['status'] ?></td>
</tr>
<?php } ?>

</table>