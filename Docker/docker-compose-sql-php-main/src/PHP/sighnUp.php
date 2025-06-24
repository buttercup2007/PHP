<?php
session_start();

// Error- en invoervariabelen
$nameErr = $emailErr = $ageErr = $pwdErr = "";
$name = $email = $age = $pwd = "";
$feedback = "";
$isEdit = isset($_GET['edit']); // Als bewerkingsmodus actief is

// Verbinding maken
$servername = "mysql";
$username = "root";
$password = "password";
$database = "users";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("<p>Connection failed: " . $conn->connect_error . "</p>");
}

// Bestaande data ophalen als we gaan bewerken
if ($isEdit && isset($_GET['id'])) {
    $stmt = $conn->prepare("SELECT id, name, email, age FROM user_info WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $name = $row['name'];
        $email = $row['email'];
        $age = $row['age'];
        $_SESSION['edit_id'] = $row['id']; // Opslaan voor latere verwerking
    } else {
        $feedback = "Gebruiker niet gevonden.";
    }
    $stmt->close();
}

// Bij verzenden formulier
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validatie
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

    if (!$isEdit) { // Alleen bij registratie wachtwoord vragen
        if (empty($_POST["pwd"])) {
            $pwdErr = "Password is required";
        } else {
            $pwd = htmlspecialchars(trim($_POST["pwd"]));
            if (strlen($pwd) < 6) {
                $pwdErr = "Password must be at least 6 characters long";
            }
        }
    }

    // Geen fouten
    if (empty($nameErr) && empty($emailErr) && empty($ageErr) && ($isEdit || empty($pwdErr))) {
        if ($isEdit && isset($_SESSION['edit_id'])) {
            // UPDATE query
            $stmt = $conn->prepare("UPDATE user_info SET name = ?, email = ?, age = ? WHERE id = ?");
            $stmt->bind_param("ssii", $name, $email, $age, $_SESSION['edit_id']);

            if ($stmt->execute()) {
                $feedback = "Gegevens succesvol bijgewerkt!";
            } else {
                $feedback = "Fout bij bijwerken: " . $stmt->error;
            }
            $stmt->close();
        } else {
            // Nieuw account aanmaken
            $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO user_info (name, email, age, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssis", $name, $email, $age, $hashedPwd);

            if ($stmt->execute()) {
                header("Location: users.php?a=1");
                exit();
            } else {
                $feedback = "Fout bij registratie: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title><?php echo $isEdit ? 'Wijzig Gegevens' : 'Sign up'; ?></title>
    <style>
        body {
            text-align: center;
            font-family: Arial, sans-serif;
        }
        .error {
            color: red;
        }
        form {
            display: inline-block;
            text-align: left;
            margin-top: 20px;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>

<h1><?php echo $isEdit ? 'Wijzig jouw gegevens' : 'Registreren'; ?></h1>

<?php if ($feedback): ?>
    <p class="<?php echo strpos($feedback, 'succes') !== false ? 'success' : 'error'; ?>"><?php echo $feedback; ?></p>
<?php endif; ?>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . ($isEdit ? '?edit=1&id=' . $_GET['id'] : ''); ?>" method="post">

    <label for="email">Email:</label><br>
    <input type="text" id="email" name="email" value="<?php echo $email; ?>">
    <span class="error">* <?php echo $emailErr; ?></span><br><br>

    <?php if (!$isEdit): ?>
        <label for="pwd">Password:</label><br>
        <input type="password" id="pwd" name="pwd" value="<?php echo $pwd; ?>">
        <span class="error">* <?php echo $pwdErr; ?></span><br><br>
    <?php endif; ?>

    <label for="age">Age:</label><br>
    <input type="number" id="age" name="age" value="<?php echo $age; ?>">
    <span class="error">* <?php echo $ageErr; ?></span><br><br>

    <label for="name">Name:</label><br>
    <input type="text" id="name" name="name" value="<?php echo $name; ?>">
    <span class="error">* <?php echo $nameErr; ?></span><br><br>

    <input type="submit" value="<?php echo $isEdit ? 'Bijwerken' : 'Registreren'; ?>">
</form>

</body>
</html>
