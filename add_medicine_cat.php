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

</head>

<body>
    <div class="app-main__inner">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-header">Add Medicine Category</div>
                        <div class="card-body">
                            <div class="position-relative row form-group">

                                <!-- Invoice Number -->
                                <div class="col-sm-3">
                                    <label for="category_name">Category Name:</label>
                                    <input type="text" id="category_name"class="form-control"  name="category_name" placeholder="Enter category name" required>
                                </div>

                                <!-- Customer Name -->
                                <div class="col-sm-3">
                                    <label for="status">Status:</label>
                                    <select id="status"class="form-control"  name="status" required>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>


                                <div class="col-sm-3">
                                    <button type="submit" class="btn btn-success savebtn"style="margin-top: 12%;">Save</button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php if (!empty($message)): ?>
            <script>
                toastr.<?php echo $alertType; ?>('<?php echo $message; ?>');
            </script>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="main-card mb-3 card">
                    <div class="card-header">
                        Medicine Category

                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div style="margin-top: 10px; margin-bottom: 25px; display: flex; flex-wrap: wrap; gap: 10px;">
                                <select id="filter_name" class="form-control" style="flex: 1;">
                                    <option value="">Select Category</option>
                                    <?php
                                    // Fetch distinct categories for filtering
                                    $result_categories = $conn->query("SELECT DISTINCT id, name FROM medicine_category WHERE status = 1");
                                    while ($row = $result_categories->fetch_assoc()) {
                                        echo "<option value='" . htmlspecialchars($row['name']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                                    }
                                    ?>
                                </select>

                                <select id="filter_status" class="form-control" style="flex: 1;">
                                    <option value="">Select Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>

                                <input type="date" id="filter_date" class="form-control" style="flex: 1;" />

                                <button id="searchBtn" class="btn btn-warning" style="flex: 0 0 auto;">Search</button>
                                <button id="clearBtn" class="btn btn-red" style="flex: 0 0 auto;">Clear</button>
                            </div>
                            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="">Sl</th>
                                        <th class="">Category Name</th>
                                        <th class="">Status</th>
                                        <th class="">Date</th>
                                    </tr>
                                </thead>
                                <tbody id="categoryTableBody">
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
                    </div>

                </div>
            </div>
        </div>

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
            filterTable(); // Show all categories after clearing filters
        });

        $('#searchBtn').on('click', function(e) {
            e.preventDefault(); // Prevent form submission
            filterTable();
        });

        function filterTable() {
            var nameFilter = $('#filter_name').val();
            var statusFilter = $('#filter_status').val();
            var filterDate = $('#filter_date').val();

            $('#categoryTableBody tr').filter(function() {
                var nameMatch = nameFilter === "" || $(this).children('td:nth-child(2)').text().trim() === nameFilter;
                var statusMatch = statusFilter === "" || $(this).children('td:nth-child(3)').find('span').text().trim() === (statusFilter == 1 ? 'Active' : 'Inactive');

                // Date filter logic
                var createdAt = $(this).children('td:nth-child(4)').text().trim();
                var dateMatch = filterDate === "" || createdAt === filterDate;

                // Show or hide the row based on the filter conditions
                $(this).toggle(nameMatch && statusMatch && dateMatch);
            });
        }
    });
</script>

</body>

</html>