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

if (isset($_POST['pass'], $_GET['id_user'], $_POST['newpass'])) {

    include('../config.php');

    $id_user = $_GET['id_user'];
    $entered_password = $_POST['pass'];
    $pass = $_POST['newpass'];


    $stmt = $conn->prepare("SELECT id_user, user, pass, perm FROM user WHERE id_user = ?");
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id_user, $username, $hashed_password, $perm);
        $stmt->fetch();

        if (password_verify($entered_password, $hashed_password)) {
            $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

            $sql = "UPDATE user SET pass=? WHERE id_user=?";
            $stmt = $conn->prepare($sql);
            
            $stmt->bind_param("si", $hashed_pass, $id_user);
            $stmt->execute();

            // Check if the query was successful
            if ($stmt->affected_rows > 0) {
                //echo "User updated successfully.";
                echo '<script>alert("บันทึกสำเร็จ"); window.location.href="../manage_staff.php";</script>';
            } else {
                // echo "Failed to update user.";
                echo '<script>alert("บันทึกไม่สำเร็จ"); window.location.href="../manage_staff.php";</script>';
            }
        } else {
            echo '<script>alert("รหัสผ่านเก่าไม่ถูกต้อง"); window.location.href="../manage_staff.php";</script>';
        }

    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: ../signin.php");
    exit();
}
?>