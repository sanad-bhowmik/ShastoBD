<?php
require_once("../include/dbcon.php");
require_once("../include/dasfunctions.php");


if(isset($_POST['oid'])){
  if(isset($_POST['simg']) && $_POST['simg'] != ""){
    $img =  $_POST['simg'];
  }
  //echo "string";

  //$sliderDetails =  get_fetched_slider_details_data_by_id($sid);
  

  $gm1="";

//image manipulation


  if((isset($_FILES["gm1"]) && $_FILES["gm1"]["error"] == 0 ) )
  {

    //echo $_FILES["gm1"]["name"];
    //die;

    $server="https://app.shasthobd.com/";
    $gm2="";
    $gallery='applications/appImg/';
   $app_gm1_name = $_FILES["gm1"]["name"];
   $app_gm1_name = str_replace(' ', '',$app_gm1_name);

   $app_gm1_type = $_FILES["gm1"]["type"];
   $app_gm1_size = $_FILES["gm1"]["size"];
   $app_gm1      = $_FILES["gm1"]["tmp_name"];
   $app_gm1_mb   = $app_gm1_size/1024/1024;
   $app_gm1_mb   = $app_gm1_mb ." "."mb";
   $app_gm1_path = '../applications/appImg/';


   if(file_exists($app_gm1_path.$app_gm1_name)){
    $gm1 = uniqid().time().$app_gm1_name;
  }else{
    $gm1 = uniqid().time().$app_gm1_name;
  }
  $gm1 =  str_replace(' ', '_', $gm1);
  if(move_uploaded_file($app_gm1,  $app_gm1_path.$gm1 )){

    if ( is_file('../applications/appImg/'.$img) ) {



     chmod ( '../applications/appImg/'.$img,0777);
    
     if(unlink ('../applications/appImg/'.$img)){

      $file = fopen("../log/unlink.txt","w");
      echo fwrite($file,'../applications/appImg/'.$img);
      fclose($file);

     }




   }

    //$upload_msg .="Gallery Image 1".$gm1;
 }
 else{
  $gm1="";
 }

}// end img1


//end image manipulation

if($gm1==""){
  $gm1 = $img;
} 

      $fileName1=$gm1;
      $gm1 = $gallery.$gm1;
      $filepath1 = $server.$gm1;



  $pid = trim($_POST['oid']);

  $name = $_POST['name'];

  $sql  = "update tbl_specialist set Specialization='".$name."', ImagePath='".$filepath1."' where OID ='$pid' limit 1 ";

  $result = mysqli_query($GLOBALS['con'],$sql);

  if($result){
   echo  "200";

 }
 else{

  echo  "failed";

}


}else{

  echo "error";
}






?>