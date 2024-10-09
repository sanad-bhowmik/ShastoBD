<?php

$today = date("Y-m-d");
$doctors = get_count_by_sql("select count(*) from tbl_doctor where Active=1 ");
$patients = get_count_by_sql("select count(*) from tbl_patient where 1=1 ");
$appointment = get_count_by_sql("select count(*) from appointmentview where AppointmentDate='$today' ");

?>

<!--  Start content-->
                    <div class="app-main__inner">
                        <div class="row">
                            <div class="col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content bg-midnight-bloom">
                                    <div class="widget-content-wrapper text-white">
                                        <div class="widget-content-left">
                                            <div class="widget-heading">Doctors</div>
                                            <div class="widget-subheading">Active Doctors</div>
                                        </div>
                                        <div class="widget-content-right">
                            <div class="widget-numbers text-white"><span><?= $doctors ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content bg-arielle-smile">
                                    <div class="widget-content-wrapper text-white">
                                        <div class="widget-content-left">
                                            <div class="widget-heading">Today <?=$today?></div>
                                            <div class="widget-subheading">Total Appointment Today</div>
                                        </div>
                                        <div class="widget-content-right">
                                            <div class="widget-numbers text-white"><span><?= $appointment ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content bg-grow-early">
                                    <div class="widget-content-wrapper text-white">
                                        <div class="widget-content-left">
                                            <div class="widget-heading">Paitents</div>
                                            <div class="widget-subheading">Total Patients</div>
                                        </div>
                                        <div class="widget-content-right">
                                            <div class="widget-numbers text-white"><span><?= $patients ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                          <!--   <div class="d-xl-none d-lg-block col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content bg-premium-dark">
                                    <div class="widget-content-wrapper text-white">
                                        <div class="widget-content-left">
                                            <div class="widget-heading">Products Sold</div>
                                            <div class="widget-subheading">Revenue streams</div>
                                        </div>
                                        <div class="widget-content-right">
                                            <div class="widget-numbers text-warning"><span>$14M</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        </div>

 

                  

                         <div class="row">
                         <div class="col-md-12 col-lg-12">
                            <div class="mb-3 card">
                                <div class="card-header-tab card-header-tab-animation card-header">
                                    <div class="card-header-title">
                                        <i class="header-icon lnr-apartment icon-gradient bg-love-kiss"> </i>
                                        DashBoard
                                    </div>

                                </div>
                                <div class="card-body">
                                    

                               </div>
                           </div>
                       </div>
                   </div> <!--end row -->



          </div>  <!--end content -->

