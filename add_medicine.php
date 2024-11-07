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
    $group = $_POST['medicine_group'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO medicine (name, cat_id, group_id, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $name, $category, $group, $status);

    if ($stmt->execute()) {
        $message = "Medicine added successfully!";
        echo "<script>toastr.success('$message');</script>";
    } else {
        $message = "Error adding medicine: " . $conn->error;
        $alertType = "error";
    }

    $stmt->close();
}

// Fetch the list of medicines along with their categories
$sql = "SELECT m.id, m.name AS medicine_name, m.status, c.name AS category_name, g.name AS group_name 
        FROM medicine m 
        JOIN medicine_category c ON m.cat_id = c.id
        JOIN medicine_group g ON m.group_id = g.id";
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
    <div class="app-main__inner">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-header">Add Medicine</div>
                        <div class="card-body">
                            <div class="position-relative row form-group">
                                <div class="col-sm-2">
                                    <label for="medicine_name">Medicine Name:</label>
                                    <input type="text" id="medicine_name" name="medicine_name" class="form-control" placeholder="Enter medicine name" required>
                                </div>

                                <div class="col-sm-2">
                                    <label for="category">Category:</label>
                                    <select id="category" class="form-control" name="category" required>
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

                                <div class="col-sm-2">
                                    <label for="medicine_group">Medicine Group:</label>
                                    <select id="medicine_group" name="medicine_group" class="form-control" required>
                                        <option value="">Select Medicine Group</option>
                                        <?php
                                        // Fetch medicine groups from the database
                                        $groupQuery = "SELECT id, name FROM medicine_group WHERE status = 1";
                                        $groupResult = $conn->query($groupQuery);
                                        while ($row = $groupResult->fetch_assoc()) {
                                            echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-sm-2">
                                    <label for="status">Status:</label>
                                    <select id="status" class="form-control" name="status" required>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <button type="submit" class="btn btn-secondary" style="margin-top: 20%;">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="row">
            <div class="col-md-12">
                <div class="main-card mb-3 card">
                    <div class="card-header">
                        Medicine
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div style="margin-top: 10px; margin-bottom: 25px;">
                                <div class="d-flex flex-wrap" style="gap: 10px;">
                                    <input type="text" id="filter_name" class="form-control" placeholder="Search by Medicine Name" style="flex: 1 1 200px;" />

                                    <select id="filter_category" class="form-control" style="flex: 1 1 200px;">
                                        <option value="">Select Category</option>
                                        <?php
                                        // Fetch distinct categories for filtering
                                        $result_categories = $conn->query("SELECT DISTINCT id, name FROM medicine_category WHERE status = 1");
                                        while ($row = $result_categories->fetch_assoc()) {
                                            echo "<option value='" . $row['name'] . "'>" . $row['name'] . "</option>";
                                        }
                                        ?>
                                    </select>

                                    <select id="filter_group" class="form-control" style="flex: 1 1 200px;">
                                        <option value="">Select Medicine Group</option>
                                        <?php
                                        // Fetch distinct medicine groups for filtering
                                        $result_groups = $conn->query("SELECT DISTINCT id, name FROM medicine_group WHERE status = 1");
                                        while ($row = $result_groups->fetch_assoc()) {
                                            echo "<option value='" . $row['name'] . "'>" . $row['name'] . "</option>";
                                        }
                                        ?>
                                    </select>

                                    <button id="searchBtn" class="btn btn-success" style="flex: 0 0 auto;">Search</button>
                                    <button id="clearBtn" class="btn btn-danger" style="flex: 0 0 auto;">Clear</button>
                                </div>
                            </div>

                            <table id="medicineTable" class="align-middle mb-0 table table-borderless table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">Sl</th>
                                        <th class="text-center">Medicine Name</th>
                                        <th class="text-center">Category</th>
                                        <th class="text-center">Group</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        $index = 1;
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . $index++ . "</td>";
                                            echo "<td><span class='medicine-name'>" . $row['medicine_name'] . "</span><input type='text' class='medicine-input' value='" . $row['medicine_name'] . "' style='display:none;'></td>";
                                            echo "<td>" . $row['category_name'] . "</td>";
                                            echo "<td>" . $row['group_name'] . "</td>";
                                            echo "<td>
                                <button class='edit-btn btn btn-info'>Edit</button>
                                <button class='btn btn-success save-btn' style='display: none;' data-id='" . $row['id'] . "'>Save</button>
                              </td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5'>No medicines found</td></tr>";
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
            $('#clearBtn').on('click', function(e) {
                e.preventDefault(); // Prevent form submission
                $('#filter_name').val(''); // Clear medicine name input
                $('#filter_category').val('').trigger('change'); // Clear category filter
                $('#filter_group').val('').trigger('change'); // Clear group filter
                $('#medicineTable tbody tr').show(); // Show all medicines
            });

            $('#searchBtn').on('click', function(e) {
                e.preventDefault(); // Prevent form submission
                filterTable();
            });

            function filterTable() {
                var nameFilter = $('#filter_name').val().toLowerCase();
                var categoryFilter = $('#filter_category').val();
                var groupFilter = $('#filter_group').val();

                $('#medicineTable tbody tr').filter(function() {
                    var nameMatch = $(this).find('.medicine-name').text().toLowerCase().includes(nameFilter) || nameFilter === "";
                    var categoryMatch = categoryFilter === "" || $(this).children('td:nth-child(3)').text() === categoryFilter;
                    var groupMatch = groupFilter === "" || $(this).children('td:nth-child(4)').text() === groupFilter;

                    $(this).toggle(nameMatch && categoryMatch && groupMatch);
                });
            }

            // Edit functionality
            $('.edit-btn').on('click', function() {
                var row = $(this).closest('tr');
                row.find('.medicine-name').hide();
                row.find('.medicine-input').show();
                $(this).hide();
                row.find('.save-btn').show();
            });

            // Save functionality
            // Save functionality
$('.save-btn').on('click', function() {
    var row = $(this).closest('tr');
    var medicineId = $(this).data('id');
    var newMedicineName = row.find('.medicine-input').val();

    $.ajax({
        url: 'update_medicine.php', // Your update medicine script
        method: 'POST',
        data: {
            id: medicineId,
            name: newMedicineName
        },
        success: function(response) {
            response = JSON.parse(response); // Ensure response is parsed
            if (response.success) {
                toastr.success('Medicine updated successfully!'); // Show success notification
                row.find('.medicine-name').text(newMedicineName).show();
                row.find('.medicine-input').hide();
                row.find('.edit-btn').show();
                row.find('.save-btn').hide();
            } else {
                toastr.error('Error updating medicine: ' + response.error); // Use the error key
            }
        },
        error: function() {
            toastr.error('An unexpected error occurred.');
        }
    });
});

        });
    </script>
</body>

</html>

<?php
$conn->close();
?>