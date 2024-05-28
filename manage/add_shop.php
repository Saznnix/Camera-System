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

if (isset($_POST['shop_name'], $_POST['id_user'], $_POST['user'])) {

    include('../config.php');

    $id_user = $_POST['id_user'];
    $user = $_POST['user'];
    $shop_name = $_POST['shop_name'];

    // Check if shop_name already exists in the database
    $sql_check_shop_name = "SELECT shop_name FROM shop WHERE shop_name = ?";
    $stmt_check_shop_name = $conn->prepare($sql_check_shop_name);
    $stmt_check_shop_name->bind_param("s", $shop_name); // แก้ไขตรงนี้เป็น "s" แทน "d"
    $stmt_check_shop_name->execute();
    $stmt_check_shop_name->store_result();

    if ($stmt_check_shop_name->num_rows > 0) {
        echo '<script>alert("มีชื่อร้านนี้ในระบบอยู่แล้ว"); window.location.href="../manage_staff.php";</script>';
        exit(); // ใส่ exit เพื่อหยุดการทำงานทันทีหลังจากแสดงข้อความแล้ว
    }

    // Using prepared statement to prevent SQL injection
    $sql_shop = "INSERT INTO shop (id_user, shop_name, user ,status) VALUES (?, ?, ?, 1)";
    $stmt_shop = $conn->prepare($sql_shop);
    $stmt_shop->bind_param("dss", $id_user, $shop_name, $user);
    $stmt_shop_success = $stmt_shop->execute();

    // Check if the query was successful
    if ($stmt_shop_success) {
        echo '<script>alert("เพิ่มสำเร็จ"); window.location.href="../manage_staff.php";</script>';
    } else {
        echo '<script>alert("เพิ่มไม่สำเร็จ"); window.location.href="../manage_staff.php";</script>';
    }

    // Close statement and connection
    $stmt_shop->close();
    $stmt_check_shop_name->close();
    $conn->close();
}
?>