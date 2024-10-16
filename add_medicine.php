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

// Fetch categories from the medicine_category table
$categories = [];
$category_query = "SELECT id, name FROM medicine_category WHERE status = 1"; // Only fetch active categories
$result = $conn->query($category_query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the authenticated user ID from session
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in the session after login

    // Get form data
    $medicine_name = $_POST['medicine_name'];
    $category_id = $_POST['category']; // Get the selected category ID
    $qty = $_POST['qty'];
    $status = $_POST['status'];

    // Check if all required fields are filled
    if (!empty($medicine_name) && !empty($category_id) && !empty($qty)) {
        // Insert data into the medicine table
        $query = "INSERT INTO medicine (user_id, cat_id, name, qty, status) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($query)) {
            // Bind parameters
            $stmt->bind_param("iisii", $user_id, $category_id, $medicine_name, $qty, $status);

            // Execute the statement
            $stmt->execute();

            // Check if the insertion was successful
            if ($stmt->affected_rows > 0) {
                $message = "Medicine added successfully!";
                $alertType = "success"; // Set alert type for success
            } else {
                $message = "Failed to add medicine. Please try again.";
                $alertType = "error"; // Set alert type for error
            }
            $stmt->close();
        } else {
            $message = "Database error: Unable to prepare statement.";
            $alertType = "error"; // Set alert type for error
        }
    } else {
        $message = "Please fill out all fields.";
        $alertType = "error"; // Set alert type for error
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Medicine</title>
    <!-- Include Toastr CSS and JS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</head>

<body>
    <div class="container">
        <h2>Add Medicine</h2>
        <form method="POST" action="">
            <div class="flex-container">
                <div class="form-group">
                    <label for="medicine_name">Medicine Name:</label>
                    <input type="text" id="medicine_name" name="medicine_name" placeholder="Enter medicine name" required>
                </div>
                <div class="form-group">
                    <label for="category">Category:</label>
                    <select id="category" name="category" required>
                        <!-- PHP code to fetch categories from the medicine_category table -->
                        <option value="">Select a category</option>
                        <?php
                        foreach ($categories as $category) {
                            echo '<option value="' . $category['id'] . '">' . $category['name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="flex-container">
                <div class="form-group">
                    <label for="qty">Quantity:</label>
                    <input type="number" id="qty" name="qty" placeholder="Enter quantity" min="1" required>
                </div>
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn-primary">Add Medicine</button>

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
        height: 80%;
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
        /* space between the two fields */
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
    input[type="number"],
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
    input[type="number"]:focus,
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