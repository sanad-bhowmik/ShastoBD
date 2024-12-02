<?php
$dbhost = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "shasthobdapi";

// Define the path to save the PDF
$uploadDir = 'C:/xampp/htdocs/shasthobdAdmin/themefiles/assets/pdf/';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf'])) {
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

        // Insert file path into the database
        $status = 'active'; // Example status
        $stmt = $conn->prepare("INSERT INTO ot_Prescription (file_path, status) VALUES (?, ?)");
        $stmt->bind_param("ss", $filePath, $status);

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