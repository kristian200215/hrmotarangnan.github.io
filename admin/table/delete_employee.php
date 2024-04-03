<?php
// delete_employee.php

$host = 'localhost';
$dbname = 'hrmo_tarangnan';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

$response = ['success' => false];

if(isset($_POST['employee_id'])) {
    $employeeId = $_POST['employee_id'];

    // Prepare your DELETE statement (adapt table name and column names to your schema)
    $stmt = $conn->prepare("DELETE FROM employees WHERE employee_id = ?");
$stmt->bind_param("i", $employeeId);

    if($stmt->execute()) {
        $response['success'] = true;
    }

    $stmt->close();
}

echo json_encode($response);
?>
