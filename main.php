<?php
include 'login/auth.php'; // Include authentication check
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
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
</div>

<div class="content">
    <form method="get" action="search.php">
        <label for="search">Search by Customer surname or Bike brand:</label><br>
        <input type="text" id="search" class="search-input" name="search" placeholder="...">
        <input type="submit" name="search-button" value="Search"> 
    </form>
  
    <form method="POST" action="connection_check.php"> 
        <b>Check connection</b> 
        <input type="submit" name="submit" value="!!!"> 
    </form>
</div>

</body>
</html>
