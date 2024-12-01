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
    <div class="menu-item"><<button>Calendar</button></div>
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
<?php
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
