<?php
$host = 'localhost';
$user = 'root';
$pass = 'JaneandThomasin10!_8.5!'; 
$db   = 'care_project'; 

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Database connection failed: " . $conn->connect_error);
}
?>
