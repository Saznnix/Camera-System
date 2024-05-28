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

if (isset($_POST['shop_name'], $_POST['id_user'], $_POST['user'], $_POST['product_name'], $_POST['price'])) {

    include('../config.php');

    $id_user = $_POST['id_user'];
    $user = $_POST['user'];
    $shop_name = $_POST['shop_name'];
    $product_name = $_POST['product_name'];
    $price = (float) $_POST["price"];
    $id = $_POST["id"];

    // Prepare statement to prevent SQL injection
    $sql_product = "INSERT INTO product (id_user, user, shop_name, product_name, price) VALUES (?, ?, ?, ?, ?)";
    $stmt_product = $conn->prepare($sql_product);

    if (!$stmt_product) {
        echo "เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . $conn->error;
        exit();
    }

    // Bind parameters
    $stmt_product->bind_param("dsssd", $id_user, $user, $shop_name, $product_name, $price);

    // Execute statement
    $stmt_product_success = $stmt_product->execute();

    // Check if the query was successful
    if ($stmt_product_success) {
        echo '<script>alert("เพิ่มสำเร็จ"); window.location.href="shop_edit.php?id='.$id.'";</script>';
    } else {
        echo '<script>alert("เพิ่มไม่สำเร็จ"); window.location.href="shop_edit.php?id='.$id.'";</script>';
    }

    // Close statement and connection
    $stmt_product->close();
    $conn->close();
}

?>