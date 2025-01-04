<?php
include '../login/auth.php'; // Ensure the user is authenticated

// Check if the 'date' parameter is passed in the request
if (isset($_GET['date'])) {
    $date = $_GET['date'];

    // Debug: Log the received date
    error_log("Received date: " . $date);

    // Query to count the appointments on the given date
    $query = "SELECT COUNT(*) as appointment_count FROM appointment WHERE date_recieved = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $date);
    $stmt->execute();
    $stmt->bind_result($appointment_count);
    $stmt->fetch();
    
    // Debug: Log the result
    error_log("Appointment count for " . $date . ": " . $appointment_count);

    // Return the result as JSON
    echo json_encode([
        'appointment_count' => $appointment_count
    ]);

    $stmt->close();
    $conn->close();
} else {
    // Debug: Log the error
    error_log('No date provided in request');
    echo json_encode(['error' => 'No date provided']);
}
?>
