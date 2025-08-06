<?php
$host = 'localhost';
$user = 'root';
$pass = 'YOUR_PASSWORD'; 
$db   = 'care_project'; 

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Database connection failed: " . $conn->connect_error);
}
error_reporting(E_ALL & ~E_DEPRECATED);
?>
