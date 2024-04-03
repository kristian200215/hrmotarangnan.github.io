<?php
// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if employee_id and new_status are set in the POST request
    if (isset($_POST["employee_id"]) && isset($_POST["new_status"])) {
        $employeeId = $_POST["employee_id"];
        $newStatus = $_POST["new_status"];
        
        // Database connection details
        $host = 'localhost';
        $dbname = 'hrmo_tarangnan';
        $username = 'root';
        $password = '';

        // Create connection
        $conn = new mysqli($host, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Update status in the ServicePeriods table
        $sql = "UPDATE ServicePeriods SET status = '$newStatus', end_date = CURDATE() WHERE employee_id = $employeeId AND status = 'Active'";
        if ($conn->query($sql) === TRUE) {
            echo "Status updated successfully.";
        } else {
            echo "Error updating status: " . $conn->error;
        }

        $conn->close();
    } else {
        // If employee_id or new_status is not set in the POST request
        echo "Missing parameters.";
    }
} else {
    // If the request method is not POST
    echo "Invalid request method.";
}
?>
