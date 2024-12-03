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
        header("Location: ../main.php");   // Redirect to the main page
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
    <style>
        /* General body styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Title of the login form */
        h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 30px;
            text-align: center;
        }

        /* Main container for the login form */
        .login {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        /* Style for input fields */
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box; /* Ensures padding doesn't affect width */
        }

        /* Style for submit button */
        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            box-sizing: border-box; /* Ensures padding doesn't affect width */
        }

        /* Button hover effect */
        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        /* Error message style */
        .error {
            color: red;
            font-size: 14px;
            text-align: center;
            margin-bottom: 15px;
        }

        /* Responsive styles */
        @media (max-width: 480px) {
            .login {
                padding: 20px;
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <div class="login">
    <h1>Login</h1>
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required><br><br>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required><br><br>
            <button type="submit">Login</button>
        </form>

        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
    </div>
    
</body>
</html>
