<?php
include 'login/auth.php'; // Include authentication check
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>BikeRegist</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="topbar">
    <div class="main"><a href="main.php"><button>BikeRegist</button></a></div>
    <div class="logout"><a href="login/logout.php"><button>Logout</button></a></div>
</div>

<div class="main-menu">
    <div class="menu-item"><a href="calendar.php"><button>Calendar</button></a></div>
    <div class="menu-item" id="line"><a href="appointment_schedule.php"><button>Schedule</button></a></div>
    <div class="menu-item"><a href="appointment_list.php"><button>Appointments</button></a></div>
    <div class="menu-item" id="line"><a href="customers_list.php"><button>Customers</button></a></div>
    <div class="menu-item"><button>Settings</button></div>
</div>

<div class="content">
    <?php
    if (isset($_GET['id_customer'])) {
        $id_customer = $_GET['id_customer'];

        // Query to get customer and their bikes
        $query = "SELECT customer.*, bike.*
                  FROM customer
                  INNER JOIN bike ON customer.id_customer = bike.id_customer
                  WHERE customer.id_customer = $id_customer";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            // Fetch customer information from the first row
            $customer = $result->fetch_assoc();

            // Display customer information
            ?>
            <h1>Customer Information</h1>
            <div class="card">
                <h2>Customer</h2><br>
                <p><?php echo $customer['name'] . " " . $customer['surname']; ?></p><br>
                Contact Information:<br><br>
                <p><?php echo $customer['phone']; ?></p>
                <p><?php echo $customer['email']; ?></p>
            </div>

            <h2>Bikes</h2>
            <?php
            // Reset the result pointer to loop through all bikes
            $result->data_seek(0); // Rewind result set to the first row

            // Loop through each bike and display details
            while ($bike = $result->fetch_assoc()) {
                ?>
                <div class="card">
                    <p><?php echo $bike['brand'] . " " . $bike['model']; ?></p>
                    <p><?php echo $bike['year'] . ", " . $bike['color']; ?></p>
                </div>
                <?php
            }
        } else {
            echo "Customer or bikes not found.";
        }
    } else {
        echo "No customer ID specified.";
    }

    $conn->close();
    ?>
</div>

</body>
</html>
