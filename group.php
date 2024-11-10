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
    $groupName = $_POST['group_name'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO medicine_group (name, status) VALUES (?, ?)");
    $stmt->bind_param("si", $groupName, $status);

    if ($stmt->execute()) {
        $message = "Group added successfully!";
        $alertType = "success";
    } else {
        $message = "Error adding group: " . $conn->error;
        $alertType = "error";
    }

    $stmt->close();
}

// Fetch the list of groups
$sql = "SELECT id, name, status FROM medicine_group";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Group</title>
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
                        <div class="card-header">Add Group</div>
                        <div class="card-body">
                            <div class="position-relative row form-group responsive-form">
                                <!-- Group Name -->
                                <div class="col-sm-3 form-item">
                                    <label for="group_name">Group Name:</label>
                                    <input type="text" id="group_name" name="group_name" class="form-control" placeholder="Enter group name" required>
                                </div>

                                <!-- Status -->
                                <div class="col-sm-3 form-item">
                                    <label for="status">Status:</label>
                                    <select id="status" name="status" class="form-control" required>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>

                                <!-- Save Button -->
                                <div class="col-sm-3 form-item">
                                    <button type="submit" class="btn btn-primary save-button" style="margin-top: 27px;">Save</button>
                                </div>
                            </div>

                            <style>
                                @media (max-width: 600px) {
                                    .responsive-form {
                                        flex-direction: column;
                                        align-items: stretch;
                                    }

                                    .save-button {
                                        margin-top: 10px;
                                        width: 100%;
                                    }
                                }
                            </style>

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
                        Medicine Group
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div style="margin-top: 10px; margin-bottom: 25px; display: flex; flex-wrap: wrap; gap: 10px;">
                                <div>
                                    <label for="filter_group" style="flex: 0 0 auto; align-self: center;">Search by Group Name:</label>
                                    <select id="filter_group" class="form-control" style="flex: 1; min-width: 250px;">
                                        <option value="">All Groups</option>
                                        <?php
                                        // Fetch distinct group names for the Select2 dropdown
                                        $groupQuery = "SELECT DISTINCT name FROM medicine_group";
                                        $groupResult = $conn->query($groupQuery);
                                        while ($row = $groupResult->fetch_assoc()) {
                                            echo "<option value='" . htmlspecialchars($row['name']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <button id="clearBtn" class="btn btn-info" style="flex: 0 0 auto;margin-left: 10px;height: 10%;margin-top: 3%;">Clear</button>
                            </div>
                            <table class="align-middle mb-0 table table-borderless table-striped table-hover" id="groupTable">
                                <thead>
                                    <tr>
                                        <th class="">Sl</th>
                                        <th class="">Group Name</th>
                                        <th class="">Status</th>
                                        <th class="">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="categoryTableBody">
                                    <?php
                                    // Fetch group data (Ensure to fetch from the correct result set)
                                    $result = $conn->query("SELECT id, name, status FROM medicine_group");
                                    if ($result->num_rows > 0) {
                                        $index = 1;
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . $index++ . "</td>";
                                            echo "<td><span class='group-name'>" . htmlspecialchars($row['name']) . "</span><input type='text' class='group-input' value='" . htmlspecialchars($row['name']) . "' style='display:none;'></td>";
                                            echo "<td>" . ($row['status'] == 1 ? 'Active' : 'Inactive') . "</td>";
                                            echo "<td>
                                        <button class='edit-btn btn btn-info'>Edit</button>
                                        <button class='save-btn btn btn-success' style='display: none;' data-id='" . $row['id'] . "'>Save</button>
                                    </td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4'>No groups found</td></tr>";
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
        // Clear button functionality
        $('#clearBtn').on('click', function() {
            $('#filter_group').val(null).trigger('change'); // Reset Select2 dropdown
            $('#groupTable tbody tr').show(); // Show all table rows
        });
        $(document).ready(function() {
            $('.select2').select2(); // Initialize Select2 dropdown

            $('#filter_group').on('change', function() {
                var selectedGroup = $(this).val().toLowerCase();

                $('#groupTable tbody tr').filter(function() {
                    var groupName = $(this).find('.group-name').text().toLowerCase();
                    $(this).toggle(selectedGroup === "" || groupName === selectedGroup);
                });
            });

            // Edit and save functionality for group name
            $(document).on('click', '.edit-btn', function() {
                var $row = $(this).closest('tr');
                $row.find('.group-input').show(); // Show input field
                $row.find('.group-name').hide(); // Hide the normal text
                $(this).hide(); // Hide edit button
                $row.find('.save-btn').show(); // Show save button
            });

            $(document).on('click', '.save-btn', function() {
                var $row = $(this).closest('tr');
                var newGroupName = $row.find('.group-input').val();
                var groupId = $(this).data('id');

                $row.find('.group-input').hide(); // Hide input field
                $row.find('.group-name').text(newGroupName).show(); // Update and show the normal text
                $(this).hide(); // Hide save button
                $row.find('.edit-btn').show(); // Show edit button again

                $.ajax({
                    type: 'POST',
                    url: 'update_group.php', // Script to handle group update
                    data: {
                        id: groupId,
                        name: newGroupName
                    },
                    success: function(response) {
                        var res = JSON.parse(response);
                        if (res.status === "success") {
                            toastr.success('Group updated successfully!');
                        } else {
                            toastr.error('Error updating group: ' + res.message);
                        }
                    },
                    error: function() {
                        toastr.error('Error updating group.');
                    }
                });
            });
        });
    </script>
</body>

</html>