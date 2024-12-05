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

// Fetch medicines for dropdown that exist in stock_in and have InQty > 0
$medicines = [];
$sql = "
    SELECT m.id, m.name 
    FROM medicine m
    WHERE EXISTS (
        SELECT 1 
        FROM stock_in si 
        WHERE si.medicine_id = m.id AND si.InQty > 0
    )
";
$result_medicines = $conn->query($sql);
if ($result_medicines->num_rows > 0) {
    while ($row = $result_medicines->fetch_assoc()) {
        $medicines[] = $row;
    }
}

$customers = [];
$sql = "
    SELECT id, customer_name
    FROM sale_customer
";
$result_customers = $conn->query($sql);
if ($result_customers->num_rows > 0) {
    while ($row = $result_customers->fetch_assoc()) {
        $customers[] = $row;
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
    <div class="container" style="display: flex; justify-content: space-between; height: 76%;">
        <div class="form-container" style="flex: 1; padding: 20px; margin-right: 10px; border-radius: 8px; ">
            <h2>Add Sale Info</h2>
            <form id="saleForm" method="POST" action=""
                style="margin-left: -21px;margin-bottom: 49px;display: flex;align-items: center;gap: 10px;width: 119%;">

                <!-- Medicine Name Dropdown -->
                <div class="form-group" style="flex: 1;">
                    <label for="customer_name">Customer Name:</label>
                    <select id="customer_name" name="customer_name"  class="select2"
                        style="width: 100%; border: 1px solid #778899b5; border-radius: 5px;">
                        <option value="">Select</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?php echo $customer['customer_name']; ?>">
                                <?php echo htmlspecialchars($customer['customer_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Medicine Name Dropdown -->
                <div class="form-group" style="flex: 1;">
                    <label for="medicine_name">Medicine Name:</label>
                    <select id="medicine_name" name="medicine_name" required class="select2"
                        style="width: 100%; border: 1px solid #778899b5; border-radius: 5px;">
                        <option value="">Select</option>
                        <?php foreach ($medicines as $medicine): ?>
                            <option value="<?php echo $medicine['id']; ?>">
                                <?php echo htmlspecialchars($medicine['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Unit Price Input -->
                <div class="form-group" style="flex: 1;">
                    <label for="unit_price">Unit Price:</label>
                    <input type="number" id="unit_price" name="unit_price"
                        style="border: 1px solid #778899b5; height: 31px; width: 100%; border-radius: 5px;"
                        placeholder="Unit price" required>
                </div>

                <!-- Quantity Input -->
                <div class="form-group" style="flex: 1;">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" placeholder=""
                        style="border: 1px solid #778899b5; height: 31px; width: 100%; border-radius: 5px;" required>
                </div>

                <!-- Add Button -->
                <div class="form-group" style="min-width: 100px;">
                    <button type="button" class="btn-green" id="addBtn" style="width: 63%;margin-left: 3%;">Add</button>
                </div>
            </form>
        </div>

        <div class="table-container"
            style="flex: 1; padding: 20px; margin-left: 10px; border-radius: 8px; background-color: #f9f9f9;box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;">
            <h2>Added Items</h2>
            <table id="addedItemsTable"
                style="width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 10%;">
                <thead>
                    <tr style="background-color: #eaeaea;">
                        <th style="border: 1px solid #ccc; padding: 8px;">#</th>
                        <th style="border: 1px solid #ccc; padding: 8px;">Medicine Name</th>
                        <th style="border: 1px solid #ccc; padding: 8px;">Customer</th>
                        <th style="border: 1px solid #ccc; padding: 8px;">Quantity</th>
                        <th style="border: 1px solid #ccc; padding: 8px;">Unit Price</th>
                    </tr>
                </thead>
                <tbody style="background-color: white;">
                    <!-- Added items will be populated here -->
                </tbody>
            </table>
            <div class="flex-container priceSec" style="margin-top: 15px; display: flex; align-items: center;">
                <div class="form-group" style="flex: 1;">
                    <label for="total_price">Total Price:</label>
                    <input type="text" id="total_price" name="total_price" placeholder="00" readonly
                        style="width: 100%; border: 1px solid #778899b5; border-radius: 5px;">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="discount">Discount:</label>
                    <input type="number" id="discount" name="discount" value="0"
                        style="width: 100%; border: 1px solid #778899b5; border-radius: 5px;">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="payable">Payable:</label>
                    <input type="text" id="payable" name="payable" placeholder="00" readonly
                        style="width: 100%; border: 1px solid #778899b5; border-radius: 5px;">
                </div>
                <div class="form-group" style="width: 100px; margin-top: 17px;">
                    <button type="button" class="btn-danger" id="saveBtn" style="width: 100%;">Save</button>
                </div>
            </div>
        </div>
    </div>
    <style>
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                gap: 20px;
            }

            .form-container form {
                flex-direction: column;
            }

            .price-section {
                flex-direction: column;
            }

            .save-button,
            .add-button {
                align-self: stretch;
                margin-top: 10px;
            }

            table th,
            table td {
                font-size: 14px;
            }
        }
    </style>
    <script>
        $(document).ready(function () {
            $('.select2').select2(); // Initialize select2 for dropdowns
            $('#customer_name').select2();
            let totalPrice = 0;

            // Handle Add button click
            $('#addBtn').on('click', function () {
                var medicineName = $('#medicine_name option:selected').text();
                var customerName = $('#customer_name option:selected').text();
                var customerId = $('#customer_name').val(); // Get the customer ID
                var unitPrice = parseFloat($('#unit_price').val());
                var quantity = parseInt($('#quantity').val());

                if (!customerId) {  // Ensure a customer is selected
                    toastr.error('Please select a customer.');
                    return;
                }

                if (medicineName && unitPrice && quantity) {
                    var itemTotal = unitPrice * quantity;
                    totalPrice += itemTotal;

                    $('#addedItemsTable tbody').append(
                        `<tr>
                    <td>${$('#addedItemsTable tbody tr').length + 1}</td>
                    <td>${medicineName}</td>
                    <td>${customerName}</td>
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

            $('#discount').on('input', function () {
                updatePayable();
            });

            function updatePayable() {
                var discount = parseFloat($('#discount').val()) || 0;
                var payable = totalPrice - discount;
                $('#payable').val(payable.toFixed(2));
            }

            // Handle Save button click
            $('#saveBtn').on('click', function () {
                var rows = $('#addedItemsTable tbody tr');
                var total_price = $('#total_price').val();
                var discount = $('#discount').val();
                var payable = $('#payable').val();


                console.log("Customer ID:", customerId); // Debugging line
                console.log("Customer Name:", customerName);

                
                if (rows.length === 0) {
                    toastr.error('No items added.');
                    return;
                }

                var saleData = [];
                var customerId = $('#customer_name').val(); // Get customer ID for the sale
                var customerName = $('#customer_name option:selected').text();
                rows.each(function () {
                    var row = $(this);
                    var item = {
                        customer_id: customerId,  // Include customer ID in the item
                        customer_name: row.find('td:eq(2)').text(),  // Customer name from the row
                        medicine_name: row.find('td:eq(1)').text(),
                        quantity: row.find('td:eq(3)').text(),
                        unit_price: row.find('td:eq(4)').text(),
                        quantity: row.find('td:eq(2)').text(),
                        total_price: row.find('td:eq(4)').text(),  // Assuming total price is in the 4th column
                        discount: row.find('td:eq(5)').text(),     // Assuming discount is in the 5th column
                        payable: row.find('td:eq(6)').text()
                    };
                    saleData.push(item);
                });

                const { jsPDF } = window.jspdf;
                var doc = new jsPDF();

                // Generate a random Invoice No.
                const randomInvoiceNo = `INV-${Math.floor(100000 + Math.random() * 900000)}`;

                // Get the current date
                const currentDate = new Date();
                const formattedDate = `${currentDate.getDate()}/${currentDate.getMonth() + 1}/${currentDate.getFullYear()}`;
                // Add Invoice Title with Bottom Margin
                doc.setFontSize(16);
                doc.setFont('helvetica', 'bold');
                doc.text('INVOICE', 100, 20, { align: 'center' });
                let marginBottom = 20;
                let currentY = 20 + marginBottom;

                // Order Details Section
                doc.setFontSize(10);
                doc.setFont('helvetica', 'normal');
                doc.text('Invoice No:', 10, currentY);
                doc.text(randomInvoiceNo, 40, currentY);
                doc.text('Invoice Date:', 80, currentY);
                doc.text(formattedDate, 110, currentY);
                doc.text('Status:', 150, currentY);
                doc.text('New', 170, currentY);

                currentY += 10;

                doc.setFontSize(10);
                doc.text('Customer Name:', 10, currentY);
                doc.text(customerName, 40, currentY);
                doc.text('Address:', 10, currentY + 5);
                doc.text('', 40, currentY + 5);
                doc.text('Mobile:', 10, currentY + 10);
                doc.text('', 40, currentY + 10);
                doc.text('Payment:', 150, currentY);
                doc.text('Cash', 170, currentY);
                doc.text('Sold By:', 150, currentY + 5);
                doc.text('Admin', 170, currentY + 5);

                doc.setFillColor(200, 200, 200);
                doc.rect(10, currentY + 20, 190, 8, 'F');
                doc.setFontSize(10);
                doc.setFont('helvetica', 'bold');

                doc.text('Sl.', 12, currentY + 25);
                doc.text('Item Name', 30, currentY + 25);
                doc.text('Quantity', 90, currentY + 25);
                doc.text('Unit Price', 120, currentY + 25);
                doc.text('Amount', 150, currentY + 25);

                // Table Content
                doc.setFont('helvetica', 'normal');
                let yPosition = currentY + 33;
                let sl = 1;

                rows.each(function () {
                    var row = $(this);
                    var itemName = row.find('td:eq(1)').text();
                    var quantity = parseFloat(row.find('td:eq(3)').text());
                    var unitPrice = parseFloat(row.find('td:eq(4)').text());

                    var amount = (quantity * unitPrice).toFixed(2);

                    doc.text(sl.toString(), 12, yPosition);
                    doc.text(itemName, 0, yPosition + -4, { align: 'left' });
                    doc.text(quantity.toString(), 100, yPosition, { align: 'right' });
                    doc.text(unitPrice.toFixed(2), 135, yPosition, { align: 'right' });
                    doc.text(amount, 193, yPosition, { align: 'right' });

                    sl++;
                    yPosition += 8;
                });

                doc.setLineWidth(0.1);
                yPosition += 2;
                doc.line(10, yPosition, 200, yPosition);

                yPosition += 5;
                doc.setFont('helvetica', 'normal');
                doc.text('Gross Amount:', 140, yPosition);
                doc.text(total_price, 180, yPosition);

                yPosition += 5;
                doc.text('Discount (ABS):', 140, yPosition);
                doc.text(discount, 180, yPosition);

                yPosition += 5;
                doc.text('Tax Amount:', 140, yPosition);
                doc.text('0.00', 180, yPosition);

                yPosition += 5;
                doc.text('Net Amount:', 140, yPosition);
                doc.text(payable, 180, yPosition);

                yPosition += 5;
                doc.text('Paid Amount:', 140, yPosition);
                doc.text(payable, 180, yPosition);

                yPosition += 5;
                doc.text('Due Amount:', 140, yPosition);
                doc.text('0.00', 180, yPosition);

                let footerYPosition = 265;
                doc.text('Authorized Signature', 10, footerYPosition);
                doc.text('Customer Signature', 150, footerYPosition);

                // Company Info positioned below the signatures
                doc.text('EMON DENTAL.', 10, footerYPosition + 5);
                doc.text('979 Eastern Plaza, Dhaka-1205, Bangladesh.Contact: +8801XXXXXXXXX, Email: info@example.com', 10, footerYPosition + 10);

                doc.setFillColor(135, 206, 235);
                doc.rect(0, 290, 210, 20, 'F');

                const fileName = `Invoice_${randomInvoiceNo}.pdf`;
                var pdfBlob = doc.output('blob');
                var pdfUrl = URL.createObjectURL(pdfBlob);
                window.open(pdfUrl, '_blank');

                // Prepare data to send to the server
                $.ajax({
                    url: 'save_sale_info.php',
                    method: 'POST',
                    data: {
                        saleData: saleData,
                        fileName: fileName // Include the generated file name
                    },
                    success: function (response) {
                        console.log('Raw server response:', response); // Log raw response to see if it's HTML
                        try {
                            response = JSON.parse(response);
                            if (response.success) {
                                toastr.success('Successfully Added');
                                $('#addedItemsTable tbody').empty();
                                $('#total_price').val('00');
                                $('#discount').val('');
                                $('#payable').val('');
                            } else {
                                toastr.success('Invoice Created Successfully');
                            }
                        } catch (e) {
                            toastr.error('Unexpected response format from server');
                        }
                    },
                    error: function () {
                        toastr.error('Failed to save data');
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