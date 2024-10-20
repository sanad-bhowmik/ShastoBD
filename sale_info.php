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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>

    </style>
</head>

<body>
    <div class="container" style="display: flex;justify-content: space-between;margin: 20px;height: 76%;">
        <div class="form-container" style="flex: 1; margin-right: 20px; padding: 20px; border-radius: 8px; background-color: #f9f9f9; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
            <h2>Add Sale Info</h2>
            <form id="saleForm" method="POST" action="" style="margin-bottom: 49px;">
                <div class="flex-container">
                    <!-- Medicine Name Dropdown -->
                    <div class="form-group" style="min-width: 200px;">
                        <label for="medicine_name">Medicine Name:</label>
                        <select id="medicine_name" name="medicine_name" required class="select2" style="width: 100%; border: 1px solid #778899b5; border-radius: 5px;">
                            <option value="">Select Medicine</option>
                            <?php foreach ($medicines as $medicine): ?>
                                <option value="<?php echo $medicine['id']; ?>"><?php echo htmlspecialchars($medicine['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Unit Price Input -->
                    <div class="form-group" style="min-width: 200px;">
                        <label for="unit_price">Unit Price:</label>
                        <input type="number" id="unit_price" name="unit_price" style="border: 1px solid #778899b5;height: 31px;width: 100%;border-radius: 5px;" placeholder="Enter unit price" required>
                    </div>

                    <!-- Quantity Input -->
                    <div class="form-group" style="min-width: 200px;">
                        <label for="quantity">Quantity:</label>
                        <input type="number" id="quantity" name="quantity" placeholder="Enter quantity" style="border: 1px solid #778899b5;height: 31px;width: 100%;border-radius: 5px;" required>
                    </div>

                    <!-- Add Button -->
                    <div class="form-group" style="min-width: 100px; ">
                        <button type="button" class="btn-green" id="addBtn" style="width: 100%;">Add</button>
                    </div>
                </div>
            </form>


        </div>

        <div class="table-container" style="flex: 1; padding: 20px; border-radius: 8px; background-color: #f9f9f9; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
            <h2>Added Items</h2>
            <table id="addedItemsTable" style="width: 100%;border-collapse: collapse;margin-top: 15px;margin-bottom: 10%;">
                <thead>
                    <tr style="background-color: #eaeaea;">
                        <th style="border: 1px solid #ccc; padding: 8px;">#</th>
                        <th style="border: 1px solid #ccc; padding: 8px;">Medicine Name</th>
                        <th style="border: 1px solid #ccc; padding: 8px;">Quantity</th>
                        <th style="border: 1px solid #ccc; padding: 8px;">Unit Price</th>
                    </tr>
                </thead>
                <tbody style="background-color: white;">
                    <!-- Added items will be populated here -->
                </tbody>
            </table>
            <div class="flex-container priceSec" style="margin-top: 15px; display: flex; align-items: center;">
                <div class="form-group">
                    <label for="total_price">Total Price:</label>
                    <input type="text" id="total_price" name="total_price" placeholder="00" readonly style="width: 100%; border: 1px solid #778899b5; border-radius: 5px;">
                </div>
                <div class="form-group">
                    <label for="discount">Discount:</label>
                    <input type="number" id="discount" name="discount" value="0" style="width: 100%; border: 1px solid #778899b5; border-radius: 5px;">
                </div>
                <div class="form-group">
                    <label for="payable">Payable:</label>
                    <input type="text" id="payable" name="payable" placeholder="00" readonly style="width: 100%; border: 1px solid #778899b5; border-radius: 5px;">
                </div>
                <div class="form-group" style="width: 10%; margin-top: 17px;">
                    <button type="button" class="btn-danger" id="saveBtn" style="width: 100%;">Save</button>
                </div>
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
                        <td>${unitPrice.toFixed(2)}</td>
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

                const {
                    jsPDF
                } = window.jspdf; // Using jsPDF
                var doc = new jsPDF();

                // Add Company Logo
                const logo = 'themefiles/assets/images/logo-inverse.png'; // Logo path
                const imgWidth = 50; // Set the desired width for the logo
                const imgHeight = 20; // Set the desired height for the logo
                doc.addImage(logo, 'PNG', 10, 10, imgWidth, imgHeight); // Adjust logo position and size

                // Set the background color to white
                doc.setFillColor(255, 255, 255); // White background
                doc.rect(0, 0, doc.internal.pageSize.width, doc.internal.pageSize.height, 'F'); // Fill background

                // Title
                doc.setFontSize(26);
                doc.setFont('helvetica', 'bold');
                doc.setTextColor(0, 0, 0); // Black text color
                doc.text('Sale Invoice', 70, 30); // Centered title

                // Invoice Information
                doc.setFontSize(12);
                doc.setFont('helvetica', 'normal');
                const date = new Date().toLocaleDateString();
                doc.text(`Invoice Date: ${date}`, 10, 45);
                doc.text(`Total Price: Tk ${total_price}`, 10, 55);
                doc.text(`Discount: Tk ${discount}`, 10, 65);
                doc.text(`Payable: Tk ${payable}`, 10, 75);

                // Draw a line for separation
                doc.setTextColor(0, 0, 0); // Reset to black for line
                doc.line(10, 80, 200, 80); // Draw line from (x1, y1) to (x2, y2)

                // Adding Added Items Header
                doc.setFontSize(14);
                doc.setFont('helvetica', 'bold');
                doc.setTextColor(0, 0, 0); // Black text color
                doc.text('Added Items:', 10, 90);
                doc.setFontSize(12);
                doc.setFont('helvetica', 'bold'); // Set bold font for the header
                doc.text('Medicine Name', 10, 100);
                doc.text('Quantity', 100, 100);
                doc.text('Unit Price', 150, 100);

                // Draw a line for item header
                doc.line(10, 103, 200, 103); // Draw line under the header

                // Reset font for items
                doc.setFont('helvetica', 'normal');
                let yPosition = 108; // Starting position for items

                // Adding added items to the PDF
                rows.each(function() {
                    var row = $(this);
                    var medicineName = row.find('td:eq(1)').text();
                    var quantity = row.find('td:eq(2)').text();
                    var unitPrice = parseFloat(row.find('td:eq(3)').text()).toFixed(2);

                    doc.text(medicineName, 10, yPosition);
                    doc.text(quantity, 100, yPosition);
                    doc.text(`Tk ${unitPrice}`, 150, yPosition);

                    yPosition += 10; // Increment y position for the next item
                });

                // Draw line after items
                doc.line(10, yPosition, 200, yPosition);

                // Add totals and discounts with better layout
                doc.setFont('helvetica', 'bold');
                yPosition += 10; // Move down for totals
                doc.text("Total Price:", 140, yPosition);
                doc.text(`Tk ${total_price}`, 180, yPosition);

                yPosition += 10; // Move down for discount
                doc.text("Discount:", 140, yPosition);
                doc.text(`Tk ${discount}`, 180, yPosition);

                yPosition += 10; // Move down for payable
                doc.text("Payable:", 140, yPosition);
                doc.text(`Tk ${payable}`, 180, yPosition);

                // Draw line before thank you message
                doc.line(10, yPosition + 10, 200, yPosition + 10);

                // Add thank you message at the bottom
                doc.setFontSize(12); // Set smaller font size for the thank you message
                doc.setFont('helvetica', 'italic');
                doc.text('Thank you for your business!', 10, yPosition + 20);

                // Footer with contact information
                doc.setFontSize(10);
                doc.setFont('helvetica', 'normal');
                doc.text('Contact: +8801XXXXXXXXX', 10, yPosition + 30);
                doc.text('Email: info@example.com', 10, yPosition + 35);
                doc.text('Website: www.example.com', 10, yPosition + 40);

                // Generate a unique random string (4 characters + 4 digits)
                const randomString = Math.random().toString(36).substr(2, 4) + Math.floor(Math.random() * 10000).toString();
                const fileName = `inv_${randomString}.pdf`; // Create a unique filename

                // Save the PDF
                doc.save(fileName);

                // Prepare data to send to the server
                $.ajax({
                    url: 'save_sale_info.php',
                    method: 'POST',
                    data: {
                        saleData: saleData,
                        fileName: fileName // Include the generated file name
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