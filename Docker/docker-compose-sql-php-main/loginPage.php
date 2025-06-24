<?php
// Error and input variables
$nameErr = $emailErr = $ageErr = $pwdErr = "";
$name = $email = $age = $pwd = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    
    // Validate email
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = htmlspecialchars(trim($_POST["email"]));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

    // Validate password
    if (empty($_POST["pwd"])) {
        $pwdErr = "Password is required";
    } else {
        $pwd = htmlspecialchars(trim($_POST["pwd"]));
        if (strlen($pwd) < 6) {
            $pwdErr = "Password must be at least 6 characters long";
        }
    }

    // If no errors, insert into database
    if (empty($nameErr) && empty($emailErr) && empty($ageErr) && empty($pwdErr)) {
        // Database credentials
        $servername = "mysql";
        $username = "root";
        $password = "password"; 
        $database = "users";
        
        
        $conn = new mysqli($servername, $username, $password, $database);


        if ($conn->connect_error) {
            die("<p>Connection failed: " . $conn->connect_error . "</p>");
        }

        // hash password
        $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);

        // prepare and execute insert statement
        $stmt = $conn->prepare("SELECT * FROM user_info WHERE email = ? and PASSWORD = ?");
        $stmt->bind_param("ss", $email, $hashedPwd);

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0)
        {
        
            $url = "users.php?a=1";
            header('Location: '.$url);
            die();

        } else {
            echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up</title>
    <style>
        body {
            text-align: center;
            font-family: Arial, sans-serif;
        }
        .error {
            color: #FF0000;
        }
        form {
            display: inline-block;
            text-align: left;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<h1>Login in</h1>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <label for="email">Email:</label><br>
    <input type="text" id="email" name="email" value="<?php echo $email; ?>">
    <span class="error">* <?php echo $emailErr; ?></span><br><br>

    <label for="pwd">Password:</label><br>
    <input type="password" id="pwd" name="pwd" value="<?php echo $pwd; ?>">
    <span class="error">* <?php echo $pwdErr; ?></span><br><br>

    <input type="submit" value="Submit">
</form>

</body>
</html>
