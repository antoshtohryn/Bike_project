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
    <div class="menu-item"><button>Calendar</button></div>
    <div class="menu-item" id="line"><a href="appointment_schedule.php"><button>Schedule</button></a></div>
    <div class="menu-item"><a href="appointment_list.php"><button>Appointments</button></a></div>
    <div class="menu-item"><button>Notes</button></div>
    <div class="menu-item"><button>Customers</button></div>
    <div class="menu-item" id="line"><button>Messages</button></div>
    <div class="menu-item"><button>Settings</button></div>
    <div class="menu-item"><button>Help</button></div>
</div>

<div class="content">
        <div class="customer-input">
        <form method="POST" action="appointment_registration_process.php" >
            <h2>Customer</h2>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="name">Surname:</label>
            <input type="text" id="surname" name="surname" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email">
            <label for="email">Phone:</label>
            <input type="phone" id="phone" name="phone" required>
            
            <h2>Date</h2>
            <label for="name">Date:</label>
            <input type="date" id="date" name="date">

            <h2>Bike</h2>
            <label for="name">Brand:</label>
            <input type="text" id="brand" name="brand" required>
            <label for="name">Model:</label>
            <input type="text" id="model" name="model" required>
            <label for="name">Year:</label>
            <input type="number" id="year" name="year" required>
            <label for="name">Color:</label>
            <input type="text" id="color" name="color" required>

            <h2>Service</h2>
            <select id="service" name="service">
                <?php
                // Check connection
                if ($conn->connect_error) {
                    echo"Connection failed";
                }
                echo "Connected successfully";
                // Fetch options from database
                $sql = "SELECT type FROM service";
                $result = $conn->query($sql);
            
                // Display options in select dropdown
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['type'] . "'>" . $row['type'] . "</option>";
                    }
                }
                $conn->close();
                ?>
            </select>

            <h2>Other information</h2>
            <label for="comment">Comments:</label>
            <textarea id="comment" name="comment" rows="4" style="resize: both;"></textarea>
            <label for="name">Estimated price:</label>
            <input type="number" id="price" name="price">
            
            <br>
            <input type="submit" name="submit" value="Register">
        </form>
    </div>
</div>

</body>
</html>
