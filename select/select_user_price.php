<?php
header('Content-Type: application/json; charset=utf-8');
include("../config.php");

if(isset($_POST['rfid'])){

$rfid = $_POST['rfid'];

$sql = "SELECT rfid, id_user FROM rfid WHERE rfid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $rfid);
$stmt->execute();
$result = $stmt->get_result();

$row = array(); // กำหนดให้ $row เป็น array ว่าง
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    //echo $row["id_user"];
} else {
    echo 0;
}

$stmt->close();


$id_user = $row["id_user"];


$sql_getmoney = "SELECT 
CASE 
    WHEN (SELECT SUM(price) FROM data_log WHERE id_user = ? AND mode = 0) IS NOT NULL 
         AND (SELECT SUM(price) FROM data_log WHERE id_user = ? AND mode = 1) IS NOT NULL 
    THEN
        (SELECT SUM(price) FROM data_log WHERE id_user = ? AND mode = 0) - (SELECT SUM(price) FROM data_log WHERE id_user = ? AND mode = 1)
    ELSE
        (SELECT SUM(price) FROM data_log WHERE id_user = ? AND mode = 0)
END AS money_balance";



$stmt_getmoney = $conn->prepare($sql_getmoney);
$stmt_getmoney->bind_param("iiiii", $id_user, $id_user, $id_user, $id_user, $id_user);
$stmt_getmoney->execute();
$result_getmoney = $stmt_getmoney->get_result();
$row_getmoney = $result_getmoney->fetch_assoc();
$stmt_getmoney->close();

echo $row_getmoney['money_balance'];

}

?>