<?php
$host = 'localhost';
$dbname = 'hrmo_tarangnan';
$username = 'root';
$password = '';
$employee_id = isset($_GET['employee_id']) ? $_GET['employee_id'] : 0;

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT 
            e.employee_id, e.first_name, e.middle_name, e.last_name, e.address, e.birthday,
            pos.position_title, sp.start_date, sp.end_date, sp.employment_type, sp.salary, sp.station_assignment, sp.branch_type
        FROM 
            Employees e
        INNER JOIN 
            ServicePeriods sp ON e.employee_id = sp.employee_id
        INNER JOIN 
            Positions pos ON sp.position_id = pos.position_id
        WHERE 
            e.employee_id = ?
        ORDER BY 
            sp.start_date";



$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();

$records = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $records[] = $row;
    }
} else {
    echo "<p>No service records found.</p>";
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Service Record</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
            /* Reset some default styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        width: 100%;
        font-family: 'Poppins', sans-serif;
        background-color: #2d3339;
       
    }

    /* Navigation styles */
    .nav-header {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        display: flex;
        justify-content: space-around;
        text-align: center;
        background-color: #2d3339;
        color: #fff;
        padding: 10px 20px;
        z-index: 1000;
        -webkit-box-shadow: -15px 14px 25px -16px #000000;
        -moz-box-shadow: -15px 14px 25px -16px #000000;
        box-shadow: -15px 14px 25px -16px #000000;
                
    }

    .nav-header h2 {
        margin: 0;
    }
    .nav-header .btn {
        color: #fff;
    }

    /* Container styles */
    .container {
        margin-top: 60px; /* Account for the fixed navigation height */
        padding: 20px;
    }

    /* Table styles */
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .table th, .table td {
        border: 1px solid black;
        padding: 8px;
        text-align: left;
    }

    .table th {
        background-color: #2d3339;
        color: #fff;
    }

    .table td {
        background-color: #fff;
    }

    .no-print {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 20px;
    }

    .control-buttons {
        display: flex;
        justify-content: center;
    }

    /* Bond paper styles */
    .bond-paper {
        background-color: #fff;
        margin-top: 2%;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .header-info {
        margin-bottom: 20px;
        margin-top: 20px;
    }
    nav h2 img {

        width: 70px;
        padding-right: 5px;
        border-radius: 50%;

    }
    .btn {
        margin-top: 5px;
    }

    /* Responsive styles */
    @media screen and (max-width: 768px) {
        .container {
            padding: 10px;
        }
    }
      
    </style>
</head>
<body>
        <div class="container">
            <nav class="nav-header">
                <h2><img src="/tarangnan/admin/log/logo.jpg" alt="Logo">Service Record</h2>
                <div class="btn">
                <button onclick="exportPDF()" class="btn btn-danger">Export to PDF</button>
                <a class="btn btn-secondary" href="/tarangnan/admin/table/table.php">BACK</a>
                </div>
            </nav>

            <div class="no-print control-buttons">
             <div class="bond-paper">
                <div class="header-info">
                    <table>
                        <tr>
                            <td style="vertical-align: bottom;"><strong>Name:</strong></td>
                            <td>
                                <div style="border-bottom: 1px solid black; text-align: center; min-width: 150px; display: inline-block;">
                                    <?php echo htmlspecialchars($records[0]['last_name'] ?? ''); ?>
                                </div>
                                <div style="border-bottom: 1px solid black; text-align: center; min-width: 150px; display: inline-block;">
                                    <?php echo htmlspecialchars($records[0]['first_name'] ?? ''); ?>
                                </div>
                                <div style="border-bottom: 1px solid black; text-align: center; min-width: 150px; display: inline-block;">
                                    <?php echo htmlspecialchars($records[0]['middle_name'] ?? ''); ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td></td> <!-- Empty cell under 'Name:' -->
                            <td>
                                <span style="margin-left: 40px; margin-right: 70px; display: inline-block; text-align: center;">Last Name</span>
                                <span style="margin-right: 70px; display: inline-block; text-align: center;">First Name</span>
                                <span style="display: inline-block; text-align: center;">Middle Name</span>
                            </td>
                        </tr>
                    </table>
                </div>

                <table class="table" id="serviceRecordTable">
                <thead>
                    <tr>
                        <th colspan="3" style="text-align: center;">INCLUSIVE DATES OF SERVICE</th>
                        <th colspan="5" style="text-align: center;">RECORDS OF APPOINTMENT</th>
                    </tr>
                    <tr>
                        <th>FROM</th>
                        <th>TO</th>
                        <th>DESIGNATION</th>
                        <th>STATUS</th>
                        <th>SALARY PER ANNUM</th>
                        <th>STATION ASSIGNMENT</th>
                        <th>BRANCH</th>
                        <th>L/V Absence w/o Pay</th>
                    </tr>

                            </thead>
                            <tbody>
                            <?php foreach ($records as $record): ?>
                                <?php
                                $originalStartDate = new DateTime($record['start_date']);
                                $startDate = clone $originalStartDate;
                                $endDate = $record['end_date'] ? new DateTime($record['end_date']) : new DateTime();
                                $originalEndDate = clone $endDate;
                                $isFirstPeriod = true;
                                while ($startDate < $originalEndDate) {
                                    if ($isFirstPeriod) {
                                        $tempEnd = new DateTime($startDate->format('Y') . '-12-31');
                                        $isFirstPeriod = false;
                                    } else {
                                        $startDate = new DateTime($startDate->format('Y') . '-01-01');
                                        $tempEnd = new DateTime($startDate->format('Y') . '-12-31');
                                    }
                                    if ($startDate->format('Y') >= $originalEndDate->format('Y')) {
                                        $tempEnd = $originalEndDate;
                                    }
                                    echo "<tr>";
                                    echo "<td>" . date('m-d-y', strtotime($startDate->format('Y-m-d'))) . "</td>";
                                    echo "<td>" . date('m-d-y', strtotime($tempEnd->format('Y-m-d'))) . "</td>";
                                    echo "<td>" . htmlspecialchars($record['position_title']) . "</td>";
                                    echo "<td>" . htmlspecialchars($record['employment_type']) . "</td>";
                                    echo "<td> P" . number_format($record['salary'], 2) . "</td>";
                                    echo "<td>" . htmlspecialchars($record['station_assignment']) . "</td>";
                                    echo "<td>" . htmlspecialchars($record['branch_type']) . "</td>";
                                    echo "</tr>";
                                    
                                    $startDate = $tempEnd->modify('+1 day');
                                }
                                ?>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    <script>
       function exportPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

     // Add logo image in base64 format; replace '...' with actual base64 string of 'tarangnan.jpg'
     const logo = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBxMSEhUSEhMWFRUXGB4bFxgYGBsgHxshHx0dHyAeIB8fISggIiElHRgZITEiJiotLi4uHx8zODMsNygtLisBCgoKDg0OGxAQGjUlICYtNTItMC0tLy8wLS0vLS4tLS02LS0tLy0tLy0tLS0tLy0tLS0tLS8tLS0vLS0tLS0tLf/AABEIAOAA4AMBIgACEQEDEQH/xAAcAAACAwEBAQEAAAAAAAAAAAAFBgAEBwMCAQj/xABLEAACAQIEAwUEBgcGAwcFAQABAgMEEQAFEiEGMUETIlFhcQcygZEUI0JSobEVM0NygrLBU2JzkqLwJbPRJDRjg8Li8SZEdNLhFv/EABsBAQACAwEBAAAAAAAAAAAAAAACBAEDBQYH/8QAOBEAAQMCAgcIAQIEBwAAAAAAAQACEQMhEjEEBSJBUWFxE4GRobHB0fAyFHI0UmKyBhUjM0Ki8f/aAAwDAQACEQMRAD8A3HExMTBFMTExMEUxMTEwRTExxmnVFLOwVRzLGwHxOEao46mqmMWUUxqLEhqmS6wrz5Hm9rdPxwRPU0yopZ2CqOZYgAfE4T6/2kUSsY6ftKyT7lMjOL+God38cUm4KEmmTOK01JLDTFfs4Qx5ALe7G/j8sM1fNTZZSSTCNY4okvpRQL25AeZO2CJdGZ53U/qaOGjQ8mqJNTW/cS+/rbHT/wDyWYy/95zWUXFitPGqD5m5+N8L1DV8QZjF9KgenpYmGqGNrlmHS50nn4m3ww+cG1VW9Mv09ESoHvBGBuOhIHInwwRLeY+z6gijMlbV1LItrvNVOFG+1yTbmcBv0HwuOc9OfM1F/wCuNPzXLYqmMwzxrJG1rqw2NjcfiMY9U8MUa8SR0v0aLsGptXZ6e7ezb28dsETVl3s6ySpjEtPGroSQHjka22x3BxKb2fUBZkpayojdfeWKrYlfUA7dOeCnF2awZPlztBGifZhjUWBduW3gN2PkMZlwXlcuWZrRPUOxeuhcyXHJnN7eZuFJ9cEWhHhLMYv+7ZtKbDZaiNXHzFj8cef0rnVN+vo4axRzemk0ty+49t/S+LPtaz1qPLZZI2KyMQiEcwSeY9Bc4VRXZ/QUq1UjwVcKxCSRXuJEFgSCbC+m/MX5YImqg9pNEzCOoMlHL9ypRk38NR7v44cI5AwupBB5EG4wvZJV02bUUc8kCski7pIA1jyIvbx64EScAvTEyZVVPSk7mF7vCx/dO6+o+WCJ8xMINHxzLTOIc3p/oxOy1CHVA3q3NSfP8MPMEyuoZGDKdwQbg/HBF1xMTEwRTExMTBFMTExMEUxMTEwRTExMTBFMLfFXF8FDpQhpaiT9VBHu7n06DzOB3FXFzrMKCgUTVr8+qQL9+Qjlbw9PEY9ZJwwmWxS1Th6ysKs0kp3kc8yiX91fIeWCKjS8JVOYMJ83fuXulFESI18O0PN28enw2x19ovFMmUU8L01LG0RcI2+lUHQAKOoBsengcFuB+L4cyg7WMaJFOmWMnvIf+h6HF7irI0rqSalk5SLYH7rDdWHowBwRZvxBnVLn9AfozGOtpz20cbGzhl5hfvAjqOoB6YIzV7Z3w/IU/XhNMiDn2kdiRb+8LEDzGFbhDhiHMYZKWa9NmVC2jto+6zKCQrHlqta2rnaxvuMMPsp4RzLLqupWfQ1PJuX131tzDBed9yDe3xwRfeDs9hrsmjpRWijmijWN2DBWUJYXFyLhlGAvsufRnU8dNPLWU/YkSTPuAwIIN/UMo8bnww28X8M5Ekhnrlhidjdu+VLnmToU3JPMkC5xWyrjakiQQ5TltRMl/wBjAVS/iXP5nBFpmM5zHI6k8RQVaxMacU+lpNrA9/bnfqOnXFsZzncv6vLoIR07ae5+SA4+LHxCeb5enkFlb8bjBEK404Sqs1zGOKYPDQQoSHVl1O5HTnY3IG45BvEYWfaBwHLl8cFbBU1NUYJlskneKrzJBHS6KD64e2j4hHJ8vfyKyL+NziNnOdxfrMugmHXsZwD8nAwRK/tPnGZVuV5cpOiUCeS3PS17W8DpWT5jAXOeH5afMUymSvqloqpR2ZJ1XJP6tr7HcWuPFdsPR9odPG4avoKmlddhJJAWA8bOBy9MWM4y7L87EMsFWplgbXG8TKWU3B7yncC4B3AwRGGlpMnoVDN2cEK2FzdmPh5sT4YyHPOLczqZ6StVmpaaWoEVNEDu6kjU7Dk17gX5eHiXLPvZvUZjVSSZhVXgC2p0iBGknqQbi46ne/kNsKee5VWw5nldFV1C1MaS64GtZwLrcP420Cx364It0qaZJUKSqrqwsysAQfUHbCHNwzU0F6jJpdcR3ajkbVG2+/ZNzQ7Ha9r/ACx09qfFjwItDR3atqe6gX3kU7F/LrY9LE9MA/YXVLT5XNPNJpiSRmJYmygAXtflc9BzJwRPPCvFsFcGVbxzp+tgfZ0PmOo8xhjxn1RltNnMEeYUTvT1Iv2U4UqwKm2lx9tb+uCXCHFLyu1FWqIa6IXZfsyr0kjPVT1HTBE34mJiYIpiYmJgimJiYmCKYSuNeI5ldcvoAHrJhz+zAh2Mj+FuYHXwPLBHjfiT6DACidpUSnRTxDm7n+gvc4D5Xlxyigqa2ZTUVbKZqhgd2a19IJ5Iv5Am3TBEa4X4XjoYWSI65n70kz7tI/3mPO1+mMrpuPc7pq6SjmhjqJASwjtpJXp2ZvuLchucGsi9tSFEavpZIFb3ZkBaNvHpceg1YJ8VUNHncKy0VTH9Lh70DqwDA89DDnY/gfjcizXNuMhS1619NTzUk7G1VTSD6uUdSp6E+nPcdb79w3nkNdTpUwG6OOvNT1U+YO2F3hF2zGitmlGBIhMb9qgs+nbWL7jz6XuRtgbLn0k5/R+RxokcfdkqtP1UX92Mfaf/AH5giJ5/mOW5bUtVMmqsmXSEiGqV7dAo5A2FybDYYpLDnGZbuwyymPJVs07DzPJPz8sHOFuDKeivJ3pqh95J5d3Y+X3R5DDPgiU8l9n1BTHX2PbS8zLMdbk+JLYaVQAWAsByAx7wOzugaohaFZnhLWu6W1AXBIF+VxcX88ZFzdF7os0hmMixSK5ibTJY+6edjirRcS0k0phiqYnlF7orgnbn628sJXs/poqdM2juyxJMQTe7Adnub9T1wt5FE6JlbzxJHSLUXhmQDtXLM2jtRfZWvva/TF0aIwlwk2iMr7M+2QUcS1mt4lpIZRDLUxJKbWRnAO/L0v54JTzqil3YKqi5JOwA64xbPoJH/SxgiSSm7f8A7RNIB2qFCO0EYv3gttr264LcZ8QU8yxUH0jsaf6OJZHY2aQafq4h5k2LelsP0YOENPXfFgchcZxB8rwxLTaOqiqIlkjZZInGxG4YcsL2c+z2gqDr7LsZeYlgOhwfEFccvZNWRyZZTqjhmjXTIB9lrk2PnYjDlirVZgqOZwKkFnjLm+XbgjM6YcwbLUKPyf8APyxayqsy3NZ4qpLrV03JJAUkj2OzJ1XvHcXHnh5wrcU8FQVhEoLQVK7x1EWzg+f3h5Hz5Y1ogGR8Iy0r1+ZVzrLUuJNDLeyRhTa1xsbAC3QDrjMOBIps0ggyiIMlOkhmq5PEXuq/hYDx36Y1Ki4llpm+gZ0q2kBSOqA+qmBFtL/dYj54JfoWHJ8tqmokN1jklBY6iSFJW55kLtbyGCLjnfHeW5VopLsWRQohhXUVA5X3AHpe+OK11Dn0N6aUx1EB1RvbTLC3Q26qeRsSDywu+zqipKLLWziqvNLLdpJNOtlBa2kDpvuT89hjvn1HfNMszHLY7pOdM8kY7jKbW1W66ddyfuqOmCJr4P4peV2oqxRFXQjvKPdlXpLGeqnmR0w34VOOeFzVok1ORHWU5108nLcfYY/dbkcWeC+JBXQamXs54zoni6xuOY9DzGCJixMTEwRTHGomVFZ2NlUEk+AG5x2wge0GdquanyiIkdv9ZUsDusKEXGx+2bD0264IvHB0LV9TJnE6nQLx0KEHuxg7yafvOb7+HlbBjgviuLM4JHCadMjxvGdza503H95bXHjcYH8Vcd0+WPHSrBLKRGO7EuyLyUHpcgcvD1GMzy3jVKPNJK+OkqIaSoFqlGTkxJOtenPe1+reWCLtXQ1eX1M+R06K0VY4NK0tiIla+q2oEE2FvIi/M4d8q9j1BAYJCZTJFYu2sgSEb7joL9Bblvi9m2VUeeJS1EFQD2EocPH71uZQg7qSQDvytjhxjmMtdUjKKNyosGrZh+zjP2Afvt+HzsRV8xrps5mekpHaKhibTUVC85SOccZ8PE/05uWXRUlFGtPEY4UQbJcD4m+5J8TucWcnyuKlhSCBAkaCygfmfEk7k+OEb2icRPQvFKtyjTlZUBsWXsh16EHcYnTpuqODG5lE9fpaD+2j/wAwxP0tB/bR/wCYYH5LJBVwpPBNI6ONjrO3iCOhHIjHypkhR0QyudRsSJNl828N9vU4gTGf3d6oiP6Wg/to/wDMMT9LQf20f+YY8Jl6EXEkhHiHOKWYmKEXMkhNwNIk33O5t4AXY+QOMEgCSiqZVl9HAaq06uKly8isy23FiBbpbAfL+EaKJ4ia2SSGB9cMLyqUja9wfE2PK+G6KijYXWV2HiJL44V8UUSlmke4Fwvad5vAAHqTsMbu3qNnazz7hHkPJYgFLOZcJUUskrLWSRRTtqnhjlUJIb3JPUX6254YJ6ahaPs/qLaNAPcJAtYWJ8BixRwRSqGSVzcA2Em48jj3UUsUYu8rr6yW5Yw6s9wEmY+/CQFT4chpaOnSCKZCqC2olbt5m1rnBP8AS0H9tH/mGKWXrFMgZZJASN1MneXxBHlixLRRqLtK6jxMlsQxF5xTM36ysxC6/paD+2j/AMwxP0tB/bR/5hgfl5ilvpkkBDEWMm5tyIHgRuPLFfiOrho4TLI8hPJEDm7nwH9T0xGREqTWuc4NaJJyG9X8ypKWuienl0TIw7y3BI8CLbgg8iMJeX10uUyrQVzmailOinqH+xf9lKfTYH+nIzwJXvOgkkN2PafAdpsvoBthizrKYauF4J0DxuLEH8CPAg7g4AyJWatN1N7mOzBIPUGFl+acF5pQiWPKZVkpJr/US6T2ermV17W+PqDgnw6tNw5Q6KyqLSOdfZg338I08PFupxd4MzGWjnOUVjlmVS1JMf2sY+yTy1p+NvmNrODaTLVqc0rHarmUs6GbcA37igHmeQv8rYyoIbwl7RK7Mc3SGOIRUoVi8bDvabXDs1r6rlbAbb9eeGHjCnOXVaZtAPq3IirkHIoTZZbeKG2/h5Xwg8F8YLSRySRI1bmla5dlQEhASdIYgeJJ0jlcXtjWOFKStlo3TNuzZ5dV0UCyow9xrbG1yNr+p54ImSGZXUOpurAEEdQeWOuET2e1TU8k2UzHv0x1QMftwN7nqV3U/DD3gi5TyhFZ2NlUEk+AAucIXA06MtZnVQdCzk6C32II76f8xu1vTF/2pVjLRfR4zaWrkSnS3PvsAx532XVg4+QQNR/QnS8PZiMqNtgLdOXjgiWD7Xsn5/STf/Bl/wD0x4l9qmTzDsmmMgfu6DBKdV9rWK4Rczy98gMiS00dXRSBjBK0al43I2DEjle23I9Lb4dfZNwnTJl9PO8UMk0n12sqpKkm6gG22kW9DfBFZzWGkyOlmkpIbS1DhY4wd3kbZVF+SjdrdBfxwW4E4c+hU9pDrqJj2lRIebOenovID/rgDGwr84eVz/2XLQQL+6ZmG5PTuL8jbBbjXir6IEjisZXGq53Cre2rzJ3t6HEXvDBidkoPe1jcTsk34xv26fq0/wAc/wDKGNhQ3APljH/bmPq0/wDyD/yhi7oH8VT6+ymkf2f8ZSZdN1eFyO0j/wDUv94D58vDG1RwGqWSakEXZSKChv75tc8h3TqNjfqOXXH5pIw5ezrjmTLpdL3enc/WJ90/eXz8R1x0tc6mp6azFhk5kcY39R5i3XIcWmQv0ZRRBEVVQIAB3Rbu+W2BWZ5c7ShokRbodbt1NxYWG5Ngd/PrywVoaxJo1liYOjgMrDkQcWcecr0KdemadQS05jz3X8CjXFpkIbklN2cSgoEbcuAQbnxuOd7DHDOqFpChjRS4Ni7fZFj8SL22H4YM45SyhVLMbAAkk9AOZxl9Bj6ZpOGyREcvvBA4gyENyWiaPtC6KHLe8p94dPMW32P44+55RmRF0IGcMLG4GkX7xv4WFrDnglG4YBgbgi4PiDjpjAoUxS7GNmIjlER4LOIzKC5PQNHJI0iJqNtLr1FtxY7jcX63v5YsZ5TGSFlVA727guBZuhBPK3PBLFLM8wjgjaWVtKKLk/0HiT4YU6DKdMUmiGgRHL7x70kudzS9USCiLVFSEKqncK89V7aFB3uQefkeWMp4jzySrlMkm3RV6KPAeXieuLHFnEclbLqN1RdkTwHj6nqfhgFimxlOkwUqQhoy+le41Rqv9OO2rf7h/wCo4cJ4nuFpnXvZd+oT/wAz+fD3hE9l36hP/M/nw9HHQp/iF4/T/wCKq/vd/cUr8fcOGspw0R01MDdrTv4OPs/utyPw8ML9bTpxBlS6pGgeN/rlALFHj2dSoO9xcj1U26YNcIcV/SZJIJLCRCxVhsHXVbl0I2v48/HAmJxQ5wGQj6Lma3BHuiZRzHTvrv5m+DXhwlqpse14xNNkD4Y414fyyPs6ZpC32nMLl2PmSB8hYYJy+3DLgLiOqYXtcRrb5lxjt7V+GaYZfJPEkUMsDCZGCqt2BuV8y3h42ws8P5fUcQyx1FXGIaCG2mJRbtXA3N7Da97noNh1OJKSauOKlY/oedQd5YrLLb7cEtrna99JIb4nGgRSBgGBuCLg+RxSzHKY5aZ6UqBG0ZTSBsBaw28sL3str2koVikN5aZ2p5PH6s2X/Tp/HBFUzYfSc8pIeaUkLzsNram7ieYIuT8MVOK/aLPBVyU1FRtVdgoaoYX7g52Fgd7eOL/Bf1uY5nU7G0iQKR4Rrc/HUxB9MZtS8XS5e2ZtUUVQstW7FJCtgoAYIGv4aibg4ImbNva9QTL2b0ss1M4Amcr3VLDdd+ZH/wAYOZLQ0+TZbU1MErSQspnh1G4AZR2aj1JAv1vjOY+J6FOHvoMEgepeweMowJZ3uSCRY2FhcHoMPHHFBooctysftZYIWt92MAufSwJwRFeDsk7LKlhkcLNVKzOzEAtJKCfid+XljNs2gqo5lhnBvGgAD72F25EfZ328L/DGtcdZTJPShYPfjdXVQbE6QRYeYBuPTGf5ZxNVpUPJ2PaTlBGbhrjSSPdH2jbfFPSQ1xh0jnEg8ojMGI/9VSvTY/FjmA2ZFxdwAsMyCQQD6StE4Qq6qeNpalRGpsIk02NhzY3336cthfrhK9scWpUH/jn/AJQxo2Rdv2CGpI7U7sFFgtzcL8BYHzvhH9psWrSP/FP/AC1x09BMVmdd6stBwiT45rG5KLyxWkovLDZJRYrSUePUtrKSv+zjjN8ufspSWpnPeHVCftL5eI+PPG+x1aNH2qHWhXUCu9xa+3jjKeGuHYooS1RErSyA3VwDpU8l8iRubenTFzJc3/RkojJLUUjW33MDH/0H8MeV0nTdH0nSC2kL8dz+Mc84/n3bUB2sVGzC0ShzKOYdxt7X0nZh/Cd/jyxWzmoDR6VII7QK9jytuVPrYbeBwMqYbfWIwaO+pJEN+zvvv/d35jpsfHFV5GaR2VB9Z2Zk3supdtSn7V0A/Acxjg19Nw0alOtDHQYM2dYgOb35i5bvVprLgtuj2TTARWYgAOVW58TsPXewx3rczjh99u9a+lRdvkN/icLbOyul0uElcoOYJIYKzeAAbr4HyxapafVd3YCMG8kjbdoRuQP7u3Pw2HjjGj6eX0qVOjtvIEncBAlx77AWLiCBxJ1MAkmwTBPWokZlc6EC6iW2sPPGLcZ8VPWSWF1hU9wf1bzPh0xd454parfsoiRCp2/vEfaP9B054VEp8X6tTFYZL1WptVCiBpFcbX/Efy8z/Vy3fuyr49dmcXUpsWEpsaYXoHVgFo/sxFoU/wDM/nwb4rqqqKMS0qh9J+sjIJJU9Rbe4Ph0v4YF+zxbRKPJ/wCfDJnAmML/AEcgSgXTULgkb2Pry8r4ugbMcl831htaRV5ud/cVh9EKh6gpCrB3VtkBub81u3IDe5xoHFuR6so7KNlaoo1R0ZbEpJEA3w26eBws5vxNVNPDIYeznTVGDY3OuwtpPUXJFsPXAmUSxU8n0kHXM5ZlY3NiAve8za59cVtGwtdDZPURF5ytcn04QubQYxuEskyJJIgSHGbbiTe1o5Qg3Fq0eY5VT1tW7pAvZzPouTvYMhA/vHTfpgbH7Voggiy3LqiZEWy6UKooHjYGw88FvZWgFNU0EoDClqZItLC4K6tS7Hp1wg8e+0KGsl+gQzfR6BTaaVEJaQDmqKv2Ty3sDzO3O4rSaPZv7Qa7M6xlanRKVFOtludLfZBcmxvvsB54N5CPo2d11PySpjjqUFvtDuPv6i9vPC7wxx3DEsVJleWVLRBgC7LbYkBnNr6jbfcjDJxavZ5pldQB77SQN/EuofLQfngi++yQh6SacC3bVc728jIbfhh1liVhZgCPAi+Ev2Mr/wAJpz46j82OHjBEt1vBGXyusr0kWtWDBlGk3BuLlbX3HI4EZ2O1z2gj6QwTS28zZAf9Rw94Q4BfiKQ/doVA+Mhv+QwRdeOMxqaWWKphuYgpWReane/e8Ljk3l8MKPD/ABVOhmSnhVp6ia92JOm6AgaRubEk3JA8sM/F3E81FUMjoJIZIyUFgCNrGx5GxtsfHCvw5xDLFEKSljHbySMDKQCd7HujrYdTsLcsU6jwHkB8com/AdR1AuQtDngMqQ8jaaIiYJBMD90cwIkctggBCqGN2sLm1rm25wi8dKCwv/aGw6k6FsB54fUBsL87YQuOQ+odmoLa25kC31a73xbNbsR2gGS2VX4Gl0TCWZ6KNYzqNpTuCDsv923IjxPytbHjKKJWtM+wBui/eI+0T92/IdeePq0AvqmbtD0X7I9erfHbyxKqvAsLgX2H/QY5btZ6SWuaX/ln8DgCLQFzDpVQTiOfkiVTW4E1tSGBVtwRYjA+TMQwuD1IPkRzGKEtQTjnFxCoVdJKMcO5sKY9kQAjHuyKxGi/37e8vlzB3B3ONHiA0jTbTYabcrdLYxrDHwpxB2BEUh+pJ2J/Zn737p6jpz8cVNcGtpgFQ5tzjfz/AHW711tV62l/ZVt+R9j8rQJiultRAWx1Emwt1xn+f5qKm0almC9WvvbkVHIIPmTzsMfeLOIO3bsoz9Sp3I/aEfa81HTx5+GF+J7EN4G9vhb8QbYxqdrtHBLsnRbhz6+gtK6A15QoacwOaHNabngeI44c792SuJTYsJTYv00QZQy7g/7+YxbSmx6bCveu0md6GpTY7pTYJJTY7pTYzhVZ2kJi4HWygfv/AM4w2ODY22NtsLPCS2JH7/8AOMNBxabkF5DSDNZ5/qPqVj3E/E07BKepjUSxThg692+lW+yb8xyIPww0cEZrU1kz1MoKwhNKKPduSDt942G7flhY4m4hlZZKOpjBlVgFlAAbZri48CvUfLBzhLiieqmhhRRHFHGpk2BLd2wueSgnkBubc8VGPGMAvJvlF54Hpnu3HrSDwWMl5O24RF5gWP7RJnI5mVa4dXss7zGL7MscMwHnbQf5cGcv4Ly+Fi8dJEHJJLFbm5Nzub/LAX3eI/36Df8Ahk/92HzFxb1zjjCiygAeAFsJPtXAWGlmJsIqyBifAawD+F8PWET20D/hUp+68Z+TDBF79jLf8Jpx4ah8mOHjCL7IgEpJoAb9jVTpf0c2w7Sy6Rc4i97WNLnGALnosgSYXTCHCbcRSD71CpH8Mhv+Yw9g4Rc8PZZ7QSdJoJor+Ys4H+k4ksL3xjxDCjS0tVDdDHqjfTqF7HmOYseovhX4c4ljpafRDCr1buwLaeVz3QTzbYjYYf8AiLL6SqPYTle1C3TvaWsfA9RccsJvBa0lNFNU1KjtI5yFJ3PuqAFXx574rP7TFMgDjwG/v4d/fF3a9k+HADEL8BDsXtB5+OoR3sL87b4z/wBoFWIu8b21nkL/ALNegw45DmX0iCOfTo1i+km9tyOfwwke0dGOlVC3Mp7zC4X6tbm3U+AxnSnDsHHdCmyh+pIpNMYt8TzykeoHNZ1nPECkJ2Z1XbvDccuQPqfywLlzZmBVrSqffIFrenpjzV0BNnNyG1FAQNRC/aNthfoPDFurjmlYmbT9XErtoVLIje6UCkatVx6b87Yo02Ui0Qfs9xzG4i65tfV7qTsDs93PfmDnebHeJ3BDqSvI1XuxJFrnnba5PpgglWftWHo2CeX8MkPKrKvbxIkioe8jgg3iYeJA2I5E4dsty2GNSqxoY2syI6glb7kajva+4vy3GNGkOo5j7b71VCtRpm+X3qs5+lL/ALtj2k6nkRjSHhiP7GM2/wDDW4/occZooyjfUx+62+hfA9MUXVaYCqGlT4nwSHQ0Ukj6IY2kvfuoLlevpp/2Mdaehkk16EdtALPb7IHMnw9OfljZspCrBFpVV+rS9gBfujwxaEg35b8/P1x6D/KG73dVe/Q097j6fKx3KpZYU7fsyYCdJfaxax+1zLefI8r3w3oBtdWViAVVlIY35WXmb/h1thzDC1rC3hYW+XLH0ydevjizS0Ls24cU/fvTiuzoOnVNFpCkDiaMp3dI3TcAzGUwlJAL2s2u9tAF3v1GkeHjy88dkZT7t32udAJ0jxbw9OflhnDi97C/K9t/S+Pok8Mbf06snWzzuCo8MkFzpII7+4/eGGQ4B5R+vf8Aj/mXF/OK3sIJZtOrs0ZtN7XsL2v0xA2WovxbR3391mfEXEsdRBJDUQqtUjBUfTfk+9jzU2B2O3ng5wVxBCewpaaH9mDK9tIuE3NuZN7bnAvjSSkqqdKunUds8qoejA2JIZfGw54buG8tpKQiGIr2rLcgtqcgc/QA4rM7QvmQRx4i8d/E/REdp2TdoEYjfi2GxymczkQPAP73Ef7lBv8AxSf+3D5hD4cPa53mMvSKOGEHztqP82Hsm2LKkvuEX20n/hUo+88a/Nxh1il1AEdcJXtZIaClhNyJayBSB4awT+F8RY9r2hzTINx0WSIML7wc3Z5jmlPtvIk6gcrOlvzXf1xU9rWb1lNRuYo1KMNLSayGQsQFKrbfc+Pzx3zc/Rs8pJrWSrheBjtbUvfTzJNiPjhqzPLEqNCyKGVWD2O41Dlt5HfFPT6bn09kEmRsgwHCQCDyiZ98jJhE3Sl7L86qa+naSpeMFW0GJFZSun79yTc7G22xGPXtZBijpK4f/a1Ubtb7jHSw9CDb44cUoEVi6gKxtqIHvW8fHFfijKhV0k9Mf2sbKD4G3dPwax+GMaBQdRa5rxeeJNtwvcQLQet5R5nJCeMOFxWqjo4WRPdY8mU72NvmD6+OEHJ+EGesaknk0lFLG2+oE37t9r2bmR0w0cMzTV+UxqkjRVUB0NZipEkR0lWt0I2sfEHCdmhzBZRJOsiMbQ9qyKt1JvpLgWNwSL+AxsrNZiktJnOMjG48+scJVUspl7mOYTjb3Et2gDfM4YExMxK1nIa6Bw8FP7kGlLjly6Hra1r+N8LHHfvD/EP/AC1x84e7PLDFTOyvPUSDWFO0a2svzPzufDH3jr3h/in/AJa4hpbidEfizi44clf1S8u0mnizm44WmPAhJFbT3KOoF0PLxUixGKWT0KfSFhsbxyFyfGPSNK/ArywYwOWnf6SXC2Xo9xsQAFIHPxx5yhWIBk7vvqT1Xo9bav8A1NMNZZxNjuBwFsnyHcE05dSFWkd7F5Wubcgo2VR6Dr44t26H4H/fXFWkrVkFrhZBzUncHyHVT44t8xviLiSbr5dUa5jyyoIcMwd3JeNHjfyN8fJx3Wv91tx6Hnj3q6H8uePEjWVuZGk9DtsfwxB/4larI/l8v1MX+Gn8oxZ7XAmgl+qj/wANf5RhW40zg92FFqopRIGSZEfQF+050X1qNhbxIx9JqAMElX2vL34QmfMeKKaCZIJZNLva2xsL8rnkLnbBjtcYWudSxVa1DzdvIlwGkViCLW22Gn0AHXxxfzD2h1bjs4ykeoWLIjFgPIm9j52xRbpjBOIHPKPfJdN2rqsNLIiLmRn0MHyWxtOALk2HiTbH3tsZxwxlprLT1U8kwQgLG2q1xuCbgA/Ac+Zw9GXFykC8YiI+8lz65FN2AOkjOxjzgnwHKc0SyM3mf+P+YYs59mUEIRJ/cmJjJPLdT73gDyv54pcOm7t/H/MMB+InjzFpqJWCTwPePUdpO73h8NRHlscc2sS2YzvHMroh5FIEZxbmYmPJKefcIGKrip4JNfai6g7aRfmSNjsGsf8Arh64Q4XFCJJpXDSMNyOSqN+u5PifIYzrLhmHbFoFkkaMGEOiqwWx2UMe6LDr4Nhq4llnosqdJJGkq6pxGt3udcllCrfawUdPM4qUWMxSGER4Cd2efTxWQymHNYGEYRPIF20R1gwYmIIBVz2RoZIKitbnV1Msg/d1EL8LC3wxz9qWdVNDTiSmeMlm0dm6szNq+5Yg3Fibb7DDfw5la0lLBTLyijVL+JA3PxNz8cdXoEZg7gMwvpJHu38PDENPoOrNa1gvPEgRvBi5BFo79ytMMJJ9k2b1lTRRmWNQirpWTWWZypIYsthY3HifhizxYe1zXK6e/uGSdv4V0j5lj8sNGV5YkGtY1CozF7DYaibsbeZucKnD5NTnddUc0po46ZDfr777fvEj4YzoFNzKW1IudkmQ0SYA5REe2QPN7Kz7UqJmou3jF5aWRKhLc+4wLDYX93Vt6YaMqr1qIY5kN1kQMCPMXx3niDqyMLqwII8QdjhI9m8pp3qcqcm9K+qEnm0Lm6+uk3X5YvKCfMTExMEWeKf0dnDISVpsyF1PRJ1G/pqX8bYD8VnMQHpZQ00Z91tAswBuDdRsfHDrxhkqZjSNHG47RG1wyKQdEqHbf5qfInC/QcaVclGJI4UaaAmKsiYNqRxycAHdWsT/APBxqrNxNzI6fG9aqoMYg4iL2+IM+CXuG6YvJJXVMlxAVYg++7AWjFulrBfVfXBjP8xFRDFNa2tzceDdmoIv6g4Ss0r2qJJJQgXWQWVC1i3W9+RNrjzv440wQ09Jlqyhu1FgQVIAlZzsLMCOZtuLgDyxTDRVpvYDDSDJ5znfpdbdFrMoVhpVIDszLs42p2hfLDutlHNJlsfMOh4ZjnjWWMd11DBojpO46o10PwK4C1nDcsfJlP7wKN+N1PwbHIq6prs/HaHL4K9fR11o1T8tk88vEe8IKy352PqMeoZGT3SR5HcfI491MDRm0isn7wsD6HkccyMc446Zwmx4H4V+pSo6VTh4D284I91aGZSddB9FYH8Djt+lyVOqNjsfdZT08yMDsev+h/LGHPJBBXKf/hrVtVw/0y39rnDyJI8kyUB+qj/w1/IYsBz44q5b+oi/w1/lGLOPrIXyiZX0tfnvj4NtwBf0GJjzG2rZAWtz0i4HqeQ+eBIaJKy1hJhovyC9lziFsdIKKST3bD0Gs/gQg+LHBAZKERpJbKqgsS51tYb8to1+RxVfplMZXV6lq6s78tnr8BVsurxDFJMdwgkYeZ1gAX8zYYTOI4t48xgkCmZtwPejkA0vYdRYH5+eHOlkp6zL5ZC3ZBkZXZmv2ek3HgLbBrAC98Zbl9Y0MiOyB1RtSq+oLcHu+puQx9ADjgaVUDjiP4m88DIg2vkuq1rA1rql6TQDN8xGEWvtct08Ey8LyZjZaaFWiUklm0Du3NySWG5AwagU5hm43LU+Wrp1H7c7Dc+B0r5c7+OONdxvWLS6jDGs1QRHRxqG1O55uQTsije/U4a+DshTLqNYSwLbvNIdtcjbsx+Ow8gMbaLYbMkzx+FupyZeXE4r3t5QI8Ew4mPIN9xj1jatio5zmC00Es7myxoWPwGF32WZe8VAskv62pd6h/WQ3H+nTil7RGNVNS5Un7d+1qPKGMgkGxB77bfA4eYkCgKBYAWA8AMEXTCD7QqZ6aanzeEXNPdKlR9qFuZ8yp3/APjD9jjUQq6sjC6sCCPEHngi8Q1KvGJUOtWXUpX7QIuLeuMxjzCvz1nSBjQ0KOUkYEds5GxW32PT88FeDahsvqnyidj2ZvJQuftRk3MV/vIb7eHlbH3PKd8sq5K+nilliqAFmp4luWl+zIPu35E23OCJp4b4egoIRBTqQt7ksxJYnmST1OFfjLLpaOo/S9GhYgaayFf2sY+2B99fxH4qmccb5jPHVM0keWpTDvxkFqhtVtAUEWsxIXV0O+NB4CNRHl9P9Pl1TONy5F+8bqpJ5taw88EVPNeL6F6ZCUaWGpQlSq7N0Kk9HUjccxjL6mtO0CO/Y6yyI4FzfbV+8ANwOe554d+IckbLHkqIIfpGXytqqqWwPZnrLGPzH+w05FQZdU0uqmjieCYbkDc+Rv3gQeh3BxXrUnVDBIjvnpM5KBD3EsfBpuEEXnqDNiOnEXRHhyNEpYVjcSIEFnHI/wCzgm6AixAI88ZxUZXV5U5lpbzUzG7od9PmRz/jX4jrhmyDi+mqrKG7OTrG+x/hPJh6Y2tcPxNvu5b3UsLcTLt9OvD05ojLk8RBCgpfmBbSfVDdD8sAa7g9DuiAecZ0f6DdPlbDhiYy9jXjC8SOd1inUfTdiYSDyMLLa7hyWPkwPk/cPwJuh+eBVVTvEbSoyXvbULA7dDyPwONlK32O4xQlyiMghQUB5hfdPqhup+WOZX1RQf8AhsnxHgfldbR9d6RTIx7Q52PiPgpGyd9UUKpdz2aXCi9u6Odth8bYvwUkjmwt5gd8/G3dHxbDRDk8YAVhqA5KbBB6ILL+GCCIALAWA5AY9A/THnKy8rS1ZSYNra8h8+aW6bh0ndwB+8dX+hbJ89WC0WUxi2oF7ctXIeiiyD4DBHExVc4uMkyr7GNYIaIHK3ovKqBsNsUc7jRqeVZHCIUIZjyG3PA/PuLKekBDvrk6Rpu3x6KPM2wqxUVXmzh57wUqm6qPteYB3P7x28B1xrc8TAufufBbm0pGJ1m8fgb/ALJCRIKxl1QGRuz1AuqAb25P62OynyvjSsp4xoIqZrI8cVOl2LpsPAX6ux5DmTgpndDl1LSXqUjWCIXBYb3Ph9osx8NzhX4Z4ebMJI6qogFPRRtqpaSwGo9JZR1PgD+XPVSpOpmAbd8+MrQA5pDKcCm0WF56kzc93KAiPBmVS1c5zesUq7rppIT+xiPUj77Dfywu8Q5tT5jmE9LVzmno6NCWiLFHme27dDpUWsOvPrhn9o/FlVl5pfo9OsomkKEuwUard1L8gW3IJ22OE3i3iDLa7/s+bUs1DVKO7IUuVB5HUtyUvfmCOeLCmvvAXHMlHSLEaLMKhC7GJxESBGT3AGPPbw2xrr16pAZ5h2SqmtwxHcAFzcjbbC77O3rDTkVU0VQgI+jzxn9altmbwPTxuDz5kZxdKczq0yqFvqoyJK5x0UbpF6s1iR4YIrHs6pmqGnzWYd+qNoQfsQL7g9W94/Dzw9Y5RRBFCqLKoAAHQDHXBFMTExMES7xpw4K6DSrdnPGddPL1RxyPoeRGK/AvE5q42inHZ1cB0VEfLvD7aj7rcxhqwocXcMPI61tGwiroh3SfdmXrFIOqnkDzGCJf9o2RJLmFNVVEaJR06NJPOebaSNMZ9WIsOu+OWTUE+d1KV1UrRUELXpYDcGQjlI3l4fhtclsyrMqfNKaSGWKzDuVFPJ70beB8uoYeRGF6GpqMkWSOUSVVELCl0qWlRjsITYW0+DH03uBgi0VyADewFt78sIOacKT0crVmUFQW701Ix+rl80+61vgfzQOLOIqmsiq/p8zUXYr9XQp3Wk1W0lnPvL42+Qw/cIZtLBPSZVp7TRRiSokYnUjHkPA7nTb49LYIjXC/GtPWkxENBULtJTy7OD5feHmPLljnn3AtPUEug7GQ8yoFifEry+IscfM0yfLs3UkOryRMVE0D2kjYbe8PyNxgUrZvl2xAzOmHI7LOo/le3z88Yc0OEFSa9zDLTBVV8rzaj/UyGZByCnV/pfcfAnHxePa2LaemU253V0P9Rg/k3tFoKg9mZewl5GKcdmwPh3tsNQ0uL7MDyOxBxDszucfX1W3tgfyYD5elvJIEXtQT7VM38Min8wuL0ftMo9g6zIT4pq/kJwz1GT08nvwRt6op/pjPzwQ9TUSSrGtJBfSg094qv2tPIajc79LbYie0GRnuU29g7MFvfPsjtR7R6RfdWZ/RAB82IwOl9qCfZpm/ikUfkGwPbgR6WZJbfTIQ1pEYd7SdidAspK89vDlvjQ4Mnp09yCJfRFH9MB2h3x3LDjo7cgT3x7JBbj+sl2gplF+Vg7n8gMff0fm9Z+scwoedzoH+VLsficaQSqC50qBzOwAwq517RqGnbs1kNRNyEVOO0Ynw22/HEuzO9x9PRR7Zo/BgHn628l8yHgOCnIeT66Qb3YWUHxC/1JJx24o40gpCIUDVFU3uU8W7H97oq+ZwFYZxmOxtllMedrNOw9eSX8t8MXDXCtJQArAt5W3eR21Sv5ljvbyFhibWhogBanvc8y4ygeT8Jz1cq1mbEO6m8NKu8UPgT95/PpizmvEcNdDV0mXVaCrRCFt4ixspOxB93UL2v5YD8R+0Cqpcymg+iiWkhjR5Cl+00sATJzsVBupW3S98KHEGR0cbDM6Il6GVgZGhNpKRzykTqFv7yHbmNtiMqK6cMZk1RRDLMxYmOoUilqG5pKh3ie+4dW5X/rho9lOVCaOqkrkaasEpgmaZQRpQDSqbW02O/W/wwO4S4HnmapjrHjqaGoCzRzo2ljJtaRAPdYrcN47c98PnEefxZfCiIpkmfuwQqbvI3L1t4scEVXjLiH6FFHS0iK1XN9XTxDkvTWR0ReeL/BvDi0MGi+uVzrmkPN3PM38PDA/g3hZ4XetrWEtdMO+32Yl6RRjoo8euHDBFMTExMEUxMTEwRTExMTBEocXcLvI61tEwhrohZW+zKvWOQdQeh6HHThHjJKsmCZDT1kf6ynfY7c2T7y+Yw14XOKuEoK4KzXjnT9VOmzofI9R5HBEL9oPCLV8tEQkZSKcNMxA16BuVB+6SNx6YzrIHqszrMwMQaCGaW1TUG4ZYU92Fb2s7Dn4Dn5vNPxZVZcRDm6ao72StiF0bw7RRuh8drfnhuq6eOrpZUikAWdGHaRkH3hbUDyJscEWM8F5oKSneHL0DVddKxiB37CBLqskpPQDW2/jhs4X9o+mnmnzGSPskl7KCREYNOVA1FUubjcbjbfFefgl8ryyWGhjaoq6giJ5gACqtza17qgHQX3IJxVybJIqXOqWlmsEp6IfRw3us5JMjjprLFj48vAYImqkzfKc4JiZI5ZFF+zmj0yAeK3Gr4qccD7MYYzejqqujPhHMxX/KdreRwNjzGOrziGKro5Kerp9ckckciMrR7gFyN7EC1vPpgFn/ABVVzVSZhTystBBUx04CsbTaidbkcitwACfHbBE4rkedxfq8zimHQTQC/wA0tiX4hXa2Xv53lX8N8D/aHx5Nl+YUsajVTtHrnAW5CliuoHmLWvg8nEsj5slGmkwGkE5bqSWIFj4WGCKj/wDULbf8PTz+tb8NsfWyLOpf1mZxxDqIIBf5vfD0cZXwV7Q5pauehrQFYyyJTyhbAshsYz01WsQf/wCYIiw9mUD96uqqqrtue1mYL/lBsBgzwy2VxsYKFqQOPeWFoy23jY6jbxOMxrs6mqcvpIKqZykmYvT1L3sSoa4UkWsCCB6DBvjOClyyRJ6fKWf6MocTqdMa3OnvWN2IPTzwRaPxC8y0s5phecRt2Q8WsbfjjDspqqdWiqGos1qK1DrmkXtAdQ5g/wB3y+GN7oalZY0lX3XUMPQi/wDXCbm+R5u0830fMIlp5eWuO7w7Wsltj43JGCJOzvM2ra7L6ygman+mxSUzuVBZChuVty1bkA+hw15L7LqeklR4JpgmgrPExDJODzDqRaxudrYOZJklJllKillCw6mM0pF7tuzFjyv+WF+q4oq8xJhyhDHFez1soso/wlO7nzt/1wRFeJOK4qHRSUsXbVTC0VNEPdHQtbZEGPnB/Crwu1bWuJ66Ud5/sxL/AGcY6KPHri9wtwlBQhil5Jn/AFs8m8jnzPhfphiwRTExMTBFMTExMEUxMTEwRTExMTBFMTExMEXGaFXUq6hlPMEXB+Bwj1nAstM5myio+jE7tTuNUDei81J8vww/YmCJCj4/emIjzWlelJ2EyXeFj+8N19D88Gcwy7L82iXV2c6jdHR+8vmGU3GGCSMMLMAQeYIuMJ+Y+zWhdjJAJKSX79M7J/pHd/DBFRrPZpHHBOtDI0U869m80zO7dmfeUb7XG18CuJfZJAtE6URnEyqCqdqdEjAg3KsSLnc7WscG1ybOqb9RXRVSC1kqo7N599LH53x6XinM4tqjKXb+9Tyo4+TWIwRCYchmqsyRqqBlibLRE+oCwZidS+ovij7LcnqoMyqI6pWIpoFghkKkBkDErZrWPdI9OXTDGPadTqLzUtbD+/Tv+dsQe1rK+szr+9Gw/pgie8ZXRcGNUvmtNMjRqalZ6Wa3Jyp7yHyKqD64Mn2s5X0mdv3Y3P8ATEPtOgYfUUtbN+5Tv+ZGCIPwTwDK1BV0WZr+tnMiurAm5VRrHgbrfcdcX19m0koWKtzKoqadCLQ6Qga3IOwJLD5YtPxTmcu1PlLp/eqJVUfJbk48nJc6qf8AvFfFSob3Slju3l33uflbBE1VddTUcQ7SSOCNBYXIUADoBhTfj2SqJjymleoPIzyXSFfid29Bb1xcy72bUMbiWVXqpB9upcyf6T3fww3RxhRZQAByAFhgiSKXgNqhhNm1Qat+YhXuwJ6Jzb1OHaGJUUKoCqNgALAfDHXEwRTExMTBFMTExMEUxMTEwRf/2Q=='; // Ensure this is the base64 representation of your image
    doc.addImage(logo, 'JPEG', 45, 15, 20, 20); // Adjust size and position as needed


   
    // Centered header text
    doc.setFontSize(12); // Normal font size for the header
    doc.setFont(undefined, 'normal');
    const pageCenter = doc.internal.pageSize.width / 2;
    doc.text('Republic of the Philippines', pageCenter, 20, null, null, 'center');
    doc.text('Province of Samar', pageCenter, 26, null, null, 'center');
    doc.text('Municipality of Tarangnan', pageCenter, 32, null, null, 'center');
    doc.setFontSize(12); // Slightly larger font size for 'Service Record'
    doc.setFont(undefined, 'bold');
    doc.text('Service Record', pageCenter, 40, null, null, 'center');

    // Adjust startY for the content below the header
    const startY = 55; // This is the Y position where the rest of your document starts

 // Existing script starts here - adjust the starting Y positions as necessary
doc.setFontSize(10); // Reset to default font size for the rest of the document
doc.setFont(undefined, 'normal');
const startX = 10; // X position for labels
const lineLength = 45; // Length for underlines
const nameSpacing = 60; // Space between names
const labelOffset = 3; // Space below the underline for the label
const sectionSpacing = 5; // Space between sections (name to address)

// Retrieve name and address parts from PHP variables, converted to uppercase
let lastName = "<?php echo strtoupper(htmlspecialchars($records[0]['last_name'] ?? 'N/A')); ?>";
let firstName = "<?php echo strtoupper(htmlspecialchars($records[0]['first_name'] ?? 'N/A')); ?>";
let middleName = "<?php echo strtoupper(htmlspecialchars($records[0]['middle_name'] ?? 'N/A')); ?>";
let address = "<?php echo strtoupper(htmlspecialchars($records[0]['address'] ?? 'N/A')); ?>";

// 'Name:' label and details
doc.text('NAME:', startX, startY);
let positions = [startX + 25, startX + 25 + nameSpacing, startX + 25 + 2 * nameSpacing];
[lastName, firstName, middleName].forEach((name, index) => {
    doc.text(name, positions[index], startY);
    doc.line(positions[index], startY + 2, positions[index] + lineLength, startY + 2);
    doc.setFontSize(8);
    doc.text(index === 0 ? '(Last name)' : index === 1 ? '(First name)' : '(Middle name)', positions[index], startY + labelOffset + 2);
    doc.setFontSize(10); // Reset font size for other texts
});


// Adjust Y position for the birthdate section, place it before the address
let birthdateStartY = startY + sectionSpacing + 5;

// Retrieve and format the birthdate part from PHP variables
let birthdate = "<?php echo date('F d, Y', strtotime($records[0]['birthday'] ?? 'N/A')); ?>";


// Calculate the X position for the birthdate and place
let birthdateX = positions[0] + 1; // Align with the last name

// Adjust spacing between address and birthdate
let spaceBetweenAddressAndBirthdate = 10; // Adjust this value as needed
let placeX = startX + 100; // Adjust this value based on the desired spacing

// Adjust left margin for the "Date of Birth" label
let dateOfBirthMarginLeft = 0; // Adjust this value as needed

let addressStartY = birthdateStartY + sectionSpacing + 1; // Adjust Y position for the address section accordingly
doc.setFontSize(10); // Font size for address label and data
doc.text('BIRTH:', startX, addressStartY);
doc.text(birthdate, birthdateX, addressStartY); // Adjust X position for the birthdate
doc.line(birthdateX, addressStartY + 2, placeX - spaceBetweenAddressAndBirthdate, addressStartY + 2); // Underline for birthdate

// Set a smaller font size for the "Date of Birth" label
doc.setFontSize(8); // Change font size for "Date of Birth"
doc.text('(Date)', birthdateX + dateOfBirthMarginLeft, addressStartY + labelOffset + 2); // Adjust left margin for the "Date of Birth" label

// Set back to original font size for address data
doc.setFontSize(10); // Reset font size for address data
doc.text(address, placeX, addressStartY); // Adjust X position for the address

// Set a smaller font size for the "Place" label
doc.setFontSize(8); // Change font size for "Place"
doc.text('(Place)', placeX, addressStartY + labelOffset + 2); // Adjust spacing similar to names

// Add an underline for "Place"
doc.line(placeX, addressStartY + 2, placeX + (lineLength * 2), addressStartY + 2); // Underline for place

// Reset to default font size for any following text
doc.setFontSize(10); // Reset font size for any text that follows

    // Insert the certification sentence below the 'Address' section
    let certificationStartY = addressStartY + 20; // Adjust based on your document. Add more if more space is needed
    let certificationText = "       THIS IS TO CERTIFY that the employee named herein actually rendered services in this office as shown by the service below, each line of which is supported by the appointment and other papers actually issued by this office and approved by the authorities concerned.";
    doc.setFontSize(10); // Reset to default font size for the certification text
    doc.setFont(undefined, 'normal');
    doc.text(certificationText, startX, certificationStartY, { maxWidth: 180, align: "justify" }); // Ensure the text wraps and fits within your page width

    // Ensure the table or any subsequent content starts below the newly added certification sentence
    let newTableStartY = certificationStartY + 20; // Adjust this value based on the length of your certification text

    // Generate the table below the new certification text
    doc.autoTable({
    html: '#serviceRecordTable',
    theme: 'grid',
    startY: newTableStartY,
    pageBreak: 'auto', // 
    margin: { top: 20, right: 20, bottom: 30, left: 20 }, 
    styles: {
        fontSize: 10,
        lineColor: [0, 0, 0],
        lineWidth: 0.1,
        halign: 'center', // Horizontally center all text
        valign: 'middle' // Vertically center all text
    },
    headStyles: {
        fillColor: [255, 255, 255],
        textColor: [0, 0, 0],
        fontStyle: 'bold',
        lineColor: [0, 0, 0],
        lineWidth: 0.1,
        halign: 'center', // Horizontally center header text
        valign: 'middle' // Vertically center header text
    },
    bodyStyles: {
        halign: 'center', // Horizontally center body text
        valign: 'middle' // Vertically center body text
    },
    didDrawPage: function (data) {
        finalY = data.cursor.y; // Capturing the final Y position after the table is drawn
    }
});
    // Add compliance sentence below the table
    let complianceText = "      Issued in compliance with Executive Order No. 54 dated August 1, 1954 and in accordance with Circular No.58 dated August 1, 1954 of the system.";
    doc.setFontSize(10); // Reset to default font size for the compliance text
    doc.setFont(undefined, 'normal');

    // Check if 'finalY' is undefined or not set properly; if so, manually set it. Replace '285' with your estimation if needed.
    if (!finalY || finalY < 10) finalY = 285; // This line ensures there is a 'finalY' value if autoTable didn't provide one

    doc.text(complianceText, startX, finalY + 10, { maxWidth: 180, align: "justify" }); // Position the text just below the table

  // After adding the compliance sentence...
let spaceAfterCompliance = 20; // Space after the compliance text before the certification starts
let certificationY = finalY + 20 + spaceAfterCompliance; // Calculate position after compliance sentence

// Calculate center positions for the two sections
let pageWidth = doc.internal.pageSize.width;
let nameSectionCenter1 = startX + (pageWidth / 4); // Center of the first half of the page
let nameSectionCenter2 = startX + 3 * (pageWidth / 4); // Center of the second half of the page

// 'CERTIFIED CORRECT:' and 'APPROVED BY:' titles, aligned with their respective sections
doc.setFontSize(10); // Font size for the titles
doc.setFont(undefined, 'normal');
doc.text('CERTIFIED CORRECT:', nameSectionCenter1, certificationY, null, null, 'center');
doc.text('APPROVED BY:', nameSectionCenter2, certificationY, null, null, 'center');

// Set up positions for names and their titles, aligned and centered below each section title
let namesY = certificationY + 20; // Y position for names
let titlesY = namesY + 5; // Reduced space between names and their titles

// Draw lines for names to be written on, centered below each section title
doc.line(nameSectionCenter1 - 30, namesY, nameSectionCenter1 + 30, namesY); // Underline for the first name
doc.line(nameSectionCenter2 - 30, namesY, nameSectionCenter2 + 30, namesY); // Underline for the second name

// Write names, centered under each respective underline
doc.setFontSize(10); // Adjust font size for names
doc.text('ALVIN C. EVANGELISTA', nameSectionCenter1, namesY - 2, null, null, 'center'); // Name under the first section
doc.text('DANILO V. TAN', nameSectionCenter2, namesY - 2, null, null, 'center'); // Name under the second section

// Write titles, centered directly below each name
doc.text('HRMO II', nameSectionCenter1, titlesY, null, null, 'center'); // Title under the first name
doc.text('Municipal Mayor', nameSectionCenter2, titlesY, null, null, 'center'); // Title under the second name

// Save the document
doc.save('service-record.pdf');

       }
    </script>
</body>
</html>
