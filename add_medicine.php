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
    $name = $_POST['medicine_name'];
    $category = $_POST['category'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO medicine (name, cat_id, status) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $name, $category, $status);

    if ($stmt->execute()) {
        $message = "Medicine added successfully!";
        $alertType = "success";
    } else {
        $message = "Error adding medicine: " . $conn->error;
        $alertType = "error";
    }

    $stmt->close();
}

// Fetch the list of medicines along with their categories
$sql = "SELECT m.name AS medicine_name, m.status, c.name AS category_name 
        FROM medicine m 
        JOIN medicine_category c ON m.cat_id = c.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Medicine</title>
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
                <div class="form-group Mname">
                    <label for="medicine_name">Medicine Name:</label>
                    <input type="text" id="medicine_name" name="medicine_name" placeholder="Enter medicine name" required>
                </div>
                <div class="form-group categoryf">
                    <label for="category">Category:</label>
                    <select id="category" name="category" required>
                        <option value="">Select Category</option>
                        <?php
                        // Fetch categories from the database
                        $categoryQuery = "SELECT id, name FROM medicine_category WHERE status = 1";
                        $categoryResult = $conn->query($categoryQuery);
                        while ($row = $categoryResult->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group status">
                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="form-group" style="margin-top: 19px;">
                    <button type="submit" class="btn-primary" style="margin-left: -92%;">Save</button>
                </div>
            </div>
        </form>

        <?php if (!empty($message)): ?>
            <script>
                toastr.<?php echo $alertType; ?>('<?php echo $message; ?>');
            </script>
        <?php endif; ?>
    </div>

    <div class="container " style="margin: -28px auto;">
        <h2>Medicines List</h2>

        <div class="flex-container">
            <div id="noflex">
                <div class="form-group Mname" style="width: 16%;">
                    <input type="text" id="filter_name" placeholder="Search by Medicine Name" style="" />
                </div>
                <div class="form-group" style="width: 16%;">
                    <select id="filter_category" class="select2">
                        <option value="">Select Category</option>
                        <?php
                        // Fetch distinct categories for filtering
                        $result_categories = $conn->query("SELECT DISTINCT id, name FROM medicine_category WHERE status = 1");
                        while ($row = $result_categories->fetch_assoc()) {
                            echo "<option value='" . $row['name'] . "'>" . $row['name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <button id="searchBtn" class="btn-primary">Search</button>
                </div>
                <div class="form-group">
                    <button id="clearBtn" class="btn-primary" style="background-image: radial-gradient(circle 986.6px at 10% 20%, rgba(251, 6, 6, 0.94) 0%, rgba(3, 31, 213, 1) 82.8%, rgba(248, 101, 248, 1) 87.9%);">Clear</button>
                </div>
            </div>
        </div>

        <table id="medicineTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Medicine Name</th>
                    <th>Category</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    $index = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $index++ . "</td>";
                        echo "<td>" . $row['medicine_name'] . "</td>";
                        echo "<td>" . $row['category_name'] . "</td>";
                        echo "<td>" . ($row['status'] == 1 ? 'Active' : 'Inactive') . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No medicines found</td></tr>";
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
                $('#filter_name').val(''); // Clear medicine name input
                $('#filter_category').val('').trigger('change'); // Clear category filter
                $('#medicineTable tbody tr').show(); // Show all medicines
            });

            $('#searchBtn').on('click', function(e) {
                e.preventDefault(); // Prevent form submission
                filterTable();
            });

            function filterTable() {
                var nameFilter = $('#filter_name').val().toLowerCase();
                var categoryFilter = $('#filter_category').val();

                $('#medicineTable tbody tr').filter(function() {
                    var nameMatch = $(this).children('td:nth-child(2)').text().toLowerCase().indexOf(nameFilter) > -1 || nameFilter === "";
                    var categoryMatch = $(this).children('td:nth-child(3)').text() === categoryFilter || categoryFilter === "";

                    $(this).toggle(nameMatch && categoryMatch);
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
            width: 100%;
        }

        .flex-container {
            flex-direction: column;
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

        .Mname {
            margin-left: 0%;
        }

        #noflex {
            display: block;
            width: 98%;
        }

        #filter_name {
            width: 614%;
            margin-bottom: 10px;
        }

        #filter_category {
            width: 614%;
        }
        #searchBtn{
            margin-top: 10px;
            width: 98%;
        }
        #clearBtn{
            margin-top: 10px;
            width: 98%;
        }

    }

    @media (min-width: 1024px) {
        .status {
            margin-left: -16%;
        }

        #searchBtn {
            margin-right:-144px;
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
            margin-left: -202%;
        }

        .btn-red {
            margin-left: -90%;
        }

        .btn-warning {
            margin-left: -17%;
        }

        #clearBtn {
            margin-left: -72%;
        }

        #noflex {
            margin-bottom: 10px;
            display: flex;
            gap: 12px;
            width: 92%;
        }

        .Mname {
            margin-right: -3px;
        }

        .categoryf {
            margin-left: -16%;
        }

        #filter_name {
            width: 100%;
            border: 1px solid #b0a8a8;
            height: 29px;
            border-radius: 5px;
            background-color: white;
            color: black;
        }

        #filter_category {
            width: 100%;

        }
    }
</style>