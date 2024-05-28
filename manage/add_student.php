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

if (isset($_POST["user"],$_POST['fname'], $_POST['lname'])) {

    include('../config.php');


    // สร้างเลขสุ่มที่ไม่ซ้ำกันจากเวลาในการสุ่มและเลขสุ่ม
    $id_user = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);


    $user = $_POST['user'];
    $pass = substr($_POST['user'], -6);
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);



    // Check if id_user already exists in the database
    $sql_check_id_user = "SELECT id_user FROM user WHERE id_user = ?";
    $stmt_check_id_user = $conn->prepare($sql_check_id_user);
    $stmt_check_id_user->bind_param("d", $id_user);
    $stmt_check_id_user->execute();
    $stmt_check_id_user->store_result();

    if ($stmt_check_id_user->num_rows > 0) {
        echo '<script>alert("เกิดข้อผิดพลาดบางอย่าง โปรดลองใหม่อีกครั้ง"); window.location.href="../manage_students.php";</script>';
    } else {
        // Check if user already exists in the database
        $sql_check_user = "SELECT user FROM user WHERE user = ?";
        $stmt_check_user = $conn->prepare($sql_check_user);
        $stmt_check_user->bind_param("s", $user);
        $stmt_check_user->execute();
        $stmt_check_user->store_result();

        if ($stmt_check_user->num_rows > 0) {
            echo '<script>alert("มี user นี้อยู่ในระบบอยู่แล้ว โปรดลองใหม่อีกครั้ง"); window.location.href="../manage_students.php";</script>';
        } else {



            // Using prepared statement to prevent SQL injection
            $sql_user = "INSERT INTO user (id_user, user, pass, perm, fname , lname, status) VALUES (?, ?, ?, 2, ?, ?, 1)";
            $stmt = $conn->prepare($sql_user);
            $stmt->bind_param("dssss", $id_user, $user, $hashed_pass, $fname, $lname);
            $stmt_success = $stmt->execute();

            // Check if the query was successful
            if ($stmt_success) {
                echo '<script>alert("เพิ่มสำเร็จ"); window.location.href="../manage_students.php";</script>';
            } else {
                echo '<script>alert("เพิ่มไม่สำเร็จ"); window.location.href="../manage_students.php";</script>';
            }
            $stmt->close();


            // Close statement and connection

            $conn->close();
        }
        $stmt_check_user->close();





    }
    $stmt_check_id_user->close();





}
?>