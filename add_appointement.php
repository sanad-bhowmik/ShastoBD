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

$patientQuery = "SELECT OID, Name FROM tbl_patient WHERE Active = 1";
$patientResult = $conn->query($patientQuery);
?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<!-- Include Toastr CSS and JS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<div class="form-container">
    <h2>Book Appointment</h2>
    <form action="appointment_process.php" method="POST">
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
            <label for="patientid">Patient Name:
                <select name="patientid" id="patientid" required disabled>
                    <option value="">Select Patient</option>
                    <?php
                    if ($patientResult->num_rows > 0) {
                        while ($row = $patientResult->fetch_assoc()) {
                            echo "<option value='" . $row['OID'] . "'>" . $row['Name'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>No Patients Available</option>";
                    }
                    ?>
                </select>
            </label>
        </div>
        <div class="form-row">
            <label for="AppointmentTime">Appointment Time:
                <input type="time" name="AppointmentTime" required>
            </label>
            <label for="AppointmentDate">Appointment Date:
                <input type="date" name="AppointmentDate" required>
            </label>
        </div>
        <div class="form-row">
            <label for="PatientMobile">Patient Mobile:
                <input type="text" name="PatientMobile" id="PatientMobile" readonly required>
            </label>
            <label for="gender">Gender:
                <select name="gender" id="gender" readonly required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </label>
        </div>
        <input type="submit" value="Book Appointment">
    </form>
</div>

<script>
    $(document).ready(function() {
        $('#doctorid').select2({
            placeholder: "Select Doctor",
            allowClear: true
        });
        $('#patientid').select2({
            placeholder: "Select Patient",
            allowClear: true
        });

        $('#doctorid').on('change', function() {
            var doctorSelected = $(this).val();
            if (doctorSelected) {
                $('#patientid').prop('disabled', false);
            } else {
                $('#patientid').prop('disabled', true);
                $('#PatientMobile').val('');
                $('#gender').val('').trigger('change');
            }
        });

        $('#patientid').on('change', function() {
            var patientId = $(this).val();
            if (patientId) {
                $.ajax({
                    url: 'get_patient_details.php',
                    type: 'GET',
                    data: {
                        id: patientId
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data) {
                            $('#PatientMobile').val(data.Mobile);
                            $('#gender').val(data.Gender).trigger('change');
                        } else {
                            $('#PatientMobile').val('');
                            $('#gender').val('').trigger('change');
                        }

                        $('#PatientMobile').prop('readonly', true);
                        $('#gender').prop('disabled', true);
                    },
                    error: function() {
                        alert('Error retrieving patient details.');
                    }
                });
            } else {
                $('#PatientMobile').val('');
                $('#gender').val('').trigger('change');
            }
        });

        <?php if (isset($_SESSION['success_message'])): ?>
            toastr.success("<?php echo $_SESSION['success_message']; ?>");
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
    });
</script>


<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    .form-container {
        background-color: #fff;
        margin: 50px auto;
        padding: 20px;
        width: 96%;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .form-container h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }

    .form-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .form-row label {
        width: 45%;
        font-weight: bold;
    }

    input[type="text"],
    input[type="time"],
    input[type="date"],
    select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        margin-top: 5px;
        font-size: 16px;
    }

    input[type="submit"] {
        background-color: #28a745;
        color: white;
        padding: 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        width: 100%;
        margin-top: 15px;
    }

    input[type="submit"]:hover {
        background-color: #218838;
    }
</style>