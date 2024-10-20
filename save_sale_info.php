<?php
include_once("include/initialize.php");

$dbhost = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "shasthobdapi";

$conn = new mysqli($dbhost, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $saleData = $_POST['saleData'];
    $fileName = $_POST['fileName']; // Receive the file name from the AJAX request

    // Array to hold the inserted IDs for sale_info
    $insertedIds = [];

    foreach ($saleData as $sale) {
        $medicine_name = $sale['medicine_name'];
        $quantity = $sale['quantity'];
        $unit_price = $sale['unit_price'];
        $total_price = $sale['total_price'];
        $discount = $sale['discount'];
        $payable = $sale['payable'];

        $customerId = 1; // Assuming a default customer ID
        $customer_name = 'SasthoBD'; // Default customer name
        $customer_address = '123 Main St'; // Default address
        $customer_phone = '555-1234'; // Default phone number

        // Prepare statement for sale_info
        $stmt = $conn->prepare("INSERT INTO sale_info (inv_num, totalPrice, discount, payable_price, customerId, customer_name, customer_address, customer_phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        // Fetch the last invoice number
        $sql = "SELECT inv_num FROM sale_info ORDER BY id DESC LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $lastInvNum = $row['inv_num'];
            $numPart = intval(str_replace("INV-", "", $lastInvNum));
            $nextNum = $numPart + 1;
            $inv_num = "INV-" . str_pad($nextNum, 2, "0", STR_PAD_LEFT);
        } else {
            $inv_num = "INV-01";
        }

        // Bind and execute statement for sale_info
        $stmt->bind_param("sddddsss", $inv_num, $total_price, $discount, $payable, $customerId, $customer_name, $customer_address, $customer_phone);

        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'message' => $stmt->error]);
            exit;
        }

        // Get the last inserted ID from sale_info
        $insertedId = $stmt->insert_id; // Get the ID of the newly inserted sale_info
        $insertedIds[] = $insertedId; // Store the inserted ID

        // Insert into sale_invoice with the unique file name
        $file_path = 'C:/xampp/htdocs/shasthobdAdmin/themefiles/assets/pdf/' . $fileName; // Use the received file name
        $stmtInvoice = $conn->prepare("INSERT INTO sale_invoice (inv_num, file_path, file_name, sale_info_id) VALUES (?, ?, ?, ?)");
        $stmtInvoice->bind_param("sssi", $inv_num, $file_path, $fileName, $insertedId); // Bind the file name and sale_info ID

        if (!$stmtInvoice->execute()) {
            echo json_encode(['success' => false, 'message' => $stmtInvoice->error]);
            exit;
        }

        // Insert into sale_details
        $stmtDetails = $conn->prepare("INSERT INTO sale_details (inv_id, inv_num, medicine_name, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $stmtDetails->bind_param("iss", $insertedId, $inv_num, $medicine_name); // Bind the inserted ID, inv_num, and medicine_name

        if (!$stmtDetails->execute()) {
            echo json_encode(['success' => false, 'message' => $stmtDetails->error]);
            exit;
        }
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
?>
