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

// Query to fetch doctor names
$doctorQuery = "SELECT DOCID, DocName,MobileNum FROM tbl_doctor WHERE Active = 1";
$doctorResult = $conn->query($doctorQuery);

// Query to fetch patient names
$patientQuery = "SELECT OID, Name,Address,Gender FROM tbl_patient WHERE Active = 1";
$patientResult = $conn->query($patientQuery);
?>

<!-- Include jQuery, Select2, Toastr, and jsPDF -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<div class="form-container" style="margin-left: 9%;margin-top: 32px;max-width: 866px;padding: 20px;border: 1px solid #ccc;border-radius: 8px;background-color: #f9f9f9;box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;">
    <h2 style="text-align: center; margin-bottom: 20px;">Add Prescription</h2>
    <form id="prescriptionForm" method="POST" style="font-size: 15px;">
        <div class="form-row" style="margin-bottom: 15px; display: flex; flex-wrap: wrap;">
            <div style="flex: 1; padding-right: 10px;">
                <label for="doctorid" style="display: block; margin-bottom: 5px;">Doctor:
                    <select name="doctorid" id="doctorid" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        <option value="">Select Doctor</option>
                        <?php
                        if ($doctorResult->num_rows > 0) {
                            while ($row = $doctorResult->fetch_assoc()) {
                                echo "<option value='" . $row['DOCID'] . "' data-mobile='" . $row['MobileNum'] . "'>" . $row['DocName'] . "</option>";
                            }
                        } else {
                            echo "<option value=''>No Doctors Available</option>";
                        }
                        ?>
                    </select>

                </label>
            </div>
            <div style="flex: 1; padding-left: 10px;">
                <label for="patientid" style="display: block; margin-bottom: 5px;">Patient:
                    <select name="patientid" id="patientid" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        <option value="">Select Patient</option>
                        <?php
                        if ($patientResult->num_rows > 0) {
                            while ($row = $patientResult->fetch_assoc()) {
                                echo "<option value='" . $row['OID'] . "' data-gender='" . $row['Gender'] . "'>" . $row['Name'] . "</option>";
                            }
                        } else {
                            echo "<option value=''>No Patients Available</option>";
                        }
                        ?>
                    </select>

                </label>
            </div>
        </div>
        <div class="form-row" style="margin-bottom: 15px; display: flex; flex-wrap: wrap;">
            <div style="flex: 1; padding-left: 10px;">
                <label for="medicine" style="display: block; margin-bottom: 5px;">Medicine Name:
                    <input type="text" name="medicine" id="medicine" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                </label>
            </div>
            <div style="flex: 1; padding-left: 10px;">
                <label for="duration" style="display: block; margin-bottom: 5px;">Duration:
                    <input type="text" name="duration" id="duration" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                </label>
            </div>
        </div>

        <div class="form-row" style="margin-bottom: 15px; display: flex; flex-wrap: wrap;">
            <div style="flex: 1; padding-left: 10px;">
                <label for="dosage" style="display: block; margin-bottom: 5px;">Dosage:
                    <input type="text" name="dosage" id="dosage" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                </label>
            </div>
            <div style="flex: 1; padding-left: 10px;">
                <label for="notes" style="display: block; margin-bottom: 5px;">Additional Notes:
                    <textarea name="notes" id="notes" rows="4" style="width: 100%;padding: 8px;border: 1px solid #ccc;border-radius: 4px;height: 40px;"></textarea>
                </label>
            </div>
        </div>
        <input type="submit" value="Add Prescription" style="width: 16%;padding: 10px;color: white;border: none;border-radius: 4px;cursor: pointer;margin-left: 44%;background-image: radial-gradient(circle farthest-corner at 10% 20%, rgba(14,174,87,1) 0%, rgba(12,116,117,1) 90%);">
    </form>
</div>

<script>
    $(document).ready(function() {
        // Initialize Select2
        $('#doctorid').select2({
            placeholder: "Select Doctor",
            allowClear: true
        });
        $('#patientid').select2({
            placeholder: "Select Patient",
            allowClear: true
        });

        // Handle form submission
        $('#prescriptionForm').on('submit', function(e) {
            e.preventDefault();

            var refNo = Math.floor(100000 + Math.random() * 900000);
            var formData = $(this).serialize() + '&refNo=' + refNo;

            // Send AJAX request
            $.ajax({
                url: 'prescription_process.php',
                type: 'POST',
                data: formData,
                success: function(response) {
                    toastr.success('Prescription added successfully!');

                    // Create PDF after successful insertion
                    createPDF(formData);

                    $('#prescriptionForm')[0].reset();
                },
                error: function(xhr, status, error) {
                    toastr.error('Error adding prescription: ' + error);
                }
            });
        });
    });

    function createPDF(formData) {
        const {
            jsPDF
        } = window.jspdf;
        const pdf = new jsPDF();

        const params = new URLSearchParams(formData);
        const doctorName = $('#doctorid option:selected').text();
        const doctorMobile = $('#doctorid option:selected').data('mobile'); // Get doctor's mobile number
        const patientName = $('#patientid option:selected').text();
        const patientGender = $('#patientid option:selected').data('gender'); // Get patient's gender
        const medicine = params.get('medicine');
        const duration = params.get('duration');
        const dosage = params.get('dosage');
        const notes = params.get('notes');
        const refNo = params.get('refNo');

        // Define colors
        const primaryColor = [3, 169, 244]; // Light Blue for title
        const secondaryColor = [40, 40, 40]; // Dark text color
        const borderColor = [0, 0, 0]; // Black for borders

        // 1. Header Section: Doctor Information
        pdf.setFontSize(18);
        pdf.setTextColor(...primaryColor);
        pdf.text('Dr. ' + doctorName, 20, 20);
        pdf.setFontSize(12);
        pdf.setTextColor(100);
        pdf.text('Mobile: ' + doctorMobile, 20, 26); // Doctor's mobile number
        pdf.text('Certification: ' + refNo, 20, 32); // Doctor's certification number

        // 2. Patient Information Section
        pdf.setFontSize(12);
        pdf.setTextColor(...secondaryColor);
        pdf.text('Patient Name: ' + patientName, 20, 50);
        pdf.text('Gender: ' + patientGender, 20, 56); // Patient's gender
        pdf.text('Date: ' + new Date().toLocaleDateString(), 150, 68); // Today's date

        // 3. Medicine Details Section
        pdf.setTextColor(...primaryColor);
        pdf.setFontSize(14);
        pdf.text('Medicine Details', 20, 90);

        // Medicine Details Box with outer border
        pdf.setDrawColor(...borderColor); // Set border color to black
        pdf.setLineWidth(0.5);

        // Inner border for Medicine Details
        pdf.setDrawColor(...borderColor); // Set inner border color to black
        pdf.setLineWidth(0.5);

        // Populate the Medicine Details
        pdf.setFontSize(12);
        pdf.setTextColor(...secondaryColor);
        pdf.text('Medicine: ' + (medicine || '_________________'), 25, 105);
        pdf.text('Duration: ' + (duration || '_________________'), 25, 113);
        pdf.text('Dosage: ' + (dosage || '_________________'), 25, 121);

        // 4. Notes Section
        if (notes) {
            pdf.setFontSize(14);
            pdf.setTextColor(...primaryColor);
            pdf.text('Notes:', 20, 135);

            pdf.setDrawColor(...borderColor); // Set notes section border to black
            pdf.rect(20, 140, 170, 30); // Rectangle border for the notes section
            pdf.setFontSize(12);
            pdf.setTextColor(...secondaryColor);
            pdf.text(notes, 25, 150);
        }

        // 5. Signature Section
        pdf.setFontSize(12);
        pdf.text('Signature:', 150, 180);
        pdf.setDrawColor(...secondaryColor);
        pdf.line(150, 182, 200, 182); // Signature line

        // 6. Footer Section: Hospital Information
        pdf.setDrawColor(200);
        pdf.rect(0, 270, 210, 20); // Footer background (light gray)

        pdf.setFontSize(10);
        pdf.setTextColor(60);
        pdf.text('HOSPITAL', 20, 280);
        pdf.text('Phone: 55 47 79 94 15', 20, 285);
        pdf.text('Email: hospital@email.com', 80, 280);
        pdf.text('Address: 123 Street, City', 80, 285);
        pdf.text('Website: www.hospital.com', 150, 285);

        // Open print dialog
        pdf.output('dataurlnewwindow');
    }
</script>