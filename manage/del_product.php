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

if (isset($_GET['id'])) {

    include('../config.php');

    $id = $_GET['id'];


    // Using prepared statement to prevent SQL injection
    $sql = "DELETE FROM `product` WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id); // i แทนให้ระบุว่าข้อมูลที่เข้ามาเป็น Integer
    $stmt->execute();


    // Check if the query was successful
    if ($stmt->affected_rows > 0) {
        echo '<script>alert("ลบสำเร็จ"); window.location.href="shop_edit.php?id='.$id.'";</script>';
    } else {
        echo '<script>alert("ลบไม่สำเร็จ"); window.location.href="shop_edit.php?id='.$id.'";</script>';
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>