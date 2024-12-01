<?php
include 'login/auth.php'; // Include authentication check
?>
<?php
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed."]));
}

// Check if request is valid
if (isset($_POST['id_appointment']) && isset($_POST['new_status'])) {
    $id_appointment = intval($_POST['id_appointment']);
    $new_status = $_POST['new_status'];

    // Update query
    $query = "UPDATE appointment SET status = ? WHERE id_appointment = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $new_status, $id_appointment);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "new_status" => $new_status]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update status."]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}

$conn->close();
?>
