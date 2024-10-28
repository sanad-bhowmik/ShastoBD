<?php
$dbhost = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "shasthobdapi";

// Create connection
$con = mysqli_connect($dbhost, $dbusername, $dbpassword, $dbname);

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and sanitize
    $patientName = mysqli_real_escape_string($con, $_POST['patientName']);
    $patientMobile = mysqli_real_escape_string($con, $_POST['patientMobile']);
    $patientEmail = mysqli_real_escape_string($con, $_POST['patientEmail']);
    $patientAddress = mysqli_real_escape_string($con, $_POST['patientAddress']);
    $patientGender = mysqli_real_escape_string($con, $_POST['patientGender']);

    // SQL query to insert patient data
    $sql = "INSERT INTO tbl_patient (Name, Mobile, Email, Address, Gender, Active) 
            VALUES ('$patientName', '$patientMobile', '$patientEmail', '$patientAddress', '$patientGender', '1')";

    // Execute the query
    if (mysqli_query($con, $sql)) {
        // Redirect to patients.php with a success alert
        mysqli_close($con);
        header("Location: patients.php?msg=success");
        exit(); // Make sure to exit after the redirect
    } else {
        echo "Error: " . mysqli_error($con);
    }

    // Close the database connection
    mysqli_close($con);
}
?>
