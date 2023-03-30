<!-- About course & Test Content -->
<?php
	use Config\MY_Lang;
	$this->myconfig = new \Config\MY_Lang(); 
?>
<div class="course_steps">
    <div class="container-fluid">
        <div class="home_banner about_course_test">
            <div class="row">
                <img src="<?php echo base_url('public/images/about-course-banner.jpg'); ?>">
                <div class="banner_text ">
                    <h1><?php echo lang('app.cats_steps_steps'); ?></h1>
                    <p><?php echo lang('app.cats_steps_courses_tests'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- section 1 -->

    <section class="course_steps_one">
        <div class="container">
            <div class="cats_package">
                <div class="row">
                    <div class="col-sm-12 col-xs-12 text_center">
                        <h2 class="text-center"><?php echo lang('app.cats_steps_complete_package'); ?></h2>
                        <p><?php echo lang('app.cats_steps_offeres'); ?></p>
                    </div>
                </div>
                <div class="cats_package course_details">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            <div class="cats_course_list">
                                <ul class="list-unstyled">
                                    <li><a class="scroll_to" href="#primary"><img src="<?php echo base_url('public/images/cats-list.png'); ?>" /><span><?php echo lang('app.cats_steps_primary'); ?></span></a></li>
                                    <li><a class="scroll_to" href="#core"><img src="<?php echo base_url('public/images/cats-list.png'); ?>" /><span><?php echo lang('app.cats_steps_core'); ?></span></a></li>
                                    <li><a class="scroll_to" href="#higher"><img src="<?php echo base_url('public/images/cats-list.png'); ?>" /><span><?php echo lang('app.cats_steps_higher'); ?></span></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-12 col-xs-12 text_center">
                            <p><?php echo lang('app.cats_steps_consisted'); ?></p>
                            <span class="educat_flowchart"><img src="<?php echo base_url('public/images/cats-ref-flowchart.png'); ?>"></span>
                            <p><?php echo lang('app.cats_steps_consisted_content'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- section 3 -->

    <section class="course_steps_three">
        <div class="container">
            <div class="cats_journey">
                <div class="row">
                    <div class="col-sm-12">
                        <h2 class="text-center"><?php echo lang('app.cats_steps_journey'); ?></h2>
                        <p><?php echo lang('app.cats_steps_journey_title'); ?></p>
                        <ul class="list-unstyled cats_journey_list cats_checkstep_list_number ">
                            <li>
                                <div class="bullet_points">
                                    <span class="number_count">1</span>
                                    <p><?php echo lang('app.cats_steps_journey_title_list1'); ?></p>
                                </div>
                            </li>
                            <li>
                                <div class="bullet_points">
                                    <span class="number_count">2</span>
                                    <p><?php echo lang('app.cats_steps_journey_title_list2'); ?></p>
                                </div>
                            </li>
                            <li>
                                <div class="bullet_points">
                                    <span class="number_count">3</span>
                                    <p> <?php echo lang('app.cats_steps_journey_title_list3'); ?></p>
                                </div>
                            </li>
                            <li>
                                <div class="bullet_points">
                                    <span class="number_count">4</span>
                                    <p><?php echo lang('app.cats_steps_journey_title_list4'); ?></p>
                                </div>
                            </li>
                            <li>
                                <div class="bullet_points">
                                    <span class="number_count">5</span>
                                    <p><?php echo lang('app.cats_steps_journey_title_list5'); ?></p>
                                </div>
                            </li>
                            <li>
                                <div class="bullet_points">
                                    <span class="number_count">6</span>
                                    <p><?php echo lang('app.cats_steps_journey_title_list6'); ?></p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- section 4 -->

    <section class="course_steps_four">
        <div class="container">
            <div class="cats_step_tests">
                <div class="row">
                    <div class="col-sm-12">
                        <h2 class="text-center"><?php echo lang('app.cats_steps_tests'); ?></h2>
                        <ul class="list-unstyled cats_checkstep_list  cats_step_tests_list">
                            <li><span><?php echo lang('app.cats_steps_tests_list1'); ?></span></li>
                            <li><span><?php echo lang('app.cats_steps_tests_list2'); ?></span></li>
                            <li><span><?php echo lang('app.cats_steps_tests_list3'); ?></span></li>
                            <li><span><?php echo lang('app.cats_steps_tests_list4'); ?></span></li>
                        </ul>
                    </div>
                </div>
                <div id="primary" class="cats_primary">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            <div class="cats_primary_content">
                                <div class="row">
                                    <div class="col-md-8 col-sm-12 col-xs-12">
                                        <div class="cats_primary_block">
                                            <div class="row">
                                                <div class="col-sm-12 col-xs-12 text-center">
                                                    <span class="primary_img"><img src="<?php echo base_url('public/images/cats-primary.png'); ?>" /></span>
                                                </div>
                                            </div>
                                            <form class="form-horizontal">
                                                <div class="form-group">
                                                    <label class="col-sm-6 col-xs-12 control-label"><?php echo lang('app.cats_steps_tests_primary_title1'); ?></label>
                                                    <div class="col-sm-6 col-xs-12">
                                                        <p class="form-control-static"><?php echo lang('app.cats_steps_tests_primary_title1_content'); ?></p>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-6 col-xs-12 control-label"><?php echo lang('app.cats_steps_tests_primary_title2'); ?></label>
                                                    <div class="col-sm-6 col-xs-12">
                                                        <p class="form-control-static"><?php echo lang('app.cats_steps_tests_primary_title2_content1'); ?><span class="disp_block"><?php echo lang('app.cats_steps_tests_primary_title2_content2'); ?></span></p>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-6 col-xs-12 control-label"><?php echo lang('app.cats_steps_tests_primary_title3'); ?></label>
                                                    <div class="col-sm-6 col-xs-12">
                                                        <p class="form-control-static"><?php echo lang('app.cats_steps_tests_primary_title3_content'); ?></p>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-6 col-xs-12 control-label"><?php echo lang('app.cats_steps_tests_primary_title4'); ?></label>
                                                    <div class="col-sm-6 col-xs-12">
                                                        <p class="form-control-static"><?php echo lang('app.cats_steps_tests_primary_title4_content'); ?></p>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12 col-xs-12 text-center">
                                        <div class="cats_primary_user_content">
                                            <div class="cats_primary_user">
                                                <img src="<?php echo base_url('public/images/cats-primary-img.png'); ?>" />
                                            </div>
                                            <label><?php echo lang('app.cats_steps_tests_primary_image_quote'); ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-xs-12">
                            <div class="cats_primary_txt mt40">
                                <p><?php echo lang('app.cats_steps_tests_primary_final_content1'); ?></p>
                                <p class="mt20"><?php echo lang('app.cats_steps_tests_primary_final_content2'); ?></p>
                                <p class="mt20"><?php echo lang('app.cats_steps_tests_primary_final_content3'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div id ="core" class="cats_primary cats_core">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            <div class="cats_primary_content">
                                <div class="row">
                                    <div class="col-md-8 col-sm-12 col-xs-12">
                                        <div class="cats_primary_block cats_core_block">
                                            <div class="row">
                                                <div class="col-sm-12 col-xs-12 text-center">
                                                    <span class="primary_img"><img src="<?php echo base_url('public/images/cats-core.png'); ?>" /></span>
                                                </div>
                                            </div>
                                            <form class="form-horizontal">
                                                <div class="form-group">
                                                    <label class="col-sm-6 col-xs-12 control-label"><?php echo lang('app.cats_steps_tests_core_title1'); ?></label>
                                                    <div class="col-sm-6 col-xs-12">
                                                        <p class="form-control-static"><?php echo lang('app.cats_steps_tests_core_title1_content'); ?></p>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-6 col-xs-12 control-label"><?php echo lang('app.cats_steps_tests_core_title2'); ?></label>
                                                    <div class="col-sm-6 col-xs-12">
                                                        <p class="form-control-static"><?php echo lang('app.cats_steps_tests_core_title2_content1'); ?><span class="disp_block"><?php echo lang('app.cats_steps_tests_core_title2_content2'); ?></span><span class="disp_block"><?php echo lang('app.cats_steps_tests_core_title2_content3'); ?></span></p>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-6 col-xs-12 control-label"><?php echo lang('app.cats_steps_tests_core_title3'); ?></label>
                                                    <div class="col-sm-6 col-xs-12">
                                                        <p class="form-control-static"><?php echo lang('app.cats_steps_tests_core_title3_content'); ?></p>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-6 col-xs-12 control-label"><?php echo lang('app.cats_steps_tests_core_title4'); ?></label>
                                                    <div class="col-sm-6 col-xs-12">
                                                        <p class="form-control-static"><?php echo lang('app.cats_steps_tests_core_title4_content'); ?></p>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-6 col-xs-12 control-label"><?php echo lang('app.cats_steps_tests_core_title5'); ?></label>
                                                    <div class="col-sm-6 col-xs-12">
                                                        <p class="form-control-static"><?php echo lang('app.cats_steps_tests_core_title5_content'); ?></p>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12 col-xs-12 text-center">
                                        <div class="cats_primary_user_content">
                                            <div class="cats_primary_user">
                                                <img src="<?php echo base_url('public/images/cats-core-img.png'); ?>" />
                                            </div>
                                            <label><?php echo lang('app.cats_steps_tests_core_image_quote'); ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-xs-12">
                            <div class="cats_primary_txt mt40">
                                <p><?php echo lang('app.cats_steps_tests_core_final_content1'); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="higher" class="cats_primary cats_higher">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="cats_primary_content">
                            <div class="row">
                                <div class="col-md-8 col-sm-12 col-xs-12">
                                    <div class="cats_primary_block cats_higher_block">
                                        <div class="row">
                                            <div class="col-sm-12 col-xs-12 text-center">
                                                <span class="primary_img"><img src="<?php echo base_url('public/images/cats-higher.png'); ?>" /></span>
                                            </div>
                                        </div>
                                        <form class="form-horizontal">
                                            <div class="form-group">
                                                <label class="col-sm-6 col-xs-12 control-label"><?php echo lang('app.cats_steps_tests_higher_title1'); ?></label>
                                                <div class="col-sm-6 col-xs-12">
                                                    <p class="form-control-static"><?php echo lang('app.cats_steps_tests_higher_title1_content'); ?></p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-6 col-xs-12 control-label"><?php echo lang('app.cats_steps_tests_higher_title2'); ?></label>
                                                <div class="col-sm-6 col-xs-12">
                                                    <p class="form-control-static"><?php echo lang('app.cats_steps_tests_higher_title2_content1'); ?></p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-6 col-xs-12 control-label"><?php echo lang('app.cats_steps_tests_higher_title3'); ?></label>
                                                <div class="col-sm-6 col-xs-12">
                                                    <p class="form-control-static"><?php echo lang('app.cats_steps_tests_higher_title3_content'); ?></p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-6 col-xs-12 control-label"><?php echo lang('app.cats_steps_tests_higher_title4'); ?></label>
                                                <div class="col-sm-6 col-xs-12">
                                                    <p class="form-control-static"><?php echo lang('app.cats_steps_tests_higher_title4_content'); ?></p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-6 col-xs-12 control-label"><?php echo lang('app.cats_steps_tests_higher_title5'); ?></label>
                                                <div class="col-sm-6 col-xs-12">
                                                    <p class="form-control-static"><?php echo lang('app.cats_steps_tests_higher_title5_content'); ?></p>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-12 col-xs-12 text-center">
                                    <div class="cats_primary_user_content">
                                        <div class="cats_primary_user">
                                            <img src="<?php echo base_url('public/images/cats-higher-img.png'); ?>" />
                                        </div>
                                        <label><?php echo lang('app.cats_steps_tests_higher_image_quote'); ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-xs-12">
                        <div class="cats_primary_txt mt40">
                            <p><?php echo lang('app.cats_steps_tests_higher_final_content1'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
            <div class="col-sm-6 col-xs-6 text-center mt40">                        
                    <a href="<?php echo site_url("/pages/cats_step_expert_say"); ?>" class="btn btn-sm btn-continue btn-pink btn-ellipsis"><?php echo lang('app.home_cats_steps_experts_say'); ?></a>
                </div>
                <div class="col-sm-6 col-xs-6 text-center mt40">                        
                    <a href="<?php echo site_url("/pages/cats_step_learner_say"); ?>" class="btn btn-sm btn-continue btn-pink btn-ellipsis"><?php echo lang('app.home_cats_steps_learners_say'); ?></a>
                </div>
            </div>
        </div>
</div>
</section>
</div>

<script type = "text/javascript">
    $(document).on('click', 'a[href^="#"]', function (e) {
        // target element id
        var id = $(this).attr('href');

        // target element
        var $id = $(id);
        if ($id.length === 0) {
            return;
        }

        // prevent standard hash navigation (avoid blinking in IE)
        e.preventDefault();

        // top position relative to the document
        var pos = $id.offset().top;

        var fixed_header = $('.fixed_header').height();

        var move_to = pos - fixed_header;

        move_to = move_to - 30;

        // animated top scrolling
        $('body, html').animate({scrollTop: move_to});
    });

</script>


<script>

    $(document).ready(function () {
        txtMiddle();
    });
    $(window).load(function () {
        txtMiddle();
    });
    $(window).resize(function () {
        txtMiddle();
    });
    
    
    function txtMiddle() { 
        if ($(window).width() > 991) {
            $('.cats_primary_block').each(function () {
                $(this).parents('.cats_primary_content').find('.cats_primary_user_content').height($(this)[0].clientHeight + 4);
                var imgHeight = $(this).parents('.cats_primary_content').find('.cats_primary_user')[0].clientHeight;
                var divHeight = $(this).parents('.cats_primary_content').find('.cats_primary_user_content').height();
                $(this).parents('.cats_primary_content').find('.cats_primary_user_content label').height(divHeight - imgHeight);
            })
        }
        else {
            $('.cats_primary_block').each(function () {
                $(this).parents('.cats_primary_content').find('.cats_primary_user_content').height('auto');

                $(this).parents('.cats_primary_content').find('.cats_primary_user_content label').height('auto');
            })
        }
    }
</script>