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
        tr {
            cursor: pointer;
        }
        tr:hover {
        background-color: #D1F4F5;
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


    <h1>Found Appointments</h1>


<?php
$servername = "localhost"; 
$username = "anton"; 
$password = "anton"; 
$database = "bikeshop"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

if(isset($_GET['search'])) {
    $search = $_GET['search'];

    // Query to retrieve id_customer from customer table based on search query
    $customerQuery = "SELECT id_customer FROM customer WHERE surname LIKE '%$search%'";
    $customerResult = $conn->query($customerQuery);

    // Query to retrieve id_bike from bike table based on search query
    $bikeQuery = "SELECT id_bike FROM bike WHERE brand LIKE '%$search%'";
    $bikeResult = $conn->query($bikeQuery);

    if (($customerResult->num_rows > 0) || ($bikeResult->num_rows > 0)) {
        $appointments = array();

        // Fetch the id_customer if found
        if ($customerResult->num_rows > 0) {
            $customerRow = $customerResult->fetch_assoc();
            $id_customer = $customerRow['id_customer'];

            // Fetch appointments for the specific customer
            $customerAppointmentsQuery = "
                SELECT 
                    appointment.id_appointment, 
                    customer.name AS name, 
                    bike.brand AS brand, 
                    appointment.date_recieved 
                FROM appointment 
                JOIN customer ON appointment.id_customer = customer.id_customer
                JOIN bike ON appointment.id_bike = bike.id_bike
                WHERE appointment.id_customer = $id_customer
            ";
            $customerAppointmentsResult = $conn->query($customerAppointmentsQuery);
            if ($customerAppointmentsResult->num_rows > 0) {
                while($row = $customerAppointmentsResult->fetch_assoc()) {
                    $appointments[] = $row;
                }
            }
        }

        // Fetch appointments for the specific bike brand if found
        if ($bikeResult->num_rows > 0) {
            $bikeRow = $bikeResult->fetch_assoc();
            $id_bike = $bikeRow['id_bike'];

            // Fetch appointments for the specific bike brand
            $bikeAppointmentsQuery = "
                SELECT 
                    appointment.id_appointment, 
                    customer.name AS name, 
                    bike.brand AS brand, 
                    appointment.date_recieved 
                FROM appointment 
                JOIN customer ON appointment.id_customer = customer.id_customer
                JOIN bike ON appointment.id_bike = bike.id_bike
                WHERE appointment.id_bike = $id_bike
            ";
            $bikeAppointmentsResult = $conn->query($bikeAppointmentsQuery);
            if ($bikeAppointmentsResult->num_rows > 0) {
                while($row = $bikeAppointmentsResult->fetch_assoc()) {
                    $appointments[] = $row;
                }
            }
        }

        // Display appointments
        if (!empty($appointments)) {
            echo "<table>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Bike</th>
                        <th>Date</th>
                    </tr>";

            foreach ($appointments as $appointment) {
                echo "<tr onclick=\"window.location='appointment_details.php?id_appointment=" . $appointment["id_appointment"] . "'\">";
                echo "<td>" . $appointment["id_appointment"] . "</td>";
                echo "<td>" . $appointment["name"]. "</td>";
                echo "<td>" . $appointment["brand"]. "</td>";
                echo "<td>" . $appointment["date_recieved"]. "</td>";
                echo "</tr>";
            }           
            echo "</table>";
        } else {
            echo "No appointments found.";
        }
    } else {
        echo "No customer or bike found for the provided search query.";
    }
}



$conn->close();
?>

</div>

</body>
</html>
