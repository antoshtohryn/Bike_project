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
    <div class="menu-item"><button>Calendar</button></div>
    <div class="menu-item" id="line"><a href="appointment_schedule.php"><button>Schedule</button></a></div>
    <div class="menu-item"><a href="appointment_list.php"><button>Appointments</button></a></div>
    <div class="menu-item"><button>Notes</button></div>
    <div class="menu-item"><button>Customers</button></div>
    <div class="menu-item" id="line"><button>Messages</button></div>
    <div class="menu-item"><button>Settings</button></div>
    <div class="menu-item"><button>Help</button></div>
    <div class="menu-item"><button>Logout</button></div>
</div>

<div class="content">
    <?php
        if (isset($_GET['id_appointment'])) {
            $id_appointment = $_GET['id_appointment'];
            $query = "SELECT appointment.*, bike.*, customer.*, service.*
                      FROM appointment
                      INNER JOIN bike ON appointment.id_bike = bike.id_bike
                      INNER JOIN customer ON appointment.id_customer = customer.id_customer
                      INNER JOIN service ON appointment.service_type = service.type
                      WHERE appointment.id_appointment = $id_appointment";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                $appointment = $result->fetch_assoc();
    ?>
    <script>
    function toggleStatus(idAppointment) {
        const currentStatus = document.getElementById("status").innerText;
        const newStatus = currentStatus === "open" ? "closed" : "open";

        $.ajax({
            url: "update_status.php",
            method: "POST",
            data: { id_appointment: idAppointment, new_status: newStatus },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    // Update status on page
                    document.getElementById("status").innerText = data.new_status;

                    // Update button text and style
                    const button = document.getElementById("statusButton");
                    button.innerText = data.new_status === "open" ? "Close Appointment" : "Open Appointment";
                    button.className = data.new_status === "open" ? "green" : "red";
                } else {
                    alert("Error: " + data.message);
                }
            },
            error: function() {
                alert("An error occurred while updating the status.");
            }
        });
    }
    </script>

    <button id="statusButton" 
            class="<?php echo $appointment['status'] === 'open' ? 'green' : 'red'; ?>" 
            onclick="toggleStatus(<?php echo $appointment['id_appointment']; ?>)">
        <?php echo $appointment['status'] === 'open' ? 'Close Appointment' : 'Open Appointment'; ?>
    </button>

    <h1>Appointment Details</h1>

    <div class="card">
        <h2>Bike</h2><br>
        <p><?php echo $appointment['brand']; echo " "; echo $appointment['model']; ?></p>
        <p><?php echo $appointment['year']; echo ", "; echo $appointment['color']; ?></p>
    </div>

    <div class="card">
        <h2>Notes</h2><br>
        <p><?php echo $appointment['comment']; ?></p>
    </div>

    <div class="card">
        <h2>Service Type</h2><br>
        <p><?php echo $appointment['service_type']; ?></p>
        <p>Price: <?php echo $appointment['price']; ?></p>
    </div>

    <div class="card">
        <h2>Customer</h2><br>
        <p><?php echo $appointment['name']; echo " "; echo $appointment['surname']; ?></p><br>
        Contact information<br><br>
        <p><?php echo $appointment['phone']; ?></p>
        <p><?php echo $appointment['email']; ?></p>
    </div>  

    <div class="card">
        <h2>Status</h2><br>
        <p id="status">Recieved at: <?php echo $appointment['date_recieved']; ?></p>
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
