<?php
$servername = "localhost";
$username = "root";  
$password = ""; 
$database = "hrmo_tarangnan"; 

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(isset($_POST['departmentId'])) {
    $departmentId = $_POST['departmentId'];

    // SQL to delete a department
    $sql = "DELETE FROM Departments WHERE department_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $departmentId);
    if($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error";
    }
    $stmt->close();
    $conn->close();
}
?>
