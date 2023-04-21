<!-- Home page content -->
<?php
    $this->session          = session();
    $this->lang             = new \Config\MY_Lang();
    $this->request          = \Config\Services::request();
    $cats_separatorImage    = base_url('public/images/cats_separator.png');
    $cats_for_iconImage     = base_url('public/images/cats_for_icon.png');
?>

<div class="home_page">
    <div class="container-fluid">
        <div class="home_banner">
            <div class="row">
                <img src="<?php echo base_url('public/images/home-banner.jpg'); ?>" alt="Loading">
                <div class="banner_text">
                    <h1><?php echo lang('app.home_power_english'); ?></h1>
                    <p><?php echo lang('app.home_power_english_content1'); ?><span class="disp_block"><?php echo lang('app.home_power_english_content2'); ?></span><span class="disp_block"><?php echo lang('app.home_power_english_content3'); ?></span></p>
                    <button id="get_started" class="btn btn-started btn-sm"><?php echo lang('app.language_dashboard_book_cats'); ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- section 1 -->

    <section class="home_one">
        <div class="find_out_more">
            <div class="container">
                <?php $session = session();  if($session->markAsFlashdata('wrong_login')) { ?>
                    <div role="alert" class="alert alert-danger alert-dismissible">
                        <button aria-label="Close" data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span></button>
                        <?php echo $this->session->getFlashdata('wrong_login'); ?>
                    </div>
                    <?php $session = session(); $session->destroy();  ?>
                <?php } ?>
                <?php if ($session->markAsFlashdata('reset_failure')) { ?>
                    <div role="alert" class="alert alert-danger alert-dismissible">
                        <button aria-label="Close" data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span></button>
                        <?php echo $this->session->getFlashdata('reset_failure'); ?>
                    </div>
                <?php } ?>
                <?php if ($session->getFlashdata('reset_success')) {
                   ?>
                    <div role="alert" class="alert alert-success alert-dismissible">
                        <button aria-label="Close" data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span></button>
                        <?php echo $session->getFlashdata('reset_success'); ?>
                    </div>
                <?php } ?>
                <?php if ($session->getFlashdata('successmessage')) { ?>
                    <div role="alert" class="alert alert-success alert-dismissible">
                        <button aria-label="Close" data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span></button>
                        <?php echo $session->getFlashdata('successmessage'); ?>
                    </div>
                <?php } ?>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <h2 class="text-center"><?php echo lang('app.home_step_success'); ?></h2>
                        <p class="text_center"><?php echo lang('app.home_home_step_success_content1'); ?></p>
                    </div>
                    <div class="col-md-4 col-sm-12 col-xs-12 text-center">
                        <a class="benchmark_verify" href="<?php echo site_url("pages/cats_stepcheck"); ?>">
                            <div>
                                <img src="<?php echo base_url('public/images/benchmark-verify.png'); ?>" alt="Loading">
                                <p><strong><?php echo lang('app.home_StepCheck'); ?></strong>
                                    <span class="disp_block"><?php echo lang('app.home_placement_testing_services'); ?></span>
                                </p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 col-sm-12 col-xs-12 text-center">
                        <a class="about_course" href="<?php echo site_url("pages/cats_steps"); ?>">
                            <div>
                                <img src="<?php echo base_url('public/images/about-course.png'); ?>" alt="Loading">
                                <p><strong><?php echo lang('app.home_cats_steps'); ?></strong>
                                    <span class="disp_block"><?php echo lang('app.home_courses_tests'); ?></span>
                                </p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 col-sm-12 col-xs-12 text-center">
                        <a class="information_edu" href="<?php echo site_url("pages/cats_solution"); ?>">
                            <div>
                                <img src="<?php echo base_url('public/images/information-edu.png'); ?>" alt="Loading">
                                <p><strong><?php echo lang('app.home_information_educator'); ?></strong>
                                    <span class="disp_block"><?php echo lang('app.home_cats_solution'); ?></span>
                                </p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- section 3 -->

    <section class="home_three">
        <div class="container">
            <div class="cats_for">
                <div class="row">
                    <div class="col-sm-12 col-xs-12 text-center">
                        <img src="<?php echo base_url('public/images/cats_difference.png'); ?>" alt="Loading">
                        <p><?php echo lang('app.home_cats_difference_content'); ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-xs-12 text-center">
                        <h2><?php echo lang('app.home_who_cats'); ?>&#63;</h2>
                        <p><?php echo lang('app.home_who_cats_content'); ?></p>
                    </div>
                </div>
            </div>
            <div class="cats_for_sub_heading">
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <h3><?php echo lang('app.home_childrens_english'); ?></h3>
                        <p><?php echo lang('app.home_childrens_english_content'); ?></p>
                        <div class="separator_icon">
                            <img src="<?= $cats_separatorImage; ?>" alt="Loading">
                            <hr class="separator">
                        </div>
                    </div>
                    <div class="col-sm-12 col-xs-12 mt30">
                        <h3><?php echo lang('app.home_oldlearners'); ?></h3>
                        <p><?php echo lang('app.home_oldlearners_content'); ?></p>
                        <div class="separator_icon">
                            <img src="<?= $cats_separatorImage; ?>" alt="Loading">
                            <hr class="separator">
                        </div>
                    </div>
                    <div class="col-sm-12 col-xs-12 mt30">
                        <h3><?php echo lang('app.home_adult_workplace'); ?></h3>
                        <p><?php echo lang('app.home_adult_workplace_content'); ?></p>
                        <div class="separator_icon">
                            <img src="<?= $cats_separatorImage; ?>" alt="Loading">
                            <hr class="separator">
                        </div>
                    </div>
                    <div class="col-sm-12 col-xs-12 mt30">
                        <h3><?php echo lang('app.home_parents'); ?></h3>
                        <p><?php echo lang('app.home_parents_content'); ?></p>
                        <div class="separator_icon">
                            <img src="<?= $cats_separatorImage; ?>" alt="Loading">
                            <hr class="separator">
                        </div>
                    </div>
                    <div class="col-sm-12 col-xs-12 mt30">
                        <h3><?php echo lang('app.home_employers'); ?></h3>
                        <p><?php echo lang('app.home_employers_content'); ?></p>
                        <div class="separator_icon">
                            <img src="<?= $cats_separatorImage; ?>" alt="Loading">
                            <hr class="separator">
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <section class="home_sub_two">
        <div class="cats_difference">
            <div class="container">
                <div class="sub_heading">
                    <div class="row">
                        <div class="col-md-6 col-sm-12 col-xs-12">
                            <div class="cats_logo_icon">
                                <img src="<?= $cats_for_iconImage; ?>" alt="Loading">
                                <h3><?php echo lang('app.home_inclusive'); ?></h3>
                            </div>
                            <p><?php echo lang('app.home_inclusive_content'); ?></p>
                        </div>
                        <div class="col-md-6 col-sm-12 col-xs-12 mt20_xs">
                            <div class="cats_logo_icon">
                                <img src="<?= $cats_for_iconImage; ?>" alt="Loading">
                                <h3><?php echo lang('app.home_affordable'); ?></h3>
                            </div>
                            <p><?php echo lang('app.home_affordable_content'); ?></p>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-6 col-sm-12 col-xs-12 mt20">
                            <div class="cats_logo_icon">
                                <img src="<?= $cats_for_iconImage; ?>" alt="Loading">
                                <h3><?php echo lang('app.home_world_class'); ?></h3>
                            </div>
                            <p><?php echo lang('app.home_world_class_content1'); ?></p>
                            <p class="mt20"><?php echo lang('app.home_world_class_content2'); ?></p>
                        </div>
                        <div class="col-md-6 col-sm-12 col-xs-12 mt20">
                            <div class="cats_logo_icon">
                                <img src="<?= $cats_for_iconImage; ?>" alt="Loading">
                                <h3><?php echo lang('app.home_achievable'); ?></h3>
                            </div>
                            <p><?php echo lang('app.home_achievable_content'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    </section>
    <!-- section 2 -->
</div>