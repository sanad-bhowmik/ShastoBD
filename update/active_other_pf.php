<?php
require_once("../include/dbcon.php");
require_once("../include/dasfunctions.php");


if(isset($_POST['pid'])){

  $pid = trim($_POST['pid']);

  $sql  = "update tbl_otherprofessional set Active=1 where OID ='$pid' limit 1 ";

  $result = mysqli_query($GLOBALS['con'],$sql);

  if($result){
   echo  "Activated successfully !!";

 }
 else{

  echo  "Somthing went wrong !!";

}





}






?>