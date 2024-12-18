<?php
include '../login/auth.php'; // Include authentication check
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
    <h1>Weekly Completed Appointments</h1>
    <canvas id="appointmentsChart" width="800" height="400"></canvas>

    <?php
    // Set the date range for the last 3 months
    $date_end = date('Y-m-d');
    $date_start = date('Y-m-d', strtotime('-3 months', strtotime($date_end)));

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
    
    // Generate list of all weeks in the date range
    $current_date = strtotime($date_start);
    $end_date = strtotime($date_end);
    
    // Loop through all weeks within the 3-month range
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
        echo "<p>No completed appointments found.</p>";
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
