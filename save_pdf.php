<?php
$dbhost = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "shasthobdapi";

// Define the path to save the PDF
$uploadDir = 'C:/xampp/htdocs/shasthobdAdmin/themefiles/assets/pdf/prescption';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf']) && isset($_POST['appointment_number'])) {
    // Retrieve the appointment number from POST data
    $appointmentNumber = $_POST['appointment_number'];

    // Validate the appointment number
    if (empty($appointmentNumber)) {
        echo 'error|Appointment number is required.';
        exit;
    }

    // Retrieve file information
    $originalFileName = pathinfo($_FILES['pdf']['name'], PATHINFO_FILENAME);
    $fileExtension = pathinfo($_FILES['pdf']['name'], PATHINFO_EXTENSION);

    // Create a unique file name
    $uniqueSuffix = date('YmdHis') . '_' . uniqid();
    $uniqueFileName = $originalFileName . '_' . $uniqueSuffix . '.' . $fileExtension;

    $tempName = $_FILES['pdf']['tmp_name'];
    $destination = $uploadDir . $uniqueFileName;

    // Ensure the directory exists
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Move the uploaded file
    if (move_uploaded_file($tempName, $destination)) {
        // File path to save in the database
        $filePath = '/shasthobdAdmin/themefiles/assets/pdf/' . $uniqueFileName; 
        $conn = new mysqli($dbhost, $dbusername, $dbpassword, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Database connection failed: " . $conn->connect_error);
        }

        // Insert file path and appointment number into the database
        $status = 'active'; // Example status
        $stmt = $conn->prepare("INSERT INTO ot_Prescription (appointment_number, file_path, status) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $appointmentNumber, $filePath, $status);

        if ($stmt->execute()) {
            // Send success response with the saved file path
            echo "success|$filePath";
        } else {
            echo "error|Database insertion failed: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        echo 'error|Failed to move uploaded file.';
    }
} else {
    echo 'invalid|No file uploaded or invalid request.';
}
?>
