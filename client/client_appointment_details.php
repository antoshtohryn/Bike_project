<?php
include '../login/auth.php'; // Include authentication check
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../style.css">
    <title>BikeRegist</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="topbar">
    <div class="main"><a href="main.php"><button>BikeRegist</button></a></div>
    <div class="logout"><a href="login/logout.php"><button>Logout</button></a></div>
</div>

<div class="main-menu">
    <div class="menu-item" id="line"><a href="client_appointment_registration_form.php"><button>Book visit</button></a></div>
    <div class="menu-item"><a href="client.php"><button>Profile </button></a></div>
</div>

<div class="content">
    <?php
        if (isset($_GET['id_appointment'])) {
            $id_appointment = $_GET['id_appointment'];
            $query = "SELECT appointment.*, bike.*, customer.*
                      FROM appointment
                      INNER JOIN bike ON appointment.id_bike = bike.id_bike
                      INNER JOIN customer ON appointment.id_customer = customer.id_customer
                      WHERE appointment.id_appointment = $id_appointment";
            $result = $conn->query($query);
        
            if ($result->num_rows > 0) {
                $appointment = $result->fetch_assoc();
    ?>

    <h1>Appointment Details</h1>

    <div class="card">
        <h2>Bike</h2><br>
        <p><?php echo $appointment['brand']; echo " "; echo $appointment['model']; ?></p>
        <p><?php echo $appointment['year']; echo ", "; echo $appointment['color']; ?></p>
    </div>

    <div class="card">
        <h2>Notes</h2><br>
        <p id="comment-text"><?php echo $appointment['comment']; ?></p><br>
    </div>

    <div class="card">
        <h2>Service Type</h2><br>
        <p><?php echo $appointment['service_type']; ?></p><br>
        <p id="price-text">Price: <?php echo $appointment['price']; ?></p><br>
    </div>

    <div class="card">
        <h2>Customer</h2><br>
        <p><?php echo $appointment['name']; echo " "; echo $appointment['surname']; ?></p><br>
        Contact information:<br><br>
        <p><?php echo $appointment['phone']; ?></p>
        <p><?php echo $appointment['email']; ?></p>
    </div>  

    <div class="card">
        <h2>Status</h2><br>
        <p id="date_recieved">Schheduled for: <?php echo $appointment['date_recieved']; ?></p>
        <p id="status"><?php echo $appointment['status']; ?></p>
    </div>

    <?php
            } else {
                echo "Appointment not found.";
            }
        } else {
            echo "No appointment ID specified.";
        }

        $conn->close();
    ?>
</div>

</body>
</html>
