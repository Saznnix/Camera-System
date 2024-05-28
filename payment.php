<?php
session_start();

// Check if the user is not logged in, then redirect to the login page
if (!isset($_SESSION['username'])) {
    header("Location: signin.php");
    exit();
} else {
    // Regenerate session ID to prevent session fixation attacks
    session_regenerate_id();
}

$perm = $_SESSION["perm"];
if ($perm == 0 || $perm == 3 || $perm == 2) {
    //header("Location: signin.php");
} else {
    header("Location: signin.php");
}
include('sector/sidebar.php');

include('config.php');

if (isset($_GET['rfid'])) {

    $rfid = $_GET['rfid'];
    $sql_rfid = "SELECT id_user FROM rfid WHERE rfid = ?";
    $stmt_rfid = $conn->prepare($sql_rfid);
    $stmt_rfid->bind_param("s", $rfid);
    $stmt_rfid->execute();
    $stmt_rfid->store_result();

    if ($stmt_rfid->num_rows > 0) {
        $stmt_rfid->bind_result($id_user_rfid);
        $stmt_rfid->fetch();
        $id_user = $id_user_rfid;
    }
}

if (isset($_GET['id_user'])) {
    $id_user = $_GET['id_user'];
}

// Use prepared statement to avoid SQL Injection
$sql_in = "SELECT * FROM data_log WHERE mode = 0 AND id_user = ? ORDER BY id DESC LIMIT 50";
$stmt_in = $conn->prepare($sql_in);
$stmt_in->bind_param("i", $id_user);
$stmt_in->execute();
$result_in = $stmt_in->get_result();

// Check if the query was successful
if (!$result_in) {
    die("Query failed: " . $stmt_in->error);
}

$sql_out = "SELECT * FROM data_log WHERE mode = 1 AND id_user = ? ORDER BY id DESC LIMIT 50";
$stmt_out = $conn->prepare($sql_out);
$stmt_out->bind_param("i", $id_user);
$stmt_out->execute();
$result_out = $stmt_out->get_result();

// Check if the query was successful
if (!$result_out) {
    die("Query failed: " . $stmt_out->error);
}

$sql_shop = "SELECT * FROM shop";
$result_shop = $conn->query($sql_shop);

// Check if the query was successful
if (!$result_shop) {
    die("Query failed: " . $conn->error);
}

$sql_name = "SELECT * FROM user WHERE id_user = ?";
$stmt_name = $conn->prepare($sql_name);
$stmt_name->bind_param("i", $id_user);
$stmt_name->execute();
$result_name = $stmt_name->get_result();
$row_name = $result_name->fetch_assoc();
$stmt_name->close();


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


$row_getmoney['money_balance'] = number_format((float) $row_getmoney['money_balance'], 2, '.', '');



$stmt_in->close();
$stmt_out->close();
$conn->close();


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Payment</title>
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

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    <!--js sweet alert-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-secondary navbar-dark">
                <a href="index.php" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary"><i class="fa fa-user-edit me-2"></i>RIETC</h3>
                </a>
                <div class="d-flex align-items-center ms-4 mb-4">
                    <div class="position-relative">
                        <img class="rounded-circle" src="<?php echo $imageprofile; ?>" alt=""
                            style="width: 60px; height: 60px;">
                        <div
                            class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1">
                        </div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0">
                            <?php echo $_SESSION['username'] ?>
                        </h6>
                        <span> <?php echo $rank ?></span>
                    </div>
                </div>
                <div class="navbar-nav w-100">
                    <?php

                    if ($perm == 0) {
                        echo '<a href="index.php" class="nav-item nav-link"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>';
                        echo '<a href="form.php" class="nav-item nav-link "><i class="fa fa-keyboard me-2"></i>เติมเงิน</a>';

                        echo '<a href="' . $partstaff . '" class="nav-item nav-link"><i class="fa fa-address-card me-2"></i>จัดการบัญชีพนักงาน</a>
    <a href="' . $partstudents . '" class="nav-item nav-link"><i class="fa fa-address-book me-2"></i>จัดการบัญชีนักศึกษา</a>';

                        echo '<div class="nav-item dropdown">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i
            class="fa fa-laptop me-2"></i>ร้านค้า</a>
    <div class="dropdown-menu bg-transparent border-0">';
                        if ($result_shop->num_rows > 0) {
                            while ($row = $result_shop->fetch_assoc()) {
                                //$shop_name[] = $row['shop_name'];
                                echo '<a href="shop.php?shop_name=' . $row['shop_name'] . '" class="dropdown-item">' . $row['shop_name'] . '</a>';
                            }
                        } else {
                            echo 'An error occurred.';
                        }
                        echo '</div></div>';

                    } else if ($perm == 3) {
                        echo '<a href="form.php" class="nav-item nav-link active"><i class="fa fa-keyboard me-2"></i>เติมเงิน</a>';

                    } else if ($perm == 2) {
                        echo '<a href="form.php" class="nav-item nav-link active"><i class="fa fa-keyboard me-2"></i>ข้อมูลธุรกรรม</a>';

                    } else {
                        echo 'An error occurred.';
                    }

                    ?>

                </div>
            </nav>
        </div>
        <!-- Sidebar End -->


        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <nav class="navbar navbar-expand bg-secondary navbar-dark sticky-top px-4 py-0">
                <a href="index.php" class="navbar-brand d-flex d-lg-none me-4">
                    <h2 class="text-primary mb-0"><i class="fa fa-user-edit"></i></h2>
                </a>
                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i class="fa fa-bars"></i>
                </a>
                <div class="navbar-nav align-items-center ms-auto">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <span class="d-none d-lg-inline-flex">
                                <?php echo $_SESSION['username'] ?>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-secondary border-0 rounded-0 rounded-bottom m-0">
                            <a href="logout/logout.php" class="dropdown-item">Log Out</a>
                        </div>
                    </div>
                </div>
            </nav>
            <!-- Navbar End -->

            <!-- Sale & Revenue Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
                            <i class="fa fa-chart-line fa-3x text-primary"></i>
                            <div class="ms-3">
                                <p class="mb-2">ชื่อบัญชี</p>
                                <h6 class="mb-0">
                                    <h6 class="mb-0">
                                        <?php
                                        if ($row_name != null) {
                                            echo $row_name['fname'];
                                            echo " ";
                                            echo $row_name['lname'];
                                        } else {
                                            echo "ไม่มีผู้ใช้ในระบบ";
                                        }
                                        ?>
                                    </h6>
                                </h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
                            <i class="fa fa-chart-line fa-3x text-primary"></i>
                            <div class="ms-3">
                                <p class="mb-2">เงินที่เหลือ</p>
                                <h6 class="mb-0">
                                    <h6 class="mb-0">
                                        <?php echo $row_getmoney['money_balance']; ?>
                                    </h6>
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Sale & Revenue End -->


            <!-- Table Start -->
            <?php
            if ($perm == 0 || $perm == 3) {
                echo ' <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-secondary rounded h-100 p-4">
                            <h6 class="mb-4">เติมเงิน</h6>
                            <form action="insert_log/insert_payment.php?id_user='.$id_user.'"method="post">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">จำนวนเงินที่ต้องการเติม</label>
                                    <input type="number" class="form-control" id="search-input" placeholder="จำนวนเงิน"
                                        name="price" autocomplete="off" step="0.01">

                                    <br>
                                    <label for="exampleInputEmail1" class="form-label">รหัสผ่าน</label>
                                    <input type="password" class="form-control" id="password" placeholder="Password"
                                        name="password">
                                </div>
                                <button type="submit" class="btn btn-primary">ตกลง</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>';

            }
            ?>
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-secondary rounded h-100 p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h6 class="mb-0">เงินเข้า</h6>
                                <?php
                                if ($id_user != null) {
                                    echo '<a href="showall.php?payin=1&id_user=' . $id_user . '">Show All</a>';
                                } ?>
                            </div>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">ผู้ทำรายการ</th>
                                        <th scope="col">จำนวนเงิน</th>
                                        <th scope="col">เวลา</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = $result_in->fetch_assoc()) {
                                        $row['price'] = number_format((float) $row['price'], 2, '.', '');
                                        echo "<tr><th scope='row' >" . $row['maker'] . "</th>";
                                        echo "<td> " . $row['price'] . " </td>";
                                        echo "<td> " . $row['time'] . " </td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-secondary rounded h-100 p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h6 class="mb-0">เงินออก</h6>
                                <?php
                                if ($id_user != null) {
                                    echo '<a href="showall.php?payout=1&id_user=' . $id_user . '">Show All</a>';
                                } ?>
                            </div>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">หมายเลขคำสั่งซื้อ</th>
                                        <th scope="col">ผู้ทำรายการ</th>
                                        <th scope="col">จำนวนเงิน</th>
                                        <th scope="col">เวลา</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = $result_out->fetch_assoc()) {
                                        $row['price'] = number_format((float) $row['price'], 2, '.', '');
                                        echo "<tr><th scope='row' >" . $row['id_order'] . "</th>";
                                        echo "<th scope='row' >" . $row['shop_name'] . "</th>";
                                        echo "<td> " . $row['price'] . " </td>";
                                        echo "<td> " . $row['time'] . " </td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Table End -->


            <!-- Footer Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="bg-secondary rounded-top p-4">
                </div>
            </div>
            <!-- Footer End -->
        </div>
        <!-- Content End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
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