<?php
include_once("include/header.php");




if (isset($_POST['submit'])) {

  $param = array();

  /* if(isset($_POST['doc_id']) &&  $_POST['doc_id'] !="" &&  !empty($_POST['doc_id'])){
    
    $docID = $_POST['doc_id'];
    $condition = "doctorID =".$docID;
    array_push($param,$condition);

  }
*/

  if (isset($_POST['name'])) {

    $name = $_POST['name'];
    $condition = " (DocName like '%" . $name . "%' or PatientName like '%" . $name . "%') ";

    array_push($param, $condition);
  }
  if (isset($_POST['status'])) {


    if ($_POST['status'] != "all") {
      $condition = " service_status = '" . $_POST['status'] . "' ";
      array_push($param, $condition);
    }
  }


  if (isset($_POST['fdate']) && isset($_POST['todate'])) {


    $condition = " DATE(created_at) between '" . $_POST['fdate'] . "' and '" . $_POST['todate'] . "' ";
    array_push($param, $condition);
  }

  if (isset($_POST['txn_id'])) {

    $condition = " txn_id like '%" . $_POST['txn_id'] . "%'";
    array_push($param, $condition);
  }


  $condition = implode(" and ", $param);


  $sql_get_data = "select * from invoiceList where  " . $condition . " order by  created_at desc";
  //var_dump($sql_get_data);
  //die;
  $result = mysqli_query($GLOBALS['con'], $sql_get_data);
} // end if submit
else {

  $sql_get_data = "select * from invoiceList where 1=1 order by  created_at desc   ";

  $result = mysqli_query($GLOBALS['con'], $sql_get_data);
}
?>

<div class="app-main__inner">
  <form id="search" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <div class="row">
      <div class="col-md-12">
        <div class="main-card mb-3 card">
          <div class="card-header">Search Invoice</div>
          <div class="card-body">
            <div class="position-relative row form-group">

              <div class="col-sm-3">
                <input type="text" hidden="true" name="doc_id" class="form-control" id="doc_id">
                <p class="text">Type Name :</p> <input autocomplete="off" type="text" name="name" value="<?php if (isset($_POST['name'])) echo $_POST['name']; ?>" class="form-control" placeholder="Type Name">
                <div class="list-group" id="show-list">
                </div>
              </div>
              <div class="col-sm-3">

                <p class="text">Transaction ID :</p> <input autocomplete="off" type="text" value="<?php if (isset($_POST['txn_id'])) echo $_POST['txn_id']; ?>" name="txn_id" class="form-control" id="txn_id" placeholder="Type Transaction ">


              </div>
              <div class="col-sm-2">

                <p class="text">From Date :</p>
                <input class="form-control" required value="<?php if (isset($_POST['fdate'])) echo $_POST['fdate']; ?>" type="date" name="fdate" id="fdate">
              </div>
              <div class="col-sm-2">

                <p class="text">To Date :</p>
                <input class="form-control" required type="date" value="<?php if (isset($_POST['todate'])) echo $_POST['todate']; ?>" name="todate" id="todate">
              </div>

              <div class="col-sm-2">

                <p class="text">Service</p>

                <select name="status" id="status" class="form-control">
                  <option value="all">All
                  </option>

                  <option value="active">Active
                  </option>
                  <option value="done">Done
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
        <div class="card-header"> <span id="total"></span> &nbsp;&nbsp;&nbsp;&nbsp; <span id="totalQty"></span>

        </div>
        <div class="card-body">
          <div class="card-title">Doctors</div>
          <!-- Button trigger modal -->


          <div class="table-responsive">
            <table id="DocTable" class="align-middle mb-0 table table-borderless table-striped table-hover">
              <thead>
                <tr>
                  <th class="text-center">Sl</th>
                  <th class="text-center">Paid By</th>

                  <th class="text-center">To Doctor</th>

                  <th class="text-center">Transaction ID</th>

                  <th class="text-center">Paid</th>
                  <th class="text-center">Received</th>
                  <th class="text-center">Service</th>
                  <th class="text-center">Pay Date</th>

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
                      <?php

                      echo  "<a href='#' id='" . $rs['patientID'] . "' class='patientDetails'>" . $rs['PatientName'] . "</a><br>" . $rs['PatientNumber'] . "";

                      ?>
                    </td>

                    <td class="text-center">
                      <?php echo $rs['DocName']; ?>
                    </td>

                    <td class="text-center">
                      <?php echo $rs['txn_id']; ?>
                    </td>
                    <td class="text-center">
                      <?php echo $rs['amount']; ?>
                    </td>
                    <td class="text-center">
                      <?php echo $rs['paid_amount']; ?>
                    </td>
                    <td class="text-center">
                      <?php echo $rs['service_status']; ?>
                    </td>
                    <td class="text-center">
                      <?php echo $rs['created_at']; ?>
                    </td>



                    <td class="text-center">
                      N/A
                      <!-- <button id="<?php echo $rs['id']; ?>" type="button" class="btn-sm mr-2 mb-2 btn-primary doctorDetails">Details</button> -->

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

    var table = $('#DocTable').DataTable();

    var sum = table.column(4).data().sum();
    var sum2 = table.column(5).data().sum();

    $('#total').html("Total Amount : " + sum.toFixed(2));
    $('#totalQty').html("Total Recevied: " + sum2.toFixed(2));

  });
</script>