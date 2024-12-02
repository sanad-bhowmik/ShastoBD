<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the sale data (if needed for saving in the database)
    $saleData = json_decode($_POST['saleData'], true);
    
    // Retrieve the PDF file sent via the form data
    $pdf = $_FILES['pdf'];

    // Path to save the PDF
    $uploadDir = 'C:/xampp/htdocs/shasthobdAdmin/themefiles/assets/pdf/invoice/';  // Set path to the folder where PDFs should be saved
    $fileName = basename($pdf['name']);  // Get the original file name

    // Ensure the directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Create the directory if it does not exist
    }

    // Full path to save the PDF
    $filePath = $uploadDir . $fileName;

    // Check if the file is uploaded without errors
    if ($pdf['error'] === UPLOAD_ERR_OK) {
        // Move the uploaded PDF to the desired directory
        if (move_uploaded_file($pdf['tmp_name'], $filePath)) {
            // Optionally, you can save sale data to a database here

            // Response to indicate successful PDF upload
            echo json_encode(['success' => true, 'message' => 'Data and PDF saved successfully', 'filePath' => $filePath]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to move the PDF file']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error in file upload']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
