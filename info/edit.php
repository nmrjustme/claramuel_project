<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $course = $conn->real_escape_string($_POST['course']);

    $stmt = $conn->prepare("UPDATE students SET first_name=?, last_name=?, email=?, course=? WHERE student_id=?");
    $stmt->bind_param("ssssi", $first_name, $last_name, $email, $course, $id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Student updated successfully";
    } else {
        $_SESSION['error'] = "Error updating student: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
    header("Location: index.php");
    exit();
}
?>