<!DOCTYPE html>
<html>
<head>
    <title>My Appointments</title>
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
    </style>
</head>
<body>

<h1>My Appointments</h1>


<?php
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
$query = "SELECT id_appointment, id_customer, id_bike, date_recieved FROM appointment";
$result = mysqli_query($conn, $query); 
if($result)
{
    $rows = mysqli_num_rows($result); 
       print "<table>
	   			<tr>
					<th>ID</th>
					<th>Customer</th>
					<th>Bike</th>
					<th>Date</th>
				</tr>";
    for ($i = 0 ; $i < $rows ; ++$i)
    {
        $row = mysqli_fetch_row($result);
        print "<tr>";
            for ($j = 0 ; $j < 4 ; ++$j) 
				print "<td>$row[$j]</td>";
        print "</tr>";
    }
    print "</table>";
}

$conn->close();
?>

</body>
</html>