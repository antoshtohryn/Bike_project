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

    // Insert bike information
    $conn->query("INSERT INTO bike VALUES (null, $id_customer, '$brand', '$model', $year, '$color')");

    // Get the last inserted bike ID
    $query_id_bike = "SELECT id_bike FROM bike ORDER BY id_bike DESC LIMIT 1";
    $query = mysqli_query($conn, $query_id_bike);
    $row = $query->fetch_assoc();
    $id_bike = $row['id_bike'];

    $selected_services = $_POST['selected_services']; // This is the comma-separated string
    // Insert the appointment
    $conn->query("INSERT INTO appointment VALUES (null, $id_customer, $id_bike, '$selected_services', $price, 'open', '$comment', '$date', '$date')");

    echo "Appointment added to the Database";
    // Redirect to appointment list
    header("Location: client_appointment_list.php");
    exit(); // Ensure no further code is executed after redirection
}
$conn->close();
?>