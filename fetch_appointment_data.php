<?php
$dbhost = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "shasthobdapi";

$conn = new mysqli($dbhost, $dbusername, $dbpassword, $dbname);

// Check the database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['appointment_number'])) {
    $appointmentNumber = $_POST['appointment_number'];

    // Prepare the SQL query with LEFT JOINs
    $query = "SELECT a.appointment_number, d.DocName AS doctor_name, p.Name AS patient_name
  FROM appointmentview a
LEFT JOIN tbl_doctor d ON a.DOCID = d.DOCID
LEFT JOIN tbl_patient p ON a.PatientID = p.OID  -- Assuming `PatientID` in appointmentview links to OID
WHERE a.appointment_number = ?";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        // Show error if query preparation fails
        echo json_encode(['success' => false, 'error' => 'Query preparation failed: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("s", $appointmentNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'doctor_name' => $data['doctor_name'], 'patient_name' => $data['patient_name']]);
    } else {
        // Show specific error when no rows are found
        echo json_encode(['success' => false, 'error' => 'No data found for this appointment number.']);
    }

    $stmt->close();
} else {
    // Show error if appointment number is not set
    echo json_encode(['success' => false, 'error' => 'Appointment number not provided.']);
}

$conn->close();
