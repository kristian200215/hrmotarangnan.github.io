
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Service Records</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<style>
    body {
        background-color: #ddd;
        font-family: "Inter", sans-serif;
        margin:0;
        padding:0;
    }
    .btn {
        margin-right: 5px;
        border-radius: 20px;
    }
    .navbar {
        background-color: #2D3339;
        color: white;
        margin-bottom: 1%;
        padding: 10px 20px;
        -webkit-box-shadow: -0.5px 4px 16.5px -0.5px #000000;
        -moz-box-shadow: -0.5px 4px 16.5px -0.5px #000000;
        box-shadow: -15px 14px 25px -16px #000000;
    }
    .logo {
        display: flex;
        align-items: center;
        margin: 0;
        gap: 10px;
    }
    .logo img {
        border-radius: 50%;
    }
    .logo h5 {
        padding-top: 10px;
        margin:0;
    }
    .logo span p {
        color:  #5a6672;
        font-size: 10px;
        font-style: italic;
    }
    .status-icon {
        margin-right: 5px;
    }
    .table th, .table td {
        padding:5px;
        border:1px solid black;
        text-align:center;
        background-color:#ddd;
    }
    .table thead th {
        background-color: #333;
        color: #ffffff;
        text-align:center;
    }
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0,0,0,.05);
    }
    .cont-1{
        display:flex;
        justify-content:space-between;
        margin-top:20px;
        margin-left:20px;
        margin-right:20px;
        padding:0;
    }
    .cont-1 h2{
        font-weight: 700;
    }
    .containerr{
        padding:0 20px;
    }
    .container table{
        text-align:center;
    }
    .status-active, .status-promoted, .status-retired, .status-transferred, .status-endofterm {
        text-align: center;
        color: black;
    }
    .status-promoted {
        color: black;
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
</style>
<body>
<nav class="navbar">
        <div class="logo">
            <img src="/tarangnan/main/log/logo.jpg" width="70" alt="Logo"><span><h5>TARANGNAN | HRMO</h5>
            <p>Develop by Samar College Student 2024</p></span> </a>
            
        </div>
        <a class="btn btn-secondary" href="/tarangnan/login/index.php">ADMIN</a>

    </nav>

     <!-- Updated search bar with icon -->
     <div class="cont-1">
            <h2>Employee Service Records</h2>

            <div class="input-group mb-3">
                <input type="text" id="searchBar" class="form-control" placeholder="Search in table..." aria-label="Search" aria-describedby="basic-addon1">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fas fa-search"></i></span>
                </div>
            </div>
        </div>

    <div class="containerr">
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

    <script>
$(document).ready(function() {
    // Fetch and display employee data
    $.getJSON('fetch_data.php', function(data) {
        let employees = {};

        $.each(data, function(key, value) {
            if (!employees[value.employee_id]) {
                employees[value.employee_id] = {
                    name: value.first_name + ' ' + value.last_name,
                    records: []
                };
            }
            employees[value.employee_id].records.push({
                position_title: value.position_title,
                start_date: value.start_date,
                end_date: value.end_date ? value.end_date : 'Present',
                status: value.status
            });
        });

        $.each(employees, function(id, employee) {
            employee.records.sort(function(a, b) {
                return new Date(b.start_date) - new Date(a.start_date);
            });

            let firstRecord = true;
            $.each(employee.records, function(index, record) {
                let statusClass = '';
                let iconHtml = '';
                switch(record.status) {
                    case 'Transferred': statusClass = 'status-transferred'; iconHtml = '<i class="fas fa-exchange-alt"></i> '; break;
                    case 'Promoted': statusClass = 'status-promoted'; iconHtml = '<i class="fas fa-arrow-up"></i> '; break;
                    case 'Retired': statusClass = 'status-retired'; iconHtml = '<i class="fas fa-retirement"></i> '; break;
                    case 'Active': statusClass = 'status-active'; iconHtml = '<i class="fas fa-check"></i> '; break;
                    case 'Resigned': statusClass = 'status-resigned'; iconHtml = '<i class="fas fa-sign-out-alt"></i> '; break;
                    case 'End Of Term': statusClass = 'status-endofterm'; iconHtml = '<i class="fas fa-sign-out-alt"></i> '; break;
                }

                let viewButton = firstRecord ? `<a href="view_service_record.php?employee_id=${id}" class="btn-simple">Export to PDF</a>` : '';
                let actionButtons = `${viewButton}`;
                if(firstRecord) {
                    actionButtons += `<a href="#" class="btn-simple toggle-history" data-employee-id="${id}">View History</a>`;
                }

                let hiddenClass = index > 0 ? 'hidden-record' : '';
                let rows = `<tr class="${hiddenClass} employee-${id}" data-employee-id="${id}">
                                <td>${firstRecord ? employee.name : ''}</td>
                                <td>${record.position_title}</td>
                                <td>${record.start_date}</td>
                                <td>${record.end_date}</td>
                                <td class="${statusClass}">${iconHtml}${record.status}</td>
                                <td>${actionButtons}</td>
                            </tr>`;
                $('#employeeData').append(rows);
                firstRecord = false;
            });

            if(employee.records.length > 1) {
                $(`.employee-${id}:not(:first)`).hide();
            }
        });

        $(document).on('click', '.toggle-history', function() {
            let employeeId = $(this).data('employee-id');
            $(`.employee-${employeeId}:not(:first)`).toggle();
        });
    });

    // Search functionality
    $("#searchBar").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#employeeData tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
</script>
</body>
</html>
