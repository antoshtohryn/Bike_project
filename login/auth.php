<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login/login.php"); // Redirect to login page if not logged in
    exit();
}

// Get the current date
$currentDate = date('Y-m-d');

// Compare session login date with current date
if (isset($_SESSION['login_date']) && $_SESSION['login_date'] !== $currentDate) {
    // If the dates don't match, destroy the session and redirect to the login page
    session_destroy();
    header("Location: login/login.php"); // Redirect to login page
    exit();
}

// Connection credentials
$servername = "localhost";
$db_username = "anton";
$db_password = "anton";
$user_database = "bikeshop"; // Database name is the username

// Connect to the user's database
$conn = new mysqli($servername, $db_username, $db_password, $user_database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
