<?php
include_once("include/initialize.php");
include_once("include/header.php");
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = $_POST['category_name'];
    $status = $_POST['status'];

    if (!empty($category_name)) {
        $query = "INSERT INTO medicine_category (name, status) VALUES (?, ?)";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("si", $category_name, $status);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $message = "Category added successfully!";
                $alertType = "success"; // Set alert type for success
            } else {
                $message = "Failed to add category. Please try again.";
                $alertType = "error"; // Set alert type for error
            }
            $stmt->close();
        } else {
            $message = "Database error: Unable to prepare statement.";
            $alertType = "error"; // Set alert type for error
        }
    } else {
        $message = "Please enter a category name.";
        $alertType = "error"; // Set alert type for error
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicine Category</title>
    <!-- Include Toastr CSS and JS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</head>

<body>
    <div class="container">
        <h2>Add Medicine Category</h2>
        <form method="POST" action="">
            <div class="flex-container">
                <div class="form-group">
                    <label for="category_name">Category Name:</label>
                    <input type="text" id="category_name" name="category_name" placeholder="Enter category name" required>
                </div>
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn-primary">Add Category</button>
        </form>

        <?php if (isset($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
    </div>
</body>


</html>
<style>
    .container {
        max-width: 89%;
        margin: 50px auto;
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: rgba(50, 50, 93, 0.25) 0px 13px 27px -5px, rgba(0, 0, 0, 0.3) 0px 8px 16px -8px;
    }

    h2 {
        text-align: center;
        color: #333;
        font-size: 24px;
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        font-size: 14px;
        color: #555;
        margin-bottom: 8px;
        display: block;
        font-weight: 600;
    }

    input[type="text"],
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
        background-image: radial-gradient(circle 382px at 50% 50.2%, rgb(61 20 126) 0.1%, rgb(8 7 39) 100.2%);
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