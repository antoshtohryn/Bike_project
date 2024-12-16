<?php
// Database connection details for the 'user_management' database
$host = 'localhost'; 
$dbname = 'user_management'; 
$username = 'anton'; // Replace with your DB username
$password = 'anton'; // Replace with your DB password

$error = ""; // Variable to store error message

try {
    // Create a PDO connection to the 'user_management' database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $login_user = trim($_POST['username']);
        $login_pass = trim($_POST['password']);

        // Check if the username already exists in the 'users' table
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':username', $login_user);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            // Username already exists
            $error = "Username is already taken. Please choose a different username.";
        } else {
            // Hash the password
            $hashed_password = password_hash($login_pass, PASSWORD_DEFAULT);

            // Insert the user into the 'users' table
            $insert_query = "INSERT INTO users (username, password) VALUES (:username, :password)";
            $insert_stmt = $pdo->prepare($insert_query);
            $insert_stmt->bindParam(':username', $login_user);
            $insert_stmt->bindParam(':password', $hashed_password);
            $insert_stmt->execute();

            // Create a new database for the user using their username
            $new_db_name = $login_user;
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$new_db_name`");

            // Switch to the newly created user database
            $pdo->exec("USE `$new_db_name`");

            // Create the necessary tables in the new user database
            $create_customer_table = "
                CREATE TABLE customer (
                    id_customer SMALLINT(6) NOT NULL AUTO_INCREMENT,
                    name TEXT NOT NULL,
                    surname TEXT NOT NULL,
                    email VARCHAR(30) NOT NULL,
                    phone VARCHAR(12) NOT NULL,
                    PRIMARY KEY (id_customer)
                )";
            $pdo->exec($create_customer_table);

            $create_bike_table = "
                CREATE TABLE bike (
                    id_bike SMALLINT(6) NOT NULL AUTO_INCREMENT,
                    id_customer SMALLINT(6) NOT NULL,
                    brand TEXT NOT NULL,
                    model TEXT NOT NULL,
                    year INT(4) NOT NULL,
                    color TEXT NOT NULL,
                    PRIMARY KEY (id_bike),
                    FOREIGN KEY (id_customer) REFERENCES customer(id_customer)
                )";
            $pdo->exec($create_bike_table);

            $create_appointments_table = "
                CREATE TABLE appointment (
                    id_appointment SMALLINT(6) NOT NULL AUTO_INCREMENT,
                    id_customer SMALLINT(6) NOT NULL,
                    id_bike SMALLINT(6) NOT NULL,
                    service_type TEXT NOT NULL,
                    price INT(4) NOT NULL,
                    status TEXT NOT NULL,
                    comment TEXT NOT NULL,
                    date_recieved DATE DEFAULT current_timestamp(),
                    date_completed DATE,
                    PRIMARY KEY (id_appointment),
                    FOREIGN KEY (id_customer) REFERENCES customer(id_customer),
                    FOREIGN KEY (id_bike) REFERENCES bike(id_bike)
                )";
            $pdo->exec($create_appointments_table);

            $create_service_table = "
                CREATE TABLE service (
                    id_service SMALLINT(6) NOT NULL AUTO_INCREMENT,
                    type VARCHAR(100) NOT NULL,
                    price int(4) NOT NULL,
                    time_mins int(3) NOT NULL,
                    PRIMARY KEY (id_service)
                )";
            $pdo->exec($create_service_table);

            // Registration successful, show message and redirect after 2 seconds
            echo "<p class='success'>Registration successful! Welcome, $login_user.</p>";
            echo "<p class='info'>You will be redirected to the login page soon...</p>";

            // Use JavaScript to redirect after a delay of 4 seconds
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'login.php';
                    }, 4000);
                  </script>";

            exit(); // Ensure no further code is executed
        }
    }
} catch (PDOException $e) {
    // Handle error
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
h1 {
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

/* Input Styles */
label {
    display: block;
    font-size: 16px;
    color: #555;
    margin-bottom: 8px;
}

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
input[type="submit"] {
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

input[type="submit"]:hover {
    background-color: #0056b3;
}

/* Success/Error Message Styling */
p {
    font-size: 18px;
    text-align: center;
    margin-top: 20px;
}

p.success {
    color: #4CAF50;
}

p.error {
    color: #f44336;
    text-align: center;
    font-size: 16px;
    margin-top: 15px;
}

/* Redirection Notice Styling */
p.info {
    font-size: 16px;
    text-align: center;
    color: #888;
    margin-top: 10px;
}

/* Responsive Design for Smaller Screens */
@media (max-width: 480px) {
    form {
        padding: 20px;
        width: 90%;
    }

    h1 {
        font-size: 22px;
    }

    input[type="submit"] {
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
        <h1>Register New User</h1><br>
        <form method="POST" action="">
            <label for="username">Username: </label>
            <input type="text" id="username" name="username" required><br><br>
            
            <label for="password">Password: </label>
            <input type="password" id="password" name="password" required><br><br>
            
            <input type="submit" value="Register">
        </form>

        <div class="options">
            <a href="login.php">Login page</a><br><br>
        </div>

        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

    </div>
</body>
</html>
