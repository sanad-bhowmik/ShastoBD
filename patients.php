<?php
include_once("include/header.php");
?>
<script type="text/javascript">
  // Function to show alert based on URL parameters
  function showAlert() {
    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get('msg');

    if (message === 'success') {
      alert("Patient added successfully.");
    }
  }

  // Call the function when the document is ready
  $(document).ready(function () {
    showAlert();
  });
</script>
<script type="text/javascript">
  function readURL(input) {
    var id = input.id;
    if (input.files && input.files[0]) {
      var reader = new FileReader();

      reader.onload = function (e) {
        if (id == "gm1") {
          $('#g1').show();
          $('#g1').attr('src', e.target.result);
        } else if (id == "gm2") {
          $('#g2').show();
          $('#g2').attr('src', e.target.result);
        } else if (id == "gm3") {
          $('#g3').show();
          $('#g3').attr('src', e.target.result);
        } else if (id == "gm4") {
          $('#g4').show();
          $('#g4').attr('src', e.target.result);
        } else if (id == "gi") {
          $('#gip').show();
          $('#gip').attr('src', e.target.result);
        }
      };

      reader.readAsDataURL(input.files[0]);
    }
  }
</script>


<div class="app-main__inner">
  <div class="row">
    <div class="col-md-12">
      <div class="main-card mb-3 card">
        <div class="card-header">Add Patient</div>
        <div class="card-body">
          <!-- Form to Add Patient -->
          <form id="addPatientForm" method="post" action="add_patient.php" class="mb-3">
            <div class="row align-items-end">
              <div class="col-md-2">
                <label for="patientName">Patient Name</label>
                <input type="text" class="form-control" id="patientName" name="patientName" style="font-size: 12px;"
                  required>
              </div>
              <div class="col-md-2">
                <label for="patientMobile">Mobile</label>
                <input type="text" class="form-control" id="patientMobile" name="patientMobile" style="font-size: 12px;"
                  required>
              </div>
              <div class="col-md-2">
                <label for="patientEmail">Email</label>
                <input type="email" class="form-control" id="patientEmail" name="patientEmail" style="font-size: 12px;"
                  >
              </div>
              <div class="col-md-2">
                <label for="patientAddress">Address</label>
                <input type="text" class="form-control" id="patientAddress" name="patientAddress"
                  style="font-size: 12px;" >
              </div>
              <div class="col-md-2">
                <label for="patientGender">Gender</label>
                <select class="form-control" id="patientGender" name="patientGender" style="font-size: 12px;" required>
                  <option value="">Select Gender</option>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                  <option value="Other">Other</option>
                </select>
              </div>
              <div class="col-md-2">
                <button type="submit" class="btn btn-success mt-4">Save</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>



<div class="app-main__inner" style="margin-top: -36px;">
  <div class="row">
    <div class="col-md-12">
      <div class="main-card mb-3 card">
        <div class="card-body">
          <div class="card-title">Patients</div>


          <!-- End Form -->

          <?php
          $sql_get_data = "SELECT * FROM tbl_patient WHERE Active ='1' ORDER BY OID DESC";
          $result = mysqli_query($GLOBALS['con'], $sql_get_data);
          ?>

          <div class="table-responsive">
            <table id="DocTable" class="align-middle mb-0 table table-borderless table-striped table-hover">
              <thead>
                <tr>
                  <th class="text-center">#</th>
                  <th class="text-center">Sl</th>
                  <th class="text-center">Name</th>
                  <th class="text-center">Mobile</th>
                  <th class="text-center">Email</th>
                  <th class="text-center">Address</th>
                  <th class="text-center">Gender</th>
                  <th class="text-center">Option</th>
                </tr>
              </thead>
              <tbody>
                <?php $i = 1;
                while ($rs = mysqli_fetch_array($result)) { ?>
                  <tr>
                    <td class="text-muted text-center"><?php echo $i; ?></td>
                    <td class="text-center"><?php echo $rs['si_num']; ?></td>
                    <td class="text-center"><?php echo $rs['Name']; ?></td>
                    <td class="text-center"><?php echo $rs['Mobile']; ?></td>
                    <td class="text-center"><?php echo $rs['Email']; ?></td>
                    <td class="text-center"><?php echo $rs['Address']; ?></td>
                    <td class="text-center"><?php echo $rs['Gender']; ?></td>
                    <td class="text-center">
                      <button id="<?php echo $rs['OID']; ?>" type="button"
                        class="btn-sm mr-2 mb-2 btn-primary patientDetails">Details</button>
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
<div id="pModal" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">
          <div id="pdt">Details</div>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="UpdateDoctor" method="post" enctype="multipart/form-data">
        <div class="modal-body" id="patient-details"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function () {
    $('.bd-example-modal-lg').on('hidden.bs.modal', function () {
      //location.reload();
    });
  });

  //===============================details
  $(document).on('click', '.patientDetails', function () {
    var p_id = $(this).attr("id");
    $.ajax({
      url: "get/get_patient_details_admin.php",
      method: "POST",
      data: {
        p_id: p_id
      },
      success: function (data) {
        $("#patient-details").html(data);
        $(".bd-example-modal-lg").modal('show');
      }
    });
  });

  //=======================end

  //===========start remove
  $('.docDelete').on('click', function () {
    var promotionID = $(this).attr("id");
    $.confirm({
      title: 'Confirm!',
      content: 'Are you sure to Remove this? Data cannot be recovered!',
      buttons: {
        confirm: function () {
          $.ajax({
            url: "delete/delete_doctor.php",
            method: "POST",
            data: {
              pid: promotionID
            },
            success: function (response) {
              console.log(response);
              $.confirm({
                title: 'Notice',
                content: response + " Reload Page?",
                buttons: {
                  Yes: function () {
                    location.reload();
                  },
                  No: function () { }
                }
              });
            }
          });
        },
        cancel: function () {
          $.alert('Canceled!');
        }
      }
    });
  });
  //==============end remove

  $('#UpdateDoctor').on('submit', function (event) {
    event.preventDefault();
    var chkbox = $("input[type=checkbox]");
    var count = 0;

    chkbox.each(function (index) {
      if (this.checked) {
        count++;
      }
    });

    if ($('#docid').val() == "" || count < 1) {
      $.alert({
        title: 'Encountered an error!',
        content: 'Something went downhill. You have not filled all fields.',
        type: 'red',
        typeAnimated: true,
      });
    } else {
      $.ajax({
        url: "update/update_doctor.php",
        method: "POST",
        data: new FormData(this),
        contentType: false,
        processData: false,
        success: function (data) {
          if (data === 'failed') {
            console.log(data);
            toastr.error('Something went wrong/Missing').fadeOut(6000);
          } else if (data === 'error') {
            console.log(data);
            toastr.error('Something wrong with update').fadeOut(6000);
          } else {
            toastr.success("Update Success").fadeOut(5000);
            $('#pdt').html('<span style="color:red;">Update success !! </span>');

            $.alert({
              title: 'Update was Success!',
              content: 'Doctor data updated successfully!',
              type: 'green',
              typeAnimated: true
            });
          }
        }
      });
    }
  });

  $(document).ready(function () {
    $('#DocTable').DataTable({
      lengthMenu: [15, 25, 50],
      "columnDefs": [{
        "className": "dt-center",
        "targets": "_all"
      }],
    });
  });
</script>