<?php
header('Content-Type: application/json; charset=utf-8');
include("../config.php");

if (isset($_POST["id_user"])) {
    $id_user = $_POST["id_user"];

    // ใช้ Prepared Statements เพื่อป้องกัน SQL Injection
    $sql = "SELECT shop_name FROM shop WHERE id_user = ? AND status = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row['shop_name'];
        }
        $json = json_encode($data);
        echo $json;
    } else {
        $ERR = 0;
        echo json_encode($ERR);
    }

    $stmt->close();
}


?>