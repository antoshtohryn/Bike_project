<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login/login.php"); // Redirect to login page if not logged in
    exit();
}

// Connection credentials
$servername = "localhost";
$db_username = "anton";
$db_password = "anton";
$user_database = $_SESSION['username']; // Database name is the username

// Connect to the user's database
$conn = new mysqli($servername, $db_username, $db_password, $user_database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
