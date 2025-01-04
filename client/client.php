<?php
include '../login/auth.php'; // Include authentication check
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../style.css">
    <title>BikeRegist</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="topbar">
    <div class="main"><a href="client.php"><button>BikeRegist</button></a></div>
    <div class="logout"><a href="../login/logout.php"><button>Logout</button></a></div>
</div>

<div class="main-menu">
    <div class="menu-item" id="line"><a href="client_appointment_registration_form.php"><button>Book visit</button></a></div>
    <div class="menu-item"><a href="client.php"><button>Profile</button></a></div>
</div>

<div class="content">
    <?php
    // Connect to the user's database
    $conn = new mysqli('localhost', 'anton', 'anton', 'user_management');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $username = $_SESSION['username'];
    $checkQuery = "SELECT id_customer FROM users WHERE username = '$username'";
    $result = $conn->query($checkQuery);
    $row = $result->fetch_assoc();
    $id_customer = (int)$row['id_customer'];

    $conn = new mysqli('localhost', 'anton', 'anton', 'bikeshop');
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

        // Query to get customer and their bikes
        $query = "SELECT customer.*, bike.* 
                  FROM customer 
                  INNER JOIN bike ON customer.id_customer = bike.id_customer 
                  WHERE customer.id_customer = $id_customer";
        $result = $conn->query($query);

            $query = "SELECT * FROM customer WHERE customer.id_customer = $id_customer";
            $result = $conn->query($query);
            // Fetch customer information from the first row
            $customer = $result->fetch_assoc();

            // Display customer information
            ?>
            <h1>Customer and Appointments Information</h1>
            <h2>Customer</h2><br>
            <div class="card">
                <p><?php echo $customer['name'] . " " . $customer['surname']; ?></p><br>
                Contact Information:<br><br>
                <p><?php echo $customer['phone']; ?></p>
                <p><?php echo $customer['email']; ?></p>
            </div>

            <h2>Bikes</h2>
            <?php
             $query = "SELECT * FROM bike WHERE bike.id_customer = $id_customer";
             $result = $conn->query($query);
            if ($result && $result->num_rows > 0){
               // Reset the result pointer to loop through all bikes
               $result->data_seek(0);
   
               // Loop through each bike and display details
               while ($bike = $result->fetch_assoc()) {
                   ?>
                   <div class="card">
                       <p><?php echo $bike['brand'] . " " . $bike['model']; ?></p>
                       <p><?php echo $bike['year'] . ", " . $bike['color']; ?></p>
                   </div>
                   <?php
               }
            }else {
                echo "<p>No bikes found for this customer.</p>";
            }
            // Query to get all appointments for this customer
            $appointmentsQuery = "
                SELECT 
                    appointment.id_appointment, 
                    bike.brand AS bike_brand, 
                    bike.model AS bike_model, 
                    appointment.status, 
                    appointment.date_recieved
                FROM appointment
                INNER JOIN bike ON appointment.id_bike = bike.id_bike
                WHERE appointment.id_customer = $id_customer
                ORDER BY appointment.date_recieved DESC
            ";
            $appointmentsResult = $conn->query($appointmentsQuery);

            ?>
            <h2>Appointments</h2>
            <?php
            if ($appointmentsResult && $appointmentsResult->num_rows > 0) {
                // Display appointments in a table format
                echo "<table border='1' cellspacing='0' cellpadding='5'>
                        <tr>
                            <th>ID</th>
                            <th>Bike</th>
                            <th>Status</th>
                            <th>Date Scheduled</th>
                        </tr>";
                while ($appointment = $appointmentsResult->fetch_assoc()) {
                    $statusClass = ($appointment['status'] === "open") ? "text-open" : "text-closed";
                    echo "<tr onclick=\"window.location='client_appointment_details.php?id_appointment=" . $appointment['id_appointment'] . "'\">";
                    echo "<td>" . $appointment['id_appointment'] . "</td>";
                    echo "<td>" . $appointment['bike_brand'] . " " . $appointment['bike_model'] . "</td>";
                    echo "<td><span class='$statusClass'>" . ucfirst($appointment['status']) . "</span></td>";
                    echo "<td>" . $appointment['date_recieved'] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No appointments found for this customer.</p>";
            }
        $conn->close();
    ?>

    
    <!-- DELETE ACCOUNT BUTTON -->
    <div class="delete-account">
       <br><button id="deleteAccountBtn" onclick="openDeleteConfirmation()">Delete Account</button>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <h2>Are you sure you want to delete your account?</h2>
            <p>This action will permanently delete your account, appointments, and all associated records. Please confirm!</p>
            <button onclick="openPasswordConfirmation()">Confirm</button>
            <button class="cancel" onclick="closeModal('confirmationModal')">Cancel</button>
        </div>
    </div>

    <!-- Password Confirmation Modal -->
    <div id="passwordModal" class="modal">
        <div class="modal-content">
            <h2>Confirm Your Identity</h2>
            <form id="deleteAccountForm" method="POST" action="../processes/delete_account.php">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required><br>

                <label for="password">Password:</label>
                <input type="text" name="password" id="password" required><br><br>

                <button type="submit">Delete Account</button>
                <button type="button" class="cancel" onclick="closeModal('passwordModal')">Cancel</button>
            </form>
        </div>
    </div>

</div>

<script>
    // Open the confirmation modal
    function openDeleteConfirmation() {
        document.getElementById("confirmationModal").style.display = "block";
    }

    // Open the password confirmation modal
    function openPasswordConfirmation() {
        document.getElementById("confirmationModal").style.display = "none";
        document.getElementById("passwordModal").style.display = "block";
    }

    // Close a modal by its ID
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = "none";
    }

    // Close the password modal after form submission
    $(document).ready(function() {
        $('#deleteAccountForm').submit(function(e) {
            e.preventDefault(); // Prevent form from submitting normally

            var username = $('#username').val();
            var password = $('#password').val();

            $.post('../processes/delete_account.php', {username: username, password: password}, function(response) {
                if (response.success) {
                    alert("Account deleted successfully!");
                    window.location.href = '../login/login.php'; // Redirect to login page after account deletion
                } else {
                    alert(response.error || "An error occurred. Please try again.");
                }
            }, 'json');
        });
    });
</script>

</body>
</html>