<?php
include_once("include/initialize.php");
include_once("include/header.php");

?>
<style>
  #uploadForm {
    border-top: #F0F0F0 2px solid;
    background: #FAF8F8;
    padding: 10px;
  }

  #uploadForm label {
    margin: 2px;
    font-size: 1em;
    font-weight: bold;
  }

  .demoInputBox {
    padding: 5px;
    border: #F0F0F0 1px solid;
    border-radius: 4px;
    background-color: #FFF;
  }

  #progress-bar {
    background-color: #12CC1A;
    height: 20px;
    color: #FFFFFF;
    width: 0%;
    -webkit-transition: width .3s;
    -moz-transition: width .3s;
    transition: width .3s;
  }

  .btnSubmit {
    background-color: #09f;
    border: 0;
    padding: 10px 40px;
    color: #FFF;
    border: #F0F0F0 1px solid;
    border-radius: 4px;
  }

  #progress-div {
    border: #0FA015 1px solid;
    padding: 5px 0px;
    margin: 30px 0px;
    border-radius: 4px;
    text-align: center;
  }

  #targetLayer {
    width: 100%;
    text-align: center;
  }
</style>
<script>
  $(document).ready(function() {


    $("#DocType").change(function() {


      // console.log("1");
      var sub = $(this).val();
      if (sub == "Specialist") {

        $("#gen_prac").val("N");

        $.ajax({
          url: "get/get_speciality.php",

          type: 'GET',
          success: function(response) {
            // console.log(response);
            var resp = $.trim(response);
            $("#speciality").html(resp);
          }
        });
      } else if (sub == "General Practitioner") {

        $("#speciality").html("<option value=''>--Speciality--</option>");
        $("#gen_prac").val("Y");
      } else {
        $("#speciality").html("<option value=''>--Speciality--</option>");
        $("#gen_prac").val("N");
      }
    });


    $("#st1").on('change', function() {

      var value = $("#st1")[0].selectedIndex;
      $(".st").each(function(index) {

        this.selectedIndex = value;


      });


    });
    $("#et1").on('change', function() {

      var value = $("#et1")[0].selectedIndex;
      $(".et").each(function(index) {

        this.selectedIndex = value;


      });
    });


    $("#Saturday").click(function() {
      this.checked ? $("#st2").prop("required", true) : $("#st2").prop("required", false);
      this.checked ? $("#et2").prop("required", true) : $("#et2").prop("required", false);
      // this.checked ? $("#et2").attr('required',1) : $("#et2").attr('required',0);
      //console.log($("#st2"));
    });
    $("#Sunday").click(function() {
      this.checked ? $("#st3").prop("required", true) : $("#st3").prop("required", false);
      this.checked ? $("#et3").prop("required", true) : $("#et3").prop("required", false);
      // this.checked ? $("#et2").attr('required',1) : $("#et2").attr('required',0);
      //console.log($("#st2"));
    });
    $("#Monday").click(function() {
      this.checked ? $("#st4").prop("required", true) : $("#st4").prop("required", false);
      this.checked ? $("#et4").prop("required", true) : $("#et4").prop("required", false);
      // this.checked ? $("#et2").attr('required',1) : $("#et2").attr('required',0);
      //console.log($("#st2"));
    });
    $("#Tuesday").click(function() {
      this.checked ? $("#st5").prop("required", true) : $("#st5").prop("required", false);
      this.checked ? $("#et5").prop("required", true) : $("#et5").prop("required", false);
      // this.checked ? $("#et2").attr('required',1) : $("#et2").attr('required',0);
      //console.log($("#st2"));
    });
    $("#Wednesday").click(function() {
      this.checked ? $("#st6").prop("required", true) : $("#st6").prop("required", false);
      this.checked ? $("#et6").prop("required", true) : $("#et6").prop("required", false);
      // this.checked ? $("#et2").attr('required',1) : $("#et2").attr('required',0);
      //console.log($("#st2"));
    });
    $("#Thursday").click(function() {
      this.checked ? $("#st7").prop("required", true) : $("#st7").prop("required", false);
      this.checked ? $("#et7").prop("required", true) : $("#et7").prop("required", false);
      // this.checked ? $("#et2").attr('required',1) : $("#et2").attr('required',0);
      //console.log($("#st2"));
    });
    $("#Friday").click(function() {
      this.checked ? $("#st8").prop("required", true) : $("#st8").prop("required", false);
      this.checked ? $("#et8").prop("required", true) : $("#et8").prop("required", false);
      // this.checked ? $("#et2").attr('required',1) : $("#et2").attr('required',0);
      //console.log($("#st2"));
    });









    ///////////////////











    $("#g1").hide();
    $("#g2").hide();


    $('#uploadForm').submit(function(e) {
      e.preventDefault();
      var chkbox = $("input[type=checkbox]");
      var count = 0;

      chkbox.each(function(index) {

        // console.log(this);
        if (this.checked) {
          count++;
        }



      });




      if (count > 0) {

        //  console.log($('#gameFile').val());


        $('#loader-icon').show();
        $(this).ajaxSubmit({
          target: '#targetLayer',
          beforeSubmit: function() {
            $("#progress-bar").width('0%');
          },
          uploadProgress: function(event, position, total, percentComplete) {

            console.log(percentComplete);
            $("#progress-bar").width(percentComplete + '%');
            $("#progress-bar").html('<div id="progress-status">' + percentComplete + ' %</div>')
          },
          success: function() {
            $('#loader-icon').hide();
            $("#g1").hide();
            $("#g2").hide();;
          },
          resetForm: true
        }); //end  ajax submit



        return false;
      } // end if
      else {

        $.alert({
          title: 'Notice!!',
          content: 'Please Check at least One Practice Day',
          type: 'green',
          typeAnimated: true

        });

      }
    });



  });

  function readURL(input) {

    var id = input.id;


    if (input.files && input.files[0]) {
      var reader = new FileReader();

      reader.onload = function(e) {

        if (id == "gm1") {
          $('#g1').show();
          $('#g1').attr('src', e.target.result);
        } else if (id == "gm2") {

          $('#g2').show();
          $('#g2').attr('src', e.target.result);
        }



      };

      reader.readAsDataURL(input.files[0]);
    }
  }
</script>




<!-- Main content -->
<div class="app-main__inner">


  <div class="row">
    <div class="col-md-12">
      <div class="main-card mb-3 card">
        <div class="card-header">Add Doctor</div>
        <div class="card-body">


          <form id="uploadForm" action="up/up_doctor.php" method="post" enctype="multipart/form-data">




            <div class="position-relative row  form-group">

              <div class="col-sm-3">
                <!--  Hospital:  <select  id="hospital" class="form-control" name="docHospital" >

                  <?php
                  /*  $output ='<option value="">--Select Hospital--</option>';
                  $result2 = get_all_hospital();
                  while ($values=mysqli_fetch_array($result2)) {

                    $output .="<option value='".$values['OID']."'>".strtoupper($values['HospitalName'])."</option>";
                  }
                 echo $output; */
                  ?>
                </select>

                <div id="addhospital" class="btn-success">+ Add Hospital</div> -->
                Hospital: <input type="text" required="" class="form-control" name="docHospital">

              </div>




              <div class="col-sm-3">
                DocType: <select id="DocType" class="form-control" name="DocType">

                  <?php
                  $output = '<option value="">--Select Type--</option>';

                  $output .= "<option value='Specialist'>Specialist</option>";
                  $output .= "<option value='General Practitioner'>General Practitioner</option>";
                  $output .= "<option value='Other Professional'>Other Professional</option>";

                  echo $output;
                  ?>
                </select>

              </div>




              <div class="col-sm-3">


                Speciality: <select id="speciality" class="form-control selectpicker" name="speciality[]" multiple data-live-search="true">
                  <option value=''>--Speciality--</option>

                </select>



              </div>

              <div class="col-sm-3">
                General Practice: <input class="form-control" id="gen_prac" type="text" readonly="true" name="gen_prac" value="Y">

              </div>






            </div>

            <div class="position-relative row  form-group p-t-10">
              <div class="col-sm-4">

                Doctor Name: <input class="form-control" required="" type="text" name="docName" id="docName">



              </div>
              <div class="col-sm-4">

                BMDC REG: <input class="form-control" type="text" name="bmdcReg" id="bmdcReg">



              </div>
              <div class="col-sm-4">

                Doctor Degree: <input class="form-control" required="" type="text" name="docDegree" id="docDegree">




              </div>




            </div>


            <div class="position-relative row  form-group p-t-10">
              <div class="col-sm-3">

                General Fee: <input class="form-control" required min="0" type="number" name="docPayment" id="docpayment">




              </div>

              <div class="col-sm-3">
                Report Showing Fee: <input class="form-control" required min="0" value="0" type="number" name="report_showing_payment" id="report_showing_payment">




              </div>
              <div class="col-sm-3">

                Follow up Fee:<input class="form-control" required min="0" value="0" type="number" name="followup_payment" id="followup_payment">


              </div>
              <div class="col-sm-3">

                Counseling Fee <input class="form-control" required min="0" value="0" type="number" name="counseling_payment" id="counseling_payment">


              </div>



            </div>


            <div class="position-relative row  form-group p-t-10">
              <div class="col-sm-3">

                Doctor Mobile (Login): <input required="" class="form-control" type="text" name="docNumber" id="docNumber">



              </div>
              <div class="col-sm-3">

                Doctor Password: <input class="form-control" required="" type="text" name="docPass" id="docPass">



              </div>
              <div class="col-sm-3">

                Doctor Address: <textarea id="docAddress" class="form-control" name="docAddress" cols="35" rows="1"></textarea>




              </div>
              <div class="col-sm-3">

                Doctor Remarks: <input class="form-control" type="text" name="docRemarks" id="docRemarks">




              </div>


            </div>
            <div class="row  form-group p-t-10">


              <div class="col-sm-3">
                Other Professional: <select name="op[]" id="op" class="form-control selectpicker" multiple data-live-search="true">
                  <?php
                  $output3 = '';
                  $result4 = get_all_op();
                  while ($values = mysqli_fetch_array($result4)) {

                    $output3 .= "<option value='" . $values['OID'] . "'>" . strtoupper($values['Professional']) . "</option>";
                  }
                  echo $output3;
                  ?>
                </select>

              </div>
              <div class="col-sm-3">
                Gender: <select name="gender" id="gender" class="form-control">
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                  <option value="Others">Others</option>
                </select>

              </div>
              <div class="col-sm-3">
                Date Of Birth: <input required type="date" value="<?= date('Y-m-d') ?>" class="form-control" name="dob">
              </div>

              <div class="col-sm-3">
                Email: <input type="email" class="form-control" name="email">
              </div>






            </div>


            <hr>
            <div class="">
              <label>Default Start End Time: </label>

              <select id="st1" required="" class="st">

                <?php
                $output = '<option value="">--Start Time --</option>';
                $result2 = get_times();
                while ($values = mysqli_fetch_array($result2)) {

                  $output .= "<option value='" . $values['time'] . "'>" . strtoupper($values['12hr_time']) . "</option>";
                }
                echo $output;
                ?>
              </select>
              <select id="et1" required="">

                <?php
                $output = '<option value="">--End Time --</option>';
                $result2 = get_times();
                while ($values = mysqli_fetch_array($result2)) {

                  $output .= "<option value='" . $values['time'] . "'>" . strtoupper($values['12hr_time']) . "</option>";
                }
                echo $output;
                ?>
              </select>
            </div>

            <hr>

            <div class="row">



              <div class="col-sm-4">
                <div class="input-group mb-2">
                  <div class="input-group-prepend">
                    <div class="input-group-text">
                      <input type="checkbox" id="Saturday" name="saturday[day]" value="Saturday">
                      <label for="Saturday">Saturday</label>
                    </div>
                  </div>
                  <select id="st2" name="saturday[st]" class="st">

                    <?php
                    $output = '<option value="">Start Time</option>';
                    $result2 = get_times();
                    while ($values = mysqli_fetch_array($result2)) {

                      $output .= "<option value='" . $values['time'] . "'>" . strtoupper($values['12hr_time']) . "</option>";
                    }
                    echo $output;
                    ?>
                  </select>
                  <select id="et2" name="saturday[et]" class="et">

                    <?php
                    $output = '<option value="">End Time</option>';
                    $result2 = get_times();
                    while ($values = mysqli_fetch_array($result2)) {

                      $output .= "<option value='" . $values['time'] . "'>" . strtoupper($values['12hr_time']) . "</option>";
                    }
                    echo $output;
                    ?>
                  </select>
                </div>
              </div>

              <div class="col-sm-4">


                <div class="input-group mb-2">
                  <div class="input-group-prepend">
                    <div class="input-group-text">
                      <input type="checkbox" id="Sunday" name="sunday[day]" value="Sunday">
                      <label for="Sunday">Sunday &nbsp;&nbsp;</label>
                    </div>
                  </div>







                  <select id="st3" name="sunday[st]" class="st">

                    <?php
                    $output = '<option value="">Start Time</option>';
                    $result2 = get_times();
                    while ($values = mysqli_fetch_array($result2)) {

                      $output .= "<option value='" . $values['time'] . "'>" . strtoupper($values['12hr_time']) . "</option>";
                    }
                    echo $output;
                    ?>
                  </select>


                  <select id="et3" name="sunday[et]" class="et">

                    <?php
                    $output = '<option value="">End Time</option>';
                    $result2 = get_times();
                    while ($values = mysqli_fetch_array($result2)) {

                      $output .= "<option value='" . $values['time'] . "'>" . strtoupper($values['12hr_time']) . "</option>";
                    }
                    echo $output;
                    ?>
                  </select>

                </div>
              </div>

              <div class="col-sm-4">
                <div class="input-group mb-2">
                  <div class="input-group-prepend">
                    <div class="input-group-text">
                      <input type="checkbox" id="Monday" name="monday[day]" value="Monday">
                      <label for="Monday">Monday&nbsp;&nbsp;</label>
                    </div>
                  </div>



                  <select id="st4" name="monday[st]" class="st">

                    <?php
                    $output = '<option value="">Start Time</option>';
                    $result2 = get_times();
                    while ($values = mysqli_fetch_array($result2)) {

                      $output .= "<option value='" . $values['time'] . "'>" . strtoupper($values['12hr_time']) . "</option>";
                    }
                    echo $output;
                    ?>
                  </select>
                  <select id="et4" name="monday[et]" class="et">

                    <?php
                    $output = '<option value="">End Time</option>';
                    $result2 = get_times();
                    while ($values = mysqli_fetch_array($result2)) {

                      $output .= "<option value='" . $values['time'] . "'>" . strtoupper($values['12hr_time']) . "</option>";
                    }
                    echo $output;
                    ?>
                  </select>
                </div>
              </div>
            </div>





            <div class="row  form-group p-t-10">


              <div class="col-sm-4">
                <div class="input-group mb-2">
                  <div class="input-group-prepend">
                    <div class="input-group-text">
                      <input type="checkbox" id="Tuesday" name="tuesday[day]" value="Tuesday">
                      <label for="Tuesday">Tuesday&nbsp;</label>
                    </div>
                  </div>




                  <select id="st5" name="tuesday[st]" class="st">

                    <?php
                    $output = '<option value="">Start Time</option>';
                    $result2 = get_times();
                    while ($values = mysqli_fetch_array($result2)) {

                      $output .= "<option value='" . $values['time'] . "'>" . strtoupper($values['12hr_time']) . "</option>";
                    }
                    echo $output;
                    ?>
                  </select>
                  <select id="et5" name="tuesday[et]" class="et">

                    <?php
                    $output = '<option value="">End Time</option>';
                    $result2 = get_times();
                    while ($values = mysqli_fetch_array($result2)) {

                      $output .= "<option value='" . $values['time'] . "'>" . strtoupper($values['12hr_time']) . "</option>";
                    }
                    echo $output;
                    ?>
                  </select>

                </div>
              </div>


              <div class="col-sm-4">
                <div class="input-group mb-2">
                  <div class="input-group-prepend">
                    <div class="input-group-text">
                      <input type="checkbox" id="Wednesday" name="wednesday[day]" value="Wednesday">
                      <label for="Wednesday">Wednesday</label>
                    </div>
                  </div>


                  <select id="st6" name="wednesday[st]" class="st">

                    <?php
                    $output = '<option value="">Start Time</option>';
                    $result2 = get_times();
                    while ($values = mysqli_fetch_array($result2)) {

                      $output .= "<option value='" . $values['time'] . "'>" . strtoupper($values['12hr_time']) . "</option>";
                    }
                    echo $output;
                    ?>
                  </select>
                  <select id="st6" name="wednesday[et]" class="et">

                    <?php
                    $output = '<option value="">End Time</option>';
                    $result2 = get_times();
                    while ($values = mysqli_fetch_array($result2)) {

                      $output .= "<option value='" . $values['time'] . "'>" . strtoupper($values['12hr_time']) . "</option>";
                    }
                    echo $output;
                    ?>
                  </select>
                </div>
              </div>


              <div class="col-sm-4">
                <div class="input-group mb-2">
                  <div class="input-group-prepend">
                    <div class="input-group-text">
                      <input type="checkbox" id="Thursday" name="thursday[day]" value="Thursday">
                      <label for="Thursday">Thursday</label>
                    </div>
                  </div>



                  <select id="st7" name="thursday[st]" class="st">

                    <?php
                    $output = '<option value="">Start Time</option>';
                    $result2 = get_times();
                    while ($values = mysqli_fetch_array($result2)) {

                      $output .= "<option value='" . $values['time'] . "'>" . strtoupper($values['12hr_time']) . "</option>";
                    }
                    echo $output;
                    ?>
                  </select>
                  <select id="et7" name="thursday[et]" class="et">

                    <?php
                    $output = '<option value="">End Time</option>';
                    $result2 = get_times();
                    while ($values = mysqli_fetch_array($result2)) {

                      $output .= "<option value='" . $values['time'] . "'>" . strtoupper($values['12hr_time']) . "</option>";
                    }
                    echo $output;
                    ?>
                  </select>
                </div>
              </div>



              <div class="col-sm-4">
                <div class="input-group mb-2">
                  <div class="input-group-prepend">

                    <div class="input-group-text">
                      <input type="checkbox" id="Friday" name="friday[day]" value="Friday">
                      <label for="Friday">Friday&nbsp;&nbsp;&nbsp;&nbsp;</label>
                    </div>
                  </div>


                  <select id="st8" name="friday[st]" class="st">

                    <?php
                    $output = '<option value="">Start Time</option>';
                    $result2 = get_times();
                    while ($values = mysqli_fetch_array($result2)) {

                      $output .= "<option value='" . $values['time'] . "'>" . strtoupper($values['12hr_time']) . "</option>";
                    }
                    echo $output;
                    ?>
                  </select>
                  <select id="et8" name="friday[et]" class="et">

                    <?php
                    $output = '<option value="">End Time</option>';
                    $result2 = get_times();
                    while ($values = mysqli_fetch_array($result2)) {

                      $output .= "<option value='" . $values['time'] . "'>" . strtoupper($values['12hr_time']) . "</option>";
                    }
                    echo $output;
                    ?>
                  </select>


                </div>
              </div>


            </div>





            <div class="row  form-group p-t-10">
              <div class="col-sm-6">
                <div class="custom-file">
                  <input type="file" name="gm1" onchange="readURL(this)" accept="image/*" class="custom-file-input" id="gm1">
                  <label class="custom-file-label" for="customFile">Profile Image</label>
                </div>
                <img class="up-pre" id="g1" src="#" alt="" />

              </div>
              <div class="col-sm-6">
                <div class="custom-file">
                  <input onchange="readURL(this)" name="gm2" accept="image/*" type="file" class="custom-file-input" id="gm2">
                  <label class="custom-file-label" for="customFile">Signature</label>
                </div>
                <img class="up-pre" id="g2" src="#" alt="" />
              </div>




            </div>




            <div id="progress-div">
              <div id="progress-bar"></div>
            </div>
            <div id="targetLayer"></div>




            <div class="position-relative row form-check">
              <div class="col-sm-4 offset-sm-2">

                <button type="submit" name="submit" class="btn btn-secondary">Submit</button>


              </div>
            </div>


          </form>
          <div id="loader-icon" style="display:none;"><img src="LoaderIcon.gif" /></div>




        </div>





      </div>
    </div>
  </div><!--end row -->


</div> <!-- app inner main -->











<?php
include_once("include/footer.php");
?>
<!-- Large modal -->

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Hospital</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="addhospitaldata">
        <div class="modal-body">

          <div class="form-group">
            <label for="recipient-name" class="col-form-label">Hospital Name:</label>
            <input type="text" required class="form-control" id="HospitalName" name="hospitalName">
          </div>


        </div>
        <div class="modal-footer">

          <button type="submit" class="btn btn-primary">Save Hospital</button>

          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>



<script type="text/javascript">
  $(".custom-file-input").on("change", function() {
    //  console.log("hello");
    var fileName = $(this).val().split("\\").pop();
    $(this).siblings(".custom-file-label").addClass("selected").html(fileName);

  });


  $(document).ready(function() {

    $("#addhospital").on("click", function() {
      //console.log("hello");
      $("#exampleModal").modal('show');

    });
    $('#exampleModal').on('hidden.bs.modal', function() {
      location.reload();
    });


    $('#addhospitaldata').submit(function() {

      // console.log("test");
      event.preventDefault();


      //console.log($('#pid').val());
      var datas = new FormData(this);
      console.log(datas);

      $.ajax({
        url: "up/up_hospital.php",
        method: "POST",
        data: new FormData(this),
        contentType: false,
        processData: false,

        success: function(data) {

          // console.log(data);
          if (data === 'failed') {
            $.alert({
              title: 'Failed!',
              content: 'Added unccessfully !!',
              type: 'Red',
              typeAnimated: true

            });

          } else {
            // console.log(data);
            // alert(data);
            //   console.log(data);
            // $(".bd-example-modal-lg").modal('hide');
            $('#exampleModalLabel').html('<span style="color:green;">Add success !! </span>');

            $.alert({
              title: 'Success!',
              content: 'Added successfully !!',
              type: 'Green',
              typeAnimated: true

            });

            // $(".bd-example-modal-lg").modal('show');
            //  $('#pdt').html('<span >Product Details </span>');


            $('#addhospitaldata')[0].reset();
          }


        },
        error: function() {
          alert("failure");
        }
      });



    });



  });
</script>