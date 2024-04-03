<?php
session_start(); // Start the session
if(!isset($_SESSION['username'])) {
    // If the user is not logged in, redirect to login page
    header("Location: /tarangnan/main/table/table.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Service Records</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
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
.containerr {
    transition: margin-left 0.5s;
    padding: 20px;
    margin-left: 210px;
}
    .btn {
        margin-right: 5px;
        border-radius: 20px;
    }
    .status-icon {
        margin-right: 5px;
    }
    .table th, .table td {
        padding:5px;
        border:1px solid #999292;
        text-align:center;
        background-color: #f2f2f2;
        font-size:15px;
    }
    .table thead th {
        background-color: #333;
        color: #ffffff;
        text-align:center;
    }
    .cont-1{
        display:flex;
        justify-content:space-between;
        margin-top:0px;
        margin-left:20px;
        margin-right:20px;
        padding:0;
    }
    .cont-1 h2{
        text-shadow:0px 0px 5px rgb(0, 0, 0);
    }
    .container table{
        text-align:center;
    }
    .status-badge {
        padding: 3px 8px;
        border-radius: 12px;
        display: inline-block;
        font-size: 0.8em;
    }
    
    .status-active {
        background-color: #4CAF50;
        color: white;
    }
    
    .status-retired {
        background-color: #F44336;
        color: white;
    }
    
    .status-resigned {
        background-color: #FF9800;
        color: white;
    }
    
    .status-promoted {
        background-color: #FFEB3B;
        color: black;
    }
    
    .status-transferred {
        background-color: #9E9E9E;
        color: white;
    }
    
    .btn-simple {
        background-color: #ffffff;
        color: #007bff;
        border: 1px solid #007bff;
        padding: 5px;
        border-radius: 5px;
        text-decoration: none;
        font-size: 14px;
        margin-right: 5px;
        text-decoration:none;
    }
    .btn-simple:hover {
        background-color: #007bff;
        color: #ffffff;
    }
    .input-group{
        width:30%;
    }
    .bagis{
        border:1px solid white;
    }
    .logout {
        position: absolute;
        bottom: 0;
    }
</style>
<body>
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

    <a href="/tarangnan/admin/table/dashboard.php" class="sidebar-btn" style="text-decoration:none;"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="/tarangnan/admin/table/table.php" class="sidebar-btn" style="text-decoration:none;"><i class="fas fa-chart-line"></i>Main Table</a>
    <a href="/tarangnan/admin/et/table.php" class="sidebar-btn" style="text-decoration:none;"><i class="fas fa-user-edit"></i> Update Employee</a>
    <a href="/tarangnan/admin/employee/add_employe.php"class="sidebar-btn" style="text-decoration:none;"><i class="fas fa-user-plus"></i> Add Employee</a>
    <a href="logout.php" class="sidebar-btn logout" style="text-decoration:none;"><i class="fas fa-solid fa-power-off"></i>Logout</a>
</div>
    <div class="containerr">
    <div class="cont-1">
            <h2>Employee Update Status</h2>

            <div class="input-group mb-3">
                <input type="text" id="searchBar" class="form-control" placeholder="Search in table..." aria-label="Search" aria-describedby="basic-addon1">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
                </div>
            </div>
        </div>
        <table class="table table-striped">
            <thead>
            <tr>
                    <th><i class="fas fa-user"></i> Name</th>
                    <th><i class="fas fa-briefcase"></i> Position</th>
                    <th><i class="fas fa-calendar-alt"></i> Start Date</th>
                    <th><i class="fas fa-calendar-check"></i> End Date</th>
                    <th><i class="fas fa-info-circle"></i> Status</th>
                    <th><i class="fas fa-tools"></i> Actions</th>
                </tr>
            </thead>
            <tbody id="employeeData"></tbody>
        </table>
    </div>

    <!-- Modal for selecting new status -->
    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Select New Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <select class="form-control" id="newStatusSelect">
                        <option value="Retired">Retired</option>
                        <option value="Promoted">Promoted</option>
                        <option value="Resigned">Resigned</option>
                        <option value="Transferred">Transferred</option>
                        <option value="End Of Term">End Of Term</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updateStatus()">Update</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for adding new service record -->
    <div class="modal fade" id="addRecordModal" tabindex="-1" role="dialog" aria-labelledby="addRecordModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRecordModalLabel">Add New Service Record</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Form for adding new service record -->
                    <form id="addRecordForm">
                        <div class="form-group">
                            <label for="start_date">Start Date:</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        <div class="form-group">
                            <label for="end_date">End Date:</label>
                            <input type="date" class="form-control" id="end_date" name="end_date">
                        </div>
                        <div class="form-group">
                            <label for="department">Department:</label>
                            <select class="form-control" id="department" name="department" required onchange="fetchPositions($('#department').val())">
                                <option value="">Select Department</option>
                                <!-- Department ine -->
                            </select>
                            
                        </div>
                        <div class="form-group">
                            <label for="position">Position:</label>
                            <select class="form-control" id="position" name="position" required>
                                <option value="">Select Position</option>
                                <!-- Position adi -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="salary">Salary:</label>
                            <input type="number" class="form-control" id="salary" name="salary" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="employment_type">Employment Type:</label>
                            <select class="form-control" id="employment_type" name="employment_type" required>
                                <option value="">Select Employment Type</option>
                               
                                <option value="Casual">Casual</option>
                                <option value="Permanent">Permanent</option>
                                <option value="Elective">Elective</option>
                                <option value="Coterminous">Coterminous</option>

                            </select>
                        </div>
                        <div class="form-group">
                            <label for="station_assignment">Station Assignment:</label>
                            <input type="text" class="form-control" id="station_assignment" name="station_assignment">
                        </div>
                        <div class="form-group">
                            <label for="branch_type">Branch Type:</label>
                            <select class="form-control" id="branch_type" name="branch_type" required>
                                <option value="">Select Branch Type</option>
                                <option value="Local">Local</option>
                                <option value="National">National</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="employee_status">Employee Status:</label>
                            <select class="form-control" id="employee_status" name="employee_status" required>
                                <option value="Active">Active</option>
                                <option value="Retired">Retired</option>
                                <option value="Transferred">Transferred</option>
                                <option value="Resigned">Resigned</option>
                                <option value="Promoted">Promoted</option>
                                <option value="End Of Term">End Of Term</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="addServiceRecord()">Add Record</button>
                </div>
            </div>
        </div>
    </div>

     <!-- Modal -->
    
                <!-- Modal -->

    <script>
        $(document).ready(function() {
    
            $.getJSON('fetch_data.php', function(data) {
                let employees = {}; 

                $.each(data, function(key, value) {
                    if (!employees[value.employee_id]) {
                        employees[value.employee_id] = {
                            name: value.first_name + ' ' + value.last_name,
                            position: value.position_title,
                            start_date: value.start_date,
                            end_date: value.end_date ? value.end_date : 'Present',
                            status: value.status 
                        };
                    } else {
                        if (value.start_date > employees[value.employee_id].start_date) {
                            employees[value.employee_id].position = value.position_title;
                            employees[value.employee_id].start_date = value.start_date;
                            employees[value.employee_id].end_date = value.end_date ? value.end_date : 'Present';
                            employees[value.employee_id].status = value.status;
                        }
                    }
                });

                $.each(employees, function(id, employee) {
                    let row = `<tr>
                                    <td>${employee.name}</td>
                                    <td>${employee.position}</td>
                                    <td>${employee.start_date}</td>
                                    <td>${employee.end_date}</td>
                                    <td>${employee.status}</td>`;

    
                    let actionButton = `<button class="btn btn-primary btn-sm" onclick="showStatusModal(${id})" ${employee.status !== 'Active' ? 'disabled' : ''}>Update Status</button>`;
                    row += `<td>${actionButton} <button class="btn btn-success btn-sm" onclick="showAddRecordModal(${id})" ${employee.status === 'Active' ? 'disabled' : ''}>Add New Service Record</button></td></tr>`;

                    $('#employeeData').append(row);
                });
            });
        });
        $("#searchBar").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#employeeData tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

      
        function showStatusModal(employeeId) {
            $('#statusModal').modal('show');
            $('#statusModal').data('employeeId', employeeId); 
        }

        function showAddRecordModal(employeeId) {
            $('#addRecordModal').modal('show');
            $('#addRecordModal').data('employeeId', employeeId);
        }

      
        function updateStatus() {
            var employeeId = $('#statusModal').data('employeeId');
            var newStatus = $('#newStatusSelect').val();

           
            $.post('update_status.php', { employee_id: employeeId, new_status: newStatus }, function(response) {
                // Handle response
                alert(response);
                location.reload();
            });

           
            $('#statusModal').modal('hide');
        }

      
        function addServiceRecord() {
            var employeeId = $('#addRecordModal').data('employeeId');
            var formData = $('#addRecordForm').serialize();
            
          
            $.post('add_service_record.php', formData + '&employee_id=' + employeeId, function(response) {
              
                alert(response);
                location.reload();
            });

           
            $('#addRecordModal').modal('hide');
        }

       
    function fetchDepartments() {
        $.ajax({
            url: 'get_departments.php',
            type: 'GET',
            success: function(response) {
                $('#department').html('<option value="">Select Department</option>' + response);
            }
        });
    }

   
    function fetchPositions(departmentId) {
        if (departmentId) {
            $.ajax({
                url: 'get_positions.php',
                type: 'GET',
                data: { department_id: departmentId },
                success: function(response) {
                    $('#position').html('<option value="">Select Position</option>' + response);
                }
            });
        } else {
            $('#position').html('<option value="">Select Position</option>'); 
        }
    }

  
    function showAddRecordModal(employeeId) {
        $('#addRecordModal').modal('show');
        $('#addRecordModal').data('employeeId', employeeId); 

        fetchDepartments();
    }
    </script>
</body>
</html>
