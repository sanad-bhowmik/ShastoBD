<?php
include_once("include/initialize.php");
include_once("include/header.php");

$dbhost = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "shasthobdapi";

$conn = new mysqli($dbhost, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$alertType = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['supplier_name'])) {
    $supplier_id = $_POST['supplier_name'];
    $cat_id = $_POST['address'];
    $medicine_id = $_POST['phone'];
    $InQty = $_POST['status'];

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

// Fetch suppliers, categories, and medicines for dropdowns
$supplierResult = $conn->query("SELECT id, name FROM suppliers");
$addressResult = $conn->query("SELECT id, name FROM medicine_category");
$phoneResult = $conn->query("SELECT id, name FROM medicine");

// Initialize filter query
$filterQuery = "SELECT s.name AS supplier_name, c.name AS category_name, m.name AS medicine_name, stock.InQty, stock.created_at 
                FROM stock_in stock 
                JOIN suppliers s ON stock.supplier_id = s.id 
                JOIN medicine_category c ON stock.cat_id = c.id 
                JOIN medicine m ON stock.medicine_id = m.id";

// Apply filters if needed
$conditions = [];
if (!empty($_POST['filter_supplier'])) {
    $supplier_id = intval($_POST['filter_supplier']);  // Sanitize input
    $conditions[] = "stock.supplier_id = $supplier_id";
}
if (!empty($_POST['filter_category'])) {
    $cat_id = intval($_POST['filter_category']);  // Sanitize input
    $conditions[] = "stock.cat_id = $cat_id";
}
if (!empty($_POST['filter_medicine'])) {
    $medicine_id = intval($_POST['filter_medicine']);  // Sanitize input
    $conditions[] = "stock.medicine_id = $medicine_id";
}
if (!empty($_POST['filter_date'])) {
    $filter_date = $_POST['filter_date'];  // Sanitize input
    $conditions[] = "DATE(stock.created_at) = '$filter_date'";
}

// Add conditions to the filter query if there are any
if (count($conditions) > 0) {
    $filterQuery .= " WHERE " . implode(" AND ", $conditions);
}

$stockResult = $conn->query($filterQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Supplier</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
</head>

<body>
    <div class="container">
        <form method="POST" action="">
            <div class="flex-container">
                <div class="form-group">
                    <label for="supplier_name">Supplier:</label>
                    <select id="supplier_name" name="supplier_name" required>
                        <option value="">Select Supplier</option>
                        <?php while ($row = $supplierResult->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="address">Category:</label>
                    <select id="address" name="address" required>
                        <option value="">Select Category</option>
                        <?php while ($row = $addressResult->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="phone">Medicine:</label>
                    <select id="phone" name="phone" required>
                        <option value="">Select Medicine</option>
                        <?php while ($row = $phoneResult->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">QTY:</label>
                    <input type="text" id="status" name="status" placeholder="" required>
                </div>
                <div class="form-group" style="margin-top: 19px;">
                    <button type="submit" class="btn-primary">Save</button>
                </div>
            </div>
        </form>

        <?php if (!empty($message)): ?>
            <script>
                toastr.<?php echo $alertType; ?>('<?php echo $message; ?>');
            </script>
        <?php endif; ?>
    </div>

    <div class="container" style="margin: -28px auto;">
        <h2>Stock List</h2>
        <form method="POST" action="">
            <div style="margin-bottom: 10px; display: flex; flex-wrap: wrap; gap: 12px;">
                <div class="form-group" style="">
                    <select id="filter_supplier" name="filter_supplier" class="select2" style="width: 100%;">
                        <option value="">Select Supplier</option>
                        <?php
                        $result_suppliers = $conn->query("SELECT DISTINCT s.id, s.name FROM stock_in stock JOIN suppliers s ON stock.supplier_id = s.id");
                        while ($row = $result_suppliers->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group" style="">
                    <select id="filter_category" name="filter_category" class="select2" style="width: 100%;">
                        <option value="">Select Category</option>
                        <?php
                        $result_categories = $conn->query("SELECT DISTINCT c.id, c.name FROM stock_in stock JOIN medicine_category c ON stock.cat_id = c.id");
                        while ($row = $result_categories->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group" style="">
                    <select id="filter_medicine" name="filter_medicine" class="select2" style="width: 100%;">
                        <option value="">Select Medicine</option>
                        <?php
                        $result_medicines = $conn->query("SELECT DISTINCT m.id, m.name FROM stock_in stock JOIN medicine m ON stock.medicine_id = m.id");
                        while ($row = $result_medicines->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group" style="">
                    <input type="date" id="filter_date" name="filter_date" class="form-control" style="" />
                </div>
                <div class="form-group" style="">
                    <button type="submit" class="btn-primary">Filter</button>
                    <button id="clearBtn" class="btn-primary" style="background-image: radial-gradient(circle 986.6px at 10% 20%, rgba(251, 6, 6, 0.94) 0%, rgba(194, 4, 4, 0.94) 60%);">Clear</button>
                </div>
            </div>
        </form>

        <style>
            @media (max-width: 600px) {
                .form-group {
                    flex: 1 1 48%;

                }

                .form-group:nth-child(odd) {
                    margin-right: 4%;
                    /* Add some spacing between odd and even elements */
                }

                .form-group:last-child {
                    flex: 1 1 100%;
                    /* Make the buttons take the full width */
                }

                #filter_date {
                    width: 96%;
                    border: 1px solid #b0a8a8;
                    height: 29px;
                    border-radius: 5px;
                    background-color: white;
                    color: black;
                }
            }

            @media (min-width: 1024px) {
                #filter_date {
                    width: 100%;
                    border: 1px solid #b0a8a8;
                    height: 29px;
                    border-radius: 5px;
                    background-color: white;
                    color: black;
                }
            }
        </style>

        <table class="table" style="width: 100%; border-collapse: collapse; border-spacing: 0;">
            <thead style="background-color: #9ad8d6; text-align: center;">
                <tr>
                    <th>Supplier Name</th>
                    <th>Category Name</th>
                    <th>Medicine Name</th>
                    <th>InQty</th>
                    <th>Created_at</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stockResult->fetch_assoc()): ?>
                    <tr style="border-bottom: 1px solid;">
                        <td><?php echo $row['supplier_name']; ?></td>
                        <td><?php echo $row['category_name']; ?></td>
                        <td><?php echo $row['medicine_name']; ?></td>
                        <td><?php echo $row['InQty']; ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>

<script>
    $(document).ready(function() {
        $('.select2').select2();

        $('#clearBtn').click(function() {
            $('#filter_supplier').val('').trigger('change');
            $('#filter_category').val('').trigger('change');
            $('#filter_medicine').val('').trigger('change');
            $('#filter_date').val('');
        });
    });
</script>

<style>
    .container {
        max-width: 97%;
        margin: 50px auto;
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: rgba(50, 50, 93, 0.25) 0px 13px 27px -5px, rgba(0, 0, 0, 0.3) 0px 8px 16px -8px;
    }

    h2 {
        text-align: center;
        color: #333;
        font-size: 18px;
        margin-bottom: 20px;
    }

    .flex-container {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }

    .form-group {
        width: 19%;
    }

    label {
        font-size: 12px;
        color: #555;
        margin-bottom: 6px;
        display: block;
        font-weight: 600;
    }

    input[type="text"],
    input[type="tel"],
    select {
        width: 100%;
        padding: 6px 8px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        color: #333;
        background-color: #f9f9f9;
        transition: all 0.3s ease;
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
        width: 28%;
        padding: 6px 8px;
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        background-image: radial-gradient(circle farthest-corner at 10% 20%, rgba(14, 174, 87, 1) 0%, rgba(12, 116, 117, 1) 90%);
        transition: background-color 0.3s ease;
    }

    button.btn-primary:hover {
        background-color: #218838;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 30px;
    }

    table th,
    table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    table th {
        background-color: #f2f2f2;
        font-size: 14px;
    }

    table td {
        font-size: 12px;
    }

    table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    table tr:hover {
        background-color: #f1f1f1;
    }

    @media (max-width: 768px) {
        .form-group {
            width: 45%;
        }

        button.btn-primary {
            width: 45%;
            margin-bottom: 10px ;
        }
    }

    @media (max-width: 480px) {
        .form-group {
            width: 100%;
        }

        button.btn-primary {
            width: 100%;
        }
    }
</style>