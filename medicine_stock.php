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
    die("Connection failed: Please contact the administrator.");
}

$message = "";
$alertType = "";

// Handle form submission for new stock entry
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['supplier_name'])) {
    $supplier_id = $_POST['supplier_name'];
    $cat_id = $_POST['address'];
    $medicine_id = $_POST['phone'];
    $InQty = $_POST['status'];
    $rate = $_POST['rate'];  // New Rate variable

    // Insert data into stock_in table with the new rate column
    $stmt = $conn->prepare("INSERT INTO stock_in (supplier_id, cat_id, medicine_id, InQty, rate, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("iiiii", $supplier_id, $cat_id, $medicine_id, $InQty, $rate);

    if ($stmt->execute()) {
        $message = "Stock added successfully!";
        $alertType = "success";
    } else {
        $message = "Error adding stock: " . $stmt->error;
        $alertType = "error";
    }

    $stmt->close();
}

// Handle update for existing stock entry
if (isset($_POST['update_id'])) {
    $update_id = $_POST['update_id'];
    $InQty = $_POST['InQty'];
    $rate = $_POST['rate'];
    $OutQty = $_POST['OutQty'];

    // Update the stock_in table with new values
    $stmt = $conn->prepare("UPDATE stock_in SET InQty = ?, rate = ?, OutQty = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("iiii", $InQty, $rate, $OutQty, $update_id);

    if ($stmt->execute()) {
        $message = "Stock updated successfully!";
        $alertType = "success";
    } else {
        $message = "Error updating stock: " . $stmt->error;
        $alertType = "error";
    }

    $stmt->close();
}

// Fetch suppliers, categories, and medicines for dropdowns
$supplierResult = $conn->query("SELECT id, name FROM suppliers");
$addressResult = $conn->query("SELECT id, name FROM medicine_category");
$phoneResult = $conn->query("SELECT id, name FROM medicine");

$filterQuery = "SELECT stock.id, s.name AS supplier_name, c.name AS category_name, m.name AS medicine_name, stock.InQty, stock.rate, stock.OutQty, stock.created_at 
                FROM stock_in stock 
                JOIN suppliers s ON stock.supplier_id = s.id 
                JOIN medicine_category c ON stock.cat_id = c.id 
                JOIN medicine m ON stock.medicine_id = m.id";

$conditions = [];
if (!empty($_POST['filter_supplier'])) {
    $supplier_id = intval($_POST['filter_supplier']);
    $conditions[] = "stock.supplier_id = $supplier_id";
}
if (!empty($_POST['filter_category'])) {
    $cat_id = intval($_POST['filter_category']);
    $conditions[] = "stock.cat_id = $cat_id";
}
if (!empty($_POST['filter_medicine'])) {
    $medicine_id = intval($_POST['filter_medicine']);
    $conditions[] = "stock.medicine_id = $medicine_id";
}
if (!empty($_POST['filter_date'])) {
    $filter_date = $_POST['filter_date'];
    if (preg_match("/^\d{4}-\d{2}-\d{2}$/", $filter_date)) {
        $conditions[] = "DATE(stock.created_at) = '$filter_date'";
    } else {
        echo "Invalid date format";
    }
}

if (count($conditions) > 0) {
    $filterQuery .= " WHERE " . implode(" AND ", $conditions);
}

$stockResult = $conn->query($filterQuery);
if (!$stockResult) {
    die("Query failed: " . $conn->error);
}

$conn->close();
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
    <div class="app-main__inner">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-header">Add Medicine Stock</div>
                        <div class="card-body">
                            <div class="position-relative row form-group">

                                <!-- Invoice Number -->
                                <div class="col-sm-3">
                                    <label for="supplier_name">Supplier:</label>
                                    <select id="supplier_name" name="supplier_name" class="form-control" required>
                                        <option value="">Select Supplier</option>
                                        <?php while ($row = $supplierResult->fetch_assoc()): ?>
                                            <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <!-- Customer Name -->
                                <div class="col-sm-3">
                                    <label for="address">Category:</label>
                                    <select id="address" name="address" class="form-control" required>
                                        <option value="">Select Category</option>
                                        <?php while ($row = $addressResult->fetch_assoc()): ?>
                                            <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <!-- Customer Phone -->
                                <div class="col-sm-3">
                                    <label for="phone">Medicine:</label>
                                    <select id="phone" name="phone" class="form-control" required>
                                        <option value="">Select Medicine</option>
                                        <?php while ($row = $phoneResult->fetch_assoc()): ?>
                                            <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <!-- Created Date -->
                                <div class="col-sm-3">
                                    <label for="status">QTY:</label>
                                    <input type="text" id="status" name="status" class="form-control" placeholder="Enter Quantity" required>
                                </div>
                                <div class="col-sm-3">
                                    <label for="rate">Rate:</label>
                                    <input type="text" id="rate" name="rate" class="form-control" placeholder="Enter Rate" required>
                                </div>
                                <div class="col-sm-3">
                                    <button type="submit" class="btn btn-success" style="margin-top: 12%;">Save</button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="row">
            <div class="col-md-12">
                <div class="main-card mb-3 card">
                    <div class="card-header">
                        Medicine Stock

                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div class="filter-container">
                                <input type="text" id="filter_supplier" placeholder="Filter Supplier" class="form-control filter-input">
                                <input type="text" id="filter_category" placeholder="Filter Category" class="form-control filter-input">
                                <input type="text" id="filter_medicine" placeholder="Filter Medicine" class="form-control filter-input">
                                <button id="clearBtn" class="btn btn-danger">Clear</button>
                            </div>

                            <style>
                                .filter-container {
                                    display: flex;
                                    flex-wrap: wrap;
                                    gap: 10px;
                                    margin-top: 10px;
                                    margin-bottom: 25px;
                                }

                                .filter-input {
                                    flex: 1;
                                    min-width: 150px;
                                }

                                @media (max-width: 600px) {
                                    .filter-container {
                                        flex-direction: column;
                                    }

                                    .filter-input,
                                    #clearBtn {
                                        width: 100%;
                                    }
                                }
                            </style>

                            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">Sl</th>
                                        <th class="text-center">Supplier Name</th>
                                        <th class="text-center">Category Name</th>
                                        <th class="text-center">Medicine Name</th>
                                        <th class="text-center">InQty</th>
                                        <th class="text-center">Rate</th>
                                        <th class="text-center">OutQty</th>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="stockTableBody">
                                    <?php
                                    // Initialize index counter for stock entries
                                    $stockIndex = 1;

                                    // Fetching stock entries first and adding them to the table
                                    while ($row = $stockResult->fetch_assoc()): ?>
                                        <tr style="border-bottom: 1px solid;" id="row-<?php echo $row['id']; ?>">
                                            <td class="text-center"><?php echo $stockIndex++; ?></td> <!-- Index for stock entries -->
                                            <td><?php echo $row['supplier_name']; ?></td>
                                            <td><?php echo $row['category_name']; ?></td>
                                            <td><?php echo $row['medicine_name']; ?></td>
                                            <td class="editable" data-field="InQty" data-id="<?php echo $row['id']; ?>">
                                                <span class="value"><?php echo $row['InQty']; ?></span>
                                                <input type="text" class="input-field" value="<?php echo $row['InQty']; ?>" style="display:none;">
                                            </td>
                                            <td class="editable" data-field="rate" data-id="<?php echo $row['id']; ?>">
                                                <span class="value"><?php echo $row['rate']; ?></span>
                                                <input type="text" class="input-field" value="<?php echo $row['rate']; ?>" style="display:none;">
                                            </td>
                                            <td class="editable" data-field="OutQty" data-id="<?php echo $row['id']; ?>">
                                                <span class="value"><?php echo $row['OutQty']; ?></span>
                                                <input type="text" class="input-field" value="<?php echo $row['OutQty']; ?>" style="display:none;">
                                            </td>
                                            <td><?php echo $row['created_at']; ?></td>
                                            <td>
                                                <button class="edit-btn btn btn-secondary" data-id="<?php echo $row['id']; ?>">Edit</button>
                                                <button class="save-btn btn btn-success" data-id="<?php echo $row['id']; ?>" style="display:none;">Save</button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script>
        $(document).ready(function() {
            $('.select2').select2();

            $("#clearBtn").click(function(e) {
                e.preventDefault();
                $("select, #filter_date").val("");
                $("form")[0].submit();
            });

            $(".edit-btn").click(function() {
                var rowId = $(this).data("id");
                $("#row-" + rowId + " .value").hide();
                $("#row-" + rowId + " .input-field").show();
                $(this).hide();
                $("#row-" + rowId + " .save-btn").show();
            });

            $(".save-btn").click(function() {
                var rowId = $(this).data("id");
                var InQty = $("#row-" + rowId + " .editable[data-field='InQty'] .input-field").val();
                var rate = $("#row-" + rowId + " .editable[data-field='rate'] .input-field").val();
                var OutQty = $("#row-" + rowId + " .editable[data-field='OutQty'] .input-field").val();

                // Create a form to send the update request
                $.post("", {
                    update_id: rowId,
                    InQty: InQty,
                    rate: rate,
                    OutQty: OutQty
                }, function(response) {
                    toastr.success('Stock updated successfully!');
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                });
            });
        });
        $(document).ready(function() {
            // Filter functionality for the table
            $("#filter_supplier, #filter_category, #filter_medicine").on("input", function() {
                var supplierFilter = $("#filter_supplier").val().toLowerCase();
                var categoryFilter = $("#filter_category").val().toLowerCase();
                var medicineFilter = $("#filter_medicine").val().toLowerCase();

                $("#stockTableBody tr").filter(function() {
                    $(this).toggle(
                        ($(this).find("td:eq(1)").text().toLowerCase().indexOf(supplierFilter) > -1 || supplierFilter === "") &&
                        ($(this).find("td:eq(2)").text().toLowerCase().indexOf(categoryFilter) > -1 || categoryFilter === "") &&
                        ($(this).find("td:eq(3)").text().toLowerCase().indexOf(medicineFilter) > -1 || medicineFilter === "")
                    );
                });
            });
            $("#clearBtn").click(function() {
                // Reload the page
                location.reload();
            });
        });
    </script>
</body>

</html>