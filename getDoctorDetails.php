<?php
// Database connection parameters
$dbhost = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "shasthobdapi";

// Create a connection to the database
$conn = new mysqli($dbhost, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the doctor name from the POST request
if (isset($_POST['name'])) {
    $doctorName = $conn->real_escape_string(trim($_POST['name']));

    // Prepare SQL statement to prevent SQL injection
    $sql = "SELECT DocDegree, DocAddress, MobileNum FROM doctors WHERE DocName = '$doctorName'";
    
    $result = $conn->query($sql);
    
    // Check if the doctor exists
    if ($result->num_rows > 0) {
        // Fetch associative array
        $doctorDetails = $result->fetch_assoc();
        // Return the details as JSON
        echo json_encode($doctorDetails);
    } else {
        // No doctor found, return empty JSON
        echo json_encode([]);
    }
} else {
    // If name parameter is not set, return an error
    echo json_encode(['error' => 'Doctor name not provided']);
}

// Close the connection
$conn->close();
?>
