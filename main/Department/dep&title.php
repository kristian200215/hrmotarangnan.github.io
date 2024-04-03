<?php
$servername = "localhost";
$username = "root";  
$password = ""; 
$database = "hrmo_tarangnan"; 

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function addDepartment($conn, $departmentName) {
    $stmt = $conn->prepare("INSERT INTO Departments (department_name) VALUES (?)");
    $stmt->bind_param("s", $departmentName);
    $stmt->execute();
    $stmt->close();
}

function addPosition($conn, $positionTitle, $departmentId) {
    $stmt = $conn->prepare("INSERT INTO Positions (department_id, position_title) VALUES (?, ?)");
    $stmt->bind_param("is", $departmentId, $positionTitle);
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST['add_department'])) {
    addDepartment($conn, $_POST['department_name']);
}

if (isset($_POST['add_position'])) {
    addPosition($conn, $_POST['position_title'], $_POST['department_id']);
}

$departments = [];
$result = $conn->query("SELECT department_id, department_name FROM Departments");
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Departments and Positions</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container form-container">
        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#addDepartmentModal">Add New Department</button>
        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#addPositionModal">Add New Position</button>

        <div class="modal fade" id="addDepartmentModal" tabindex="-1" aria-labelledby="addDepartmentModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="addDepartmentModalLabel">Add New Department</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form method="post">
                <div class="modal-body">
                  <div class="form-group">
                    <label for="departmentName">Department Name:</label>
                    <input type="text" class="form-control" id="departmentName" name="department_name" required>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" name="add_department" class="btn btn-primary">Add Department</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="modal fade" id="addPositionModal" tabindex="-1" aria-labelledby="addPositionModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="addPositionModalLabel">Add New Position</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form method="post">
                <div class="modal-body">
                  <div class="form-group">
                    <label for="positionTitleModal">Position Title:</label>
                    <input type="text" class="form-control" id="positionTitleModal" name="position_title" required>
                  </div>
                  <div class="form-group">
                    <label for="departmentModal">Department:</label>
                    <select class="form-control" id="departmentModal" name="department_id" required>
                      <?php
                      foreach ($departments as $department) {
                        echo "<option value='{$department['department_id']}'>{$department['department_name']}</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" name="add_position" class="btn btn-primary">Add Position</button>
                </div>
              </form>
            </div>
          </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$conn->close();
?>

