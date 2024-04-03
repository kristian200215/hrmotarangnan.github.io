<?php

$host = 'localhost';
$dbname = 'hrmo_tarangnan';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT department_id, department_name FROM Departments";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($departments as $department) {
        echo "<option value=\"{$department['department_id']}\">{$department['department_name']}</option>";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage(); // Output any errors
}
?>
