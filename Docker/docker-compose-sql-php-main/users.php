<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Games; met SQL prepared statement en partial</title>

  <link rel="stylesheet" href="css/style.css">
</head>

<body>
<style>
table {
  font-family: Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: auto;
}

td,
th {
  border: 1px solid #ddd;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #f2f2f2;
}

tr:hover {
  background-color: #ddd;
}

th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: #04aa6d;
  color: white;
}
</style>

<h2>User Table</h2>

  <table>
    <tr>
      <th>Id</th>
      <th>name</th>
      <th>email</th>
      <th>password</th>
    </tr>

    <?php
    $conn = require_once "../partials/dbconnection.php";
    

    $stmt = $conn->prepare("SELECT * FROM user_info");


    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0)
      exit('No users found');

    while ($row = $result->fetch_assoc()) {
      echo "<tr>";
  
      echo "<td>" . $row['id'] . "</td>";
      echo "<td>" . $row['name'] . "</td>";
      echo "<td>" . $row['email'] . "</td>";
      echo "<td>" . $row['password'] . "</td>";
      echo "</tr>";
    }
    echo "</table>";

    $stmt->close();
    ?>
 
   <input type="submit" value="Delete">
   <input type="submit" value="Update">
</body>

</html>