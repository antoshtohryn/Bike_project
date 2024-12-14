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
                document.getElementById("status").innerText = newStatus === "open" ? "open" : "Completed on " + data.date_completed;

                // Update button text and style
                const button = document.getElementById("statusButton");
                button.innerText = newStatus === "open" ? "Close Appointment" : "Open Appointment";
                button.className = newStatus === "open" ? "green" : "red";
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
        <p id="comment-text"><?php echo $appointment['comment']; ?></p><br>
        <button id="edit-button">Edit</button>
    </div>

    <!-- Modal for editing the comment -->
    <div id="edit-modal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <form method="POST" action="update_comment.php">
                <label for="comment-input">Edit Comment:</label><br>
                <textarea id="comment_input" name="comment_input"><?php echo $appointment['comment']; ?></textarea><br>
                <input type="hidden" name="id_appointment" value=<?php echo $id_appointment; ?>>
                <button type="submit">Save</button>
            </form>
        </div>
    </div>

    <script>
        // Get elements
        const editButton = document.getElementById("edit-button");
        const modal = document.getElementById("edit-modal");
        const closeButton = document.querySelector(".close-button");

        // Open modal
        editButton.addEventListener("click", () => {
            modal.style.display = "block";
        });

        // Close modal
        closeButton.addEventListener("click", () => {
            modal.style.display = "none";
        });

        // Close modal when clicking outside of it
        window.addEventListener("click", (event) => {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    </script>

    <div class="card">
        <h2>Service Type</h2><br>
        <p><?php echo $appointment['service_type']; ?></p><br>
        <p id="price-text">Price: <?php echo $appointment['price']; ?></p><br>
        <button id="price-edit-button">Edit</button>
    </div>

    <!-- Modal for editing the price -->
    <div id="edit-price-modal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <form method="POST" action="update_price.php">
                <label for="price-input">Edit Price:</label><br>
                <input type="number" id="price_input" name="price_input" value="<?php echo $appointment['price']; ?>"><br>
                <input type="hidden" name="id_appointment" value="<?php echo $id_appointment; ?>">
                <button type="submit">Save</button>
            </form>
        </div>
    </div>

    <script>
        // Get elements for the price modal
        const priceEditButton = document.getElementById("price-edit-button");
        const priceModal = document.getElementById("edit-price-modal");

        // Open modal for editing price
        priceEditButton.addEventListener("click", () => {
            priceModal.style.display = "block";
        });

        // Close the price modal
        closeButton.addEventListener("click", () => {
            priceModal.style.display = "none";
        });

        // Close the modal when clicking outside of it
        window.addEventListener("click", (event) => {
            if (event.target === priceModal) {
                priceModal.style.display = "none";
            }
        });
    </script> 

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
