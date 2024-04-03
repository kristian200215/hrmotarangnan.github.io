<?php
$servername = "localhost";
$username = "root";  
$password = ""; 
$database = "hrmo_tarangnan"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check for POST data
if (isset($_POST['department_id']) && !empty($_POST['department_name'])) {
    $departmentId = $_POST['department_id'];
    $departmentName = $_POST['department_name'];

    // Prepare and bind
    $stmt = $conn->prepare("UPDATE departments SET department_name = ? WHERE department_id = ?");
    $stmt->bind_param("si", $departmentName, $departmentId);

    // Execute
    if ($stmt->execute()) {
        echo "Department updated successfully.";
    } else {
        echo "Error updating department: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Required data not provided.";
}

$conn->close();
?>
