<?php
$dbhost = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "shasthobdapi";

$conn = new mysqli($dbhost, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['appointment_number'])) {
    $appointmentNumber = $_POST['appointment_number'];

    $query = "SELECT d.DocName AS doctor_name, p.Name AS patient_name 
              FROM tbl_doctor d
              JOIN appointmentview a ON d.DOCID = a.DOCID
              JOIN tbl_patient p ON a.OID = p.OID
              WHERE a.appointment_number = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $appointmentNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'doctor_name' => $data['doctor_name'], 'patient_name' => $data['patient_name']]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
}

$conn->close();
?>
