<?php
include_once("include/initialize.php");
include_once("include/header.php");

// Database connection
$dbhost = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "shasthobdapi";

// Create connection
$conn = new mysqli($dbhost, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize message variable
$message = "";
$alertType = ""; // Variable to hold alert type for toastr

// Handling form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['supplier_name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $status = $_POST['status'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO suppliers (name, address, phone, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $name, $address, $phone, $status);

    if ($stmt->execute()) {
        $message = "Supplier added successfully!";
        $alertType = "success"; // Type for toastr
    } else {
        $message = "Error adding supplier: " . $conn->error;
        $alertType = "error"; // Type for toastr
    }

    $stmt->close(); // Close the prepared statement
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Supplier</title>
    <!-- Include Toastr CSS and JS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</head>

<body>
    <div class="container">
        <h2>Add Supplier</h2>
        <form method="POST" action="">
            <div class="flex-container">
                <div class="form-group">
                    <label for="supplier_name">Supplier Name:</label>
                    <input type="text" id="supplier_name" name="supplier_name" placeholder="Enter supplier name" required>
                </div>
                <div class="form-group">
                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" placeholder="Enter address" required>
                </div>
            </div>
            <div class="flex-container">
                <div class="form-group">
                    <label for="phone">Phone Number:</label>
                    <input type="tel" id="phone" name="phone" placeholder="Enter phone number" required>
                </div>
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn-primary">Add Supplier</button>
        </form>

        <!-- Display message -->
        <?php if (!empty($message)): ?>
            <script>
                toastr.<?php echo $alertType; ?>('<?php echo $message; ?>');
            </script>
        <?php endif; ?>
    </div>
</body>

</html>

<style>
    .container {
        max-width: 97%;
        height: auto;
        margin: 50px auto;
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: rgba(50, 50, 93, 0.25) 0px 13px 27px -5px, rgba(0, 0, 0, 0.3) 0px 8px 16px -8px;
    }

    h2 {
        text-align: center;
        color: #333;
        font-size: 26px;
        margin-bottom: 25px;
    }

    .flex-container {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        width: 48%;
        /* Each input field will take up 48% of the row */
    }

    label {
        font-size: 14px;
        color: #555;
        margin-bottom: 8px;
        display: block;
        font-weight: 600;
    }

    input[type="text"],
    input[type="tel"],
    select {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 16px;
        color: #333;
        transition: all 0.3s ease;
        background-color: #f9f9f9;
    }

    input[type="text"]:focus,
    input[type="tel"]:focus,
    select:focus {
        border-color: #80bdff;
        background-color: #fff;
        outline: none;
    }

    button.btn-primary {
        display: inline-block;
        width: 13%;
        color: #fff;
        padding: 12px 15px;
        font-size: 12px;
        font-weight: 600;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        background-image: radial-gradient(circle 986.6px at 10% 20%, rgba(251, 6, 6, 0.94) 0%, rgba(3, 31, 213, 1) 82.8%, rgba(248, 101, 248, 1) 87.9%);
        margin-left: 43%;
    }

    button.btn-primary:hover {
        background-color: #218838;
    }

    .message {
        text-align: center;
        color: #d9534f;
        margin-top: 20px;
        font-weight: bold;
    }
</style>