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

if (isset($_POST['doctorName'])) {
    $doctorName = $conn->real_escape_string($_POST['doctorName']);
    
    // Query to fetch the doctor's mobile number and BmdcReg based on the name
    $query = "SELECT MobileNum, BmdcReg FROM tbl_doctor WHERE DocName = '$doctorName' AND Active = 1 LIMIT 1";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            'mobile' => $row['MobileNum'],
            'bmdcReg' => $row['BmdcReg']
        ]);
    } else {
        echo json_encode(['mobile' => 'Not Found', 'bmdcReg' => 'Not Found']);
    }
} else {
    echo json_encode(['error' => 'No doctor name provided']);
}

$conn->close();
?>
