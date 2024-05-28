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
$perm = $_SESSION["perm"];

if ($perm == 0 || $perm == 1) {
    //header("Location: signin.php");
} else {
    header("Location: signin.php");
}

include('sector/sidebar.php');

include('config.php');

$shop_name = $_GET['shop_name'];

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
        <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-secondary navbar-dark">
                <a href="index.php" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary"><i class="fa fa-user-edit me-2"></i>RIETC</h3>
                </a>
                <div class="d-flex align-items-center ms-4 mb-4">
                    <div class="position-relative">
                        <!--<img class="rounded-circle"
                            src="https://i.pinimg.com/originals/1c/ec/60/1cec60b076ed3e42a0a253548370a353.gif" alt=""
                            style="width: 40px; height: 40px;">-->
                        <div
                            class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1">
                        </div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0">
                            <?php echo $_SESSION['username'] ?>
                        </h6>
                        <span><?php echo $rank; ?></span>
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
<a href="#" class="nav-link dropdown-toggle active" data-bs-toggle="dropdown"><i
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

                    }
                    else if ($perm == 1) {
                        echo '<a href="form.php" class="nav-item nav-link active"><i class="fa fa-keyboard me-2"></i>ข้อมูลธุรกรรม</a>';

                    }  else {
                        echo 'An error occurred.';
                    }

                    ?>
                </div>
        </div>
        </nav>
    </div>
    <!-- Sidebar End -->


    <!-- Content Start -->
    <div class="content">
        <!-- Navbar Start -->
        <nav class="navbar navbar-expand bg-secondary navbar-dark sticky-top px-4 py-0">
            <a href="index.html" class="navbar-brand d-flex d-lg-none me-4">
                <h2 class="text-primary mb-0"><i class="fa fa-user-edit"></i></h2>
            </a>
            <a href="#" class="sidebar-toggler flex-shrink-0">
                <i class="fa fa-bars"></i>
            </a>
            <form method="post" action="" class="d-none d-md-flex ms-4">
                <h5>
                    <?php echo $shop_name ?>
                </h5>
            </form>
            <div class="navbar-nav align-items-center ms-auto">
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <!--<img class="rounded-circle me-lg-2"
                                src="https://i.pinimg.com/originals/1c/ec/60/1cec60b076ed3e42a0a253548370a353.gif"
                                alt="" style="width: 40px; height: 40px;">-->
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
                            <p class="mb-2">เงินเข้าระบบวันนี้</p>
                            <h6 class="mb-0">
                                <h6 class="mb-0">
                                    <?php echo $totalPrice0_today; ?>
                                </h6>
                            </h6>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
                        <i class="fa fa-chart-line fa-3x text-primary"></i>
                        <div class="ms-3">
                            <p class="mb-2">เงินเข้าระบบย้อนหลัง30วัน</p>
                            <h6 class="mb-0">
                                <?php echo $totalPrice0_last30days; ?>
                            </h6>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
                        <i class="fa fa-chart-line fa-3x text-primary"></i>
                        <div class="ms-3">
                            <p class="mb-2">เงินเข้าระบบทั้งหมด</p>
                            <h6 class="mb-0">
                                <?php echo $totalPrice0_last30days; ?>
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Sale & Revenue End -->



        <!-- Recent Sales Start -->
        <div class="container-fluid pt-4 px-4">
            <div class="bg-secondary text-center rounded p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="mb-0">เงินเข้าระบบ</h6>
                    <a href="showall.php?shop_in=1&&shop_name=<?php echo $shop_name ?>">Show All</a>
                </div>
                <div class="table-responsive">
                    <table class="table text-start align-middle table-bordered table-hover mb-0">
                        <thead>
                            <tr class="text-white">
                                <th scope="col">วันที่</th>
                                <th scope="col">หมายเลขสั่งซืื้อ</th>
                                <th scope="col">รายการ</th>
                                <th scope="col">จำนวนเงิน</th>
                                <th scope="col">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
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
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Recent Sales End -->

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

    <script>
        function showResults(query) {
            // ใช้ AJAX เพื่อดึงข้อมูลจาก server
            $.ajax({
                url: 'select/select_shop.php',
                type: 'GET',
                data: { term: query },
                dataType: 'json',
                success: function (data) {
                    displayResults(data);
                }
            });
        }

        function displayResults(results) {
            const resultsContainer = document.getElementById('search-results');
            resultsContainer.innerHTML = '';

            if (results.length === 0) {
                resultsContainer.innerHTML = '<p>No results found</p>';
            } else {
                const ul = document.createElement('ul');
                results.forEach(result => {
                    const li = document.createElement('li');
                    li.textContent = result;
                    li.addEventListener('click', () => {
                        document.getElementById('search-input').value = result;
                        resultsContainer.style.display = 'none';
                    });
                    ul.appendChild(li);
                });
                resultsContainer.appendChild(ul);
            }
            resultsContainer.style.display = 'block';
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function (event) {
            const container = document.getElementById('search-container');
            if (!container.contains(event.target)) {
                document.getElementById('search-results').style.display = 'none';
            }
        });
    </script>

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