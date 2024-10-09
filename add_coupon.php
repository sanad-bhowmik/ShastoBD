<?php

include_once("include/initialize.php");

include_once("include/header.php");
?> 
<style>

#uploadForm {border-top:#F0F0F0 2px solid;background:#FAF8F8;padding:10px;}
#uploadForm label {margin:2px; font-size:1em; font-weight:bold;}
.demoInputBox{padding:5px; border:#F0F0F0 1px solid; border-radius:4px; background-color:#FFF;}
#progress-bar {background-color: #12CC1A;height:20px;color: #FFFFFF;width:0%;-webkit-transition: width .3s;-moz-transition: width .3s;transition: width .3s;}
.btnSubmit{background-color:#09f;border:0;padding:10px 40px;color:#FFF;border:#F0F0F0 1px solid; border-radius:4px;}
#progress-div {border:#0FA015 1px solid;padding: 5px 0px;margin:30px 0px;border-radius:4px;text-align:center;}
#targetLayer{width:100%;text-align:center;}

</style>
<script>
$(document).ready(function() { 
  $('#uploadForm').submit(function(e) { 
            e.preventDefault();
            $('#loader-icon').show();
            $(this).ajaxSubmit({ 
                target:   '#targetLayer', 
                beforeSubmit: function() {
                    $("#progress-bar").width('0%');
                },
                uploadProgress: function (event, position, total, percentComplete){ 

                    console.log(percentComplete);
                    $("#progress-bar").width(percentComplete + '%');
                    $("#progress-bar").html('<div id="progress-status">' + percentComplete +' %</div>')
                },
                success:function (){
                    $('#loader-icon').hide();
                    
                },
                resetForm: true 
            }); 
            return false; 
    }); // end upload form
});// end document
</script>


<!-- Main content -->
<div class="app-main__inner">


    <div class="row">
      <div class="col-md-12">
        <div class="main-card mb-3 card">
          <div class="card-header">Add Coupon</div>
          <div class="card-body">
            

            <form id="uploadForm"  action="up/up_coupon.php" method="post" >


              <div class="position-relative row  form-group  p-t-10">
                <div class="col-sm-2">
                <p class="text">Coupon Code</p> <input type="text" required="true" name="coupon_code" class="form-control" placeholder="Coupon Code">
              </div>

               <div class="col-sm-2">
                <p class="text">Discount</p> <input type="number" required="true" name="discount" class="form-control"  placeholder="Discount">
              </div>
              <div class="col-sm-3">
                <p class="text">Discount Type</p> 
                    <select name="type" class="form-control">
                    <option value="fixed">Fixed</option>
                    <option value="percent">Percent</option>
                    </select>

              </div>
              <div class="col-sm-3">
                <p class="text">Doctor Type</p> 
                    <select name="doctor_type" class="form-control">
                    <option value="All">All</option>
                    <option value="Specialist">Specialist</option>
                    <option value="General Practitioner">General Practitioner</option>
                    <option value="Other Professional">Other Professional</option>
                    </select>

              </div>
               <div class="col-sm-2">
                <p class="text">Expiry Date</p> 
                <input type="date" required="true" name="expiry_date" class="form-control"  >
              </div>


             </div>
                  
             
             <hr>




<hr>
<div class="position-relative row form-group p-t-10 ">
<div class="col-sm-4 ">


                   <button type="submit"    name="submit" class="btn btn-secondary">Submit</button>


</div>
</div>
             

<div id="progress-div"><div id="progress-bar"></div></div>
<div id="targetLayer"></div>


             </form>
          </div> 
             <div id="loader-icon" style="display:none;"><img src="LoaderIcon.gif" /></div>

          </div>
        </div>
      </div><!--end row -->


  <div class="row">
    <div class="col-md-12">
      <div class="main-card mb-3 card">
        <div class="card-header">Coupon List
         
        </div>

            <?php
          $sql_get_data ="select * from tbl_coupons   ";
      
      $result = mysqli_query($GLOBALS['con'],$sql_get_data);
      ?>



        <div class="table-responsive">
          <table class="align-middle mb-0 table table-borderless table-striped table-hover">
            <thead>
              <tr>
                <th class="text-center">SL</th>
                <th class="text-center">Coupon Code</th>
                <th class="text-center">Coupon Discount</th>
                <th class="text-center">Discount Type</th>
                <th class="text-center">Doctor Type</th>
                
                <th class="text-center">Status</th>
                <th class="text-center">Expiry Date</th>
               
                <th class="text-center">Option</th>
              </tr>
            </thead>
            <tbody>
              <?php $i=0; while ($rs = mysqli_fetch_array($result)) { $i++;?>
                <tr>


                 <td class="text-muted text-center" >
                  <?php echo $i; ?>
                </td>
                <td class="text-center">
                  <?php echo $rs['coupon_code']; ?>
                </td>
                  <td class="text-center">
                  <?php echo $rs['discount_percent']; ?>
                </td>

                </td>
                  <td class="text-center">
                  <?php echo $rs['discount_type']; ?>
                </td>
                </td>
                  <td class="text-center">
                  <?php echo $rs['doctor_type']; ?>
                </td>
              
                 <td class="text-center">
                  <?= $rs['coupon_status']=="1" ? 'Active' : 'In-Active' ?>
                </td>
                             
                <td class="text-center">
                  <?php  echo $rs['expiry_date']; ?>
                </td>
                <td class="text-center">
                  <?php  echo "r" ?>
                </td>

              </tr>
            <?php } ?> 
          </tbody>
            </table>
          </div>
     
        </div>
      </div>

     </div> 







</div> <!-- app inner main -->












<?php
include_once("include/footer.php");
?>
