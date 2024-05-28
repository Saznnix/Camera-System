<?php

session_start();

if (!isset($_SESSION['username'])) {
    // User is not logged in, redirect to login page
    header("Location: ../signin.php");
    exit();
} else {
    // Regenerate session ID to prevent session fixation attacks
    session_regenerate_id();
}

$perm = $_SESSION["perm"];

if ($perm != 0) {
    header("Location: ../signin.php");
    exit();
}

if (isset($_GET['id_user'])) {

    include('../config.php');

    $id_user = $_GET['id_user'];


    // Using prepared statement to prevent SQL injection
    $sql = "DELETE FROM `user` WHERE id_user = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_user); // i แทนให้ระบุว่าข้อมูลที่เข้ามาเป็น Integer
    $stmt->execute();


    // Check if the query was successful
    if ($stmt->affected_rows > 0) {
        echo '<script>alert("ลบสำเร็จ"); window.location.href="../manage_staff.php";</script>';
    } else {
        echo '<script>alert("ลบไม่สำเร็จ"); window.location.href="../manage_staff.php";</script>';
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>