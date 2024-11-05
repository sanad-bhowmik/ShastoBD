<?php
include_once("include/initialize.php");

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

if (isset($_POST['inv_num'])) {
    $inv_num = mysqli_real_escape_string($conn, $_POST['inv_num']);

    // Fetch the invoice details before dropping
    $sql_fetch = "SELECT * FROM sale_info WHERE inv_num = '$inv_num' AND status = 1";
    $result = mysqli_query($conn, $sql_fetch);

    if ($result && mysqli_num_rows($result) > 0) {
        $invoice = mysqli_fetch_assoc($result);
        
        // Prepare the insert query for drop_invoice table
        $totalPrice = $invoice['totalPrice'];
        $discount = $invoice['discount'];
        $payable_price = $invoice['payable_price'];
        $customerId = $invoice['customerId'];
        $customer_name = $invoice['customer_name'];
        $customer_address = $invoice['customer_address'];
        $customer_phone = $invoice['customer_phone'];

        $sql_insert = "INSERT INTO drop_invoice (inv_num, totalPrice, discount, payable_price, customerId, customer_name, customer_address, customer_phone, status) 
                       VALUES ('$inv_num', $totalPrice, $discount, $payable_price, $customerId, '$customer_name', '$customer_address', '$customer_phone', 0)";

        // Begin transaction
        $conn->begin_transaction();

        try {
            // Insert into drop_invoice table
            if (!mysqli_query($conn, $sql_insert)) {
                throw new Exception("Error inserting into drop_invoice: " . mysqli_error($conn));
            }

            // Update the status to 0 in the sale_info table
            $sql_update = "UPDATE sale_info SET status = 0 WHERE inv_num = '$inv_num' AND status = 1";

            if (!mysqli_query($conn, $sql_update)) {
                throw new Exception("Error updating sale_info: " . mysqli_error($conn));
            }

            // Commit the transaction
            $conn->commit();
            echo "Invoice dropped successfully."; // Success message
        } catch (Exception $e) {
            // Rollback the transaction if any query fails
            $conn->rollback();
            echo $e->getMessage(); // Display error message
        }
    } else {
        echo "Invoice not found or already dropped."; // Message if invoice does not exist
    }
} else {
    echo "No invoice number provided."; // Message if no invoice number is sent
}

// Close the connection
$conn->close();
?>
