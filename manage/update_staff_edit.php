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

if (isset($_POST['fname'], $_POST['lname'], $_POST['gridRadios'], $_GET['id_user'])) {

    include('../config.php');

    $id_user = $_GET['id_user'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $status = $_POST['gridRadios'];

    // Using prepared statement to prevent SQL injection
    $sql = "UPDATE user SET fname=?, lname=?, status=? WHERE id_user=?";
    $stmt = $conn->prepare($sql);

    // Bind parameters and execute the statement
    $stmt->bind_param("sssi", $fname, $lname, $status, $id_user);
    $stmt->execute();

    // Check if the query was successful
    if ($stmt->affected_rows > 0) {
        //echo "User updated successfully.";
        
        echo '<script>alert("บันทึกสำเร็จ"); window.location.href="../manage_staff.php";</script>';
    } else {
        // echo "Failed to update user.";
        echo '<script>alert("บันทึกไม่สำเร็จ"); window.location.href="../manage_staff.php";</script>';
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>