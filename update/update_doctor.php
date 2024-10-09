<?php
session_start();
require_once("../include/dbcon.php");
require_once("../include/dasfunctions.php");
date_default_timezone_set("Asia/Dhaka"); 

if(isset($_POST['docID'])){

  $docId = $_POST['docID'];

 //========================================
$dayOfPractice ="";

$days= array();

$daysWithTime = array();
$allDaysWithTime = array();
  
if(isset($_POST['saturday']['day'])){
  $times =  array();
  $times['StartTime']=$_POST['saturday']['st'];
  $times['EndTime']=$_POST['saturday']['et'];
 // array_push($times, $_POST['saturday']['st']);

  //var_dump($times);
  $allDaysWithTime[$_POST['saturday']['day']]= $times;
  array_push($days,$_POST['saturday']['day']);
  
}
if(isset($_POST['sunday']['day'])){
  $times =  array();
  $times['StartTime']=$_POST['sunday']['st'];
  $times['EndTime']=$_POST['sunday']['et'];
 // array_push($times, $_POST['saturday']['st']);

  //var_dump($times);
  $allDaysWithTime[$_POST['sunday']['day']]= $times;
  array_push($days,$_POST['sunday']['day']);
  
}
if(isset($_POST['monday']['day'])){
  $times =  array();
  $times['StartTime']=$_POST['monday']['st'];
  $times['EndTime']=$_POST['monday']['et'];
 // array_push($times, $_POST['saturday']['st']);

  //var_dump($times);
  $allDaysWithTime[$_POST['monday']['day']]= $times;
  array_push($days,$_POST['monday']['day']);
  
}
if(isset($_POST['tuesday']['day'])){
  $times =  array();
  $times['StartTime']=$_POST['tuesday']['st'];
  $times['EndTime']=$_POST['tuesday']['et'];
 // array_push($times, $_POST['saturday']['st']);

  //var_dump($times);
  $allDaysWithTime[$_POST['tuesday']['day']]= $times;
  array_push($days,$_POST['tuesday']['day']);
  
}
if(isset($_POST['wednesday']['day'])){
  $times =  array();
  $times['StartTime']=$_POST['wednesday']['st'];
  $times['EndTime']=$_POST['wednesday']['et'];
 // array_push($times, $_POST['saturday']['st']);

  //var_dump($times);
  $allDaysWithTime[$_POST['wednesday']['day']]= $times;
  array_push($days,$_POST['wednesday']['day']);
  
}
if(isset($_POST['thursday']['day'])){
  $times =  array();
  $times['StartTime']=$_POST['thursday']['st'];
  $times['EndTime']=$_POST['thursday']['et'];
 // array_push($times, $_POST['saturday']['st']);

  //var_dump($times);thursday
  $allDaysWithTime[$_POST['thursday']['day']] = $times;
  array_push($days,$_POST['thursday']['day']);
  
}
if(isset($_POST['friday']['day'])){
  $times =  array();
  $times['StartTime']=$_POST['friday']['st'];
  $times['EndTime']=$_POST['friday']['et'];
 // array_push($times, $_POST['saturday']['st']);

  //var_dump($times);
  $allDaysWithTime[$_POST['friday']['day']]= $times;
  array_push($days,$_POST['friday']['day']);
  
}


  $dayOfPractice  = implode(",", $days);





$specialArr =  array();
$speciality="";

if(isset($_POST['speciality'])){

foreach ($_POST['speciality'] as $value) {
 array_push($specialArr,$value);
}

$speciality = implode(",",$specialArr);

}

$opArr =  array();
$op="";

if(isset($_POST['op'])){

foreach ($_POST['op'] as $value) {
 array_push($opArr,$value);
}

$op = implode(",",$opArr);

}




//==========================================
 
//image manipulation
      $gm1="";
      $fileName1="";
      $fileName2="";
     // $server="http://103.108.140.210:85/shasthobdAdmin/";
     $server="https://app.shasthobd.com/";
      $gm2="";
      $gallery='applications/docImg/';
      $filepath1="";
      $filepath2="";

      $upload_msg ="";


      // gallary image 1=============================
 if(isset($_FILES["gm1"]) && $_FILES["gm1"]["error"] == 0){
      $app_gm1_name = $_FILES["gm1"]["name"];
      $app_gm1_type = $_FILES["gm1"]["type"];
      $app_gm1_size = $_FILES["gm1"]["size"];
      $app_gm1      = $_FILES["gm1"]["tmp_name"];
      $app_gm1_mb   = $app_gm1_size/1024/1024;
      $app_gm1_mb   = $app_gm1_mb ." "."mb";
      $app_gm1_path = '../applications/docImg/';

    
      if(file_exists($app_gm1_path.$app_gm1_name)){
        $gm1 = time().$app_gm1_name;
      }else{
        $gm1 = time().$app_gm1_name;
      }
       $gm1 =  str_replace(' ', '_', $gm1);

      if(move_uploaded_file($app_gm1,  $app_gm1_path.$gm1 )){

       $upload_msg .="-Profile Image ".$gm1;
      }
      else{
        $upload_msg .=" -Profile Image  uploading failed ";
      }

      $fileName1=$gm1;
      $gm1 = $gallery.$gm1;
      $filepath1 = $server.$gm1;


    }

//============================================================



      if(isset($_FILES["gm2"]) && $_FILES["gm2"]["error"] == 0){

      $app_gm2_name = $_FILES["gm2"]["name"];
      $app_gm2_name = str_replace(' ', '',$app_gm2_name);
      $app_gm2_type = $_FILES["gm2"]["type"];
      $app_gm2_size = $_FILES["gm2"]["size"];
      $app_gm2      = $_FILES["gm2"]["tmp_name"];
      $app_gm2_mb   = $app_gm2_size/1024/1024;
      $app_gm2_mb   = $app_gm2_mb ." "."mb";
      $app_gm2_path = '../applications/docImg/';


      if(file_exists($app_gm2_path.$app_gm2_name)){
        $gm2 = time().$app_gm2_name;
      }else{
        $gm2 = time().$app_gm2_name;
      }
        $gm2 =  str_replace(' ', '_', $gm2);

      if(move_uploaded_file($app_gm2,  $app_gm2_path.$gm2 )){

       $upload_msg .="-Signature Image ".$gm2;
      }
      else{
        $upload_msg .=" -Signature Image uploading failed ";
      }


      $fileName2=$gm2;
      $gm2 = $gallery.$gm2;
      $filepath2 = $server.$gm2;



      }// end is set

//end image manipulation



//=====================================================



$docHospital="";
$docType="";
$gen_prac="";
$docName="";
$bmdcReg="";
$docDegree="";
$docPayment=0;
$followup_payment=0;
$report_showing_payment =0;
$counseling_payment=0;
$docMobile="";
$docPass="";
$docAddress="";

$remarks="";
$dob="";
$email="";
$gender ="";
$active=1;
$status=1;


$json_day_time = json_encode($allDaysWithTime);



     

      
      $date = date("Y-m-d H:i:s");

      if(isset($_POST['docHospital'])){

        $docHospital = mysqli_real_escape_string($GLOBALS['con'], $_POST['docHospital']) ;
         
      }
      if(isset($_POST['DocType'])){
         $docType = $_POST['DocType'];
      }


      if(isset($_POST['gen_prac'])){
         $gen_prac = $_POST['gen_prac'];
      }


      $reasons  = array();

      if($gen_prac=="Y"){

    foreach ($result=get_all_reasons() as $value ) {

      array_push($reasons,$value['OID']);
      
        }
   }
    $reasons = implode(",", $reasons);




      if(isset($_POST['docName'])){
        $docName =  mysqli_real_escape_string($GLOBALS['con'], $_POST['docName']);
      }
      if(isset($_POST['bmdcReg'])){
         $bmdcReg = $_POST['bmdcReg'];
      }
      if(isset($_POST['docDegree'])){
         $docDegree = mysqli_real_escape_string($GLOBALS['con'], $_POST['docDegree']) ;
      }
      if(isset($_POST['docPayment'])){
         $docPayment = mysqli_real_escape_string($GLOBALS['con'], $_POST['docPayment']) ;
      }
      //===07012021
      if(isset($_POST['followup_payment'])){
        $followup_payment = mysqli_real_escape_string($GLOBALS['con'], $_POST['followup_payment']) ;
     }
      //4/12/2021
      
      if(isset($_POST['counseling_payment'])){
        $counseling_payment = mysqli_real_escape_string($GLOBALS['con'], $_POST['counseling_payment']) ;
     }
   
     if(isset($_POST['report_showing_payment'])){
      $report_showing_payment = mysqli_real_escape_string($GLOBALS['con'], $_POST['report_showing_payment']) ;
   }

      
      if(isset($_POST['docNumber'])){
         $docMobile = mysqli_real_escape_string($GLOBALS['con'], $_POST['docNumber']) ;
      }
       if(isset($_POST['docPass'])){
         $docPass = mysqli_real_escape_string($GLOBALS['con'], $_POST['docPass']) ;
      }
      
       if(isset($_POST['docNumber'])){
         $docMobile = mysqli_real_escape_string($GLOBALS['con'], $_POST['docNumber']) ;
      }
       if(isset($_POST['docAddress'])){
         $docAddress = mysqli_real_escape_string($GLOBALS['con'], $_POST['docAddress']) ;
      }
       if(isset($_POST['docRemarks'])){
        $remarks = mysqli_real_escape_string($GLOBALS['con'], $_POST['docRemarks']) ;
      }
        if(isset($_POST['gender'])){
        $gender = mysqli_real_escape_string($GLOBALS['con'], $_POST['gender']) ;
      }
       if(isset($_POST['email'])){
        $email = mysqli_real_escape_string($GLOBALS['con'], $_POST['email']) ;
      }
       if(isset($_POST['dob'])){
        $dob = mysqli_real_escape_string($GLOBALS['con'], $_POST['dob']) ;
      }
      
      
      $user_id = $_SESSION['user_id'];








//===================================================


$udate = date("Y-m-d H:i:s");
$user = $_SESSION['user'];

$sql  = "update tbl_doctor " ;

$sql .= "set DocName='$docName' , BmdcReg = '$bmdcReg' ,JsonTime = '$json_day_time' ,DocDegree = '$docDegree', ";

$sql .= "DocAddress='$docAddress' , DocType = '$docType' ,ReasonID = '$reasons' , ";

$sql .= "HospitalID='$docHospital' , SpecialArea = '$speciality' ,MobileNum = '$docMobile' , ";
$sql .= "Password='$docPass' , Remarks = '$remarks',Payment = '$docPayment',general_payment = '$docPayment',
        report_showing_payment='$report_showing_payment',followup_payment ='$followup_payment',counseling_payment='$counseling_payment', ";

if($gm1 !=""){
  $sql .= "DocImage='$filepath1' ,fileName ='$fileName1' , ";
} 
if($gm2 !=""){

 $sql .= "DocSignature='$filepath2' ,fileNameSignature ='$fileName2' , ";

} 

$sql .= "Gen_Prac='$gen_prac' , OtherPfID = '$op' ,DayOfPractice = '$dayOfPractice' , ";


$sql .= "Gender = '$gender',DOB = '$dob',Email = '$email', ";

$sql .= "updated_at='$udate' , updated_by ='$user' where DOCID='$docId'  limit 1";


$result = mysqli_query($GLOBALS['con'],$sql);

if($result){
 // echo $pid;
 // echo $sql;
 echo "success";
}
else{
  //echo $sql;

    echo "error";
}


}
else{

  echo "failed";

}

?>