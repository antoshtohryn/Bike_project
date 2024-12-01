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
    <h2>Create appointment</h2>
        <div class="customer-input">
        <form method="POST" action="appointment_registration_process.php" >
            <h2>Customer</h2>
            <label for="name">Name:</label><br>
            <input type="text" id="name" name="name" required><br>
            <label for="name">Surname:</label><br>
            <input type="text" id="surname" name="surname" required><br>
            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email"><br>
            <label for="email">Phone:</label><br>
            <input type="phone" id="phone" name="phone" required><br>
            
            <h2>Date</h2>
            <label for="name">Date:</label><br>
            <input type="date" id="date" name="date"><br>

            <h2>Bike</h2>
            <label for="name">Brand:</label><br>
            <input type="text" id="brand" name="brand" required><br>
            <label for="name">Model:</label><br>
            <input type="text" id="model" name="model" required><br>
            <label for="name">Year:</label><br>
            <input type="number" id="year" name="year" required><br>
            <label for="name">Color:</label><br>
            <input type="text" id="color" name="color" required><br>

            <h2>Service</h2>
            <select id="service" name="service">
                <?php
                // Check connection
                if ($conn->connect_error) {
                    echo"Connection failed";
                }
                echo "Connected successfully";
                // Fetch options from database
                $sql = "SELECT type FROM service";
                $result = $conn->query($sql);
            
                // Display options in select dropdown
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['type'] . "'>" . $row['type'] . "</option>";
                    }
                }
                $conn->close();
                ?>
            </select><br>

            <h2>Other information</h2>
            <label for="name">Comments:</label><br>
            <input type="text" id="comment" name="comment"><br>
            <label for="name">Estimated price:</label><br>
            <input type="number" id="price" name="price"><br>
            
            <br>
            <input type="submit" name="submit" value="Register">
        </form>
    </div>
</div>

</body>
</html>
