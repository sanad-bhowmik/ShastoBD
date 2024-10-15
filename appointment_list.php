<?php
include_once("include/header.php");
if (isset($_POST['submit'])) {

  $param = array();

  if (isset($_POST['doc_id']) &&  $_POST['doc_id'] != "" &&  !empty($_POST['doc_id'])) {

    $docID = $_POST['doc_id'];
    $condition = "DOCID =" . $docID;
    array_push($param, $condition);
  }
  if (isset($_POST['status'])) {
    if ($_POST['status'] != "all") {
      $condition = " Status = '" . $_POST['status'] . "' ";
      array_push($param, $condition);
    }
  }
  if (isset($_POST['fdate']) && isset($_POST['todate'])) {


    $condition = " AppointmentDate between '" . $_POST['fdate'] . "' and '" . $_POST['todate'] . "' ";
    array_push($param, $condition);
  }


  $condition = implode(" and ", $param);


  $sql_get_data = "select * from appointmentview  where  " . $condition . "  order by AppointmentDate desc";
  //var_dump($sql_get_data);
  //die;
  $result = mysqli_query($GLOBALS['con'], $sql_get_data);
} // end if submit
else {
  $sql_get_data = "select * from appointmentview  where 1=1 order by AppointmentDate desc ";

  $result = mysqli_query($GLOBALS['con'], $sql_get_data);
}









?>
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

                      echo  "<a href='#' id='" . $rs['PatientID'] . "' class='patientDetails'>" . $rs['PatientName'] . "</a><br>" . $rs['PatientMobile'] . "";

                      ?>
                    </td>
                    <!-- <td class="text-center">
                      <?php echo $rs['Status']; ?>
                    </td> -->



                    <td class="text-center">
                   
                       <button id="<?php echo $rs['DOCID']; ?>" type="button" class="btn-sm mr-2 mb-2 btn-primary doctorDetails">Details</button>

                      <!--   <button id="<?php echo $rs['DOCID']; ?>" type="button" class="btn-sm mr-2 mb-2 btn-danger docDelete">Remove</button> -->

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

<div id="pModal" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
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
        <div class="modal-body" id="patient-details">

        </div>
        <div class="modal-footer">

          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <!-- <button id="saveChanges" type="submit" class="btn btn-primary">Save changes</button> -->
        </div>
      </form>
    </div>
  </div>
</div>


<script type="text/javascript">
  $(document).ready(function() {


    //===============================details
    $(document).on('click', '.patientDetails', function() {

      var p_id = $(this).attr("id");
      //  console.log(product_id);

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





    }); // end  li click function


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