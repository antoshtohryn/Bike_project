<<<<<<< HEAD
<?php
include 'login/auth.php'; // Include authentication check
?>
=======
>>>>>>> b6cf5bea3f3c34867f129e18d97bddf3cf18ff15
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>BikeRegist</title>
<<<<<<< HEAD
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
=======
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 15px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        input[type="submit"] {
        background-color: #4CAF50; /* Green background */
        color: white; /* White text */
        padding: 10px 20px; /* Some padding */
        margin: 10px 0; /* Some margin */
        border: none; /* Remove borders */
        border-radius: 5px; /* Rounded corners */
        cursor: pointer; /* Pointer/hand icon on hover */
        font-size: 16px; /* Increase font size */
        font-weight: bold; /* Bold text */
        transition: background-color 0.3s ease; /* Smooth transition */
        }    
    </style>
>>>>>>> b6cf5bea3f3c34867f129e18d97bddf3cf18ff15
</head>
<body>

<div class="topbar">
<<<<<<< HEAD
    <div class="main"><a href="main.php"><button>BikeRegist</button></a></div>
    <div class="logout"><a href="login/logout.php"><button>Logout</button></a></div>
</div>

<div class="main-menu">
    <div class="menu-item"><a href="calendar.php"><button>Calendar</button></a></div>
    <div class="menu-item" id="line"><a href="appointment_schedule.php"><button>Schedule</button></a></div>
    <div class="menu-item"><a href="appointment_list.php"><button>Appointments</button></a></div>
    <div class="menu-item" id="line"><a href="customers_list.php"><button>Customers</button></a></div>
    <div class="menu-item"><a href="settings.php"><button>Settings</button></a></div>
</div>

<div class="content">
    <?php
        if (isset($_GET['id_appointment'])) {
            $id_appointment = $_GET['id_appointment'];
            $query = "SELECT appointment.*, bike.*, customer.*
                      FROM appointment
                      INNER JOIN bike ON appointment.id_bike = bike.id_bike
                      INNER JOIN customer ON appointment.id_customer = customer.id_customer
                      WHERE appointment.id_appointment = $id_appointment";
            $result = $conn->query($query);
        
            if ($result->num_rows > 0) {
                $appointment = $result->fetch_assoc();
    ?>
    <script>
    function toggleStatus(idAppointment) {
    const currentStatus = document.getElementById("status").innerText;
    const newStatus = currentStatus === "open" ? "closed" : "open";

    $.ajax({
        url: "update_status.php",
        method: "POST",
        data: { id_appointment: idAppointment, new_status: newStatus },
        success: function(response) {
            const data = JSON.parse(response);
            if (data.success) {
                // Update status on page
                document.getElementById("status").innerText = newStatus === "open" ? "open" : "Completed on " + data.date_completed;

                // Update button text and style
                const button = document.getElementById("statusButton");
                button.innerText = newStatus === "open" ? "Close Appointment" : "Open Appointment";
                button.className = newStatus === "open" ? "green" : "red";
            } else {
                alert("Error: " + data.message);
            }
        },
        error: function() {
            alert("An error occurred while updating the status.");
        }
    });
}

    </script>

    <button id="statusButton" 
            class="<?php echo $appointment['status'] === 'open' ? 'green' : 'red'; ?>" 
            onclick="toggleStatus(<?php echo $appointment['id_appointment']; ?>)">
        <?php echo $appointment['status'] === 'open' ? 'Close Appointment' : 'Open Appointment'; ?>
    </button>

    <h1>Appointment Details</h1>

    <div class="card">
        <h2>Bike</h2><br>
        <p><?php echo $appointment['brand']; echo " "; echo $appointment['model']; ?></p>
        <p><?php echo $appointment['year']; echo ", "; echo $appointment['color']; ?></p>
    </div>

    <div class="card">
        <h2>Notes</h2><br>
        <p id="comment-text"><?php echo $appointment['comment']; ?></p><br>
        <button id="edit-button">Edit</button>
    </div>

    <!-- Modal for editing the comment -->
    <div id="edit-modal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <form method="POST" action="update_comment.php">
                <label for="comment-input">Edit Comment:</label><br>
                <textarea id="comment_input" name="comment_input"><?php echo $appointment['comment']; ?></textarea><br>
                <input type="hidden" name="id_appointment" value=<?php echo $id_appointment; ?>>
                <button type="submit">Save</button>
            </form>
        </div>
    </div>

    <script>
        // Get elements
        const editButton = document.getElementById("edit-button");
        const modal = document.getElementById("edit-modal");
        const closeButton = document.querySelector(".close-button");

        // Open modal
        editButton.addEventListener("click", () => {
            modal.style.display = "block";
        });

        // Close modal
        closeButton.addEventListener("click", () => {
            modal.style.display = "none";
        });

        // Close modal when clicking outside of it
        window.addEventListener("click", (event) => {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    </script>

    <div class="card">
        <h2>Service Type</h2><br>
        <p><?php echo $appointment['service_type']; ?></p><br>
        <p id="price-text">Price: <?php echo $appointment['price']; ?></p><br>
        <button id="price-edit-button">Edit</button>
    </div>

    <!-- Modal for editing the price -->
    <div id="edit-price-modal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <form method="POST" action="update_price.php">
                <label for="price-input">Edit Price:</label><br>
                <input type="number" id="price_input" name="price_input" value="<?php echo $appointment['price']; ?>"><br>
                <input type="hidden" name="id_appointment" value="<?php echo $id_appointment; ?>">
                <button type="submit">Save</button>
            </form>
        </div>
    </div>

    <script>
        // Get elements for the price modal
        const priceEditButton = document.getElementById("price-edit-button");
        const priceModal = document.getElementById("edit-price-modal");

        // Open modal for editing price
        priceEditButton.addEventListener("click", () => {
            priceModal.style.display = "block";
        });

        // Close the price modal
        closeButton.addEventListener("click", () => {
            priceModal.style.display = "none";
        });

        // Close the modal when clicking outside of it
        window.addEventListener("click", (event) => {
            if (event.target === priceModal) {
                priceModal.style.display = "none";
            }
        });
    </script> 

    <div class="card">
        <h2>Customer</h2><br>
        <p><?php echo $appointment['name']; echo " "; echo $appointment['surname']; ?></p><br>
        Contact information:<br><br>
        <p><?php echo $appointment['phone']; ?></p>
        <p><?php echo $appointment['email']; ?></p>
    </div>  

    <div class="card">
        <h2>Status</h2><br>
        <p id="date_recieved">Schheduled for: <?php echo $appointment['date_recieved']; ?></p>
        <p id="status"><?php echo $appointment['status']; ?></p>
    </div>

    <?php
            } else {
                echo "Appointment not found.";
            }
        } else {
            echo "No appointment ID specified.";
        }

        $conn->close();
    ?>
=======
    <div class="page-title"><a href="main.html">BikeRegist</a></div>
    <div class="user-info">
        <span>Welcome, user</span>
    </div>
    <div class="notification-icon">🔔</div>
</div>

<div class="main-menu">
    <div class="menu-item"><a href="calendar.html"><button>Calendar</button></a></div>
    <div class="menu-item" id="line"><button>Schedule</button></div>
    <div class="menu-item"><a href="appointment_list.php"><button>Appointments</button></a></div>
    <div class="menu-item"><button>Notes</button></div>
    <div class="menu-item"><button>Customers</button></div>
    <div class="menu-item" id="line"><button>Messages</button></div>
    <div class="menu-item"><button>Settings</button></div>
    <div class="menu-item"><button>Help</button></div>
    <div class="menu-item"><button>Logout</button></div>
</div>

<div class="content">
    <form method="POST" action="appointment_registaration_form.php"> 
        <input type="submit" name="submit" value="Create new"> 
    </form>


    <h1>Appointment Details</h1>


<?php
$servername = "localhost"; 
$username = "anton"; 
$password = "anton"; 
$database = "bikeshop"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $database);


if(isset($_GET['id_appointment'])) {
    // Extract the id_appointment value from the URL
    $id_appointment = $_GET['id_appointment'];

    // Fetch appointment details with related data
    $query = "SELECT appointment.*, bike.*, customer.*
              FROM appointment
              INNER JOIN bike ON appointment.id_bike = bike.id_bike
              INNER JOIN customer ON appointment.id_customer = customer.id_customer
              WHERE appointment.id_appointment = $id_appointment";
   
    $result = $conn->query($query);

    // Check if appointment details are found
    if ($result->num_rows > 0) {
        // Output data of the appointment
        $appointment = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Appointment Details</title>
    <style>
        /* Add your CSS styles here */
    </style>
</head>
<body>

<div class="bike-details">
    <h2>Bike Details</h2>
    <p>Model: <?php echo $appointment['model']; ?></p>
    <p>Color: <?php echo $appointment['color']; ?></p>
    <!-- Add more bike details here -->
</div>

<div class="customer-details">
    <h2>Customer Details</h2>
    <p>Name: <?php echo $appointment['name']; ?></p>
    <p>Email: <?php echo $appointment['email']; ?></p>
    <!-- Add more customer details here -->
</div>

<div class="notes">
    <h2>Notes</h2>
    <p><?php echo $appointment['comment']; ?></p>
    <!-- Add more notes here -->
</div>

<div class="service-type">
    <h2>Service Type</h2>
    <p><?php echo $appointment['service_type']; ?></p>
    <!-- Add more service type details here -->
</div>

</body>
</html>

<?php
    } else {
        echo "Appointment not found.";
    }
} else {
    // If id_appointment parameter is not set in the URL
    echo "No parameter specified in the URL.";
}

$conn->close();
?>
>>>>>>> b6cf5bea3f3c34867f129e18d97bddf3cf18ff15
</div>

</body>
</html>
