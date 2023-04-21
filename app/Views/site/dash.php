<?php
use Config\Oauth;
use Config\MY_Lang;
$this->session = \Config\Services::session();
$this->db = \Config\Database::connect();
$this->oauth = new Oauth(); 
$this->lang = new MY_Lang(); 
?>

<style>
    .final_msg .alert{
        margin: 20px 0 0;
    }
    .final_test_token {
        display: block;
        margin: auto;
        margin-bottom: 20px;
    }
    .practice_error_msg, .final_error_msg{
        margin: 20px 0 20px 0;
    }
    .practice_error_msg .alert, .final_error_msg .alert{
        margin-bottom: 0;
    }
</style>
<div id="bloc1-dash">
<?php
if(isset($learnerType) && $learnerType != ''){
	$learner = $learnerType;
}
else{
	$learner = 'over13';
}
$learner = $this->session->get('learnertype');
if(isset($learner) && $learner == 'under13'){
	$learnerType = 'under13';
}else{
	$learnerType = 'over13';
}
if($products) {
    $course_level = (isset($products['0']['level']) && $products['0']['level'] != '' ) ? $products['0']['level'] : '';
    $course_url = $this->oauth->catsurl('moodle_course_url_by_name') . 'Level ' . $course_level;
}
$recent_product = $products['0'];
$prval = $recent_product;
if(!empty($prval)){
    $practice_test1_token = $practice_test2_token = $practice_test_url1 = $practice_test_url2 = $final_test_token = $final_test_url = "";
    $practice_test_launch_btn1 = $practice_test_launch_btn2 = $final_test_launch_btn = "disabled";
    //WP-1202 Fetch the Practice Test test Launch details 
    if(isset($prval['practice_test']) && count($prval['practice_test']) > 0){
        $practice_tests = $prval['practice_test'];
        //WP-1308 Starts
        if(isset($practice_tests['practice_test_count'])){
            $practice_test_count = $practice_tests['practice_test_count'];
        }
        //WP-1308 Ends
        if(isset($practice_tests['launch_urls']) && count($practice_tests['launch_urls']) > 0){
            if(isset($practice_tests['launch_urls']['practice_test1'])){
                $practice_test_url1 = $practice_tests['launch_urls']['practice_test1'];
            }
            if(isset($practice_tests['launch_urls']['practice_test2'])){
                $practice_test_url2 = $practice_tests['launch_urls']['practice_test2'];
            }
        }
        if(isset($practice_tests['launch_btns']) && count($practice_tests['launch_btns']) > 0){
            if(isset($practice_tests['launch_btns']['practice_test1'])){
                $practice_test_launch_btn1 = ($practice_tests['launch_btns']['practice_test1'] === 'enable') ? "" : "disabled";
            }
            if(isset($practice_tests['launch_btns']['practice_test2'])){
                $practice_test_launch_btn2 = ($practice_tests['launch_btns']['practice_test2']=== 'enable') ? "" : "disabled";
            }
        }
        if(isset($practice_tests['launch_tokens']) && count($practice_tests['launch_tokens']) > 0){
            if(isset($practice_tests['launch_tokens']['practice_test1'])){
                $practice_test1_token = $practice_tests['launch_tokens']['practice_test1'];
            }
            if(isset($practice_tests['launch_tokens']['practice_test2'])){
                $practice_test2_token = $practice_tests['launch_tokens']['practice_test2'];
            }
        }
    }
    //WP-1202 Fetch the Final Test test Launch details
    if(isset($prval['final_test']) && count($prval['final_test']) > 0){
        $final_tests = $prval['final_test'];
        if(isset($final_tests['launch_urls']) && count($final_tests['launch_urls']) > 0){
            $final_test_url = $final_tests['launch_urls']['final_test1'];
        }
        if(isset($final_tests['launch_btns']) && count($final_tests['launch_btns']) > 0){
            $final_test_launch_btn = ($final_tests['launch_btns']['final_test1'] === 'enable') ? "" : "disabled";
        }
        if(isset($final_tests['launch_tokens']) && count($final_tests['launch_tokens']) > 0){
            $final_test_token = $final_tests['launch_tokens']['final_test1'];
        }
    }
	
// about tab changes starts

$current_product_id = (isset($products['0']['product_id']) && $products['0']['product_id'] != '' ) ? $products['0']['product_id'] : '';

$levelarray = array();
$recommended_product_id = $current_product_id;
$level = $recommended_product_id;

if (!empty($recommended_product_id)) {
    $levelarray [] = $recommended_product_id - 1;
    $levelarray [] = $recommended_product_id;
    $levelarray [] = $recommended_product_id + 1;
}
if ($this->session->get('next_course')) {
    $level = $this->session->get('next_course');
    $levelarray [] = $level;
}
$step_forward = array();
$step_forward [] = $all_products['0'];
$step_forward [] = $all_products['1'];
$step_forward [] = $all_products['2'];
$step_up = array();
$step_up [] = $all_products['3'];
$step_up [] = $all_products['4'];
$step_up [] = $all_products['5'];
$step_ahead = array();
$step_ahead [] = $all_products['6'];
$step_ahead [] = $all_products['7'];
$step_ahead [] = $all_products['8'];
$step_higher = array();
$step_higher [] = $all_products['9'];
$step_higher [] = $all_products['10'];
$step_higher [] = $all_products['11'];

// about tab changes ends	
?>
    <?php if ((isset($prval['final_test_section'])) && ($prval['final_test_section'] == 'show')) { ?>
    <div class="terms_condtion nav_dashboard final_test">
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#final_test"><?php echo lang('app.language_dashboard_final_test'); ?></a></li>
        <?php } ?>
        </ul>
        <div class="tab-content">
            <?php if ((isset($prval['final_test_section'])) && ($prval['final_test_section'] == 'show')) { ?>
            <div id="final_test" class="tab-pane fade in active learning_tab">
                <div class="row">
                    <?php if (null !== $this->session->getFlashdata('final_error')) { ?>
                        <div class="final_error_msg">
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <?php echo $this->session->getFlashdata('final_error'); ?>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if (isset($is_mobile)&& ($is_mobile['device_type'] == "phone") && ($prval['product_id'] >= 10 && $prval['product_id'] < 13)) { ?>
                        <div class="final_error_msg">
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <?php echo lang('app.language_dashboard_higher_final_not_mobile'); ?>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="col-sm-12">
                    <?php $institution_zone_values = @get_institution_zone_from_utc($institution_timezone,$prval['start_date_time'],$prval['end_date_time']); ?>
                        <p> <?php echo lang('app.language_dashboard_final_test_txt') . " " . $institution_zone_values['institute_start_time'] . ' and ' . $institution_zone_values['institute_end_time'] . "."; ?> </p>
                        <?php if(isset($is_mobile)&& ($is_mobile['device_type'] == "phone") && ($prval['product_id'] >= 10 && $prval['product_id'] < 13)){ ?>
                            <input type="button" value="Start Final Test" class="btn btn-main final_test_token btn_cats disabled" />
                        <?php } else {?>
                            <input type="button" value="Start Final Test" onclick="window.open('<?php echo $final_test_url; ?>' <?php echo ($final_test_token == '') ? "" : ",'_self'"; ?>)" class="btn btn-main final_test_token btn_cats" <?php echo ($final_test_url == "") ? "disabled" : $final_test_launch_btn; ?> />
                            <input type="hidden" id="final_test_token" value= <?php echo base64_encode($final_test_token); ?>>
                        <?php }?>
                    </div>
                </div>
            </div>  
        </div>
    </div>
                    <?php } ?>
<div class="nav_dashboard">
    <div class="overflow_x">
    <ul class="nav nav-tabs">
        <!-- Learning and Preparation Tab -->
        <li class="active"> <a data-toggle="tab" href="#learning"><?php echo lang('app.language_dashboard_learning_and_preparation'); ?></a> </li>
        <!-- Practice Test Tab -->
        <?php if($prval['product_id'] < 10){ ?>
        <li> <a data-toggle="tab" href="#practice"><?php echo (isset($practice_test_count) && $practice_test_count == 1) ? lang('app.language_dashboard_practice_test') : lang('app.language_dashboard_practice_tests'); ?></a> </li>
        <?php } ?>
         <!-- Final Test Arrangement Tab -->
        <?php if(!isset($prval['results']) && !isset($higherresults)) { // To hide this section if final test is completed ?>
        <li> <a data-toggle="tab" href="#book"><?php echo lang('app.language_dashboard_book_your_finaltest'); ?></a> </li>
        <?php } ?>
        <?php if ($learnerType != 'under13') { ?>
            <!-- Result Tab -->
         <li> <a data-toggle="tab" href="#result"><?php echo lang('app.language_dashboard_your_result'); ?></a> </li>
        <?php if ($show_book_next != 'hide') { ?>
         <!-- Your Next Step Tab -->
        <li> <a data-toggle="tab" href="#nextlevel"><?php echo lang('app.language_dashboard_final_next_level'); ?></a> </li>
        <?php } }?>
		<li> <a data-toggle="tab" href="#level_about"><?php echo lang('app.language_dashboard_about_level'); ?></a> </li>
        <li class="pull-right"></li> 
    </ul>
    </div>
    <div class="tab-content"> <!--Learning and Preparation COMPLETED -STARTS -->
        <div id="learning" class="tab-pane fade in active learning_tab">
            <a class="help_icon_hide" href="<?php if($this->lang->lang() == 'en'){ echo $helplinks['4']['target_url'];} elseif($this->lang->lang() == 'ms'){ echo $helplinks['12']['target_url'];}  ?>"  target="_blank" class="ico-help-md pull-right hidden-xs" title="help">
                <img src="<?php echo base_url().'/public/images/ico-help.png';?>" alt="icon">
            </a>
            <p><?php echo lang('app.language_dashboard_learning_you_have_45'); ?> <span id="product_name"><?php echo (isset($prval['name']) && $prval['name'] != '' ) ? $prval['name'] : ''; ?></span> <?php echo lang('app.language_dashboard_learning_test'); ?>. <?php echo lang('app.language_dashboard_learning_as_you_study'); ?>.</p>
           
            <div class="courseBtn text_center" id="courseBtn_<?php echo (isset($prval['product_id']) && $prval['product_id'] != '') ? $prval['product_id'] : ''; ?>">
                <?php if ((isset($prval['results']) && $prval['results']['candidate_id']) || (isset($higherresults) && $higherresults[0]->thirdparty_id)) { ?>
                    <button type="button" class="btn btn-main btn_cats mb20" disabled><?php echo lang('app.wc_language_dashboard_open_course1'); ?></button>
                    <?php } else {
                    if (isset($is_mobile)) {
                        $page_apps_id =  ($is_mobile['device_os'] == "IOS") ? (($prval['product_id'] >= 10 && $prval['product_id'] < 13) ? $list_apps['7']['id'] : $list_apps['1']['id']) : (($prval['product_id'] >= 10 && $prval['product_id'] < 13) ? $list_apps['6']['id'] : $list_apps['0']['id']); ?>
                        <a  class ="dash-mobile-popup-link btn btn-main btn_cats mb20" data-toggle="modal" data-target="#mobile-playstore-link-modal-dash" data-backdrop="static" data-keyboard="false" data-id="<?php echo $page_apps_id; ?>" id="<?php echo $page_apps_id; ?>" ><?php echo lang('app.wc_language_dashboard_open_course1'); ?> </a>
                    <?php } else { ?>
                        <button type="button" id="courseBtnWc" class="btn btn-main btn_cats mb20"><?php echo lang('app.wc_language_dashboard_open_course1'); ?></button>
                <?php }
                } ?>
            </div>
            <div class="row">
                <div class="col-sm-6 col-s-12">
                    <p><?php echo lang('app.language_dashboard_learning_download_the_cats_app'); ?>.</p>
                    <div class="app_icon">
                        <div class="row"> <?php 
                        if ($prval['product_id'] >= 10 && $prval['product_id'] < 13) { ?>
                            <div class="col-md-3 col-sm-4 col-xs-12 text-center">
                                 <a href="<?php echo $list_apps['6']['app_link']; ?>" target="_blank" class="ico-help" title="Android">
                                    <img src="<?php echo base_url() . '/public/images/ico-android.png'; ?>" alt="icon">
                                    <span>Android</span>
                                 </a>
                                 
                            </div>
                            <div class="col-md-3 col-sm-4 col-xs-12 text-center">                                   
                                <a href="<?php echo $list_apps['7']['app_link']; ?>" target="_blank" class="ico-help" title="iOS">
                                    <img src="<?php echo base_url() . '/public/images/ico-ios.png'; ?>" alt="icon">
                                    <span>iOS</span>
                                </a>
                                
                            </div>
                            <div class="col-md-3 col-sm-4 col-xs-12 text-center hidden-md hidden-sm visible-xs">
                                <a class="help_icon_hide" href="<?php if($this->lang->lang() == 'en'){ echo $helplinks['4']['target_url'];} elseif($this->lang->lang() == 'ms'){ echo $helplinks['12']['target_url'];}  ?>"  target="_blank" class="ico-help" title="help">
                                    <img src="<?php echo base_url().'/public/images/ico-help.png';?>" alt="icon">
                                </a>
                            </div>

 <?php   
                        } else { ?>
                            <div class="col-md-3 col-sm-4 col-xs-12 text-center">
                                <a href="<?php echo $list_apps['0']['app_link']; ?>" target="_blank" class="ico-help" title="Android">
                                    <img src="<?php echo base_url() . '/public/images/ico-android.png'; ?>" alt="icon">
                                    <span>Android</span>
                                </a>
                                
                            </div>
                            <div class="col-md-3 col-sm-4 col-xs-12 text-center">
                                <a href="<?php echo $list_apps['1']['app_link']; ?>" target="_blank" class="ico-help" title="iOS">
                                    <img src="<?php echo base_url() . '/public/images/ico-ios.png'; ?>" alt="icon">
                                    <span>iOS</span>
                                </a>
                                
                            </div>
                            <div class="col-md-3 col-sm-4 col-xs-12 text-center hidden-md hidden-sm visible-xs">
                                <a class="help_icon_hide" href="<?php if($this->lang->lang() == 'en'){ echo $helplinks['4']['target_url'];} elseif($this->lang->lang() == 'ms'){ echo $helplinks['12']['target_url'];}  ?>"  target="_blank" class="ico-help" title="help">
                                    <img src="<?php echo base_url().'/public/images/ico-help.png';?>" alt="icon">
                                </a>
                            </div>
 <?php  
                        } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!--Learning and Preparation COMPLETED -ENDS -->

        <?php if($prval['product_id'] < 10){ ?>
       <div id="practice" class="tab-pane fade practice_tab">
            <a class="help_icon_hide" href="<?php if($this->lang->lang() == 'en'){ echo $helplinks['5']['target_url'];} elseif($this->lang->lang() == 'ms'){ echo $helplinks['13']['target_url'];}  ?>"  target="_blank" class="ico-help-md pull-right hidden-xs" title="help">
                <img src="<?php echo base_url().'/public/images/ico-help.png';?>" alt="icon">
            </a>
           <?php if(null !== $this->session->getFlashdata('practice_error') && $practice_test1_token != ""){ ?>
            <div class="practice_error_msg">
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <?php echo $this->session->getFlashdata('practice_error'); ?>
                </div>
            </div>
            <?php } ?>
            <?php if(isset($practice_test_count) && $practice_test_count == 1){ ?>
                <p><?php echo lang('app.language_dashboard_you_can_take_single_practice'); ?> (<?php echo $prval['name']; ?>). </p>
            <?php }else{ ?> 
                <p><?php echo lang('app.language_dashboard_you_can_take'); ?> (<?php echo $prval['name']; ?>). </p>
            <?php } ?>  
            <p class="practice_text"><?php echo lang('app.language_dashboard_you_need_txt'); ?></p>
            <div class="text_center">
                <?php if(isset($practice_test_count) && $practice_test_count == 1){ ?>
                    <input type="button" value="<?php echo lang('app.language_dashboard_practice_test_launch'); ?>" onclick="window.open('<?php echo $practice_test_url1; ?>' <?php echo ($practice_test1_token == '') ? "" : ",'_self'"; ?>)" class="btn btn-main btn_cats practice_test1_token" <?php echo ($practice_test_url1 == "") ? "disabled" : $practice_test_launch_btn1; ?> />
                    <input type="hidden" id="practice_test1_token" value= <?php echo base64_encode($practice_test1_token); ?>>
                <?php }else{ ?> 
                    <input type="button" value="<?php echo lang('app.language_dashboard_practice_test_launch'); ?>" onclick="window.open('<?php echo $practice_test_url1; ?>' <?php echo ($practice_test1_token == '') ? "" : ",'_self'"; ?>)" class="btn btn-main btn_cats practice_test1_token" <?php echo ($practice_test_url1 == "") ? "disabled" : $practice_test_launch_btn1; ?> />
                    <input type="hidden" id="practice_test1_token" value= <?php echo base64_encode($practice_test1_token); ?>>
                    <input type="button" value="<?php echo lang('app.language_dashboard_practice_test_launch').' 2'; ?>" onclick="window.open('<?php echo $practice_test_url2; ?>' <?php echo ($practice_test2_token == '') ? "" : ",'_self'"; ?>)" class="btn btn-main btn_cats practice_test2_token" <?php echo ($practice_test_url2 == "") ? "disabled" : $practice_test_launch_btn2; ?> />
                    <input type="hidden" id="practice_test2_token" value= <?php echo base64_encode($practice_test2_token); ?>>
                <?php } ?>    
            </div>
            <div class="mt20 text-center hidden-md hidden-sm visible-xs">
                <a class="help_icon_hide" href="<?php if($this->lang->lang() == 'en'){ echo $helplinks['5']['target_url'];} elseif($this->lang->lang() == 'ms'){ echo $helplinks['13']['target_url'];}  ?>"  target="_blank" class="ico-help" title="help">
                    <img src="<?php echo base_url().'/public/images/ico-help.png';?>" alt="icon">
                </a>
            </div>
       </div>
        <?php } ?>
        
            <?php if(!isset($prval['results']) && !isset($higherresults)) { // To hide this section if final test is completed ?>
            <div id="book" class="tab-pane fade book_tab">
                <?php if($prval['booking_id'] > 0 && $prval['booking_status'] == 1) {
                        //WP-1301 - Final test arrangements tab section for unsupervised learner -- On change Dash
                        if((!$prval['token_status'])){
                            if(isset($prval['final_test']['is_supervised']) && !$prval['final_test']['is_supervised']) {?>
        				    	<?php if (null !== $this->session->getFlashdata('final_error')) { ?>
                                    <div class="final_error_msg">
                                        <div class="alert alert-danger alert-dismissible" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                            <?php echo $this->session->getFlashdata('final_error'); ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if (isset($is_mobile) && ($is_mobile['device_type'] == "phone") && ($prval['product_id'] >= 10 && $prval['product_id'] < 13)) { ?>
                                    <div class="final_error_msg">
                                        <div class="alert alert-danger alert-dismissible" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                            <?php echo lang('app.language_dashboard_higher_final_not_mobile'); ?>
                                        </div>
                                    </div>
                                <?php } ?>
        				        <p>
                                    <?php echo (isset($practice_test_count) && $practice_test_count == 1) ? lang('app.language_dashboard_finaltest_self_description_single') : lang('app.language_dashboard_finaltest_self_description');?></p>
                                </p>
        					    <ul>
            					    <li><?php echo lang('app.language_dashboard_finaltest_self_checklist1'); ?></li>
            					    <li><?php echo lang('app.language_dashboard_finaltest_self_checklist2'); ?></li>
            					    <li><?php echo lang('app.language_dashboard_finaltest_self_checklist3'); ?></li>
            					    <li><?php echo lang('app.language_dashboard_finaltest_self_checklist4'); ?></li>
        					    </ul>
                                <?php if(isset($is_mobile) && ($is_mobile['device_type'] == "phone") && ($prval['product_id'] >= 10 && $prval['product_id'] < 13)){ ?>
                                    <input type="button" value="Start Final Test" class="btn btn-main final_test_token btn_cats disabled" />
                                <?php } else {?>
        					        <input type="button" value="Start Final Test" id="final_test_self" class="btn btn-main final_test_token btn_cats" <?php echo ($final_test_url == "") ? "disabled" : $final_test_launch_btn; ?> />
                                    <input type="hidden" id="final_test_token" value= <?php echo base64_encode($final_test_token); ?>>
                                <input type="hidden" id="final_test_url" value= <?php echo ($final_test_url); ?>>
                                <?php }
                            }
                        } else {
                            //WP-1142 - To show institution timezone
                            $institution_zone_values = @get_institution_zone_from_utc($institution_timezone,$prval['start_date_time'],$prval['end_date_time']);
                            $starttime = $institution_zone_values['institute_start_time'];?><!-- Show event date and time in Learner Dashboard -->
                            <p><?php echo lang('app.language_dashboard_get_finaltest_button');?></p>
                            <p><?php echo lang('app.language_learner_session_venue') .' : '.$prval['venue_name'].' - '.$prval['address_line1'].', '.$prval['address_line2'].', '.$prval['city']; ?></p>
                            <p><?php $newDate = $institution_zone_values['institute_event_date'];echo lang('app.language_learner_session_date_time') .' : '. $newDate.' & '.$starttime; ?></p>
                            <?php if(trim($prval['notes']) != ''){?>
                            	<p> <?php echo lang('app.more_information') .' : '.$prval['notes']; ?></p><?php
                            }
                        }
                   } else {
                          if(!$prval['token_status']) {?>
    				          <p><?php echo lang('app.language_dashboard_finaltest_self_system_error'); ?></p><?php
    				      }else{ ?>
    						<p><?php echo lang('app.language_dashboard_to_setup_finaltest'); ?></p><?php
    				      }
                   } ?>
	        </div>
    	    <?php }?>
        
        <?php if($learnerType != 'under13'){ ?>
        <div id="result" class="tab-pane fade result_tab">
            <a class="help_icon_hide" href="<?php if($this->lang->lang() == 'en'){ echo $helplinks['7']['target_url'];} elseif($this->lang->lang() == 'ms'){ echo $helplinks['15']['target_url'];}  ?>"  target="_blank" class="ico-help-md pull-right hidden-xs" title="help">
                <img src="<?php echo base_url().'/public/images/ico-help.png';?>" alt="icon">
            </a>
            <?php $attempt = count($products); foreach ($products as $prkey => $prval) { ?>
            <h3><?php if($attempt > 1){echo "Attempt ".($attempt - $prkey);}?></h3>
            <?php if($prval['product_id'] < 10){ ?>
            <?php if(isset($practice_test_count) && $practice_test_count == 1){ ?>
                <p class="result_text"><?php echo lang('app.language_dashboard_see_how_well_single_practice_test'); ?> <span id="product_name"><?php echo (isset($prval['name']) && $prval['name'] != '' ) ? $prval['name'] : ''; ?></span>. <?php echo lang('app.language_dashboard_you_can_see'); ?>.</p>
            <?php }else{ ?>
                <p class="result_text"><?php echo lang('app.language_dashboard_see_how_well'); ?> <span id="product_name"><?php echo (isset($prval['name']) && $prval['name'] != '' ) ? $prval['name'] : ''; ?></span>. <?php echo lang('app.language_dashboard_you_can_see'); ?>.</p>
            <?php } ?>
            <?php } else { ?>
            <p class="result_text"><?php echo lang('app.language_dashboard_see_how_well_higher'); ?> <span id="product_name"><?php echo (isset($prval['name']) && $prval['name'] != '' ) ? $prval['name'] : ''; ?></span>.</p>
            <?php } ?>
            <div class="row">
                <?php if($prval['product_id'] < 10){ ?>
                <!--Practise Test1 Button-->
                <?php if(isset($prval['practice_test']['practice_test_count']) && $prval['practice_test']['practice_test_count'] == 1){
                    $practice_test_label = '';
                }else{
                    $practice_test_label = '1';
                } ?>
                <div class="col-md-3 col-sm-4 col-xs-12 text-center">
                    <?php if(!empty($prval['practice_test']['launch_details']['practice_test1']['final_result'])) { ?>
                        <button data-toggle="modal" data-target=".practice-test-results" data-backdrop="static" data-keyboard="false" data-id="<?php echo lang('app.language_dashboard_practice_test'); ?> 1|<?php if(!empty($prval['practice_test']['launch_details']['practice_test1']['test_number'])){ echo $prval['practice_test']['launch_details']['practice_test1']['test_number']; } ?>|<?php if(!empty($prval['practice_test']['launch_details']['practice_test1']['final_result'])){ echo $prval['practice_test']['launch_details']['practice_test1']['final_result']; } ?>" class="btn btn-main practice-test-button btn_cats"><?php echo lang('app.language_dashboard_practice_test_title').' '.$practice_test_label; ?></button>&nbsp;&nbsp;<img alt="loading" id="loading_modal<?php if(!empty($prval['practice_test']['launch_details']['practice_test1']['final_result'])){ echo $prval['practice_test']['launch_details']['practice_test1']['final_result']; } ?><?php if(!empty($prval['practice_test']['launch_details']['practice_test1']['test_number'])){ echo $prval['practice_test']['launch_details']['practice_test1']['test_number']; } ?>" style="display:none;" src="<?php echo base_url('public/images/loading.gif'); ?>">
                    <?php } elseif(isset($prval['tds_practice_results']) && !empty($prval['tds_practice_results']['practice_test1'])){
                            $result_practice1 = $prval['tds_practice_results']['practice_test1'];
                            $status_result1 = ($result_practice1['status'] == 0) ? lang('app.language_dashboard_not_taken') : lang('app.language_dashboard_practice_pending');
                                if(!empty($result_practice1['processed_data'])){ ?>
                                    <button data-toggle="modal" data-target=".practice-test-results" data-backdrop="static" data-keyboard="false" data-id="<?php echo lang('app.language_dashboard_practice_test').' '.$practice_test_label;?>|<?php if(!empty($result_practice1['token'])){ echo $result_practice1['token']; } ?>" class="btn btn-main practice-test-button-tds btn_cats"><?php echo lang('app.language_dashboard_practice_test_title').' '.$practice_test_label; ?></button><img alt="loading" id="loading_modal<?php if(!empty($result_practice1['token'])){ echo $result_practice1['token']; } ?>?>" style="display:none;" src="<?php echo base_url('public/images/loading.gif'); ?>">
                          <?php } else{ ?>
                                    <p> <a class="btn btn-main btn_cats" disabled><?php echo lang('app.language_dashboard_practice_test_title').' '.$practice_test_label; ?></a>
                                        <span><?php echo $status_result1; ?></span>
                                    </p>
                            <?php } ?>
                    <?php } else { ?>
                            <p><a class="btn btn-main btn_cats" disabled><?php echo lang('app.language_dashboard_practice_test_title').' '.$practice_test_label; ?></a>
                                <span><?php echo lang('app.language_dashboard_not_taken'); ?></span>
                            </p>
                    <?php } ?>
                </div>
                <!--Practise Test2 Button-->
                <?php if((isset($prval['practice_test']['practice_test_count']) && $prval['practice_test']['practice_test_count'] != 1) || !isset($prval['practice_test']['practice_test_count'])){ ?>
                <div class="col-md-3 col-sm-4 col-xs-12 text-center">
                    <?php if(!empty($prval['practice_test']['launch_details']['practice_test2']['final_result'])) { ?>
                            <button  data-toggle="modal" data-target=".practice-test-results" data-backdrop="static" data-keyboard="false" data-id="<?php echo lang('app.language_dashboard_practice_test'); ?> 2|<?php if(!empty($prval['practice_test']['launch_details']['practice_test2']['test_number'])){ echo $prval['practice_test']['launch_details']['practice_test2']['test_number']; } ?>|<?php if(!empty($prval['practice_test']['launch_details']['practice_test2']['final_result'])){ echo $prval['practice_test']['launch_details']['practice_test2']['final_result']; } ?>" class="btn btn-main practice-test-button btn_cats"><?php echo lang('app.language_dashboard_practice_test_title').' 2'; ?></button>&nbsp;&nbsp;<img alt="loading" id="loading_modal<?php if(!empty($prval['practice_test']['launch_details']['practice_test2']['final_result'])){ echo $prval['practice_test']['launch_details']['practice_test2']['final_result']; } ?><?php if(!empty($prval['practice_test']['launch_details']['practice_test1']['test_number'])){ echo $prval['practice_test']['launch_details']['practice_test1']['test_number']; } ?>" style="display:none;" src="<?php echo base_url('public/images/loading.gif'); ?>">
                    <?php } elseif(isset($prval['tds_practice_results']) && !empty($prval['tds_practice_results']['practice_test2'])){
                            $result_practice2 = $prval['tds_practice_results']['practice_test2'];
                            $status_result2 = ($result_practice2['status'] == 0) ? lang('app.language_dashboard_not_taken') : lang('app.language_dashboard_practice_pending');
                                if(!empty($result_practice2['processed_data'])){ ?>
                                    <button data-toggle="modal" data-target=".practice-test-results" data-backdrop="static" data-keyboard="false" data-id="<?php echo lang('app.language_dashboard_practice_test'); ?> 2|<?php if(!empty($result_practice2['token'])){ echo $result_practice2['token']; } ?>" class="btn btn-main practice-test-button-tds btn_cats"><?php echo lang('app.language_dashboard_practice_test_title').' 2'; ?></button><img alt="loading" id="loading_modal<?php if(!empty($result_practice2['token'])){ echo $result_practice2['token']; } ?>?>" style="display:none;" src="<?php echo base_url('public/images/loading.gif'); ?>">
                          <?php } else{ ?>
                                    <p> <a class="btn btn-main btn_cats" disabled><?php echo lang('app.language_dashboard_practice_test_title'); ?> 2</a>
                                        <span><?php echo $status_result2; ?></span>
                                    </p>
                          <?php } ?>
                    <?php } else { ?>
                            <p><a class="btn btn-main btn_cats" disabled><?php echo lang('app.language_dashboard_practice_test_title'); ?> 2</a>
                                <span><?php echo lang('app.language_dashboard_not_taken'); ?></span>
                            </p>
                    <?php } ?>
                </div>
                <?php }?>
               <?php }?>
                 <!--Final Test Button--> 
                <div class="col-md-3 col-sm-4 col-xs-12 text-center mt20-xs">
                    <?php 
                        if(isset($tds_final_result_status) && $tds_final_result_status == 1 ){
                            $status_final = lang('app.language_dashboard_practice_pending');
                        }else{
                            $status_final = lang('app.language_dashboard_not_taken');
                        }
                    ?>
                    <?php if(isset($course_type) && $course_type == 'Higher'){
                            if(isset($higherresults) && $higherresults[0]->thirdparty_id && isset($higherresults[0]->processed_data)){ ?>
                                <div id="testresultbox_higher_tds"> <a  class ="final_result_higher btn btn-main btn btn-info btn-lg btn_cats" data-toggle="modal" data-target="#final-test-higher" data-backdrop="static" data-keyboard="false" data-id="<?php echo $higherresults[0]->thirdparty_id; ?>" id="<?php echo $higherresults[0]->thirdparty_id; ?>" token="<?php echo $higherresults[0]->token; ?>"><?php echo lang('app.language_dashboard_your_final_result'); ?> </a>
                                </div>    
                      <?php } elseif(isset($higherresults) && $higherresults[0]->thirdparty_id && isset($higherresults[0]->logit_values)){?>
                                <div id="testresultbox_higher"> <a  class ="final_result_higher btn btn-main btn btn-info btn-lg btn_cats" data-toggle="modal" data-target="#final-test-higher" data-backdrop="static" data-keyboard="false" data-id="<?php echo $higherresults[0]->candidate_id; ?>" id="<?php echo $higherresults[0]->candidate_id; ?>" token="NULL"><?php echo lang('app.language_dashboard_your_final_result'); ?> </a>
                                </div>
                     <?php } else { ?>
                                <p>
                                    <button type="button" class="btn btn-main  btn_cats" disabled><?php echo lang('app.language_dashboard_your_final_result'); ?></button>
                                    <span><?php echo $status_final; ?></span>
                                </p>
                      <?php } } else {
                                if(!empty($prval['results'])) {
                                        if($prval['thirdparty_id'] == $prval['results']['third_partyid']){ ?>
                                        <div id="testresultbox_core"> <a class ="final_result_core_dash btn-main btn btn-info btn-lg btn_cats" data-toggle="modal" data-target="#final-test-core-dash" data-backdrop="static" data-keyboard="false" data-id="<?php echo $prval['results']['candidate_id']; ?>" id="<?php echo $prval['results']['candidate_id']; ?>" ><?php echo lang('app.language_dashboard_your_final_result'); ?> </a>
                                        </div>     
                                <?php }  } else {?>
                                        <p>
                                            <button type="button" class="btn btn-main btn_cats" disabled><?php echo lang('app.language_dashboard_your_final_result'); ?></button>
                                            <span><?php echo $status_final; ?></span>
                                        </p>							
                                <?php  }?>
                        <?php  }?>
                </div>
                <div class="col-xs-12 mt20 text-center hidden-md hidden-sm visible-xs">
                    <a class="help_icon_hide" href="<?php if($this->lang->lang() == 'en'){ echo $helplinks['7']['target_url'];} elseif($this->lang->lang() == 'ms'){ echo $helplinks['15']['target_url'];}  ?>"  target="_blank" class="ico-help" title="help">
                        <img src="<?php echo base_url().'/public/images/ico-help.png';?>" alt="icon">
                    </a>
                </div>
            </div>
            <?php }?>
        </div>
       <?php }?>
         <!--Responsive code Starts--> 
         <?php 
        //to fetch content of the levels
        $step_frwd_one = @get_level_contents(FALSE,"A1.1");
        $step_frwd_two = @get_level_contents(FALSE,"A1.2");
        $step_frwd_three = @get_level_contents(FALSE,"A1.3");
        $step_up_one = @get_level_contents(FALSE,"A2.1");
        $step_up_two = @get_level_contents(FALSE,"A2.2");
        $step_up_three = @get_level_contents(FALSE,"A2.3");
        $step_ahead_one = @get_level_contents(FALSE,"B1.1");
        $step_ahead_two = @get_level_contents(FALSE,"B1.2");
        $step_ahead_three = @get_level_contents(FALSE,"B1.3");
        ?>
		<div id="level_about" class="tab-pane fade level_about_tab get_started">
			<div class="result_grade_tab">
				<div class="panel with-nav-tabs panel-default">
					<div class="panel-heading">
						<ul class="nav nav-tabs inner_tabs">
						<?php if ((isset($tokenType) && (($tokenType == "cats_core") || ($tokenType == "cats_core_or_higher") || ($tokenType == "catslevel")))) { ?>
							<li <?php if (in_array($recommended_product_id, array("1", "2", "3"))) { ?>class="active"<?php } ?>><a href="#a1_tab" data-toggle="tab"><span>A1</span></a></li>
							<li <?php if (in_array($recommended_product_id, array("4", "5", "6"))) { ?>class="active"<?php } ?>><a href="#a2_tab" data-toggle="tab"><span>A2</span></a></li>
							<li <?php if (in_array($recommended_product_id, array("7", "8", "9"))) { ?>class="active"<?php } ?>><a href="#b1_tab" data-toggle="tab"><span>B1</span></a></li>
						<?php } ?>
						<?php if (isset($tokenType) && (($tokenType == "cats_higher") || ($tokenType == "cats_core_or_higher")|| ($tokenType == "catslevel") )) { ?>
							<li <?php if (in_array($recommended_product_id, array("10", "11", "12"))) { ?>class="active"<?php } ?> ><a href="#b2_tab" data-toggle="tab"><span>B2/C1</span></a></li>
						<?php } ?>
						</ul>
					</div>
					<div class="result_grade_content inner_content_tab">
					<div class="panel-body">
						<div class="tab-content">
						<?php if ((isset($tokenType) && (($tokenType == "cats_core") || ($tokenType == "cats_core_or_higher") || ($tokenType == "catslevel")))) { ?>
						<div class="tab-pane fade <?php if (in_array($recommended_product_id, array("1", "2", "3"))) { ?>in active <?php } ?>" id="a1_tab">
							<div class="row">
								<div class="col-sm-3 col-xs-12 inner_align">
								<h3><?php echo lang('app.language_3_cats_courses'); ?></h3>
								<ul  class="step_frwd">					
									<?php foreach ($step_forward as $key => $value) { ?>	
                                                                        <?php $tab_id = "#step_frwd_".$value->id."_tab"; ?>
									<?php if (!empty($levelarray) && ( $levelarray['0'] == $value->id || (!empty($levelarray['1']) && $levelarray['1'] == $value->id) || (!empty($levelarray['2']) && $levelarray['2'] == $value->id) )) { ?>
										<li class="<?php if ($recommended_product_id == $value->id) echo "active" ?>">
                                                                                    <a style="color:#252b2f;text-decoration: none;" data-toggle="pill" href="<?php echo $tab_id; ?>"><?php echo $value->name; ?></a>
										</li>
									<?php } elseif ($this->session->get('user_enable_courses') == 'enableall') { ?>
                                                                                <li><input type="radio" name="recommended"  value ="<?php echo $value->id; ?>" <?php if ($recommended_product_id == $value->id) echo "checked" ?>/><a style="color:#252b2f;text-decoration: none;" data-toggle="pill" href="<?php echo $tab_id; ?>"><?php echo $value->name; ?></a></li>
									<?php } else { ?>	
                                                                                <li><a style="color:#252b2f;text-decoration: none;" data-toggle="pill" href="<?php echo $tab_id; ?>"><?php echo $value->name; ?></a></li>
									<?php } ?>					
									<?php } ?>
								</ul>
								</div>
								<div class="tab-content col-sm-9 col-xs-12 inner_h3" style="padding: 0;">
                                    <div id="step_frwd_1_tab" class="tab-pane fade <?php echo in_array($recommended_product_id, array("2", "3")) ? "":"active in"?>">
                                        <h3><?php echo lang('app.language_site_level_details_heading'); ?>:</h3>
                                            <ul class="step_frwd_text">
                                                <li><em class="fa fa-circle"></em><?php echo $step_frwd_one[0]; ?></li>
                                                <li><em class="fa fa-circle"></em><?php echo $step_frwd_one[1]; ?></li>
                                                <li><em class="fa fa-circle"></em><?php echo $step_frwd_one[2]; ?></li>
                                                <li><em class="fa fa-circle"></em><?php echo $step_frwd_one[3]; ?></li>
                                            </ul>
                                    </div>
                                    <div id="step_frwd_2_tab" class="tab-pane fade <?php echo ($recommended_product_id == 2) ? "active in":""?>">
                                        <h3><?php echo lang('app.language_site_level_details_heading'); ?>:</h3>
                                        <ul class="step_frwd_text">
                                            <li><em class="fa fa-circle"></em><?php echo $step_frwd_two[0]; ?></li>
                                            <li><em class="fa fa-circle"></em><?php echo $step_frwd_two[1]; ?></li>
                                            <li><em class="fa fa-circle"></em><?php echo $step_frwd_two[2]; ?></li>
                                            <li><em class="fa fa-circle"></em><?php echo $step_frwd_two[3]; ?></li>
                                        </ul>
                                    </div>
                                    <div id="step_frwd_3_tab" class="tab-pane fade <?php echo ($recommended_product_id == 3) ? "active in":""?>">
                                        <h3><?php echo lang('app.language_site_level_details_heading'); ?>:</h3>
                                        <ul class="step_frwd_text">
                                            <li><em class="fa fa-circle"></em><?php echo $step_frwd_three[0]; ?></li>
                                            <li><em class="fa fa-circle"></em><?php echo $step_frwd_three[1]; ?></li>
                                            <li><em class="fa fa-circle"></em><?php echo $step_frwd_three[2]; ?></li>
                                            <li><em class="fa fa-circle"></em><?php echo $step_frwd_three[3]; ?></li>
                                        </ul>
                                    </div>
                                </div>
							</div>	
						</div>
						<div class="tab-pane fade <?php if (in_array($recommended_product_id, array("4", "5", "6"))) { ?>in active <?php } ?>" id="a2_tab">
							<div class="row">
								<div class="col-sm-3 col-xs-12 inner_align">
								<h3><?php echo lang('app.language_3_cats_courses'); ?></h3>
								<ul class="step_frwd">
									<?php foreach ($step_up as $key => $value) { ?>
                                                                        <?php $tab_id = "#step_up_".$value->id."_tab"; ?>
										<?php if (!empty($levelarray) && ( $levelarray['0'] == $value->id || (!empty($levelarray['1']) && $levelarray['1'] == $value->id) || (!empty($levelarray['2']) && $levelarray['2'] == $value->id) )) { ?>
											<li class="<?php if ($recommended_product_id == $value->id) echo "active" ?>">

												<?php if ($recommended_product_id == $value->id) { ?>
													<?php if ($recommended_product_id == $value->id) { ?>
														<input type="hidden" name="recommended" id="recommended" value="<?php echo $value->id; ?>" />
												<?php } ?>
											<?php } ?>
                                                                                                                <a style="color:#252b2f;text-decoration: none;" data-toggle="pill" href="<?php echo $tab_id; ?>"><?php echo $value->name; ?></a>
											</li>
										<?php } elseif ($this->session->get('user_enable_courses') == 'enableall') { ?>
                                                                                        <li><input type="radio" name="recommended"  value ="<?php echo $value->id; ?>" <?php if ($recommended_product_id == $value->id) echo "checked" ?>/><a style="color:#252b2f;text-decoration: none;" data-toggle="pill" href="<?php echo $tab_id; ?>"><?php echo $value->name; ?></a></li>
										<?php } else { ?>
                                                                                        <li><a style="color:#252b2f;text-decoration: none;" data-toggle="pill" href="<?php echo $tab_id; ?>"><?php echo $value->name; ?></a></li>
										<?php } ?>
									<?php } ?>
								</ul>
								</div>
								<div class="tab-content col-sm-9 col-xs-12 inner_h3" style="padding: 0;">
                                                                    <div id="step_up_4_tab" class="tab-pane fade <?php echo in_array($recommended_product_id, array("5", "6")) ? "":"active in"?>">
                                                                      <h3><?php echo lang('app.language_site_level_details_heading'); ?>:</h3>
                                                                        <ul class="step_frwd_text">
                                                                            <li><em class="fa fa-circle"></em><?php echo $step_up_one[0]; ?></li>
                                                                            <li><em class="fa fa-circle"></em><?php echo $step_up_one[1]; ?></li>
                                                                            <li><em class="fa fa-circle"></em><?php echo $step_up_one[2]; ?></li>
                                                                            <li><em class="fa fa-circle"></em><?php echo $step_up_one[3]; ?></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div id="step_up_5_tab" class="tab-pane fade <?php echo ($recommended_product_id == 5) ? "active in":""?>">
                                                                        <h3><?php echo lang('app.language_site_level_details_heading'); ?>:</h3>
                                                                        <ul class="step_frwd_text">
                                                                            <li><em class="fa fa-circle"></em><?php echo $step_up_two[0]; ?></li>
                                                                            <li><em class="fa fa-circle"></em><?php echo $step_up_two[1]; ?></li>
                                                                            <li><em class="fa fa-circle"></em><?php echo $step_up_two[2]; ?></li>
                                                                            <li><em class="fa fa-circle"></em><?php echo $step_up_two[3]; ?></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div id="step_up_6_tab" class="tab-pane fade <?php echo ($recommended_product_id == 6) ? "active in":""?>">
                                                                        <h3><?php echo lang('app.language_site_level_details_heading'); ?>:</h3>
                                                                        <ul class="step_frwd_text">
                                                                            <li><em class="fa fa-circle"></em><?php echo $step_up_three[0]; ?></li>
                                                                            <li><em class="fa fa-circle"></em><?php echo $step_up_three[1]; ?></li>
                                                                            <li><em class="fa fa-circle"></em><?php echo $step_up_three[2]; ?></li>
                                                                            <li><em class="fa fa-circle"></em><?php echo $step_up_three[3]; ?></li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
							</div>
						</div>
						<div class="tab-pane fade <?php if (in_array($recommended_product_id, array("7", "8", "9"))) { ?>in active <?php } ?>" id="b1_tab">
							<div class="row">
								<div class="col-sm-3 col-xs-12 inner_align">
								<h3><?php echo lang('app.language_3_cats_courses'); ?></h3>
								<ul class="step_frwd">
									<?php foreach ($step_ahead as $key => $value) { ?>
                                                                        <?php $tab_id = "#step_ahead_".$value->id."_tab"; ?>
										<?php if (!empty($levelarray) && ( $levelarray['0'] == $value->id || (!empty($levelarray['1']) && $levelarray['1'] == $value->id) || (!empty($levelarray['2']) && $levelarray['2'] == $value->id) )) { ?>
											<li class="<?php if ($recommended_product_id == $value->id) echo "active" ?>">
												<?php if ($recommended_product_id == $value->id) { ?>
													<?php if ($recommended_product_id == $value->id) { ?>
														<input type="hidden" name="recommended" id="recommended" value="<?php echo $value->id; ?>" />
												<?php } ?>
											<?php } ?>
                                                                                                                <a style="color:#252b2f;text-decoration: none;" data-toggle="pill" href="<?php echo $tab_id; ?>"><?php echo $value->name; ?></a>
											</li>
										<?php } elseif ($this->session->get('user_enable_courses') == 'enableall') { ?>
                                                                                        <li><input type="radio" name="recommended"  value ="<?php echo $value->id; ?>" <?php if ($recommended_product_id == $value->id) echo "checked" ?>/><a style="color:#252b2f;text-decoration: none;" data-toggle="pill" href="<?php echo $tab_id; ?>"><?php echo $value->name; ?></a></li>
										<?php } else { ?>
                                                                                        <li><a style="color:#252b2f;text-decoration: none;" data-toggle="pill" href="<?php echo $tab_id; ?>"><?php echo $value->name; ?></a></li>
										<?php } ?>
									<?php } ?>
								</ul>
								</div>
								<div class="tab-content col-sm-9 col-xs-12 inner_h3" style="padding: 0;">
                                                                    <div id="step_ahead_7_tab" class="tab-pane fade <?php echo in_array($recommended_product_id, array("8", "9")) ? "":"active in"?>">
                                                                        <h3><?php echo lang('app.language_site_level_details_heading'); ?>:</h3>
                                                                        <ul class="step_frwd_text">
                                                                            <li><em class="fa fa-circle"></em><?php echo $step_ahead_one[0]; ?></li>
                                                                            <li><em class="fa fa-circle"></em><?php echo $step_ahead_one[1]; ?></li>
                                                                            <li><em class="fa fa-circle"></em><?php echo $step_ahead_one[2]; ?></li>
                                                                            <li><em class="fa fa-circle"></em><?php echo $step_ahead_one[3]; ?></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div id="step_ahead_8_tab" class="tab-pane fade <?php echo ($recommended_product_id == 8) ? "active in":""?>">
                                                                        <h3><?php echo lang('language_site_level_details_heading'); ?>:</h3>
                                                                        <ul class="step_frwd_text">
                                                                            <li><em class="fa fa-circle"></em><?php echo $step_ahead_two[0]; ?></li>
                                                                            <li><em class="fa fa-circle"></em><?php echo $step_ahead_two[1]; ?></li>
                                                                            <li><em class="fa fa-circle"></em><?php echo $step_ahead_two[2]; ?></li>
                                                                            <li><em class="fa fa-circle"></em><?php echo $step_ahead_two[3]; ?></li>
                                                                        </ul>
                                                                    </div>
                                                                    <div id="step_ahead_9_tab" class="tab-pane fade <?php echo ($recommended_product_id == 9) ? "active in":""?>">
                                                                      <h3><?php echo lang('app.language_site_level_details_heading'); ?>:</h3>
                                                                        <ul class="step_frwd_text">
                                                                            <li><em class="fa fa-circle"></em><?php echo $step_ahead_three[0]; ?></li>
                                                                            <li><em class="fa fa-circle"></em><?php echo $step_ahead_three[1]; ?></li>
                                                                            <li><em class="fa fa-circle"></em><?php echo $step_ahead_three[2]; ?></li>
                                                                            <li><em class="fa fa-circle"></em><?php echo $step_ahead_three[3]; ?></li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
							</div>
						</div>
						<?php } ?>
						<?php if (($tokenType == "cats_core_or_higher") || ($tokenType == "catslevel") || ($tokenType == "cats_higher")) { ?>
						<div class="tab-pane fade <?php if (in_array($recommended_product_id, array("10", "11", "12"))) { ?>in active <?php } ?>" id="b2_tab">
							<div class="row">
								<div class="col-sm-3 col-xs-12 inner_align">
								<h3><?php echo lang('app.language_3_cats_courses'); ?></h3>
								<ul class="step_frwd">
									<?php foreach ($step_higher as $key => $value) { ?>
										<?php if (!empty($levelarray) && ( $levelarray['0'] == $value->id || (!empty($levelarray['1']) && $levelarray['1'] == $value->id) || (!empty($levelarray['2']) && $levelarray['2'] == $value->id) )) { ?>
											<li class="<?php if ($recommended_product_id == $value->id) echo "active" ?>">
												<?php if ($recommended_product_id == $value->id) { ?>
													<?php if ($recommended_product_id == $value->id) { ?>
														<input type="hidden" name="recommended" id="recommended" value="<?php echo $value->id; ?>" />
												<?php } ?>
											<?php } ?>
											<?php echo $value->name; ?>
											</li>
										<?php } elseif ($this->session->get('user_enable_courses') == 'enableall') { ?>
											<li><input type="radio" name="recommended"  value ="<?php echo $value->id; ?>" <?php if ($level == $recommended_product_id->id) echo "checked" ?>/><?php echo $value->name; ?></li>
										<?php } else { ?>
											<li><?php echo $value->name; ?></li>
										<?php } ?>
									<?php } ?>
								</ul>
								</div>
								<div class="col-sm-9 col-xs-12">
									<h3><?php echo lang('app.language_site_you_will_be_able_to'); ?>:</h3>
									<ul class="step_frwd_text">
										<li><em class="fa fa-circle"></em><?php echo lang('app.language_site_talk_and_write_about'); ?>.</li>
										<li><em class="fa fa-circle"></em><?php echo lang('app.language_site_express_and'); ?>.</li>
										<li><em class="fa fa-circle"></em><?php echo lang('app.language_site_understand'); ?>.</li>
										<li><em class="fa fa-circle"></em><?php echo lang('app.language_site_contribute'); ?>.</li>
										<li><em class="fa fa-circle"></em><?php echo lang('app.language_site_write_emails'); ?>.</li>
									</ul>
								</div>
							</div>
						</div>
						<?php }?>
						</div>
					</div>
					</div>
				</div>
			</div>
		</div>
         <!--Responsive code Ends-->
        <div id="nextlevel" class="tab-pane fade book_tab result_tab">
            <?php if ($learnerType != 'under13') { ?>
                <?php if ($show_book_next != 'hide') { ?>
            <div class="bg-lightblue">
                <div class="container-fluid">
                    <div class="row">
                        <?php
                            $next_level_to_purchase = (!empty($highest_level_purchased)) ? $next_level_to_purchase : "";

                            if(isset($productEligible)){
                                $eligibleProduct = [];
                                foreach($productEligible as $product){
                                        $eligibleProduct[] = $product->group_id;
                                }
                            }
                            $builder = $this->db->table('product_groups');
                            $builder->select('product_groups.name');
                            $builder->whereIn('id', $eligibleProduct);
                            $query = $builder->get();
                            $query_result = $query->getResultArray();
                            $eligibleProduct_name = array_map("current", $query_result);
                                                    
                            $productGroupId = '';
                            if($next_level_to_purchase != '' && $next_level_to_purchase <=9){
                                    $productGroupId = 2;
                            }
                            if($next_level_to_purchase != '' && $next_level_to_purchase >9){
                                    $productGroupId = 3;
                            }
                        ?>
                            <div class="col-sm-12">
                              <?php if( (!empty($highest_level_purchased)) && ($next_level_to_purchase < 13)) { ?>
                                  <?php if($productGroupId != ''){ ?>
                                      <?php if(in_array($productGroupId, $eligibleProduct)) { ?>
                              <p><?php echo lang('app.language_site_learner_level_next_step_intro');?> <span style="color:#246c73;font-weight: bold;" id="next_course"></span>.</p>
                              <p style="margin-bottom:20px;"><?php echo lang('app.language_site_learner_level_next_step_by_ins');?></p>			
                                      <?php }else{ ?>
                                        <p style="margin-bottom:20px;"><?php echo lang('app.language_site_learner_level_handle_by_ins');?></p>
                                      <?php } ?>
                                  <?php }else{ ?>
                                      <p style="margin-bottom:20px;"><?php echo lang('app.language_site_learner_reached_top_level');?> </p>
                                  <?php } ?>              
                              <?php } else { ?>
                                      <p style="margin-bottom:20px;"><?php echo lang('app.language_site_learner_reached_top_level');?> </p>
                              <?php }  ?>				
                          </div>
                          <input type="hidden" name="highest_product_id" id="highest_product_id" value="<?php echo (isset($next_level_to_purchase) && $next_level_to_purchase != '' ) ? $next_level_to_purchase : ''; ?>" />		
                          <div class="col-sm-12 grid_box">
                              <?php if(in_array("Core", $eligibleProduct_name)) { ?>
                                  <div class="col-nine text-center">
                                          <div class="col-nine-cont <?php if( (!empty($highest_level_purchased)) /*&& ($highest_level_purchased < 9)*/ && ( $next_level_to_purchase == 1) ) { echo 'active'; }?>"><span>Step</span><span>Forward</span><span>1</span></div>
                                  </div>
                                  <div class="col-nine text-center">
                                          <div class="col-nine-cont <?php if( (!empty($highest_level_purchased)) /*&& ($highest_level_purchased < 9)*/ && ($next_level_to_purchase == 2) ) { echo 'active'; }?>"><span>Step</span><span>Forward</span><span>2</span></div>
                                  </div>
                                  <div class="col-nine text-center">
                                          <div class="col-nine-cont <?php if( (!empty($highest_level_purchased)) /*&& ($highest_level_purchased < 9)*/ && ($next_level_to_purchase == 3) ) { echo 'active'; }?>"><span>Step</span><span>Forward</span><span>3</span></div>
                                  </div>
                                  <div class="col-nine text-center">
                                          <div class="col-nine-cont <?php if( (!empty($highest_level_purchased)) /*&& ($highest_level_purchased < 9)*/ && ($next_level_to_purchase == 4) ) { echo 'active'; }?>"><span>Step</span><span>Up</span><span>1</span></div>
                                  </div>
                                  <div class="col-nine text-center">
                                          <div class="col-nine-cont <?php if( (!empty($highest_level_purchased)) /*&& ($highest_level_purchased < 9)*/ && ($next_level_to_purchase == 5) ) { echo 'active'; }?>"><span>Step</span><span>Up</span><span>2</span></div>
                                  </div>
                                  <div class="col-nine text-center">
                                          <div class="col-nine-cont <?php if( (!empty($highest_level_purchased)) /*&& ($highest_level_purchased < 9)*/ && ($next_level_to_purchase == 6) ) { echo 'active'; }?>"><span>Step</span><span>Up</span><span>3</span></div>
                                  </div>
                                  <div class="col-nine text-center">
                                          <div class="col-nine-cont <?php if( (!empty($highest_level_purchased)) /*&& ($highest_level_purchased < 9)*/ && ($next_level_to_purchase == 7) ) { echo 'active'; }?>"><span>Step</span><span>Ahead</span><span>1</span></div>
                                  </div>
                                  <div class="col-nine text-center">
                                          <div class="col-nine-cont <?php if( (!empty($highest_level_purchased)) /*&& ($highest_level_purchased < 9)*/ && ($next_level_to_purchase== 8) ) { echo 'active'; }?>"> <span>Step</span><span>Ahead</span><span>2</span></div>
                                  </div>
                                  <div class="col-nine text-center">
                                          <div class="col-nine-cont <?php if( (!empty($highest_level_purchased)) /*&& ($highest_level_purchased < 9)*/ && ($next_level_to_purchase == 9) ) { echo 'active'; }?>"> <span>Step</span><span>Ahead</span><span>3</span></div>
                                  </div>
                              <?php } ?>
                                  <?php if(in_array("Higher", $eligibleProduct_name)) { ?>
                                  <div class="col-nine text-center">
                                          <div class="col-nine-cont <?php if( (!empty($highest_level_purchased)) /*&& ($highest_level_purchased < 12)*/ && ($next_level_to_purchase == 10) ) { echo 'active'; }?>"> <span>Step</span><span>Higher</span><span>1</span></div>
                                  </div>
                                  <div class="col-nine text-center">
                                          <div class="col-nine-cont <?php if( (!empty($highest_level_purchased)) /*&& ($highest_level_purchased < 12)*/ && ($next_level_to_purchase == 11) ) { echo 'active'; }?>"> <span>Step</span><span>Higher</span><span>2</span></div>
                                  </div>
                                  <div class="col-nine text-center">
                                          <div class="col-nine-cont <?php if( (!empty($highest_level_purchased)) /*&& ($highest_level_purchased < 12)*/ && ($next_level_to_purchase == 12) ) { echo 'active'; }?>"> <span>Step</span><span>Higher</span><span>3</span></div>
                                  </div><div class="clearfix"></div>
                                  <?php } ?>
                          </div>
                        <div class="col-sm-12">
                            <?php if( (!empty($highest_level_purchased)) && ($next_level_to_purchase < 13)) { ?>
                                <?php if($productGroupId != ''){ ?>
                                    <?php if(in_array($productGroupId, $eligibleProduct)) { ?>
                                        <div id="book_now_dash" class="start-btn text_center">
                                                <a href="#" style="text-align:center" class="btn-main btn_cats"><?php echo 'Book Now'; ?></a>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
            <?php } ?>
        </div>

   </div>
</div>
<?php  } ?>
<!-- Modal for view practice test result -->
<div  class="modal fade practice-test-results" role="dialog" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog inner_modal1">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom: 0px;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>
    <!-- modal for Core final test result - STARTS-->
    <div  class="modal fade" id = "final-test-core-dash" role="dialog" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg inner_modal">
            <div class="modal-content">
                <div class="modal-header" style="border-bottom: 0px;">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>
    <!-- modal for Core final test result - ENDS-->

    <!-- Modal for view mobile app Link -->
    <div class="container">
        <div id="mobile-playstore-link-modal-dash" class="modal fade" role="dialog">
            <div class="modal-dialog modal-xs">
                <div class="modal-content">
                    <div class="modal-header" style="border-bottom: 0px solid #e5e5e5;">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" style="font-weight: bold; text-align: center;">&nbsp;<img class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" alt="Loading"/>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div class="modal-footer" style="border-top: 0px solid #e5e5e5;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal for view higher results - STARTS -->
    <div  class="modal fade" id = "final-test-higher" role="dialog" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="border-bottom: 0px;">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>
    <!-- modal for Core final test higher results - ENDS-->
</div>
<script>
    $(function () {
	
        
                //to show final test results WP-1279 - core & tds process
                $('a.final_result_core_dash').click(function (ev) {
                        ev.preventDefault();
                        var fetch_id = $(this).attr('id');
                        var display = "popup";
                        var from ="dash";
                        $.get("<?php echo site_url('site/core_certificate'); ?>" + '/' + fetch_id + '/' + display + '/' + from, function (html) {
                                $('#final-test-core-dash .modal-body').html(html);
                                $('#final-test-core-dash').modal('show', {backdrop: 'static'});
                        });
                });
                        /* book test/event */
                $('.booktest').click(function(e){
					console.log( $(this).attr('id'));
					$('#thirdpartyid').val($(this).attr('id'));
                    e.preventDefault();
                    $('#product_form').submit();
                });				
				/* pdf download */
				$('.pdfdownload').click(function(e) {				
					idval = $(this).attr("ID");
					pdf_val = $('#values'+idval).val();				
					window.location = "<?php echo site_url('site/result_pdf/?q='); ?>"+pdf_val;				
        		});
				//to show practice test results
				$('a.practice-test-button').click(function(ev){
					 ev.preventDefault();
					 var fetch_id = $(this).data('id').split('|');
					 $('#loading_modal'+fetch_id['2']+fetch_id['1']).show();
					 $.get("<?php echo site_url('site/gen_practicetest_result'); ?>"+'/'+  fetch_id['1'] +'/'+ fetch_id['2'], function(html){
						$('#loading_modal'+fetch_id['2']+fetch_id['1']).hide();
						$('.practice-test-results .modal-title').text(fetch_id['0']);
						$('.practice-test-results .modal-body').html(html);
						$('.practice-test-results').modal('show', {backdrop: 'static'});
					 });
				});
                                  //to show practice test results
                                $('button.practice-test-button').click(function (ev) {
                                    ev.preventDefault();

                                    var fetch_id = $(this).data('id').split('|');
                                    $('#loading_modal' + fetch_id['2'] + fetch_id['1']).show();
                                    $.get("<?php echo site_url('site/gen_practicetest_result'); ?>" + '/' + fetch_id['1'] + '/' + fetch_id['2'], function (html) {
                                        $('#loading_modal' + fetch_id['2'] + fetch_id['1']).hide();
                                        $('.practice-test-results .modal-title').text(fetch_id['0']);
                                        $('.practice-test-results .modal-body').html(html);
                                        $('.practice-test-results').modal('show', {backdrop: 'static'});
                                    });
                                });
                                
                                //to show TDS practice test results
                                $('button.practice-test-button-tds').click(function (ev) {
                                    ev.preventDefault();

                                    var fetch_id = $(this).data('id').split('|');
                                    $('#loading_modal' + fetch_id['1']).show();
                                    $.get("<?php echo site_url('site/gen_practicetest_result_tds'); ?>" + '/' + fetch_id['1'], function (html) {
                                            $('#loading_modal' + fetch_id['1']).hide();
                                            $('.practice-test-results .modal-title').text(fetch_id['0']);
                                            $('.practice-test-results .modal-body').html(html);
                                            $('.practice-test-results').modal('show', {backdrop: 'static'});
                                    });
                                });
                                
                                $('a.final_result_higher').click(function (ev) {
                                    ev.preventDefault();
                                    var fetch_id = $(this).attr('id');
                                    var token = $(this).attr('token');
                                    var display = "popup";
                                    $.get("<?php echo site_url('site/higher_certificate'); ?>" + '/' + fetch_id + '/' + token + '/' + display, function (html) {
                                            $('#final-test-higher .modal-body').html(html);
                                            $('#final-test-higher').modal('show', {backdrop: 'static'});
                                    });
                                });
                //WC-15 - Divert learners to app version for non-desktop access
                $('a.dash-mobile-popup-link').click(function(ev) {
                    ev.preventDefault();
                    var fetch_id = $(this).attr('id');
                    $.get("<?php echo site_url('site/play_store_link'); ?>" + '/' + fetch_id, function(html) {
                        $('#mobile-playstore-link-modal-dash .modal-body').html(html);
                        $('#mobile-playstore-link-modal-dash').modal('show', {
                            backdrop: 'static'
                        });
                    });
                });
               
				
	});
</script>

<script>

    $(document).ready(function() {
        tabScroll();
    });

    $(window).resize(function(){
        tabScroll();
    });

    function tabScroll(){          
        var listWidth = 0;                  
        $('.nav.nav-tabs li').each(function(){
            listWidth += $(this).outerWidth();
        })

        if($(window).width() < 768){
            $('.overflow_x .nav-tabs').width(listWidth)  ;
        }
        else{
            $('.overflow_x .nav-tabs').width('auto')  ;
        }
    }        

</script>
