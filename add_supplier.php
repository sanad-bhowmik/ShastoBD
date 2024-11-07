<?php
include_once("include/initialize.php");
include_once("include/header.php");

$dbhost = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "shasthobdapi";

$conn = new mysqli($dbhost, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$alertType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['edit_supplier_id'])) {
        // Edit supplier
        $id = $_POST['edit_supplier_id'];
        $name = $_POST['edit_supplier_name'];
        $address = $_POST['edit_supplier_address'];
        $phone = $_POST['edit_supplier_phone'];

        $stmt = $conn->prepare("UPDATE suppliers SET name = ?, address = ?, phone = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $address, $phone, $id);

        if ($stmt->execute()) {
            $message = "Supplier updated successfully!";
            $alertType = "info"; // Update type to info for toastr
        } else {
            $message = "Error updating supplier: " . $conn->error;
            $alertType = "error";
        }

        $stmt->close();
    } else {
        // Add new supplier
        $name = $_POST['supplier_name'];
        $address = $_POST['address'];
        $phone = $_POST['phone'];
        $status = $_POST['status'];

        $stmt = $conn->prepare("INSERT INTO suppliers (name, address, phone, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $name, $address, $phone, $status);

        if ($stmt->execute()) {
            $message = "Supplier added successfully!";
            $alertType = "info"; // Update type to info for toastr
        } else {
            $message = "Error adding supplier: " . $conn->error;
            $alertType = "error";
        }

        $stmt->close();
    }
}

$sql = "SELECT * FROM suppliers";
$result = $conn->query($sql);
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
                        <div class="card-header">Add Supplier</div>
                        <div class="card-body">
                            <div class="position-relative row form-group">

                                <!-- Invoice Number -->
                                <div class="col-sm-3">
                                    <label for="supplier_name">Supplier Name:</label>
                                    <input type="text" class="form-control" id="supplier_name" name="supplier_name" placeholder="Enter supplier name" required>
                                </div>

                                <!-- Customer Name -->
                                <div class="col-sm-3">
                                    <label for="address">Address:</label>
                                    <input type="text" id="address" class="form-control" name="address" placeholder="Enter address" required>
                                </div>

                                <!-- Customer Phone -->
                                <div class="col-sm-3">
                                    <label for="phone">Phone Number:</label>
                                    <input type="tel" id="phone" class="form-control" name="phone" placeholder="Enter phone number" required>
                                </div>

                                <!-- Created Date -->
                                <div class="col-sm-3">
                                    <label for="status">Status:</label>
                                    <select id="status" class="form-control" name="status" required>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>

                                <div class="col-sm-3">
                                    <button type="submit" class="btn btn-success" style="margin-top: 10px;">Save</button>
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
                        Supplier
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div style="margin-top: 10px; margin-bottom: 25px; display: flex; flex-wrap: wrap; gap: 10px;">
                                <select id="filter_name" class="form-control" style="flex: 1; max-width: 150px;">
                                    <option value="">Select Name</option>
                                    <?php
                                    $result_names = $conn->query("SELECT DISTINCT name FROM suppliers");
                                    while ($row = $result_names->fetch_assoc()) {
                                        echo "<option value='" . htmlspecialchars($row['name']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                                    }
                                    ?>
                                </select>

                                <select id="filter_phone" class="form-control" style="flex: 1; max-width: 150px;">
                                    <option value="">Select Number</option>
                                    <?php
                                    $result_phones = $conn->query("SELECT DISTINCT phone FROM suppliers");
                                    while ($row = $result_phones->fetch_assoc()) {
                                        echo "<option value='" . htmlspecialchars($row['phone']) . "'>" . htmlspecialchars($row['phone']) . "</option>";
                                    }
                                    ?>
                                </select>

                                <button id="clearBtn" class="btn btn-danger" style="flex: 0 0 auto; height: 38px;">Clear</button>
                            </div>

                            <table class="align-middle mb-0 table table-borderless table-striped table-hover" id="supplierTable">
                                <thead>
                                    <tr>
                                        <th class="text-center">Sl</th>
                                        <th class="text-center">Supplier Name</th>
                                        <th class="text-center">Address</th>
                                        <th class="text-center">Phone</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="stockTableBody">
                                    <?php
                                    $sql = "SELECT * FROM suppliers";
                                    $result = $conn->query($sql);

                                    if ($result->num_rows > 0) {
                                        $index = 1;
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr data-id='" . $row['id'] . "'>";
                                            echo "<td>" . $index++ . "</td>";
                                            echo "<td class='supplier-name'>" . $row['name'] . "</td>";
                                            echo "<td class='supplier-address'>" . $row['address'] . "</td>";
                                            echo "<td class='supplier-phone'>" . $row['phone'] . "</td>";
                                            echo "<td>" . ($row['status'] == 1 ? 'Active' : 'Inactive') . "</td>";
                                            echo "<td>
                        <button class='edit-btn btn btn-info'>Edit </button>
                        <button class='save-btn btn btn-success' style='display: none;'>
                            <span>SAVE</span>
                        </button>
                      </td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6'>No suppliers found</td></tr>";
                                    }
                                    ?>
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

            $('#clearBtn').on('click', function(e) {
                e.preventDefault(); // Prevent form submission
                $('#filter_name').val('').trigger('change'); // Clear name filter
                $('#filter_phone').val('').trigger('change'); // Clear phone filter
                $('#supplierTable tbody tr').show(); // Show all suppliers
            });

            $('#filter_name').on('change', function() {
                filterTable();
            });

            $('#filter_phone').on('change', function() {
                filterTable();
            });

            function filterTable() {
                var nameFilter = $('#filter_name').val();
                var phoneFilter = $('#filter_phone').val();
                $('#supplierTable tbody tr').filter(function() {
                    $(this).toggle(
                        ($(this).children('td:nth-child(2)').text().indexOf(nameFilter) > -1 || nameFilter === "") &&
                        ($(this).children('td:nth-child(4)').text().indexOf(phoneFilter) > -1 || phoneFilter === "")
                    );
                });
            }

            // Edit button functionality
            $(document).on('click', '.edit-btn', function() {
                var row = $(this).closest('tr');
                var supplierId = row.data('id');

                // Show inputs for editing
                row.find('.supplier-name').html('<input type="text" value="' + row.find('.supplier-name').text() + '">');
                row.find('.supplier-address').html('<input type="text" value="' + row.find('.supplier-address').text() + '">');
                row.find('.supplier-phone').html('<input type="text" value="' + row.find('.supplier-phone').text() + '">');

                row.find('.edit-btn').hide(); // Hide edit button
                row.find('.save-btn').show(); // Show save button
            });

            // Save button functionality
            $(document).on('click', '.save-btn', function() {
                var row = $(this).closest('tr');
                var supplierId = row.data('id');

                var name = row.find('.supplier-name input').val();
                var address = row.find('.supplier-address input').val();
                var phone = row.find('.supplier-phone input').val();

                $.ajax({
                    url: 'add_supplier.php',
                    type: 'POST',
                    data: {
                        edit_supplier_id: supplierId,
                        edit_supplier_name: name,
                        edit_supplier_address: address,
                        edit_supplier_phone: phone
                    },
                    success: function(response) {
                        // Update the row with the new values
                        row.find('.supplier-name').text(name);
                        row.find('.supplier-address').text(address);
                        row.find('.supplier-phone').text(phone);

                        row.find('.edit-btn').show(); // Show edit button
                        row.find('.save-btn').hide(); // Hide save button

                        // Show Toastr success message
                        toastr.info('Supplier updated successfully!');
                    },
                    error: function(xhr, status, error) {
                        toastr.error('Error updating supplier');
                    }
                });
            });
        });
    </script>
</body>

</html>