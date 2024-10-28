<?php

$role_id = $_SESSION['role_id'];
$menus = get_all_menus_by_role_id($role_id);
$company_id = $_SESSION['company_id'];

if ($role_id == 4) {

    $newapps = 0;
} else {

    $newapps = 0;
}
// Get the current page URL
$current_page = basename($_SERVER['PHP_SELF']);



?>


<ul class="vertical-nav-menu">
    <li class="app-sidebar__heading">Dashboards</li>
    <li>
        <a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'mm-active' : '' ?>">
            <i class="metismenu-icon pe-7s-home"></i>
            HOME
        </a>
    </li>
    <li class="app-sidebar__heading">Administrator</li>
    <?php
    $output = '';


    foreach ($menus as $menu) {
        // Check if the current page is under this menu to keep it active
        $is_active = '';
        $menu_id = $menu['menu_id'];
        $sub_menus = get_all_sub_menus_by_menu_id_role_id($menu_id, $role_id);

        foreach ($sub_menus as $sub_menu) {
            if ($current_page == basename($sub_menu['page_url'])) {
                $is_active = 'mm-active';
                break;
            }
        }

        $output .= '<li class="' . $is_active . '">
                                    <a href="#">
                                        <i class="metismenu-icon ' . $menu['icon_class'] . '">
                                        </i>' . $menu['menu_name'];


        if ($menu['notification'] == 1) {

            $output .=    $newapps > 0 ? ' <span style="color:red"> (' . $newapps . ')</span>' : '';
        }

        $output .=    ' <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                                    </a>
                                     <ul>';
        $menu_id = $menu['menu_id'];
        $sub_menus = get_all_sub_menus_by_menu_id_role_id($menu_id, $role_id);



        foreach ($sub_menus as  $sub_menu) {


            $output .= ' <li>
                                             <a href="' . $sub_menu['page_url'] . '" class="' . ($current_page == basename($sub_menu['page_url']) ? 'mm-active' : '') . '">
                        <i class="metismenu-icon"></i>' . $sub_menu['sub_menu_name'];

            if ($sub_menu['notification'] == 1 && $newapps > 0) {

                $output .=    '<img width=50 height=30 src="themefiles/assets/images/new.gif">';
            }



            $output .= '    </a>
                                        </li>';
        }





        $output .= ' </ul>


                                </li>';
    }

    echo $output;

    ?>






    <!--   <li class="">
                                    <a href="#">
                                        <i class="metismenu-icon pe-7s-car"></i>
                                        Viewing
                                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                                    </a>
                                    <ul>
                                      <li>
                                            <a href="games.php">
                                                <i class="metismenu-icon">
                                                </i>Games
                                            </a>
                                        </li>
                                         <li>
                                            <a href="summary_report.php">
                                                <i class="metismenu-icon">
                                                </i>Summary Report
                                            </a>
                                        </li> 

                                    </ul>
                                </li> -->
    <!--  <li  >
                                    <a href="tables-regular.html">
                                        <i class="metismenu-icon pe-7s-display2"></i>
                                        Tables
                                    </a>
                                </li>
                                <li class="app-sidebar__heading">Widgets</li>
                                <li>
                                    <a href="dashboard-boxes.html">
                                        <i class="metismenu-icon pe-7s-display2"></i>
                                        Dashboard Boxes
                                    </a>
                                </li>
                                <li class="app-sidebar__heading">Forms</li>
                                <li>
                                    <a href="forms-controls.html">
                                        <i class="metismenu-icon pe-7s-mouse">
                                        </i>Forms Controls
                                    </a>
                                </li>
                                <li>
                                    <a href="forms-layouts.html">
                                        <i class="metismenu-icon pe-7s-eyedropper">
                                        </i>Forms Layouts
                                    </a>
                                </li>
                                <li>
                                    <a href="forms-validation.html">
                                        <i class="metismenu-icon pe-7s-pendrive">
                                        </i>Forms Validation
                                    </a>
                                </li>
                                <li class="app-sidebar__heading">Charts</li>
                                <li>
                                    <a href="charts-chartjs.html">
                                        <i class="metismenu-icon pe-7s-graph2">
                                        </i>ChartJS
                                    </a>
                                </li>
                                <li class="app-sidebar__heading">PRO Version</li>
                                <li>
                                    <a href="https://dashboardpack.com/theme-details/architectui-dashboard-html-pro/" target="_blank">
                                        <i class="metismenu-icon pe-7s-graph2">
                                        </i>
                                        Upgrade to PRO
                                    </a>
                                </li> -->
</ul>