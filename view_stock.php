<?php
include_once("include/initialize.php");
include_once("include/header.php");

// Database connection
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

// Fetch stock_in data including status
$stock_in_query = "
    SELECT si.id, s.name AS supplier_name, c.name AS category_name, m.name AS medicine_name, si.InQty, si.created_at, si.status 
    FROM stock_in si
    JOIN suppliers s ON si.supplier_id = s.id
    JOIN medicine_category c ON si.cat_id = c.id
    JOIN medicine m ON si.medicine_id = m.id
";

$stock_in_result = $conn->query($stock_in_query);

// Check for errors in the query execution
if (!$stock_in_result) {
    die("Query failed: " . $conn->error); // Output the error message
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock List</title>

    <!-- Include Select2 CSS and JS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
</head>

<body>
    <div class="container">
        <h2>Current Stock</h2>
        <table id="stockTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Supplier</th>
                    <th>Category</th>
                    <th>Medicine</th>
                    <th>In Quantity</th>
                    <th>Date</th>
                    <th>Status</th> <!-- New Status Column -->
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stock_in_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['supplier_name']; ?></td>
                        <td><?php echo $row['category_name']; ?></td>
                        <td><?php echo $row['medicine_name']; ?></td>
                        <td><?php echo $row['InQty']; ?></td>
                        <td><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                        <td>
                            <?php if ($row['status'] == 1): ?>
                                <span class="badge badge-success">Active</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>


    <style>
        .container {
            max-width: 97%;
            margin: 50px auto;
            padding: 30px;
            background-color: #f9f9f9;
            border-radius: 12px;
            box-shadow: rgba(50, 50, 93, 0.25) 0px 13px 27px -5px, rgba(0, 0, 0, 0.3) 0px 8px 16px -8px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }

        /* Styles for the stock table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: rgba(50, 50, 93, 0.25) 0px 13px 27px -5px, rgba(0, 0, 0, 0.3) 0px 8px 16px -8px;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #00a37a;
            color: white;
            font-size: 16px;
        }

        td {
            font-size: 15px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Badge Styles */
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            color: white;
            font-weight: bold;
        }

        .badge-success {
            background-color: #28a745;
            /* Green background for active */
        }

        .badge-danger {
            background-color: #dc3545;
            /* Red background for inactive */
        }
    </style>
</body>

</html>