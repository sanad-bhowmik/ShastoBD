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
    $name = $_POST['supplier_name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO suppliers (name, address, phone, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $name, $address, $phone, $status);

    if ($stmt->execute()) {
        $message = "Supplier added successfully!";
        $alertType = "success";
    } else {
        $message = "Error adding supplier: " . $conn->error;
        $alertType = "error";
    }

    $stmt->close();
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

        <?php if (!empty($message)): ?>
            <script>
                toastr.<?php echo $alertType; ?>('<?php echo $message; ?>');
            </script>
        <?php endif; ?>
    </div>

    <div class="container" style="margin: -28px auto;">
        <h2>Suppliers List</h2>

        <div style="margin-bottom: 10px;display: flex;gap: 12px;">
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
                <!-- <button id="searchBtn" class="btn-primary">Search</button> -->
                <button id="clearBtn" class="btn-primary" style="  background-image: radial-gradient(circle 986.6px at 10% 20%, rgba(251, 6, 6, 0.94) 0%, rgba(3, 31, 213, 1) 82.8%, rgba(248, 101, 248, 1) 87.9%);">Clear</button>
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
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT name, address, phone, status FROM suppliers";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $index = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $index++ . "</td>";
                        echo "<td>" . $row['name'] . "</td>";
                        echo "<td>" . $row['address'] . "</td>";
                        echo "<td>" . $row['phone'] . "</td>";
                        echo "<td>" . ($row['status'] == 1 ? 'Active' : 'Inactive') . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No suppliers found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $('.select2').select2();

            $('#searchBtn').on('click', function(e) {
                e.preventDefault(); // Prevent form submission
                filterTable();
            });

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
        });
    </script>
</body>

</html>

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