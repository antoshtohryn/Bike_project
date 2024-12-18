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

    // Connect to the default database
    $conn = new mysqli($servername, $username, $password, "user_management");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch user from the `users` table by username
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $login_user);
    $stmt->execute();
    $result = $stmt->get_result();

    // If user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Check if the entered password matches the hashed password
        if ($user && password_verify($login_pass, $user['password'])) {
            $_SESSION['login_date'] = date('Y-m-d');
            // Check if the user is the admin
            if ($login_user === 'bikeshop') {
                // Redirect admin to calendar
                $_SESSION['username'] = $login_user;
                header("Location: ../bikeshop/calendar.php");
                exit();
            } else {
                // Redirect clients to client page
                $_SESSION['username'] = $login_user;
                header("Location: ../client/client.php");
                exit();
            }
        } else {
            $error = "Invalid username or password!";
        }
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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

        h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 20px;
            text-align: center;
        }

        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }


        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        button[type="submit"], a.button-link {
            display: block;
            text-align: center;
            width: 100%;
            padding: 12px;
            margin-bottom: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            font-size: 16px;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover, a.button-link:hover {
            background-color: #0056b3;
        }

        .options {
            text-align: center;
            margin-top: 15px;
        }

        .options a {
            text-decoration: none;
            color: #007bff;
        }

        .options a:hover {
            text-decoration: underline;
        }

        .error {
            color: red;
            font-size: 14px;
            text-align: center;
            margin-bottom: 15px;
        }

        @media (max-width: 480px) {
    .container {
        padding: 20px;
        width: 90%;
    }

    h2 {
        font-size: 22px;
    }

    button[type="submit"] {
        font-size: 16px;
    }
}
    </style>
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required><br>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required><br>
            <button type="submit">Login</button>
        </form>

        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

        <div class="options">
            <a href="register.php">Register</a><br><br>
            <a href="change_password.php">Change Password</a>
        </div>
    </div>
</body>
</html>
