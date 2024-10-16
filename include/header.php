<?php
require_once("include/initialize.php");
if ($_SESSION['logged_in'] != true) {
    redirect_to("index.php");
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
    <meta name="description" content="This is an example dashboard created using build-in elements and components.">
    <meta name="msapplication-tap-highlight" content="no">
    <!--
    =========================================================
    * ArchitectUI HTML Theme Dashboard - v1.0.0
    =========================================================
    * Product Page: https://dashboardpack.com
    * Copyright 2019 DashboardPack (https://dashboardpack.com)
    * Licensed under MIT (https://github.com/DashboardPack/architectui-html-theme-free/blob/master/LICENSE)
    =========================================================
    * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
    -->
    <link href="themefiles/main.css" rel="stylesheet">
</head>
<link href="plugin/datatables.min.css" rel="stylesheet">
<link href="css/dasstyle.css" rel="stylesheet">
</head>
<link href="plugin/dist/jquery-confirm.min.css" rel="stylesheet">
<script type="text/javascript" src="themefiles/assets/scripts/main.js"></script>
<script type="text/javascript" src="chart/canvasjs.min.js"></script>
<script src="plugin/jquery3.4.1.js"></script>
<!-- <script src="http://malsup.github.com/jquery.form.js"></script>  -->
<script src="plugin/jquery.form.min.js"></script>
<script src="plugin/datatables.min.js"></script>
<script src="plugin/bootstrap4min.js"></script>
<script src="plugin/dist/jquery-confirm.min.js"></script>
<link href="plugin/toastr.css" rel="stylesheet">
<script src="plugin/toastr.min.js"></script>
<script src="plugin/sum.js"></script>


<body>
    <div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
        <div class="app-header header-shadow bg-grow-early  header-text-dark">
            <div class="app-header__logo">
                <div class="logo-src"></div>
                <div class="header__pane ml-auto">
                    <div>
                        <button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar">
                            <span class="hamburger-box">
                                <span class="hamburger-inner"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="app-header__mobile-menu">
                <div>
                    <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                        <span class="hamburger-box">
                            <span class="hamburger-inner"></span>
                        </span>
                    </button>
                </div>
            </div>
            <div class="app-header__menu">
                <span>
                    <button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                        <span class="btn-icon-wrapper">
                            <i class="fa fa-ellipsis-v fa-w-6"></i>
                        </span>
                    </button>
                </span>
            </div>
            <div class="app-header__content">
                <div class="app-header-left">
                    <div class="search-wrapper">
                        <div class="input-holder">
                            <input type="text" class="search-input" placeholder="Type to search">
                            <button class="search-icon"><span></span></button>
                        </div>
                        <button class="close"></button>
                    </div>
                </div>
                <div class="app-header-right">
                    <div class="header-btn-lg pr-0">
                        <div class="widget-content p-0">
                            <div class="widget-content-wrapper">
                                <div class="widget-content-left">


                                </div>
                                <div class="widget-content-left  ml-3 header-user-info">
                                    <div class="widget-heading">
                                        <?php
                                        if (isset($_SESSION['name'])) {
                                            echo $_SESSION['name'];
                                        } else {

                                            echo "Admin";
                                        }

                                        ?>
                                    </div>

                                </div>
                                <div class="widget-content-right header-user-info ml-3">
                                    <a href="index.php?logout=true" class="btn-shadow p-1 btn btn-primary btn-sm show-toastr-example">
                                        <i class="fa text-white pe-7s-power pr-1 pl-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="app-main">
            <div class="app-sidebar sidebar-shadow bg-happy-green sidebar-text-light">
                <div class="app-header__logo">
                    <div class="logo-src"></div>
                    <div class="header__pane ml-auto">
                        <div>
                            <button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar">
                                <span class="hamburger-box">
                                    <span class="hamburger-inner"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="app-header__mobile-menu">
                    <div>
                        <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                            <span class="hamburger-box">
                                <span class="hamburger-inner"></span>
                            </span>
                        </button>
                    </div>
                </div>
                <div class="app-header__menu">
                    <span>
                        <button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                            <span class="btn-icon-wrapper">
                                <i class="fa fa-ellipsis-v fa-w-6"></i>
                            </span>
                        </button>
                    </span>
                </div>


                <div class="scrollbar-sidebar">
                    <div class="app-sidebar__inner">
                        <?php include("nav.php"); ?>
                    </div>
                </div>
            </div>
            <div class="app-main__outer">

                <style>
                    .scrollbar-sidebar {
                        max-height: 600px;

                        overflow-y: auto;
                        overflow-x: hidden;
                        padding: 0;
                    }

                    * {
                        scrollbar-width: thin;
                        /* scrollbar-color: rgb(23 120 180) rgb(3 35 40); */
                        scrollbar-color: rgb(3 35 40) rgb(23 120 180);
                    }

                    .app-sidebar__inner {
                        height: auto;
                        padding: 0;
                    }
                </style>