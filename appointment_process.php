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
    // Get posted data
    $doctorid = $_POST['doctorid'];
    $patientName = $_POST['patientName'];
    $patientMobile = $_POST['PatientMobile'];
    $gender = $_POST['gender'];
    $appointmentTime = $_POST['AppointmentTime'];
    $appointmentDate = $_POST['AppointmentDate'];

    // Check if the doctor exists
    $doctorQuery = "SELECT DocDegree, BmdcReg, DocName, DocType, DayOfPractice, DocImage, MobileNum FROM tbl_doctor WHERE DOCID = ?";
    $doctorStmt = $conn->prepare($doctorQuery);
    if ($doctorStmt === false) {
        die("Error preparing doctor statement: " . $conn->error);
    }
    $doctorStmt->bind_param("i", $doctorid);
    $doctorStmt->execute();
    $doctorStmt->bind_result($docDegree, $bmdcReg, $docName, $docType, $dayOfPractice, $docImage, $mobileNum);
    $doctorStmt->fetch();
    $doctorStmt->close();

    // Generate random values for OID and PatientID
    $OID = rand(10, 99); // Generates a random number between 10 and 99
    $PatientID = rand(1000, 9999); // Generates a random number between 1000 and 9999
    $type = 'Regular'; // Default type value

    // Insert appointment data into appointmentview table
    $insertQuery = "INSERT INTO appointmentview 
        (OID, PatientName, PatientMobile, PatientID, DOCID, Appointment_Time, AppointmentDate, Created_at, Updatedat, ParientGender, MobileNum, DocDegree, BmdcReg, DocName, DocType, DayOfPractice, type) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), ?, ?, ?, ?, ?, ?, ?,?)";

    $stmt = $conn->prepare($insertQuery);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param(
        "ississsssssssss",
        $OID, 
        $patientName,
        $patientMobile,
        $PatientID,
        $doctorid,
        $appointmentTime,
        $appointmentDate,
        $gender, // Insert the patient's gender
        $mobileNum,
        $docDegree,
        $bmdcReg,
        $docName,
        $docType,
        $dayOfPractice,
        $type // Default type value
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
