<?php
session_start();
require_once("../include/dbcon.php");
require_once("../include/dasfunctions.php");
date_default_timezone_set("Asia/Dhaka"); 

if(isset($_POST['aid'])){

  $aid = $_POST['aid'];

  $productDetails = get_fetched_content_details_data_by_content_id($aid);
  
  $cat_id = $_POST['cat_id'];
  $title = $_POST['title'];
  $price_id = $_POST['price_id'];

  $price = get_price_by_id($price_id);

  $description = $_POST['des'];

  $user_remarks = $_POST['user_remarks'];

//=============================
      


//=================================

      $gm1="";
      $gm2="";
      $gm3="";
      $gm4="";
      $ai="";
      $appUp="";
      $gallery='applications/gallery/';
      $appMb="";
      $appName="";
      $filePath="";




  if((isset($_FILES["app_file"]) && $_FILES["app_file"]["error"] == 0 ) )
  {
      $app_file_name = $_FILES["app_file"]["name"];


      $app_file_name = str_replace(' ', '',$app_file_name);
      $appName       =$app_file_name;

      $app_file_type = $_FILES["app_file"]["type"];
      $app_file_size = $_FILES["app_file"]["size"];
      $app_file      = $_FILES["app_file"]["tmp_name"];
      $app_file_mb   = number_format(($app_file_size/1024/1024), 2);
      $appMb= $app_file_mb   = $app_file_mb ." "."mb";
      $app_file_path = '../applications/apk/';

      if(file_exists($app_file_path.$app_file_name)){

        $appUp = uniqid().time().$app_file_name;
      
      }else{
        $appUp = uniqid().time().$app_file_name;
      }
       $appUp =  str_replace(' ', '_', $appUp);






  if(move_uploaded_file($app_file ,  $app_gm1_path.$appUp)){

    if ( is_file('../applications/apk/'.$productDetails['file_path']) ) {



     chmod ( '../applications/apk/'.$productDetails['file_path'],0777);
    
     if(unlink ('../applications/apk/'.$productDetails['file_path'])){

      $file = fopen("../log/unlink.txt","w");
      echo fwrite($file,'../applications/apk/'.$productDetails['file_path']);
      fclose($file);

     }




   }

   $appUp ='applications/apk/'.$appUp;
    //$upload_msg .="Gallery Image 1".$gm1;
 }
 else{
  $appUp= "";
 }



}// end app icon


















//image manipulation

  if((isset($_FILES["ai"]) && $_FILES["ai"]["error"] == 0 ) )
  {
      $app_icon_name = $_FILES["ai"]["name"];
      $app_icon_name = str_replace(' ', '', $app_icon_name);

      $app_icon_type = $_FILES["ai"]["type"];
      $app_icon_size = $_FILES["ai"]["size"];
      $app_icon      = $_FILES["ai"]["tmp_name"];
      $app_icon_mb   = $app_icon_size/1024/1024;
      $app_icon_mb   = $app_icon_mb ." "."mb";
      $app_icon_path = '../applications/icon/';


   if(file_exists($app_icon_path.$app_icon_name)){
    $ai = uniqid().time().$app_icon_name ;
  }else{
    $ai = uniqid().time().$app_icon_name ;
  }
  $ai =  str_replace(' ', '_', $ai);
  


  if(move_uploaded_file($app_icon,  $app_gm1_path.$ai)){

    if ( is_file('../applications/icon/'.$productDetails['icon']) ) {



     chmod ( '../applications/icon/'.$productDetails['icon'],0777);
    
     if(unlink ('../applications/icon/'.$productDetails['icon'])){

      $file = fopen("../log/unlink.txt","w");
      echo fwrite($file,'../applications/icon/'.$productDetails['icon']);
      fclose($file);

     }
   }

     $ai= 'applications/icon/'.$ai;
    //$upload_msg .="Gallery Image 1".$gm1;
 }
 else{
  $ai= "";
 }



}// end app icon




//========================================
  if((isset($_FILES["gm1"]) && $_FILES["gm1"]["error"] == 0 ) )
  {


   $app_gm1_name = $_FILES["gm1"]["name"];
   $app_gm1_name = str_replace(' ', '',$app_gm1_name);

   $app_gm1_type = $_FILES["gm1"]["type"];
   $app_gm1_size = $_FILES["gm1"]["size"];
   $app_gm1      = $_FILES["gm1"]["tmp_name"];
   $app_gm1_mb   = $app_gm1_size/1024/1024;
   $app_gm1_mb   = $app_gm1_mb ." "."mb";
   $app_gm1_path = '../applications/gallery/';


   if(file_exists($app_gm1_path.$app_gm1_name)){
    $gm1 = uniqid().time().$app_gm1_name;
  }else{
    $gm1 = uniqid().time().$app_gm1_name;
  }
  $gm1 =  str_replace(' ', '_', $gm1);
  


  if(move_uploaded_file($app_gm1,  $app_gm1_path.$gm1 )){

    if ( is_file('../applications/gallery/'.$productDetails['gallery_img1']) ) {



     chmod ( '../applications/gallery/'.$productDetails['gallery_img1'],0777);
    
     if(unlink ('../applications/gallery/'.$productDetails['gallery_img1'])){

      $file = fopen("../log/unlink.txt","w");
      echo fwrite($file,'../applications/gallery/'.$productDetails['gallery_img1']);
      fclose($file);

     }




   }
   $gm1 = $gallery.$gm1;
    //$upload_msg .="Gallery Image 1".$gm1;
 }
 else{
  $gm1="";
 }



}// end img1


if(isset($_FILES["gm2"]) && $_FILES["gm2"]["error"] == 0){

  $app_gm2_name = $_FILES["gm2"]["name"];
  $app_gm2_name = str_replace(' ', '',$app_gm2_name);
  $app_gm2_type = $_FILES["gm2"]["type"];
  $app_gm2_size = $_FILES["gm2"]["size"];
  $app_gm2      = $_FILES["gm2"]["tmp_name"];
  $app_gm2_mb   = $app_gm2_size/1024/1024;
  $app_gm2_mb   = $app_gm2_mb ." "."mb";
 $app_gm2_path = '../applications/gallery/';


   if(file_exists($app_gm2_path.$app_gm2_name)){
    $gm2 = uniqid().time().$app_gm2_name;
  }else{
    $gm2 = uniqid().time().$app_gm2_name;
  }
  $gm2 =  str_replace(' ', '_', $gm2);
  

  
  if(move_uploaded_file($app_gm2,  $app_gm2_path.$gm2 )){

    if ( is_file('../applications/gallery/'.$productDetails['gallery_img2']) ) {



     chmod ( '../applications/gallery/'.$productDetails['gallery_img2'],0777);
    
     if(unlink ('../applications/gallery/'.$productDetails['gallery_img2'])){

      $file = fopen("../log/unlink.txt","w");
      echo fwrite($file,'../applications/gallery/'.$productDetails['gallery_img2']);
      fclose($file);

     }




   }
   $gm2 = $gallery.$gm2;
    //$upload_msg .="Gallery Image 1".$gm1;
 }
 else{
  $gm2="";
 }




}//end img2


if(isset($_FILES["gm3"]) && $_FILES["gm3"]["error"] == 0){

  $app_gm3_name = $_FILES["gm3"]["name"];
  $app_gm3_name = str_replace(' ', '',$app_gm3_name);
  $app_gm3_type = $_FILES["gm3"]["type"];
  $app_gm3_size = $_FILES["gm3"]["size"];
  $app_gm3      = $_FILES["gm3"]["tmp_name"];
  $app_gm3_mb   = $app_gm3_size/1024/1024;
  $app_gm3_mb   = $app_gm3_mb ." "."mb";
  $app_gm3_path = '../applications/gallery/';


   if(file_exists($app_gm3_path.$app_gm3_name)){
    $gm3 = uniqid().time().$app_gm3_name;
  }else{
    $gm3 = uniqid().time().$app_gm3_name;
  }
  $gm3 =  str_replace(' ', '_', $gm3);
  

  
  if(move_uploaded_file($app_gm3,  $app_gm3_path.$gm3 )){

    if ( is_file('../applications/gallery/'.$productDetails['gallery_img3']) ) {



     chmod ( '../applications/gallery/'.$productDetails['gallery_img3'],0777);
    
     if(unlink ('../applications/gallery/'.$productDetails['gallery_img3'])){

      $file = fopen("../log/unlink.txt","w");
      echo fwrite($file,'../applications/gallery/'.$productDetails['gallery_img3']);
      fclose($file);

     }




   }
   $gm3 = $gallery.$gm3;
    //$upload_msg .="Gallery Image 1".$gm1;
 }
 else{
  $gm3="";
 }

} // end img3

if(isset($_FILES["gm4"]) && $_FILES["gm4"]["error"] == 0){
  $app_gm4_name = $_FILES["gm4"]["name"];
  $app_gm4_name = str_replace(' ', '',$app_gm4_name);
  $app_gm4_type = $_FILES["gm4"]["type"];
  $app_gm4_size = $_FILES["gm4"]["size"];
  $app_gm4      = $_FILES["gm4"]["tmp_name"];
  $app_gm4_mb   = $app_gm4_size/1024/1024;
  $app_gm4_mb   = $app_gm4_mb ." "."mb";
 $app_gm4_path = '../applications/gallery/';


   if(file_exists($app_gm4_path.$app_gm4_name)){
    $gm4 = uniqid().time().$app_gm4_name;
  }else{
    $gm4 = uniqid().time().$app_gm4_name;
  }
  $gm4 =  str_replace(' ', '_', $gm4);
  
  
  if(move_uploaded_file($app_gm4,  $app_gm4_path.$gm4 )){

    if ( is_file('../applications/gallery/'.$productDetails['gallery_img4']) ) {



     chmod ( '../applications/gallery/'.$productDetails['gallery_img4'],0777);
    
     if(unlink ('../applications/gallery/'.$productDetails['gallery_img4'])){

      $file = fopen("../log/unlink.txt","w");
      echo fwrite($file,'../applications/gallery/'.$productDetails['gallery_img4']);
      fclose($file);

     }




   }
    $gm4 = $gallery.$gm4;

    //$upload_msg .="Gallery Image 1".$gm1;
 }
 else{
  $gm4="";
 }




}// end img4

//end image manipulation

if($gm1==""){
  $gm1 =$productDetails['gallery_img1'];
} 
if($gm2==""){
  $gm2 =$productDetails['gallery_img2'];
} 
if($gm3==""){
  $gm3 =$productDetails['gallery_img3'];
} 

if($gm4==""){
  $gm4 =$productDetails['gallery_img4'];
} 
if($appMb==""){
  $appMb =$productDetails['size'];
} 
if($appName==""){
  $appName =$productDetails['file_name'];
} 
if($ai==""){
  $ai =$productDetails['app_icon'];
} 
if($appUp==""){
  $appUp =$productDetails['file_path'];
} 



$udate = date("Y-m-d H:i:s");
$user = $_SESSION['user'];

$sql  = "update applications " ;

$sql .= " set cat_id='$cat_id' , description = '$description' ,price_id = '$price_id' ,price = '$price' ,size='$appMb',  ";
$sql .= " file_name='$appName',file_path='$appUp' , icon='$ai', user_remarks='$user_remarks', ";

$sql .= " gallery_img1='$gm1' , gallery_img2 = '$gm2' ,gallery_img3= '$gm3' , gallery_img4 ='$gm4' ,";


$sql .= " updated_date='$udate' , updated_by ='$user' where id='$aid'  limit 1";

mysqli_query($GLOBALS['con'],'SET CHARACTER SET utf8');
$result = mysqli_query($GLOBALS['con'],$sql);

if($result){
  //echo $sql;

  echo $aid;
}
else{
 // echo $sql;

 echo "Error!!";
}


}
else{

  echo "failed";

}

?>