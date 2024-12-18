<?php
include '../login/auth.php'; // Include authentication check

// Update the comment in the database
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_appointment = $_POST['id_appointment'];
    $new_price = $_POST['price_input'];

    $sql = "UPDATE appointment SET price = '$new_price' WHERE id_appointment = '$id_appointment'";
    $stmt = $conn->prepare($sql);
    

    if ($stmt->execute()) {
        echo "Comment updated successfully.";
        header("Location: ../bikeshop/appointment_details.php?id_appointment=$id_appointment"); // Redirect to the page
        exit();
    } else {
        echo "Error updating comment: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
