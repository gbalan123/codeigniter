<?php
	use Config\MY_Lang;
	$this->myconfig                             = new \Config\MY_Lang();
    $icon_content_splitterImage                 = base_url('public/images/icon-content-splitter.png');
    $language_site_main_stepcheck_learn_moreLang= lang('app.language_site_main_stepcheck_learn_more');
    $language_site_main_stepcheck_stepcheckLang = lang('app.language_site_main_stepcheck_stepcheck');
?>

<!-- CATs CheckStep Content Pgage -->
<div class="checkstep_home_page">
    <div class="container-fluid">
        <div class="home_banner about_course_test info_educat">
            <div class="row">
                <img src="<?php echo base_url('public/images/cats-stepcheck-home.jpg'); ?>" alt="cats-stepcheck-home">
                <div class="banner_text ">
                    <h1> <?php echo lang('app.language_site_main_stepcheck_cats'); ?></h1>
                    <p><?php echo lang('app.language_site_main_stepcheck_transformation_testing'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- section 1 -->

    <section class="cat_stepcheck_one">
        <div class="container">
            <div class="cats_home_checkstep">
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <h2 class="text-center"><?php echo lang('app.language_site_main_stepcheck_cats'); ?></h2>
                        <p class="text_center"><?php echo lang('app.language_site_main_stepcheck_interconnected'); ?></p>
                        <p class="text_center"><?php echo lang('app.language_site_main_stepcheck_demand'); ?></p>
                        <p class="text_center"><?php echo lang('app.language_site_main_stepcheck_world_class'); ?></p>
                        <p class="text_center margin_none"><?php echo lang('app.language_site_main_stepcheck_decision_maker'); ?></p>
                    </div>

                </div>
                </section>

                <!-- section 2 -->

                <section class="cat_stepcheck_two">
                    <div class="container">
                        <div class="cats_checkstep_cards">
                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    <h2 class="text-center"><?php echo lang('app.language_site_main_stepcheck_difference'); ?></h2>
                                </div>
                                <div class="col-sm-4 col-xs-12">
                                    <div class="checkstep_subcard tranquil">
                                        <div class="subcard_title text-center">
                                            <h3><?php echo lang('app.language_site_main_stepcheck_powerful'); ?></h3>
                                        </div>
                                        <div class="subcard_content">
                                            <p><?php echo lang('app.language_site_main_stepcheck_choose_skills'); ?></p>
                                            <div class="separator_icon">
                                                <img src="<?= $icon_content_splitterImage; ?>" alt="icon-content-splitter">
                                                <hr class="separator">
                                            </div>
                                            <p><?php echo lang('app.language_site_main_stepcheck_innovative'); ?></p>
                                            <div class="separator_icon">
                                                <img src="<?= $icon_content_splitterImage; ?>" alt="icon-content-splitter">
                                                <hr class="separator">
                                            </div>
                                            <p><?php echo lang('app.language_site_main_stepcheck_level_ability'); ?></p>
                                            <div class="separator_icon">
                                                <img src="<?= $icon_content_splitterImage; ?>" alt="icon-content-splitter">
                                                <hr class="separator">
                                            </div>
                                            <p><?php echo lang('app.language_site_main_stepcheck_available_demand'); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-xs-12">
                                    <div class="checkstep_subcard alabaster">
                                        <div class="subcard_title text-center">
                                            <h3><?php echo lang('app.language_site_main_stepcheck_affordable'); ?></h3>
                                        </div>
                                        <div class="subcard_content">
                                            <p><?php echo lang('app.language_site_main_stepcheck_highest_quality'); ?></p>
                                            <div class="separator_icon">
                                                <img src="<?= $icon_content_splitterImage; ?>" alt="icon-content-splitter">
                                                <hr class="separator">
                                            </div>
                                            <p><?php echo lang('app.language_site_main_stepcheck_funded'); ?></p>
                                            <div class="separator_icon">
                                                <img src="<?= $icon_content_splitterImage; ?>" alt="icon-content-splitter">
                                                <hr class="separator">
                                            </div>
                                            <p><?php echo lang('app.language_site_main_stepcheck_vision'); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-xs-12">
                                    <div class="checkstep_subcard solitude">
                                        <div class="subcard_title text-center">
                                            <h3><?php echo lang('app.language_site_main_stepcheck_instant'); ?></h3>
                                        </div>
                                        <div class="subcard_content">
                                            <p><?php echo lang('app.language_site_main_stepcheck_results_instantly'); ?></p>
                                            <div class="separator_icon">
                                                <img src="<?= $icon_content_splitterImage; ?>" alt="icon-content-splitter">
                                                <hr class="separator">
                                            </div>
                                            <p><?php echo lang('app.language_site_main_stepcheck_rigorously'); ?></p>
                                            <div class="separator_icon">
                                                <img src="<?= $icon_content_splitterImage; ?>" alt="icon-content-splitter">
                                                <hr class="separator">
                                            </div>
                                            <p><?php echo lang('app.language_site_main_stepcheck_specialists'); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- section 3 -->

                <section class="cat_stepcheck_three">
                    <div class="container">
                        <div class="cats_checkstep_box">
                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    <h2 class="text-center"><?php echo lang('app.language_site_main_stepcheck_who_for'); ?></h2>
                                </div>
                                <div class="col-sm-4">
                                    <div class="checkstep_box_one box_content">
                                        <h4 class="box_content_title text-center"><?php echo lang('app.language_site_main_stepcheck_agencies'); ?></h4>
                                        <div class="box_img">
                                            <img src="<?php echo base_url('public/images/who_stepcheck_1.jpg'); ?>" alt="Loading">
                                        </div>
                                        <div class="box_content text-center">
                                            <p><?php echo lang('app.language_site_main_stepcheck_recurit'); ?></p>
                                            <a href="<?php echo site_url("/pages/cats_stepcheck_employers"); ?>" class="btn btn-sm btn-continue btn-pink"><?= $language_site_main_stepcheck_learn_moreLang; ?></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="checkstep_box_one two box_content">
                                        <h4 class="box_content_title text-center"><?php echo lang('app.language_site_main_stepcheck_institutions'); ?></h4>
                                        <div class="box_img">
                                            <img src="<?php echo base_url('public/images/who_stepcheck_2.jpg'); ?>" alt="Loading">
                                        </div>
                                        <div class="box_content text-center">
                                            <p><?php echo lang('app.language_site_main_stepcheck_institutions_for'); ?></p>
                                            <a href="<?php echo site_url("/pages/cats_stepcheck_education"); ?>" class="btn btn-sm btn-continue btn-pink"><?= $language_site_main_stepcheck_learn_moreLang; ?></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="checkstep_box_one three box_content">
                                        <h4 class="box_content_title text-center"><?php echo lang('app.language_site_main_stepcheck_goverment_agencies'); ?></h4>
                                        <div class="box_img">
                                            <img src="<?php echo base_url('public/images/who_stepcheck_3.jpg'); ?>" alt="Loading">
                                        </div>
                                        <div class="box_content text-center">
                                            <p><?php echo lang('app.language_site_main_stepcheck_goverment_targets'); ?></p>
                                            <a href="<?php echo site_url("/pages/cats_stepcheck_goverment"); ?>" class="btn btn-sm btn-continue btn-pink"><?= $language_site_main_stepcheck_learn_moreLang; ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- section 4 -->

                <section class="cat_stepcheck_four">
                    <div class="container">
                        <div class="cats_checkstep_about">
                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    <h2 class="text-center"><?php echo lang('app.language_site_main_stepcheck_about'); ?></h2>
                                    <div class="checkstep_about">
                                        <div class="checkstep_about_img">
                                            <img src="<?php echo base_url('public/images/checkstep-about.jpg'); ?>" alt="checkstep-about">
                                        </div>
                                        <div class="checkstep_about_content">
                                            <p><?php echo lang('app.language_site_main_stepcheck_range_skills'); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>


                <!-- section 5 -->

                <section class="cat_stepcheck_five">
                    <div class="container">
                        <div class="cats_checkstep_skill">
                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    <h2 class="text-center"><?php echo lang('app.language_site_main_stepcheck_skills_test'); ?></h2>
                                    <P class="text_center"><?php echo lang('app.language_site_main_stepcheck_separate_test'); ?></P>
                                </div>
                            </div>
                            <div class="stepcheck_express">
                                <ul class="list-unstyled stepcheck_express_list tranquil">
                                    <li>
                                        <div class="skill_title text_center">
                                            <h3><?= $language_site_main_stepcheck_stepcheckLang; ?><span class="disp_block"><?php echo lang('app.language_site_main_stepcheck_express'); ?></span> </h3>
                                        </div>
                                    </li>
                                    <li>
                                        <ul class="skill_sublist_head">
                                            <li>
                                                <div class="skill_sublist text-center">
                                                    <i class="fa fa-headphones" aria-hidden="true"></i>
                                                    <p><?php echo lang('app.language_site_main_stepcheck_ex_listening'); ?></p>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="skill_sublist text-center">
                                                    <i class="fa fa-book" aria-hidden="true"></i>
                                                    <p><?php echo lang('app.language_site_main_stepcheck_ex_reading'); ?></p>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="skill_sublist disabled text-center">
                                                    <i class="fa fa-microphone" aria-hidden="true"></i>
                                                    <p><?php echo lang('app.language_site_main_stepcheck_ex_speaking'); ?></p>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="skill_sublist disabled text-center">
                                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                                    <p><?php echo lang('app.language_site_main_stepcheck_ex_writing'); ?></p>
                                                </div>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <div class="skill_mainlist text-center">
                                            <i class="fa fa-clock-o" aria-hidden="true"></i>
                                            <p><?php echo lang('app.language_site_main_stepcheck_ex_time'); ?></p>
                                        </div>
                                    </li>
                                </ul>

                                <ul class="list-unstyled stepcheck_express_list tranquil alabaster mt40">
                                    <li>
                                        <div class="skill_title text_center">
                                            <h3><?= $language_site_main_stepcheck_stepcheckLang; ?><span class="disp_block"><?php echo lang('app.language_site_main_stepcheck_ex_with_speaking'); ?></span> </h3>
                                        </div>
                                    </li>
                                    <li>
                                        <ul class="skill_sublist_head">
                                            <li>
                                                <div class="skill_sublist text-center">
                                                    <i class="fa fa-headphones" aria-hidden="true"></i>
                                                    <p><?php echo lang('app.language_site_main_stepcheck_ex_sp_listening'); ?></p>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="skill_sublist text-center">
                                                    <i class="fa fa-book" aria-hidden="true"></i>
                                                    <p><?php echo lang('app.language_site_main_stepcheck_ex_sp_reading'); ?></p>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="skill_sublist text-center">
                                                    <i class="fa fa-microphone" aria-hidden="true"></i>
                                                    <p><?php echo lang('app.language_site_main_stepcheck_ex_sp_speaking'); ?></p>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="skill_sublist disabled text-center">
                                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                                    <p><?php echo lang('app.language_site_main_stepcheck_ex_sp_writing'); ?></p>
                                                </div>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <div class="skill_mainlist text-center">
                                            <i class="fa fa-clock-o" aria-hidden="true"></i>
                                            <p><?php echo lang('app.language_site_main_stepcheck_ex_sp_time'); ?></p>
                                        </div>
                                    </li>
                                </ul>

                                <ul class="list-unstyled stepcheck_express_list tranquil solitude mt40">
                                    <li>
                                        <div class="skill_title text_center">
                                            <h3><?= $language_site_main_stepcheck_stepcheckLang; ?><span class="disp_block"><?php echo lang('app.language_site_main_stepcheck_ex_communicator'); ?></span> </h3>
                                        </div>
                                    </li>
                                    <li>
                                        <ul class="skill_sublist_head">
                                            <li>
                                                <div class="skill_sublist text-center">
                                                    <i class="fa fa-headphones" aria-hidden="true"></i>
                                                    <p><?php echo lang('app.language_site_main_stepcheck_ex_comm_listenig'); ?></p>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="skill_sublist text-center">
                                                    <i class="fa fa-book" aria-hidden="true"></i>
                                                    <p><?php echo lang('app.language_site_main_stepcheck_ex_comm_reading'); ?></p>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="skill_sublist  text-center">
                                                    <i class="fa fa-microphone" aria-hidden="true"></i>
                                                    <p><?php echo lang('app.language_site_main_stepcheck_ex_comm_speaking'); ?></p>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="skill_sublist  text-center">
                                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                                    <p><?php echo lang('app.language_site_main_stepcheck_ex_comm_writing'); ?></p>
                                                </div>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <div class="skill_mainlist text-center">
                                            <i class="fa fa-clock-o" aria-hidden="true"></i>
                                            <p><?php echo lang('app.language_site_main_stepcheck_ex_comm_timr'); ?></p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-xs-12 text-center mt40">                        
                                    <a href="<?php echo site_url("/pages/cats_stepcheck_format"); ?>" class="btn btn-sm btn-continue btn-pink btn-ellipsis"><?php echo lang('app.language_site_main_stepcheck_format_test'); ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- section 6 -->

                <section class="cat_stepcheck_six">
                    <div class="container">
                        <div class="cats_checkstep_running">
                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    <h2 class="text-center"><?php echo lang('app.language_site_main_stepcheck_running'); ?></h2>
                                    <p><?php echo lang('app.language_site_main_stepcheck_administered'); ?></p>
                                    <p><?php echo lang('app.language_site_main_stepcheck_unique_code'); ?></p>
                                    <ul class="list-unstyled cats_checkstep_list_number ">
                                        <li>
                                            <div class="bullet_points">
                                                <span class="number_count">1</span>
                                                <p><?php echo lang('app.language_site_main_stepcheck_candidate_code'); ?></p>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="bullet_points">
                                                <span class="number_count">2</span>
                                                <p><?php echo lang('app.language_site_main_stepcheck_information_manually'); ?></p>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="bullet_points">
                                                <span class="number_count">3</span>
                                                <p> <?php echo lang('app.language_site_main_stepcheck_takes_test'); ?></p>
                                            </div>
                                        </li>
                                    </ul>
                                    <p class="margin_none"><?php echo lang('app.language_site_main_stepcheck_local_distributor'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- section 7 -->

                <section class="cat_stepcheck_seven">
                    <div class="container">
                        <div class="cats_checkstep_result">
                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    <h2 class="text-center"><?php echo lang('app.language_site_main_stepcheck_results'); ?></h2>
                                    <p class="text_center"><?php echo lang('app.language_site_main_stepcheck_report_provided'); ?></p>
                                    <ul class="list-unstyled cats_checkstep_list support_learning_list">
                                        <li><?php echo lang('app.language_site_main_stepcheck_cats_id'); ?></li>
                                        <li><?php echo lang('app.language_site_main_stepcheck_date_taken'); ?></li>
                                        <li><?php echo lang('app.language_site_main_stepcheck_cefr_level'); ?></li>
                                        <li><?php echo lang('app.language_site_main_stepcheck_short_description'); ?></li>
                                    </ul>
                                    <p class="text_center margin_none"><?php echo lang('app.language_site_main_stepcheck_reporting_dashboard'); ?></p>
                                </div>

                            </div>
                        </div>
                    </div>
                </section>


                <!-- section 8 -->

                <section class="cat_stepcheck_eight">
                    <div class="container">
                        <div class="cats_checkstep_journey">
                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    <h2 class="text-center"><?php echo lang('app.language_site_main_stepcheck_single_step'); ?></h2>
                                    <p class="margin_none text_center"><?php echo lang('app.language_site_main_stepcheck_standalone'); ?></p>                                               
                                    <div class="text-center">
                                         <a class="btn btn-sm btn-continue btn-pink btn-ellipsis" href="<?php echo site_url("/pages/cats_steps"); ?>"><?php echo lang('app.language_site_main_stepcheck_here'); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- section 9 -->

                <section class="cat_stepcheck_nine">
                    <div class="container">
                        <div class="cats_checkstep_support">
                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    <h2 class="text-center"><?php echo lang('app.language_site_main_stepcheck_support'); ?></h2>
                                    <div class="checkstep_about">
                                        <div class="checkstep_about_img">
                                            <img src="<?php echo base_url('public/images/cats-checkstep-support.jpg'); ?>" alt="cats-checkstep-support">
                                        </div>
                                        <div class="checkstep_about_content">
                                            <p><?php echo lang('app.language_site_main_stepcheck_aspects_test'); ?></p>
                                        </div>
                                    </div>
                                </div>
      
                                <div class="col-sm-6 col-xs-12">
                                    <div class="support_content join text_center">
                                        <h3><?php echo lang('app.language_site_main_stepcheck_become'); ?></h3>
                                        <p><?php echo lang('app.language_site_main_stepcheck_offer_clients'); ?></p>
                                        <a  href="JavaScript:Void(0);" class="btn btn-sm btn-continue btn-pink" data-toggle='modal' data-target='#cats_network_join'><?php echo lang('app.language_site_main_stepcheck_join_network'); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- section 10 -->



            </div>


<!-- Modal starts here -->
    <div class="cats_network_popup">
        <div class="modal fade" id="cats_network_join" role="dialog">
            <div class="modal-dialog modal-lg">

                <!-- Modal content-->

                <div class="modal-content">
                    <div class="modal-header">
                        <h4><?php echo lang('app.language_site_main_join_cats_step_title');?></h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p><?php echo lang('app.language_site_main_join_cats_step_desc1');?></p>                        
                        <p><?php echo lang('app.language_site_main_join_cats_step_desc2');?></p>
                        <p><?php echo lang('app.language_site_main_join_cats_step_desc3');?></p>
                        <p class="text-center"><?php echo lang('app.language_site_main_join_cats_step_desc4');?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>


            <script>
                $(document).ready(function () {
                    setSize();
                    setBoxSize();
                    setHeadHeight();
                });

                $(window).resize(function () {
                    setSize();
                    setBoxSize();
                    setHeadHeight();
                });

                $(window).load(function () {
                    setSize();
                    setBoxSize();
                    setHeadHeight();
                });

                function setSize() {
                    var heights = [];
                    $('.checkstep_subcard').each(function () {
                        $(this)[0].style.height = 'auto';
                    });
                    $('.checkstep_subcard').each(function () {
                        heights.push($(this)[0].getBoundingClientRect().height);

                    });
                    $('.checkstep_subcard').each(function () {
                        $(this)[0].style.height = Math.max(heights[0], heights[1], heights[2]) + 'px';
                    });
                }

                function setBoxSize() {
                    var heights = [];

                    $('.box_content').each(function () {
                        $(this)[0].style.height = 'auto';
                    });

                    $('.box_content').each(function () {
                        heights.push($(this).find('p')[0].getBoundingClientRect().height);

                    });
                    $('.box_content p').each(function () {
                        $(this)[0].style.height = Math.max(heights[0], heights[1], heights[2]) + 'px';
                    });
                }

                function setHeadHeight() {
                    var heights = [];

                    $('.checkstep_box_one.box_content h4').each(function () {
                        $(this)[0].style.height = 'auto';
                    });

                    $('.checkstep_box_one.box_content ').each(function () {
                        heights.push($(this).find('h4')[0].getBoundingClientRect().height);

                    });
                    $('.checkstep_box_one.box_content > h4').each(function () {
                        $(this)[0].style.height = Math.max(heights[0], heights[1], heights[2]) + 'px';
                    });
                }
            </script>