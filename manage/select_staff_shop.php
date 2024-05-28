<?php
header('Content-Type: application/json; charset=utf-8');
include('../config.php');

// Validate and sanitize input
$searchTerm = isset($_GET['term']) ? $_GET['term'] : '';
$searchTerm = mysqli_real_escape_string($conn, $searchTerm);

// Use prepared statements for the query
$sql = "SELECT * FROM user WHERE (user LIKE ? OR fname LIKE ? OR lname LIKE ?) AND perm = 1";

$stmt = $conn->prepare($sql);
$searchTerm = "%" . $searchTerm . "%";
$stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
if ($stmt->errno) {
    die("Execute failed: " . $stmt->error);
}

$result = $stmt->get_result();

// Handle errors
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Fetch data and store in an array of objects
$data = array();
while ($row = $result->fetch_assoc()) {
    $userObject = array(
        'id_user' => $row['id_user'],
        'user' => $row['user'],
        'fname' => $row['fname'],
        'lname' => $row['lname']
    );
    $data[] = $userObject;
}

// Send data back in JSON format
echo json_encode($data);

// Close prepared statement and MySQL connection
$stmt->close();
$conn->close();


?>