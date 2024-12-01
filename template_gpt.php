<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>BikeRegist</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Button Styling */
        button.green {
            background-color: #28a745; /* Green */
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        button.green:hover {
            background-color: #218838; /* Darker green on hover */
        }

        button.red {
            background-color: #dc3545; /* Red */
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        button.red:hover {
            background-color: #c82333; /* Darker red on hover */
        }
    </style>
</head>
<body>

<div class="topbar">
    <div class="main"><a href="main.html"><button>BikeRegist</button></a></div>
    <div class="user-info">
        <span>Welcome, user</span>
    </div>
    <div class="notification-icon">🔔</div>
</div>

<div class="main-menu">
    <div class="menu-item"><a href="calendar.html"><button>Calendar</button></a></div>
    <div class="menu-item" id="line"><button>Schedule</button></div>
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
        $servername = "localhost"; 
        $username = "anton"; 
        $password = "anton"; 
        $database = "bikeshop"; 

        $conn = new mysqli($servername, $username, $password, $database);

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
        <?php echo $appointment['status'] === 'open' ? 'Close Appointment' : 'Reopen Appointment'; ?>
    </button>

    <h1>Appointment Details</h1>

    <div class="bike-details">
        <h2>Bike Details</h2>
        <p>Model: <?php echo $appointment['model']; ?></p>
        <p>Color: <?php echo $appointment['color']; ?></p>
    </div>

    <div class="customer-details">
        <h2>Customer Details</h2>
        <p>Name: <?php echo $appointment['name']; ?></p>
        <p>Email: <?php echo $appointment['email']; ?></p>
    </div>

    <div class="notes">
        <h2>Notes</h2>
        <p><?php echo $appointment['comment']; ?></p>
    </div>

    <div class="service-type">
        <h2>Service Type</h2>
        <p><?php echo $appointment['service_type']; ?></p>
    </div>

    <div class="status">
        <h2>Status</h2>
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
