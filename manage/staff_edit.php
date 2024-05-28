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


include('../sector/sidebar.php');
include('../config.php');


if (isset($_GET['id_user']) && is_numeric($_GET['id_user'])) {

    $id_user = $_GET['id_user'];


    // Prepare and bind the parameter to avoid SQL injection
    $stmt = $conn->prepare("SELECT * FROM user WHERE id_user = ?");
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result_user = $stmt->get_result();
    $row_user = mysqli_fetch_assoc($result_user);

    // Check if the user query was successful
    if ($stmt->affected_rows > 0) {
        // Proceed with further operations
    } else {
        // Handle error if user query was not successful
        // For example, redirect to an error page or display an error message
    }

    // Prepare and bind the parameter to avoid SQL injection
    $stmts_shop = $conn->prepare("SELECT * FROM shop WHERE id_user = ?");
    $stmts_shop->bind_param("i", $id_user);
    $stmts_shop->execute();
    $result_shop = $stmts_shop->get_result();

    $maker = $row_user['user'];
    $stmts_maker = $conn->prepare("SELECT * FROM data_log WHERE maker = ? AND mode = 0 ");
    $stmts_maker->bind_param("s", $maker);
    $stmts_maker->execute();
    $result_maker = $stmts_maker->get_result();

    // Check if the shop query was successful


} else {
    header("Location: ../signin.php");
    exit();
}




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
    <link href="../img/favicon.ico" rel="icon">

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
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../css/style.css" rel="stylesheet">
</head>

<body>
    <style>
        #search-results {
            position: absolute;
            width: 200px;
            background-color: #fff;
            border: 1px solid #ccc;
            display: none;
        }

        #search-results ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        #search-results li {
            padding: 8px;
            cursor: pointer;
        }

        #search-results1 {
            position: absolute;
            width: 200px;
            background-color: #fff;
            border: 1px solid #ccc;
            display: none;
        }

        #search-results1 ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        #search-results1 li {
            padding: 8px;
            cursor: pointer;
        }
    </style>

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
                <a href="../index.php" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary"><i class="fa fa-user-edit me-2"></i>
                        <?php echo $title; ?>
                    </h3>
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
                        <span>
                            <?php echo $rank ?>
                        </span>
                    </div>
                </div>
                <div class="navbar-nav w-100">
                    <a href="../manage_staff.php" class="nav-item nav-link active"><i
                            class="fa fa-address-book me-2"></i>จัดการบัญชีพนักงาน</a>';
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

            <!-- Form Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-secondary rounded h-100 p-4">
                            <h6 class="mb-4">แก้ไขข้อมูลบัญชี</h6>
                            <form action="update_staff_edit.php?id_user=<?php echo $id_user; ?>" method="post">
                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form-label">ชื่อ</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="fname"
                                            value="<?php echo $row_user['fname'] ?>" name="fname">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="inputPassword3" class="col-sm-2 col-form-label">นามสกุล</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="lname" name="lname"
                                            value="<?php echo $row_user['lname'] ?>">
                                    </div>
                                </div>
                                <fieldset class=" row mb-3">
                                    <legend class="col-form-label col-sm-2 pt-0">สถานะ</legend>
                                    <div class="col-sm-10">
                                        <?php
                                        if ($row_user['status'] == 1) {
                                            echo '<div class="form-check">
                                            <input class="form-check-input" type="radio" name="gridRadios"
                                                id="gridRadios1" value="1" checked>
                                            <label class="form-check-label" for="gridRadios1">
                                                active
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gridRadios"
                                                id="gridRadios2" value="0">
                                            <label class="form-check-label" for="gridRadios2">
                                                non active
                                            </label>
                                        </div>
                                    </div>';
                                        } else {

                                            echo '<div class="form-check">
                                            <input class="form-check-input" type="radio" name="gridRadios"
                                                id="gridRadios1" value="1" >
                                            <label class="form-check-label" for="gridRadios1">
                                                active
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gridRadios"
                                                id="gridRadios2" value="0" checked>
                                            <label class="form-check-label" for="gridRadios2">
                                                non active
                                            </label>
                                        </div>
                                    </div>';
                                        }
                                        ?>
                                </fieldset>

                                <button type="submit" class="btn btn-primary">บันทึก</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-secondary rounded h-100 p-4">
                            <h6 class="mb-4">เปลี่ยนรหัสผ่าน</h6>
                            <form action="admin_password_edit.php?id_user=<?php echo $id_user ?>" method="post">
                                <div class="row mb-3">
                                    <div class="col-sm-10">
                                        <label class="form-label">รหัสผ่านเก่า</label>
                                        <input type="password" class="form-control" id="search-input1"
                                            placeholder="รหัสผ่านเก่า" name="pass" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-10">
                                        <label class="form-label">รหัสผ่านใหม่</label>
                                        <input type="password" class="form-control" name="newpass"
                                            placeholder="รหัสผ่านใหม่" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">บันทึก</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            if ($stmts_shop->affected_rows > 0) {
                echo '  <div class="container-fluid pt-4 px-4">
                <div class="bg-secondary text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">ร้านค้า</h6>
                        <a href="showall.php?money_in=1">Show All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table text-start align-middle table-bordered table-hover mb-0">
                            <thead>
                                <tr class="text-white">
                                    <th scope="col">ID เจ้าของร้าน</th>
                                    <th scope="col">ชื่อร้านค้า</th>
                                    <th scope="col">สภานะ</th>
                                    <th scope="col">วันที่ลงทะเบียน</th>
                                    <th scope="col" class="text-primary">EDIT</th>
                                </tr>
                            </thead>
                            <tbody>';
                // Proceed with further operations
                while ($row = mysqli_fetch_assoc($result_shop)) {

                    echo "<tr><td>" . $row['id_user'] . "</td> ";
                    echo "<td>" . $row['shop_name'] . "</td> ";
                    if ($row['status'] == 1) {
                        echo "<td class='text-success' > ACTIVE </td> ";
                    } else {
                        echo "<td class='text-danger'> NON ACTIVE </td> ";
                    }
                    echo "<td>" . $row['time'] . "</td> ";
                    echo "<td><a class='btn btn-sm btn-primary' href='shop_edit.php?id=" . $row['id'] . "'>EDIT</a></td>";
                    echo '</tr>';
                }
                echo '</tbody>
                        </table>
                    </div>
                </div>
            </div>';
            }
            if ($stmts_maker->affected_rows > 0) {
                echo '  <div class="container-fluid pt-4 px-4">
                <div class="bg-secondary text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">ร้านค้า</h6>
                        <a href="showall.php?money_in=1">Show All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table text-start align-middle table-bordered table-hover mb-0">
                            <thead>
                                <tr class="text-white">
                                    <th scope="col">หมายเลขทำรายการ</th>
                                    <th scope="col">ชื่อรายการ</th>
                                    <th scope="col">ผู้รับ</th>
                                    <th scope="col">จำนวนเงิน</th>
                                    
                                    <th scope="col">วันที่</th>
                                </tr>
                            </thead>
                            <tbody>';
                // Proceed with further operations
                while ($row = mysqli_fetch_assoc($result_maker)) {

                    echo "<tr><td>" . $row['id_order'] . "</td> ";
                    echo "<td>" . $row['shop_name'] . "</td> ";
                    echo "<td>" . $row['id_user'] . "</td> ";
                    echo "<td>" . $row['price'] . "</td> ";

                    echo "<td>" . $row['time'] . "</td> ";
                    echo '</tr>';
                }
                echo '</tbody>
                        </table>
                    </div>
                </div>
            </div>';
            }
            ?>
            <!-- Form End -->


            <!-- Footer Start -->

            <!-- Footer End -->
        </div>
        <!-- Content End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/chart/chart.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="../lib/tempusdominus/js/moment.min.js"></script>
    <script src="../lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="../lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="../js/main.js"></script>
</body>

</html>