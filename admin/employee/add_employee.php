<?php

$host = 'localhost';
$dbname = 'hrmo_tarangnan';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Start transaction
    $pdo->beginTransaction();

    $first_name = $_POST['first_name'];
    $middle_name = isset($_POST['middle_name']) && !empty($_POST['middle_name']) ? $_POST['middle_name'] : null;
    $last_name = $_POST['last_name'];

    // Check for duplicate entry
    $duplicateCheckSql = "SELECT COUNT(*) FROM Employees WHERE first_name = ? AND IFNULL(middle_name, '') = IFNULL(?, '') AND last_name = ?";
    $stmt = $pdo->prepare($duplicateCheckSql);
    $stmt->execute([$first_name, $middle_name, $last_name]);
    $duplicateCount = $stmt->fetchColumn();

    if ($duplicateCount > 0) {
        throw new Exception("An employee with the same name already exists.");
    }

    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];
    $birthday = $_POST['birthday'];

    $sql = "INSERT INTO Employees (first_name, middle_name, last_name, contact_number, address, birthday) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$first_name, $middle_name, $last_name, $contact_number, $address, $birthday]);

    $employee_id = $pdo->lastInsertId();
    $previousEndDate = null;

    if (isset($_POST['start_date']) && is_array($_POST['start_date'])) {
        for ($i = 0; $i < count($_POST['start_date']); $i++) {
            $start_date = new DateTime($_POST['start_date'][$i]);
            $end_date = isset($_POST['end_date'][$i]) && $_POST['end_date'][$i] ? new DateTime($_POST['end_date'][$i]) : null;
            $status = $_POST['employee_status'][$i];

            if ($status == 'Active' && !empty($_POST['end_date'][$i])) {
                throw new Exception("An active service period must not have an end date.");
            }

            if ($end_date && $start_date > $end_date) {
                throw new Exception("End date must be after the start date for all service periods.");
            }

            if ($previousEndDate && $start_date <= $previousEndDate) {
                throw new Exception("Service periods must not overlap.");
            }

            $previousEndDate = $end_date ? $end_date : $start_date;

            $sql = "INSERT INTO ServicePeriods (employee_id, department_id, position_id, start_date, end_date, status, salary, employment_type, station_assignment, branch_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $employee_id,
                $_POST['department'][$i],
                $_POST['position'][$i],
                $_POST['start_date'][$i],
                ($status == 'Active' ? null : ($_POST['end_date'][$i] ? $_POST['end_date'][$i] : null)),
                $status,
                $_POST['salary'][$i],
                $_POST['employment_type'][$i],
                $_POST['station_assignment'][$i],
                $_POST['branch_type'][$i]
            ]);
        }
    }

    // Commit transaction
    $pdo->commit();

    echo "<script>
    alert('New employee added successfully.');
    window.location.href = 'add_employe.php';
    </script>";

} catch(PDOException $e) {
   
    $pdo->rollBack();
    echo "<script>
    alert('Database error: " . addslashes($e->getMessage()) . "');
    history.go(-1);
    </script>";
} catch(Exception $e) {
    
    $pdo->rollBack();
    echo "<script>
    alert('Error: " . addslashes($e->getMessage()) . "');
    history.go(-1);
    </script>";
}
?>
