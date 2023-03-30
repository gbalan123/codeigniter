<?php 
$router = service('router');
$method = $router->methodName();
$this->session = session();
$learnertype = $this->session->get('learnertype');
$learnerProdType = $this->session->get('learnerprodtype');
?>
<!--For malaysian view screen changing starts-->
<?php if(isset($learnertype)):?>
<?php if($learnertype == 'under13'): ?>
<div class="main_tab">

    <div class="no_pointer_events overflow_x">
        <ul class="nav nav-tabs <?php echo ($learnerProdType!='cats_core')?'child-font':''; ?>">

            <li class="<?php echo ($method == 'bookingscreen1' ) || ($method == 'bookingscreen1a') ? 'active' : ''; ?>">
                <a><?php echo lang('app.language_site_booking_tabs_m1new'); ?></a></li>

            <li
                class="<?php echo ($method == 'bookingscreen3' || $method == 'bookingscreen3a' || $method == 'bookingscreen2a') ? 'active' : ''; ?>">
                <a><?php echo lang('app.language_site_booking_tabs_m3'); ?></a></li>

            <li class="<?php echo ($method == 'bookingscreen4' || $method == 'bookingscreen2p' ) ? 'active' : ''; ?>">
                <a><?php echo lang('app.language_site_booking_tabs_m4'); ?></a></li>

        </ul>
    </div>
</div>
<?php elseif($learnertype == 'over13'): ?>
<div class="main_tab">
    <div class="no_pointer_events overflow_x">
        <ul class="nav nav-tabs">

            <li class=<?php echo ($method == 'bookingscreen1' ) || ($method == 'bookingscreen1a') ? 'active' : ''; ?>>
                <a><?php echo lang('app.language_site_booking_tabs_m1new'); ?></a></li>

            <li class=<?php echo ($method == 'bookingscreen2' ) ? 'active' : ''; ?>>
                <a><?php echo lang('app.language_site_booking_tabs_m2'); ?></a></li>

            <?php if(isset($organization_data) && $organization_data['type_of_token'] != ''): ?>

            <!--WP-1060 starts-->
            <li class=<?php echo ($method == 'bookingscreen3' || $method == 'bookingscreen3a') ? 'active' : ''; ?>><a>
                    <?php
                        if(($organization_data['type_of_token'] == "cats_core") ||($organization_data['type_of_token'] == "cats_higher") || ($organization_data['type_of_token'] == "cats_core_or_higher")||($organization_data['type_of_token'] == "catslevel")){
                            echo lang('app.language_site_booking_tabs_m3');
                        } elseif (($organization_data['type_of_token'] == "speaking_test")){
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
                        if (($organization_data['type_of_token'] == "cats_core") || ($organization_data['type_of_token'] == "cats_higher") || ($organization_data['type_of_token'] == "cats_core_or_higher") || ($organization_data['type_of_token'] == "catslevel")) {
                            echo lang('app.language_site_booking_tabs_m4');
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
            <li class=<?php echo ($method == 'bookingscreen1' ) || ($method == 'bookingscreen1a') ? 'active' : ''; ?>>
                <a><?php echo lang('app.language_site_booking_tabs_m1new'); ?></a></li>
            <li class=<?php echo ($method == 'bookingscreen2' ) ? 'active' : ''; ?>>
                <a><?php echo lang('app.language_site_booking_tabs_m2'); ?></a></li>

            <li class=<?php echo ($method == 'bookingscreen3' ) ? 'active' : ''; ?>>
                <a><?php echo lang('app.language_site_booking_tabs_m3'); ?></a></li>
            <li><a><?php echo lang('app.language_site_booking_tabs_m4'); ?> </a></li>
        </ul>
    </div>
</div>
<?php endif; ?>
<!--For malaysina viewers screen changing ends-->