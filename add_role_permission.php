<?php
include_once("include/header.php");
?>



<div class="app-main__inner">

<form id="UpdatePermission"  method="post" >
    <div class="row">
        <div class="col-md-12">
            <div class="main-card mb-3 card">
                <div class="card-body">
                    <div class="card-title"> Add Role Permissions</div>
                    <hr>
        <div class="row">
                    	<div class="col-md-4">

                <p>User Role :</p>  <select id="user_role" required="true" name="user_role" class="form-control">
                  <?php 
                  $output ='<option>--Select User Role---</option>';

                  foreach ($roles=get_all_user_role() as  $value) {
                    
                     $output .='<option value="'.$value["id"].'">'.$value["role_name"].'</option>';
                  }
                  echo $output;
                  ?>
                  </select>
                    	</div>	
             </div>
          </div>
      </div>
  </div>
</div>

<div id="menu-list">
</div>

<button id="saveChanges" type="submit" class="btn btn-primary">Save changes</button>

</form>

</div>  <!-- app inner end -->

<?php
include_once("include/footer.php");
?>

<!-- Large modal -->


<script type="text/javascript">






      $("#user_role").change(function(){

        //var text = $( "#sub_category option:selected" ).text();
        var id = $(this).val();
        console.log(id);

       

      if(id != "") {
          $.ajax({
            url:"get/get_menus_with_permission.php",
            data:{role_id:id},
            type:'POST',
            success:function(response) {
            //  console.log(response);
              $("#menu-list").html(response);
            }
          });
        } else {
          $("#menu-list").html("No Data");
        }

    }); // end series change

  $(document).ready(function(){
        
      $('#UpdatePermission').on('submit',function(event){
      event.preventDefault();

      var role_id = $('#role_id').val(); 
      var menus= $('.menus:checkbox:checked');
      var sub_menus= $('.sub_menus:checkbox:checked');
      
      var menu_array = [];
      var sub_menu_array = [];

        $.each(menus, function(){

                menu_array.push($(this).val());       

            });


          $.each(sub_menus, function(){

                sub_menu_array.push($(this).val());
               

            });
    

 //   console.log($('#cid').val());

    if($('#role_id').val() == "")  
    {  
        $.alert({
              title: 'Encountered an error!',
              content: 'Something went downhill, this may be serious',
              type: 'red',
              typeAnimated: true,
            
            }); 
    } 
    else{

       $.ajax({
            url:"update/update_permission.php",
            method:"POST",
            data: new FormData(this),
            contentType:false,
            processData:false,
           
            success:function(data)
            {   
                if(data==='failed'){
                    alert('Something went wrong');
                  //  console.log(data);

                }
                else{
                 // alert(data);
                   console.log(data);
                   // $(".bd-example-modal-lg").modal('hide');
            

              $.alert({
              title: 'Update was Success!',
              content: 'Permissions updated successfully !!',
              type: 'Green',
              typeAnimated: true
          
            });
                   // loadProductDetailsAdmin(data);
                 // $(".bd-example-modal-lg").modal('show');
                  //  $('#pdt').html('<span >Product Details </span>');
                }
                

            }
        });

    }

});
     });


</script>
