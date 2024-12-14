
<?php
$servername = "localhost"; // Change this to your MySQL server hostname
$username = "anton"; // Change this to your MySQL username
$password = "anton"; // Change this to your MySQL password
$database = "bikeshop"; // Change this to the name of your MySQL database

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    echo"Connection failed";
}

echo "Connected successfully";

// Close connection
$conn->close();
?>
