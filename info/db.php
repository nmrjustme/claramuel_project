<?php
$host = '35.222.250.108';
$db = 'student_info';
$user = 'root';
$pass = '!Student-db2025!';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
