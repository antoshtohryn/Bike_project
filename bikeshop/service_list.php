<?php
include '../login/auth.php'; // Include authentication check

// Handle form submission to add a new service
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])) {
    $type = $_POST['type'];
    $price = $_POST['price'];
    $time_mins = $_POST['time_mins'];

    if (!empty($type) && !empty($price) && !empty($time_mins)) {
        $insertQuery = "INSERT INTO service (type, price, time_mins) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("sii", $type, $price, $time_mins);
        $stmt->execute();
        $stmt->close();
    }
}

// Handle row deletion
if (isset($_POST['delete_id'])) {
    $deleteId = $_POST['delete_id'];

    $deleteQuery = "DELETE FROM service WHERE id_service = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $stmt->close();
}

// Fetch all services
$selectQuery = "SELECT id_service, type, price, time_mins FROM service";
$result = $conn->query($selectQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <title>Settings</title>
    <style>
        .service-form {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        .service-form input {
            padding: 5px;
            font-size: 16px;
        }

        .service-form button {
            padding: 5px 10px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .service-form button:hover {
            background-color: #218838;
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
        }

        .delete-btn:hover {
            background-color: #c82333;
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
    <h2>Service Management</h2>

    <!-- Input Form -->
    <form method="POST" class="service-form">
        <input type="text" name="type" placeholder="Service Type" required>
        <input type="number" name="price" placeholder="Price" min="0" required>
        <input type="number" name="time_mins" placeholder="Time (mins)" min="0" required>
        <button type="submit" name="add_service">Add Service</button>
    </form>

    <!-- Table Displaying Services -->
    <table>
        <thead>
            <tr>
                <th>Service Type</th>
                <th>Price</th>
                <th>Time (mins)</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['type']); ?></td>
                        <td><?php echo htmlspecialchars($row['price']); ?></td>
                        <td><?php echo htmlspecialchars($row['time_mins']); ?></td>
                        <td>
                            <form method="POST" style="display:inline;" onsubmit="return confirmDelete();">
                                <input type="hidden" name="delete_id" value="<?php echo $row['id_service']; ?>">
                                <button type="submit" class="delete-btn">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No services found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
 
<script>
    function confirmDelete() {
        return confirm("Are you sure you want to delete this service?");
    }
</script>

</body>
</html>

<?php
$conn->close();
?>
