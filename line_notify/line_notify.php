<?php
include('../config.php');

//echo "yes";

if(isset($_POST['shop_name'])){

$shop_name = $_POST["shop_name"];

// Validate and sanitize the input (optional, depending on your requirements)
$shop_name = filter_var($shop_name, FILTER_SANITIZE_STRING);

$sql = "SELECT * FROM data_log WHERE mode = 0 AND shop_name = ? ORDER BY id DESC LIMIT 50";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $shop_name);
$stmt->execute();
$result = $stmt->get_result();

$sql_buy = "SELECT * FROM data_log WHERE mode = 1 AND shop_name = ? ORDER BY id DESC LIMIT 10";
$stmt_buy = $conn->prepare($sql_buy);
$stmt_buy->bind_param("s", $shop_name);
$stmt_buy->execute();
$result_buy = $stmt_buy->get_result();

// Rest of your code...

$sql_shop = "SELECT * FROM shop";
$result_shop = $conn->query($sql_shop);




$currentDate = date("Y-m-d"); // วันที่ปัจจุบัน

// คำสั่ง SQL สำหรับรวมราคาของ mode = 0 วันนี้
$sql_money0_today = "SELECT SUM(price) AS total_price0_today FROM data_log WHERE mode = 0 AND DATE(time) = '$currentDate' AND shop_name = '$shop_name'";
$result_money0_today = $conn->query($sql_money0_today);
$row_money0_today = $result_money0_today->fetch_assoc();
$totalPrice0_today = $row_money0_today['total_price0_today'];

$thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));

// SQL query for mode = 0
$sql_money0_last30days = "SELECT SUM(price) AS total_price0_last30days FROM data_log WHERE mode = 0 AND time >= '$thirtyDaysAgo' AND shop_name = '$shop_name'";
$result_money0_last30days = $conn->query($sql_money0_last30days);
$row_money0_last30days = $result_money0_last30days->fetch_assoc();
$totalPrice0_last30days = $row_money0_last30days['total_price0_last30days'];

$sql_money0_all = "SELECT SUM(price) AS total_price0_all FROM data_log WHERE mode = 0 AND shop_name = '$shop_name'";
$result_money0_all = $conn->query($sql_money0_all);
$row_money0_all = $result_money0_all->fetch_assoc();
$totalPrice0_all = $row_money0_all['total_price0_all'];

// กรอกค่าเริ่มต้นในกรณีที่ไม่มีข้อมูล
$totalPrice0_today = $totalPrice0_today ?? 0;
$totalPrice0_all = $totalPrice0_all ?? 0;
$totalPrice0_last30days = $totalPrice0_last30days ?? 0;

$totalPrice0_today = number_format((float) $totalPrice0_today, 2, '.', '');
$totalPrice0_all = number_format((float) $totalPrice0_all, 2, '.', '');
$totalPrice0_last30days = number_format((float) $totalPrice0_last30days, 2, '.', '');

$conn->close();

// รหัสรับรองการเข้าถึง (Access Token) ที่ได้จาก Line Developers
$access_token = 'cztQtDffkmZR3W0NwQIBa3D7LSt4qJbZfdrBa1VxmNS';

// ข้อความที่ต้องการส่ง
$message = "ร้านค้า $shop_name \nยอดขายของวันนี้ \n$totalPrice0_today บาท \nยอดขายย้อนหลัง 30 วัน \n$totalPrice0_last30days บาท ";

// URL ของ Line Notify API
$url = 'https://notify-api.line.me/api/notify';

// ตัวแปรที่จำเป็นสำหรับการเรียก API
$data = array('message' => $message);
$options = array(
    'http' => array(
        'header' => "Authorization: Bearer $access_token\r\n" .
                    "Content-type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($data),
    ),
);

// ใช้ stream context ในการเรียก API
$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

// ตรวจสอบผลลัพธ์
if ($result === FALSE) {
    echo 'Error sending Line Notify!';
} else {
    echo 'Line Notify sent successfully!';
}
}
?>
