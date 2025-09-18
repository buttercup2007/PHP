<?php
$nameErr = $emailErr = $ageErr = $pwdErr = "";
$name = $email = $age = $pwd = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
    } else {
        $name = htmlspecialchars(trim($_POST["name"]));
        if (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
            $nameErr = "Only letters and white space allowed";
        }
    }

    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = htmlspecialchars(trim($_POST["email"]));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

    if (empty($_POST["age"])) {
        $ageErr = "Age is required";
    } else {
        $age = htmlspecialchars(trim($_POST["age"]));
        if (!is_numeric($age) || $age < 0) {
            $ageErr = "Please enter a valid age";
        }
    }

    if (empty($_POST["pwd"])) {
        $pwdErr = "Password is required";
    } else {
        $pwd = htmlspecialchars(trim($_POST["pwd"]));
        if (strlen($pwd) < 6) {
            $pwdErr = "Password must be at least 6 characters long";
        }
    }

    if (empty($nameErr) && empty($emailErr) && empty($ageErr) && empty($pwdErr)) {
       
        $conn = new mysqli("mysql", "root", "password", "users");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

       
        $checkStmt = $conn->prepare("SELECT id FROM user_info WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkStmt->store_result();
        if ($checkStmt->num_rows > 0) {
            $emailErr = "Email is already registered";
        } else {
            
            $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO user_info (name, email, age, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssis", $name, $email, $age, $hashedPwd);

            if ($stmt->execute()) {
                
                header("Location: loginPage.php?registered=1");
                exit;
            } else {
                echo "<p style='color:red'>Error: " . $stmt->error . "</p>";
            }
            $stmt->close();
        }
        $checkStmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Sign Up</title>
<style>
    body { text-align: center; font-family: Arial, sans-serif; }
    .error { color: red; }
    form { display: inline-block; text-align: left; margin-top: 20px; }
</style>
</head>
<body>
<h1>Sign Up</h1>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label>Name:</label><br />
    <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" /><br />
    <span class="error"><?php echo $nameErr; ?></span><br />

    <label>Email:</label><br />
    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" /><br />
    <span class="error"><?php echo $emailErr; ?></span><br />

    <label>Age:</label><br />
    <input type="number" name="age" value="<?php echo htmlspecialchars($age); ?>" /><br />
    <span class="error"><?php echo $ageErr; ?></span><br />

    <label>Password:</label><br />
    <input type="password" name="pwd" /><br />
    <span class="error"><?php echo $pwdErr; ?></span><br />

    <input type="submit" value="Register" />
</form>
<p><a href="loginPage.php">Already have an account? Log in here</a></p>
</body>
</html>

