<?php
include 'login/auth.php';

if (isset($_POST['id_customer'])) {
    $id_customer = intval($_POST['id_customer']); // Sanitize input
    $query = "SELECT id_bike, brand, model, year, color FROM bike WHERE id_customer = $id_customer";
    $result = $conn->query($query);

    $bikes = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $bikes[] = $row;
        }
    }

    echo json_encode($bikes);
}
?>
