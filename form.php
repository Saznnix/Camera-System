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

if ($perm == 0 || $perm == 3) {
    //header("Location: signin.php");
} else {
    header("Location: signin.php");
}

include('sector/sidebar.php');

include('config.php');

$stmt = $conn->prepare("SELECT * FROM user WHERE perm = 2 ORDER BY id DESC");
$stmt->execute();
$result = $stmt->get_result();




$stmt_shop = $conn->prepare("SELECT * FROM shop");
$stmt_shop->execute();
$result_shop = $stmt_shop->get_result();


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
                <a href="index.php" class="navbar-brand mx-4 mb-3">
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
                    <?php

                    if ($perm == 0) {
                        echo '<a href="index.php" class="nav-item nav-link"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>';
                        echo '<a href="form.php" class="nav-item nav-link active"><i class="fa fa-keyboard me-2"></i>เติมเงิน</a>';

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

                    } else {
                        echo 'An error occurred.';
                    }

                    ?>
                    <!--<a href="index.php" class="nav-item nav-link"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
                    <a href="form.php" class="nav-item nav-link active"><i class="fa fa-keyboard me-2"></i>เติมเงิน</a>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i
                                class="fa fa-laptop me-2"></i>ร้านค้า</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <?php
                            /*
                            while ($row = $result_shop->fetch_assoc()) {
                                //$shop_name[] = $row['shop_name'];
                                echo '<a href="shop.php?shop_name=' . $row['shop_name'] . '" class="dropdown-item">' . $row['shop_name'] . '</a>';
                            }*/
                            ?>
                        </div>
                    </div>-->


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
                            <h6 class="mb-4">ค้นหาด้วยข้อมูลนักศึกษา</h6>
                            <form action="payment.php" method="get">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">ค้นหา ด้วยรหัสนักศึกษา ชื่อ
                                        นามสกุล.</label>
                                    <input type="text" class="form-control" id="search-input"
                                        placeholder="รหัสนักศึกษา ชื่อ นามสกุล..." oninput="showResults(this.value)"
                                        name="id_user" autocomplete="off" required>
                                    <div id="search-results"></div>
                                </div>
                                <button type="submit" class="btn btn-primary">ค้นหา</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-secondary rounded h-100 p-4">
                            <h6 class="mb-4">ค้นหาด้วยบัตร RFID</h6>
                            <form action="payment.php" method="get">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">ค้นหาด้วยบัตร RFID</label>
                                    <input type="text" class="form-control" id="search-input" placeholder="บัตร RFID"
                                        name="rfid" autocomplete="off" required>
                                    <div id="search-results"></div>
                                </div>
                                <button type="submit" class="btn btn-primary">ค้นหา</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-secondary rounded h-100 p-4">
                            <h6 class="mb-4">เพิ่ม RFID</h6>
                            <form action="insert_log/insert_rfid.php" method="post">
                                <div class="row mb-3">
                                    <div class="col-sm-10">
                                        <label for="exampleInputEmail1" class="form-label">ค้นหา ด้วยรหัสนักศึกษา ชื่อ
                                            นามสกุล.</label>
                                        <input type="text" class="form-control" id="search-input1"
                                            placeholder="รหัสนักศึกษา ชื่อ นามสกุล..."
                                            oninput="showResults1(this.value)" name="id_user" autocomplete="off"
                                            required>
                                        <div id="search-results1"></div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="inputPassword3" name="rfid"
                                            placeholder="บัตร RFID" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">ตกลง</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>



            <div class="container-fluid pt-4 px-4">
                <div class="bg-secondary text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">รายชื่อนักศึกษา</h6>
                        <a href="showall.php?user=1">Show All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table text-start align-middle table-bordered table-hover mb-0">
                            <thead>
                                <tr class="text-white">
                                    <th scope="col">รหัส นศ.</th>
                                    <th scope="col">ชื่อ</th>
                                    <th scope="col">นามสกุล</th>
                                    <th scope="col">เติมเงิน</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                if ($result->num_rows > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr><td>" . $row['user'] . "</td> ";
                                        echo "<td>" . $row['fname'] . "</td> ";
                                        echo "<td>" . $row['lname'] . "</td> ";
                                        echo "<td> <a class='btn btn-sm btn-primary' href='payment.php?id_user=" . $row['id_user'] . "'> เติมเงิน </a></td> </tr> ";
                                    }
                                } else {
                                    echo "<tr><td>An error occurred.</td> ";
                                    echo "<td>An error occurred.</td> ";
                                    echo "<td>An error occurred.</td> ";
                                    echo "</tr> ";
                                }


                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Form End -->


            <!-- Footer Start -->

            <!-- Footer End -->
        </div>
        <!-- Content End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <script>
        function showResults(query) {

            if (query.trim() === '') {
                // ถ้า query ว่าง ให้ปิด dropdown
                document.getElementById('search-results').style.display = 'none';
                return;
            }
            // ใช้ AJAX เพื่อดึงข้อมูลจาก server
            $.ajax({
                url: 'select/select_user.php',
                type: 'GET',
                data: {
                    term: query
                },
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
                resultsContainer.style.display = 'none'; // ถ้าไม่มีข้อมูลให้ซ่อน dropdown
                return;
            }

            const ul = document.createElement('ul');
            results.forEach(result => {
                const li = document.createElement('li');
                li.textContent = `${result.user} - ${result.fname} ${result.lname}`;
                li.addEventListener('click', () => {
                    document.getElementById('search-input').value = result.id_user;
                    resultsContainer.style.display = 'none';
                });
                ul.appendChild(li);
            });
            resultsContainer.appendChild(ul);
            resultsContainer.style.display = 'block';
        }




        function showResults1(query) {

            if (query.trim() === '') {
                // ถ้า query ว่าง ให้ปิด dropdown
                document.getElementById('search-results1').style.display = 'none';
                return;
            }
            // ใช้ AJAX เพื่อดึงข้อมูลจาก server
            $.ajax({
                url: 'select/select_user.php',
                type: 'GET',
                data: {
                    term: query
                },
                dataType: 'json',
                success: function (data) {
                    displayResults1(data);
                }
            });
        }

        function displayResults1(results) {
            const resultsContainer = document.getElementById('search-results1');
            resultsContainer.innerHTML = '';

            if (results.length === 0) {
                resultsContainer.style.display = 'none'; // ถ้าไม่มีข้อมูลให้ซ่อน dropdown
                return;
            }

            const ul = document.createElement('ul');
            results.forEach(result => {
                const li = document.createElement('li');
                li.textContent = `${result.id_user} - ${result.fname} ${result.lname}`;
                li.addEventListener('click', () => {
                    document.getElementById('search-input1').value = result.id_user;
                    resultsContainer.style.display = 'none';
                });
                ul.appendChild(li);
            });
            resultsContainer.appendChild(ul);
            resultsContainer.style.display = 'block';
        }


        // Close dropdown when clicking outside
        document.addEventListener('click', function (event) {
            const container = document.getElementById('search-container');
            if (!container.contains(event.target)) {
                document.getElementById('search-results').style.display = 'none';
                document.getElementById('search-results1').style.display = 'none';
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