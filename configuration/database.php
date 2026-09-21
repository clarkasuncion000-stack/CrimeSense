<?php
// Database connection settings for the local XAMPP environment.
$host = "localhost";
$user = "root";
$pass = "";
$db   = "crimesense";

// Create a MySQLi connection object to access the database.
$conn = new mysqli($host, $user, $pass, $db);

// If the connection fails, stop execution and show the error message.
if($conn->connect_error){
    die("Database Connection Failed : " . $conn->connect_error);
}

// Set the database character set to UTF-8 so names and text display correctly.
$conn->set_charset("utf8");
?>