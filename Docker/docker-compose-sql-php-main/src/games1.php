<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Games; met SQL plain en geen partial</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <table>
    <tr>
      <th>Id</th>
      <th>Age</th>
      <th>Summary</th>
      <th>Score</th>
    </tr>

    <?php
    $servername = "mysql";
    $username = "root";
    $password = "password";

    try {
      $conn = new mysqli($servername, $username, $password, "games");
      if ($conn->connect_error) {
        error_log($conn->connect_error);
        exit("Connection DB failed");
      }
    } catch (Exception $e) {
      error_log($e);
      exit("Connection DB failed");
    }

    $result = $conn->query("SELECT * FROM GAMES;");
    if ($result->num_rows == 0) {
      exit('No rows');
    }

    // output data of each row
    while ($row = $result->fetch_assoc()) {
      echo "<tr>";
      echo "<td> <a href='details.php?id=" . $row['id'] . "'>" . $row['id'] . "</a></td>";
      echo "<td>" . $row['name'] . "</td>";
      echo "<td>" . substr($row['summary'], 0, 50) . "</td>";
      echo "<td>" . $row['metascore'] . "</td>";
      echo "</tr>";
    }
    ?>
</body>

</html>