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

// Initialize variables
$message = "";
$alertType = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $supplier_id = $_POST['supplier_id'];
    $cat_id = $_POST['cat_id'];
    $medicine_id = $_POST['medicine_id'];
    $InQty = $_POST['InQty'];

    // Insert data into stock_in table
    $stmt = $conn->prepare("INSERT INTO stock_in (supplier_id, cat_id, medicine_id, InQty, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("iiii", $supplier_id, $cat_id, $medicine_id, $InQty);

    if ($stmt->execute()) {
        $message = "Stock added successfully!";
        $alertType = "success";
    } else {
        $message = "Error adding stock: " . $stmt->error;
        $alertType = "error";
    }

    $stmt->close();
}

// Fetch suppliers
$suppliers_query = "SELECT id, name FROM suppliers";
$suppliers_result = $conn->query($suppliers_query);

// Fetch medicine categories
$categories_query = "SELECT id, name FROM medicine_category";
$categories_result = $conn->query($categories_query);

// Fetch medicines
$medicines_query = "SELECT id, name FROM medicine";
$medicines_result = $conn->query($medicines_query);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Stock</title>
    <!-- Include Toastr CSS and JS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Include Select2 CSS and JS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
</head>

<body>
    <div class="container">
        <h2>Add Stock</h2>
        <form method="POST" action="">
            <div class="flex-container">
                <div class="form-group">
                    <label for="supplier_id">Supplier:</label>
                    <select id="supplier_id" name="supplier_id" required>
                        <option value="">Select Supplier</option>
                        <?php while ($row = $suppliers_result->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="cat_id">Category:</label>
                    <select id="cat_id" name="cat_id" required>
                        <option value="">Select Category</option>
                        <?php while ($row = $categories_result->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="flex-container">
                <div class="form-group">
                    <label for="medicine_id">Medicine:</label>
                    <select id="medicine_id" name="medicine_id" required>
                        <option value="">Select Medicine</option>
                        <?php while ($row = $medicines_result->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="InQty">Quantity:</label>
                    <input type="number" id="InQty" name="InQty" placeholder="Enter quantity" required>
                </div>
            </div>

            <button type="submit" class="btn-primary">Add Stock</button>
        </form>

        <!-- Display message if exists -->
        <?php if (!empty($message)): ?>
            <script>
                toastr.<?php echo $alertType; ?>('<?php echo $message; ?>');
            </script>
        <?php endif; ?>
    </div>

    <script>
        // Initialize Select2 for searchable dropdowns
        $(document).ready(function() {
            $('#supplier_id, #cat_id, #medicine_id').select2({
                placeholder: "Select an option",
                allowClear: true
            });
        });
    </script>
</body>

</html>

<style>
    .container {
        max-width: 97%;
        margin: 50px auto;
        padding: 30px;
        background-color: #f9f9f9;
        border-radius: 12px;
        box-shadow: rgba(50, 50, 93, 0.25) 0px 13px 27px -5px, rgba(0, 0, 0, 0.3) 0px 8px 16px -8px;
    }

    h2 {
        text-align: center;
        color: #333;
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
    }

    label {
        font-size: 14px;
        color: #555;
        margin-bottom: 8px;
        display: block;
        font-weight: 600;
    }

    select,
    input[type="number"] {
        width: 100%;
        padding: 12px;
        border: 1px solid #847b7b;
        border-radius: 3px;
        font-size: 14px;
        color: #333;
        background-color: #ffffff;
        transition: border-color 0.3s;
        height: 30px;
    }

    select:focus,
    input[type="number"]:focus {
        border-color: #80bdff;
        background-color: #fff;
        outline: none;
    }

    button.btn-primary {
        display: block;
        margin: 0 auto;
        padding: 12px 20px;
        font-size: 12px;
        background-color: #00a37a;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
    }

    button.btn-primary:hover {
        background-color: #218838;
    }
</style>