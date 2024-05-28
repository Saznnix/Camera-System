<?php
/*
include("../config.php");



if (isset($_POST["user"]) && isset($_POST["pass"])) {
    $user = $_POST["user"];
    $pass = $_POST["pass"];

    $sql = "SELECT * FROM user WHERE user = ? AND pass = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user, $pass);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $json = json_encode($row);
        echo $json;
    } else {
        echo "0";
    }

    $stmt->close();
}*/


include("../config.php");

if (isset($_POST["user"]) && isset($_POST["pass"])) {



    $user = $_POST["user"];
    $pass = $_POST["pass"];

    $sql = "SELECT * FROM user WHERE user = ? AND perm = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashed_pass_from_db = $row['pass'];

        if (password_verify($pass, $hashed_pass_from_db)) {
            $json = json_encode($row);
            echo $json;
        } else {
            echo "0"; // รหัสผ่านไม่ถูกต้อง
        }
    } else {
        echo "0"; // ไม่พบผู้ใช้
    }

    $stmt->close();
}


?>