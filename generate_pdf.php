<?php
include_once("include/initialize.php");

$dbhost = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "shasthobdapi";

// Create connection
$conn = new mysqli($dbhost, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['inv_num'])) {
    $inv_num = $_POST['inv_num'];

    // Query to get the sale invoice details
    $sql = "SELECT si.inv_num, si.totalPrice, si.discount, si.payable_price, si.customer_name, si.customer_phone
            FROM sale_info si WHERE si.inv_num = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $inv_num);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Return the data for PDF generation
        echo json_encode(['file_name' => $row['inv_num'] . '.pdf']);
    } else {
        echo json_encode(['error' => 'Invoice not found']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
