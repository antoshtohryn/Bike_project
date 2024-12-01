
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
    <div class="page-title"><a href="main.html"><button>BikeRegist</button></a></div>
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
<?php
$servername = "localhost"; 
$username = "anton"; 
$password = "anton"; 
$database = "bikeshop"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    echo"Connection failed";
}

if(isset($_POST['submit']))
{
$name = $_POST['name'];
$surname = $_POST['surname'];
$email = $_POST['email'];
$phone = $_POST['phone'];

$date = $_POST['date'];

$brand = $_POST['brand'];
$model = $_POST['model'];
$year = $_POST['year'];
$color = $_POST['color'];

$service = $_POST['service'];
$comment = $_POST['comment'];
$price = $_POST['price'];


$conn->query("INSERT INTO customer VALUES (null, '$name', '$surname', '$email', $phone)");
$conn->query("INSERT INTO bike VALUES (null, '$brand', $year, '$model', '$color')");

$query_id_customer = "SELECT id_customer FROM customer ORDER BY id_customer DESC LIMIT 1";
$query = mysqli_query($conn, $query_id_customer);
$row = $query->fetch_assoc();
$id_customer = $row['id_customer'];

$query_id_bike = "SELECT id_bike FROM bike ORDER BY id_bike DESC LIMIT 1";
$query = mysqli_query($conn, $query_id_bike);
$row = $query->fetch_assoc();
$id_bike = $row['id_bike'];
//$id_service = ("SELECT id_service FROM service WHERE type='$service'");
$conn->query("INSERT INTO appointment VALUES (null, '$id_customer', '$id_bike', '$service', $price, null, '$comment', '$date')");
////////////////////////////////////////////////////////////////////////////////////// 

}
echo "Appointment added to the Database";
// Close connection
header("Location: appointment_list.php");
//exit(); // Ensure no further code is executed after redirection
$conn->close();
?>

</div>

</body>
</html>
