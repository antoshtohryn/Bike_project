<?php
// Database connection details for 'user_management' and 'bikeshop' databases
$host = 'localhost'; 
$dbname1 = 'user_management'; 
$dbname2 = 'bikeshop';
$username = 'anton'; 
$password = 'anton';

$error = ""; // Variable to store error message

try {
    // Create PDO connections for both databases
    $pdo_user_management = new PDO("mysql:host=$host;dbname=$dbname1", $username, $password);
    $pdo_user_management->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo_bikeshop = new PDO("mysql:host=$host;dbname=$dbname2", $username, $password);
    $pdo_bikeshop->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve and sanitize inputs
        $login_user = trim($_POST['username']);
        $login_pass = trim($_POST['password']);
        $name = trim($_POST['name']);
        $surname = trim($_POST['surname']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format!";
        } else {
            // Begin a transaction
            $pdo_bikeshop->beginTransaction();

            try {
                // Insert customer details into 'bikeshop.customer' table
                $insert_customer_query = "INSERT INTO customer (name, surname, email, phone) 
                                          VALUES (:name, :surname, :email, :phone)";
                $insert_customer_stmt = $pdo_bikeshop->prepare($insert_customer_query);
                $insert_customer_stmt->bindParam(':name', $name);
                $insert_customer_stmt->bindParam(':surname', $surname);
                $insert_customer_stmt->bindParam(':email', $email);
                $insert_customer_stmt->bindParam(':phone', $phone);
                $insert_customer_stmt->execute();

                // Get the last inserted 'id_customer'
                $id_customer = $pdo_bikeshop->lastInsertId();

                // Commit the bikeshop transaction
                $pdo_bikeshop->commit();

                // Check if the username already exists in 'user_management.users'
                $query = "SELECT * FROM users WHERE username = :username";
                $stmt = $pdo_user_management->prepare($query);
                $stmt->bindParam(':username', $login_user);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $error = "Username is already taken. Please choose a different username.";
                } else {
                    // Hash the password
                    $hashed_password = password_hash($login_pass, PASSWORD_DEFAULT);

                    // Insert user details into 'user_management.users' table
                    $insert_user_query = "INSERT INTO users (id_customer, username, password) 
                                          VALUES (:id_customer, :username, :password)";
                    $insert_stmt = $pdo_user_management->prepare($insert_user_query);
                    $insert_stmt->bindParam(':username', $login_user);
                    $insert_stmt->bindParam(':password', $hashed_password);
                    $insert_stmt->bindParam(':id_customer', $id_customer);
                    $insert_stmt->execute();

                    // Registration successful, redirect
                    echo "<p class='success'>Registration successful! Welcome, $login_user.</p>";
                    echo "<p class='info'>You will be redirected to the login page soon...</p>";

                    echo "<script>
                            setTimeout(function() {
                                window.location.href = 'login.php';
                            }, 4000);
                          </script>";

                    exit(); // Ensure no further code is executed
                }
            } catch (Exception $e) {
                // Rollback the transaction in case of an error
                $pdo_bikeshop->rollBack();
                $error = "An error occurred: " . $e->getMessage();
            }
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
    margin-top: 300px;
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

input[type="text"], input[type="password"], input[type="email"], input[type="tel"] {
    width: 100%;
    padding: 12px;
    margin: 10px 0 20px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
}

input[type="text"]:focus, input[type="password"]:focus, input[type="email"]:focus, input[type="tel"]:focus {
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
            <label for="name">Name: <span style="color: red;">*</span></label>
            <input type="text" id="name" name="name" required><br>

            <label for="surname">Surname: <span style="color: red;">*</span></label>
            <input type="text" id="surname" name="surname" required><br>

            <label for="email">Email: <span style="color: red;">*</span></label>
            <input type="email" id="email" name="email" required><br>

            <label for="phone">Phone Number: <span style="color: red;">*</span></label>
            <input type="tel" id="phone" name="phone" placeholder="+370XXXXXXXX" required><br>
            <script>
                const phoneInput = document.getElementById('phone');

                phoneInput.addEventListener('input', function () {
                    // Remove all non-numeric characters except "+" from the input
                    let value = this.value.replace(/[^0-9+]/g, '');

                    // Ensure the input starts with either "+370" or "8"
                    if (!value.startsWith('+370') && !value.startsWith('8')) {
                    value = '+370' + value.replace(/^(\+370|8)/, '');
                    }

                    // Limit the input length to the maximum allowed for Lithuanian numbers
                    this.value = value.slice(0, 12); // "370XXXXXXXX" (11 characters)
                });
                </script>


            <label for="username">Username: <span style="color: red;">*</span></label>
            <input type="text" id="username" name="username" required><br>
            
            <label for="password">Password: <span style="color: red;">*</span></label>
            <input type="password" id="password" name="password" 
                required 
                minlength="8"
                pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}" 
                title="Password must be at least 8 characters, include at least one uppercase letter, one lowercase letter, one number, and one special character.">
            <br>
            
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
