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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id']; // Get the medicine id
    $name = $_POST['name']; // Get the new name

    $stmt = $conn->prepare("UPDATE medicine SET name = ? WHERE id = ?");
    $stmt->bind_param("si", $name, $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }

    $stmt->close();
}

$conn->close();
?>
