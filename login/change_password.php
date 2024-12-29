<?php
// Start session
session_start();

// Database credentials
$servername = "localhost";
$username = "anton";
$password = "anton";
$dbname = "user_management";

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize error/success messages
$error = "";
$success = "";

// Handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate that new passwords match
    if ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } else {
        // Fetch user record from the database
        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            // Verify the old password
            if (password_verify($old_password, $row['password'])) {
                // Hash the new password
                $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);

                // Update the password in the database
                $update_query = "UPDATE users SET password = ? WHERE username = ?";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param("ss", $new_password_hashed, $user);

                if ($update_stmt->execute()) {
                    // Success message and redirect
                    $success = "Password updated successfully! Redirecting to login page...";
                    echo "<script>
                            setTimeout(function() {
                                window.location.href = 'login.php';
                            }, 3000); // Redirect after 3 seconds
                          </script>";
                } else {
                    $error = "Error updating password. Please try again.";
                }
                $update_stmt->close();
            } else {
                $error = "Old password is incorrect.";
            }
        } else {
            $error = "User not found.";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <style>
        /* Basic Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Body Styles */
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

/* Heading Styles */
h2 {
    color: #333;
    font-size: 28px;
    margin-bottom: 20px;
    text-align: center;
}

/* Container Styles */
.container {
    background-color: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
}

/* Error and Success Message Styling */
.error, .success {
    font-size: 14px;
    text-align: center;
    margin-bottom: 15px;
}

.error {
    color: #f44336;
}

.success {
    color: #4CAF50;
}

/* Form Label Styling */
label {
    display: block;
    font-size: 16px;
    color: #555;
    margin-bottom: 8px;
}

/* Input Fields Styling */
input[type="text"], input[type="password"] {
    width: 100%;
    padding: 12px;
    margin: 10px 0 20px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
}

input[type="text"]:focus, input[type="password"]:focus {
    border-color: #4CAF50;
    outline: none;
}

/* Submit Button Styling */
button[type="submit"] {
    display: block;
    width: 100%;
    padding: 12px;
    background-color: #007bff;
    color: white;
    border: none;
    font-size: 16px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button[type="submit"]:hover {
    background-color: #0056b3;
}

/* Responsive Design for Smaller Screens */
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Change Password</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="old_password">Old Password:</label>
            <input type="password" id="old_password" name="old_password" required>

            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" 
                required 
                minlength="8"
                pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}" 
                title="Password must be at least 8 characters, include at least one uppercase letter, one lowercase letter, one number, and one special character.">
            <br>

            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit">Change Password</button>
        </form>

        <div class="options">
            <a href="login.php">Login page</a><br><br>
        </div>
    </div>
</body>
</html>
