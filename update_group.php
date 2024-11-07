<?php
include_once("include/initialize.php");

$dbhost = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "shasthobdapi";

$conn = new mysqli($dbhost, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the ID and new name from the POST request
    $groupId = $_POST['id'];
    $newGroupName = $_POST['name'];

    // Prepare the SQL statement to update the group name
    $stmt = $conn->prepare("UPDATE medicine_group SET name = ? WHERE id = ?");
    $stmt->bind_param("si", $newGroupName, $groupId);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Group updated successfully!']);
        } else {
            echo json_encode(['status'  => 'No changes made or group not found.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error updating group: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

$conn->close();
