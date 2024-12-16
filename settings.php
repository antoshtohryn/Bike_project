<?php
include 'login/auth.php'; // Include authentication check
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Settings</title>
</head>
<body>

<div class="topbar">
    <div class="main"><a href="main.php"><button>BikeRegist</button></a></div>
    <div class="logout"><a href="login/logout.php"><button>Logout</button></a></div>
</div>

<div class="main-menu">
    <div class="menu-item"><a href="calendar.php"><button>Calendar</button></a></div>
    <div class="menu-item" id="line"><a href="appointment_schedule.php"><button>Schedule</button></a></div>
    <div class="menu-item"><a href="appointment_list.php"><button>Appointments</button></a></div>
    <div class="menu-item" id="line"><a href="customers_list.php"><button>Customers</button></a></div>
    <div class="menu-item"><a href="settings.php"><button>Settings</button></a></div>
</div>

<div class="content">
    <div class="container">
        <div class="image-button" onclick="openPage_service()">
            <img src="service_pic.png" alt="Click Me">
            <!-- Label below the image -->
            <h3>Add new service</h3>
        </div>

        <script>
            // JavaScript function to open a new page
            function openPage_service() {
                window.location.href = 'service_list.php';  // Replace with your desired URL
            }
        </script>

        <div class="image-button" onclick="openPage_statistics()">
            <img src="stat.jpg" alt="Click Me">
            <!-- Label below the image -->
            <h3>Statistics</h3>
        </div>

        <script>
            // JavaScript function to open a new page
            function openPage_statistics() {
                window.location.href = 'statistics.php';  // Replace with your desired URL
            }
        </script>
    </div>
    
</div>

</body>
</html>
