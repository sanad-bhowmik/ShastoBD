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
// Query to fetch medicine names
$medicineQuery = "SELECT name FROM medicine";
$medicineResult = $conn->query($medicineQuery);

// Query to fetch patient names
$patientQuery = "SELECT OID, Name, Address, Gender FROM tbl_patient WHERE Active = 1";
$patientResult = $conn->query($patientQuery);

// Query to fetch appointment numbers
$appointmentQuery = "SELECT appointment_number FROM appointmentview WHERE Status = 'active'";

$appointmentResult = $conn->query($appointmentQuery);
?>

<!-- Include jQuery, Select2, Toastr, and jsPDF -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<div class="app-main__inner">
    <form method="" id="prescriptionForm" action="">
        <div class="row">
            <div class="col-md-12">
                <div class="main-card mb-3 card">
                    <div class="card-header">Add Medicine Stock</div>
                    <div class="card-body">
                        <div class="position-relative form-group" style="margin: 1%;">

                            <div class="form-row responsive-row">
                                <!-- Appointment Number Select2 Searchable Dropdown -->
                                <div class="responsive-column">
                                    <label for="appointmentNumber">
                                        <span>Appointment Number:</span>
                                        <select name="appointmentNumber" id="appointmentNumber">
                                            <option value="" disabled selected>Select</option>
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
                                <div class="responsive-column">
                                    <label for="doctorName">Doctor:
                                        <input type="text" name="doctorName" id="doctorName" readonly>
                                    </label>
                                </div>

                                <!-- Patient Name (Auto-filled based on Appointment) -->
                                <div class="responsive-column">
                                    <label for="patientName">Patient:
                                        <input type="text" name="patientName" id="patientName" readonly>
                                    </label>
                                </div>

                                <!-- Medicine Name Select2 Dropdown -->
                                <div class="responsive-column">
                                    <label for="medicine">Medicine Name:
                                        <select name="medicine" id="medicine">
                                            <option value="" disabled selected>Select Medicine</option>
                                            <?php
                                            if ($medicineResult->num_rows > 0) {
                                                while ($row = $medicineResult->fetch_assoc()) {
                                                    echo "<option value='" . htmlspecialchars($row['name']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                                                }
                                            } else {
                                                echo "<option value=''>No Medicines Available</option>";
                                            }
                                            ?>
                                        </select>
                                    </label>
                                </div>
                            </div>

                            <div class="form-row responsive-row">
                                <!-- Medicine Group (Readonly) -->
                                <div class="responsive-column">
                                    <label for="medicineGroup">Medicine Group:
                                        <input type="text" name="medicineGroup" id="medicineGroup" readonly>
                                    </label>
                                </div>

                                <div class="responsive-column">
                                    <label for="duration">Duration:</label>
                                    <input type="text" name="duration" id="duration" required>
                                </div>

                                <div class="responsive-column">
                                    <label for="dosage">Dosage:</label>
                                    <input type="text" name="dosage" id="dosage" required>
                                </div>

                                <div class="responsive-column">
                                    <label for="notes">Additional Notes:</label>
                                    <textarea name="notes" id="notes" rows="2" required></textarea>
                                </div>

                                <div class="responsive-button">
                                    <button type="button" class="btn btn-secondary kik" id="saveButton">Add To
                                        List</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>


<div class="row" style="margin-left: 10px; margin-right: 10px;">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-header">
                Medicine Stock
            </div>
            <div class="form-container">
                <!-- Table to display submitted data -->
                <table id="dataTable">
                    <thead>
                        <tr>
                            <th>Appointment Number</th>
                            <th>Patient</th>
                            <th>Medicine</th>
                            <th>Group</th>
                            <th>Duration</th>
                            <th>Dosage</th>
                            <th>Notes</th>
                            <th>Doctor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data rows will be added here dynamically -->
                    </tbody>
                </table>

                <button onclick="generatePDF()" class="print-btn">
                    <span class="printer-wrapper">
                        <span class="printer-container">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 92 75">
                                <path stroke-width="5" stroke="black"
                                    d="M12 37.5H80C85.2467 37.5 89.5 41.7533 89.5 47V69C89.5 70.933 87.933 72.5 86 72.5H6C4.067 72.5 2.5 70.933 2.5 69V47C2.5 41.7533 6.75329 37.5 12 37.5Z">
                                </path>
                                <mask fill="white" id="path-2-inside-1_30_7">
                                    <path d="M12 12C12 5.37258 17.3726 0 24 0H57C70.2548 0 81 10.7452 81 24V29H12V12Z">
                                    </path>
                                </mask>
                                <path mask="url(#path-2-inside-1_30_7)" fill="black"
                                    d="M7 12C7 2.61116 14.6112 -5 24 -5H57C73.0163 -5 86 7.98374 86 24H76C76 13.5066 67.4934 5 57 5H24C20.134 5 17 8.13401 17 12H7ZM81 29H12H81ZM7 29V12C7 2.61116 14.6112 -5 24 -5V5C20.134 5 17 8.13401 17 12V29H7ZM57 -5C73.0163 -5 86 7.98374 86 24V29H76V24C76 13.5066 67.4934 5 57 5V-5Z">
                                </path>
                                <circle fill="black" r="3" cy="49" cx="78"></circle>
                            </svg>
                        </span>

                        <span class="printer-page-wrapper">
                            <span class="printer-page"></span>
                        </span>
                    </span>
                    Print
                </button>

            </div>
        </div>
    </div>
</div>

<!-- Your existing HTML code remains here -->


<script>
    $(document).ready(function () {
        // Initialize Select2 on Appointment Number dropdown
        $('#appointmentNumber').select2({
            placeholder: "Select Appointment",
            allowClear: true,
            width: 'resolve'
        });

        // Other existing jQuery code...
    });

    $(document).ready(function () {
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
        $('#prescriptionForm').on('submit', function (e) {
            e.preventDefault();

            var refNo = Math.floor(100000 + Math.random() * 900000);
            var formData = $(this).serialize() + '&refNo=' + refNo;

            // Send AJAX request
            $.ajax({
                url: 'prescription_process.php',
                type: 'POST',
                data: formData,
                success: function (response) {
                    toastr.success('Prescription added successfully!');

                    // Create PDF after successful insertion
                    createPDF(formData);

                    $('#prescriptionForm')[0].reset();
                },
                error: function (xhr, status, error) {
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
            success: function (response) {
                if (response === 'success') {
                    toastr.success('Appointment status updated successfully!');
                } else {
                    toastr.error('Failed to update appointment status.');
                }
            },
            error: function () {
                toastr.error('Error updating appointment status.');
            }
        });

        const {
            jsPDF
        } = window.jspdf;
        const pdf = new jsPDF();

        const doctorName = $('#doctorName').val();
        const patientName = $('#patientName').val();
        const margin = 20;

        // Header Section
        const headerHeight = 35;
        pdf.setFillColor(0, 102, 204);
        pdf.rect(0, 0, pdf.internal.pageSize.getWidth(), headerHeight, 'F');

        pdf.setTextColor(255, 255, 255);
        pdf.setFontSize(16);
        pdf.setFont("helvetica", "bold");
        pdf.text(doctorName, margin, 15);
        pdf.setFontSize(11);
        pdf.setFont("helvetica", "italic");
        pdf.text("General Physician, Internal Medicine Specialist", margin, 25);

        // Logo
        const logoImg = new Image();
        logoImg.src = 'https://i.ibb.co.com/TqfJdhm/logo-inverse.png'; // Replace with actual logo URL

        logoImg.onload = function () {
            pdf.addImage(logoImg, 'PNG', pdf.internal.pageSize.getWidth() - 45, 5, 40, 25);
            createPrescriptionContent(pdf, doctorName, patientName);
        };

        logoImg.onerror = function () {
            console.error('Failed to load logo image.');
            createPrescriptionContent(pdf, doctorName, patientName);
        }
    }

    function createPrescriptionContent(pdf, doctorName, patientName) {
        const margin = 20;
        const headerHeight = 35;

        // Positioning patient information
        const patientInfoY = headerHeight + 10;

        // Patient Information
        pdf.setTextColor(0, 0, 0);
        pdf.setFont("helvetica", "normal");
        pdf.setFontSize(10);

        // Date, Name, Age, and Weight positioning
        const pageWidth = pdf.internal.pageSize.getWidth();
        const patientInfoWidth = (pageWidth - margin * 2) / 3;

        pdf.text(`Name: ${patientName || "____________________"}`, margin, patientInfoY);
        pdf.text(`Age: __________`, margin + patientInfoWidth, patientInfoY);
        pdf.text(`Weight: __________`, margin + 2 * patientInfoWidth, patientInfoY);

        // Left Panel for Patient Information
        const leftPanelWidth = 70;
        const currentYStart = headerHeight + 25;
        pdf.setFillColor(245, 245, 245);
        pdf.rect(margin, currentYStart, leftPanelWidth, pdf.internal.pageSize.getHeight() - headerHeight - 40, 'F');

        pdf.setFontSize(10);

        // Rx Symbol
        pdf.setFontSize(24);
        pdf.setFont("helvetica", "bold");
        pdf.setTextColor(0, 102, 204);

        // Updated left margin to 10
        pdf.text("Rx", leftPanelWidth + 14 + 15, currentYStart + 25);

        // Table Section
        const startY = currentYStart + 45;
        const medicineX = leftPanelWidth + margin + 10;
        const durationX = medicineX + 40;
        const dosageX = durationX + 40;

        // Table Headers
        pdf.setFontSize(11);
        pdf.setFont("helvetica", "bold");
        pdf.setTextColor(0, 0, 0);
        pdf.text("Medicine", medicineX, startY);
        pdf.text("Duration", durationX, startY);
        pdf.text("Dosage", dosageX, startY);
        pdf.line(medicineX, startY + 2, pdf.internal.pageSize.getWidth() - margin, startY + 2);

        let currentY = startY + 10;
        const lineHeight = 10;
        const maxWidth = {
            medicine: 30,
            duration: 30,
            dosage: 30
        };

        pdf.setFont("helvetica", "normal");

        $('#dataTable tbody tr').each(function () {
            const medicineName = $(this).find('td').eq(3).text();
            const groupName = $(this).find('td').eq(2).text();
            const duration = $(this).find('td').eq(4).text();
            const dosage = $(this).find('td').eq(5).text();

            const medicineText = `${groupName} (${medicineName})`;
            const medicineLines = pdf.splitTextToSize(medicineText || '____', maxWidth.medicine);
            const durationLines = pdf.splitTextToSize(duration || '____', maxWidth.duration);
            const dosageLines = pdf.splitTextToSize(dosage || '____', maxWidth.dosage);

            const rowHeight = Math.max(medicineLines.length, durationLines.length, dosageLines.length) * lineHeight;

            pdf.text(medicineLines, medicineX, currentY);
            pdf.text(durationLines, durationX, currentY);
            pdf.text(dosageLines, dosageX, currentY);

            currentY += rowHeight;
        });

        // Notes Section
        if ($('#dataTable tbody tr').length > 0) {
            const lastRow = $('#dataTable tbody tr').last();
            const notes = lastRow.find('td').eq(6).text();

            if (notes) {
                pdf.setFontSize(12);
                pdf.setFont("helvetica", "bold");
                pdf.setTextColor(0, 102, 204);
                pdf.text('Notes:', medicineX, currentY + 10);
                pdf.setFont("helvetica", "normal");
                pdf.setFontSize(10);
                pdf.text(notes, medicineX + 5, currentY + 20, {
                    maxWidth: pdf.internal.pageSize.getWidth() - margin * 2 - 10
                });
                currentY += 30;
            }
        }

        // Signature Section
        currentY = pdf.internal.pageSize.getHeight() - 45;
        pdf.setFontSize(10);
        pdf.setFont("helvetica", "italic");
        pdf.text("Doctor's Signature", pdf.internal.pageSize.getWidth() - 60, currentY + 10);

        // Footer Section
        pdf.setFillColor(0, 102, 204);
        pdf.rect(0, pdf.internal.pageSize.getHeight() - 20, pdf.internal.pageSize.getWidth(), 20, 'F');

        pdf.setFont("helvetica", "normal");
        pdf.setTextColor(255, 255, 255);
        pdf.setFontSize(10);
        pdf.text("Emon Dental, 123 Demo Street, Dhaka, Bangladesh | Phone: +880 1234-567890", margin, pdf.internal.pageSize.getHeight() - 10);
        pdf.setFont("helvetica", "italic");
        pdf.text("Thank you for choosing Emon Dental. We wish you a speedy recovery!", margin, pdf.internal.pageSize.getHeight() - 5);

        // Save to server and trigger download
        savePDFToServer(pdf);
    }

    function savePDFToServer(pdf) {
        const pdfBlob = pdf.output('blob');
        const formData = new FormData();
        const appointmentNumber = $('#appointmentNumber').val();  // Get appointment number from input field
        const fileName = `appointment_${appointmentNumber}.pdf`;

        formData.append('pdf', pdfBlob, fileName);
        formData.append('appointment_number', appointmentNumber);  // Append the appointment number to the form data

        // Save to server
        $.ajax({
            url: 'save_pdf.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.startsWith('success')) {
                    const filePath = response.split('|')[1]; // Extract file path from response
                    toastr.success('PDF saved successfully on the server.');

                    // Open the saved PDF in a new browser tab
                    window.open(filePath, '_blank');
                } else {
                    toastr.error('Error saving PDF to the server.');
                }
            },
            error: function () {
                toastr.error('Error during PDF upload.');
            }
        });
    }


</script>

<script>
    $('#medicine').on('change', function () {
        var selectedMedicine = $(this).val();

        if (selectedMedicine) {
            $.ajax({
                type: "POST",
                url: "getMedicineGroup.php",
                data: {
                    medicineName: selectedMedicine
                },
                success: function (response) {
                    console.log("Received group name: ", response);
                    $('#medicineGroup').val(response);
                },
                error: function () {
                    $('#medicineGroup').val("Error retrieving group");
                }
            });
        } else {
            $('#medicineGroup').val("");
        }
    });

    $(document).ready(function () {
        // Event listener for Appointment Number selection
        $('#appointmentNumber').on('change', function () {
            var appointmentNumber = $(this).val();

            if (appointmentNumber) {
                // Fetch Doctor and Patient data based on Appointment Number
                $.ajax({
                    url: 'fetch_appointment_data.php', // PHP file to fetch details
                    type: 'POST',
                    data: {
                        appointment_number: appointmentNumber
                    },
                    success: function (data) {
                        var result = JSON.parse(data);
                        if (result.success) {
                            $('#doctorName').val(result.doctor_name);
                            $('#patientName').val(result.patient_name);
                        } else {
                            toastr.error(result.error);
                        }
                    },
                    error: function () {
                        toastr.error('Error fetching appointment details.');
                    }
                });
            } else {
                $('#doctorName').val('');
                $('#patientName').val('');
            }
        });
        $(document).ready(function () {
            $('#medicine').select2({
                placeholder: "Select Medicine",
                allowClear: true
            });
        });

        // Event listener for the Save button
        $('#saveButton').on('click', function () {
            // Get form field values
            var appointmentNumber = $('#appointmentNumber').val();
            var doctorName = $('#doctorName').val();
            var patientName = $('#patientName').val();
            var medicine = $('#medicine').val();
            var group = $('#medicineGroup').val();
            var duration = $('#duration').val();
            var dosage = $('#dosage').val();
            var notes = $('#notes').val();

            // Append the data to the table
            var newRow = `
            <tr>
                <td>${appointmentNumber}</td>
                <td>${patientName}</td>
                <td>${medicine}</td>
                <td>${group}</td>
                <td>${duration}</td>
                <td>${dosage}</td>
                <td>${notes}</td>
                <td>${doctorName}</td>
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

<style>
    .responsive-row {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .responsive-column {
        flex: 1;
        padding: 0.5rem;
    }

    .responsive-column label {
        display: block;
        margin-bottom: 0.5rem;
    }

    #kik {
        margin-left: 1%;
        width: 10%;
        margin-top: -4%;
    }

    .responsive-column input,
    .responsive-column select,
    .responsive-column textarea {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #b2adad;
        border-radius: 0.25rem;
        height: 34px;
        text-align: center;
    }

    .responsive-button {
        width: 100%;
        margin-top: 1rem;
    }

    .responsive-button button {
        padding: 0.5rem;
        color: white;
        border: none;
        border-radius: 0.25rem;
        cursor: pointer;
        background-color: #6c757d;
        margin-left: 1%;
        margin-top: -4%;
    }

    /* Mobile Styles */
    @media (max-width: 768px) {
        .responsive-row {
            flex-direction: column;
        }

        .responsive-button {
            width: 100%;
        }
    }

    .form-container {
        padding: 12px;
    }

    table {
        width: 100%;
        margin-top: 20px;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 8px;
        text-align: left;
        border: 1px solid #ddd;
    }

    th {
        background-color: #f2f2f2;
    }

    .print-btn {
        display: inline-block;
        background-color: #007bff;
        color: white;
        padding: 10px 15px;
        border-radius: 5px;
        margin-top: 20px;
        cursor: pointer;
        text-decoration: none;
    }

    .print-btn .printer-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .print-btn svg {
        width: 20px;
        height: 20px;
    }

    /* Mobile Styles */
    @media (max-width: 768px) {
        table {
            width: 100%;
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }

        .print-btn {
            width: 100%;
            text-align: center;
        }

        .print-btn .printer-wrapper {
            justify-content: center;
        }

        .main-card {
            padding: 0;
        }

        .card-header {
            text-align: center;
            font-size: 1.2rem;
            padding: 10px;
        }

        .form-container {
            padding: 10px;
        }

        th,
        td {
            padding: 5px;
            font-size: 12px;
        }
    }

    .print-btn {
        width: 100px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: white;
        border: 1px solid rgb(213, 213, 213);
        border-radius: 10px;
        gap: 10px;
        font-size: 16px;
        cursor: pointer;
        overflow: hidden;
        font-weight: 500;
        box-shadow: 0px 10px 10px rgba(0, 0, 0, 0.065);
        transition: all 0.3s;
        margin-top: 13px;
        color: black;
    }

    .printer-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 100%;
    }

    .printer-container {
        height: 50%;
        width: 100%;
        display: flex;
        align-items: flex-end;
        justify-content: center;
    }

    .printer-container svg {
        width: 100%;
        height: auto;
        transform: translateY(4px);
    }

    .printer-page-wrapper {
        width: 100%;
        height: 50%;
        display: flex;
        align-items: flex-start;
        justify-content: center;
    }

    .printer-page {
        width: 70%;
        height: 10px;
        border: 1px solid black;
        background-color: white;
        transform: translateY(0px);
        transition: all 0.3s;
        transform-origin: top;
    }

    .print-btn:hover .printer-page {
        height: 16px;
        background-color: rgb(239, 239, 239);
    }

    .print-btn:hover {
        background-color: rgb(239, 239, 239);
    }

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