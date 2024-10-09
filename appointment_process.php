<?php
session_start();
$dbhost = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "shasthobdapi";

$conn = new mysqli($dbhost, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doctorid = $_POST['doctorid'];
    $patientid = $_POST['patientid'];
    $appointmentTime = $_POST['AppointmentTime'];
    $appointmentDate = $_POST['AppointmentDate'];

    // Fetch doctor details
    $doctorQuery = "SELECT DocDegree, BmdcReg, DocName, DocType, DayOfPractice, DocImage, MobileNum FROM tbl_doctor WHERE DOCID = ?";
    $doctorStmt = $conn->prepare($doctorQuery);
    if ($doctorStmt === false) {
        die("Error preparing doctor statement: " . $conn->error);
    }
    $doctorStmt->bind_param("i", $doctorid);
    $doctorStmt->execute();
    $doctorStmt->bind_result($docDegree, $bmdcReg, $docName, $DocType, $dayOfPractice, $docImage, $mobileNum);
    $doctorStmt->fetch();
    $doctorStmt->close();

    if (empty($docDegree) || empty($bmdcReg) || empty($docName) || empty($DocType) || empty($dayOfPractice) || empty($docImage) || empty($mobileNum)) {
        die("Error: Doctor with DOCID $doctorid not found.");
    }

    // Fetch patient details including gender
    $patientQuery = "SELECT Name, Gender FROM tbl_patient WHERE OID = ?";
    $patientStmt = $conn->prepare($patientQuery);
    if ($patientStmt === false) {
        die("Error preparing patient statement: " . $conn->error);
    }
    $patientStmt->bind_param("i", $patientid);
    $patientStmt->execute();
    $patientStmt->bind_result($patientName, $patientGender);
    $patientStmt->fetch();
    $patientStmt->close();

    if (empty($patientName)) {
        die("Error: Patient with OID $patientid not found.");
    }

    // Insert appointment data into appointmentview table
    $insertQuery = "INSERT INTO appointmentview 
        (OID, PatientID, DOCID, Appointment_Time, AppointmentDate, Created_at, Updatedat, PatientMobile, MobileNum, PatientName, ParientGender, type, status, DocDegree, BmdcReg, DocName, DocType, DayOfPractice, DocImage) 
        VALUES (?, ?, ?, ?, ?, NOW(), NOW(), ?, ?, ?, ?, 'Regular', 'Active', ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($insertQuery);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param(
        "iiissssssssssss",
        $patientid,
        $patientid,
        $doctorid,
        $appointmentTime,
        $appointmentDate,
        $patientMobile, // Assuming this variable is still set elsewhere
        $mobileNum,
        $patientName, // Insert the patient's name
        $patientGender, // Insert the patient's gender
        $docDegree,
        $bmdcReg,
        $docName,
        $DocType,
        $dayOfPractice,
        $docImage
    );

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Appointment booked successfully.";
        header("Location: add_appointement.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
