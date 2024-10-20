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

// Query to get the sale invoice and related sale info
$sql = "SELECT si.inv_num, si.totalPrice, si.discount, si.payable_price, si.customer_name, si.customer_phone, s.file_name, s.file_path
        FROM sale_invoice s 
        JOIN sale_info si ON s.inv_num = si.inv_num";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Invoice Data</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>

    <div class="container">
        <h2>Sale Invoice Data</h2>

        <!-- Filters -->
        <div class="filters" style="display: flex; align-items: flex-start; gap: 10px; margin-top: 5%;">
            <input type="text" id="filterInvoiceNum" placeholder="Search by Invoice Number" style="margin-right: 10px;">
            <input type="text" id="filterCustomerName" placeholder="Search by Customer Name" style="margin-right: 10px;">
            <input type="text" id="filterCustomerPhone" placeholder="Search by Customer Phone" style="margin-right: 10px;">
            <button id="searchBtn" style="margin-right: 10px;">Search</button>
            <button id="clearBtn" class="clearBtn">Clear</button>
        </div>

        <!-- Table to display sale invoice data -->
        <table id="invoiceTable">
            <thead>
                <tr>
                    <th>Invoice Number</th>
                    <th>Total Price</th>
                    <th>Discount</th>
                    <th>Payable Price</th>
                    <th>Customer Name</th>
                    <th>Customer Phone</th>
                    <th>File Name</th>
                    <th>Action</th> <!-- New Action Column -->
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    // Output data for each row
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['inv_num'] . "</td>";
                        echo "<td>" . $row['totalPrice'] . "</td>";
                        echo "<td>" . $row['discount'] . "</td>";
                        echo "<td>" . $row['payable_price'] . "</td>";
                        echo "<td>" . $row['customer_name'] . "</td>";
                        echo "<td>" . $row['customer_phone'] . "</td>";
                        echo "<td>" . $row['file_name'] . "</td>";
                        // Add action button with relative URL
                        echo "<td><a href='themefiles/assets/pdf/" . $row['file_name'] . "' target='_blank' title='View PDF'><i class='fas fa-eye'></i></a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No data found</td></tr>"; // Update colspan
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Search button click event
            $('#searchBtn').click(function() {
                // Get input values and convert them to lowercase
                var invoiceNum = $('#filterInvoiceNum').val().toLowerCase().trim();
                var customerName = $('#filterCustomerName').val().toLowerCase().trim();
                var customerPhone = $('#filterCustomerPhone').val().toLowerCase().trim();

                // Filter the table rows
                $('#invoiceTable tbody tr').each(function() {
                    // Get the row values and convert them to lowercase
                    var rowInvoiceNum = $(this).children('td:nth-child(1)').text().toLowerCase().trim(); // Invoice Number
                    var rowCustomerName = $(this).children('td:nth-child(5)').text().toLowerCase().trim(); // Customer Name
                    var rowCustomerPhone = $(this).children('td:nth-child(6)').text().toLowerCase().trim(); // Customer Phone

                    // Debugging: print out values to verify they match correctly
                    console.log("Row Invoice Number:", rowInvoiceNum);
                    console.log("Input Invoice Number:", invoiceNum);

                    // Show or hide the row based on filter criteria
                    if (
                        (rowInvoiceNum.indexOf(invoiceNum) > -1 || invoiceNum === "") &&
                        (rowCustomerName.indexOf(customerName) > -1 || customerName === "") &&
                        (rowCustomerPhone.indexOf(customerPhone) > -1 || customerPhone === "")
                    ) {
                        $(this).show(); // Show the row if it matches the filters
                    } else {
                        $(this).hide(); // Hide the row if it doesn't match the filters
                    }
                });
            });

            // Clear button click event
            $('#clearBtn').click(function() {
                // Clear input fields
                $('#filterInvoiceNum').val('');
                $('#filterCustomerName').val('');
                $('#filterCustomerPhone').val('');

                // Show all rows
                $('#invoiceTable tbody tr').show();
            });
        });
    </script>

</body>

</html>


<style>
    body {
        font-family: Arial, sans-serif;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        font-size: 0.9em;
        text-align: left;
    }

    th,
    td {
        padding: 12px 15px;
        border: 1px solid #ddd;
    }

    th {
        background-color: #f4f4f4;
    }

    .filters {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .filters input {
        padding: 10px;
        font-size: 0.9em;
        width: 30%;
        border: 1px solid #ccc;
    }

    .filters button {
        padding: 10px 15px;
        background-image: radial-gradient(circle 382px at 50% 50.2%, rgba(73, 76, 212, 1) 0.1%, rgba(3, 1, 50, 1) 100.2%);
        color: white;
        border: none;
        cursor: pointer;
        font-size: 0.9em;
        border-radius: 14px;
    }

    .filters .clearBtn {
        background-color: #6c757d;
        border-radius: 14px;
        background-image: radial-gradient(circle 986.6px at 10% 20%, rgba(251, 6, 6, 0.94) 0%, rgba(3, 31, 213, 1) 82.8%, rgba(248, 101, 248, 1) 87.9%);
    }

    .filters .clearBtn:hover {
        background-color: #5a6268;
    }

    .filters button:hover {
        background-color: #0056b3;
    }

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