<?php
include '../login/auth.php'; // Include authentication check

// Check if a month and year are set, otherwise use the current month and year
if (isset($_POST['month']) && isset($_POST['year'])) {
    $selected_month = $_POST['month'];
    $selected_year = $_POST['year'];
} else {
    $selected_month = date('m'); // Default to current month
    $selected_year = date('Y');  // Default to current year
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics - Completed Appointments</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Include Chart.js -->
    <link rel="stylesheet" href="../style.css">
    <style>
        /* Canvas Styling */
        canvas {
            width: 80% !important; /* Make the graph responsive */
            height: 500px !important;
            display: block;
            margin: 0 auto; /* Center the canvas */
        }
        h1 {
            text-align: center;
            color: #333;
            font-family: Arial, sans-serif;
            font-size: 1.8em;
            margin-bottom: 20px;
        }
        .form-container {
            text-align: center;
            margin-bottom: 20px;
        }
        select {
            padding: 10px;
            font-size: 1em;
            margin-right: 10px;
        }
        #view  {
            padding: 10px 20px;
            font-size: 1.2em;
            background-color: #4CAF50; /* Green background color */
            color: white; /* White text color */
            border: none;
            border-radius: 5px; /* Rounded corners */
            cursor: pointer; /* Pointer cursor on hover */
            transition: background-color 0.3s ease; /* Smooth background transition */
        }

        button:hover {
            background-color: #45a049; /* Darker green when hovered */
        }

        button:active {
            background-color: #3e8e41; /* Even darker green when clicked */
        }

        button:focus {
            outline: none; /* Removes the default focus outline */
        }

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
    <div class="form-container">
        <form method="POST">
            <!-- Month Dropdown -->
            <select name="month">
                <?php
                // Generate month options dynamically (1 to 12)
                $months = [
                    '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
                    '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
                    '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
                ];
                foreach ($months as $key => $value) {
                    echo "<option value=\"$key\" " . ($key == $selected_month ? "selected" : "") . ">$value</option>";
                }
                ?>
            </select>

            <!-- Year Dropdown -->
            <select name="year">
                <?php
                // Generate year options dynamically (from 2020 to current year)
                $current_year = date('Y');
                for ($year = 2020; $year <= $current_year; $year++) {
                    echo "<option value=\"$year\" " . ($year == $selected_year ? "selected" : "") . ">$year</option>";
                }
                ?>
            </select>

            <!-- Submit Button -->
            <button id="view" type="submit">View Appointments</button>
        </form>
    </div>

    <h1>Completed Appointments in <?php echo $months[$selected_month]; ?> <?php echo $selected_year; ?> by Week</h1>
    <canvas id="appointmentsChart" width="800" height="400"></canvas>

    <?php
    // Set the date range based on selected month and year
    $date_start = $selected_year . '-' . $selected_month . '-01';
    $date_end = date('Y-m-t', strtotime($date_start)); // Last day of the selected month

    // Query to get the count of completed appointments grouped by week and calculate the last day of each week
    $query = "
        SELECT 
            YEAR(date_completed) AS year, 
            WEEK(date_completed, 1) AS week, 
            DATE_ADD(DATE(date_completed) - INTERVAL WEEKDAY(date_completed) DAY, INTERVAL 6 DAY) AS last_day_of_week,
            COUNT(*) AS total
        FROM appointment
        WHERE date_completed IS NOT NULL 
            AND date_completed != '0000-00-00'
            AND status = 'closed'
            AND date_completed BETWEEN '$date_start' AND '$date_end'
        GROUP BY YEAR(date_completed), WEEK(date_completed, 1)
        ORDER BY year, week;
    ";

    $result = $conn->query($query);

    // Prepare data for the chart
    $weeks = [];
    $totals = [];
    
    // Generate list of all weeks in the selected month
    $current_date = strtotime($date_start);
    $end_date = strtotime($date_end);
    
    // Loop through all weeks in the selected month
    while ($current_date <= $end_date) {
        $week_end = date('Y-m-d', strtotime('next Sunday', $current_date));  // Get the last day of the week
        
        // Add the week (last day of the week) to weeks array, initializing count to 0
        $weeks[] = $week_end;
        $totals[] = 0; // Initialize with 0 completed appointments
        
        // Move to the next week
        $current_date = strtotime('+1 week', $current_date);
    }

    // Insert the data from the query into the totals array
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Find the week and update the total
            $week_end = $row['last_day_of_week'];
            $index = array_search($week_end, $weeks);
            if ($index !== false) {
                $totals[$index] = $row['total'];
            }
        }
    } else {
        echo "<p>No completed appointments found for the selected month and year.</p>";
    }

    $conn->close();
    ?>
</div>

<script>
    // Pass PHP data to JavaScript
    const weeks = <?php echo json_encode($weeks); ?>;
    const totals = <?php echo json_encode($totals); ?>;

    // Chart.js configuration
    const ctx = document.getElementById('appointmentsChart').getContext('2d');
    new Chart(ctx, {
        type: 'line', // Change to 'line' for a linear graph
        data: {
            labels: weeks,
            datasets: [{
                label: 'Completed Appointments',
                data: totals,
                backgroundColor: 'rgba(54, 162, 235, 0.2)', // Light blue
                borderColor: 'rgba(54, 162, 235, 1)', // Blue border
                borderWidth: 2,
                fill: false, // Line only (no fill)
                pointBackgroundColor: 'rgba(54, 162, 235, 1)', // Points color
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1, // Set the Y-axis step size to 1
                        precision: 0
                    },
                    title: {
                        display: true,
                        text: 'Number of Appointments'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Last Day of the Week'
                    },
                    ticks: {
                        autoSkip: true, // Skip some x labels if they are too many
                        maxRotation: 90, // Rotate the labels if they overlap
                        minRotation: 45
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `Completed: ${context.raw}`;
                        }
                    }
                }
            }
        }
    });
</script>

</body>
</html>
