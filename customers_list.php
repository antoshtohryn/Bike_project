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
    <div class="menu-item"><a href="calendar.php"><button>Calendar</button></a></div>
    <div class="menu-item" id="line"><a href="appointment_schedule.php"><button>Schedule</button></a></div>
    <div class="menu-item"><a href="appointment_list.php"><button>Appointments</button></a></div>
    <div class="menu-item" id="line"><a href="customers_list.php"><button>Customers</button></a></div>
    <div class="menu-item"><a href="settings.php"><button>Settings</button></a></div>
</div>

<div class="content">
    <form method="get" action="search.php">
        <label for="search">Search by Customer surname or Bike brand:</label>
        <input type="search" id="search" class="search-input" name="search" placeholder="..." required>
        <input type="submit" name="search-button" value="Search">
    </form>
    <h1>All customers</h1>

    <div class="content_buttons">
    <?php
    // Base query
    $query = "SELECT * from customer";
    
    $result = $conn->query($query);

    if ($result) {
        $rows = mysqli_num_rows($result); 
        print "<table>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                </tr>";

        while ($row = $result->fetch_assoc()) {
           
            print "<tr onclick=\"window.location='customer_details.php?id_customer=" . $row["id_customer"] . "'\">";
            print "<td>" . $row["id_customer"] . "</td>";
            print "<td>" . $row["name"] . " " . $row["surname"] . "</td>";
            print "</tr>";
        }                
    } else {
        print "No appointments found.";
    }

    print "</table>";
    $conn->close();
    ?>
</div>
</body>
</html>
