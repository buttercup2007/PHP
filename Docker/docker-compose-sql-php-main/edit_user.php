<?php
$conn = require_once "../partials/dbconnection.php";

if (!isset($_GET['id'])) {
    die("User ID not provided.");
}

$id = intval($_GET['id']);
$name = $email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Form was submitted - process update
    $newName = htmlspecialchars(trim($_POST["name"]));
    $newEmail = htmlspecialchars(trim($_POST["email"]));

    $updateStmt = $conn->prepare("UPDATE user_info SET name = ?, email = ? WHERE id = ?");
    $updateStmt->bind_param("ssi", $newName, $newEmail, $id);
    $updateStmt->execute();
    $updateStmt->close();

    // ✅ Redirect to users.php with success message
    header("Location: users.php?updated=1");
    exit;
} else {
    // Display current values
    $stmt = $conn->prepare("SELECT name, email FROM user_info WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($name, $email);
    $stmt->fetch();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit User</title>
</head>
<body>
  <h2>Edit User</h2>
  <form method="post">
    <label>Name:</label><br>
    <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required><br><br>

    <input type="submit" value="Update">
    <a href="users.php">Cancel</a>
  </form>
</body>
</html>
