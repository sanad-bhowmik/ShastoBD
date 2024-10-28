<?php

$today = date("Y-m-d");
$doctors = get_count_by_sql("select count(*) from tbl_doctor where Active=1 ");
$patients = get_count_by_sql("select count(*) from tbl_patient where 1=1 ");
$appointment = get_count_by_sql("select count(*) from appointmentview where AppointmentDate='$today' ");
$appointment2 = get_count_by_sql("select count(*) from appointmentview");
$medicine_sale = get_value_by_sql("select sum(InQty) from stock_in");
$total_payable_price = get_payable_price_sum();
// Calculate heights as percentages based on the maximum value for proper scaling
$max_value = max($doctors, $patients, $appointments_today, $total_appointments, $medicine_sale, $total_payable_price);
$doctor_height = ($doctors / $max_value) * 100;
$patient_height = ($patients / $max_value) * 100;
$appointment_height = ($appointments_today / $max_value) * 100;
$total_appointment_height = ($total_appointments / $max_value) * 100;
$medicine_sale_height = ($medicine_sale / $max_value) * 100;
$total_payable_price_height = ($total_payable_price / $max_value) * 100;

?>

<!--  Start content-->
<div class="app-main__inner">


    <div id="root">
        <div class="container pt-5">
            <div class="row align-items-stretch">
                <div class="c-dashboardInfo col-lg-4 col-md-6">
                    <div class="wrap">
                        <h4 class="heading heading5 hind-font medium-font-weight c-dashboardInfo__title">Doctors<svg
                                class="MuiSvgIcon-root-19" focusable="false" viewBox="0 0 24 24" aria-hidden="true" role="presentation">
                                <path fill="none" d="M0 0h24v24H0z"></path>
                                <path
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z">
                                </path>
                            </svg></h4><span class="hind-font caption-12 c-dashboardInfo__count"><?= $doctors ?></span>
                        <span
                            class="hind-font caption-12 c-dashboardInfo__subInfo">Active Doctors</span>
                    </div>
                </div>
                <div class="c-dashboardInfo col-lg-4 col-md-6">
                    <div class="wrap">
                        <h4 class="heading heading5 hind-font medium-font-weight c-dashboardInfo__title">Today <?= $today ?><svg
                                class="MuiSvgIcon-root-19" focusable="false" viewBox="0 0 24 24" aria-hidden="true" role="presentation">
                                <path fill="none" d="M0 0h24v24H0z"></path>
                                <path
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z">
                                </path>
                            </svg></h4><span class="hind-font caption-12 c-dashboardInfo__count"><?= $appointment ?></span><span
                            class="hind-font caption-12 c-dashboardInfo__subInfo">Total Appointment Today</span>
                    </div>
                </div>
                <div class="c-dashboardInfo col-lg-4 col-md-6">
                    <div class="wrap">
                        <h4 class="heading heading5 hind-font medium-font-weight c-dashboardInfo__title">Paitents<svg
                                class="MuiSvgIcon-root-19" focusable="false" viewBox="0 0 24 24" aria-hidden="true" role="presentation">
                                <path fill="none" d="M0 0h24v24H0z"></path>
                                <path
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z">
                                </path>
                            </svg></h4><span class="hind-font caption-12 c-dashboardInfo__count"><?= $patients ?></span>
                        <span
                            class="hind-font caption-12 c-dashboardInfo__subInfo"><?= $today ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-xl-4">
            <div class="card mb-3 widget-content bg-midnight-bloom">
                <div class="widget-content-wrapper text-white">
                    <div class="widget-content-left">
                        <div class="widget-heading">Stock</div>
                        <div class="widget-subheading">Medicine Stock</div>
                    </div>
                    <div class="widget-content-right">
                        <div class="widget-numbers text-white"><span><?= $medicine_sale ?></span> ৳</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card mb-3 widget-content bg-arielle-smile">
                <div class="widget-content-wrapper text-white">
                    <div class="widget-content-left">
                        <div class="widget-heading">Sale <?= $today ?></div>
                        <div class="widget-subheading">Medicine Sale</div>
                    </div>
                    <div class="widget-content-right">
                        <div class="widget-numbers text-white"><span><?= $total_payable_price ?></span> ৳</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card mb-3 widget-content bg-grow-early2">
                <div class="widget-content-wrapper text-white">
                    <div class="widget-content-left">
                        <div class="widget-heading">Appointment</div>
                        <div class="widget-subheading">Total Appointment</div>
                    </div>
                    <div class="widget-content-right">
                        <div class="widget-numbers text-white"><span><?= $appointment2 ?></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div>
        <ul class="chart">
            <li>
                <span style="height: <?= $doctor_height ?>%" title="Doctors: <?= $doctors ?>"></span>
            </li>
            <li>
                <span style="height: <?= $patient_height ?>%" title="Patients: <?= $patients ?>"></span>
            </li>
            <li>
                <span style="height: <?= $appointment_height ?>%" title="Appointments Today: <?= $appointments_today ?>"></span>
            </li>
            <li>
                <span style="height: <?= $total_appointment_height ?>%" title="Total Appointments: <?= $total_appointments ?>"></span>
            </li>
            <li>
                <span style="height: <?= $medicine_sale_height ?>%" title="Medicine Sale: <?= $medicine_sale ?>"></span>
            </li>
            <li>
                <span style="height: <?= $total_payable_price_height ?>%" title="Total Payable Price: <?= $total_payable_price ?>"></span>
            </li>
        </ul>
    </div>

</div>
<style>
    .chart {
        display: table;
        table-layout: fixed;
        width: 60%;
        max-width: 700px;
        height: 200px;
        margin: 0 auto;
        background-image: linear-gradient(bottom, rgba(0, 0, 0, 0.1) 2%, transparent 2%);
        background-size: 100% 50px;
        background-position: left top;
    }

    .chart li {
        position: relative;
        display: table-cell;
        vertical-align: bottom;
        height: 200px;
    }

    .chart span {
        margin: 0 1em;
        display: block;
        background: rgb(3 96 144 / 87%);
        animation: draw 1s ease-in-out;
    }

    .chart span:before {
        position: absolute;
        left: 0;
        right: 0;
        top: 100%;
        padding: 5px 1em 0;
        display: block;
        text-align: center;
        content: attr(title);
        word-wrap: break-word;
    }

    @keyframes draw {
        0% {
            height: 0;
        }
    }

    .c-dashboardInfo {
        margin-bottom: 15px;
    }

    .c-dashboardInfo .wrap {
        background: #ffffff;
        box-shadow: 2px 10px 20px rgba(0, 0, 0, 0.1);
        border-radius: 7px;
        text-align: center;
        position: relative;
        overflow: hidden;
        padding: 40px 25px 20px;
        height: 100%;
    }

    .c-dashboardInfo__title,
    .c-dashboardInfo__subInfo {
        color: #6c6c6c;
        font-size: 1.18em;
    }

    .c-dashboardInfo span {
        display: block;
    }

    .c-dashboardInfo__count {
        font-weight: 600;
        font-size: 2.5em;
        line-height: 64px;
        color: #323c43;
    }

    .c-dashboardInfo .wrap:after {
        display: block;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 10px;
        content: "";
    }

    .c-dashboardInfo:nth-child(1) .wrap:after {
        background: linear-gradient(82.59deg, #00c48c 0%, #00a173 100%);
    }

    .c-dashboardInfo:nth-child(2) .wrap:after {
        background: linear-gradient(81.67deg, #0084f4 0%, #1a4da2 100%);
    }

    .c-dashboardInfo:nth-child(3) .wrap:after {
        background: linear-gradient(69.83deg, #0084f4 0%, #00c48c 100%);
    }

    .c-dashboardInfo:nth-child(4) .wrap:after {
        background: linear-gradient(81.67deg, #ff647c 0%, #1f5dc5 100%);
    }

    .c-dashboardInfo__title svg {
        color: #d7d7d7;
        margin-left: 5px;
    }

    .MuiSvgIcon-root-19 {
        fill: currentColor;
        width: 1em;
        height: 1em;
        display: inline-block;
        font-size: 24px;
        transition: fill 200ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
        user-select: none;
        flex-shrink: 0;
    }
</style>