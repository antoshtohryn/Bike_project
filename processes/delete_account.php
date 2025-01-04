<?php
include '../login/auth.php'; // Include authentication check

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect username and password from the form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Establish database connection for user management (for authentication)
    $conn_user_management = new mysqli('localhost', 'anton', 'anton', 'user_management');

    if ($conn_user_management->connect_error) {
        die("Connection failed: " . $conn_user_management->connect_error);
    }

    // Get the logged-in user's current username from session
    $session_username = $_SESSION['username'];

    // Check if the provided username and session username match
    if ($username == $session_username) {
        // Fetch the stored password for the user
        $query_user = "SELECT password FROM users WHERE username = '$username'";
        $result_user = $conn_user_management->query($query_user);

        if ($result_user->num_rows > 0) {
            $row_user = $result_user->fetch_assoc();
            $stored_password = $row_user['password'];

            // Verify if the password entered is correct
            if (password_verify($password, $stored_password)) {
                // Establish connection to the bikeshop database for deleting appointments and bikes
                $conn_bikeshop = new mysqli('localhost', 'anton', 'anton', 'bikeshop');

                if ($conn_bikeshop->connect_error) {
                    die("Connection failed: " . $conn_bikeshop->connect_error);
                }

                // Get the customer's ID to perform deletions
                $user_id_query = "SELECT id_customer FROM users WHERE username = '$username'";
                $user_result = $conn_user_management->query($user_id_query);
                $user_row = $user_result->fetch_assoc();
                $id_customer = $user_row['id_customer'];

                // 1. Check and delete associated appointments from bikeshop database
                $appointments_query = "SELECT id_appointment FROM appointment WHERE id_customer = $id_customer";
                $appointments_result = $conn_bikeshop->query($appointments_query);

                if ($appointments_result->num_rows > 0) {
                    // Delete all appointments associated with the customer
                    $delete_appointments_query = "DELETE FROM appointment WHERE id_customer = $id_customer";
                    $conn_bikeshop->query($delete_appointments_query);
                }

                // 2. Check and delete associated bikes from bikeshop database
                $bikes_query = "SELECT id_bike FROM bike WHERE id_customer = $id_customer";
                $bikes_result = $conn_bikeshop->query($bikes_query);

                if ($bikes_result->num_rows > 0) {
                    // Delete all bikes associated with the customer
                    $delete_bikes_query = "DELETE FROM bike WHERE id_customer = $id_customer";
                    $conn_bikeshop->query($delete_bikes_query);
                }

                // 3. Finally, delete the customer record from bikeshop database
                $delete_customer_query = "DELETE FROM customer WHERE id_customer = $id_customer";
                if ($conn_bikeshop->query($delete_customer_query)) {
                    // 4. Now delete the user record from the user_management database
                    $delete_user_query = "DELETE FROM users WHERE username = '$username'";

                    if ($conn_user_management->query($delete_user_query)) {
                        // If the deletion was successful, log the user out and redirect to the login page
                        session_unset();
                        session_destroy();

                        // Return success response
                        echo json_encode(["success" => true]);
                        exit();
                    } else {
                        // If the user deletion failed
                        echo json_encode(["error" => "Error deleting user record from user_management."]);
                        exit();
                    }
                } else {
                    // If the customer deletion from bikeshop failed
                    echo json_encode(["error" => "Error deleting customer record from bikeshop."]);
                    exit();
                }
            } else {
                // Password verification failed
                echo json_encode(["error" => "Incorrect password. Please try again."]);
                exit();
            }
        } else {
            // Username not found
            echo json_encode(["error" => "Username not found."]);
            exit();
        }
    } else {
        // Provided username doesn't match the session username
        echo json_encode(["error" => "The username does not match."]);
        exit();
    }

    // Close the database connections
    $conn_user_management->close();
    $conn_bikeshop->close();
}
