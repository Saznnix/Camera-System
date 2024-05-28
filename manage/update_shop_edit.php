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

if (isset($_POST['shop_name'],$_GET['id'],$_POST['gridRadios'])) {

    include('../config.php');

    $id_user = $_GET['id'];
    $shop_name = $_POST['shop_name'];
    $status = $_POST['gridRadios'];

    // Using prepared statement to prevent SQL injection
    $sql = "UPDATE shop SET shop_name=?, status=? WHERE id=?";
    $stmt = $conn->prepare($sql);

    // Bind parameters and execute the statement
    $stmt->bind_param("sii", $shop_name, $status, $id_user);
    $stmt->execute();

    // Check if the query was successful
    if ($stmt->affected_rows > 0) {
        echo '<script>alert("บันทึกสำเร็จ"); window.location.href="../manage_staff.php";</script>';
    } else {
        echo '<script>alert("บันทึกไม่สำเร็จ"); window.location.href="../manage_staff.php";</script>';
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    header("Location: ../signin.php");
    exit();
}
?>
