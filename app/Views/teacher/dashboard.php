<?php 
use Carbon\Carbon; 
use App\Models\School\Schoolmodel;
use Config\Oauth;
    $this->request = \Config\Services::request();
    $this->schoolmodel = new Schoolmodel();
    $this->encrypter = \Config\Services::encrypter();
    $this->oauth = new \Config\Oauth(); 
	$this->yellowfin_access = $this->oauth->catsurl('yellowfin_access');
    $tz_to = $institutionTierId['timezone'];
?>
<style>
.disabled-icon{
	opacity: 0.5;
	margin-left: 1px;
}
.search_teacher table tr td:last-child span {
    color: #79b2d8;
}
.search_teacher .progress {
    margin-top: 5px;
}
</style>
<div class="bg-lightgrey"> 
    <div class="container">
		<div class="institution_page">
        <div class="row">
			<div class="mt20">
            <?php include_once 'messages.php'; ?>
			</div>
            <section class="col-sm-12">
              <?php if(!isset($_GET['view']) && !isset($_GET['class'])) : ?>
                 <h1 class="user_name"><?php echo lang('app.language_dashboard_welcome') . ', ' . ucfirst($this->session->get('user_firstname')." ".$this->session->get('user_lastname')); ?> </h1> 
               <?php endif; ?>

			<div class="institution_tab nav_dashboard teacher-dashboard mt40">
                     <?php if(isset($_GET['view']) && $_GET['view'] == 'learners' && isset($_GET['class'])) : ?>
                    <p class="blueTitle"><strong>Group:</strong> <?php if(!empty($teachingClass['englishTitle'])){
                        echo ucfirst($teachingClass['englishTitle']);
                    }else{}  ?></p>
                     <?php endif; ?>

                 <?php if(isset($_GET['view']) && $_GET['view'] == 'learners' && isset($_GET['class'])) : ?>
                    <p><a href="<?php echo site_url('teacher/dashboard'); ?>"><span class="fa fa-long-arrow-left"></span> <?php echo lang('app.language_search_events_back_to_dash'); ?></a></p>
                    <p style="font-size: 15px;"><?php echo lang('app.language_admin_institutions_add_sentences'); ?></p>		
                 <?php endif; ?>
                
                    <!-- Nav tabs -->
                    <?php if(isset($_GET['view']) && $_GET['view'] == 'learners' && isset($_GET['class'])) : ?>
                    <?php else: ?>
                          <ul class="nav nav-tabs" role="tablist">
                            <li id="tab_tea" role="presentation" class="<?php echo ($this->session->get('tab_classes')) ? 'active' : ''; ?>"><a href="#classes" id="tab_classes" aria-controls="classes" role="tab" data-toggle="tab"><?php echo lang('app.language_teacher_class_tab'); ?></a></li>
                            <li id="tab_rep" role="presentation" class="<?php echo ($this->session->get('tab_reports')) ? 'active' : ''; ?>"><a href="#report" id="tab_reports" aria-controls="profile1" role="tab" data-toggle="tab"><?php echo lang('app.language_teacher_report_tab'); ?></a></li>
                          </ul>
                     <?php endif; ?>
                    <!-- Tab panes -->
                    <div class="tab-content">
                       <div role="tabpanel" class="tab-pane <?php echo ($this->session->get('tab_classes')) ? 'active' : ''; ?>" id="test_classes">
                            <?php if(isset($_GET['view']) && $_GET['view'] == 'learners' && isset($_GET['class'])) : ?>
                             <div class="text-left mt20">
                                 <form class="form-inline" action="<?php echo site_url('teacher/dashboard'); ?>" id="searchForm" >
                                     <input type="hidden" name="view" value="learners"/>
                                     <input type="hidden" name="class" value="<?php echo (isset($_GET['class']) && $_GET['class']!='') ? $_GET['class'] : ''; ?>"/>
                                <div class="form-group" >
                                    <input maxlength="50"  type="text" placeholder="<?php echo lang('app.language_admin_institutions_enter_search_term'); ?>" name="search" class="form-control clearable search" id="search" value="<?php echo @$search_item; ?>">
                                </div>
                                <button type="submit" class="btn btn-success" ><?php echo lang('app.language_admin_institutions_search_btn'); ?></button>
                                <button type="button" id="clearLearnerBtn" class="btn btn-default" ><?php echo 'Clear'; ?></button>
                             </form>
                                
                            </div> <div class="clearfix"></div>
							<?php if ($this->session->get('failure')) { ?> 
									<p class="mb10 mt20" style="font-size:15px;"><?php echo session('failure'); ?></p>
							<?php } ?>
                            
                            <?php if(isset($searchData) && $searchData != FALSE && !empty($searchData)):?>
                                <p class="mb10 mt20" style="font-size:15px;">The following results match your search criteria of <strong><?php echo @$search_item; ?></strong></p>
<div class="institution_content">
                                <div class="table-responsive search_teacher">
                            <table  class="table table-bordered institution_table">
                            	  
                            	<?php $titles = 0; $count = 0; $total_searchData=count($searchData); ?>
	                            <?php foreach($searchData as $learner): 
                                   if(@get_search_thirdparty_count($learner->last_thirdparty_id) <= 1){
									   if($titles == 0){
										   $titles++;
                                ?> 
								<thead>
                            		<th></th>
                                    <th width="11%"><?php echo lang('app.language_tbl_label_token_id'); ?></th>
                            		<th><?php echo lang('app.language_teacher_toknpage_email_username'); ?></th>
                                    <th width="21%"><?php echo lang('app.language_teacher_level_progress'); ?></th>
									<th><?php echo lang('app.language_teacher_group_supervisor'); ?></th>
                                    <th><?php echo lang('app.language_admin_institutions_dob'); ?></th>
                                    <th><?php echo lang('app.language_tbl_label_test_results'); ?></th>
                            	</thead>
								<tbody>
								<?php } ?>
                                    <tr>
                                        <input type="hidden" id="teacher_class" name="teacher_class" value="<?php echo base64_encode($this->encrypter->encrypt($teachingClass['teacherClassId'])); ?>">
                                        <?php if(@in_array($learner->last_thirdparty_id, $student_associated_classes)){ ?>
                                            <td> </td>
                                        <?php } else { ?>
                                            <td>
                                                <input type="radio"  <?php echo (count($searchData) > 1) ? '' : 'checked';?> name="learner_id" <?php echo (@in_array($learner->last_thirdparty_id, $student_associated_classes)) ? 'disabled' : ''; ?>  data-thirdparty-id="<?php echo  base64_encode($this->encrypter->encrypt($learner->last_thirdparty_id)); ?>"  value="<?php echo base64_encode($this->encrypter->encrypt($learner->id)); ?>">
                                            </td>
                                            <?php } ?>
                                        <td><span><?php echo $learner->token == 'u13token' ? "N/A" : $learner->token?></span></td>
                                        <td> 
                                            <span><?php $user_name= ucfirst($learner->firstname).' '.$learner->lastname;$short_user_name = strlen($user_name) > 20 ? mb_substr($user_name,0,20)."..." : $user_name;?>
                                                <a href="#" data-toggle="tooltip" title="<?php echo $user_name; ?>"><?php echo $short_user_name; ?></a>
                                            </span><br>
                                            <span><?php if(empty($learner->token) || ($learner->token == 'u13token') ) { $data_user = $learner->username;  
                                                        } else { $data_user = $learner->email; } $short_user = strlen($data_user) > 25 ? substr($data_user,0,25)."..." : $data_user;?>	
                                                <a href="#" data-toggle="tooltip" title="<?php echo $data_user; ?>"><?php echo  $short_user; ?></a>
                                            </span>
                                        </td>
                                        <td>
                                            <span><?php echo $learner->level?></span>
                                            <span> <?php $progress = $learner->course_progress == NULL ? 0 : $learner->course_progress;?>
                                                <div class="" style="">
                                                    <div class="progress">
                                                        <div class="progress-bar active" role="progressbar" aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100" style="background-color: #99c8f1; width:<?php echo $progress; ?>%">
                                                            <span><?php echo round($progress); ?>%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </span>          
                                        </td>
                                        <td>
                                            <?php if(@in_array($learner->last_thirdparty_id, $student_associated_classes)){ ?>
                                                <span>
                                                    <a href="#" data-toggle="tooltip" title="<?php echo $learner->class_name; ?>">
                                                        <?php echo $short_user = strlen($learner->class_name) > 20 ? substr($learner->class_name,0,20)."..." : $learner->class_name;?>
                                                    </a>
                                                </span><br>
                                                <span>
                                                    <?php $teacher_name = $learner->TR_fname.'&nbsp;'.$learner->TR_lname;?> 
                                                    <a href="#" data-toggle="tooltip" title="<?php echo $teacher_name; ?>">
                                                        <?php echo $short_teacher = strlen($teacher_name) > 20 ? mb_substr($teacher_name,0,20)."..." : $teacher_name; ?>
                                                    </a>
                                                </span>
                                            <?php } else { ?>
                                                <?php echo lang('app.language_admin_institutions_not_allocated'); ?>
                                            <?php } ?>    
                                        </td>
                                        <td><?php echo date("d-M-Y", $learner->dob) ?> </td>
                                        <td> 
                                            <!-- practice Test Starts -->
                                            <?php if($learner->course_type != "Higher"){
                                                    if(isset($learner->practice_details->practiceresults_tds) && $learner->practice_details->practiceresults_tds != NULL ){
                                                        //TDS Practice Test Start
                                                        //WP-1308 Starts
                                                        $practice_search_label = isset($learner->practice_details->practice_count) && ($learner->practice_details->practice_count == 1) ? 'Practice Test' : 'Practice Test 1';
                                                        //Tds practice test 1
                                                        if(isset($learner->practice_details->practiceresults_tds['practice_test1'])){
                                                            $practice_test1 = $learner->practice_details->practiceresults_tds['practice_test1'];
                                                        }
                                                        if(!empty($practice_test1) && !empty($practice_test1['processed_data'])){
                                                            if($learner->course_type == "Primary"){ $percent = json_decode($practice_test1['processed_data']);
                                                                echo "<a href='#' style='text-decoration:none;cursor:default;pointer-events: none;'>Practice Test (" . $percent->overall->percentage . ")</a>";
													            echo "<br>";
                                                            }else{
                                                                echo '<div><a  style="display:inline-block;white-space: nowrap;" href="#" data-toggle="modal" data-target=".practice-test-results" data-backdrop="static" data-keyboard="false" 
                                                                data-id="'.$practice_search_label.'|' . $practice_test1['token'] . '" class="practice-test-button-tds">'.$practice_search_label.'</a>';
                                                                if(isset($practice_test1['audio_reponses']) && ($practice_test1['audio_reponses'] == 1)) { 
                                                                    if($practice_test1['audio_available']){?>
                                                                        <a href="<?php if(isset($practice_test1['url']) && !empty($practice_test1['url'])){ echo $practice_test1['url']; }?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Listen to practice test audio responses (opens in a new tab)"><em class="bi bi-volume-up-fill"></em></a>
                                                                    <?php }else{ 
                                                                        echo @disable_icon();
                                                                    }
                                                                }
                                                            }
                                                            echo '</div>';
                                                        }else{
                                                            echo '<span style="display:block;white-space: nowrap;">'.$practice_search_label.'</span>'; 
                                                        }//Tds practice test 1 ends
                                                        //Tds practice test 2
                                                        if(isset($learner->practice_details->practice_count) && $learner->practice_details->practice_count > 1){
                                                            if(isset($learner->practice_details->practiceresults_tds['practice_test2'])){
                                                                $practice_test2 = $learner->practice_details->practiceresults_tds['practice_test2'];
                                                            }
                                                            if(!empty($practice_test2) && !empty($practice_test2['processed_data'])){
                                                            echo '<div><a  style="display:inline-block;white-space: nowrap;" href="#" data-toggle="modal" data-target=".practice-test-results" data-backdrop="static" data-keyboard="false" 
                                                                    data-id="Practice Test 2|' . $practice_test2['token'] . '" class="practice-test-button-tds">Practice Test 2</a>'; 
                                                                    if(isset($practice_test2['audio_reponses']) && ($practice_test2['audio_reponses'] == 1)) { 
                                                                        if($practice_test2['audio_available']){?>
                                                                            <a href="<?php if(isset($practice_test2['url']) && !empty($practice_test2['url'])){ echo $practice_test2['url']; }?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Listen to practice test audio responses (opens in a new tab)"><em class="bi bi-volume-up-fill"></em></a>
                                                                        <?php }else{ 
                                                                            echo @disable_icon();
                                                                        }
                                                                    }
                                                                echo '</div>';
                                                            }else{
                                                            echo '<span style="display:block;white-space: nowrap;">Practice Test 2</span>'; 
                                                            }
                                                        }//Tds practice test 2
                                                        //TDS Practice Test Ends
                                                    }elseif (isset($learner->practice_details)) {
                                                        if($learner->course_type == "Primary"){
                                                            if(isset($learner->practice_details->collegepre_primary_results) && !empty($learner->practice_details->collegepre_primary_results)){
                                                                echo "<a href='#' style='text-decoration:none;cursor:default;pointer-events: none;'>Practice Test (" . $learner->practice_details->collegepre_primary_results . ")</a>";
                                                                echo "<br>";
                                                            }else{
                                                                echo '<span style="display:block;white-space: nowrap;"> Practice Test</span>'; 
                                                            }
                                                        }else{
                                                            // collegepre PT1
                                                            if (!empty($learner->practice_details->practiceresults['0'])) {
                                                                $PT1_value = $learner->practice_details->practiceresults['0'];
                                                                echo '<div><a  style="display:inline-block;white-space: nowrap;" href="#" data-toggle="modal" data-target=".practice-test-results" data-backdrop="static" data-keyboard="false" 
                                                                            data-id="Practice Test 1|' . $PT1_value['session_number'] . '|' . $PT1_value['thirdparty_id'] . '"
                                                                            class="practice-test-button">Practice Test 1</a>';
                                                                            echo @disable_icon();
                                                                            echo '</div>';
                                                            } else {
                                                                echo '<span style="display:block;white-space: nowrap;">Practice Test 1</span>';
                                                            }
                                                            // collegepre PT2
                                                            if (!empty($learner->practice_details->practiceresults['1'])) {
                                                                $PT2_value = $learner->practice_details->practiceresults['1'];
                                                                echo '<div><a  style="display:inline-block;white-space: nowrap;" href="#" data-toggle="modal" data-target=".practice-test-results" data-backdrop="static" data-keyboard="false" 
                                                                            data-id="Practice Test 2|' . $PT2_value['session_number'] . '|' . $PT2_value['thirdparty_id'] . '"
                                                                            class="practice-test-button">Practice Test 2</a>';
                                                                            echo @disable_icon();
                                                                            echo '</div>';
                                                            } else {

                                                                echo '<span style="display:block;white-space: nowrap;">Practice Test 2</span>';
                                                            }
                                                        }
                                                    } else {
                                                        //WP-1102 - Added condition based on core, To hide Practice Test links for products higher.
                                                        echo '<span style="display:block;white-space: nowrap;">Practice Test 1</span>';
                                                        echo '<span style="display:block;white-space: nowrap;">Practice Test 2</span>';
                                                    }
                                                } ?>
                                            <!-- practice Test Ends -->
                                            <span style="display:block;white-space: nowrap;">Final Test</span>
                                        </td>
                                        <?php $count++; ?>
                                    </tr>
	                            <?php } else { ?>
								<p class="mb10 mt20" style="font-size:15px;"><?php if($titles == 0 ){ $titles++; echo "No results were produced for the search term entered.";} ?></p>
								<?php } endforeach; ?>
								</tbody>
                            </table>
                                </div>
                            </div>


                              <div class="row">
                                <div class="col-md-12 text-left mb10 mt20">
                                <button type="button" <?php echo ($count == $total_searchData ) ? 'disabled' : '';?>  class="btn btn-sm btn-continue"  id="addLearnerBtn"><?php echo lang('app.language_teacher_classes_add_btn'); ?></button> <img alt="loading" class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" />
                            <?php else: ?>
                            
                            <?php endif; ?>
                                </div>
                                <div class="col-md-12 text-right mb10 mt10">
                                <button type="button" <?php echo ((!empty($learnersData['learners'])) && (!empty($class_learners))) ? '' : 'disabled' ?> class="btn btn-sm btn-continue"  id="deleteLearnerBtn"><?php echo lang('app.language_teacher_classes_remove_btn'); ?></button>
                                </div>
                              </div>



                            <!-- teacher classes-teacher -->
							<div class="institution_content">
                                <div class="table-responsive view-tokens mt30">
                                    <table class="table table-bordered institution_table">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th width="12%"><?php  	echo lang('app.language_tbl_label_token_id');?> </th>
                                                <th><?php if (!empty($class_learners) && count($class_learners) > 1) {  
                                                        $get_url = explode("?",$_SERVER['REQUEST_URI']); 
                                                        $without_email = explode('&name',$get_url['1']);?>
                                                    <?php echo anchor(current_url() ."?".$without_email['0']. "&name=" . (($this->request->getVar('name') == 'ASC') ? 'DESC' : 'ASC'), lang('app.language_teacher_toknpage_email_username') . (($this->request->getVar('name') == 'ASC') ? '&nbsp;<em class="fa fa-arrow-up" aria-hidden="true"></em>' : '&nbsp;<em class="fa fa-arrow-down" aria-hidden="true"></em>'), array('style' => 'color:white;')); ?>
                                                <?php } else { ?>
                                                    <?php echo lang('app.language_teacher_toknpage_email_username'); ?>
                                                <?php } ?>
                                            </th>
                                
                                                <th width="14%"><?php  	echo lang('app.language_tbl_label_level');?> </th>
                                                <th><?php echo 'Course Progress'; ?></th>
                                                <th><?php  	echo lang('app.language_tbl_label_test_booking');?> </th>
                                                <th><?php echo lang('app.language_tbl_label_test_results') ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if(!empty($class_learners)) {
                                                    $i = 0;											
                                                    foreach ($class_learners['class_learners'] as $class_learner) {									
                                            ?>									
                                            <tr>
                                                <td align="center">
                                 
                                            <!-- create id="studentClassId1" -->
                                                <input type="checkbox" class="studentClassId1" name="studentClassId1"  value="<?php echo $class_learner['studentClassId']; ?>"  />
                                                </td>										
                                                <td class="text-token">
                                                                <?php if ((!empty($class_learner['token']) || empty($class_learner['token'])) && ($class_learner['cats_product'] == "cats_core" || $class_learner['cats_product'] == "cats_primary") )  {
                                                                echo 'N/A';
                                                                } else {
                                                                echo $class_learner['token']; 
                                                                } ?></td>
                                                            <td>

                                                                <span style="display:block;white-space:nowrap;">
                                                                    <?php
                                                                    if ((!empty($class_learner['token']) || empty($class_learner['token'])) && ($class_learner['cats_product'] == "cats_core" || $class_learner['cats_product'] == "cats_primary") )  {
                                                                        $data_users = $class_learner['username'];
                                                                    } else {
                                                                    $data_users = $class_learner['email'];
                                                                    }
                                                                    $short_users = strlen($data_users) > 25 ? substr($data_users, 0, 25) . "..." : $data_users;
                                                                    ?>

                                                                <a href="#" data-toggle="tooltip" title="<?php echo $data_users; ?>">
                                                                    <?php echo $short_users; ?></a> 
                                                        </span>
                                                        <span style="display:block;white-space:nowrap;">

                                                            <a href="#" data-toggle="tooltip" title="<?php echo $class_learner['firstname'].' '.$class_learner['lastname']; ?>">

                                                <?php echo $short_first_last = strlen($class_learner['firstname'].' '.$class_learner['lastname']) > 25 ? substr($class_learner['firstname'].' '.$class_learner['lastname'],0,25)."..." : $class_learner['firstname'].' '.$class_learner['lastname'];?></a> 


                                                        </span>									
                                                
                                                </td>

                                                <td><?php if($class_learner['productname']) { echo $class_learner['productname'];} else { echo 'Not available';} ?>
                                                <?php if(isset($class_learner['num_history_results']) && $class_learner['num_history_results'] > 1): ?>
                                                                <br/><a href="#" style="text-decoration: underline;" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#history_modal" id="<?php echo base64_encode($this->encrypter->encrypt($class_learner['id'])); ?>" class="history_link">View history</a>
                                                        <?php endif; ?></td>
                                                <td style="">
                                                                <?php if ($class_learner['thirdparty_id'] != '' && $class_learner['thirdparty_id'] > 0 && $class_learner['course_progress'] != NULL) { ?>
                                                                    <div class="" style="">
                                                                        <div class="progress">
                                                                            <div class="progress-bar active" role="progressbar" aria-valuenow="<?php echo $class_learner['course_progress']; ?>" aria-valuemin="0" aria-valuemax="100" style="background-color: #99c8f1; width:<?php echo $class_learner['course_progress']; ?>%">
                                                                                <span><?php echo round($class_learner['course_progress']); ?>%</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <?php
                                                                } else {
                                                                    echo lang('app.language_school_label_not_available');
                                                                    ?>                          
                                                                <?php } ?>                          
                                                            </td> 					
                                                <td>
                                                    <?php 

                                                        if($class_learner['booking_status'] == 1 && $class_learner['start_date_time']) { 
                                                            $institution_zone_values = @get_institution_zone_from_utc($tz_to, $class_learner['start_date_time'], $class_learner['end_date_time']);																			
                                                        $dt = $institution_zone_values['institute_event_date'];
                                                            echo $dt.' '.$class_learner['city'] ;
                                                        } else {
                                                            echo lang('app.language_school_label_not_available');
                                                        }
                                                    ?>											
                                                </td>
                                                <td>
                                                    <?php  if($class_learner['cats_product'] == 'cats_primary'){
                                                        if (isset($class_learner['practice_test']) && $class_learner['practice_test']['practice_test'] == 1) {
                                                        echo "<a href='#' style='text-decoration:none;cursor:default;pointer-events: none;'>Practice Test (" . $class_learner['practice_test']['percent']['percentage'] . ")</a>";
                                                        echo "<br>";
                                                    } elseif (isset($class_learner['practice_test_tds']['tds_practice_test']) && $class_learner['practice_test_tds']['tds_practice_test'] == 1) {
                                                        echo "<a href='#' style='text-decoration:none;cursor:default;pointer-events: none;'>Practice Test (" . $class_learner['practice_test_tds']['percent']['percentage'] . ")</a>";
                                                        echo "<br>";
                                                    }else {
                                                        echo '<span style="display:block;white-space: nowrap;">Practice Test</span>';
                                                    }
                                                    if (isset($class_learner['final_test']) && $class_learner['final_test']['final_result_status'] == 1) {
                                                        echo "<a href='javascript:void(0)' class='primary_results' id='" . base64_encode($this->encrypter->encrypt($class_learner['id'])) . "'data-thirdpartyid='" . base64_encode($this->encrypter->encrypt($class_learner['thirdparty_id'])) . "'>Final Test</a>";
                                                        echo  !empty($class_learner['final_test']['percent']['percentage'])? "  (". $class_learner['final_test']['percent']['percentage'] .")" : "(0%)"; 
                                                    } else {

                                                        echo '<span style="display:block;white-space: nowrap;">Final Test</span>';
                                                    }
                                                }else{
                                                    if($class_learner['productid'] <= 9){
                                                        
                                                        //Tds Practice Test Starts
                                                        if(isset($class_learner['practice_test_tds']) && $class_learner['practice_test_tds'] != NULL ){

                                                            //WP-1308 Starts
                                                            if(isset($class_learner['practice_count']) && $class_learner['practice_count'] == 1){
                                                                $practice_label = 'Practice Test';
                                                            }else{
                                                                $practice_label = 'Practice Test 1';
                                                            }

                                                            //Tds practice test 1
                                                            if(isset($class_learner['practice_test_tds']['practice_test1'])){
                                                                $practice_test1 = $class_learner['practice_test_tds']['practice_test1'];
                                                            }
                                                            if(!empty($practice_test1) && !empty($practice_test1['processed_data'])){
                                                            echo '<div><a  style="display:inline-block;white-space: nowrap;" href="#" data-toggle="modal" data-target=".practice-test-results" data-backdrop="static" data-keyboard="false" 
                                                                    data-id="'.$practice_label.'|' . $practice_test1['token'] . '" class="practice-test-button-tds">'.$practice_label.'</a>'; 
                                                                    if(isset($practice_test1['audio_reponses']) && ($practice_test1['audio_reponses'] == 1)) { 
                                                                        if($practice_test1['audio_available']){?>
                                                                            <a href="<?php if(isset($practice_test1['url']) && !empty($practice_test1['url'])){ echo $practice_test1['url']; }?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Listen to practice test audio responses (opens in a new tab)"><em class="bi bi-volume-up-fill"></em></a>
                                                                        <?php }else{ 
                                                                            echo @disable_icon();
                                                                        }
                                                                    }
                                                                    echo '</div>';
                                                                }else{
                                                            echo '<span style="display:block;white-space: nowrap;">'.$practice_label.'</span>'; 
                                                            }
                                                            //Tds practice test 2
                                                            if(isset($class_learner['practice_count']) && $class_learner['practice_count'] > 1){
                                                                if(isset($class_learner['practice_test_tds']['practice_test2'])){
                                                                    $practice_test2 = $class_learner['practice_test_tds']['practice_test2'];
                                                                }
                                                                if(!empty($practice_test2) && !empty($practice_test2['processed_data'])){
                                                                echo '<div><a  style="display:inline-block;white-space: nowrap;" href="#" data-toggle="modal" data-target=".practice-test-results" data-backdrop="static" data-keyboard="false" 
                                                                        data-id="Practice Test 2|' . $practice_test2['token'] . '" class="practice-test-button-tds">Practice Test 2</a>'; 
                                                                        if(isset($practice_test2['audio_reponses']) && ($practice_test2['audio_reponses'] == 1)) { 
                                                                            if($practice_test2['audio_available']){?>
                                                                            <a href="<?php if(isset($practice_test2['url']) && !empty($practice_test2['url'])){ echo $practice_test2['url']; }?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Listen to practice test audio responses (opens in a new tab)"><em class="bi bi-volume-up-fill"></em></a>
                                                                            <?php }else{ 
                                                                                echo @disable_icon();
                                                                            }
                                                                        }
                                                                    echo '</div>';
                                                                }else{
                                                                echo '<span style="display:block;white-space: nowrap;">Practice Test 2</span>'; 
                                                                }
                                                            }
                                                        }
                                                        //Collegepre Practice Test
                                                    elseif (isset($class_learner['practice_test']) && isset($class_learner['thirdparty_id'])) {
                                                        if (!empty($class_learner['practice_test']['practice_test1']) && $class_learner['practice_test']['practice_test1'] == 1) {
                                                            echo '<div><a  style="display:inline-block;white-space: nowrap;" href="#" data-toggle="modal" data-target=".practice-test-results" data-backdrop="static" data-keyboard="false" 
                                                                        data-id="Practice Test 1|' . $class_learner['practice_test']['session_number1'] . '|' . $class_learner['thirdparty_id'] . '"
                                                                        class="practice-test-button">Practice Test 1</a>';
                                                                        echo @disable_icon();
                                                                        echo '</div>';
                                                        } else {
                                                            echo '<span style="display:block;white-space: nowrap;">Practice Test 1</span>';
                                                        }



                                                        if (!empty($class_learner['practice_test']['practice_test2']) && $class_learner['practice_test']['practice_test2'] == 1) {
                                                            echo '<div><a  style="display:inline-block;white-space: nowrap;" href="#" data-toggle="modal" data-target=".practice-test-results" data-backdrop="static" data-keyboard="false" 
                                                                        data-id="Practice Test 2|' . $class_learner['practice_test']['session_number2'] . '|' . $class_learner['thirdparty_id'] . '"
                                                                        class="practice-test-button">Practice Test 2</a>';
                                                                        echo @disable_icon();
                                                                        echo '</div>';
                                                        } else {

                                                            echo '<span style="display:block;white-space: nowrap;">Practice Test 2</span>';
                                                        }
                                                    } else {
                                                        //WP-1102 - Added condition based on core, To hide Practice Test links for products higher.
                                                        if(($class_learner['productid']) <= 9) {
                                                            echo '<span style="display:block;white-space: nowrap;">Practice Test 1</span>';
                                                            echo '<span style="display:block;white-space: nowrap;">Practice Test 2</span>'; 
                                                        }
                                                    }  
                                                    }
                                                
                                                    if (isset($class_learner['final_test']) && ($class_learner['final_test']['final_result_status'] == 1)) {
                                                        if($class_learner['product_course_type'] === 'Higher'){
                                                            $link = FALSE;
                                                            if(isset($class_learner['final_test']['final_result_token'])){
                                                                $link = isset($class_learner['final_test']['final_result_higherdata']) && !empty($class_learner['final_test']['final_result_higherdata']) ? TRUE : FALSE;
                                                                $url = site_url('teacher/higher_certificate') . "/" . $class_learner['final_test']['final_result_candidate_id']. "/" . $class_learner['final_test']['final_result_token'];
                                                            }else{
                                                                $link = isset($class_learner['final_test']['final_result_higherdata']) && !empty($class_learner['final_test']['final_result_higherdata']) ? TRUE : FALSE;
                                                                $url = site_url('teacher/higher_certificate') . "/" . $class_learner['final_test']['final_result_candidate_id'];
                                                            }
                                                            echo  $link ? "<a href='" . $url . "' target='_blank' data-toggle='tooltip' data-placement='bottom' title='Final Test (opens in a new tab)'>Final Test</a>" : '<span style="display:block;white-space: nowrap;";>Final Test</span>';
                                                            if(isset($class_learner['final_test']['audio_reponses']) && ($class_learner['final_test']['audio_reponses'] == 1)) { 
                                                                if($class_learner['final_test']['audio_available']){ ?>
                                                                    <a href="<?php if(isset($class_learner['final_test']['url']) && !empty($class_learner['final_test']['url'])){ echo $class_learner['final_test']['url']; }?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Listen to final test audio responses (opens in a new tab)"><em class="bi bi-volume-up-fill"></em></a>
                                                                <?php }else{ 
                                                                    echo @disable_icon();
                                                                }
                                                            }
                                                        }else{
                                                            echo "<a href='" . site_url('teacher/core_certificate') . "/" . $class_learner['final_test']['final_result_candidate_id'] . "' target='_blank' data-toggle='tooltip' data-placement='bottom' title='Final Test (opens in a new tab)'>Final Test</a>";
                                                            if(isset($class_learner['final_test']['audio_reponses']) && ($class_learner['final_test']['audio_reponses'] == 1)) { 
                                                                if($class_learner['final_test']['audio_available']){ ?>
                                                                    <a href="<?php if(isset($class_learner['final_test']['url']) && !empty($class_learner['final_test']['url'])){ echo $class_learner['final_test']['url']; }?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Listen to final test audio responses (opens in a new tab)"><em class="bi bi-volume-up-fill"></em></a>
                                                                <?php }else{
                                                                    echo @disable_icon();
                                                                }
                                                            }
                                                        }
                                                        
                                                    } else {
                                                        echo '<span style="display:block;white-space: nowrap;">Final Test</span>';
                                                    }   
                                                    }?></td>
                                            <?php
                                                    $i++;
                                                }
                                                } else {
                                                ?>
                                                <tr>
                                                    <td colspan="6">
                                                        <div class="alert alert-danger fade in">
                                                        <a href="#" class="close" data-dismiss="alert">&times;</a>
                                                        <?php echo lang('app.language_school_no_u13learner'); ?>
                                                        </div>
                                                    </td>
                                                </tr>                                       
                                                <?php } ?> 											
                                        </tbody>
                                    </table> 
                                </div>
 
                                <div class="institution_pagination">
                                    <nav class="text-right">
   									

                                    <?php if ($class_learners['pager']) :?>
									<?= $class_learners['pager']->links('pagination_classleaners') ?>
									<?php endif ?> 
                                </nav>
							</div>
                            </div>
                            <!--Ends teacher classes-teacher table-->
   
                           <?php else: ?>
                             <div class="text-right mt30">
                                <label class="checkbox-inline"><input type="checkbox" name="status" <?php if($this->session->get('class_status') == '1'){ echo 'checked'; }elseif($this->session->get('class_status') == '0'){ echo ''; }else { echo 'checked';} ?> value="<?php echo 'active'; ?>"><?php echo 'Show only active Groups'; ?></label>
                                <button type="button" class="btn btn-sm btn-continue"  data-toggle="modal"  data-backdrop="static" data-keyboard="false"  data-target="#addupdateModal" id="addBtn"><em class="fa fa-plus fa-fw"></em><?php echo lang('app.language_teacher_classes_add_btn'); ?></button>
                                <button type="button" <?php echo (empty($classesData['classes'])) ? 'disabled' : '' ?> class="btn btn-sm btn-continue"  data-toggle="modal" data-backdrop="static" data-keyboard="false"  data-target="#updateaddModal" id="editBtn"><em class="fa fa-edit fa-fw"></em><?php echo lang('app.language_teacher_classes_viewedit_btn'); ?></button>
                                <button type="button" <?php echo (empty($classesData['classes'])) ? 'disabled' : '' ?> class="btn btn-sm btn-continue"  id="deleteBtn"><em class="fa fa-trash-o fa-fw"></em><?php echo lang('app.language_teacher_classes_delete_btn'); ?></button>
                            </div>
							<div class="institution_content">
							<div id="dashboard-view" class="">
                            <div class="table-responsive mt40">
                                <table class="table table-bordered institution_table">
                                    <thead>
                                        <tr> 
                                            <th>&nbsp;</th>
                                            <th><?php echo lang('app.language_teacher_class_label_name'); ?></th>
                                            <th><?php echo lang('app.language_teacher_class_label_date_created'); ?></th>
                                            <th><?php echo lang('app.language_teacher_class_label_no_in_class'); ?></th>
                                            <th><?php echo lang('app.language_teacher_class_label_class_status'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                                <?php
                                                if (!empty($classesData['classes'])) {
                                                     $i = 0;
                                                    foreach ($classesData['classes'] as $class) {
                                                        $student_count = $this->schoolmodel->record_class_learners_count($class->teacherClassId); // Student count for a class - WP-1177
                                                        $learnerUrl = ($student_count) > 0 ? ' View/add learners' : ' Add learners';
                                                        ?>
                                                <tr>
                                                    <td align="center"><input type="radio" name="class_id" data-numberinclass="<?php echo $student_count; ?>" id="classnumbers_<?php echo $student_count; ?>"  value="<?php echo $class->classId; ?>" <?php echo ($i == 0) ? 'checked="checked"' : ''; ?>  /></td>


                                                    <td>
                                                 <a href="#" data-toggle="tooltip" title="<?php echo $class->englishTitle; ?>">
											<?php echo $short_class_name = strlen($class->englishTitle) > 50 ? substr($class->englishTitle,0,50)."..." : $class->englishTitle;?></a>     	

                                                

                                                    </td>


                                                    <td><?php echo date("d-m-Y", $class->date_created);  ?></td>
                                                    <td><span class="badge"><?php echo $student_count; ?></span>&nbsp;<a style="text-decoration: underline;" href="<?php echo site_url('teacher/dashboard').'/?view=learners&class='.base64_encode($class->classId).'&search='; ?>"><?php echo $learnerUrl; ?></a></td>
                                                    <td><?php echo ($class->status == 1) ? 'Active' : 'Inactive'; ?></td>
                                                    </tr>	
                                                <?php $i++; }
    
} else { ?>										
                                            <tr>
                                                <td colspan="6">
                                                    <div class="alert alert-danger fade in">
                                                        <a href="#" class="close" data-dismiss="alert">&times;</a>
    <?php echo lang('app.language_teacher_no_classes'); ?>
                                                    </div>
                                                </td>
                                            </tr>										
<?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="institution_pagination">
                                <nav class="text-right">
									   <?php if ($classesData['pager']) :?>
									<?= $classesData['pager']->links('pagination_groups') ?>
									<?php endif ?> 
                                </nav>
							</div>
							</div>
							</div>
                           <?php endif ?>
                        </div>
                       <?php $report_session = $this->session->get('tab_reports');
                         if(isset($report_session) && $report_session == 1){ ?>
                            <div role="tabpanel" class="tab-pane <?php echo ($this->session->get('tab_reports')) ? 'active' : ''; ?>" id="report">
                                <?php if(isset($this->yellowfin_access) && $this->yellowfin_access == 1){ ?> 
                                <section class="col-sm-12">
                                    <p><span class="glyphicon glyphicon-alert"></span><?php echo lang('app.language_yellowfin_report_tab_info'); ?></p>
                                    <button type="button" class="btn btn-sm btn-continue" id="teacher_report" style="margin: 0 auto; clear: both; display: block; padding: 0px 50px; margin-top: 30px;"><?php echo lang('app.language_yellowfin_click_report_btn_txt'); ?></button>
                                </section>
                                <?php } else { ?>
                                <div class="alert alert-danger fade in">
                                    <a href="#" class="close" data-dismiss="alert">&times;</a><?php echo 'Report currently unavailable!'; ?>
                                </div>
                                <section class="col-sm-12">
                                    <p><span class="glyphicon glyphicon-alert"></span><?php echo lang('app.language_yellowfin_report_tab_info'); ?></p>
                                    <button type="button" class="btn btn-sm btn-continue" disabled style="margin: 0 auto; clear: both; display: block; padding: 0px 50px; margin-top: 30px;"><?php echo lang('app.language_yellowfin_click_report_btn_txt'); ?></button>
                                </section>
                                <?php } ?>
                            </div>
                       <?php } ?>	
                    </div>					
                </div> 
            </section>
        </div>
		</div>
    </div>
 
</div>

<!-- Modal for view practice test result -->
    <div  class="modal fade practice-test-results" role="dialog" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="border-bottom: 0px;">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>	
	
<div class="container">
    <div id="addupdateModal" class="modal fade" role="dialog" >
        <div class="modal-dialog modal-md">
            <div class="modal-content" >
                <div class="modal-header" style="border-bottom: 0px solid #e5e5e5;">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"
                        style="font-weight: bold; text-align: center;">

                        &nbsp;<img alt="loading" class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" />
                </h4>
                </div>
                <div class="modal-body" >

                </div>
                <div class="modal-footer" style="border-top: 0px solid #e5e5e5;"></div>
            </div>
        </div>
    </div>

</div>

<div class="container">
    <div id="updateaddModal" class="modal fade" role="dialog" >
        <div class="modal-dialog modal-md">
            <div class="modal-content" >
                <div class="modal-header" style="border-bottom: 0px solid #e5e5e5;">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title" style="font-weight: bold; text-align: center;">&nbsp;<img alt="loading" class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" /></h4>
                </div>
                <div class="modal-body" >

                </div>
                <div class="modal-footer" style="border-top: 0px solid #e5e5e5;"></div>
            </div>
        </div>
    </div>

</div>
<!-- Modal -->
<div id="history_modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo lang('app.language_school_learners_history'); ?></h4>
      </div>
      <div class="modal-body">
          <img alt="loading" class="loading_history text-center" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" />
      </div>
       <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Done</button>
       </div>
    </div>

  </div>
</div>

<form action="<?php echo site_url('teacher/primary_final_result'); ?>" id="primary_results_form" name="primary_results_form" method="POST">
    <input type="hidden" name="u13id" id="u13id" value="">
    <input type="hidden" name="u13thirdpartyid" id="u13thirdpartyid" value="">
</form>

<script>
    
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip({
    'selector': '',
    'placement': 'top',
    'container':'body'
  });
   $('.primary_results').on('click', function (e) { 
    	$('#u13id').val('');
        $('#u13id').val($(this).attr("ID"));
        $('#u13thirdpartyid').val('');
        $('#u13thirdpartyid').val($(this).attr("data-thirdpartyid"));
        $('#primary_results_form').submit();
    });
});
// enabling add button in teacher add leaner page
    $('input:radio[name=learner_id]').prop('checked', false);
    $('#addLearnerBtn').attr('disabled', 'true');
    $('input:radio[name=learner_id]').click(function () {
        var orderid = $('input[type="radio"][name="learner_id"]:checked').val();
        if (typeof orderid === "undefined") {
        } else {
            $('#addLearnerBtn').removeAttr('disabled');
        }
    });
    
</script>
<script>
    $(document).ready(function(){
        $('#teacher_report').on('click',function(e){
            location.href = "<?php echo site_url('report/index'); ?>";
        });
    });
</script>
<?php if(isset($this->zendesk_access) && $this->zendesk_access == 1){  ?>
	<!-- Start of cats66 Zendesk Widget script WP-1393 -->
	<script type="text/javascript">
		window.zESettings = {
			webWidget: {
				authenticate: {
					jwtFn: function(callback) {
						callback('<?php echo @get_web_widget_token($this->session->get('user_id'));?>');
					}
				}
			}
		};
	</script>
	<script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=7be50752-0a2f-49d7-95b8-873e359217de"> </script>
    <script type="text/javascript">
  		zE('webWidget', 'helpCenter:setSuggestions', { search: 'dashboard' });
	</script>
<?php }  ?>
