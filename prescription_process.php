<?php
$dbhost = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "shasthobdapi";

$conn = new mysqli($dbhost, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $doctorId = $_POST['doctorid'];
    $patientId = $_POST['patientid'];
    $medicine = $_POST['medicine'];
    $duration = $_POST['duration'];
    $dosage = $_POST['dosage'];
    $notes = $_POST['notes'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO prescription (DoctorID, PatientID, Medicine, Duration, Dosage, Notes) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissss", $doctorId, $patientId, $medicine, $duration, $dosage, $notes);

    if ($stmt->execute()) {
        echo "Prescription saved successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
