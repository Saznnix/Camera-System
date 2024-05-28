<?php
header('Content-Type: application/json; charset=utf-8');
include("../config.php");



if (
    isset($_POST["id_user"], $_POST["rfid"], $_POST["shop_name"], $_POST["price"], $_POST['details'])
) {
    $id_user_sell = $_POST["id_user"];
    $shop_name = $_POST["shop_name"];
    $rfid = $_POST["rfid"];
    $price = (float)$_POST["price"];
    $details = $_POST["details"];
    $id_order = time() . mt_rand(0, 100);

    $sql_rfid = "SELECT id_user FROM rfid WHERE rfid = ?";
    $stmt_rfid = $conn->prepare($sql_rfid);
    $stmt_rfid->bind_param("s", $rfid);
    $stmt_rfid->execute();
    $stmt_rfid->store_result();



    if ($stmt_rfid->num_rows > 0) {
        $stmt_rfid->bind_result($id_user_rfid);
        $stmt_rfid->fetch();

        $sql_getmoney = "SELECT 
            (SELECT SUM(price) FROM data_log WHERE id_user = ? AND mode = 0) -
            (SELECT SUM(price) FROM data_log WHERE id_user = ? AND mode = 1) AS money_balance";

        $stmt_getmoney = $conn->prepare($sql_getmoney);
        $stmt_getmoney->bind_param("ii", $id_user_rfid, $id_user_rfid);
        $stmt_getmoney->execute();
        $result_getmoney = $stmt_getmoney->get_result();
        $row_getmoney = $result_getmoney->fetch_assoc();
        $stmt_getmoney->close();

        if ($row_getmoney['money_balance'] < $price) {
            echo 3; // เงินไม่พอ
        } else {
            $sql_log_sell = "INSERT INTO data_log (id_order, id_user, shop_name, details, mode, price)
            VALUES (?, ?, ?, ?, 0, ?)";
            $stmt_log_sell = $conn->prepare($sql_log_sell);
            $stmt_log_sell->bind_param("ssssd", $id_order, $id_user_sell, $shop_name, $details, $price);
            $log_sell_success = $stmt_log_sell->execute();

            $sql_log_buy = "INSERT INTO data_log (id_order, id_user, shop_name, details, mode, price)
           VALUES (?, ?, ?, ?, 1, ?)";
            $stmt_log_buy = $conn->prepare($sql_log_buy);
            $stmt_log_buy->bind_param("ssssd", $id_order, $id_user_rfid, $shop_name, $details, $price);
            $log_buy_success = $stmt_log_buy->execute();

            if ($log_sell_success && $log_buy_success) {
                echo 1; // บันทึกสำเร็จ
            } else {
                echo 0; // บันทึกไม่สำเร็จ
            }
        }
    } else {
        echo 2; // ไม่มีrfidในระบบ
    }

    $stmt_rfid->close();
} else {
    echo 4; // ข้อมูลของ post ผิดพลาด
}


?>