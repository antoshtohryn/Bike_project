<?php
include '../login/auth.php'; // Authentication check

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get start and end date from the query parameters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-3 months'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Prepare and execute the query
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
        AND date_completed BETWEEN '$start_date' AND '$end_date'
    GROUP BY YEAR(date_completed), WEEK(date_completed, 1)
    ORDER BY year, week;
";

// Log the query for debugging
error_log("Executing query: " . $query);

$result = $conn->query($query);

if (!$result) {
    // Log any error from the query execution
    error_log("Error executing query: " . $conn->error);
}

// Prepare data for the chart
$weeks = [];
$totals = [];

// Generate list of all weeks in the selected date range
$current_date = strtotime($start_date);
$end_date_timestamp = strtotime($end_date);

// Loop through all weeks within the selected range
while ($current_date <= $end_date_timestamp) {
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
}

// Close the connection
$conn->close();

// Return the data as a JSON response
echo json_encode([
    'weeks' => $weeks,
    'totals' => $totals
]);
?>
