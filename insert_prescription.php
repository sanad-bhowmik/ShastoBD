<?php
include_once("include/header.php");

$dbhost = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "shasthobdapi";

$conn = new mysqli($dbhost, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and handle inputs
    $doctorId = mysqli_real_escape_string($conn, $_POST['doctorId']);
    $patientId = mysqli_real_escape_string($conn, $_POST['patientId']);
    $refNo = mysqli_real_escape_string($conn, $_POST['refNo']);
    $createdAt = date('Y-m-d H:i:s');
    $refNo = rand(1000, 9999);
    // Check if the entry already exists
    $checkSql = "SELECT * FROM tbl_prescriptionfile WHERE DOCID='$doctorId' AND PatientID='$patientId' ";
    $checkResult = mysqli_query($conn, $checkSql);

    if (mysqli_num_rows($checkResult) > 0) {
        echo json_encode(["success" => false, "message" => "This prescription already exists."]);
    } else {
        $sql = "INSERT INTO tbl_prescriptionfile (DOCID, PatientID, RefNo, created_at) 
                VALUES ('$doctorId', '$patientId', '$refNo', '$createdAt')";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(["success" => true, "message" => "New prescription added successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error: " . mysqli_error($conn)]);
        }
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}

$conn->close();
