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
    <!-- Search Form -->
    <form method="get" action="">
        <label for="search">Search by Customer surname or Bike brand:</label>
        <input type="search" id="search" class="search-input" name="search" placeholder="Enter surname or bike brand..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        <input type="hidden" name="from_date" value="<?= isset($_GET['from_date']) ? htmlspecialchars($_GET['from_date']) : '' ?>">
        <input type="hidden" name="to_date" value="<?= isset($_GET['to_date']) ? htmlspecialchars($_GET['to_date']) : '' ?>">
        <input type="submit" name="search-button" value="Search">
    </form>

    <div class="content_buttons">
        <!-- Create New Form -->
        <form method="POST" action="appointment_registaration_form.php"> 
            <input type="submit" name="submit" value="Create new"> 
        </form>

        <!-- Filter Form -->
        <form method="get" action="" class="filter-form">
            <input type="hidden" name="search" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            <label for="from-date">From:</label>
            <input type="date" id="from-date" name="from_date" value="<?= isset($_GET['from_date']) ? htmlspecialchars($_GET['from_date']) : '' ?>">
            <label for="to-date">To:</label>
            <input type="date" id="to-date" name="to_date" value="<?= isset($_GET['to_date']) ? htmlspecialchars($_GET['to_date']) : '' ?>">
            <input type="submit" value="Filter">
        </form>
    </div>

    <h1>Schedule</h1>

    <?php
    // Initialize variables for search and date filters
    $search = isset($_GET['search']) ? trim($_GET['search']) : null;
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

    // Add conditions for search and date filters
    $conditions = [];
    if ($search) {
        $conditions[] = "(customer.surname LIKE '%" . $conn->real_escape_string($search) . "%' OR bike.brand LIKE '%" . $conn->real_escape_string($search) . "%')";
    }
    if ($from_date) {
        $conditions[] = "appointment.date_recieved >= '" . $conn->real_escape_string($from_date) . "'";
    }
    if ($to_date) {
        $conditions[] = "appointment.date_recieved <= '" . $conn->real_escape_string($to_date) . "'";
    }

    if (count($conditions) > 0) {
        $query .= " AND " . implode(' AND ', $conditions);
    }

    // Add ORDER BY to sort by the appointment date
    $query .= " ORDER BY appointment.date_recieved ASC";

    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        echo "<table>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Bike</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>";

        while ($row = $result->fetch_assoc()) {
            $statusClass = $row["status"] === "open" ? "text-open" : "text-closed";
        
            echo "<tr onclick=\"window.location='appointment_details.php?id_appointment=" . $row["id_appointment"] . "'\">";
            echo "<td>" . $row["id_appointment"] . "</td>";
            echo "<td>" . htmlspecialchars($row["name"]) . " " . htmlspecialchars($row["surname"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["brand"]) . " " . htmlspecialchars($row["model"]) . "</td>";
            echo "<td><span class='$statusClass'>" . ucfirst(htmlspecialchars($row["status"])) . "</span></td>";
            echo "<td>" . htmlspecialchars($row["date_recieved"]) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No appointments found.</p>";
    }

    $conn->close();
    ?>
</div>

</body>
</html>
