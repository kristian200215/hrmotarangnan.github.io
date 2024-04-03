<?php
$host = 'localhost';
$dbname = 'hrmo_tarangnan';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT 
            e.employee_id, e.first_name, e.last_name, 
            pos.position_title, sp.start_date, sp.end_date, sp.status
        FROM 
            Employees e
        INNER JOIN 
            ServicePeriods sp ON e.employee_id = sp.employee_id
        INNER JOIN 
            Positions pos ON sp.position_id = pos.position_id
        ORDER BY 
            e.first_name, e.last_name, sp.start_date DESC";

$result = $conn->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}
$conn->close();
echo json_encode($data);
?>
