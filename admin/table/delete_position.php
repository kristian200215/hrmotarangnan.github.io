<?php
// Database connection details
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

// Check if positionId is provided
if(isset($_POST['positionId'])) {
    $positionId = $_POST['positionId'];

    // Prepare the delete statement
    $stmt = $conn->prepare("DELETE FROM positions WHERE position_id = ?");
    $stmt->bind_param("i", $positionId);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Position deleted successfully.";
    } else {
        echo "Error deleting position: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "Position ID not provided.";
}

$conn->close();
?>
