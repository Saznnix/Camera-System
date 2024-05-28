<?php
include("../config.php");

session_start();

if (isset($_POST["rfid"], $_POST["id_user"])) {
    // Validate and sanitize inputs
    $rfid = filter_var($_POST["rfid"], FILTER_SANITIZE_STRING);
    $id_user = filter_var($_POST["id_user"], FILTER_SANITIZE_STRING);

    // Prepared statement to check for existing RFID
    $sql_check_rfid = "SELECT rfid FROM rfid WHERE rfid = ?";
    $stmt_check_rfid = $conn->prepare($sql_check_rfid);
    $stmt_check_rfid->bind_param("s", $rfid);
    $stmt_check_rfid->execute();
    $result_check_rfid = $stmt_check_rfid->get_result();

    if ($result_check_rfid->num_rows > 0) {
        // RFID already exists in the system
        echo '<script>alert("มี RFID นี้ในระบบแล้ว"); window.location.href="../form.php";</script>';
    } else {
        // Prepared statement to insert new RFID
        $sql_insert_rfid = "INSERT INTO rfid (id_user, rfid) VALUES (?, ?)";
        $stmt_insert_rfid = $conn->prepare($sql_insert_rfid);
        $stmt_insert_rfid->bind_param("ss", $id_user, $rfid);

        if ($stmt_insert_rfid->execute()) {
            // RFID added successfully
            echo '<script>alert("เพิ่มสำเร็จ"); window.location.href="../form.php";</script>';
        } else {
            // Error in adding RFID
            echo '<script>alert("เพิ่มไม่สำเร็จ"); window.location.href="../form.php";</script>';
        }
    }
} else {
    echo '<script>alert("เพิ่มไม่สำเร็จ"); window.location.href="../form.php";</script>';
}

// Close database connection
$conn->close();
?>