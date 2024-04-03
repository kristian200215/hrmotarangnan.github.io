<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "hrmo_tarangnan";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch existing departments for the dropdown
$existingDepartments = [];
$deptQuery = "SELECT department_id, department_name FROM Departments";
$deptResult = $conn->query($deptQuery);
while ($row = $deptResult->fetch_assoc()) {
    $existingDepartments[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->begin_transaction();
    try {
        if (!empty($_POST['new_department_name'])) {
            // Add new department case
            $newDepartmentName = $_POST['new_department_name'];
            $stmt = $conn->prepare("INSERT INTO Departments (department_name) VALUES (?)");
            $stmt->bind_param("s", $newDepartmentName);
            $stmt->execute();
            $departmentId = $conn->insert_id;
            $stmt->close();
        } elseif (!empty($_POST['existing_department_id'])) {
            // Use existing department case
            $departmentId = $_POST['existing_department_id'];
        }

        // After checking for department insertion or selection
if (isset($departmentId) && !empty($_POST['position_title'])) {
    foreach ($_POST['position_title'] as $positionTitle) {
        if (!empty($positionTitle)) { // Ensure the position title is not empty
            $stmt = $conn->prepare("INSERT INTO Positions (department_id, position_title) VALUES (?, ?)");
            $stmt->bind_param("is", $departmentId, $positionTitle);
            $stmt->execute();
            $stmt->close();
        }
    }
}


        $conn->commit();
        echo "<script>alert('Operation successful!');</script>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Department and Multiple Positions</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>

<div class="container">
    <h2 class="mt-5">Department and Positions Management</h2>
    <!-- Trigger button for modal -->
    <button type="button" class="btn btn-primary mt-3" data-toggle="modal" data-target="#addDepartmentPositionModal">
        Add Department and Position
    </button>
</div>

<!-- Modal Structure -->
<div class="modal fade" id="addDepartmentPositionModal" tabindex="-1" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Department and Positions</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" class="modal-body">
                <div class="form-group">
                    <label for="newDepartmentName">Add New Department (optional):</label>
                    <input type="text" class="form-control" id="newDepartmentName" placeholder="Enter New Department" name="new_department_name">
                    <small class="form-text text-muted">Leave blank if adding a position to an existing department.</small>
                </div>
                <div class="form-group">
                    <label for="existingDepartment">Or Select Existing Department:</label>
                    <select class="form-control" id="existingDepartment" name="existing_department_id">
                        <option value="">Select Department</option>
                        <?php foreach ($existingDepartments as $dept) {
                            echo "<option value='{$dept['department_id']}'>{$dept['department_name']}</option>";
                        } ?>
                    </select>
                </div>
                <div class="form-group positions">
                    <label>Positions:</label>
                    <div id="positionInputsContainer">
                        <input type="text" class="form-control" placeholder="Enter Position" name="position_title[]">
                        <br>
                    </div>
                    <button type="button" class="btn btn-secondary mt-2" id="addPositionBtn">Add Another Position</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('addPositionBtn').addEventListener('click', function() {
    var container = document.getElementById('positionInputsContainer');
    var inputGroup = document.createElement('div');
    inputGroup.className = 'input-group mb-2';

    var input = document.createElement('input');
    input.type = 'text';
    input.className = 'form-control';
    input.name = 'position_title[]';
    input.placeholder = 'Enter Another Position Name';
    input.required = true;

    var inputGroupAppend = document.createElement('div');
    inputGroupAppend.className = 'input-group-append';

    var removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'btn btn-danger';
    removeBtn.innerHTML = 'Remove';

    removeBtn.onclick = function() {
        container.removeChild(inputGroup);
    };

    inputGroupAppend.appendChild(removeBtn);
    inputGroup.appendChild(input);
    inputGroup.appendChild(inputGroupAppend);
    container.appendChild(inputGroup);
});


</script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>


