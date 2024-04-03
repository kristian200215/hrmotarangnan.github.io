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


$departments = [];
$result = $conn->query("SELECT department_id, department_name FROM Departments");
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
      $departments[] = $row;
  }
}

// Total Employees
$totalEmployeesQuery = "SELECT COUNT(*) AS total_employees FROM Employees";
$totalEmployeesResult = $conn->query($totalEmployeesQuery);
$totalEmployees = $totalEmployeesResult->fetch_assoc()['total_employees'];

// Active Employees
$activeEmployeesQuery = "SELECT COUNT(*) AS active_employees FROM ServicePeriods WHERE status = 'Active'";
$activeEmployeesResult = $conn->query($activeEmployeesQuery);
$activeEmployees = $activeEmployeesResult->fetch_assoc()['active_employees'];

// Departments
$departmentsQuery = "SELECT COUNT(*) AS total_departments FROM Departments";
$departmentsResult = $conn->query($departmentsQuery);
$totalDepartments = $departmentsResult->fetch_assoc()['total_departments'];

// Positions
$positionsQuery = "SELECT COUNT(*) AS total_positions FROM Positions";
$positionsResult = $conn->query($positionsQuery);
$totalPositions = $positionsResult->fetch_assoc()['total_positions'];

function getPositionsForDepartment($conn, $departmentId) {
  $positions = [];
  $stmt = $conn->prepare("SELECT position_title FROM Positions WHERE department_id = ?");
  $stmt->bind_param("i", $departmentId);
  $stmt->execute();
  $result = $stmt->get_result();
  while ($row = $result->fetch_assoc()) {
      $positions[] = $row['position_title'];
  }
  $stmt->close();
  return $positions;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Service Records</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<style>
  body, html {
    margin: 0;
    padding: 0;
    font-family: "Poppins", sans-serif;
    background-color: #ddd;
}

.sidebar {
    height: 100%;
    width: 220px;
    position: fixed;
    background-color: #333;
    overflow-x: hidden;
    transition: 0.5s;
    box-shadow: 2px 0 5px rgba(0,0,0,0.5);
}
.logo-container {
    justify-content: center;
    align-items: center;
    padding: 20px;
    background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('/tarangnan/admin/log/bg.jpg');
    position: relative;
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
    text-align:center;
}
.logo-container .name h5{
    color:white;
    font-weight:500;
    letter-spacing:2px;
}
.logo img{
    width: 100px;
    border-radius: 50%;
}

.sidebar-btn {
    padding: 15px 20px;
    text-align: left;
    display: block;
    color: #ddd; 
    width: 100%; 
    transition: 0.3s; 
    text-decoration: none; 
    background: none; 
}
.sidebar-btn .fas{
    padding-right:10px;
}
.sidebar-btn:hover {
    background-color: #575757; 
    cursor: pointer;
    color:#ddd; 
}
.bagis{
    border:1px solid white;
}
.dashboard {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 10px;
}
.dot {
  height: 10px;
  width: 10px;
  margin-bottom: 2px;
  margin-left: 2px;
  background-color: lightGreen;
  border-radius: 50%;
  display: inline-block;
}
.card h5 {
    font-weight: bold;
    margin: 10px;
}
.card {
  background-color: whitesmoke;
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1); 
  margin: 10px;
  width: calc(22% - 20px);
  transition: transform 0.2s; 
  box-shadow:0px 0px 5px rgba(0,0,0,0.5);
  border-radius: 5px;
}
.card:hover {
  transform: translateY(-5px);
}
.card-body {
  display: flex;
  justify-content: space-between;
  padding-top: 0;
}

.else-div {
    display: flex;
    width: 100%;
    min-height: 100vh;
}

.modal-left {
    flex: 0 0 220px;
    height: 100vh;
}

.modal-right {
    flex-grow: 1;
    padding: 20px;
    overflow-y: auto; 
}
.containerr .table th, td{
  display:flex;
  justify-content:space-between;
}
.containerr{
  align-items:right;
  margin:0px 20px;

}
.containerr h4{
  font-weight:600;
}
.containerr .table thead{
  background-color:#333;
  padding:10px;
  margin:0;
  color:white;
}
.containerr .table td{
  border:1px solid #ddd;
  box-shadow:0px 0px 2px rgba(0,0,0,0.5);
  background-color:#f2f2f2;
  color:black;
}
thead{
  cursor:pointer;
  transition:0.2s;
}
thead:hover{
  background-color:#333;
  color:white;
}
.fas{
  padding:5px;
  border-radius:100%;
}
.totall, .edit-btn, .delete-btn{
  color: #007bff;
  border: 1px solid #007bff;
  padding:5px;
  cursor:pointer;
  background:#ffffff;
  border-radius:5px;
  transition:0.2s;
}
a:hover{
  background: #007bff;
  color: #ffffff;
}
.modal-title{
  line-height:30px;
}
.modal-body-scroll {
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}
.hee{
    background-color:#333;
}
.container table{
    font-size:13px;
}
.delete-btn {
    color: red; /* Change the color to red */
    cursor: pointer; /* Changes the cursor to indicate it's clickable */
    text-decoration: none; /* Removes the underline from the link */
}

.delete-btn:hover {
    color: darkred; /* Darker red color on hover for better UI feedback */
}
.containe .buton{
    background:#333;
    color: white;
    border:none;
    box-shadow:0px 0px 3px #333;
    padding:10px;
    border-radius:5px;
    transition:0.2s;
}
.containe .buton:hover{
    background: white;
    color: #333;
}
.logout {
    position: absolute;
    bottom: 0;
}
</style>
<body>

<div class="else-div">
    <div class="modal-left">

    <div class="sidebar">
        <div class="logo-container">
            <div class="logo">
                <img src="/tarangnan/admin/log/logo.jpg" alt="Logo">
            </div>
            <br>
            <div class="name">
                <h5>ADMIN</h5>
            </div>
        </div>

        <hr class="bagis" style="color:red;padding:0;margin:0;">

        <a href="dashboard.php" class="sidebar-btn" style="text-decoration:none;"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="/tarangnan/admin/table/table.php" class="sidebar-btn" style="text-decoration:none;"><i class="fas fa-chart-line"></i>Main Table</a>
        <a href="/tarangnan/admin/et/table.php" class="sidebar-btn" style="text-decoration:none;"><i class="fas fa-user-edit"></i> Update Employee</a>
        <a href="/tarangnan/admin/employee/add_employe.php"class="sidebar-btn" style="text-decoration:none;"><i class="fas fa-user-plus"></i> Add Employee</a>
        <a href="logout.php" class="sidebar-btn logout" style="text-decoration:none;"><i class="fas fa-solid fa-power-off"></i>Logout</a>
    </div>
    
</div>

<div class="modal-right">

    <div class="dashboard" style="position:position;">
    <div class="card" style="width: 18rem;">
        <h5>EMPLOYEES</h5>
        <div class="card-body">
        <div class="countz">
            <span class="dot"></span>
            <span style="font-size: 30px; font-weight: bold;"> <?php echo $totalEmployees; ?></span>
        </div>
            <img width="50" height="50" src="https://img.icons8.com/ios-filled/50/manager.png" alt="manager"/>
        </div>
    </div>
    <div class="card" style="width: 18rem;">
     <h5>ACTIVE EMPLOYEE</h5>
        <div class="card-body">
            <div class="countz">
                <span class="dot"></span>
                <span style="font-size: 30px; font-weight: bold;"><?php echo $activeEmployees; ?></span>
            </div>
            <img width="50" height="50" src="https://img.icons8.com/ios-filled/100/employee-card.png" alt="employee-card"/>    
        </div>
    </div>
    <div class="card" style="width: 18rem;">
     <h5>TOTAL DEPARMENT</h5>
        <div class="card-body">
        <div class="countz">
            <span class="dot"></span> 
            <span style="font-size: 30px; font-weight: bold;"><?php echo $totalDepartments;?></span>
        </div>
        <img width="50" height="50" src="https://img.icons8.com/ios-filled/100/department.png" alt="department"/>
        </div>
    </div>
    <div class="card" style="width: 18rem;">
     <h5>TOTAL POSITIONS</h5>
        <div class="card-body">
        <div class="countz">
            <span class="dot"></span>
            <span style="font-size: 30px; font-weight: bold;"><?php echo $totalPositions; ?></span>
        </div>
        <img width="50" height="50" src="https://img.icons8.com/ios-filled/100/collaborating-in-circle.png" alt="collaborating-in-circle"/>
        </div>
    </div>
  </div>
  <hr style="background:#ddd; box-shadow:0px 0px 10px #333;">
    <!-- Place this section where you want the table to appear -->
        <div class="containerr">

            <div class="containe">
                <!-- Trigger button for modal -->
                <button type="button" class="buton" data-toggle="modal" data-target="#addDepartmentPositionModal" style="text-decoration:none;">
                    Add Department and Position
                </button>
            </div>
<br>
            <table class="table">
                <thead>
                    <tr>
                        <th>
                          <div class="tb-head">All Department</div>
                            <div class="total">
                              <?php echo $totalDepartments; ?>
                              <i class="fas fa-arrow-down"></i>
                            </div>
                        </th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach ($departments as $department) { ?>
                    <tr>
                        <td>
                            <div class="tb-head" style="font-size:13px;"><?php echo htmlspecialchars($department['department_name']); ?></div>
                            <div class="actions" style="font-size:13px;">
                                <a class="edit-btn" style="text-decoration:none;" onclick="editDepartment(<?php echo $department['department_id']; ?>, '<?php echo htmlspecialchars($department['department_name'], ENT_QUOTES); ?>')" class="btn btn-primary btn-sm">Change Department Name</a>
                                <a class="totall" style="text-decoration:none;" data-toggle="modal" data-target="#positionsModal<?php echo $department['department_id']; ?>" data-departmentid="<?php echo $department['department_id']; ?>">See Position</a>
                                <a class="delete-btn" style="text-decoration:none;" data-departmentid="<?php echo $department['department_id']; ?>"><i class="fas fa-trash"></i></a>
                          </div>
                        </td>
                    </tr>

                    <!-- Modal Structure -->
                    <div class="modal fade" id="positionsModal<?php echo $department['department_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="positionsModalLabel<?php echo $department['department_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document"> <!-- Add modal-dialog-scrollable here -->
                            <div class="modal-content" style="margin-left:90px; background-color:#ddd;">
                                <div class="modal-header" style=" background-color:#333;">
                                    <h5 class="modal-title" id="positionsModalLabel<?php echo $department['department_id']; ?>">Department<br><?php echo htmlspecialchars($department['department_name']); ?></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:white;">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="hee" style="background-color:#333;padding:5px;"><h5 class='cc'>Positions</h5></div>
                                <div class="modal-body modal-body-scroll">
                                    <!-- Positions will be inserted here -->
                                    <!-- This is where you'll dynamically load positions related to the department -->
                                </div>
                                
                            </div>
                        </div>
                    </div>

                <?php } ?>

                </tbody>
            </table>
        </div>

    </div>
</div>

    <script>


            $('.modal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                var departmentId = button.data('departmentid'); // Extract info from data-* attributes
                var modal = $(this);
                // AJAX call to load positions
                $.ajax({
                    url: 'get_positions.php', // Point to the script you created
                    type: 'GET',
                    data: { departmentId: departmentId },
                    success: function(data) {
                        modal.find('.modal-body').html(data);
                    }
                });
            });

                

                function editPosition(positionId) {
    // Example: Navigate to an edit page, could also open a modal or another form of inline editing
    window.location.href = 'edit_position.php?positionId=' + positionId;
}

function deletePosition(positionId) {
    var confirmDelete = confirm('Are you sure you want to delete this position?');
    if (confirmDelete) {
        // Send an AJAX request to delete the position
        $.ajax({
            url: 'delete_position.php', // This script should handle the deletion
            type: 'POST',
            data: {positionId: positionId},
            success: function(response) {
                // Optionally refresh the page or remove the position from the DOM
                window.location.reload();
            },
            error: function(xhr, status, error) {
                // Handle error
                console.error(error);
            }
        });
    }
}

// Function to open the edit modal and populate it with the current position's data
function editPosition(positionId, positionTitle) {
    $('#editPositionModal').modal('show');
    $('#positionIdEdit').val(positionId);
    $('#positionTitleEdit').val(positionTitle);
}

// Handling the form submission
$(document).ready(function() {
    $('#editPositionForm').submit(function(e) {
        e.preventDefault(); // Prevent default form submission

        var formData = {
            position_id: $('#positionIdEdit').val(),
            position_title: $('#positionTitleEdit').val()
        };

        console.log(formData); // Debugging line to see what's sent

        // AJAX call to update the position
        $.ajax({
            url: 'update_position.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                alert('Position updated successfully!');
                $('#editPositionModal').modal('hide');
                location.reload(); // Or update the UI as necessary
            },
            error: function(xhr, status, error) {
                console.error('Update failed:', error);
                alert('Error updating position.');
            }
        });
    });
});
// Open the edit department modal with current data
function editDepartment(departmentId, departmentName) {
    // Populate the modal fields
    $('#departmentIdEdit').val(departmentId);
    $('#departmentNameEdit').val(departmentName);

    // Show the modal
    $('#editDepartmentModal').modal('show');
}


// Handle form submission for editing department
$(document).ready(function() {
    $('#editDepartmentForm').submit(function(e) {
        e.preventDefault(); // Prevent the form from submitting in the traditional way

        var formData = {
            department_id: $('#departmentIdEdit').val(),
            department_name: $('#departmentNameEdit').val()
        };

        // AJAX call to update_department.php
        $.ajax({
            url: 'update_department.php', // Ensure this points to the correct file location
            type: 'POST',
            data: formData,
            success: function(response) {
                alert('Department updated successfully!');
                $('#editDepartmentModal').modal('hide');
                location.reload(); // Or use a more sophisticated way to update the UI
            },
            error: function(xhr, status, error) {
                alert('Error updating department: ' + error);
            }
        });
    });
});
</script>

<footer>

<!-- Edit Department Modal -->
<div class="modal fade" id="editDepartmentModal" tabindex="-1" role="dialog" aria-labelledby="editDepartmentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDepartmentModalLabel">Edit Department</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editDepartmentForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="departmentNameEdit">Department Name:</label>
                        <input type="text" class="form-control" id="departmentNameEdit" name="department_name" required>
                    </div>
                    <input type="hidden" id="departmentIdEdit" name="department_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>


  <!-- Modal -->

    <!-- Edit Position Modal -->
<div class="modal fade" id="editPositionModal" tabindex="-1" role="dialog" aria-labelledby="editPositionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPositionModalLabel">Edit Position</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editPositionForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="positionTitleEdit">Position Title:</label>
                        <input type="text" class="form-control" id="positionTitleEdit" name="position_title" required>
                    </div>
                    <input type="hidden" id="positionIdEdit" name="position_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Structure -->
<div class="modal fade" id="addDepartmentPositionModal" tabindex="-1" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#333; color:white;">
                <h5 class="modal-title">Add Department and Positions</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" class="modal-body" style="padding:0;">
                <div class="form-group" style="padding:0px 20px; margin-top:10px;">
                    <label for="newDepartmentName"><b>Add New Department (optional)</b></label>
                    <input type="text" class="form-control" id="newDepartmentName" placeholder="Enter New Department" name="new_department_name">
                    <small class="form-text text-muted">Leave blank if adding a position to an existing department.</small>
                </div>

                <div class="form-group" style="padding:0px 20px;">
                    <label for="existingDepartment"><b>Or Select Existing Department</b></label>
                    <select class="form-control" id="existingDepartment" name="existing_department_id">
                        <option value="">Select Department</option>
                        <?php foreach ($existingDepartments as $dept) {
                            echo "<option value='{$dept['department_id']}'>{$dept['department_name']}</option>";
                        } ?>
                    </select>
                </div>
                <hr style="background:#ddd; border:1px solid #333;">
                <div class="form-group positions" style="padding:0px 20px;">
                    <label><b>Positions</b></label>
                    <div id="positionInputsContainer">
                        <input type="text" class="form-control" placeholder="Enter Position" name="position_title[]">
                        <br>
                    </div>
                    <button type="button" class="btn btn-secondary mt-2" id="addPositionBtn">Add Another Position</button>
                </div>
                <div class="modal-footer" style="background:#333;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

</footer>
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

        $(document).ready(function() {
    $('.delete-btn').click(function() {
        var departmentId = $(this).data('departmentid');
        if(confirm('Are you sure you want to delete this department?')) {
            $.ajax({
                url: 'delete_department.php',
                type: 'POST',
                data: {departmentId: departmentId},
                success: function(response) {
                    // Reload the page or remove the row from the table
                    location.reload(); // Simplest way
                }
            });
        }
    });
        // Add similar handling for edit-btn clicks here
});
    </script>
</body>
</html>
