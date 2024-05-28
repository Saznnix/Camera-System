<?php
header('Content-Type: application/json; charset=utf-8');
include('../config.php');

// Validate and sanitize input
$searchTerm = isset($_GET['term']) ? $_GET['term'] : '';
$searchTerm = mysqli_real_escape_string($conn, $searchTerm);

// Use prepared statements for the query
$sql = "SELECT * FROM rfid WHERE rfid LIKE ?";
$stmt = $conn->prepare($sql);
$searchTerm = "%" . $searchTerm . "%";
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

// Handle errors
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Fetch data and store in an array
$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row['rfid'];
}

// Send data back in JSON format
echo json_encode($data);

// Close prepared statement and MySQL connection
$stmt->close();
$conn->close();
?>
