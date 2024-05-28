<?php
include("../config.php");

session_start();

$perm = $_SESSION["perm"];
if ($perm == 0 || $perm == 3 ) {
    //header("Location: signin.php");
} else {
    header("Location: ../signin.php");
}
if (isset($_POST["password"], $_POST["price"])) {

    $password = $_POST['password'];
    $price = (float) $_POST["price"];

    // Retrieve hashed password from the database based on the user's session
    $maker = $_SESSION['username'];
    $sql_password = "SELECT pass FROM user WHERE user = ?";
    $stmt_password = $conn->prepare($sql_password);
    $stmt_password->bind_param("s", $maker);
    $stmt_password->execute();
    $result_password = $stmt_password->get_result();

    if ($result_password->num_rows > 0) {
        $row = $result_password->fetch_assoc();
        $hashed_password = $row['pass'];

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Password is correct
            $id_user = $_GET["id_user"];
            $shop_name = "เติมเงิน";
            $details = 0;
            $id_order = time() . mt_rand(0, 100);

            $sql_log_sell = "INSERT INTO data_log (id_order, id_user, shop_name, details, mode, price, maker) VALUES (?, ?, ?, ?, 0, ?, ?)";
            $stmt_log_sell = $conn->prepare($sql_log_sell);
            $stmt_log_sell->bind_param("sssdss", $id_order, $id_user, $shop_name, $details, $price, $maker);
            $log_sell_success = $stmt_log_sell->execute();

            // Check if the query was successful
            if ($log_sell_success) {
                echo '<script type="text/javascript">alert("เติมเงินสำเร็จ"); window.location.href="../form.php";</script>';
            } else {
                $message = "เติมเงินไม่สำเร็จ";
                echo '<script type="text/javascript">alert("เติมเงินไม่สำเร็จ"); window.location.href="../form.php";</script>';
            }
        } else {
            // Password is incorrect
            echo '<script type="text/javascript">alert("รหัสผ่านไม่ถูกต้อง"); window.location.href="../form.php";</script>';
        }
    } else {
        // User not found
        echo '<script type="text/javascript">alert("ไม่พบผู้ใช้"); window.location.href="../form.php";</script>';
    }

    // Close statement and connection
    $stmt_password->close();
    $stmt_log_sell->close();
    $conn->close();
}




?>