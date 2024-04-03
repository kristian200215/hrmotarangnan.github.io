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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
    <!--<link rel="stylesheet" href="styles.css">-->
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
    .status-endofterm {
        background-color: black;
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
<div class="modal-left">
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
<!-- New navbar -->
<!--
<nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="margin-left: 220px;">
  <a class="navbar-brand" href="#">Admin Panel</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
    <div class="navbar-nav ml-auto">
      <a class="nav-item nav-link" href="logout.php">Logout <i class="fas fa-sign-out-alt"></i></a>
    </div>
  </div>
</nav>
-->
<div class="containerr">
    <div class="cont-1">
            <h2>Employee Service Records</h2>

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

</body>
</html>