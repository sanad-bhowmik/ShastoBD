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
$doctorQuery = "SELECT DOCID, DocName, MobileNum FROM tbl_doctor WHERE Active = 1";
$doctorResult = $conn->query($doctorQuery);

// Query to fetch patient names
$patientQuery = "SELECT OID, Name, Address, Gender FROM tbl_patient WHERE Active = 1";
$patientResult = $conn->query($patientQuery);

// Query to fetch appointment numbers
$appointmentQuery = "SELECT appointment_number FROM appointmentview";
$appointmentResult = $conn->query($appointmentQuery);
?>

<!-- Include jQuery, Select2, Toastr, and jsPDF -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<div class="form-container" style="width: 96%; margin: 26px auto; background-color: #fff; padding: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); border-radius: 8px;">
    <form id="prescriptionForm" style="font-size: .9375rem;">
        <div class="form-row" style="margin-bottom: .9375rem; display: flex; flex-wrap: wrap;">
            <div style="flex: 1;padding-left: .625rem;margin-top: 5px;">
                <label for="appointmentNumber" style="display: block;margin-bottom: .3125rem;margin-right: 10px;margin-top: -2px;">Appointment Number:
                    <select name="appointmentNumber" id="appointmentNumber" style="width: 100%;padding: .5rem;border: .0625rem solid #ccc;border-radius: .25rem;height: 33px;font-size: 11px;padding: 1px;text-align: center;">
                        <option value="" disabled selected>Select </option>
                        <?php
                        if ($appointmentResult->num_rows > 0) {
                            while ($row = $appointmentResult->fetch_assoc()) {
                                echo "<option value='" . $row['appointment_number'] . "'>" . $row['appointment_number'] . "</option>";
                            }
                        } else {
                            echo "<option value=''>No Appointments Available</option>";
                        }
                        ?>
                    </select>
                </label>
            </div>

            <!-- Doctor Name (Auto-filled based on Appointment) -->
            <div style="flex: 1; padding-right: .625rem;">
                <label for="doctorName" style="display: block; margin-bottom: .3125rem;">Doctor:
                    <input type="text" name="doctorName" id="doctorName" readonly style="width: 100%;border: .0625rem solid #ccc;border-radius: .25rem;height: 33px;padding: 1px;text-align: center;">
                </label>
            </div>

            <!-- Patient Name (Auto-filled based on Appointment) -->
            <div style="flex: 1; padding-right: .625rem; padding-left: .625rem;">
                <label for="patientName" style="display: block; margin-bottom: .3125rem;">Patient:
                    <input type="text" name="patientName" id="patientName" readonly style="width: 100%;border: .0625rem solid #ccc;border-radius: .25rem;height: 33px;padding: 1px;text-align: center;">
                </label>
            </div>


            <!-- Medicine Name Input -->
            <div style="flex: 1; padding-left: .625rem;">
                <label for="medicine" style="display: block; margin-bottom: .3125rem;">Medicine Name:
                    <input type="text" name="medicine" id="medicine" required style="width: 100%;border: .0625rem solid #ccc;border-radius: .25rem;height: 33px;padding: 1px;text-align: center;">
                </label>
            </div>
        </div>

        <div class="form-row" style="display: flex; flex-wrap: wrap; gap: 1rem;">
            <div style="flex: 1;">
                <label for="duration">Duration:</label>
                <input type="text" name="duration" id="duration" required style="width: 100%; padding: .5rem; border: .0625rem solid #b2adad; border-radius: .25rem; height: 33px;">
            </div>

            <div style="flex: 1;">
                <label for="dosage">Dosage:</label>
                <input type="text" name="dosage" id="dosage" required style="width: 100%; padding: .5rem; border: .0625rem solid #b2adad; border-radius: .25rem; height: 33px;">
            </div>

            <div style="flex: 1;">
                <label for="notes">Additional Notes:</label>
                <textarea name="notes" id="notes" rows="2" style="width: 100%; padding: .5rem; border: .0625rem solid #b2adad; border-radius: .25rem;height: 33px;" required></textarea>
            </div>

            <div style="flex: 1; display: flex; align-items: flex-end;">
                <button type="button" id="saveButton" style="width: 100%; padding: 7px; color: white; border: none; border-radius: .25rem; cursor: pointer; background-image: radial-gradient(circle farthest-corner at 10% 20%, rgba(14,174,87,1) 0%, rgba(12,116,117,1) 90%);">Save</button>
            </div>
        </div>
    </form>


</div>

<div class="form-container" style="width: 96%; margin: 26px auto; background-color: #fff; padding: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); border-radius: 8px;">
    <!-- Table to display submitted data -->
    <table id="dataTable" style="width: 100%; margin-top: 20px; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th>Appointment Number</th>
                <th>Doctor</th>
                <th>Patient</th>
                <th>Medicine</th>
                <th>Duration</th>
                <th>Dosage</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data rows will be added here dynamically -->
        </tbody>
    </table>
    <!-- Print Button -->
    <button onclick="generatePDF()" style="margin-top: 20px; padding: 10px 15px; border: none; border-radius: 5px; background-color: #4CAF50; color: white; cursor: pointer;">
        Print
    </button>
</div>

<script>
    $(document).ready(function() {
        // Event listener for Appointment Number selection
        $('#appointmentNumber').on('change', function() {
            var appointmentNumber = $(this).val();

            if (appointmentNumber) {
                // Fetch Doctor and Patient data based on Appointment Number
                $.ajax({
                    url: 'fetch_appointment_data.php', // PHP file to fetch details
                    type: 'POST',
                    data: {
                        appointment_number: appointmentNumber
                    },
                    success: function(data) {
                        var result = JSON.parse(data);
                        if (result.success) {
                            $('#doctorName').val(result.doctor_name);
                            $('#patientName').val(result.patient_name);
                        } else {
                            toastr.error('Doctor or Patient information not found for the selected appointment.');
                        }
                    },
                    error: function() {
                        toastr.error('Error fetching appointment details.');
                    }
                });
            } else {
                $('#doctorName').val('');
                $('#patientName').val('');
            }
        });

        // Event listener for the Save button
        $('#saveButton').on('click', function() {
            // Get form field values
            var appointmentNumber = $('#appointmentNumber').val();
            var doctorName = $('#doctorName').val();
            var patientName = $('#patientName').val();
            var medicine = $('#medicine').val();
            var duration = $('#duration').val();
            var dosage = $('#dosage').val();
            var notes = $('#notes').val();

            // Append the data to the table
            var newRow = `
                <tr>
                    <td>${appointmentNumber}</td>
                    <td>${doctorName}</td>
                    <td>${patientName}</td>
                    <td>${medicine}</td>
                    <td>${duration}</td>
                    <td>${dosage}</td>
                    <td>${notes}</td>
                </tr>
            `;
            $('#dataTable tbody').append(newRow);

            // Clear the medicine, duration, dosage, and notes fields only
            $('#medicine').val('');
            $('#duration').val('');
            $('#dosage').val('');
            $('#notes').val('');
        });
    });
</script>

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

    function generatePDF() {
        const appointmentNumber = $('#appointmentNumber').val();

        // Step 1: Update the status of the appointment
        $.ajax({
            url: 'update_appointment_status.php', // PHP file to handle the status update
            type: 'POST',
            data: {
                appointment_number: appointmentNumber
            },
            success: function(response) {
                if (response === 'success') {
                    toastr.success('Appointment status updated successfully!');
                } else {
                    toastr.error('Failed to update appointment status.');
                }
            },
            error: function() {
                toastr.error('Error updating appointment status.');
            }
        });




        const formData = $('#prescriptionForm').serialize();
        const {
            jsPDF
        } = window.jspdf;
        const pdf = new jsPDF();

        const params = new URLSearchParams(formData);
        const doctorName = $('#doctorName').val();
        const doctorMobile = '0123456789';
        const patientName = $('#patientName').val();
        const patientGender = $('#patientid option:selected').data('gender');
        const textColor = [0, 0, 0];
        const borderColor = [0, 0, 0];
        const margin = 20;

        // 1. Header Section: Clinic Name with Gray Background
        const headerHeight = 30;
        pdf.setFillColor(200, 200, 200);
        pdf.rect(0, 0, pdf.internal.pageSize.getWidth(), headerHeight, 'F');

        // Clinic Name - Centered
        pdf.setFontSize(24);
        pdf.setFont("helvetica", "bold");
        pdf.setTextColor(...textColor);
        pdf.text('Emon Dental', pdf.internal.pageSize.getWidth() / 2, 20, {
            align: 'center'
        });

        // 2. Doctor Information (Left Aligned)
        pdf.setFontSize(18);
        pdf.setFont("helvetica", "bold");
        pdf.setTextColor(...textColor);
        pdf.text('Dr. ' + doctorName, margin, 40);

        // Doctor Contact Info
        pdf.setFontSize(11);
        pdf.setFont("helvetica", "normal");
        pdf.text('Mobile: ' + doctorMobile, margin, 48);

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
        // pdf.text('Gender: ' + patientGender, margin, 84);
        pdf.text('Date: ' + new Date().toLocaleDateString(), margin, 90);

        // Appointment Number (Right Aligned)
        pdf.setFontSize(12);
        pdf.setFont("helvetica", "bold");
        pdf.text('Appointment No: ' + $('#appointmentNumber').val(), 190, 70, {
            align: 'right'
        }); // Adjust for your appointment number field

        // Horizontal Line Separator for Neatness
        pdf.line(margin, 95, 200, 95);

        // 5. Prescription Details Section (Table)
        pdf.setFontSize(12);
        pdf.setFont("helvetica", "bold");
        pdf.text('Prescription Details', margin, 105);

        // Table Header
        const startY = 115;
        pdf.setFontSize(11);
        pdf.setFont("helvetica", "bold");
        pdf.text('Medicine', margin, startY);
        pdf.text('Duration', 100, startY);
        pdf.text('Dosage', 140, startY);
        pdf.line(margin, startY + 2, 200, startY + 2); // Header line

        let currentY = startY + 10; // Starting Y position for the data rows
        pdf.setFont("helvetica", "normal");

        // Loop through each row in the prescription table
        $('#dataTable tbody tr').each(function() {
            const medicine = $(this).find('td').eq(3).text();
            const duration = $(this).find('td').eq(4).text();
            const dosage = $(this).find('td').eq(5).text();
            const notes = $(this).find('td').eq(6).text();

            // Add each prescription entry to the PDF
            pdf.text(medicine || '_________________', margin, currentY);
            pdf.text(duration || '_________________', 100, currentY);
            pdf.text(dosage || '_________________', 140, currentY);

            currentY += 10; // Adjust for the height of each row
        });

        // 6. Notes Section (if applicable)
        if ($('#dataTable tbody tr').length > 0) {
            const lastRow = $('#dataTable tbody tr').last();
            const notes = lastRow.find('td').eq(6).text();

            if (notes) {
                pdf.setFontSize(12);
                pdf.setFont("helvetica", "bold");
                pdf.text('Notes:', margin, currentY);
                pdf.setDrawColor(...borderColor);
                pdf.rect(margin, currentY + 5, 170, 30);
                pdf.setFont("helvetica", "normal");
                pdf.setFontSize(11);
                pdf.text(notes, margin + 5, currentY + 15);
                currentY += 40; // Adjust for notes box height
            }
        }

        // 7. Signature Section
        pdf.setFontSize(12);
        pdf.setFont("helvetica", "bold");
        pdf.line(150, currentY + 2, 200, currentY + 2);
        pdf.setFont("helvetica", "italic");
        pdf.setFontSize(10);
        pdf.text('Doctor\'s signature', 150, currentY + 10);

        // 8. Footer Section (With Thank-You Note)
        pdf.setFontSize(10);
        pdf.setDrawColor(...borderColor);
        pdf.setFillColor(230, 230, 230);
        pdf.rect(0, currentY + 30, 210, 20, 'F');

        // Footer Text and Thank You Note
        pdf.setFont("helvetica", "normal");
        pdf.setTextColor(...textColor);
        pdf.text('Emon Dental, 123 Demo Street, Dhaka, Bangladesh | Phone: +880 1234-567890', margin, currentY + 38);
        pdf.setFont("helvetica", "italic");
        pdf.text('Thank you for choosing Emon Dental. We wish you a speedy recovery!', margin, currentY + 45);

        // PDF Name with Patient's Name
        const fileName = patientName ? `${patientName}.pdf` : "Prescription.pdf";

        pdf.autoPrint();
        window.open(pdf.output('bloburl'), '_blank');

    }
</script>
<!-- <input type="submit" value="Save" style="width: 100%; padding: 7px; color: white; border: none; border-radius: .25rem; cursor: pointer; background-image: radial-gradient(circle farthest-corner at 10% 20%, rgba(14,174,87,1) 0%, rgba(12,116,117,1) 90%);"> -->

<style>
    #dataTable {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
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
</style>