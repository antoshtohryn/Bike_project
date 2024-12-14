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
    <style>
        /* General form styling */
        .content {
            width: 60%;
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-top: 50px;
            margin-left: 300px;
            padding: 50px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Adjustments for form spacing */
        form {
            display: flex;
            flex-direction: column;
        }

        .customer-input {
            padding: 20px;
        }

        select {
            appearance: none;
            cursor: pointer;
        }

        /* Responsive Design */
        @media screen and (max-width: 768px) {
            .content {
                width: 90%;
                padding: 15px;
            }
            
            input[type="submit"] {
                width: 100%;
            }
        }
    </style>
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
        <div class="customer-input">
        <form method="POST" action="appointment_registration_process.php" id="appointmentForm">
            

        <?php
            $query = "SELECT id_customer, name, surname, phone FROM customer";
            $result = $conn->query($query);
            ?>

            <h2>Customer</h2>

            <!-- Select dropdown to choose an existing customer -->
            <label for="existing_customer">Select Existing Customer:</label>
            <select id="existing_customer" name="existing_customer">
                <option value="">Select a customer</option>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['id_customer'] . "' data-name='" . $row['name'] . "' data-surname='" . $row['surname'] . "' data-phone='" . $row['phone'] . "'>" . $row['name'] . " " . $row['surname'] . " - " . $row['phone'] . "</option>";
                    }
                }
                ?>
            </select>

            <!-- Input fields for the customer details -->
            <label for="name">Name: <span style="color: red;">*</span></label>
            <input type="text" id="name" name="name" required>

            <label for="surname">Surname: <span style="color: red;">*</span></label>
            <input type="text" id="surname" name="surname" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email">

            <label for="phone">Phone: <span style="color: red;">*</span></label>
            <input 
                type="tel" 
                id="phone" 
                name="phone" 
                placeholder="370XXXXXXXX" 
                required
            >
            <script>
            const customerSelect = document.getElementById('existing_customer');

            // When a customer is selected, populate the input fields
            customerSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];

                // Get the customer details from the selected option's data attributes
                const name = selectedOption.getAttribute('data-name');
                const surname = selectedOption.getAttribute('data-surname');
                const phone = selectedOption.getAttribute('data-phone');
                
                // Populate the form fields
                document.getElementById('name').value = name;
                document.getElementById('surname').value = surname;
                document.getElementById('phone').value = phone;
                
                // Optional: You can also fill in the email if it's available
                // document.getElementById('email').value = selectedOption.getAttribute('data-email') || '';
            });
            </script>
<script>
            const phoneInput = document.getElementById('phone');

            phoneInput.addEventListener('input', function () {
                // Remove all non-numeric characters except "+" from the input
                let value = this.value.replace(/[^0-9+]/g, '');

                // Ensure the input starts with either "+370" or "8"
                if (!value.startsWith('+370') && !value.startsWith('8')) {
                value = '+370' + value.replace(/^(\+370|8)/, '');
                }

                // Limit the input length to the maximum allowed for Lithuanian numbers
                this.value = value.slice(0, 12); // "370XXXXXXXX" (11 characters)
            });
            </script>

            
            <h2>Appointment date</h2>
            <label for="date">Date: <span style="color: red;">*</span></label>
            <input type="date" id="date" name="date" min="<?= date('Y-m-d'); ?>" />

            <script>
                // Function to disable Sundays
                document.getElementById('date').addEventListener('input', function(event) {
                    var selectedDate = new Date(event.target.value);
                    var dayOfWeek = selectedDate.getDay(); // Get the day of the week (0 = Sunday, 6 = Saturday)
                    
                    if (dayOfWeek === 0) { // If the selected day is Sunday (0 = Sunday)
                        alert('Sundays are not allowed!');
                        event.target.value = '<?= date('Y-m-d'); ?>'; // Reset the input field
                    }
                });

                // Get the current date in the format YYYY-MM-DD
                const today = new Date().toISOString().split('T')[0];
                
                // Set the value of the date input to the current date
                document.getElementById('date').value = today;
            </script>

            <h2>Bike</h2>
            <label for="name">Brand: <span style="color: red;">*</span></label>
            <input type="text" id="brand" name="brand" required>
            <label for="name">Model: <span style="color: red;">*</span></label>
            <input type="text" id="model" name="model" required>
            <label for="name">Year: <span style="color: red;">*</span></label>
            <input type="number" id="year" name="year" required>
            <label for="name">Color: <span style="color: red;">*</span></label>
            <input type="text" id="color" name="color" required>


    <h2>Service<span style="color: red;">*</span></h2>
    <select id="service" name="service[]">
        <option value="" selected disabled required>Select a service</option>
        <?php
        // Fetch options from the database
        $sql = "SELECT type FROM service";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['type'] . "'>" . $row['type'] . "</option>";
            }
        }
        ?>
    </select>
    <button id="add_service" type="button" onclick="addService()">Add Service</button>

    <h3>Selected Services</h3>
    <ul id="selectedServices"></ul>

    <label for="price">Estimated price:<span style="color: red;">*</span></label>
    <input type="number" id="price" name="price" step="0.01" required>

    <!-- Hidden inputs for selected services -->
    <input type="hidden" id="selectedServicesInput" name="selected_services">

    <script>
    // Handle selected services
    let selectedServices = [];

    function addService() {
        const serviceType = document.getElementById("service").value;

        // Prevent adding a duplicate service
        if (selectedServices.some(service => service.type === serviceType)) {
            alert("This service is already selected.");
            return;
        }

        // Fetch price for the selected service
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "get_price.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send("type=" + encodeURIComponent(serviceType));

        xhr.onload = function () {
            if (xhr.status === 200) {
                const price = parseFloat(xhr.responseText);

                // Add service and price to selected services array
                selectedServices.push({ type: serviceType, price: price });

                // Update the UI
                updateServiceList();
                updateTotalPrice();
            }
        };
    }

    function updateServiceList() {
    const serviceList = document.getElementById("selectedServices");
    serviceList.innerHTML = "";

    selectedServices.forEach((service, index) => {
        const listItem = document.createElement("li");
        listItem.innerHTML = `
            ${service.type} - €${service.price.toFixed(2)}
            <button type="button" onclick="removeService(${index})">Remove</button>
        `;
        serviceList.appendChild(listItem);
    });

    // Extract just the service types (not the entire object) and update the hidden input
    const serviceTypes = selectedServices.map(service => service.type).join(',');  // Join types into a comma-separated string
    document.getElementById("selectedServicesInput").value = serviceTypes;  // Update hidden input with service types
    }


    function removeService(index) {
        selectedServices.splice(index, 1);
        updateServiceList();
        updateTotalPrice();
    }

    function updateTotalPrice() {
        const totalPrice = selectedServices.reduce((sum, service) => sum + service.price, 0);
        document.getElementById("price").value = totalPrice.toFixed(2);
    }
    </script>



            <h2>Other information</h2>
            <label for="comment">Comments: <span style="color: red;">*</span></label>
            <textarea id="comment" name="comment" rows="4" style="resize: both;" required></textarea>

            
            <br>
            <input type="submit" name="submit" value="Register">
        </form>
    </div>
</div>

</body>
</html>
