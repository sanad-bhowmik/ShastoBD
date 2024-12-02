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

// Query to get data from appointmentview table and related file_path from ot_prescription table
$sql = "
    SELECT a.appointment_number, a.PatientName, a.PatientMobile, a.Appointment_Time, a.AppointmentDate, a.DocName, a.Status, 
           p.file_path
    FROM appointmentview a
    LEFT JOIN ot_prescription p ON a.appointment_number = p.appointment_number
    WHERE a.Status = 'Inactive'
";

$result = $conn->query($sql);

// Check if query execution was successful
if ($result === false) {
    echo "Error: " . $conn->error; // Display SQL error message
    exit; // Stop execution if the query fails
}
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

    <div class="app-main__inner">
        <div class="row">
            <div class="col-md-12">
                <div class="main-card mb-3 card">
                    <div class="card-header">
                        Inactive Appointments
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div class="filter-container">
                                <input type="text" id="appointment_number" class="form-control"
                                    placeholder="Appointment Number" onkeyup="filterTable()" />
                                <input type="text" id="patient_name" placeholder="Patient Name" onkeyup="filterTable()"
                                    class="form-control" />
                                <input type="text" id="patient_mobile" placeholder="Patient Mobile"
                                    onkeyup="filterTable()" class="form-control" />
                                <input type="date" id="appointment_date" oninput="filterTable()" class="form-control" />
                                <button type="button" class="btn btn-danger"
                                    onclick="clearFilters()">Clear</button><!-- Clear Button -->
                            </div>
                            <table class="align-middle mb-0 table table-borderless table-striped table-hover"
                                id="dataTable">
                                <thead>
                                    <tr>
                                        <th class="text-center">Sl</th>
                                        <th class="text-center">Appointment Number</th>
                                        <th class="text-center">Patient Name</th>
                                        <th class="text-center">Patient Mobile</th>
                                        <th class="text-center">Appointment Time</th>
                                        <th class="text-center">Appointment Date</th>
                                        <th class="text-center">Doctor Name</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">PDF</th>
                                    </tr>
                                </thead>
                                <tbody id="stockTableBody">
                                    <?php
                                    if ($result->num_rows > 0) {
                                        $serialNumber = 1; 
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . $serialNumber . "</td>"; // Display serial number
                                            echo "<td>" . $row["appointment_number"] . "</td>";
                                            echo "<td>" . $row["PatientName"] . "</td>";
                                            echo "<td>" . $row["PatientMobile"] . "</td>";
                                            echo "<td>" . $row["Appointment_Time"] . "</td>";
                                            echo "<td>" . $row["AppointmentDate"] . "</td>";
                                            echo "<td>" . $row["DocName"] . "</td>";
                                            echo "<td><span class='badge badge-danger'>" . htmlspecialchars($row["Status"]) . "</span></td>";

                                            // Check if the file_path exists
                                            if ($row["file_path"]) {
                                                // Generate the image for the PDF column with a link to the PDF
                                                $pdfPath = $row["file_path"];
                                                echo "<td><a href='$pdfPath' target='_blank'><img src='./themefiles/assets/images/pdf.png' alt='Image' style='width: 24px; height: 24px;cursor: pointer;'></a></td>";
                                            } else {
                                                echo "<td>No PDF</td>";
                                            }

                                            echo "</tr>";
                                            $serialNumber++;
                                        }
                                    } else {
                                        echo "<tr><td colspan='9'>No records found</td></tr>";
                                    }
                                    ?>
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>

<?php
// Close the database connection
$conn->close();
?>

<style>
    .filter-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 25px;
    }

    .filter-container input {
        flex: 1 1 200px;
    }

    .filter-container button {
        flex: 0 0 auto;
    }
</style>
