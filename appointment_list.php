<?php

include_once("include/header.php");
if (isset($_POST['submit'])) {
  $param = array();

  if (isset($_POST['doc_id']) && $_POST['doc_id'] != "" && !empty($_POST['doc_id'])) {
    $docID = $_POST['doc_id'];
    $condition = "DOCID =" . $docID;
    array_push($param, $condition);
  }

  if (isset($_POST['status'])) {
    if ($_POST['status'] != "all") {
      $condition = "Status = '" . $_POST['status'] . "'";
      array_push($param, $condition);
    }
  }

  if (isset($_POST['fdate']) && isset($_POST['todate'])) {
    $condition = "AppointmentDate BETWEEN '" . $_POST['fdate'] . "' AND '" . $_POST['todate'] . "'";
    array_push($param, $condition);
  }

  array_push($param, "Status = 'active'");

  $condition = implode(" AND ", $param);

  $sql_get_data = "SELECT * FROM appointmentview WHERE " . $condition . " ORDER BY AppointmentDate DESC";
  $result = mysqli_query($GLOBALS['con'], $sql_get_data);
} else {
  $sql_get_data = "SELECT * FROM appointmentview WHERE Status = 'active' ORDER BY AppointmentDate DESC";

  $result = mysqli_query($GLOBALS['con'], $sql_get_data);
}

?>


<style>
  .prescription-inputs {
    display: flex;
    flex-direction: column;
  }

  .prescription-inputs input {
    margin-bottom: .625rem;
    /* Space between inputs */
  }

  .table td {
    vertical-align: middle;
    /* Aligns content vertically center */
  }

  .table .btn-primary {
    margin-top: .625rem;
    /* Space above button */
  }

  /* Modal styles */
  .modal-content {
    padding: 1.25rem;
  }

  .modal-header {
    background-color: #007bff;
    color: white;
  }

  .modal-footer {
    background-color: #f7f7f7;
  }

  .form-control {
    border-radius: .3125rem;
  }

  .form-label {
    font-weight: bold;
  }
</style>
<div class="app-main__inner">


  <form id="search" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <div class="row">
      <div class="col-md-12">
        <div class="main-card mb-3 card">
          <div class="card-header">Search Appointment</div>
          <div class="card-body">
            <div class="position-relative row form-group">

              <div class="col-sm-3">
                <input type="text" hidden="true" name="doc_id" class="form-control" id="doc_id">
                <p class="text">Type Doctor Name :</p> <input autocomplete="off" type="text" name="doc_name" class="form-control" id="doc_name" placeholder="Type Doctor Name">
                <div class="list-group" id="show-list">
                </div>
              </div>
              <div class="col-sm-3">

                <p class="text">From Date :</p>
                <input class="form-control" required value="<?php if (isset($_POST['fdate'])) echo $_POST['fdate']; ?>" type="date" name="fdate" id="fdate">
              </div>
              <div class="col-sm-3">

                <p class="text">To Date :</p>
                <input class="form-control" required type="date" value="<?php if (isset($_POST['todate'])) echo $_POST['todate']; ?>" name="todate" id="todate">
              </div>

              <div class="col-sm-3">

                <p class="text">Status</p>

                <select name="status" id="status" class="form-control">
                  <option value="all">All
                  </option>
                  <option value="Completed">Completed
                  </option>
                  <option value="pending">Pending
                  </option>
                  <option value="overdue">Over Due
                  </option>
                </select>

              </div>

            </div>



            <div class="position-relative row form-group p-t-10 ">
              <div class="col-sm-4 ">

                <input type="submit" id="search" value="Search" name="submit" class="btn btn-secondary">


              </div>
            </div>




          </div>
        </div>
      </div>
    </div>

  </form>

  <div class="row">
    <div class="col-md-12">
      <div class="main-card mb-3 card">
        <div class="card-body">
          <div class="card-title">Doctors</div>
          <!-- Button trigger modal -->


          <div class="table-responsive">
            <table id="DocTable" class="align-middle mb-0 table table-borderless table-striped table-hover">
              <thead>
                <tr>
                  <th class="text-center">Sl</th>
                  <th class="text-center">Doctor</th>
                  <th class="text-center">Doctor Number</th>
                  <th class="text-center">Appointment Time</th>
                  <th class="text-center">Appointment Date</th>
                  <th class="text-center">Patient</th>
                  <!-- <th class="text-center">Status</th> -->
                  <th class="text-center">Option</th>
                </tr>
              </thead>
              <tbody>
                <?php $i = 1;
                while ($rs = mysqli_fetch_array($result)) { ?>
                  <tr>

                    <td class="text-muted text-center">
                      <?php echo $i; ?>
                    </td>

                    <td class="text-center">
                      <?php echo $rs['DocName']; ?>
                    </td>

                    <td class="text-center">
                      <?php echo $rs['MobileNum']; ?>
                    </td>

                    <td class="text-center">
                      <?php echo $rs['Appointment_Time']; ?>
                    </td>

                    <td class="text-center">
                      <?php echo $rs['AppointmentDate']; ?>
                    </td>

                    <td class="text-center">
                      <?php
                      echo $rs['PatientName'] . "<br>" . $rs['PatientMobile'];
                      ?>
                    </td>
                    <!-- <td class="text-center">
                      <?php echo $rs['Status']; ?>
                    </td> -->
                    <td class="text-center">
                      <a href="add_prescription.php" class="btn-sm mr-2 mb-2 btn-primary">Prescription</a>
                    </td>




                  </tr>
                <?php $i++;
                } ?>



              </tbody>
            </table>
          </div>


        </div>
      </div>
    </div>
  </div>
</div>
<?php
include_once("include/footer.php");
?>


<!-- Large modal -->

<!-- Large modal -->
<div id="pModal" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Add Prescription</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="prescriptionForm" method="post" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" id="doctorId" name="doctorId">
          <input type="hidden" id="patientId" name="patientId">

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="doctorName" class="form-label">Doctor Name</label>
              <input type="text" class="form-control" id="doctorName" name="doctorName" readonly>
            </div>
            <div class="col-md-6">
              <label for="patientName" class="form-label">Patient Name</label>
              <input type="text" class="form-control" id="patientName" name="patientName" readonly>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="appointmentTime" class="form-label">Appointment Time</label>
              <input type="text" class="form-control" id="appointmentTime" name="appointmentTime" readonly>
            </div>
            <div class="col-md-6">
              <label for="appointmentDate" class="form-label">Appointment Date</label>
              <input type="text" class="form-control" id="appointmentDate" name="appointmentDate" readonly>
            </div>
          </div>

          <div class="row mb-3 ">
            <div class="col-md-6 d-none">
              <label for="refNo" class="form-label">Reference Number</label>
              <input type="text" class="form-control" id="refNo" name="refNo">
            </div>
            <div class="col-md-6">
              <label for="medicine" class="form-label">Medicine</label>
              <input type="text" class="form-control" id="medicine" name="medicine" required>
            </div>
            <div class="col-md-6">
              <label for="dosage" class="form-label">Dosage</label>
              <input type="text" class="form-control" id="dosage" name="dosage" required>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="instructions" class="form-label">Instructions</label>
              <textarea class="form-control" id="instructions" name="instructions" required></textarea>
            </div>
            <div class="col-md-6">
              <label for="duration" class="form-label">Duration</label>
              <input type="text" class="form-control" id="duration" name="duration" required>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button id="saveChanges" type="submit" class="btn btn-primary">Save Prescription</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Include Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const prescriptionForm = document.getElementById('prescriptionForm');
    let isSubmitting = false; // Flag to prevent multiple submissions

    // Listener for Prescription buttons
    document.querySelectorAll('.patientDetails').forEach(button => {
      button.addEventListener('click', function(event) {
        // Prevent default action
        event.preventDefault();

        // Populate the modal with data
        const doctorId = this.getAttribute('data-doctorid');
        const doctorName = this.getAttribute('data-doctorname');
        const patientId = this.id; // Patient ID is stored in the button's ID
        const patientName = this.getAttribute('data-patientname');
        const appointmentTime = this.getAttribute('data-appointmenttime');
        const appointmentDate = this.getAttribute('data-appointmentdate');

        // Set values in the modal
        document.getElementById('doctorId').value = doctorId;
        document.getElementById('doctorName').value = doctorName;
        document.getElementById('patientId').value = patientId;
        document.getElementById('patientName').value = patientName;
        document.getElementById('appointmentTime').value = appointmentTime;
        document.getElementById('appointmentDate').value = appointmentDate;

        // Show the modal
        $('#pModal').modal('show');
      });
    });

    // Form submission listener
    prescriptionForm.addEventListener('submit', function(event) {
      event.preventDefault(); // Prevent the form from submitting the traditional way

      if (isSubmitting) {
        return; // If already submitting, exit the function
      }

      isSubmitting = true; // Set flag to true
      const saveButton = document.getElementById('saveChanges'); // Reference to the save button
      saveButton.disabled = true; // Disable the button to prevent further clicks

      const formData = new FormData(this);

      fetch('insert_prescription.php', { // Ensure the path is correct
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            console.log(data.message);
            $('#pModal').modal('hide');
            window.location.href = 'appointment_list.php';
            toastr.success("Added appointment");
          } else {
            console.error(data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error); // Network error
        })
        .finally(() => {
          isSubmitting = false;
          saveButton.disabled = false;
        });
    });
  });
</script>


<script type="text/javascript">
  $(document).ready(function() {
    $('.patientDetails').click(function() {
      var patientId = $(this).attr('id');
      var doctorId = $(this).data('doctorid');
      var doctorName = $(this).data('doctorname');
      var patientName = $(this).data('patientname');
      var appointmentTime = $(this).data('appointmenttime');
      var appointmentDate = $(this).data('appointmentdate');

      // Populate modal fields
      $('#doctorId').val(doctorId);
      $('#patientId').val(patientId);
      $('#doctorName').val(doctorName);
      $('#patientName').val(patientName);
      $('#appointmentTime').val(appointmentTime);
      $('#appointmentDate').val(appointmentDate);

      // Show the modal
      $('#pModal').modal('show');
    });
  });

  // Handle form submission
  $('#prescriptionForm').on('submit', function(e) {
    e.preventDefault();

    // Collect form data
    const formData = $(this).serialize();

    // Send data to the server via AJAX
    $.ajax({
      type: 'POST',
      url: 'insert_prescription.php', // Replace with your server-side script
      data: formData,
      success: function(response) {
        toastr.success("Prescription Added Successfully!!");
        setTimeout(() => {
          location.reload(); // Reload the page after 2 seconds
        }, 2000);
        $('#pModal').modal('hide');
        // Optionally refresh the table or handle the response
      },
      error: function() {
        // Handle error
        alert('Error adding prescription!');
      }
    });
  });





  $(document).ready(function() {


    //===============================details
    $(document).on('click', '.patientDetails', function() {

      var p_id = $(this).attr("id");
      // console.log(product_id);

      $.ajax({
        url: "get/get_patient_details_admin.php",
        method: "POST",
        data: {
          p_id: p_id
        },
        success: function(data) {
          // console.log(data);
          $("#patient-details").html(data);
          $(".bd-example-modal-lg").modal('show');
        }
      });
    });

    //=======================end



    $('#doc_name').keyup(function() {


      console.log("11");

      var searchText = $(this).val();

      if (searchText != '') {

        $.ajax({
          url: 'search/search_doctor.php',
          method: 'POST',
          data: {
            query: searchText
          },
          success: function(response) {
            $('#show-list').html(response);
          }

        });

      } else {
        $('#show-list').html('');
      }

    });


    $(document).on('click', 'li', function() {


      var id_name = $(this).text();
      id_name = id_name.split(":");
      var id = id_name[0];
      var name = id_name[1];
      //console.log(id);
      $('#doc_name').val(name);
      $('#doc_id').val(id);
      $('#show-list').html('');





    }); // end li click function


  }); // end document ready


  $(document).ready(function() {
    $('#DocTable').DataTable({

      lengthMenu: [15, 25, 50],
      "columnDefs": [{
        "className": "dt-center",
        "targets": "_all"
      }],


    });
  });
</script>