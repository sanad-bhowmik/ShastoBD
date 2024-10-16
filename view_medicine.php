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

// Fetch medicines from the medicine table
$query = "SELECT m.id, m.name AS medicine_name, mc.name AS category_name, m.qty, m.status 
          FROM medicine m 
          JOIN medicine_category mc ON m.cat_id = mc.id"; // Joining to get the category name
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicine List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            margin: 50px auto;
            max-width: 95%;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
        }

        h2 {
            text-align: center;
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }

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

        .badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
            color: white;
            text-align: center;
        }

        .status-active {
            background-color: #28a745;
            /* Green background for active */
        }

        .status-inactive {
            background-color: #dc3545;
            /* Red background for inactive */
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Medicine List</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Medicine Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['medicine_name'] . "</td>";
                        echo "<td>" . $row['category_name'] . "</td>";
                        echo "<td>" . $row['qty'] . "</td>";
                        echo "<td>";
                        // Display badge based on status
                        echo ($row['status'] == 1
                            ? "<span class='badge status-active'>Active</span>"
                            : "<span class='badge status-inactive'>Inactive</span>");
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No medicines found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>

<?php
$conn->close(); // Close the database connection
?>