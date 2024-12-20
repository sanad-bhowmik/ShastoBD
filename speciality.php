<?php
if (!isset($_GET['status']) || !is_numeric($_GET['status'])) {
  header("Location: logout.php");
  exit;
}
$status = $_GET['status'];
include_once("include/header.php");

?>

<div class="app-main__inner">


  <div class="row">
    <div class="col-md-12">
      <div class="main-card mb-3 card">
        <div class="card-body">
          <div class="card-title">Doctors</div>
          <!-- Button trigger modal -->


          <?php
          $sql_get_data = "select * from tbl_specialist where Active ='$status' order by OID DESC ";

          $result = mysqli_query($GLOBALS['con'], $sql_get_data);
          ?>



          <div class="table-responsive">
            <table id="DocTable" class="align-middle mb-0 table table-borderless table-striped table-hover">
              <thead>
                <tr>
                  <th class="text-center">Sl</th>
                  <th class="text-center">Specialization</th>
                  <th class="text-center">Option</th>
                </tr>
              </thead>
              <tbody>
                <?php $i = 1;
                while ($rs = mysqli_fetch_array($result)) { ?>
                  <tr>

                    <td class="text-muted text-center">
                      <input size="5" class="ordering" style="display: none;" id="<?= $rs['OID'] ?>" class="form-control" type="text" value="<?= $rs['ordering'] ?>" />

                      <?= $rs['ordering'] ?>
                    </td>

                    <td class="text-center">
                      <?php echo $rs['Specialization']; ?>
                    </td>


                    <td class="text-center">



                      <?php

                      if ($status == 1) {

                        $output = '<button id="' . $rs['OID'] . '" type="button" class="btn-sm mr-2 mb-2 btn-warning docInActive">InActive</button> ';
                        $output .= ' <button id="' . $rs['OID'] . '" type="button" class="btn-sm mr-2 mb-2 btn-primary docEdit">Edit</button>';
                      } else {
                        $output = '<button id="' . $rs['OID'] . '" type="button" class="btn-sm mr-2 mb-2 btn-success docActive">Active</button> ';
                        $output .= ' <button id="' . $rs['OID'] . '" type="button" class="btn-sm mr-2 mb-2 btn-danger docDelete">Remove</button>';
                      }

                      echo $output;
                      ?>





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
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">
          <div id="pdt">Details</div>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="UpdateContent" method="post" enctype="multipart/form-data">
        <div class="modal-body" id="details">

        </div>
        <div class="modal-footer">

          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button id="saveChanges" type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>


<script type="text/javascript">
  $(document).ready(function() {

    $('.bd-example-modal-lg').on('hidden.bs.modal', function() {
      location.reload();
    });


  });


  //===============================eidt
  $(document).on('click', '.docEdit', function() {

    var doc_id = $(this).attr("id");
    //  console.log(product_id);

    $.ajax({
      url: "get/get_speciality_details.php",
      method: "POST",
      data: {
        id: doc_id
      },
      success: function(data) {
        // console.log(data);
        $("#details").html(data);
        $(".bd-example-modal-lg").modal('show');
      }
    });
  });

  //=======================end


  //===========start remove
  $('.docDelete').on('click', function() {

    var promotionID = $(this).attr("id");

    //  console.log($(this).val()); 
    $.confirm({
      title: 'Confirm!',
      content: 'Are you sure to Remove this ?? Data Can not be Recovered !!',
      buttons: {
        confirm: function() {

          $.ajax({

            url: "delete/delete_special.php",
            method: "POST",
            data: {
              pid: promotionID
            },
            success: function(response) {

              console.log(response);
              $.confirm({
                title: 'notice',
                content: response + "Reload Page ??",
                buttons: {

                  Yes: function() {
                    location.reload();
                  },
                  No: function() {}
                }
              });

              //   location.reload();
            }




          });




        },
        cancel: function() {
          $.alert('Canceled!');
        }

      }
    });


  });




  //==============end remove


  //===========start InActive
  $('.docInActive').on('click', function() {

    var promotionID = $(this).attr("id");

    //  console.log($(this).val()); 
    $.confirm({
      title: 'Confirm!',
      content: 'Are you sure want to In-Active this ?? Can be Actived Later !!',
      buttons: {
        confirm: function() {

          $.ajax({

            url: "update/inactive_special.php",
            method: "POST",
            data: {
              pid: promotionID
            },
            success: function(response) {

              console.log(response);
              $.confirm({
                title: 'notice',
                content: response + "Reload Page ??",
                buttons: {

                  Yes: function() {
                    location.reload();
                  },
                  No: function() {}
                }
              });

              //   location.reload();
            }




          });




        },
        cancel: function() {
          $.alert('Canceled!');
        }

      }
    });


  });




  //==============end Inactive

  //===========start Active
  $('.docActive').on('click', function() {

    var promotionID = $(this).attr("id");

    //  console.log($(this).val()); 
    $.confirm({
      title: 'Confirm!',
      content: 'Are you sure want to Active this ?? Can be In-Actived Later !!',
      buttons: {
        confirm: function() {

          $.ajax({

            url: "update/active_special.php",
            method: "POST",
            data: {
              pid: promotionID
            },
            success: function(response) {

              console.log(response);
              $.confirm({
                title: 'notice',
                content: response + "Reload Page ??",
                buttons: {

                  Yes: function() {
                    location.reload();
                  },
                  No: function() {}
                }
              });

              //   location.reload();
            }




          });




        },
        cancel: function() {
          $.alert('Canceled!');
        }

      }
    });


  });




  //==============end active







  /*
      function loadProductDetailsAdmin(id){
          
          var product_id = id ; 
          console.log(id);
          $.ajax({
          url:"get/get_product_details_admin.php",
          method:"POST",
          data:{product_id:product_id},
          success:function(data)
          {   
            
              $("#product-details").html(data);
              $(".bd-example-modal-lg").modal('show');
          }
            });
        }*/

  $('#UpdateContent').on('submit', function(event) {
    event.preventDefault();




    if ($('#oid').val() == "") {
      $.alert({
        title: 'Encountered an error!',
        content: 'Something went downhill/ You have not filled all field ',
        type: 'red',
        typeAnimated: true,

      });
    } else {

      $.ajax({
        url: "update/update_special_details.php",
        method: "POST",
        data: new FormData(this),
        contentType: false,
        processData: false,

        success: function(data) {
          if (data === 'failed') {
            //   alert('Something went wrong');
            // console.log(data);
            toastr.error('Something went wrong/Missing').fadeOut(6000);
          } else if (data === 'error') {
            //console.log(data);
            toastr.error('Something wrong with update').fadeOut(6000);
          } else {
            // alert(data);
            // console.log(data);
            // $(".bd-example-modal-lg").modal('hide');
            toastr.success("Update Success").fadeOut(5000)
            $('#pdt').html('<span style="color:red;">Update success !! </span>');

            $.alert({
              title: 'Update was Success!',
              content: 'Data updated successfully !!',
              type: 'Green',
              typeAnimated: true

            });

            //  loadProductDetailsAdmin(data);
            // $(".bd-example-modal-lg").modal('show');
            //  $('#pdt').html('<span >Product Details </span>');
          }


        }
      });

    }

  });



  $(document).ready(function() {
    $('#DocTable').DataTable({

      lengthMenu: [15, 25, 50],
      "columnDefs": [{
        "className": "dt-center",
        "targets": "_all"
      }],


    });
  });



  $('.ordering').on('change', function() {

    var oid = $(this).attr("id");
    var value = $(this).val();

    console.log(value);
    if ($.isNumeric(value)) {
      $.ajax({
        url: "update/update_special.php",
        method: "POST",
        data: {
          oid: oid,
          ordering: value
        },
        success: function(data) {
          // console.log(data);
          alert("Updated");
        }
      });
    } else {

      alert("Enter a numeric value");
    }




  });
</script>