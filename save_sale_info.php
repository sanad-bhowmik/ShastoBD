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

    foreach ($saleData as $sale) {
        $medicine_name = $sale['medicine_name'];
        $quantity = $sale['quantity'];
        $unit_price = $sale['unit_price'];
        $total_price = $sale['total_price'];
        $discount = $sale['discount'];
        $payable = $sale['payable'];

        $customerId = 1;
        $customer_name = 'SasthoBD';
        $customer_address = '123 Main St';
        $customer_phone = '555-1234';

        $stmt = $conn->prepare("INSERT INTO sale_info (inv_num, totalPrice, discount, payable_price, customerId, customer_name, customer_address, customer_phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
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

        $stmt->bind_param("sddddsss", $inv_num, $total_price, $discount, $payable, $customerId, $customer_name, $customer_address, $customer_phone);

        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'message' => $stmt->error]);
            exit;
        }
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
?>
