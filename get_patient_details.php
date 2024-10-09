<?php
// get_patient_details.php

$dbhost = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "shasthobdapi";

$conn = new mysqli($dbhost, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $patientId = $_GET['id'];

    // Fetch patient details
    $query = "SELECT Mobile, Gender FROM tbl_patient WHERE OID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $patientId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $patientData = $result->fetch_assoc();
        echo json_encode($patientData); // Return data as JSON
    } else {
        echo json_encode(null); // No data found
    }

    $stmt->close();
}

$conn->close();
?>
