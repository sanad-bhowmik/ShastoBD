<?php
session_start();
require_once("../include/dbcon.php");
require_once("../include/dasfunctions.php");



if(isset($_POST['role_id'])){

  $role_id = $_POST['role_id'];

  $edate = date("Y-m-d");

  $added_by = $_SESSION['user'];
  $user_id = $_SESSION['user_id'];
  $can_view=1;


  $sql  = "delete from tbl_permission where role_id='$role_id' " ;

  $result = mysqli_query($GLOBALS['con'],$sql);

  if ($result) {

    if(isset($_POST['menucheckbox']))

      foreach ($_POST['menucheckbox'] as  $value) {

          $menu_id = $value;
          $sql  = "insert into tbl_permission (role_id,menu_id,can_view,added_by,user_id,edate) " ;
          $sql  .= " Values ('$role_id','$menu_id','$can_view','$added_by','$user_id','$edate') " ;

          $result = mysqli_query($GLOBALS['con'],$sql);

       
      }


      if(isset($_POST['sub_menucheckbox']))

        foreach ($_POST['sub_menucheckbox'] as  $value) {

          $myArray = explode('-', $value);

          $menu_id = $myArray[0];
          $sub_menu_id = $myArray[1];

          $sql  = "insert into tbl_permission (role_id,menu_id,sub_menu_id,can_view,added_by,user_id,edate) " ;
          $sql  .= " Values ('$role_id','$menu_id','$sub_menu_id','$can_view','$added_by','$user_id','$edate') " ;

          $result = mysqli_query($GLOBALS['con'],$sql);
          //echo $sql;

        }

        echo "success";

      }

  //echo "string";




}
else{

  echo "failed";

}

?>