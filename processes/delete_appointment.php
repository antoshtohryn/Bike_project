<?php
include '../login/auth.php'; // Authentication check

if (isset($_POST['id_appointment'])) {
    // Sanitize and get the appointment ID from the request
    $id_appointment = $_POST['id_appointment'];

    // Prepare the SQL query to delete the appointment
    $sql = "DELETE FROM appointment WHERE id_appointment = ?";

    // Prepare the statement
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id_appointment); // Bind the ID parameter as integer
        $stmt->execute(); // Execute the query

        if ($stmt->affected_rows > 0) {
            echo "Success"; // Return success message
        } else {
            echo "Error: Appointment not found.";
        }

        $stmt->close(); // Close the statement
    } else {
        echo "Error: Could not prepare the query.";
    }
}
?>
