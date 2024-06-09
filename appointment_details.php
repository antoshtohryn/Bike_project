<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>BikeRegist</title>
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
</head>
<body>

<div class="topbar">
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
</div>

</body>
</html>
