<?php

$host = 'localhost';
$dbname = 'hrmo_tarangnan';
$username = 'root';
$password = '';


$department_id = isset($_GET['department_id']) ? intval($_GET['department_id']) : 0;

try {
  
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  
    $sql = "SELECT position_id, position_title FROM Positions WHERE department_id = :department_id";
  
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':department_id', $department_id, PDO::PARAM_INT);
    $stmt->execute();

   
    $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<option value=''>Select Position</option>"; 
    foreach ($positions as $position) {
        echo "<option value=\"{$position['position_id']}\">{$position['position_title']}</option>";
    }
} catch(PDOException $e) {
    
    die("Could not connect to the database $dbname :" . $e->getMessage());
}
?>
