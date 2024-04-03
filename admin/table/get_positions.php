<style>
        .position-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px;
            background-color: #f2f2f2;
            border-radius: 5px;
            box-shadow:0px 0px 2px #333;
            cursor:pointer;
            transition:0.3s;
            margin:5px 0px;
        }
        .position-actions button {
            margin-left: 10px;
        }
        h5, .cc{
            font-family:"Inter", sans-serif;
            font-weight:500;
            letter-spacing:1px;
        }
        h5{
            color:white;
        }
        .cc{
            text-align:center;
            color:white;
        }
        .employee-count {
            color: black;
            box-shadow:0px 0px 1px #333;
            padding:3px;
            border-radius:10px;
            font-weight:400;
            font-size:13px;
        }
        .num{
            font-weight:600;

        }
        .btn-simple {
        background-color: #ffffff;
        color: #007bff;
        border: 1px solid #007bff;
        padding: 4px;
        border-radius: 5px;
        text-decoration: none;
        font-size: 13px;
        margin-right: 5px;
        text-decoration:none;
        }
        .btn-simple:hover {
            background-color: #007bff;
            color: #ffffff;
        }
        .position-tit{
            font-size:13px;
        }
    </style>
<?php
// Your database connection details
$servername = "localhost";
$username = "root";
$password = "";
$database = "hrmo_tarangnan";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if departmentId is set
if(isset($_GET['departmentId'])) {
    $departmentId = $_GET['departmentId'];
    $positions = getPositionsForDepartment($conn, $departmentId);

    // Modify this part to include Delete and Edit buttons

    foreach ($positions as $position) {
        echo "<div class='position-item'>" . 
            "<span class='position-tit'>" . htmlspecialchars($position['position_title']) . "</span>" .
            "<span class='employee-count'> Number of Employees ( <span class='num'> " . htmlspecialchars($position['employee_count']) . " </span> )</span>" .
            // Add Edit and Delete buttons
            "<div class='position-actions'>" .
                "<a type='button' class='btn-simple' onclick='editPosition(" . $position['position_id'] . ", \"" . htmlspecialchars($position['position_title'], ENT_QUOTES) . "\")'>Change Position</a>" .
                "<a type='button' class='btn-simple' onclick='deletePosition(" . $position['position_id'] . ")'>Delete</a>" .
            "</div>" .
            "</div>";
    }
    
}

// Modified to include position_id in the output
function getPositionsForDepartment($conn, $departmentId) {
    $positions = [];
    // Updated SQL query to count active employees per position
    // Assumes 'active' status indicates active employment
    $sql = "SELECT p.position_id, p.position_title, COUNT(sp.employee_id) AS employee_count
            FROM positions p
            LEFT JOIN serviceperiods sp ON p.position_id = sp.position_id AND sp.status = 'active'
            WHERE p.department_id = ?
            GROUP BY p.position_id";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
    
    $stmt->bind_param("i", $departmentId);
    if (!$stmt->execute()) {
        die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $positions[] = $row; // Now includes employee_count, representing active employees
    }
    $stmt->close();
    return $positions;
}

?>
