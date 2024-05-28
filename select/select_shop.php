<?php
header('Content-Type: application/json; charset=utf-8');
include('../config.php');

// Validate and sanitize input
$searchTerm = isset($_GET['term']) ? $_GET['term'] : '';
$searchTerm = mysqli_real_escape_string($conn, $searchTerm);

// Use prepared statements for the query
$sql = "SELECT * FROM shop WHERE shop_name LIKE ? ";
$stmt = $conn->prepare($sql);
$searchTerm = "%" . $searchTerm . "%";
$stmt->bind_param("s", $searchTerm);

// Execute the statement
$stmt->execute();

// Check for errors in execution
if ($stmt->errno) {
    die("Execute failed: " . $stmt->error);
}

// Get the result set
$result = $stmt->get_result();

// Handle errors
if (!$result) {
    die("Query failed: " . $conn->error);
}

// Fetch data and store in an array of objects
$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row; // Keep the entire row, not just 'shop_name'
}

// Send data back in JSON format
echo json_encode($data);

// Close prepared statement and MySQL connection
$stmt->close();
$conn->close();
?>
