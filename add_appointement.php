<?php
include_once("include/header.php");
$dbhost = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "shasthobdapi";

$conn = new mysqli($dbhost, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$doctorQuery = "SELECT DOCID, DocName FROM tbl_doctor WHERE Active = 1";
$doctorResult = $conn->query($doctorQuery);

// Query for appointment data
$appointmentQuery = "SELECT * FROM appointmentview";
$appointmentResult = $conn->query($appointmentQuery);
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<div class="container">
    <!-- Appointment Form Section -->
    <section class="form-section">
        <form action="appointment_process.php" method="POST" class="appointment-form">
            <div class="form-row">
                <label for="doctorid">Doctor Name:
                    <select name="doctorid" id="doctorid" required>
                        <option value="">Select Doctor</option>
                        <?php
                        if ($doctorResult->num_rows > 0) {
                            while ($row = $doctorResult->fetch_assoc()) {
                                echo "<option value='" . $row['DOCID'] . "'>" . $row['DocName'] . "</option>";
                            }
                        } else {
                            echo "<option value=''>No Doctors Available</option>";
                        }
                        ?>
                    </select>
                </label>

                <label for="patientName">Patient Name:
                    <input type="text" name="patientName" id="patientName" required placeholder="Name" style="height: 29px;">
                </label>

                <label for="PatientMobile">Patient Mobile:
                    <input type="text" name="PatientMobile" id="PatientMobile" required placeholder="Mobile" style="height: 29px;">
                </label>
            </div>

            <div style="margin-top: 2%;">
                <label for="gender" style="width: 30%;margin-right: 10px;">Gender:
                    <select name="gender" id="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </label>

                <label for="AppointmentTime" style="margin-right: 10px;width: 30%;">Appointment Time:
                    <input type="time" name="AppointmentTime" required>
                </label>

                <label for="AppointmentDate" style="margin-right: 10px;width: 27%;">Appointment Date:
                    <input type="date" name="AppointmentDate" required>
                </label>

                <input type="submit" value="Book" class="submit-button">
            </div>
        </form>
    </section>
</div>

<div class="container" style="margin-top: -2%;">
    <!-- Appointment Data Table Section -->
    <section class="table-section">
        <!-- Filter Section -->
        <div class="filter-section">
            <div style="width: 144px;margin-right: 0%;">
                <label for="filterDoctorName">Doctor Name:</label>
                <input type="text" id="filterDoctorName" placeholder="Filter by Doctor Name">
            </div>

            <div style="width: 144px;">
                <label for="filterPatientName">Patient Name:</label>
                <input type="text" id="filterPatientName" placeholder="Filter by Patient Name">
            </div>

            <div style="width: 144px;">
                <label for="filterMobile">Mobile:</label>
                <input type="text" id="filterMobile" placeholder="Filter by Mobile">
            </div>

            <div style="width: 144px;margin-right: 44%;">
                <label for="filterDate">Date:</label>
                <input type="date" id="filterDate">
            </div>

            <div style="position: absolute;z-index: 1000;margin-left: 43%;margin-top: 37px;">
                <button id="clearButton" class="btn-warning">Clear</button>
            </div>
        </div>

        <table class="appointment-table" id="appointmentTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Doctor Name</th>
                    <th>Patient Name</th>
                    <th>Mobile</th>
                    <th>Gender</th>
                    <th>Appointment Date</th>
                    <th>Appointment Time</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($appointmentResult->num_rows > 0) {
                    $index = 1; // Initialize counter for sequential numbering
                    while ($row = $appointmentResult->fetch_assoc()) {
                        echo "<tr>
                            <td>" . $index++ . "</td>
                            <td class='doctor-name'>" . $row['DocName'] . "</td>
                            <td class='patient-name'>" . $row['PatientName'] . "</td>
                            <td class='patient-mobile'>" . $row['PatientMobile'] . "</td>
                            <td>" . $row['ParientGender'] . "</td>
                            <td class='appointment-date'>" . $row['AppointmentDate'] . "</td>
                            <td>" . $row['Appointment_Time'] . "</td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No Appointments Available</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </section>
</div>

<script>
    document.getElementById('clearButton').addEventListener('click', function() {
        // Reload the page
        location.reload();
    });
    // Filter function
    document.addEventListener("DOMContentLoaded", function() {
        const filterDoctorName = document.getElementById("filterDoctorName");
        const filterPatientName = document.getElementById("filterPatientName");
        const filterMobile = document.getElementById("filterMobile");
        const filterDate = document.getElementById("filterDate");
        const table = document.getElementById("appointmentTable");
        const rows = table.getElementsByTagName("tbody")[0].getElementsByTagName("tr");

        function filterTable() {
            const doctorName = filterDoctorName.value.toLowerCase();
            const patientName = filterPatientName.value.toLowerCase();
            const mobile = filterMobile.value;
            const date = filterDate.value;

            for (let i = 0; i < rows.length; i++) {
                const rowDoctorName = rows[i].getElementsByClassName("doctor-name")[0].textContent.toLowerCase();
                const rowPatientName = rows[i].getElementsByClassName("patient-name")[0].textContent.toLowerCase();
                const rowMobile = rows[i].getElementsByClassName("patient-mobile")[0].textContent;
                const rowDate = rows[i].getElementsByClassName("appointment-date")[0].textContent;

                // Check if the row matches all filters
                const matchesDoctor = rowDoctorName.includes(doctorName);
                const matchesPatient = rowPatientName.includes(patientName);
                const matchesMobile = rowMobile.includes(mobile);
                const matchesDate = !date || rowDate === date;

                if (matchesDoctor && matchesPatient && matchesMobile && matchesDate) {
                    rows[i].style.display = ""; // Show row
                } else {
                    rows[i].style.display = "none"; // Hide row
                }
            }
        }

        // Add event listeners for filtering
        filterDoctorName.addEventListener("input", filterTable);
        filterPatientName.addEventListener("input", filterTable);
        filterMobile.addEventListener("input", filterTable);
        filterDate.addEventListener("change", filterTable);
    });
    $(document).ready(function() {
        $('#doctorid').select2({
            placeholder: "Select Doctor",
            allowClear: true
        });

        <?php if (isset($_SESSION['success_message'])): ?>
            toastr.success("<?php echo $_SESSION['success_message']; ?>");
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
    });
</script>

<style>
    .filter-section {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
        justify-content: space-between;
    }

    .filter-section label {
        font-weight: bold;
        margin-right: 5px;
    }

    .filter-section input[type="text"],
    .filter-section input[type="date"] {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    .container {
        width: 96%;
        margin: 50px auto;
        background-color: #fff;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    h2 {
        text-align: center;
        color: #333;
    }

    .form-section,
    .table-section {
        margin-bottom: 30px;
    }

    .form-row {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        align-items: center;
    }

    .form-row label,
    .form-row .submit-button {
        flex: 1 1 calc(25% - 20px);
        font-weight: bold;
    }

    input[type="text"],
    input[type="time"],
    input[type="date"],
    select {
        width: 100%;
        padding: 8px;
        border: 1px solid #a59595;
        border-radius: 4px;
        box-sizing: border-box;
        margin-top: 5px;
        font-size: 14px;
    }

    .submit-button {
        width: 80px;
        /* Make the button smaller in width */
        background-color: #28a745;
        color: white;
        padding: 8px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        text-align: center;
        height: fit-content;
        margin-top: 5px;
    }

    .submit-button:hover {
        background-color: #218838;
    }

    .appointment-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .appointment-table th,
    .appointment-table td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: center;
    }

    .appointment-table th {
        background-color: #f4f4f4;
        color: #333;
    }
</style>