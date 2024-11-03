<?php
$dbhost = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "shasthobdapi";

$conn = new mysqli($dbhost, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['medicineName'])) {
    $medicineName = $_POST['medicineName'];

    // Fetch the group_id for the selected medicine
    $medicineQuery = "SELECT group_id FROM medicine WHERE name = ?";
    $stmt = $conn->prepare($medicineQuery);
    $stmt->bind_param("s", $medicineName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $groupId = $row['group_id'];

        // Fetch the group name from medicine_group based on group_id
        $groupQuery = "SELECT name FROM medicine_group WHERE id = ?";
        $stmt = $conn->prepare($groupQuery);
        $stmt->bind_param("i", $groupId);
        $stmt->execute();
        $groupResult = $stmt->get_result();

        if ($groupResult->num_rows > 0) {
            $groupRow = $groupResult->fetch_assoc();
            echo $groupRow['name']; // Output only the group name
        } else {
            echo "No Group Found"; // Optional: Message for missing group
        }
    } else {
        echo "Invalid Medicine Selected"; // Optional: Message for invalid selection
    }
    $stmt->close();
}

$conn->close();
?>
