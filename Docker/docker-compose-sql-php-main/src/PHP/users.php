<?php
session_start();

if (!isset($_SESSION["LOGGEDIN"]) || $_SESSION["LOGGEDIN"] !== true) {
    header("Location: loginPage.php");
    exit;
}

$conn = require_once "../partials/dbconnection.php";

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $delStmt = $conn->prepare("DELETE FROM user_info WHERE id = ?");
    $delStmt->bind_param("i", $delete_id);
    $delStmt->execute();
    $delStmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Management</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    table {
      font-family: Arial, Helvetica, sans-serif;
      border-collapse: collapse;
      width: auto;
    }

    td, th {
      border: 1px solid #ddd;
      padding: 8px;
    }

    tr:nth-child(even) { background-color: #f2f2f2; }
    tr:hover { background-color: #ddd; }

    th {
      padding-top: 12px;
      padding-bottom: 12px;
      text-align: left;
      background-color: #04aa6d;
      color: white;
    }

    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
  </style>
</head>
<body>

<div class="top-bar">
  <h2>User Table</h2>
  <div>
    <strong>Welcome, <?php echo htmlspecialchars($_SESSION["USERNAME"]); ?></strong> |
    <a href="logout.php">Logout</a>
  </div>
</div>

<?php if (isset($_GET['updated'])): ?>
  <p style="color: green;">User updated successfully!</p>
<?php endif; ?>

<table>
  <tr>
    <th>Id</th>
    <th>Name</th>
    <th>Email</th>
    <th>Password</th>
    <th>Action</th>
  </tr>

  <?php
  $stmt = $conn->prepare("SELECT * FROM user_info");
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 0) {
    echo "<tr><td colspan='5'>No users found</td></tr>";
  }

  while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
    echo "<td>" . htmlspecialchars($row['password']) . "</td>";
    echo "<td>
        <form method='post' action='' onsubmit='return confirm(\"Are you sure you want to delete this user?\");' style='display:inline-block;'>
          <input type='hidden' name='delete_id' value='" . $row['id'] . "'>
          <input type='submit' value='Delete'>
        </form>
        <form method='get' action='edit_user.php' style='display:inline-block;'>
          <input type='hidden' name='id' value='" . $row['id'] . "'>
          <input type='submit' value='Update'>
        </form>
      </td>";
  }

  echo "</table>";
  $stmt->close();
  ?>

</body>
</html>
