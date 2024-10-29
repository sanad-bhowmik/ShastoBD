<?php
// Database connection
$dbhost = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "shasthobdapi";

$conn = new mysqli($dbhost, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if appointment_number is set in POST request
if (isset($_POST['appointment_number'])) {
    $appointmentNumber = $_POST['appointment_number'];

    // Update query to set Status to "Active"
    $updateQuery = "UPDATE appointmentview SET Status = 'Inactive' WHERE appointment_number = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("s", $appointmentNumber);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }

    $stmt->close();
} else {
    echo 'error';
}

$conn->close();
?>
