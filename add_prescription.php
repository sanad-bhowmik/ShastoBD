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

<div class="form-container" style="margin-left: 9%; margin-top: 2rem; max-width: 54.125rem; padding: 1.25rem; border: .0625rem solid #ccc; border-radius: .5rem; background-color: #f9f9f9; box-shadow: rgba(0, 0, 0, 0.35) 0rem .3125rem .9375rem;">
    <h2 style="text-align: center; margin-bottom: 1.25rem;">Add Prescription</h2>
    <form id="prescriptionForm" method="POST" style="font-size: .9375rem;">

        <!-- First Row: 3 Inputs -->
        <div class="form-row" style="margin-bottom: .9375rem; display: flex; flex-wrap: wrap;">
            <div style="flex: 1; padding-right: .625rem;">
                <label for="doctorid" style="display: block; margin-bottom: .3125rem;">Doctor:
                    <select name="doctorid" id="doctorid" required style="width: 100%; padding: .5rem; border: .0625rem solid #ccc; border-radius: .25rem;">
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

            <div style="flex: 1; padding-right: .625rem; padding-left: .625rem;">
                <label for="patientid" style="display: block; margin-bottom: .3125rem;">Patient:
                    <select name="patientid" id="patientid" required style="width: 100%; padding: .5rem; border: .0625rem solid #ccc; border-radius: .25rem;">
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

            <div style="flex: 1; padding-left: .625rem;">
                <label for="medicine" style="display: block; margin-bottom: .3125rem;">Medicine Name:
                    <input type="text" name="medicine" id="medicine" required style="width: 100%; padding: .5rem; border: .0625rem solid #ccc; border-radius: .25rem;">
                </label>
            </div>
        </div>

        <!-- Second Row: 2 Inputs -->
        <div class="form-row" style="margin-bottom: .9375rem; display: flex; flex-wrap: wrap;">
            <div style="flex: 1;padding-right: 3.625rem;">
                <label for="duration" style="display: block; margin-bottom: .3125rem;">Duration:
                    <input type="text" name="duration" id="duration" required style="width: 126%;padding: .5rem;border: .0625rem solid #ccc;border-radius: .25rem;">
                </label>
            </div>

            <div style="flex: 1; padding-left: .625rem;">
                <label for="dosage" style="display: block; margin-bottom: .3125rem;">Dosage:
                    <input type="text" name="dosage" id="dosage" required style="width: 100%; padding: .5rem; border: .0625rem solid #ccc; border-radius: .25rem;">
                </label>
            </div>
            <div style="flex: 1; padding-left: .625rem;">
                <label for="notes" style="display: block; margin-bottom: .3125rem;">Additional Notes:
                    <textarea name="notes" id="notes" rows="4" style="width: 146%; padding: .5rem; border: .0625rem solid #ccc; border-radius: .25rem; height: 2.5rem;"></textarea>
                </label>
            </div>
            <div style="flex: 1; padding-left: .625rem;">
                <input type="submit" value="Print" style="padding: .625rem;color: white;border: none;border-radius: .25rem;cursor: pointer;margin-left: 44%;background-image: radial-gradient(circle farthest-corner at 10% 20%, rgba(14,174,87,1) 0%, rgba(12,116,117,1) 90%);z-index: 10;position: relative;margin-top: 19px;margin-left: 52%;">

            </div>
        </div>

        <!-- Additional Notes -->
        <div class="form-row" style="margin-bottom: .9375rem; display: flex; flex-wrap: wrap;">

        </div>


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
        const doctorMobile = $('#doctorid option:selected').data('mobile');
        const patientName = $('#patientid option:selected').text();
        const patientGender = $('#patientid option:selected').data('gender');
        const medicine = params.get('medicine');
        const duration = params.get('duration');
        const dosage = params.get('dosage');
        const notes = params.get('notes');
        const refNo = params.get('refNo');

        const textColor = [0, 0, 0];
        const borderColor = [0, 0, 0];
        const margin = 20;

        // 1. Header Section: Clinic Name with Gray Background
        const headerHeight = 30; // Height of the header section
        pdf.setFillColor(200, 200, 200); // Light gray color for the header background
        pdf.rect(0, 0, pdf.internal.pageSize.getWidth(), headerHeight, 'F'); // Fill header background

        // Clinic Name - Centered
        pdf.setFontSize(24); // Larger font size for the header
        pdf.setFont("helvetica", "bold");
        pdf.setTextColor(...textColor);
        pdf.text('Emon Dental', pdf.internal.pageSize.getWidth() / 2, 20, {
            align: 'center'
        }); // Centered

        // 2. Doctor Information (Left Aligned)
        pdf.setFontSize(18);
        pdf.setFont("helvetica", "bold");
        pdf.setTextColor(...textColor);
        pdf.text('Dr. ' + doctorName, margin, 40);

        // Doctor Contact and Certification Info
        pdf.setFontSize(11);
        pdf.setFont("helvetica", "normal");
        pdf.text('Mobile: ' + doctorMobile, margin, 48);
        pdf.text('Certification No: ' + refNo, margin, 54);

        // 3. Clinic Info: Right-Aligned
        pdf.setFontSize(12);
        pdf.setFont("helvetica", "bold");
        pdf.text('123 Demo Street, Dhaka, Bangladesh', 200, 40, {
            align: 'right'
        });
        pdf.setFontSize(10);
        pdf.setFont("helvetica", "normal");
        pdf.text('+880 1234-567890', 200, 46, {
            align: 'right'
        });

        // Horizontal Line Separator
        pdf.setDrawColor(...borderColor);
        pdf.line(margin, 60, 200, 60);

        // 4. Patient Information Section (Left-Aligned)
        pdf.setFontSize(12);
        pdf.setFont("helvetica", "bold");
        pdf.text('Patient Information', margin, 70);
        pdf.setFont("helvetica", "normal");
        pdf.text('Name: ' + patientName, margin, 78);
        pdf.text('Gender: ' + patientGender, margin, 84);
        pdf.text('Date: ' + new Date().toLocaleDateString(), 150, 84);

        // Horizontal Line Separator for Neatness
        pdf.line(margin, 90, 200, 90);

        // 5. Medicine Details (Professionally added)
        pdf.setFontSize(12);
        pdf.setFont("helvetica", "bold");
        pdf.text('Prescription Details', margin, 100);

        // Medicine Name, Duration, Dosage (in table form)
        pdf.setFontSize(11);
        pdf.setFont("helvetica", "normal");
        pdf.text('Medicine: ' + (medicine || '_________________'), margin, 110);
        pdf.text('Duration: ' + (duration || '_________________'), margin, 120);
        pdf.text('Dosage: ' + (dosage || '_________________'), margin, 130);

        // 6. Notes Section (Professionally boxed if applicable)
        if (notes) {
            pdf.setFontSize(12);
            pdf.setFont("helvetica", "bold");
            pdf.text('Notes:', margin, 145);
            pdf.setDrawColor(...borderColor);
            pdf.rect(margin, 150, 170, 30);
            pdf.setFont("helvetica", "normal");
            pdf.setFontSize(11);
            pdf.text(notes, margin + 5, 160);
        }

        // 7. Signature Section
        pdf.setFontSize(12);
        pdf.setFont("helvetica", "bold");
        pdf.text('', 150, 190);
        pdf.line(150, 192, 200, 192);
        pdf.setFont("helvetica", "italic");
        pdf.setFontSize(10);
        pdf.text('Doctor\'s signature', 150, 200);

        // 8. Footer Section (With Thank-You Note)
        pdf.setFontSize(10);
        pdf.setDrawColor(...borderColor);
        pdf.setFillColor(230, 230, 230);
        pdf.rect(0, 270, 210, 20, 'F');

        // Footer Text and Thank You Note
        pdf.setFont("helvetica", "normal");
        pdf.setTextColor(...textColor);
        pdf.text('Emon Dental, 123 Demo Street, Dhaka, Bangladesh | Phone: +880 1234-567890', margin, 278);
        pdf.setFont("helvetica", "italic");
        pdf.text('Thank you for choosing Emon Dental. We wish you a speedy recovery!', margin, 285);

        // Next Appointment Section
        pdf.setFontSize(11);
        pdf.setFont("helvetica", "normal");
        pdf.text('Next Appointment Date: ___________________________', margin, 260);

        // PDF Name with Patient's Name
        const fileName = patientName ? `${patientName}.pdf` : "Prescription.pdf";

        // Trigger print dialog (not download)
        pdf.autoPrint();
        window.open(pdf.output('bloburl'), '_blank');
    }
</script>