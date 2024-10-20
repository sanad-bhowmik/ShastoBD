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
    <div class="container">
        <form method="POST" action="">
            <div class="flex-container">
                <!-- Customer Name Input -->
                <div class="form-group" style="margin-right: -9%;">
                    <label for="customer_name">Customer Name:</label>
                    <input type="text" id="customer_name" name="customer_name" placeholder="Enter customer name" required>
                </div>

                <!-- Address Input -->
                <div class="form-group" style="margin-left: 0%;margin-right: -9%;">
                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" placeholder="Enter address">
                </div>

                <!-- Phone Input -->
                <div class="form-group" style="  margin-left: -1%;">
                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" name="phone" placeholder="Enter phone number">
                </div>

                <!-- Save Button -->
                <div class="form-group">
                    <button type="submit" class="btn-green savebtn" name="action" value="add">Save</button>
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
        <h2>Sale Customers List</h2>

        <table id="customerTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer Name</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
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
                                <button class='edit-btn' data-id='" . $row['id'] . "'>Edit</button>
                                <button class='save-btn' style='display: none;' data-id='" . $row['id'] . "'>Save</button>
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


    <script>
        $(document).ready(function() {
            // Edit button functionality
            $('.edit-btn').on('click', function() {
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
            $('.save-btn').on('click', function() {
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
                    success: function(response) {
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
                    error: function() {
                        toastr.error('Error occurred while updating the customer.');
                    }
                });
            });
        });
    </script>
</body>

</html>



<style>
    .badge-success {
        background-color: green;
    }

    .badge-danger {
        background-color: red;
    }

    .container {
        max-width: 97%;
        margin: 50px auto;
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: rgba(50, 50, 93, 0.25) 0px 13px 27px -5px,
            rgba(0, 0, 0, 0.3) 0px 8px 16px -8px;
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
    select {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
        box-sizing: border-box;
    }

    .btn-green {
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

    .btn-red {
        display: inline-block;
        width: 28%;
        padding: 6px 8px;
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        background-image: radial-gradient(circle 986.6px at 10% 20%, rgba(251, 6, 6, 0.94) 0%, rgba(3, 31, 213, 1) 82.8%, rgba(248, 101, 248, 1) 87.9%);
        transition: background-color 0.3s ease;
    }

    .btn-green:hover {
        background-color: #0056b3;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th,
    td {
        padding: 10px;
        text-align: left;
        border: 1px solid #ccc;
    }

    th {
        background-color: #f2f2f2;
    }

    /* Media query for mobile responsiveness */
    @media (max-width: 768px) {
        .form-group {
            width: 100%;
            /* Full width on small screens */
        }

        .flex-container {
            flex-direction: column;
            /* Stack elements on top of each other */
        }

        .btn-green {
            width: 100%;
        }

        .btn-warning {
            width: 100%;
        }

        .btn-red {
            width: 100%;
        }

        #filter_date {
            margin-left: 5px;
            width: 98%;
        }
    }

    @media (min-width: 1024px) {
        .status {
            margin-left: -48%;
        }

        .savebtn {
            margin-left: -204%;
            margin-top: 19px;
        }

        #filter_date {
            width: 82%;
            text-align: center;
            border: 1px solid #979797;
            height: 29px;
            border-radius: 3px;
        }

        .btn-green {
            margin-left: -63%;
        }

        .btn-red {
            margin-left: -90%;
        }

        .btn-warning {
            margin-left: -17%;
        }
    }
</style>