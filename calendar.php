<?php
include 'login/auth.php'; // Include authentication check

// Get the current date
$currentDate = date('Y-m-d');

// Update appointments that are before the current date and have status 'open'
$updateAppointmentsQuery = "
    UPDATE appointment
    SET date_recieved = '$currentDate'
    WHERE status = 'open' AND date_recieved < '$currentDate'
";

// Execute the update query
if ($conn->query($updateAppointmentsQuery) === TRUE) {
    // Optionally, display a success message or log it
    // echo "Appointments have been successfully updated.";
} else {
    echo "Error updating appointments: " . $conn->error;
}

// Get the current month and year, or set to provided month/year from GET parameters
$month = isset($_GET['month']) ? $_GET['month'] : date('m');
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Get the first day of the month and the number of days in the month
$firstDay = strtotime("$year-$month-01");
$lastDay = strtotime("+1 month", $firstDay);
$daysInMonth = date('t', $firstDay);

// Get appointments for this month where status is 'open'
$appointmentsQuery = "
    SELECT 
        appointment.id_appointment, 
        customer.name AS customer_name, 
        bike.brand AS bike_brand, 
        appointment.date_recieved 
    FROM appointment
    JOIN customer ON appointment.id_customer = customer.id_customer
    JOIN bike ON appointment.id_bike = bike.id_bike
    WHERE MONTH(appointment.date_recieved) = '$month' 
    AND YEAR(appointment.date_recieved) = '$year'
    AND appointment.status = 'open'  -- Only fetch open appointments
";
$appointmentsResult = $conn->query($appointmentsQuery);
$appointments = [];
while ($row = $appointmentsResult->fetch_assoc()) {
    $appointments[$row['date_recieved']][] = $row;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Calendar - BikeRegist</title>
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
    <div class="menu-item"><button>Settings</button></div>
</div>

<div class="content">
    <form method="get" action="search.php">
        <label for="search">Search by Customer surname or Bike brand:</label>
        <input type="search" id="search" class="search-input" name="search" placeholder="..." required>
        <input type="submit" name="search-button" value="Search">
    </form>

    <div class="calendar-controls">
        <!-- Dropdown to select the month and year -->
        <form method="GET" action="calendar.php">
            <select name="month" id="month" onchange="this.form.submit()">
                <option value="01" <?php echo ($month == '01') ? 'selected' : ''; ?>>January</option>
                <option value="02" <?php echo ($month == '02') ? 'selected' : ''; ?>>February</option>
                <option value="03" <?php echo ($month == '03') ? 'selected' : ''; ?>>March</option>
                <option value="04" <?php echo ($month == '04') ? 'selected' : ''; ?>>April</option>
                <option value="05" <?php echo ($month == '05') ? 'selected' : ''; ?>>May</option>
                <option value="06" <?php echo ($month == '06') ? 'selected' : ''; ?>>June</option>
                <option value="07" <?php echo ($month == '07') ? 'selected' : ''; ?>>July</option>
                <option value="08" <?php echo ($month == '08') ? 'selected' : ''; ?>>August</option>
                <option value="09" <?php echo ($month == '09') ? 'selected' : ''; ?>>September</option>
                <option value="10" <?php echo ($month == '10') ? 'selected' : ''; ?>>October</option>
                <option value="11" <?php echo ($month == '11') ? 'selected' : ''; ?>>November</option>
                <option value="12" <?php echo ($month == '12') ? 'selected' : ''; ?>>December</option>
            </select>
            
            <select name="year" id="year" onchange="this.form.submit()">
                <?php
                for ($i = date('Y') - 5; $i <= date('Y') + 5; $i++) {
                    echo "<option value='$i'" . ($year == $i ? ' selected' : '') . ">$i</option>";
                }
                ?>
            </select>
        </form>
    </div>

    <div class="calendar">
        <div class="calendar-header">
            <span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
        </div>

        <?php
        // Start generating the calendar
        $startDay = date('w', $firstDay); // Day of the week the 1st of the month falls on
        $currentDay = 1;

        // Fill empty spaces before the first day of the month
        for ($i = 0; $i < $startDay; $i++) {
            echo "<div class='calendar-day'></div>"; // Empty cell
        }

        // Loop through the days in the month
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = "$year-$month-" . str_pad($day, 2, '0', STR_PAD_LEFT);
            $appointmentsForDay = isset($appointments[$currentDate]) ? $appointments[$currentDate] : [];

            // Check if there are appointments for the day
            $hasAppointments = !empty($appointmentsForDay) ? 'has-appointments' : '';
            $clickable = !empty($appointmentsForDay) ? 'onclick="showAppointments(\'' . $currentDate . '\')"' : '';  // Only add the onclick if there are appointments

            echo "<div class='calendar-day $hasAppointments' $clickable>";
            echo "<div class='day-number'>$day</div>";

            if (!empty($appointmentsForDay)) {
                echo "<div class='appointments'>";
                foreach ($appointmentsForDay as $appointment) {
                    echo "<p>{$appointment['customer_name']} ({$appointment['bike_brand']})</p>";
                }
                echo "</div>";
            }
            echo "</div>";

            // Add new line after Saturday (7th day)
            if (date('w', strtotime("$year-$month-$day")) == 6) {
                echo "</div><div class='calendar'>";
            }
        }

        // Close the calendar div
        echo "</div>";
        ?>
    </div>
</div>

<script>
    function showAppointments(date) {
        // You can use AJAX or a new page to show the detailed appointments for that day
        window.location.href = 'appointment_list.php?date=' + date;
    }
</script>

</body>
</html>
