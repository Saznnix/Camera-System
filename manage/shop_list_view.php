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

if ($perm == 1 || $perm == 0) {

} else {
    header("Location: ../signin.php");
    exit();
}

if (isset($_GET['id_user'])) {

    include('../config.php');
    include('../sector/sidebar.php');

    $id_user = $_GET['id_user'];

    $sql_shop_active = "SELECT * FROM shop WHERE status = 1 AND id_user = $id_user";
    $result_shop_active = $conn->query($sql_shop_active);
    $num_rows_shop = mysqli_num_rows($result_shop_active);

    $sql_shop_nonactive = "SELECT * FROM shop WHERE status = 0 AND id_user = $id_user";
    $result_shop_nonactive = $conn->query($sql_shop_nonactive);
    $num_rows_nonshop = mysqli_num_rows($result_shop_nonactive);


    $conn->close();
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
    <link href="../css/bootstrap.min.css?v=3" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../css/style.css?v=1" rel="stylesheet">
</head>

<body>
    <style>
        #search-results {
            margin-top: 50px;
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
            margin-top: 50px;
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
                            <?php echo $rank; ?>
                        </span>
                    </div>
                </div>
                <div class="navbar-nav w-100">
                    <a href="#" class="nav-item nav-link active"><i
                            class="fa fa-tachometer-alt me-2"></i>เลือกร้านค้า</a>
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
                            <a href="../logout/logout.php" class="dropdown-item">Log Out</a>
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
                            <i class="fa fa-users fa-3x text-primary"></i>
                            <div class="ms-3">
                                <p class="mb-2">จำนวนร้านค้าที่เป็นเจ้าของ</p>
                                <h6 class="mb-0">
                                    <h6 class="mb-0">
                                        <?php echo $num_rows_shop; ?>
                                    </h6>
                                </h6>
                            </div>
                        </div>
                    </div>



                </div>
            </div>
            <!-- Sale & Revenue End -->



            <!-- Recent Sales Start -->

            <!-- ////////////////////////////////// -->
            <div class="container-fluid pt-4 px-4">
                <div class="bg-secondary rounded h-100 p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">ร้านค้า</h6>
                        
                       
                    </div>

                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link text-success active" id="nav-home-tab" data-bs-toggle="tab"
                                data-bs-target="#nav-home1" type="button" role="tab" aria-controls="nav-home"
                                aria-selected="true">ACTIVE</button>
                            <button class="nav-link text-danger" id="nav-profile-tab" data-bs-toggle="tab"
                                data-bs-target="#nav-profile1" type="button" role="tab" aria-controls="nav-profile"
                                aria-selected="false">NON ACTIVE</button>
                        </div>
                    </nav>
                    <div class="tab-content pt-3" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-home1" role="tabpanel"
                            aria-labelledby="nav-home-tab">
                            <div class="table-responsive">
                                <table class="table text-start align-middle table-bordered table-hover mb-0">
                                    <thead>
                                        <tr class="text-white">
                                            <th scope="col">ID</th>
                                            <th scope="col">USER เจ้าของ</th>
                                            <th scope="col">ชื่อร้านค้า</th>
                                            <th scope="col">สถานะ</th>
                                            <th scope="col" class="text-primary">เลือก</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        while ($row = mysqli_fetch_assoc($result_shop_active)) {
                                            echo "<tr><td>" . $row['id'] . "</td> ";
                                            echo "<td>" . $_SESSION['username'] . "</td> ";
                                            echo "<td>" . $row['shop_name'] . "</td> ";
                                            if ($row['status'] == 1) {
                                                echo "<td class='text-success'>ACTIVE</td> ";
                                            } else {
                                                echo "<td class='text-danger'>NON ACTIVE</td> ";
                                            }
                                            echo "<td><a class='btn btn-sm btn-primary' href='../shop.php?shop_name=" . $row['shop_name'] . "'>เลือก</a></td> </tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-profile1" role="tabpanel" aria-labelledby="nav-profile-tab">
                            <div class="table-responsive">
                                <table class="table text-start align-middle table-bordered table-hover mb-0">
                                    <thead>
                                        <tr class="text-white">
                                            <th scope="col">ID</th>
                                            <th scope="col">USER เจ้าของ</th>
                                            <th scope="col">ชื่อร้านค้า</th>
                                            <th scope="col">สถานะ</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        while ($row = mysqli_fetch_assoc($result_shop_nonactive)) {
                                            echo "<tr><td>" . $row['id'] . "</td> ";
                                            echo "<td>" . $_SESSION['username'] . "</td> ";
                                            echo "<td>" . $row['shop_name'] . "</td> ";
                                            if ($row['status'] == 1) {
                                                echo "<td class='text-success'>ACTIVE</td> ";
                                            } else {
                                                echo "<td class='text-danger'>NON ACTIVE</td> ";
                                            }
                                            echo "</tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
            if (query.trim() === '') {
                // If query is empty, close dropdown
                document.getElementById('search-results').style.display = 'none';
                return;
            }

            // Use AJAX to fetch data from the server
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
                resultsContainer.style.display = 'none'; // If no data, hide dropdown
                return;
            }

            const ul = document.createElement('ul');
            results.forEach(result => {
                const li = document.createElement('li');
                li.textContent = `${result.shop_name}`;
                li.addEventListener('click', () => {
                    // Modify this part according to your data structure
                    document.getElementById('search-input').value = result.shop_name;
                    resultsContainer.style.display = 'none';
                });
                ul.appendChild(li);
            });
            resultsContainer.appendChild(ul);
            resultsContainer.style.display = 'block';
        }

        function showResults1(query) {
            if (query.trim() === '') {
                // If query is empty, close dropdown
                document.getElementById('search-results1').style.display = 'none';
                return;
            }

            // Use AJAX to fetch data from the server
            $.ajax({
                url: 'select/select_user.php',
                type: 'GET',
                data: { term: query },
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
                li.textContent = `${result.user} - ${result.fname} ${result.lname}`;
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
    <script src="../js/main.js"></script>
</body>

</html>