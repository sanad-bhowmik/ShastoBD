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

// Query for patients
$patientQuery = "SELECT OID, Name, Mobile FROM tbl_patient"; // Added Mobile to the query
$patientResult = $conn->query($patientQuery);
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    $(document).ready(function() {

        $('#patientNameSelect').change(function() {
            var selectedOption = $(this).find('option:selected');
            var mobile = selectedOption.data('mobile'); // Get mobile number
            var patientName = selectedOption.data('patient-name'); // Get patient name
            $('#PatientMobile').val(mobile); // Set mobile input value
            $('#patient_name').val(patientName); // Set patient name hidden input
        });

        $('#doctorid').change(function() {
            var selectedOption = $(this).find('option:selected');
            var doctorName = selectedOption.data('doctor-name'); // Get doctor name
            $('#doctor_name').val(doctorName); // Set doctor name hidden input
        });

        $('#appointmentForm').submit(function(event) {
            event.preventDefault(); // Prevent the default form submission

            // Collect data for appointment processing
            var formData = $(this).serialize(); // Serialize form data

            // Collect data for JSON to send to add_prescription.php
            var appointmentData = {
                appointment_number: Math.floor(Math.random() * 10000), // Replace with your actual logic to get the appointment number
                doctor_id: $('#doctorid').val(),
                doctor_name: $('#doctor_name').val(),
                patient_id: $('#patientNameSelect').val(),
                patient_name: $('#patient_name').val(),
                patient_mobile: $('#PatientMobile').val(),
                gender: $('#gender').val(),
                appointment_date: $('input[name="AppointmentDate"]').val(),
                appointment_time: $('input[name="AppointmentTime"]').val()
            };

            // Send AJAX request to add_prescription.php
            $.ajax({
                url: 'add_prescription.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(appointmentData),
                success: function(response) {
                    // Handle the response from the server
                    toastr.success('Prescription added successfully!');
                    console.log(response); // For debugging purposes

                    // After successful AJAX request, submit the form to appointment_process.php
                    $.post('appointment_process.php', formData, function(data) {
                        // Handle the response from appointment_process.php
                        // Redirect, update the UI, or show a success message as needed
                        toastr.success('Appointment processed successfully!');
                    });
                },
                error: function(xhr, status, error) {
                    // Handle any errors
                    toastr.error('Error adding prescription: ' + error);
                    console.error(xhr.responseText); // For debugging purposes
                }
            });
        });
    });
</script>

<div class="container">
    <!-- Appointment Form Section -->
    <section class="form-section">
        <form id="appointmentForm">
            <div class="card-header" style="margin-top: -19px;margin-bottom: 24px;">Add Supplier</div>

            <div class="form-row">
                <label for="doctorid">Doctor Name:
                    <select name="doctorid" class="form-control" id="doctorid" required>
                        <option value="">Select Doctor</option>
                        <?php
                        if ($doctorResult->num_rows > 0) {
                            while ($row = $doctorResult->fetch_assoc()) {
                                echo "<option value='" . $row['DOCID'] . "' data-doctor-name='" . $row['DocName'] . "'>" . $row['DocName'] . "</option>";
                            }
                        } else {
                            echo "<option value=''>No Doctors Available</option>";
                        }
                        ?>
                    </select>
                </label>

                <label for="patientNameSelect">Patient Name:
                    <select name="patientName" class="form-control" id="patientNameSelect" required>
                        <option value="">Select Patient</option>
                        <?php
                        if ($patientResult->num_rows > 0) {
                            while ($row = $patientResult->fetch_assoc()) {
                                echo "<option value='" . $row['OID'] . "' data-mobile='" . $row['Mobile'] . "' data-patient-name='" . $row['Name'] . "'>" . $row['Name'] . "</option>";
                            }
                        } else {
                            echo "<option value=''>No Patients Available</option>";
                        }
                        ?>
                    </select>
                </label>

                <label for="PatientMobile">Patient Mobile:
                    <input type="text" class="form-control" name="PatientMobile" id="PatientMobile" required placeholder="Mobile" readonly>
                </label>
            </div>

            <div class="form-row">
                <label for="gender">Gender:
                    <select name="gender" class="form-control" id="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </label>

                <label for="AppointmentTime">Appointment Time:
                    <input type="time" class="form-control" name="AppointmentTime" required>
                </label>

                <label for="AppointmentDate">Appointment Date:
                    <input type="date" class="form-control" name="AppointmentDate" required>
                </label>
            </div>

            <div class="submit-section">
                <input type="submit" value="Book" class="btn btn-secondary submit-button">
            </div>

            <!-- Hidden inputs for prescription -->
            <input type="hidden" name="doctor_name" id="doctor_name">
            <input type="hidden" name="patient_name" id="patient_name">
        </form>
    </section>
</div>

<style>
    @media (max-width: 600px) {
        .form-row {
            flex-direction: column;
            gap: 1rem;
        }

        label {
            width: 100%;
        }

        .submit-button {
            width: 100%;
        }
    }

    @media (min-width: 600px) {
        .form-row {
            flex-direction: row;
            gap: 1rem;
        }

        label {
            flex: 1;
        }
    }
</style>


<div class="container" style="margin-top: -2%;">
    <!-- Appointment Data Table Section -->
    <section class="table-section">
        <!-- Filter Section -->
        <div class="filter-section" style="display: flex; justify-content: space-between; align-items: flex-end; gap: 10px; margin-bottom: 30px;">
            <div style="flex: 1;">
                <label for="filterAppointmentNumber">Appointment Number:</label>
                <input type="text" id="filterAppointmentNumber" placeholder="Filter by Appointment Number" style="width: 100%;">
            </div>

            <div style="flex: 1;">
                <label for="filterPatientName">Patient Name:</label>
                <input type="text" id="filterPatientName" placeholder="Filter by Patient Name" style="width: 100%;">
            </div>

            <div style="flex: 1;">
                <label for="filterMobile">Mobile:</label>
                <input type="text" id="filterMobile" placeholder="Filter by Mobile" style="width: 100%;">
            </div>

            <div style="flex: 1;">
                <label for="filterDoctorName">Doctor Name:</label>
                <input type="text" id="filterDoctorName" placeholder="Filter by Doctor Name" style="width: 100%;">
            </div>
            <div style="flex: 1;">
                <label for="filterDate">Date:</label>
                <input type="date" id="filterDate" style="width: 100%;">
            </div>

            <div style="flex: 0; margin-left: 10px;">
                <button id="clearButton" class="btn-warning" style="padding: 6px 12px;">Clear</button>
            </div>
        </div>

        <table class="appointment-table" id="appointmentTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Appointment Number</th>
                    <th>Patient Name</th>
                    <th>Mobile</th>
                    <th>Gender</th>
                    <th>Appointment Date</th>
                    <th>Appointment Time</th>
                    <th>Doctor Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($appointmentResult->num_rows > 0) {
                    while ($row = $appointmentResult->fetch_assoc()) {
                        echo "<tr>
                            <td>" . $row['id'] . "</td>
                            <td class='appointment-number'>" . $row['appointment_number'] . "</td>
                            <td class='patient-name'>" . $row['PatientName'] . "</td>
                            <td class='patient-mobile'>" . $row['PatientjMobile'] . "</td>
                            <td>" . $row['gender'] . "</td>
                            <td class='appointment-date'>" . $row['AppointmentDate'] . "</td>
                            <td>" . $row['AppointmentTime'] . "</td>
                            <td class='doctor-name'>" . $row['DoctorName'] . "</td>
                            <td>" . $row['Status'] . "</td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>No appointments found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </section>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const filterAppointmentNumber = document.getElementById("filterAppointmentNumber");
        const filterPatientName = document.getElementById("filterPatientName");
        const filterMobile = document.getElementById("filterMobile");
        const filterDoctorName = document.getElementById("filterDoctorName");
        const filterDate = document.getElementById("filterDate");
        const table = document.getElementById("appointmentTable");
        const tbody = table.querySelector("tbody");

        function filterTable() {
            const appointmentNumber = filterAppointmentNumber.value.toLowerCase();
            const patientName = filterPatientName.value.toLowerCase();
            const mobile = filterMobile.value.toLowerCase();
            const doctorName = filterDoctorName.value.toLowerCase();
            const date = filterDate.value;

            const rows = tbody.querySelectorAll("tr");

            rows.forEach(row => {
                const appointmentNumberText = row.querySelector(".appointment-number").textContent.toLowerCase();
                const patientNameText = row.querySelector(".patient-name").textContent.toLowerCase();
                const mobileText = row.querySelector(".patient-mobile").textContent.toLowerCase();
                const doctorNameText = row.querySelector(".doctor-name").textContent.toLowerCase();
                const dateText = row.querySelector(".appointment-date").textContent;

                const isMatch =
                    (appointmentNumber === "" || appointmentNumberText.includes(appointmentNumber)) &&
                    (patientName === "" || patientNameText.includes(patientName)) &&
                    (mobile === "" || mobileText.includes(mobile)) &&
                    (doctorName === "" || doctorNameText.includes(doctorName)) &&
                    (date === "" || dateText === date);

                row.style.display = isMatch ? "" : "none";
            });
        }

        filterAppointmentNumber.addEventListener("keyup", filterTable);
        filterPatientName.addEventListener("keyup", filterTable);
        filterMobile.addEventListener("keyup", filterTable);
        filterDoctorName.addEventListener("keyup", filterTable);
        filterDate.addEventListener("change", filterTable);

        // Clear filters
        document.getElementById("clearButton").addEventListener("click", function() {
            filterAppointmentNumber.value = '';
            filterPatientName.value = '';
            filterMobile.value = '';
            filterDoctorName.value = '';
            filterDate.value = '';
            filterTable();
        });
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