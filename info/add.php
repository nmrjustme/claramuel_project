<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $course = $conn->real_escape_string($_POST['course']);

    $stmt = $conn->prepare("INSERT INTO students (first_name, last_name, email, course) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $first_name, $last_name, $email, $course);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Student added successfully";
    } else {
        $_SESSION['error'] = "Error adding student: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
    header("Location: Act1.php");
    exit();
}
?>