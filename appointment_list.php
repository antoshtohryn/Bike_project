<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>BikeRegist</title>
</head>
<body>

<div class="topbar">
<div class="main"><a href="main.html"><button>BikeRegist</button></a></div>
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


    <h1>My Appointments</h1>


<?php
if(isset($_GET['id_appointment'])) {
    // Extract the id_appointment value from the URL
    $id_appointment = $_GET['id_appointment'];
}

$servername = "localhost"; 
$username = "anton"; 
$password = "anton"; 
$database = "bikeshop"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $database);


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch appointments
$query = "
    SELECT 
        appointment.id_appointment, 
        customer.name AS name, 
        customer.surname AS surname, 
        bike.brand AS brand,
        bike.model AS model, 
        appointment.status as status,
        appointment.date_recieved 
    FROM appointment 
    JOIN customer ON appointment.id_customer = customer.id_customer
    JOIN bike ON appointment.id_bike = bike.id_bike
";
$result = $conn->query($query);
if($result)
{
    $rows = mysqli_num_rows($result); 
       print "<table>
	   			<tr>
					<th>ID</th>
					<th>Customer</th>
					<th>Bike</th>
                    <th>Status</th>
					<th>Date</th>
				</tr>";

                while ($row = $result->fetch_assoc()) {
                    $statusClass = $row["status"] === "open" ? "text-open" : "text-closed";
                
                    print "<tr onclick=\"window.location='appointment_details.php?id_appointment=" . $row["id_appointment"] . "'\">";
                    print "<td>" . $row["id_appointment"] . "</td>";
                    print "<td>" . $row["name"] . " " . $row["surname"] . "</td>";
                    print "<td>" . $row["brand"] . " " . $row["model"] . "</td>";
                    print "<td><span class='$statusClass'>" . ucfirst($row["status"]) . "</span></td>";
                    print "<td>" . $row["date_recieved"] . "</td>";
                    print "</tr>";
                }                
    }           
    /*
    for ($i = 0 ; $i < $rows ; ++$i)
    {
        $row = mysqli_fetch_row($result);
        print "<tr>";
            for ($j = 0 ; $j < 4 ; ++$j) 
				print "<td>$row[$j]</td>";
        print "</tr>";
    }
    */
    print "</table>";


$conn->close();
?>

</div>

</body>
</html>
