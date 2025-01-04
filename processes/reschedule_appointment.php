<?php
include '../login/auth.php'; // Ensure user is authenticated

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_appointment = $_POST['id_appointment'];
    $reschedule_date = $_POST['reschedule_date'];

    // Validate input
    if (empty($id_appointment) || empty($reschedule_date)) {
        echo "Missing required fields.";
        exit;
    }

    // Prepare and execute the update query
    $query = "UPDATE appointment SET date_recieved = ? WHERE id_appointment = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $reschedule_date, $id_appointment);

    if ($stmt->execute()) {
        header('Location: ../bikeshop/appointment_details.php?id_appointment=' . $id_appointment);
        exit;
    } else {
        echo "Error updating appointment: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
