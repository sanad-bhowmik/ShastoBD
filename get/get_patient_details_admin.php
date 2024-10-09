<?php
require_once("../include/dbcon.php");
require_once("../include/dasfunctions.php");


if(isset($_POST['p_id'])){

  $pId = $_POST['p_id'];
  $pDetails = get_fetched_patient_details_data_by_p_id($pId);
  $prescriptions = get_prescriptions_by_p_id($pId);
  $reports = get_patientreports_by_p_id($pId);
  $appointments = get_appointments_by_p_id($pId);



  $output   ='<div class="modal-body ">';


  $output .='<div class="row">

  <div class="col-md-3">
  Name: '.$pDetails['Name'].'
  
  </div>
  <div class="col-md-3">
  Mobile: '.$pDetails['Mobile'].'
  </div>
  <div class="col-md-3">
  Email: '.$pDetails['Email'].'
  </div>
  </div>
  <hr>
  <div class="row">
  <div class="col-md-3">
  DOB: '.$pDetails['Dob'].'
  </div>
  
  <div class="col-md-3">
 Weight: '.$pDetails['Weight'].'
  </div>
  
  </div>
  <hr>
  ';


  $output .='<hr><div class="row">

  <div class="col-md-6 " style="overflow-y: scroll; height:200px;">
 Reports: <ul class="list-group">';

 foreach($reports as $report){

$output .= ' <li class="list-group-item "><a  target="_blank" href="'.$report['ReportFile'].'">Report Name :'.$report['ReportName'].' ('.$report['ReportDate'] .') </a></li>';

 }



 
 
 $output .='</ul>
  </div>
  <div class="col-md-6" style="overflow-y: scroll; height:200px;">
 Prescriptions: <ul class="list-group">';

 foreach($prescriptions as $prescription){

$output .= ' <li class="list-group-item "><a  target="_blank" href="'.$prescription['FilePath'].'"> Ref No: '.$prescription['RefNo'].' ('.$prescription['created_at'] .') </a></li>';

 }



 
 
 $output .='</ul>
  </div>
  
  
  </div>
 
  ';

  $output .='<hr><div class="row">

  <div class="col-md-12" style="overflow-y: scroll; height:200px;">
  <div class="table-responsive">
  <table id="DocTable" class="align-middle mb-0 table table-borderless table-striped table-hover">
    <thead>
      <tr>
        <th class="text-center">Sl</th>
        <th class="text-center">Doctor</th>
        <th class="text-center">Doctor Number</th>
        <th class="text-center">Appointment Time</th>
        <th class="text-center">Appointment Date</th>
        <th class="text-center">Patient</th>
        <th class="text-center">Status</th>
      </tr>
    </thead>
    <tbody>';
    $i=1;
    foreach($appointments as $rs){

      $output .='
      
      <tr>

                    <td class="text-muted text-center">
                      '.$i.'
                    </td>

                    <td class="text-center">
                      '.$rs['DocName'] .'
                    </td>
                    <td class="text-center">
                      '. $rs['MobileNum'] .' 
                    </td>
                    <td class="text-center">
                    '. $rs['Appointment_Time'] .'
                    </td>
                    <td class="text-center">
                      '. $rs['AppointmentDate'] .'
                    </td>
                    <td class="text-center">
                    '. $rs['PatientName'] .'
                    </td>
                    <td class="text-center">
                    '. $rs['Status'] .'
                    </td>
                     </tr> ';

      $i++;
    }
    
    
    $output .='</tbody>
    </table>


  </div>
  
  
  
  
  </div>
  </div>
  <hr>
  ';






$output .='<hr><div class="row">
<div class="col-md-4">

</div>
<div class="col-md-4">
Profile: <img width="150px" class="myImg" style="border: 1px solid black;"  id="g1"  height="130px" src="'.$pDetails['PFile'].'" alt=" No Image">

</div>
<div class="col-md-4">

</div>



</div>
<hr>
';





$output .='</div>'; 

$output .='
<div id="myImgModal" class="ImgModal">
<span class="Imgclose">&times;</span>
<img class="img-modal-content" src="" id="img01">
<div id="caption"></div>
</div>






';




                echo $output;

              }
              else{

                echo " Somthing went wrong !!";

              }

              ?>

