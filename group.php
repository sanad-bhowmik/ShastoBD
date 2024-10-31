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
    <div class="container">
        <form method="POST" action="">
            <div class="flex-container">
                <div class="form-group Gname" style="margin-right: -24%;">
                    <label for="group_name">Group Name:</label>
                    <input type="text" id="group_name" name="group_name" placeholder="Enter group name" required>
                </div>
                <div class="form-group status">
                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="form-group" style="margin-top: 19px;">
                    <button type="submit" class="btn-primary" style="margin-left: -208%;">Save</button>
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


        <!-- Select2 Filter for Group Names -->
        <section style="display: flex;">
            <div class="form-group" style="width: 30%; margin-bottom: 15px; ">
                <label for="filter_group">Search by Group Name:</label>
                <select id="filter_group" class="select2" style="width: 100%;">
                    <option value="">All Groups</option>
                    <?php
                    // Fetch distinct group names for the Select2 dropdown
                    $groupQuery = "SELECT DISTINCT name FROM medicine_group";
                    $groupResult = $conn->query($groupQuery);
                    while ($row = $groupResult->fetch_assoc()) {
                        echo "<option value='" . $row['name'] . "'>" . $row['name'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <button id="clearBtn" class="btn-clear btn-info" style="margin-left: 10px;margin-top: 25px;">Clear</button>
            </div>
        </section>

        <table id="groupTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Group Name</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    $index = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $index++ . "</td>";
                        echo "<td><span class='group-name'>" . $row['name'] . "</span><input type='text' class='group-input' value='" . $row['name'] . "' style='display:none;'></td>";
                        echo "<td>" . ($row['status'] == 1 ? 'Active' : 'Inactive') . "</td>";
                        echo "<td>
                                <button class='edit-btn'>Edit</button>
                                <button class='save-btn' style='display: none;' data-id='" . $row['id'] . "'>Save</button>
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

<style>
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
        color: white;
    }

    .save-btn:after {
        content: " ";
        width: 0%;
        height: 100%;
        background: #ffd401;
        position: absolute;
        transition: all 0.4s ease-in-out;
        right: 0;
        color: black;
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

        #searchBtn {
            margin-top: 10px;
            width: 98%;
        }

        #clearBtn {
            margin-top: 10px;
            width: 98%;
        }

    }

    @media (min-width: 1024px) {
        .status {
            margin-left: -16%;
        }

        #searchBtn {
            margin-right: -144px;
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