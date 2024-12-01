<?php
include 'login/auth.php'; // Include authentication check
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>BikeRegist</title>
</head>
<body>

<div class="topbar">
    <div class="main"><a href="main.php"><button>BikeRegist</button></a></div>
    <div class="logout"><a href="login/logout.php"><button>Logout</button></a></div>
</div>

<div class="main-menu">
    <div class="menu-item"><button>Calendar</button></div>
    <div class="menu-item" id="line"><a href="appointment_schedule.php"><button>Schedule</button></a></div>
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


    <h1>Schedule</h1>


<?php
if(isset($_GET['id_appointment'])) {
    // Extract the id_appointment value from the URL
    $id_appointment = $_GET['id_appointment'];
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
    Where appointment.status = 'open'
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
