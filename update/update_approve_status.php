<?php
session_start();
require_once("../include/dbcon.php");
require_once("../include/dasfunctions.php");
date_default_timezone_set("Asia/Dhaka"); 


if(isset($_POST['app_id'])){

  $command="";
  $home_cat =0;
  $remarks="";

  $app_id = mysqli_real_escape_string($GLOBALS['con'],$_POST['app_id']);
  $command = mysqli_real_escape_string($GLOBALS['con'],$_POST['command']);

  if (isset($_POST['remarks']) ){
    $remarks = mysqli_real_escape_string($GLOBALS['con'],$_POST['remarks']);
  }
  if (isset($_POST['home_cat_id']) ){
    $home_cat = mysqli_real_escape_string($GLOBALS['con'],$_POST['home_cat_id']);     
  }
  

  $remarks= str_replace("\r\n","",trim($remarks));
  $user = $_SESSION['user'];
  $user_id = $_SESSION['user_id'];

  $status="";
  $app_status='0';


  if($command=="approve"){
    $status="approved";
    $app_status='1';
  }
  else if($command=="reject"){
    $status="rejected";
    $app_status='0';
  }
  else if($command=="re-submit"){
     $status="re-submission";
     $app_status='0';
  }
    else if($command=="re-submit-user"){
    $status="pending";
    $app_status='0';
  }
  else{

    $status="pending";
    $app_status='0';
  }


  $date = date("Y-m-d H:i:s");

  if($command=="re-submit-user"){

     $sql  ="update applications set app_status='$app_status',approve_status='$status' , user_remarks='$remarks' ,
     approve_by='',approve_date=null,flag='new' where id='$app_id' limit 1";

  }
  else{
    $sql  = "update applications set app_status='$app_status',approve_status='$status' , admin_remarks='$remarks',home_cat_id='$home_cat', approve_date='$date',approve_by='$user',approve_id='$user_id'  where id='$app_id' limit 1";

  }

  $result = mysqli_query($GLOBALS['con'],$sql);

  if($result){
   echo $sql ;

 }
 else{

  echo $sql;

}





}






?>