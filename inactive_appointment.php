<?php
include_once("include/header.php");

// Database connection
$dbhost = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "shasthobdapi";

$conn = new mysqli($dbhost, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get data from appointmentview table
$sql = "SELECT appointment_number, PatientName, PatientMobile, Appointment_Time, AppointmentDate, DocName, Status FROM appointmentview WHERE Status = 'Inactive'";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script>
        function filterTable() {
            const appointmentNumberInput = document.getElementById("appointment_number").value.toLowerCase();
            const patientNameInput = document.getElementById("patient_name").value.toLowerCase();
            const patientMobileInput = document.getElementById("patient_mobile").value.toLowerCase();
            const appointmentDateInput = document.getElementById("appointment_date").value;

            const table = document.getElementById("dataTable");
            const rows = table.getElementsByTagName("tr");

            for (let i = 1; i < rows.length; i++) { // Start from 1 to skip the header row
                const cells = rows[i].getElementsByTagName("td");
                const appointmentNumber = cells[1].textContent.toLowerCase();
                const patientName = cells[2].textContent.toLowerCase();
                const patientMobile = cells[3].textContent.toLowerCase();
                const appointmentDate = cells[5].textContent;

                // Check if the row matches the filter criteria
                const match =
                    (appointmentNumber.includes(appointmentNumberInput) || appointmentNumberInput === '') &&
                    (patientName.includes(patientNameInput) || patientNameInput === '') &&
                    (patientMobile.includes(patientMobileInput) || patientMobileInput === '') &&
                    (appointmentDate.includes(appointmentDateInput) || appointmentDateInput === '');

                // Show or hide the row based on the match
                rows[i].style.display = match ? "" : "none";
            }
        }

        function clearFilters() {
            document.getElementById("appointment_number").value = "";
            document.getElementById("patient_name").value = "";
            document.getElementById("patient_mobile").value = "";
            document.getElementById("appointment_date").value = "";

            // Call filterTable to show all rows
            filterTable();
        }
    </script>
</head>

<body>

    <section class="container">
        <!-- Filter Form -->
        <form style="margin-bottom: 20px;">
            <input type="text" id="appointment_number" placeholder="Appointment Number" onkeyup="filterTable()" style="height: 36px;width: 152px;text-align: center; margin-right: 10px;border-radius: 10px;" />
            <input type="text" id="patient_name" placeholder="Patient Name" onkeyup="filterTable()" style="height: 36px;width: 152px;text-align: center; margin-right: 10px;border-radius: 10px;" />
            <input type="text" id="patient_mobile" placeholder="Patient Mobile" onkeyup="filterTable()" style="height: 36px;width: 152px;text-align: center; margin-right: 10px;border-radius: 10px;" />
            <input type="date" id="appointment_date" oninput="filterTable()" style="height: 36px;width: 152px;text-align: center; margin-right: 10px;border-radius: 10px;" />
            <button type="button" class="btn-danger" onclick="clearFilters()" style="padding: 4px;border-radius: 10px;width: 53px;height: 31px;">Clear</button> <!-- Clear Button -->
        </form>

        <table id="dataTable">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Appointment Number</th>
                    <th>Patient Name</th>
                    <th>Patient Mobile</th>
                    <th>Appointment Time</th>
                    <th>Appointment Date</th>
                    <th>Doctor Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    $serialNumber = 1; // Initialize serial number
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $serialNumber . "</td>"; // Display serial number
                        echo "<td>" . $row["appointment_number"] . "</td>";
                        echo "<td>" . $row["PatientName"] . "</td>";
                        echo "<td>" . $row["PatientMobile"] . "</td>";
                        echo "<td>" . $row["Appointment_Time"] . "</td>";
                        echo "<td>" . $row["AppointmentDate"] . "</td>";
                        echo "<td>" . $row["DocName"] . "</td>";
                        echo "<td>" . $row["Status"] . "</td>";
                        echo "</tr>";
                        $serialNumber++; // Increment serial number
                    }
                } else {
                    echo "<tr><td colspan='8'>No records found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </section>

</body>

</html>

<?php
// Close the database connection
$conn->close();
?>





<style>
    .container {
        width: 96%;
        margin: 50px auto;
        background-color: #fff;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    #dataTable {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        background-color: #fff;
    }

    #dataTable thead {
        background-color: #f2f2f2;
    }

    #dataTable th,
    #dataTable td {
        padding: 10px;
        text-align: left;
        border: 1px solid #ccc;
    }

    #dataTable th {
        background-color: #e0e0e0;
        font-weight: bold;
    }

    #dataTable tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    #dataTable tr:hover {
        background-color: #f1f1f1;
    }

    @media (max-width: 600px) {
        body {
            padding: 10px;
        }

        #dataTable {
            font-size: 14px;
        }

        #dataTable th,
        #dataTable td {
            padding: 8px;
        }
    }
</style>