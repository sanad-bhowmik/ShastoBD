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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_name = $_POST['category_name'];
    $status = $_POST['status'];

    if (!empty($category_name)) {
        $query = "INSERT INTO medicine_category (name, status) VALUES (?, ?)";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("si", $category_name, $status);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $message = "Category added successfully!";
                $alertType = "success"; // Set alert type for success
            } else {
                $message = "Failed to add category. Please try again.";
                $alertType = "error"; // Set alert type for error
            }
            $stmt->close();
        } else {
            $message = "Database error: Unable to prepare statement.";
            $alertType = "error"; // Set alert type for error
        }
    } else {
        $message = "Please enter a category name.";
        $alertType = "error"; // Set alert type for error
    }
}
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
                margin-left: -202%;
            }

            .btn-red {
                margin-left: -90%;
            }

            .btn-warning {
                margin-left: -17%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <form method="POST" action="">
            <div class="flex-container">
                <!-- Category Name Input -->
                <div class="form-group">
                    <label for="category_name">Category Name:</label>
                    <input type="text" id="category_name" name="category_name" placeholder="Enter category name" required>
                </div>

                <!-- Status Dropdown -->
                <div class="form-group status">
                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <!-- Save Button -->
                <div class="form-group">
                    <button type="submit" class="btn-green savebtn">Save</button>
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
        <h2>Medicines List</h2>

        <div class="flex-container">
            <!-- Category Name Dropdown -->
            <div class="form-group">
                <select id="filter_name" class="select2">
                    <option value="">Select Category</option>
                    <?php
                    // Fetch distinct categories for filtering
                    $result_categories = $conn->query("SELECT DISTINCT id, name FROM medicine_category WHERE status = 1");
                    while ($row = $result_categories->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row['name']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Status Dropdown -->
            <div class="form-group">
                <select id="filter_status" class="select2">
                    <option value="">Select Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>

            <!-- Single Date Picker for Filtering -->
            <div class="form-group">
                <input type="date" id="filter_date" />
            </div>

            <!-- Search Button -->
            <div class="form-group">
                <button id="searchBtn" class="btn-warning">Search</button>
            </div>
            <div class="form-group">
                <button id="clearBtn" class="btn-red">Clear</button>
            </div>
        </div>

        <table id="medicineTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Category Name</th>
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch categories from the medicine_category table
                $result = $conn->query("SELECT id, name, status, created_at FROM medicine_category");

                if ($result->num_rows > 0) {
                    $index = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $index++ . "</td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";

                        // Status with badge design
                        $statusClass = $row['status'] == 1 ? 'badge-success' : 'badge-danger';
                        $statusText = $row['status'] == 1 ? 'Active' : 'Inactive';
                        echo "<td><span class='badge $statusClass' style='padding: 5px; color: white;'>" . $statusText . "</span></td>";

                        // Display created_at date
                        echo "<td>" . date('Y-m-d', strtotime($row['created_at'])) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No categories found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $('.select2').select2(); // Initialize select2 for dropdowns

            // Clear filter button functionality
            $('#clearBtn').on('click', function(e) {
                e.preventDefault(); // Prevent form submission
                $('#filter_name').val('').trigger('change'); // Clear category filter
                $('#filter_status').val('').trigger('change'); // Clear status filter
                $('#filter_date').val(''); // Clear date filter
                $('#medicineTable tbody tr').show(); // Show all medicines
            });

            $('#searchBtn').on('click', function(e) {
                e.preventDefault(); // Prevent form submission
                filterTable();
            });

            function filterTable() {
                var nameFilter = $('#filter_name').val();
                var statusFilter = $('#filter_status').val();
                var filterDate = $('#filter_date').val();

                $('#medicineTable tbody tr').filter(function() {
                    var nameMatch = nameFilter === "" || $(this).children('td:nth-child(2)').text() === nameFilter;
                    var statusMatch = statusFilter === "" || $(this).children('td:nth-child(3)').text().trim() === (statusFilter == 1 ? 'Active' : 'Inactive');

                    // Date filter logic
                    var createdAt = $(this).children('td:nth-child(4)').text();
                    var dateMatch = filterDate === "" || createdAt === filterDate;

                    $(this).toggle(nameMatch && statusMatch && dateMatch);
                });
            }
        });
    </script>
</body>

</html>