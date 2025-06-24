<?php
$servername = "mysql"; // or "localhost" if you're using XAMPP/LAMP
$username =  "root";// ini_get('mysqli.default_user'); // gets default from php.ini
$password = "password";//ini_get('mysqli.default_pw');   // gets default from php.ini
$database = "users";

try {
    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        error_log("MySQL connection error: " . $conn->connect_error);
        exit("Connection to database failed.");
    }
} catch (Exception $e) {
    error_log("Exception during DB connection: " . $e->getMessage());
    exit("Connection to database failed.");
}

return $conn;