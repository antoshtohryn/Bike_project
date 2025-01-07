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
    <form method="get" action="">
        <label for="search">Search by surname:</label>
        <input type="search" id="search" class="search-input" name="search" placeholder="Enter surname..." maxlength="20" required>
        <input type="submit" name="search-button" value="Search">
    </form>
    <h1>All customers</h1>

    <div class="content_buttons">
    <?php
    // Get the search value
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    // Query to fetch customers based on the search
    if (!empty($search)) {
        $query = "SELECT * FROM customer WHERE surname LIKE ?";
        $stmt = $conn->prepare($query);
        $searchParam = "%$search%";
        $stmt->bind_param("s", $searchParam);
    } else {
        $query = "SELECT * FROM customer";
        $stmt = $conn->prepare($query);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        echo "<table>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                </tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr onclick=\"window.location='customer_details.php?id_customer=" . $row["id_customer"] . "'\">";
            echo "<td>" . $row["id_customer"] . "</td>";
            echo "<td>" . $row["name"] . " " . $row["surname"] . "</td>";
            echo "</tr>";
        }                
        echo "</table>";
    } else {
        echo "<p>No customers found.</p>";
    }

    $stmt->close();
    $conn->close();
    ?>
    </div>
</div>
</body>
</html>
