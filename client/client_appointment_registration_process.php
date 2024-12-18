<?php
include '../login/auth.php'; // Include authentication check

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

if(isset($_POST['submit']))
{
    $date = $_POST['date'];
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $color = $_POST['color'];
    $comment = $_POST['comment'];
    $price = $_POST['price'];

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
    $conn->query("INSERT INTO appointment VALUES (null, $id_customer, $id_bike, '$selected_services', $price, 'open', '$comment', '$date', '$date')");

    echo "Appointment added to the Database";
    // Redirect to appointment list
    header("Location: client.php");
    exit(); // Ensure no further code is executed after redirection
}
$conn->close();
?>