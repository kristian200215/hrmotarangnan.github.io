<?php
$host = 'localhost';
$dbname = 'hrmo_tarangnan';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$employeeId = $_POST['employee_id'];
$startDate = $_POST['start_date'];
$endDate = isset($_POST['end_date']) && $_POST['end_date'] ? $_POST['end_date'] : null;
$department = $_POST['department'];
$position = $_POST['position'];
$salary = $_POST['salary'];
$employmentType = $_POST['employment_type'];
$stationAssignment = $_POST['station_assignment'];
$branchType = $_POST['branch_type'];
$employeeStatus = $_POST['employee_status'];


$sql = "INSERT INTO ServicePeriods (employee_id, department_id, position_id, start_date, end_date, status, salary, employment_type, station_assignment, branch_type) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);


if ($endDate === null) {
  
    $stmt->bind_param("iiisssdsss", $employeeId, $department, $position, $startDate, $endDate, $employeeStatus, $salary, $employmentType, $stationAssignment, $branchType);
} else {
    
    $stmt->bind_param("iiisssdsss", $employeeId, $department, $position, $startDate, $endDate, $employeeStatus, $salary, $employmentType, $stationAssignment, $branchType);
}


if ($stmt->execute() === TRUE) {
    echo "New service record added successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}


$stmt->close();
$conn->close();
?>
