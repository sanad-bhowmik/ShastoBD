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

// Handle customer addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $customer_name = $_POST['customer_name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];

    if (!empty($customer_name)) {
        // Check for duplicate customer
        $checkQuery = "SELECT COUNT(*) FROM sale_customer WHERE customer_name = ? AND phone = ?";
        if ($stmt = $conn->prepare($checkQuery)) {
            $stmt->bind_param("ss", $customer_name, $phone);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();

            if ($count > 0) {
                $message = "Duplicate customer found! Please use a different name or phone number.";
                $alertType = "warning"; // Set alert type for warning
            } else {
                // Insert new customer
                $query = "INSERT INTO sale_customer (customer_name, address, phone) VALUES (?, ?, ?)";
                if ($stmt = $conn->prepare($query)) {
                    $stmt->bind_param("sss", $customer_name, $address, $phone);
                    $stmt->execute();

                    if ($stmt->affected_rows > 0) {
                        $message = "Customer added successfully!";
                        $alertType = "success"; // Set alert type for success
                    } else {
                        $message = "Failed to add customer. Please try again.";
                        $alertType = "error"; // Set alert type for error
                    }
                    $stmt->close();
                } else {
                    $message = "Database error: Unable to prepare statement.";
                    $alertType = "error"; // Set alert type for error
                }
            }
        }
    } else {
        $message = "Please enter a customer name.";
        $alertType = "error"; // Set alert type for error
    }
}

// Handle customer editing
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $customer_id = $_POST['customer_id'];
    $customer_name = $_POST['customer_name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];

    if (!empty($customer_name) && !empty($customer_id)) {
        $query = "UPDATE sale_customer SET customer_name = ?, address = ?, phone = ? WHERE id = ?";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("ssii", $customer_name, $address, $phone, $customer_id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo json_encode(['status' => 'success', 'message' => 'Customer updated successfully!']);
                exit;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update customer. Please try again.']);
                exit;
            }
            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error: Unable to prepare statement.']);
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Please enter a customer name.']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add/Edit Sale Customer</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
</head>

<body>
    <div class="app-main__inner">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-header">Add Customer</div>
                        <div class="card-body">
                            <div class="position-relative row form-group">

                                <!-- Invoice Number -->
                                <div class="col-sm-3">
                                    <label for="customer_name">Customer Name:</label>
                                    <input type="text" id="customer_name" class="form-control" name="customer_name"
                                        placeholder="Enter customer name" required>
                                </div>

                                <!-- Customer Name -->
                                <div class="col-sm-3">
                                    <label for="address">Address:</label>
                                    <input type="text" id="address" name="address" class="form-control"
                                        placeholder="Enter address">
                                </div>
                                <!-- Customer Name -->
                                <div class="col-sm-3">
                                    <label for="phone">Phone:</label>
                                    <input type="text" id="phone" name="phone" class="form-control"
                                        placeholder="Enter phone number">
                                </div>


                                <div class="col-sm-3">
                                    <button type="submit" class="btn btn-secondary savebtn" style="margin-top: 27px;"
                                        name="action" value="add">Save</button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php if (!empty($message)): ?>
            <script>
                toastr.<?php echo $alertType; ?>('<?php echo $message; ?>');
            </script>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="main-card mb-3 card">
                    <div class="card-header">
                        Customers
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="align-middle mb-0 table table-borderless table-striped table-hover"
                                id="groupTable">
                                <thead>
                                    <tr>
                                        <th class="">#</th>
                                        <th class="">Customer Name</th>
                                        <th class="">Address</th>
                                        <th class="">Phone</th>
                                        <th class="">Date</th>
                                        <th class="">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="categoryTableBody">
                                    <?php
                                    // Fetch customers from the sale_customer table
                                    $result = $conn->query("SELECT id, customer_name, address, phone, created_at FROM sale_customer");

                                    if ($result->num_rows > 0) {
                                        $index = 1;
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . $index++ . "</td>";
                                            echo "<td><span class='customer-name'>" . htmlspecialchars($row['customer_name']) . "</span><input type='text' class='customer-input' value='" . htmlspecialchars($row['customer_name']) . "' style='display:none;'></td>";
                                            echo "<td><span class='customer-address'>" . htmlspecialchars($row['address']) . "</span><input type='text' class='address-input' value='" . htmlspecialchars($row['address']) . "' style='display:none;'></td>";
                                            echo "<td><span class='customer-phone'>" . htmlspecialchars($row['phone']) . "</span><input type='text' class='phone-input' value='" . htmlspecialchars($row['phone']) . "' style='display:none;'></td>";
                                            echo "<td>" . date('Y-m-d', strtotime($row['created_at'])) . "</td>";
                                            echo "<td>
                                <button class='btn btn-info edit-btn' data-id='" . $row['id'] . "'>Edit</button>
                                <button class='btn btn-success save-btn' style='display: none;' data-id='" . $row['id'] . "'>Save</button>
                              </td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6'>No customers found</td></tr>";
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
        $(document).ready(function () {
            // Edit button functionality
            $('.edit-btn').on('click', function () {
                var row = $(this).closest('tr');
                row.find('.customer-name').hide();
                row.find('.customer-input').show();
                row.find('.customer-address').hide();
                row.find('.address-input').show();
                row.find('.customer-phone').hide();
                row.find('.phone-input').show();
                $(this).hide();
                row.find('.save-btn').show();
            });

            // Save button functionality
            $('.save-btn').on('click', function () {
                var row = $(this).closest('tr');
                var customerId = $(this).data('id');
                var customerName = row.find('.customer-input').val();
                var address = row.find('.address-input').val();
                var phone = row.find('.phone-input').val();
                var status = row.find('.status-input').val(); // Get status value

                $.ajax({
                    url: '', // URL for the current page
                    type: 'POST',
                    data: {
                        action: 'edit',
                        customer_id: customerId,
                        customer_name: customerName,
                        address: address,
                        phone: phone,
                        status: status // Send status value
                    },
                    success: function (response) {
                        response = JSON.parse(response);
                        if (response.status === 'success') {
                            toastr.success(response.message);
                            // Update the row data
                            row.find('.customer-name').text(customerName).show();
                            row.find('.customer-input').hide();
                            row.find('.customer-address').text(address).show();
                            row.find('.address-input').hide();
                            row.find('.customer-phone').text(phone).show();
                            row.find('.phone-input').hide();
                            row.find('.edit-btn').show();
                            row.find('.save-btn').hide();
                            // Update status display
                            var statusText = (status == 1) ? 'Active' : 'Inactive';
                            var statusClass = (status == 1) ? 'badge-success' : 'badge-danger';
                            row.find('td:nth-child(5)').html(`<span class='badge ${statusClass}' style='padding: 5px; color: white;'>${statusText}</span>`);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function () {
                        toastr.error('Error occurred while updating the customer.');
                    }
                });
            });
        });
    </script>
</body>

</html>