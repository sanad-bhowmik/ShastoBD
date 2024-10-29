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
    $patientOID = $_POST['patientName']; // This now holds the OID from the dropdown
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
    // Get posted data


    $patientOID = $_POST['patientName']; // This holds the OID from the dropdown
    $gender = $_POST['gender'];
    $appointmentTime = $_POST['AppointmentTime'];
    $appointmentDate = $_POST['AppointmentDate'];

    // Query to fetch patient name and mobile based on OID
    $patientQuery = "SELECT Name, Mobile FROM tbl_patient WHERE OID = ?";
    $patientStmt = $conn->prepare($patientQuery);
    $patientStmt->bind_param("i", $patientOID);
    $patientStmt->execute();
    $patientStmt->bind_result($patientName, $patientMobile);
    $patientStmt->fetch();
    $patientStmt->close();

    $OID = rand(10, 99);
    $PatientID = $patientOID; // Use the selected patient's OID as the PatientID
    $type = 'Regular'; // Default type value

    // Generate a 4-digit random number for appointment_number and prepend with "ED-"
    $appointmentNumber = "ED-" . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    $status = "active"; // Set status to active

    // Insert appointment data into appointmentview table
    $insertQuery = "INSERT INTO appointmentview 
        (OID, PatientName, PatientMobile, PatientID, DOCID, Appointment_Time, AppointmentDate, Created_at, Updatedat, ParientGender, MobileNum, DocDegree, BmdcReg, DocName, DocType, DayOfPractice, type, appointment_number, Status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($insertQuery);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param(
        "ississsssssssssss",
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
        $type, // Default type value
        $appointmentNumber, // Generated appointment number
        $status // Set status to active
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
