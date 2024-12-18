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
    <style>

    </style>
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
    <form method="get" action="search.php">
        <label for="search">Search by Customer surname or Bike brand:</label>
        <input type="search" id="search" class="search-input" name="search" placeholder="..." required>
        <input type="submit" name="search-button" value="Search"> 
    </form>
    
    <div class="content_buttons">
        <!-- Create New Form -->
        <form method="POST" action="appointment_registaration_form.php"> 
            <input type="submit" name="submit" value="Create new"> 
        </form>

        <!-- Filter Form -->
        <form method="get" action="" class="filter-form">
            <label for="from-date">From:</label>
            <input type="date" id="from-date" name="from_date" value="<?= isset($_GET['from_date']) ? $_GET['from_date'] : '' ?>">
            <label for="to-date">To:</label>
            <input type="date" id="to-date" name="to_date" value="<?= isset($_GET['to_date']) ? $_GET['to_date'] : '' ?>">
            <input type="submit" value="Filter">
        </form>
    </div>

    <h1>Schedule</h1>

    <?php
    // Initialize date range filter variables
    $from_date = isset($_GET['from_date']) ? $_GET['from_date'] : null;
    $to_date = isset($_GET['to_date']) ? $_GET['to_date'] : null;

    // Base query with "open" status filter
    $query = "
        SELECT 
            appointment.id_appointment, 
            customer.name AS name, 
            customer.surname AS surname, 
            bike.brand AS brand,
            bike.model AS model, 
            appointment.status as status,
            appointment.date_recieved 
        FROM appointment 
        JOIN customer ON appointment.id_customer = customer.id_customer
        JOIN bike ON appointment.id_bike = bike.id_bike
        WHERE appointment.status = 'open'
    ";

    // Add date range filters to the query if provided
    $conditions = [];
    if ($from_date) {
        $conditions[] = "appointment.date_recieved >= '$from_date'";
    }
    if ($to_date) {
        $conditions[] = "appointment.date_recieved <= '$to_date'";
    }

    if (count($conditions) > 0) {
        $query .= " AND " . implode(' AND ', $conditions);
    }

    // Apply the date filter conditions
    if (count($conditions) > 0) {
        $query .= " WHERE " . implode(' AND ', $conditions);
    }

    // Add ORDER BY to sort by the appointment date (ascending or descending)
    // You can add "ASC" for ascending or "DESC" for descending.
    // By default, let's sort in ascending order.
    $query .= " ORDER BY appointment.date_recieved ASC"; // Change ASC to DESC if you want descending order.

    $result = $conn->query($query);
    
    if ($result) {
        $rows = mysqli_num_rows($result); 
        print "<table>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Bike</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>";

        while ($row = $result->fetch_assoc()) {
            $statusClass = $row["status"] === "open" ? "text-open" : "text-closed";
        
            print "<tr onclick=\"window.location='appointment_details.php?id_appointment=" . $row["id_appointment"] . "'\">";
            print "<td>" . $row["id_appointment"] . "</td>";
            print "<td>" . $row["name"] . " " . $row["surname"] . "</td>";
            print "<td>" . $row["brand"] . " " . $row["model"] . "</td>";
            print "<td><span class='$statusClass'>" . ucfirst($row["status"]) . "</span></td>";
            print "<td>" . $row["date_recieved"] . "</td>";
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
