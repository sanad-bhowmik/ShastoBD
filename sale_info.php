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

// Fetch medicines for dropdown
$medicines = [];
$result_medicines = $conn->query("SELECT id, name FROM medicine");
if ($result_medicines->num_rows > 0) {
    while ($row = $result_medicines->fetch_assoc()) {
        $medicines[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Sale Info</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <style>

    </style>
</head>

<body>
    <div class="container">
        <h2>Add Sale Info</h2>
        <form id="saleForm" method="POST" action="" style="margin-bottom: 49px;">
            <div class="flex-container">
                <!-- Medicine Name Dropdown -->
                <div class="form-group" style="margin-right: -13%;">
                    <label for="medicine_name">Medicine Name:</label>
                    <select id="medicine_name" name="medicine_name" required class="select2">
                        <option value="">Select Medicine</option>
                        <?php foreach ($medicines as $medicine): ?>
                            <option value="<?php echo $medicine['id']; ?>"><?php echo htmlspecialchars($medicine['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Unit Price Input -->
                <div class="form-group" style="margin-right: -18%;">
                    <label for="unit_price">Unit Price:</label>
                    <input type="number" id="unit_price" name="unit_price" style="border: 1px solid #778899b5;height: 31px;width: 86%;border-radius: 5px;" placeholder="Enter unit price" required>
                </div>

                <!-- Quantity Input -->
                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" placeholder="Enter quantity" style="border: 1px solid #778899b5;height: 31px;width: 86%;border-radius: 5px;" required>
                </div>

                <!-- Add Button -->
                <div class="form-group">
                    <button type="button" class="btn-green" id="addBtn">Add</button>
                </div>
            </div>
        </form>

        <h2>Added Items</h2>
        <table id="addedItemsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Medicine Name</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                </tr>
            </thead>
            <tbody>
                <!-- Added items will be populated here -->
            </tbody>
        </table>

        <!-- Total Price, Discount, Payable Section -->
        <div class="flex-container" style="margin-top: 15px; display: flex; justify-content: end; align-items: center;">
            <div class="form-group" style="width: 15%;">
                <label for="total_price">Total Price:</label>
                <input type="text" id="total_price" name="total_price" placeholder="00" readonly style="width: 100%;">
            </div>
            <div class="form-group" style="width: 15%;">
                <label for="discount">Discount:</label>
                <input type="number" id="discount" name="discount" value="0" style="width: 100%;">
            </div>
            <div class="form-group" style="width: 15%;">
                <label for="payable">Payable:</label>
                <input type="text" id="payable" name="payable" placeholder="00" readonly style="width: 100%;">
            </div>
            <div class="form-group" style="width: 6%;margin-top: 17px;">
                <button type="button" class="btn-danger" id="saveBtn" style="width: 100%;">Save</button>
            </div>

        </div>

    </div>

    <script>
        $(document).ready(function() {
            $('.select2').select2(); // Initialize select2 for dropdowns

            let totalPrice = 0;

            // Handle Add button click
            $('#addBtn').on('click', function() {
                var medicineName = $('#medicine_name option:selected').text();
                var unitPrice = parseFloat($('#unit_price').val());
                var quantity = parseInt($('#quantity').val());

                if (medicineName && unitPrice && quantity) {
                    var itemTotal = unitPrice * quantity;
                    totalPrice += itemTotal;

                    $('#addedItemsTable tbody').append(
                        `<tr>
                    <td>${$('#addedItemsTable tbody tr').length + 1}</td>
                    <td>${medicineName}</td>
                    <td>${quantity}</td>
                    <td>${unitPrice}</td>
                </tr>`
                    );

                    $('#total_price').val(totalPrice.toFixed(2));
                    $('#saleForm')[0].reset();
                    $('#medicine_name').val('').trigger('change');
                    updatePayable();
                } else {
                    toastr.error('Please fill in all fields.');
                }
            });

            $('#discount').on('input', function() {
                updatePayable();
            });

            function updatePayable() {
                var discount = parseFloat($('#discount').val()) || 0;
                var payable = totalPrice - discount;
                $('#payable').val(payable.toFixed(2));
            }

            // Handle Save button click
            $('#saveBtn').on('click', function() {
                var rows = $('#addedItemsTable tbody tr');
                var total_price = $('#total_price').val();
                var discount = $('#discount').val();
                var payable = $('#payable').val();

                if (rows.length === 0) {
                    toastr.error('No items added.');
                    return;
                }

                var saleData = [];

                rows.each(function() {
                    var row = $(this);
                    var item = {
                        medicine_name: row.find('td:eq(1)').text(),
                        quantity: row.find('td:eq(2)').text(),
                        unit_price: row.find('td:eq(3)').text(),
                        total_price: total_price,
                        discount: discount,
                        payable: payable
                    };
                    saleData.push(item);
                });

                $.ajax({
                    url: 'save_sale_info.php',
                    method: 'POST',
                    data: {
                        saleData: saleData
                    },
                    success: function(response) {
                        console.log('Raw server response:', response); // Log raw response to see if it's HTML
                        try {
                            response = JSON.parse(response);
                            if (response.success) {
                                toastr.success('Successfully Added');
                                $('#addedItemsTable tbody').empty();
                                $('#total_price').val('00');
                                $('#discount').val('0');
                                $('#payable').val('00');
                            } else {
                                toastr.error('Error saving data: ' + response.message);
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            toastr.error('Error parsing response.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error status:', status);
                        console.error('AJAX error:', error);
                        console.error('Full response:', xhr.responseText);
                        toastr.error('AJAX error: ' + error);
                    }
                });

            });
        });
    </script>
</body>

</html>


<style>
    h2 {
        text-align: center;
        margin-bottom: 20px;
    }

    .flex-container {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .form-group {
        width: 30%;
        margin-bottom: 15px;
    }

    label {
        margin-bottom: 5px;
        display: block;
    }

    input[type="text"],
    input[type="number"],
    select {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .btn-green {
        padding: 10px;
        color: #fff;
        background-color: green;
        border: none;
        border-radius: 4px;
        cursor: pointer;
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

    .total-row {
        font-weight: bold;
    }

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
            margin-left: -38%;
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
            margin-left: -101%;
            margin-top: 22px;
        }

        .btn-red {
            margin-left: -90%;
        }

        .btn-warning {
            margin-left: -17%;
        }
    }
</style>