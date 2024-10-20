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
    <div class="container">
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
                <div class="form-group" style="margin-top: 19px;">
                    <button type="submit" class="btn-primary">Save</button>
                </div>
            </div>
        </form>

        <!-- Toastr messages -->
        <?php if (!empty($message)): ?>
            <script>
                toastr.<?php echo $alertType; ?>('<?php echo $message; ?>');
            </script>
        <?php endif; ?>
    </div>

    <div class="container" style="margin: -28px auto;">
        <h2>Suppliers List</h2>

        <!-- Filters for suppliers -->
        <div style="margin-bottom: 10px; display: flex; gap: 12px;">
            <div class="form-group">
                <select id="filter_name" class="select2" style="width: 100%;">
                    <option value="">Select Name</option>
                    <?php
                    $result_names = $conn->query("SELECT DISTINCT name FROM suppliers");
                    while ($row = $result_names->fetch_assoc()) {
                        echo "<option value='" . $row['name'] . "'>" . $row['name'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <select id="filter_phone" class="select2" style="width: 100%;">
                    <option value="">Select Number</option>
                    <?php
                    $result_phones = $conn->query("SELECT DISTINCT phone FROM suppliers");
                    while ($row = $result_phones->fetch_assoc()) {
                        echo "<option value='" . $row['phone'] . "'>" . $row['phone'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <button id="clearBtn" class="btn-primary" style="background-image: radial-gradient(circle 986.6px at 10% 20%, rgba(251, 6, 6, 0.94) 0%, rgba(3, 31, 213, 1) 82.8%, rgba(248, 101, 248, 1) 87.9%);">Clear</button>
            </div>
        </div>

        <table id="supplierTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Supplier Name</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
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
                        <button class='edit-btn'>Edit 
                            <svg class='svg' viewBox='0 0 512 512'>
                                <path d='M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z'></path>
                            </svg>
                        </button>
                        <button class='save-btn' style='display: none;'>
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
<style>
    .save-btn {
        width: 59px;
        height: 28px;
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 5px;
        background: #183153;
        font-family: "Montserrat", sans-serif;
        box-shadow: 0px 6px 24px 0px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        cursor: pointer;
        border: none;
    }

    .save-btn:after {
        content: " ";
        width: 0%;
        height: 100%;
        background: #ffd401;
        position: absolute;
        transition: all 0.4s ease-in-out;
        right: 0;
    }

    .save-btn:hover::after {
        right: auto;
        left: 0;
        width: 100%;
    }

    .save-btn span {
        text-align: center;
        text-decoration: none;
        width: 100%;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.3em;
        z-index: 20;
        transition: all 0.3s ease-in-out;
    }

    .save-btn:hover span {
        color: #183153;
        animation: scaleUp 0.3s ease-in-out;
    }

    @keyframes scaleUp {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(0.95);
        }

        100% {
            transform: scale(1);
        }
    }

    .edit-btn {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        width: 59px;
        height: 28px;
        border: none;
        padding: 0px 20px;
        background-color: rgb(168, 38, 255);
        color: white;
        font-weight: 500;
        cursor: pointer;
        border-radius: 10px;
        box-shadow: 3px 3px 0px rgb(140, 32, 212);
        transition-duration: .3s;
    }

    .svg {
        width: 10px;
        position: absolute;
        right: 0;
        margin-right: 6px;
        fill: white;
        transition-duration: .3s;
    }

    .edit-btn:hover {
        color: transparent;
    }

    .edit-btn:hover svg {
        right: 43%;
        margin: 0;
        padding: 0;
        border: none;
        transition-duration: .3s;
    }

    .edit-btn:active {
        transform: translate(3px, 3px);
        transition-duration: .3s;
        box-shadow: 2px 2px 0px rgb(140, 32, 212);
    }

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