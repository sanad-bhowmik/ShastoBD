<?php
include_once("include/header.php");
$dbhost = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "shasthobdapi";

$conn = new mysqli($dbhost, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and bind
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $doctorId = $_POST['doctorid'];
    $patientId = $_POST['patientid'];
    $medicine = $_POST['medicine'];
    $duration = $_POST['duration'];
    $dosage = $_POST['dosage'];
    $notes = $_POST['notes'];
    $refNo = $_POST['refNo'];

    $stmt = $conn->prepare("INSERT INTO tbl_prescriptionfile (DOCID, PatientID, RefNo, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $doctorId, $patientId, $refNo);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $stmt->error]);
    }
    $stmt->close();
}

$conn->close();
?>
