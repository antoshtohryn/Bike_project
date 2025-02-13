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

    <button id="reschedule-button">Reschedule</button>

    <!-- Modal for Rescheduling -->
    <div id="reschedule-modal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <form method="POST" action="../processes/reschedule_appointment.php">
                <label for="reschedule-date">Select New Date:</label><br>
                <input type="date" id="reschedule-date" name="reschedule_date" required><br>
                <input type="hidden" name="id_appointment" value="<?php echo $id_appointment; ?>">
                <button type="submit">Save</button>
            </form>
            <!-- Error message -->
            <div id="date-error-message" style="color: red; display: none;">
                The chosen date is overloaded with appointments. Please choose another date.
            </div>
        </div>
    </div>

    <script>
        // Get elements for the reschedule modal
        const rescheduleButton = document.getElementById("reschedule-button");
        const rescheduleModal = document.getElementById("reschedule-modal");
        const rescheduleCloseButton = document.querySelector("#reschedule-modal .close-button");
        const rescheduleDateInput = document.getElementById("reschedule-date");
        const dateErrorMessage = document.getElementById("date-error-message");

        // Open the reschedule modal
        rescheduleButton.addEventListener("click", () => {
            rescheduleModal.style.display = "block";
        });

        // Close the reschedule modal
        rescheduleCloseButton.addEventListener("click", () => {
            rescheduleModal.style.display = "none";
        });

        // Close the modal when clicking outside of it
        window.addEventListener("click", (event) => {
            if (event.target === rescheduleModal) {
                rescheduleModal.style.display = "none";
            }
        });

        // Disable Sundays in the date picker
        rescheduleDateInput.addEventListener("focus", () => {
            const today = new Date();
            const currentDate = today.toISOString().split("T")[0]; // Get today's date in YYYY-MM-DD format
            rescheduleDateInput.setAttribute('min', currentDate); // Set the minimum date to today

            // Disable Sundays
            rescheduleDateInput.addEventListener("input", () => {
                const selectedDate = new Date(rescheduleDateInput.value);
                if (selectedDate.getDay() === 0) { // 0 corresponds to Sunday
                    alert("Sundays are not allowed for appointments.");
                    rescheduleDateInput.value = ""; // Clear the input if Sunday is selected
                }
            });
        });

        // Check appointments when the date is selected
        rescheduleDateInput.addEventListener("change", () => {
            const selectedDate = rescheduleDateInput.value;
            if (selectedDate) {
                // Send an AJAX request to check the number of appointments on the selected date
                $.ajax({
                    url: "../processes/check_appointments.php",
                    method: "GET",
                    data: { date: selectedDate },
                    success: function(response) {
                        try {
                            const data = JSON.parse(response);
                            if (data.appointment_count >= 5) {
                                // If there are more than 5 appointments, show the error message
                                dateErrorMessage.style.display = "block";
                            } else {
                                // Hide the error message if there are 5 or fewer appointments
                                dateErrorMessage.style.display = "none";
                            }
                        } catch (error) {
                            console.error("Error parsing response: ", response, error);
                            alert("An error occurred while checking the date.");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error:", status, error);
                        alert("An error occurred while checking the date.");
                    }
                });
            }
        });
    </script>

    <script>
    function toggleStatus(idAppointment) {
        const currentStatus = document.getElementById("status").innerText;
        const newStatus = currentStatus.includes("open") ? "closed" : "open";

        $.ajax({
            url: "../processes/update_status.php",
            method: "POST",
            data: { id_appointment: idAppointment, new_status: newStatus },
            success: function(response) {
                try {
                    const data = JSON.parse(response); // Parse response
                    if (data.success) {
                        // Update status on page
                        const statusElement = document.getElementById("status");
                        const button = document.getElementById("statusButton");

                        if (newStatus === "open") {
                            statusElement.innerText = "open";
                            button.innerText = "Close Appointment";
                            button.className = "green";
                        } else {
                            statusElement.innerText = "Completed on " + data.date_completed;
                            button.innerText = "Open Appointment";
                            button.className = "red";
                        }
                    } else {
                        alert("Error: " + data.message);
                    }
                } catch (error) {
                    console.error("Error parsing response: ", response, error);
                    alert("Unexpected response format.");
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", status, error);
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

    <button type="button" id="deleteBtn" onclick="confirmDelete()">Delete Appointment</button>

<!-- Hidden input to store the appointment ID -->
<input type="hidden" id="id_appointment" value="<?php echo $id_appointment; ?>" />

<script>
    // Confirm delete and send AJAX request to delete the appointment
    function confirmDelete() {
        // Display a confirmation pop-up
        const confirmation = confirm("Are you sure you want to delete this appointment? This action cannot be undone.");

        // If user confirms, call the delete function
        if (confirmation) {
            const id_appointment = document.getElementById('id_appointment').value;
            deleteAppointment(id_appointment);  // Delete the appointment
        }
    }

    // Function to delete the appointment
    function deleteAppointment(id) {
        if (id) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "../processes/delete_appointment.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            // Send appointment ID to server
            xhr.send("id_appointment=" + encodeURIComponent(id));

            // Handle server response
            xhr.onload = function () {
                if (xhr.status === 200) {
                    alert("Appointment deleted successfully.");
                    window.location.href = "appointment_list.php";  // Redirect to the appointment list
                } else {
                    alert("An error occurred while deleting the appointment.");
                }
            };
        } else {
            alert("Invalid appointment ID.");
        }
    }
</script>



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
            <form method="POST" action="../processes/update_comment.php">
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
            <form method="POST" action="../processes/update_price.php">
                <label for="price-input">Edit Price:</label><br>
                <input type="number" id="price_input" name="price_input" value="<?php echo $appointment['price']; ?>"><br>
                <input type="hidden" name="id_appointment" value="<?php echo $id_appointment; ?>">
                <button type="submit">Save</button>
            </form>
        </div>
    </div>

    <script>
        const priceInput = document.getElementById('price_input');

        priceInput.addEventListener('input', function() {
            let value = priceInput.value;

            // Restrict the input to a maximum of 4 digits and prevent dots
            const regex = /^\d{0,4}$/;

            // If the input doesn't match the regex, slice the value to ensure it fits
            if (!regex.test(value)) {
                priceInput.value = value.slice(0, 4); // Maximum of 4 digits
            }
        });
    </script>

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
        <p id="date_recieved">Scheduled for: <?php echo $appointment['date_recieved']; ?></p>
        <p id="status">
            <?php
            if ($appointment['status'] === 'open') {
                echo 'open';
            } else {
                echo 'Completed on ' . $appointment['date_completed'];
            }
            ?>
        </p>
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