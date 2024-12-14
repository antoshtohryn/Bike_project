<?php
include 'login/auth.php'; // Include authentication check

// Check database connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed."]));
}

// Check if request is valid
if (isset($_POST['id_appointment']) && isset($_POST['new_status'])) {
    $id_appointment = intval($_POST['id_appointment']);
    $new_status = $_POST['new_status'];

    // If the status is being set to 'closed', we should update the 'date_completed'
    if ($new_status === 'closed') {
        $date_completed = date('Y-m-d H:i:s'); // Current date and time
        // Update query including the date_completed field
        $query = "UPDATE appointment SET status = ?, date_completed = ? WHERE id_appointment = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $new_status, $date_completed, $id_appointment);
    } else {
        // Otherwise, just update the status (no need to touch date_completed)
        $query = "UPDATE appointment SET status = ? WHERE id_appointment = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $new_status, $id_appointment);
    }

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "new_status" => $new_status, "date_completed" => $new_status === 'closed' ? $date_completed : null]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update status."]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}

$conn->close();
?>
