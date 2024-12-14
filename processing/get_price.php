<?php
include 'login/auth.php'; // Include authentication check

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the service type from the AJAX request
$type = $_POST['type'];

// Fetch the price for the selected service
$sql = "SELECT price FROM service WHERE type = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $type);
$stmt->execute();
$stmt->bind_result($price);
$stmt->fetch();
$stmt->close();
$conn->close();

// Return the price as a response
echo $price;
?>
