<?php
// Start session
session_start();

// Connection credentials
$servername = "localhost";
$username = "anton";
$password = "anton";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_user = $_POST['username'];
    $login_pass = $_POST['password'];

    // Connect to the default database (e.g., 'login_system' or any general DB)
    $conn = new mysqli($servername, $username, $password, "user_management");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch user from the `users` table
    $query = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $login_user, $login_pass);
    $stmt->execute();
    $result = $stmt->get_result();

    // If user exists
    if ($result->num_rows > 0) {
        $_SESSION['username'] = $login_user; // Save username in session
        header("Location: ../main.php");   // Redirect to the dashboard
        exit();
    } else {
        $error = "Invalid username or password!";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
    <form method="POST" action="">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required><br><br>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
