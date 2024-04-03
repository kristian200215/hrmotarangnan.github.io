<?php 
    include("connection.php");
    session_start(); // Start the session
    if(isset($_SESSION['username'])) {
        // If the user is already logged in, redirect to welcome page
        header("Location: /tarangnan/admin/table/table.php");
        exit;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
        // Check if form is submitted
        $username = $_POST['user'];
        $password = $_POST['pass'];

        // Validate inputs
        if(empty($username) || empty($password)) {
            // Handle empty fields
            echo '<script>alert("Username and password are required.");</script>';
        } else {
            // Query to check username and password
            $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                // Set session variables and redirect to welcome page
                $_SESSION['username'] = $username;
                header("Location: /tarangnan/admin/table/dashboard.php");
                exit;
            } else {
                // Invalid credentials
                echo '<script>alert("Invalid username or password.");</script>';
            }
        }
    }
?>
    
<html>
    <head>
        <title>Login</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="styles.css">
    </head>
    <style>
        body{
            font-family:"Inter", sans-serif;
            font-weight:400;
        }
        #form{
            width:500px;
            margin-bottom:100px;
            background: rgba(0,0,0,0.4);
            -webkit-backdrop-filter: blur(10px);
        }
        h2, label{
            color:white;
            font-family:"Inter", sans-serif;
            font-weight:400;
            text-shadow:0px 0px 2px black;
            letter-spacing:2px;
        }
    </style>
    <body>
        <div id="form">
            <a class="logo">
                <img src="download.jpg" height="150" alt="MDB Logo" loading="lazy">
            </a>
            <br>
            <h2>HRMO | OFFICE</h2>
            <hr style="border:1px solid grey; box-shadow:0px 0px 1px white;">
            <form name="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <h2>LOG IN</h2>
                <label>Username: </label>
                <input type="text" id="user" name="user"></br></br>
                <label>Password: </label>
                <input type="password" id="pass" name="pass"></br></br>
                <div>
                    <input type="submit" id="btn" value="Sign In" name="submit"/>

                </div>
            </form>
        </div>
    </body>
</html>
