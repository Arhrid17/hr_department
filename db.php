<?php
$host = 'localhost';
$db_name = 'hr_department';
$username = 'root';
$password = ''; 

$conn = new mysqli($host, $username, $password, $db_name);

if ($conn->connect_error) {
    die("Помилка підключення до бази даних: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>