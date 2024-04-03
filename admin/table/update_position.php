<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$database = "hrmo_tarangnan";

// Establish database connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate POST data
if (!empty($_POST['position_id']) && isset($_POST['position_title'])) {
    $positionId = $_POST['position_id'];
    $positionTitle = $_POST['position_title'];

    // Prepare and bind
    $stmt = $conn->prepare("UPDATE positions SET position_title = ? WHERE position_id = ?");
    $stmt->bind_param("si", $positionTitle, $positionId);

    // Execute
    if ($stmt->execute()) {
        echo "Position updated successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Required data not provided.";
}

$conn->close();
?>
