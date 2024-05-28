<?php
header('Content-Type: application/json; charset=utf-8');

include("../config.php");

if (isset($_POST["shop_name"])) {
    $shop_name = $_POST["shop_name"];

    $sql = "SELECT * FROM product WHERE shop_name = ? AND status = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $shop_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        echo $json;
    } else {
        echo "ไม่มีร้านค้าที่ลงทะเบียน";
    }

    $stmt->close();
}
?>