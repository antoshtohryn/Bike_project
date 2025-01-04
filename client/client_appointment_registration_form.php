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
    <div class="main"><a href="client.php"><button>BikeRegist</button></a></div>
    <div class="logout"><a href="../login/logout.php"><button>Logout</button></a></div>
</div>

<div class="main-menu">
    <div class="menu-item" id="line"><a href="client_appointment_registration_form.php"><button>Book visit</button></a></div>
    <div class="menu-item"><a href="client.php"><button>Profile </button></a></div>
</div>

<div class="content">
        <div class="customer-input">
        <form method="POST" action="client_appointment_registration_process.php" id="appointmentForm">
           
            <?php
            // Connect to the user's database
            $conn = new mysqli('localhost', 'anton', 'anton', 'user_management');

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $username = $_SESSION['username'];
            $checkQuery = "SELECT id_customer FROM users WHERE username = '$username'";
            $result = $conn->query($checkQuery);
            $row = $result->fetch_assoc();
            $id_customer = (int)$row['id_customer'];


            $conn = new mysqli('localhost', 'anton', 'anton', 'bikeshop');
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }


            $query = "SELECT id_bike, brand, model, year, color FROM bike where id_customer = $id_customer";
            $result = $conn->query($query);
            ?>
         
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
                 // Overload validation (more than 5 appointments on this date)
                 const selectedDateStr = selectedDate.toISOString().split('T')[0];
                    fetch(`../processes/check_appointments.php?date=${selectedDateStr}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.appointment_count >= 5) {
                                alert('The chosen date is overloaded with appointments. You may still choose this date, but consider selecting another.');
                            }
                        });
                });
            </script>

            <h2>Bike</h2>

            <!-- Select dropdown to choose an existing customer -->
            <label for="existing_bike">Select Existing Bike:</label>
                <select id="existing_bike" name="existing_bike">
                    <option value="">Select bike</option>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id_bike'] . "' data-brand='" . $row['brand'] . "' data-model='" . $row['model'] . "' data-year='" . $row['year'] . "' data-color='" . $row['color'] . "' >" . $row['brand'] . " " . $row['model'] . " , " . $row['year'] . " , " . $row['color'] ."</option>";
                        }
                    }
                    ?>
                </select>  
        
            <label for="name">Brand: <span style="color: red;">*</span></label>
            <input type="text" id="brand" name="brand" required>
            <label for="name">Model: <span style="color: red;">*</span></label>
            <input type="text" id="model" name="model" required>
            <label for="name">Year: <span style="color: red;">*</span></label>
            <input type="number" id="year" name="year" required>
            <script>
                const byearInput = document.getElementById('year');

                byearInput.addEventListener('input', function() {
                    let value = byearInput.value;

                    // Restrict the input to a maximum of 3 digits and prevent dots
                    const regex = /^\d{0,4}$/;

                    // If the input doesn't match the regex, slice the value to ensure it fits
                    if (!regex.test(value)) {
                        byearInput.value = value.slice(0, 4); // Maximum of 3 digits
                    }
                });
            </script>
            <label for="name">Color: <span style="color: red;">*</span></label>
            <input type="text" id="color" name="color" required>
        
            <script>
            // Get the bike dropdown and input fields
            const bikeSelect = document.getElementById('existing_bike');
            const brandInput = document.getElementById('brand');
            const modelInput = document.getElementById('model');
            const yearInput = document.getElementById('year');
            const colorInput = document.getElementById('color');

            // Populate input fields when a bike is selected
            bikeSelect.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];

                if (selectedOption.value) {
                    // Extract data attributes from the selected option
                    const brand = selectedOption.getAttribute('data-brand');
                    const model = selectedOption.getAttribute('data-model');
                    const year = selectedOption.getAttribute('data-year');
                    const color = selectedOption.getAttribute('data-color');

                    // Populate the fields with the extracted data
                    brandInput.value = brand || '';
                    modelInput.value = model || '';
                    yearInput.value = year || '';
                    colorInput.value = color || '';
                } else {
                    // Clear the fields if no bike is selected
                    brandInput.value = '';
                    modelInput.value = '';
                    yearInput.value = '';
                    colorInput.value = '';
                }
            });
            </script>

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
    <input type="number" id="price" name="price" value="0" required>
    <script>
        const priceInput = document.getElementById('price');

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
        xhr.open("POST", "../processes/get_price.php", true);
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
            <button id="removeService" type="button" onclick="removeService(${index})">Remove</button>
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
