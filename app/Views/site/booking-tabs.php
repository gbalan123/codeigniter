<?php 
$router                                 = service('router');
$method                                 = $router->methodName();
$this->session                          = session();
$learnertype                            = $this->session->get('learnertype');
$learnerProdType                        = $this->session->get('learnerprodtype');
$cats_coreDatas                         = "cats_core";
$bookingscreen1Datas                    = "bookingscreen1";
$bookingscreen3Datas                    = "bookingscreen3";
$type_of_tokenDatas                     = "type_of_token";
$language_site_booking_tabs_m1newLang   = lang('app.language_site_booking_tabs_m1new');
$language_site_booking_tabs_m3Lang      = lang('app.language_site_booking_tabs_m3');
$language_site_booking_tabs_m4Lang      = lang('app.language_site_booking_tabs_m4');
?>
<!--For malaysian view screen changing starts-->
<?php if(isset($learnertype)):?>
<?php if($learnertype == 'under13'): ?>
<div class="main_tab">

    <div class="no_pointer_events overflow_x">
        <ul class="nav nav-tabs <?php echo ($learnerProdType!=$cats_coreDatas)?'child-font':''; ?>">

            <li class="<?php echo ($method == $bookingscreen1Datas ) || ($method == 'bookingscreen1a') ? 'active' : ''; ?>">
                <a><?= $language_site_booking_tabs_m1newLang; ?></a></li>

            <li
                class="<?php echo ($method == $bookingscreen3Datas || $method == 'bookingscreen3a' || $method == 'bookingscreen2a') ? 'active' : ''; ?>">
                <a><?= $language_site_booking_tabs_m3Lang; ?></a></li>

            <li class="<?php echo ($method == 'bookingscreen4' || $method == 'bookingscreen2p' ) ? 'active' : ''; ?>">
                <a><?= $language_site_booking_tabs_m4Lang; ?></a></li>

        </ul>
    </div>
</div>
<?php elseif($learnertype == 'over13'): ?>
<div class="main_tab">
    <div class="no_pointer_events overflow_x">
        <ul class="nav nav-tabs">

            <li class=<?php echo ($method == $bookingscreen1Datas ) || ($method == 'bookingscreen1a') ? 'active' : ''; ?>>
                <a><?= $language_site_booking_tabs_m1newLang; ?></a></li>

            <li class=<?php echo ($method == 'bookingscreen2' ) ? 'active' : ''; ?>>
                <a><?php echo lang('app.language_site_booking_tabs_m2'); ?></a></li>

            <?php if(isset($organization_data) && $organization_data[$type_of_tokenDatas] != ''): ?>

            <!--WP-1060 starts-->
            <li class=<?php echo ($method == $bookingscreen3Datas || $method == 'bookingscreen3a') ? 'active' : ''; ?>><a>
                    <?php
                        if(($organization_data[$type_of_tokenDatas] == $cats_coreDatas) ||($organization_data[$type_of_tokenDatas] == "cats_higher") || ($organization_data[$type_of_tokenDatas] == "cats_core_or_higher")||($organization_data[$type_of_tokenDatas] == "catslevel")){
                            echo $language_site_booking_tabs_m3Lang;
                        } elseif ($organization_data[$type_of_tokenDatas] == "speaking_test") {
                            echo lang('app.language_site_booking_tabs_m3s');
                        } else {
                            echo lang('app.language_site_booking_tabs_m3b');
                        }
                    ?>
                </a>
                <!--WP-1060 ends-->
            </li>

            <!--WP-1060 starts-->
            <li class=<?php echo ($method == 'bookingscreen4' ) ? 'active' : ''; ?>><a>
                    <?php
                        if (($organization_data[$type_of_tokenDatas] == $cats_coreDatas) || ($organization_data[$type_of_tokenDatas] == "cats_higher") || ($organization_data[$type_of_tokenDatas] == "cats_core_or_higher") || ($organization_data[$type_of_tokenDatas] == "catslevel")) {
                            echo $language_site_booking_tabs_m4Lang;
                        } else {
                            echo lang('app.language_site_booking_tabs_m4b');
                        }
                    ?>
                </a>
            </li>
            <!--WP-1060 ends-->

            <?php endif; ?>
        </ul>
    </div>
</div>
<?php endif;?>
<?php else: ?>
<div class="main_tab">
    <div class="no_pointer_events overflow_x">
        <ul class="nav nav-tabs">
            <li class=<?php echo ($method == $bookingscreen1Datas ) || ($method == 'bookingscreen1a') ? 'active' : ''; ?>>
                <a><?= $language_site_booking_tabs_m1newLang; ?></a></li>
            <li class=<?php echo ($method == 'bookingscreen2' ) ? 'active' : ''; ?>>
                <a><?php echo lang('app.language_site_booking_tabs_m2'); ?></a></li>

            <li class=<?php echo ($method == $bookingscreen3Datas ) ? 'active' : ''; ?>>
                <a><?= $language_site_booking_tabs_m3Lang; ?></a></li>
            <li><a><?= $language_site_booking_tabs_m4Lang; ?></a></li>
        </ul>
    </div>
</div>
<?php endif; ?>
<!--For malaysina viewers screen changing ends-->