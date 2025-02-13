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
</head>
<body>

<div class="topbar">
    <div class="main"><a href="main.php"><button>BikeRegist</button></a></div>
    <div class="logout"><a href="../login/logout.php"><button>Logout</button></a></div>
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
    $comment = $_POST['comment'];
    $price = $_POST['price'];

    
    // Check if the customer already exists
$checkQuery = "SELECT id_customer FROM customer WHERE surname = '$surname' AND phone = '$phone'";
$result = $conn->query($checkQuery);

if ($result->num_rows > 0) {
    // Customer already exists
    $row = $result->fetch_assoc();
    $id_customer = (int)$row['id_customer']; // Convert to integer
} else {
    // Insert new customer
    $insertQuery = "INSERT INTO customer (name, surname, email, phone) VALUES ('$name', '$surname', '$email', '$phone')";
    
    if ($conn->query($insertQuery) === TRUE) {
        echo "New customer added successfully.";
        // Get the last inserted customer ID
        $query_id_customer = "SELECT id_customer FROM customer ORDER BY id_customer DESC LIMIT 1";
        $query = mysqli_query($conn, $query_id_customer);
        $row = $query->fetch_assoc();
        $id_customer = $row['id_customer'];
    } else {
        echo "Error: " . $conn->error;
    }
}

// Check if the bike already exists for the customer
$checkBikeQuery = "SELECT id_bike FROM bike WHERE brand = '$brand' AND model = '$model' AND year = $year AND color = '$color' AND id_customer = $id_customer";
$bikeResult = $conn->query($checkBikeQuery);

if ($bikeResult->num_rows > 0) {
    // Bike already exists, get the bike ID
    $bikeRow = $bikeResult->fetch_assoc();
    $id_bike = (int)$bikeRow['id_bike']; // Convert to integer
    echo "Bike already exists. Bike ID: " . $id_bike;
} else {
    // Insert new bike
    $insertBikeQuery = "INSERT INTO bike (id_customer, brand, model, year, color) VALUES ($id_customer, '$brand', '$model', $year, '$color')";
    
    if ($conn->query($insertBikeQuery) === TRUE) {
        // Get the last inserted bike ID
        $query_id_bike = "SELECT id_bike FROM bike ORDER BY id_bike DESC LIMIT 1";
        $query = mysqli_query($conn, $query_id_bike);
        $row = $query->fetch_assoc();
        $id_bike = $row['id_bike'];
        echo "New bike added successfully. Bike ID: " . $id_bike;
    } else {
        echo "Error: " . $conn->error;
    }
}

    $selected_services = $_POST['selected_services']; // This is the comma-separated string
    // Insert the appointment
    $conn->query("INSERT INTO appointment VALUES (null, $id_customer, $id_bike, '$selected_services', $price, 'open', '$comment', '$date', '0000-00-00')");

    echo "Appointment added to the Database";
    // Redirect to appointment list
    header("Location: ../bikeshop/appointment_list.php");
    exit(); // Ensure no further code is executed after redirection
}
$conn->close();
?>
</div>

</body>
</html>
