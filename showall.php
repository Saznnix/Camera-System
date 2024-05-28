<?php
session_start();

if (!isset($_SESSION['username'])) {
    // User is not logged in, redirect to login page
    header("Location: signin.php");
    exit();
} else {
    // Regenerate session ID to prevent session fixation attacks
    session_regenerate_id();
}

include('config.php');

if (isset($_GET['money_in'])) {

    $sql = "SELECT * FROM data_log WHERE mode = 0 ORDER BY id DESC";
    $result = $conn->query($sql);
    //echo 'money_in';
} else if (isset($_GET['money_out'])) {
    $sql_buy = "SELECT * FROM data_log WHERE mode = 1 ORDER BY id DESC";
    $result = $conn->query($sql_buy);
    //echo 'money_out';
} else if (isset($_GET['shop_in'])) {

    $shop_name = $_GET['shop_name'];
    $sql = "SELECT * FROM data_log WHERE mode = 0 AND shop_name = ? ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $shop_name);
    $stmt->execute();
    $result = $stmt->get_result();
    // echo 'shop_name';

} else if (isset($_GET['user'])) {

    $stmt = $conn->prepare("SELECT * FROM user WHERE perm = 2 ORDER BY id DESC");
    $stmt->execute();
    $result = $stmt->get_result();

    // echo 'user';

} else if (isset($_GET['payin'])) {

    $id_user = $_GET['id_user'];
    // Use prepared statement to avoid SQL Injection
    $sql_in = "SELECT * FROM data_log WHERE mode = 0 AND id_user = ? ORDER BY id DESC LIMIT 50";
    $stmt_in = $conn->prepare($sql_in);
    $stmt_in->bind_param("i", $id_user);
    $stmt_in->execute();
    $result = $stmt_in->get_result();
    // echo 'payin';

} else if (isset($_GET['payout'])) {

    $id_user = $_GET['id_user'];
    // Use prepared statement to avoid SQL Injection
    $sql_out = "SELECT * FROM data_log WHERE mode = 1 AND id_user = ? ORDER BY id DESC LIMIT 50";
    $stmt_out = $conn->prepare($sql_out);
    $stmt_out->bind_param("i", $id_user);
    $stmt_out->execute();
    $result = $stmt_out->get_result();
    // echo 'payout';

} else {

    //  echo "no";
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>RIETC</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Roboto:wght@500;700&display=swap"
        rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid position-relative d-flex p-0">
        <!-- Spinner Start -->
        <div id="spinner"
            class="show bg-dark position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->


        <!-- Sidebar Start -->

        <!-- Sidebar End -->


        <!-- Content Start -->

        <!-- Navbar Start -->

        <!-- Navbar End -->


        <!-- Sale & Revenue Start -->

        <!-- Sale & Revenue End -->

        <!-- Recent Sales Start -->
        <div class="container-fluid pt-4 px-4">
            <div class="bg-secondary text-center rounded p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="mb-0">Show All</h6>
                </div>
                <div class="table-responsive">
                    <table class="table text-start align-middle table-bordered table-hover mb-0">
                        <thead>
                            <?php

                            if (isset($_GET['money_in'])) {

                                echo '<tr class="text-white">
                                <th scope="col">วันที่</th>
                                <th scope="col">หมายเลขคำสั่งซื้อ</th>
                                <th scope="col">ID</th>
                                <th scope="col">ชื่อรายการ</th>
                                <th scope="col">จำนวนเงิน</th>
                                <th scope="col">สถานะ</th>
                            </tr>';

                                while ($row = mysqli_fetch_assoc($result)) {
                                    $row['price'] = number_format((float) $row['price'], 2, '.', '');
                                    echo "<tr><td>" . $row['time'] . "</td> ";
                                    echo "<td>" . $row['id_order'] . "</td> ";
                                    echo "<td>" . $row['id_user'] . "</td> ";
                                    echo "<td>" . $row['shop_name'] . "</td> ";
                                    echo "<td>" . $row['price'] . "</td> ";
                                    if ($row['mode'] == 0) {
                                        echo "<td> สำเร็จ </td> </tr>";
                                    } else {
                                        echo "<td> ผิดพลาด </td> </tr>";
                                    }
                                }

                            } else if (isset($_GET['money_out'])) {

                                echo '<tr class="text-white">
                                <th scope="col">วันที่</th>
                                <th scope="col">หมายเลขคำสั่งซื้อ</th>
                                <th scope="col">ID</th>
                                <th scope="col">ชื่อรายการ</th>
                                <th scope="col">จำนวนเงิน</th>
                                <th scope="col">สถานะ</th>
                            </tr>';

                                while ($row = mysqli_fetch_assoc($result)) {
                                    $row['price'] = number_format((float) $row['price'], 2, '.', '');
                                    echo "<tr><td>" . $row['time'] . "</td> ";
                                    echo "<td>" . $row['id_order'] . "</td> ";
                                    echo "<td>" . $row['id_user'] . "</td> ";
                                    echo "<td>" . $row['shop_name'] . "</td> ";
                                    echo "<td>" . $row['price'] . "</td> ";
                                    if ($row['mode'] == 0) {
                                        echo "<td> ผิดพลาด </td> </tr>";
                                    } else {
                                        echo "<td> สำเร็จ </td> </tr>";
                                    }
                                }


                            } else if (isset($_GET['shop_in'])) {

                                echo '<tr class="text-white">
<th scope="col">วันที่</th>
<th scope="col">หมายเลขสั่งซืื้อ</th>
<th scope="col">รายการ</th>
<th scope="col">จำนวนเงิน</th>
<th scope="col">สถานะ</th>
</tr>';
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $row['price'] = number_format((float) $row['price'], 2, '.', '');
                                    echo "<tr><td>" . $row['time'] . "</td> ";
                                    echo "<td>" . $row['id_order'] . "</td> ";
                                    echo "<td>" . $row['details'] . "</td> ";
                                    echo "<td>" . $row['price'] . "</td> ";
                                    if ($row['mode'] == 0) {
                                        echo "<td> สำเร็จ </td> </tr>";
                                    } else {
                                        echo "<td> ผิดพลาด </td> </tr>";
                                    }
                                }

                            } else if (isset($_GET['user'])) {

                                echo '<tr class="text-white">
                                <th scope="col">รหัส นศ.</th>
                                <th scope="col">ชื่อ</th>
                                <th scope="col">นามสกุล</th>
                                <th scope="col">เติมเงิน</th>
                            </tr>';

                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr><td>" . $row['id_user'] . "</td> ";
                                    echo "<td>" . $row['fname'] . "</td> ";
                                    echo "<td>" . $row['lname'] . "</td> ";
                                    echo "<td> <a href='payment.php?id_user=" . $row['id_user'] . "'> เติมเงิน </a></td> </tr> ";
                                }

                            } else if (isset($_GET['payin'])) {

                                echo ' <tr>
                                <th scope="col">ผู้ทำรายการ</th>
                                <th scope="col">จำนวนเงิน</th>
                                <th scope="col">เวลา</th>
                            </tr>';

                                while ($row = $result->fetch_assoc()) {
                                    $row['price'] = number_format((float) $row['price'], 2, '.', '');
                                    echo "<tr><th scope='row' >" . $row['maker'] . "</th>";
                                    echo "<td> " . $row['price'] . " </td>";
                                    echo "<td> " . $row['time'] . " </td></tr>";
                                }


                            } else if (isset($_GET['payout'])) {

                                echo '<tr><th scope="col">หมายเลขคำสั่งซื้อ</th>
<th scope="col">ผู้ทำรายการ</th>
<th scope="col">จำนวนเงิน</th>
<th scope="col">เวลา</th>
</tr>';

                                while ($row = $result->fetch_assoc()) {
                                    $row['price'] = number_format((float) $row['price'], 2, '.', '');
                                    echo "<tr><th scope='row' >" . $row['id_order'] . "</th>";
                                    echo "<th scope='row' >" . $row['shop_name'] . "</th>";
                                    echo "<td> " . $row['price'] . " </td>";
                                    echo "<td> " . $row['time'] . " </td></tr>";
                                }

                            } else {

                                //  echo "no";
                            }
                            ?>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Sales End -->
        <!-- Footer Start -->

        <!-- Footer End -->

        <!-- Content End -->


        <!-- Back to Top -->

    </div>




    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/chart/chart.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>