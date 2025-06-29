<?php
session_start();

$emailErr = $pwdErr = "";
$email = $pwd = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = htmlspecialchars(trim($_POST["email"]));
    }

    if (empty($_POST["pwd"])) {
        $pwdErr = "Password is required";
    } else {
        $pwd = $_POST["pwd"];  // Don't trim or escape password
    }

    if (empty($emailErr) && empty($pwdErr)) {
        $conn = new mysqli("mysql", "root", "password", "users");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("SELECT id, name, password FROM user_info WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $name, $hashedPwd);
            $stmt->fetch();

            if (password_verify($pwd, $hashedPwd)) {
              
                $_SESSION["LOGGEDIN"] = true;
                $_SESSION["ID"] = $id;
                $_SESSION["USERNAME"] = $name;

                header("Location: users.php");
                exit;
            } else {
                $pwdErr = "Incorrect password";
            }
        } else {
            $emailErr = "Email not found";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Login</title>
<style>
    body { text-align: center; font-family: Arial, sans-serif; }
    .error { color: red; }
    form { display: inline-block; text-align: left; margin-top: 20px; }
</style>
</head>
<body>
<h1>Login</h1>
<?php if (isset($_GET['registered'])) {
    echo "<p style='color:green;'>Registration successful! Please login.</p>";
} ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label>Email:</label><br />
    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" /><br />
    <span class="error"><?php echo $emailErr; ?></span><br />

    <label>Password:</label><br />
    <input type="password" name="pwd" /><br />
    <span class="error"><?php echo $pwdErr; ?></span><br />

    <input type="submit" name="login" value="Login" />

</form>
<p><a href="sighnUp.php">Don't have an account? Sign up here</a></p>
</body>
</html>
