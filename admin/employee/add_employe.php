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
    <title>Add Employee Record</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<style>
.service-period {
    border: 1px solid #ddd;
    box-shadow:0px 0px 2px #282828;
    padding: 5px;
    margin-bottom: 20px;
    border-radius: 5px;
    margin:0px 5px;
}
body, html {
    margin: 0;
    padding: 0;
    font-family: "Poppins", sans-serif;
    background-color: #f4f4f4;
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
.first-line{
    display:flex;
    justify-content:space-around;
}
.first-line .form-group{
    width:30%;
    line-height:5px;
}
.container{
    box-shadow:0px 0px 5px black;
    margin-left:300px;
    padding:10px;
    border-radius:10px;
}
.container h2 {
    font-weight: bold;
}
.second-line{
    display:flex;
    justify-content:space-around;
}
.second-line .form-group{
    width:30%;
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
            <div class="name">
                <h5>ADMIN</h5>
            </div>
        </div>
    
        <hr class="bagis" style="padding:0;margin:0;">
    
        <a href="/tarangnan/admin/table/dashboard.php" class="sidebar-btn" style="text-decoration:none;"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="/tarangnan/admin/table/table.php" class="sidebar-btn" style="text-decoration:none;"><i class="fas fa-chart-line"></i>Main Table</a>
        <a href="/tarangnan/admin/et/table.php" class="sidebar-btn" style="text-decoration:none;"><i class="fas fa-user-edit"></i> Update Employee</a>
        <a href="/tarangnan/admin/employee/add_employe.php"class="sidebar-btn" style="text-decoration:none;"><i class="fas fa-user-plus"></i> Add Employee</a>
        <a href="logout.php" class="sidebar-btn logout" style="text-decoration:none;"><i class="fas fa-solid fa-power-off"></i>Logout</a>
    </div>
<br>
    <div class="container">

            <h2 style="text-align:center;">Add Employee Record</h2>
            <br>
        <form action="add_employee.php" method="post" id="employeeForm">
        <div class="first-line">
            <div class="form-group">
                <label for="birthday">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name" required>
            </div>

            <div class="form-group">
                <label for="birthday">Middle Name</label>
                <input type="text" class="form-control" id="middle_name" name="middle_name" placeholder="Middle Name" required>
            </div>
            <div class="form-group">
                <label for="birthday">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name" required>
            </div>
        </div>
    <br>
        <div class="first-line">
            <div class="form-group">
                <label for="contact_number">Contact Number</label>
                <input type="text" class="form-control" id="contact_number" name="contact_number"  placeholder="Contact Number" required>
            </div>
            <div class="form-group">
                <label for="address">Birthplace</label>
                <input type="text" class="form-control" id="address" name="address"  placeholder="Birth Place" required>
            </div>
            <div class="form-group">
                <label for="birthday">BirthDate</label>
                <input type="date" class="form-control" id="birthday" name="Birthdate" required>
            </div>
        </div>

       <br>
            <div id="servicePeriodsSection">
          
            </div>
            <br>
                
                <button type="button" class="btn btn-secondary" onclick="addServicePeriod()">Add Service Period</button>
                <br><br>
                <footer>
                <button type="submit" class="btn btn-primary">Submit</button>
</footer>
        </form>
    </div>
<br>
<br>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>


    
                <!-- Modal -->
    <script>
        $(document).ready(function() {
        
            addServicePeriod();
    
           
            $('#servicePeriodsSection').on('click', '.remove-service-period', function() {
                $(this).closest('.service-period').remove();
            });
        });
    
        function addServicePeriod() {
            var servicePeriodHtml = `
            <div class="service-period">
                <h2 style="text-align:center;"> Service Period </h2>
        <div class="second-line">
            <div class="form-group">
                <label>Start Date</label>
                <input type="date" class="form-control" name="start_date[]" required>
            </div>
                <div class="form-group">
                    <label>End Date</label>
                    <input type="date" class="form-control" name="end_date[]">
                </div>
                    <div class="form-group">
                        <label>Department</label>
                        <select class="form-control department-select" name="department[]" required onchange="fetchPositions(this, this.value)" required>
                            <option value="">Select Department</option>
                            <!-- Department options will be loaded here dynamically -->
                        </select>
                    </div>
        </div>    
            
        <div class="second-line">
                <div class="form-group">
                    <label>Position</label>
                    <select class="form-control position-select" name="position[]" required>
                        <option value="">Select Position</option>
                        <!-- Position options will be loaded here based on selected Department -->
                    </select>
                </div>
                    <div class="form-group">
                        <label for="salary">Annual Salary</label>
                        <input type="number" step="0.01" class="form-control" name="salary[]">
                    </div>
                        <div class="form-group">
                            <label for="employment_type">Employment Type</label>
                            <select class="form-control" name="employment_type[]" required>
                                <option value="">Select Employment Type</option>
                                <option value="Casual">Casual</option>
                                <option value="Permanent">Permanent</option>
                                <option value="Elective">Elective</option>
                                <option value="Coterminous">Coterminous</option>
                            </select>
                        </div>
        </div>

        <div class="second-line">
                <div class="form-group">
                    <label for="station_assignment">Station Assignment</label>
                    <input type="text" class="form-control" name="station_assignment[]">
                </div>
                    <div class="form-group">
                        <label for="branch_type">Branch Type</label>
                        <select class="form-control" name="branch_type[]" required>
                            <option value="">Select Branch Type</option>
                            <option value="Local">Local</option>
                            <option value="National">National</option>
                        </select>
                    </div>
                        <div class="form-group">
                            <label for="employee_status">Employee Status</label>
                            <select class="form-control" name="employee_status[]" required>
                                <option value="Active">Active</option>
                                <option value="Retired">Retired</option>
                                <option value="Transferred">Transferred</option>
                                <option value="Resigned">Resigned</option>
                                <option value="Promoted">Promoted</option>
                                <option value="End OF Term">End Of Term</option>
                            </select>
                        </div>
        </div>
        <button type="button" class="btn btn-danger remove-service-period">Remove</button>
    </div>
`;

    
            $('#servicePeriodsSection').append(servicePeriodHtml);
       
            fetchDepartments($('.department-select').last());
        }
    
  
        function fetchDepartments(element) {
            $.ajax({
                url: 'get_departments.php', 
                type: 'GET',
                success: function(response) {
                    $(element).html(response);
                }
            });
        }
    

        window.fetchPositions = function(element, departmentId) {
            if (departmentId) {
                $.ajax({
                    url: 'get_positions.php', 
                    type: 'GET',
                    data: { department_id: departmentId },
                    success: function(response) {
                        $(element).closest('.service-period').find('.position-select').html(response);
                    }
                });
            }
        }
    </script>
    
             
    
</body>
</html>