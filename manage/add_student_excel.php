<?php
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$uploadOk = 1;

if (isset($_FILES["fileToUpload"])) {
    $target_dir = "../uploads/";
    $originalFileName = basename($_FILES["fileToUpload"]["name"]);
    $extension = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
    $newFileName = uniqid() . '.' . $extension;
    $target_file = $target_dir . $newFileName;

    // Check if file is an Excel file
    if ($extension != "xlsx" && $extension != "xls") {
        echo '<script>alert("Sorry, only XLSX, XLS files are allowed."); window.location.href="../manage_students.php";</script>';
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            $spreadsheet = IOFactory::load($target_file);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            include('../config.php');

            $tableName = "user";
            $columnNames = array('id_user', 'user', 'pass', 'perm', 'fname', 'lname', 'status');

            // Array to store SQL queries
            $sqlQueries = array();

            foreach ($sheetData as $row) {
                // Check if the first column is not empty and not equal to 1
                if (!empty($row['A']) && $row['A'] != "1") {
                    // Assigning values to variables
                    $user = (string) $row['A'];
                    $fname = $row['B'];
                    $lname = $row['C'];
                    $password = substr($user, -6);
                    $hashed_pass = password_hash($password, PASSWORD_DEFAULT);

                    // Check if user already exists in the database
                    $checkUserSql = "SELECT * FROM $tableName WHERE user = '$user'";
                    $result = $conn->query($checkUserSql);

                    if ($result->num_rows == 0) {
                        // If user doesn't exist, add INSERT query to the SQL queries array
                        $id_user = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
                        $sqlQueries[] = "INSERT INTO $tableName (" . implode(',', $columnNames) . ") VALUES ('$id_user', '" . $user . "', '$hashed_pass', 2, '$fname', '$lname', 1)";
                    }
                }
            }

            // Execute all SQL queries
            if (!empty($sqlQueries)) {
                $success = true;
                $errorMessages = array();

                foreach ($sqlQueries as $sqlQuery) {
                    if ($conn->query($sqlQuery) !== TRUE) {
                        $success = false;
                        $errorMessages[] = "Error: " . $conn->error;
                    }
                }

                if ($success) {
                    echo '<script>alert("บันทึกสำเร็จ"); window.location.href="../manage_students.php";</script>';
                } else {
                    echo '<script>alert("บันทึกไม่สำเร็จ"); window.location.href="../manage_students.php";</script>';
                    // แสดงข้อผิดพลาดทั้งหมดที่เกิดขึ้น
                    foreach ($errorMessages as $errorMessage) {
                        echo "Error: " . $errorMessage . "<br>";
                    }
                }
            }

            $conn->close();

            echo "The file " . htmlspecialchars($originalFileName) . " has been uploaded and data has been inserted into MySQL.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>