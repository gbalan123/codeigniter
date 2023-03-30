<?php 
	use Carbon\Carbon;
	use App\Models\School\Schoolmodel;
	use App\Models\Admin\Cmsmodel;
	use App\Models\School\Eventmodel;
	use Config\MY_Lang;
	use Config\Oauth;

	//initialization of request
	$this->request = \Config\Services::request();
	$this->schoolmodel = new Schoolmodel();
	$this->Cmsmodel = new Cmsmodel();
	$this->encrypter = \Config\Services::encrypter();
	$this->eventmodel = new Eventmodel();
	$this->oauth = new \Config\Oauth(); 
	$this->yellowfin_access = $this->oauth->catsurl('yellowfin_access');
	helper('percentage_helper');
	$tz_to = $institutionTierId['timezone'];
	$get_language = $this->Cmsmodel->get_language();
?>
<?php 

if($this->session->getFlashdata('result_data') !== null){
	if(isset($this->session->getFlashdata('result_data')['product_type'])){
	$product_type = $this->session->getFlashdata('result_data')['product_type'];
	}
	if(isset($this->session->getFlashdata('result_data')['result_startdate'])){
	$result_startdate = $this->session->getFlashdata('result_data')['result_startdate'];
	}
	if(isset($this->session->getFlashdata('result_data')['result_enddate'])){
	$result_enddate = $this->session->getFlashdata('result_data')['result_enddate'];
	}
	if(isset($this->session->getFlashdata('result_data')['result_type'])){
	$result_type = $this->session->getFlashdata('result_data')['result_type'];
	}
}
?>
<style>
/* CSS for Result  PDF download - WP-1249*/ 
#results_tds .date_picker .form-group{
    margin-bottom : 5px;
}
#results_tds .date_picker p{
    font-size: 12px;
    font-weight: bold;
    margin: 0;
    color: #5d5757;
}
img.iconPlus {
    margin-bottom: -3.5px;
}
.disabled-icon{
	opacity: 0.5;
	margin-left: 1px;
}
</style>
<!--school dashboard-->
<div class="bg-lightgrey">
    <?php if (!empty($show_view)) { ?>
	<div class="container">
		<div class="row p20">
			<?php include_once 'messages.php'; ?>
			<section class="col-sm-12">
				<h1 class="user_name"><?php echo lang('app.language_dashboard_welcome') . ', ' . ucfirst($this->session->get('user_firstname')." ".$this->session->get('user_lastname')); ?> </h1>
				<p class="p20"><a href="<?php echo site_url('school/dashboard'); ?>"><span class="fa fa-long-arrow-left"></span> <?php echo lang('app.language_search_events_back_to_dash'); ?></a></p>
				<div class="institution_tab nav_dashboard">
					<p class="text-center">
						<?php if (!empty($tokens)) { ?>                     
							<strong>Order: </strong><?php echo $class_name; ?>
						<?php } ?>
					</p>
					<!-- table school-teacher -->
					<div class="institution_content">
					<div class="table-responsive view-tokens">
						<table class="table table-bordered institution_table">
						<thead>
							<tr>
								<th width="12%"><?php echo lang('app.language_tbl_label_token_id'); ?> </th>
								<th><?php if (!empty($class_learners) && count($class_learners) > 1) {  
											$get_url = explode("?",$_SERVER['REQUEST_URI']); 
											$without_email = explode('&name',$get_url['1']);?>
										<?php echo anchor(current_url() ."?".$without_email['0']. "&name=" . (($this->request->getGet('name') == 'ASC') ? 'DESC' : 'ASC'), lang('app.language_teacher_toknpage_email_username') . (($this->request->getGet('name') == 'ASC') ? '&nbsp;<i class="fa fa-arrow-up" aria-hidden="true"></i>' : '&nbsp;<i class="fa fa-arrow-down" aria-hidden="true"></i>'), array('style' => 'color:white;')); ?>
									<?php } else { ?>
										<?php echo lang('app.language_teacher_toknpage_email_username'); ?>
									<?php } ?>
								</th>
								<th width="14%"><?php echo lang('app.language_tbl_label_level'); ?> </th>
								<th><?php echo 'Course Progress'; ?></th>
								<th><?php echo lang('app.language_tbl_label_test_booking'); ?> </th>
								<th><?php echo lang('app.language_tbl_label_test_results') ?></th>
							</tr>
						</thead>
						<tbody>
						<?php

					if (!empty($class_learners)) {
						$i = 0;
						foreach ($class_learners['class_learners'] as $class_learner) {
						?>
						<td class="text-token">
						<?php if ((!empty($class_learner['token']) || empty($class_learner['token'])) && ($class_learner['cats_product'] == "cats_core" || $class_learner['cats_product'] == "cats_primary") )  {
						   echo 'N/A';
						} else {
						   echo $class_learner['token']; 
						} ?></td>
						<td>
							<span style="display:block;white-space:nowrap;">
								<?php
								if ((!empty($class_learner['token']) || empty($class_learner['token'])) && ($class_learner['cats_product'] == "cats_core" || $class_learner['cats_product'] == "cats_primary")) {
									$data_users = $class_learner['username'];
								} else {
									$data_users = $class_learner['email'];
								}
								$short_users = strlen($data_users) > 25 ? substr($data_users, 0, 25) . "..." : $data_users;
								?>

								<a href="#"  data-toggle="tooltip" title="<?php echo $data_users; ?>">
									<?php echo $short_users; ?></a> 
							</span>
							<span style="display:block;white-space:nowrap;">

								<a href="#"  data-toggle="tooltip" title="<?php echo $class_learner['firstname'] . ' ' . $class_learner['lastname']; ?>">

									<?php echo $short_first_last = strlen($class_learner['firstname'] . ' ' . $class_learner['lastname']) > 25 ? substr($class_learner['firstname'] . ' ' . $class_learner['lastname'], 0, 25) . "..." : $class_learner['firstname'] . ' ' . $class_learner['lastname']; ?></a> 
							</span>									

						</td>
						<td><?php
							if (isset($class_learner['productname'])) {
								echo $class_learner['productname'];
							} else {
								echo lang('app.language_school_label_not_available');
							}
							?>
							<?php if (isset($class_learner['num_history_results']) && $class_learner['num_history_results'] > 1): ?>
								<br/><a href="#" style="text-decoration: underline;" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#history_modal" id="<?php echo base64_encode($this->encrypter->encrypt($class_learner['id'])); ?>" class="history_link">View history</a>
							<?php endif; ?>
						</td>
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
						
						<td> <?php
							if(isset($class_learner['booking_status']) && $class_learner['booking_status'] != NULL ){
								if ($class_learner['booking_status'] == 1 && $class_learner['start_date_time']) {


									$institution_zone_values = @get_institution_zone_from_utc($tz_to, $class_learner['start_date_time'], $class_learner['end_date_time']);	

                                    $dt = $institution_zone_values['institute_event_date'];
									echo $dt.' '.$class_learner['city'] ;
									?>
									<?php if($class_learner['event_status'] == 1 ){ ?>
										<br><a style="text-decoration: underline;" href="<?php echo site_url();?>school/learner_allocation/<?php echo $class_learner['event_id']?>">View</a>
									<?php } ?>
						<?php 	} else {
									echo lang('app.language_school_label_not_available');
								}
							}else {
								echo lang('app.language_school_label_not_available');
							}
							?> 
						</td>
						<td>
							<?php
							if ($class_learner['cats_product'] == 'cats_primary') {
								if (isset($class_learner['practice_test']) && $class_learner['practice_test']['practice_test'] == 1) {
									echo "<a href='#' style='text-decoration:none;cursor:default;pointer-events: none;'>Practice Test (" . $class_learner['practice_test']['percent']['percentage'] . ")</a>";
									echo "<br>";
								}elseif (isset($class_learner['practice_test_tds']['tds_practice_test']) && $class_learner['practice_test_tds']['tds_practice_test'] == 1) {
									echo "<a href='#' style='text-decoration:none;cursor:default;pointer-events: none;'>Practice Test (" . $class_learner['practice_test_tds']['percent']['percentage'] . ")</a>";
									echo "<br>";
								} else {
									echo '<span style="display:block;white-space: nowrap;">Practice Test</span>';
								}
								if (isset($class_learner['final_test']) && $class_learner['final_test']['final_result_status'] == 1) {
									echo "<a href='javascript:void(0)' class='primary_results' id='" .base64_encode($this->encrypter->encrypt($class_learner['id'])) . "'data-thirdpartyid='" .base64_encode($this->encrypter->encrypt($class_learner['thirdparty_id'])). "'>Final Test</a>";
									 echo  !empty($class_learner['final_test']['percent']['percentage'])? "  (". $class_learner['final_test']['percent']['percentage'] .")" : "(0%)";
								} else {

									echo '<span style="display:block;white-space: nowrap;">Final Test</span>';
								}
							} else { 
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
														<a href="<?php if(isset($practice_test1['url']) && !empty($practice_test1['url'])){ echo $practice_test1['url']; }?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Listen to practice test audio responses (opens in a new tab)"><i class="bi bi-volume-up-fill"></i></a>
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
															<a href="<?php if(isset($practice_test2['url']) && !empty($practice_test2['url'])){ echo $practice_test2['url']; }?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Listen to practice test audio responses (opens in a new tab)"><i class="bi bi-volume-up-fill"></i></a>
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
									} //Collegepre Practice Test Ends
										else {
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
											$url = site_url('school/higher_certificate') . "/" . $class_learner['final_test']['final_result_candidate_id']. "/" . $class_learner['final_test']['final_result_token'];
										}else{
											$link = isset($class_learner['final_test']['final_result_higherdata']) && !empty($class_learner['final_test']['final_result_higherdata']) ? TRUE : FALSE;
											$url = site_url('school/higher_certificate') . "/" . $class_learner['final_test']['final_result_candidate_id'];
										}
										echo  $link ? "<a href='" . $url . "' target='_blank' data-toggle='tooltip' data-placement='bottom' title='Final Test (opens in a new tab)'>Final Test</a>" : '<span style="display:block;white-space: nowrap;";>Final Test</span>';
										if(isset($class_learner['final_test']['audio_reponses']) && ($class_learner['final_test']['audio_reponses'] == 1)) { 
											if($class_learner['final_test']['audio_available']){ ?>
													<a href="<?php if(isset($class_learner['final_test']['url']) && !empty($class_learner['final_test']['url'])){ echo $class_learner['final_test']['url']; }?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Listen to final test audio responses (opens in a new tab)"><i class="bi bi-volume-up-fill"></i></a>
												<?php }else{ 
												echo @disable_icon();
												}
										}
									}else{
											echo "<a href='" . site_url('school/core_certificate') . "/" . $class_learner['final_test']['final_result_candidate_id'] . "' target='_blank'data-toggle='tooltip' data-placement='bottom' title='Final Test (opens in a new tab)'>Final Test</a>";
											if(isset($class_learner['final_test']['audio_reponses']) && ($class_learner['final_test']['audio_reponses'] == 1)) { 
												if($class_learner['final_test']['audio_available']){ ?>
														<a href="<?php if(isset($class_learner['final_test']['url']) && !empty($class_learner['final_test']['url'])){ echo $class_learner['final_test']['url']; }?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Listen to final test audio responses (opens in a new tab)"><i class="bi bi-volume-up-fill"></i></a>
													<?php }else{ 
													echo @disable_icon();
													}
											}
									}
									
								} else {
									echo '<span style="display:block;white-space: nowrap;">Final Test</span>';
								}
							}
							?>
						</td>
						</tr>
						<?php $i++; ?>
						<?php }
						} else {
						?>
						<tr>
							<td colspan="7">
								<div class="alert alert-danger fade in">
									<a href="#" class="close" data-dismiss="alert">&times;</a>
									<?php echo lang('app.language_teacher_classes_no_learners'); ?>
								</div>
							</td>
						</tr>
						<?php } ?>                                          
						</tbody>
						</table>
					</div>
					</div>
					<div class="institution_pagination">
					<nav class="text-right">
							<?php //echo $class_learners['class_learners_links']; ?>
							<?php if ($class_learners['class_learners_links']) :?>
									<?= $class_learners['class_learners_links']->links('pagination_class_learners') ?>
									<?php endif ?> 
					</nav>
					</div>
				</div> 
			</section>
		</div>
	</div>
	<!-- table school teacher ends  -->
	<?php } else { ?>
	<div class="container">
		<div class="institution_page">
		<div class="row">
			<section class="col-sm-12">
				<div class="mt20">
				<?php include_once 'messages.php'; ?>
				</div>
				<?php if ($this->session->setFlashdata('failure')) { ?> 
					<div class="alert alert-danger fade in">
						<a href="#" class="close" data-dismiss="alert">&times;</a>
					<?php echo $this->session->setFlashdata('failure'); ?>
					</div>
				<?php } ?>  

				<h1 class="user_name"><?php echo lang('app.language_dashboard_welcome') . ', ' . ucfirst($this->session->get('user_firstname')." ".$this->session->get('user_lastname')); ?> </h1>
				<?php
				if ($this->session->get('tab_reports')) {
					$this->session->remove('tab_orders', TRUE);
				}
				?>              
				<div class="institution_tab nav_dashboard" style="overflow:hidden;">
					<!-- Nav tabs -->
					<ul class="nav nav-tabs" role="tablist">
						<li  id="tab_ord" role="presentation" class="<?php echo ($this->session->get('tab_orders')) ? 'active' : ''; ?>"><a href="#test_orders" id="tab_orders" aria-controls="home" role="tab" data-toggle="tab"><?php echo lang('app.language_school_label_test_order_header'); ?></a></li>
						<li  id="tab_u13" role="presentation" class="<?php echo ($this->session->get('tab_u13entries')) ? 'active' : ''; ?>"><a href="#u13_entry" id="tab_u13entries" aria-controls="home" role="tab" data-toggle="tab"><?php echo lang('app.language_school_label_u13_header'); ?></a></li>
						<!-- <li id="tab_dis" role="presentation" class="<?php echo ($this->session->get('tab_distributors')) ? 'active' : ''; ?>"><a href="#distributors" id="tab_distribtutors" aria-controls="profile" role="tab" data-toggle="tab"><?php echo lang('app.language_school_label_distributor_header'); ?></a></li> --><!-- WP-1104 Hided distributors tab in School Dashboard -->
						<li id="tab_rep" role="presentation" class="<?php echo ($this->session->get('tab_reports')) ? 'active' : ''; ?>"><a href="#report" id="tab_reports" aria-controls="profile1" role="tab" data-toggle="tab"><?php echo lang('app.language_school_report_tab'); ?></a></li>
						<li id="tab_tea" role="presentation" class="<?php echo ($this->session->get('tab_teachers')) ? 'active' : ''; ?>"><a href="#teachers" id="tab_teachers" aria-controls="teachers" role="tab" data-toggle="tab"><?php echo lang('app.language_school_teacher_tab'); ?></a></li>
						 <li id="tab_ven" role="presentation" class="<?php echo ($this->session->get('tab_venues')) ? 'active' : ''; ?>"><a href="#venues" id="tab_venues" aria-controls="venues" role="tab" data-toggle="tab"><?php echo lang('app.language_school_venue_tab_header'); ?></a></li>
						<li id="tab_evnt" role="presentation" class="<?php echo ($this->session->get('tab_events')) ? 'active' : ''; ?>"><a href="#events" id="tab_events" aria-controls="events" role="tab" data-toggle="tab"><?php echo lang('app.language_school_label_events_header'); ?></a></li>
						<li id="tab_result" role="presentation" class="<?php echo ($this->session->get('tab_results')) ? 'active' : ''; ?>"><a href="#results" id="tab_results" aria-controls="results" role="tab" data-toggle="tab"><?php echo lang('app.language_school_label_results_header'); ?></a></li>
						
					</ul>
					<!-- Tab panes -->
					<div class="institution_content">
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane <?php echo ($this->session->get('tab_orders')) ? 'active' : ''; ?>" id="test_orders">
							<p><span class="glyphicon glyphicon-alert"></span> 
							<?php echo lang('app.language_school_order_information_message'); ?>
							<a class="changeActive" id="tab_u13entries" href="#u13_entry" aria-controls="home" role="tab" data-toggle="tab">under 16 entry option.</a></p>
							<div class="text-left mt20">
								<form class="form-inline" action="<?php echo site_url('school/dashboard'); ?>" id="searchForm_orders">
									<div class="form-group">
										<input style="width: 280px" maxlength="50" type="text" placeholder="Enter code or user name or email" name="order_list_search" class="form-control clearable search" id="search_code" value="<?php echo @$search_list_o16['search_item']; ?>">
									</div>
									<button type="submit" class="btn btn-success">Search</button>
									<button type="button" id="code_orders_clearBtn" class="btn btn-default">Clear</button>
								</form>
							</div>
							<div class="col-sm-12 code_order text-right mt30">
								<?php 
								$institute_courseTypes = isset($institute_courseType) ? array_map('current', $institute_courseType) : '';
								
								if ((empty($this->session->get('orderdata')['distributor_id']))||((in_array('1', $institute_courseTypes))&&(!in_array('2', $institute_courseTypes))&&(!in_array('3', $institute_courseTypes))&&(!in_array('4', $institute_courseTypes)))) { ?>
								
								<button type="button" class="btn btn-sm btn-continue" data-toggle="modal" disabled><?php echo lang('app.language_school_label_order_tests'); ?></button>
								
								
								<?php } else {?>
							
									<button type="button" class="btn btn-sm btn-continue" data-toggle="modal" data-target="#order1modal" data-backdrop="static" data-keyboard="false"><?php echo lang('app.language_school_label_order_tests'); ?></button>
								<?php }?>
										<button type="button" class="btn btn-sm btn-continue" id="order_view" ><?php echo lang('app.language_school_label_order_view'); ?></button>
								<button type="button" class="btn btn-sm btn-continue" id="download_tokens"><?php echo lang('app.language_school_label_download_tokens'); ?></button>
								<!-- WP-1374 button added for export codes -->
								<button type="button" class="btn btn-sm btn-continue" id="export_orders" disabled><?php echo lang('app.language_school_export_codes'); ?></button>
							</div>
							<div class="col-sm-12">
							<div class="table-responsive mt40">
							<?php  
								if(isset($search_list_o16['search_item']) && $search_list_o16['search_item'] != ""){ ?>
									<!--search in code orders starts WP-1354-->
									<table class="table table-bordered institution_table">
										<thead>
											<tr>
												<th width="12%"><?php echo lang('app.language_tbl_label_token_id');?></th>
												<th><?php echo lang('app.language_tbl_label_email') ?></th>
												<th><?php echo lang('app.language_tbl_label_token_type'); ?></th>
												<th width="14%"><?php echo lang('app.language_tbl_label_level');?></th>
												<th><?php echo lang('app.language_school_u13learner_course_progress'); ?></th>
												<th><?php echo lang('app.language_tbl_label_test_booking');?> </th>
												<th width="15%"><?php echo lang('app.language_tbl_label_test_results') ?></th>
											</tr>
										</thead>
										<tbody>
											<?php 
											if(!empty($search_list_o16['search_list'])) { 
												foreach ($search_list_o16['search_list'] as $token) { ?>
											<tr>
												<td class="text-token"><?php echo $token->token; ?></td>
												<td>
													<?php if($token->user_name || $token->email ) { ?>
														<span style="display:block;white-space:nowrap;">
															<?php echo $token->email ; ?>
														</span>
														<span style="display:block;white-space:nowrap;">
															<?php echo $token->user_name; ?>
														</span>									
													<?php } elseif($token->level) { 
															echo '-';
															} else {
															echo 'Unregistered';
														} ?>
												</td>
												<td>
													<?php
														$is_supervised = (isset($token->is_supervised) && $token->is_supervised == 1)? " (supervised)" : " (unsupervised)";
														if ($token->type_of_token == 'catslevel') {
															echo 'CATs Step level'.$is_supervised;
														} elseif ($token->type_of_token == 'cats_core') {
															echo 'CATs Step Core'.$is_supervised;
														} elseif ($token->type_of_token == 'cats_higher') {
															echo 'CATs Step Higher'.$is_supervised;
														} elseif ($token->type_of_token == 'cats_core_or_higher') {
															echo 'CATs Step Core or CATs Step Higher'.$is_supervised;
														} elseif ($token->type_of_token == 'benchmarktest') {
															echo 'Benchmarking test';
														} elseif ($token->type_of_token == 'speaking_test') {
															echo 'Speaking Test'; //WP-1109 speaking disabled 
														} elseif($token->test_name != '') {
															echo $token->test_name;
														}else{
															echo '-';
														}
													?>
												</td>
												<?php 
													$step_levels = ["cats_core","cats_higher","cats_core_or_higher","catslevel"];
													if(in_array($token->type_of_token, $step_levels)){ ?>
														<td><?php echo ($token->level) ? $token->level : lang('app.language_school_label_not_available'); ?> </td>
														<td>
															<?php if(isset($token->course_progress) && $token->course_progress !== NULL) { ?>
															<div>
																<div class="progress">
																	<div class="progress-bar active" role="progressbar" aria-valuenow="<?php echo $token->course_progress; ?>" aria-valuemin="0" aria-valuemax="100" style="background-color: #99c8f1; width:<?php echo $token->course_progress; ?>%">
																		<span><?php echo round($token->course_progress); ?>%</span>
																	</div>
																</div>
															</div>
															<?php } else { echo lang('app.language_school_label_not_available'); ?>							
															<?php }  ?>							
														</td>
														<td>
															<?php 
															if(isset($token->booking_status) && $token->booking_status != NULL ){
																if($token->booking_status == 1 && $token->start_date_time) {
																	$tz_to = $institutionTierId['timezone'];
															
																	$institution_zone_values = @get_institution_zone_from_utc($tz_to, $token->start_date_time, $token->end_date_time);	

																	$dt = $institution_zone_values['institute_event_date'];
																	echo $dt.' '.$token->city ;?>
																	<?php if($token->event_status == 1 ){ ?>
																		<br><a style="text-decoration: underline;" href="<?php echo site_url();?>school/learner_allocation/<?php echo $token->event_id?>">View</a>
																	<?php } ?>
															<?php }else{
																	echo lang('app.language_school_label_not_available');
																} 
															}else{
																echo lang('app.language_school_label_not_available');
															}
															?>											
														</td>
														<td>
															<!--Practice Test 1--> 
															<?php if ($token->productid < 10) {
																//WP-1308 Starts
																$practicecount =  isset($token->practice_count)?$token->practice_count:'';
																if($practicecount > 1){
																	$practice_label = 'Practice Test 1';        
																}else{
																	$practice_label = 'Practice Test';
																}
																?><div> <?php 
																if(isset($token->practiceresults) && !empty($token->practiceresults['0'])) { ?>
																	<a style="display:inline-block;white-space: nowrap;text-decoration: underline;" href="#" data-toggle="modal" data-target=".practice-test-results" data-backdrop="static" data-keyboard="false" data-id="<?php echo $practice_label; ?>|<?php if(!empty($token->practiceresults['0']['session_number'])){ echo $token->practiceresults['0']['session_number']; } ?>|<?php if(!empty($token->practiceresults['0']['thirdparty_id'])){ echo $token->practiceresults['0']['thirdparty_id']; } ?>" class="practice-test-button"  id="loading_modal<?php if(!empty($token->practiceresults['0']['thirdparty_id'])){ echo $token->practiceresults['0']['thirdparty_id']; } ?><?php if(!empty($token->practiceresults['0']['session_number'])){ echo $token->practiceresults['0']['session_number']; } ?>"  ><?php echo $practice_label; ?></a>
																	<?php if(isset($token->practiceresults['0']['audio_reponses']) && ($token->practiceresults['0']['audio_reponses'] == 1)) { 
																		echo @disable_icon();
																		 } ?>
																<?php  } elseif(isset($token->practiceresults_tds) && !empty($token->practiceresults_tds['practice_test1']) && !empty($token->practiceresults_tds['practice_test1']['processed_data'])){?> 
																	<a  style="display:inline-block;white-space: nowrap;text-decoration: underline;" href="#" data-toggle="modal" data-target=".practice-test-results" data-backdrop="static" data-keyboard="false" data-id="<?php echo $practice_label; ?>|<?php if(!empty($token->practiceresults_tds['practice_test1']['token'])){ echo $token->practiceresults_tds['practice_test1']['token']; } ?> " class="practice-test-button-tds"  id="loading_modal<?php if(!empty($token->practiceresults_tds['practice_test1'])){ echo $token->practiceresults_tds['practice_test1']['token']; } ?>" ><?php echo $practice_label; ?></a>
																	<?php if(isset($token->practiceresults_tds['practice_test1']['audio_reponses']) && ($token->practiceresults_tds['practice_test1']['audio_reponses'] == 1)) { ?>
																	<?php if($token->practiceresults_tds['practice_test1']['audio_available']){?>
																		<a href="<?php if(isset($token->practiceresults_tds['practice_test1']['url']) && !empty($token->practiceresults_tds['practice_test1']['url'])){ echo $token->practiceresults_tds['practice_test1']['url']; }?>" target="_blank"data-toggle="tooltip" data-placement="bottom" title="Listen to practice test audio responses (opens in a new tab)"><i class="bi bi-volume-up-fill"></i></a>
																		<?php }else{ 
																			echo @disable_icon();
																		}?>  
                                                        		<?php } ?>
																	<?php } else { ?>
																		<?php if ($token->type_of_token != "cats_higher") { ?>
																			<span style="display:block;white-space: nowrap;color: #79b2d8;"><?php echo $practice_label; ?></span>
																		<?php } ?>
																<?php } ?> 
																</div>           
															<?php }?>
															<!--Practice Test 2-->                                                     
															<?php if ($token->productid < 10 && $token->practice_count > 1) {
																?><div>	<?php
																if(isset($token->practiceresults) && !empty($token->practiceresults['1'])) { 
																?>
																<a  style="display:inline-block;white-space: nowrap;text-decoration: underline;" href="#" data-toggle="modal" data-target=".practice-test-results" data-backdrop="static" data-keyboard="false" 
																data-id="Practice Test 2|<?php if(!empty($token->practiceresults['1']['session_number'])){ echo $token->practiceresults['1']['session_number']; } ?>|<?php if(!empty($token->practiceresults['1']['thirdparty_id'])){ echo $token->practiceresults['1']['thirdparty_id']; } ?>" 
																class="practice-test-button"  
																id="loading_modal<?php if(!empty($token->practiceresults['1']['thirdparty_id'])){ echo $token->practiceresults['1']['thirdparty_id']; } ?><?php if(!empty($token->practiceresults['1']['session_number'])){ echo $token->practiceresults['1']['session_number']; } ?>" >Practice Test2</a>
																<?php if(isset($token->practiceresults['1']['audio_reponses']) && ($token->practiceresults['1']['audio_reponses'] == 1)) { 
																	echo @disable_icon(); 
																	} ?>
																<?php  }  elseif(isset($token->practiceresults_tds) && !empty($token->practiceresults_tds['practice_test2']) && !empty($token->practiceresults_tds['practice_test2']['processed_data'])){ ?>   
																<a  style="display:inline-block;white-space: nowrap;text-decoration: underline;" href="#" data-toggle="modal" data-target=".practice-test-results" data-backdrop="static" data-keyboard="false" data-id="Practice Test 2|<?php if(!empty($token->practiceresults_tds['practice_test2']['token'])){ echo $token->practiceresults_tds['practice_test2']['token']; } ?> " class="practice-test-button-tds"  id="loading_modal<?php if(!empty($token->practiceresults_tds['practice_test2'])){ echo $token->practiceresults_tds['practice_test2']['token']; } ?>" >Practice Test2</a>
																<?php if(isset($token->practiceresults_tds['practice_test2']['audio_reponses']) && ($token->practiceresults_tds['practice_test2']['audio_reponses'] == 1)) { ?>
																		<?php if($token->practiceresults_tds['practice_test2']['audio_available']){?>
																			<a href="<?php if(isset($token->practiceresults_tds['practice_test1']['url']) && !empty($token->practiceresults_tds['practice_test1']['url'])){ echo $token->practiceresults_tds['practice_test2']['url']; }?>" target="_blank"data-toggle="tooltip" data-placement="bottom" title="Listen to practice test audio responses (opens in a new tab)"><i class="bi bi-volume-up-fill"></i></a>
																		<?php }else{ 
																			echo @disable_icon(); 
																			}?>  
                                                    			<?php } ?>
																<?php }else { ?>
																	<?php if ($token->type_of_token != "cats_higher") { ?>
																		<span style="display:block;white-space: nowrap;color: #79b2d8;"><?php echo 'Practice Test2'; ?></span>
																	<?php } ?>
																<?php }  ?>
																</div>
															<?php } ?>
															<!--Final Test-->  
															<?php  if($token->section_one) { ?>
																<a style="text-decoration: underline;" href="<?php echo site_url('school/core_certificate').'/'.$token->candidate_id; ?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Final Test (opens in a new tab)">Final Test</a>
															<?php } elseif($token->tds_course_type == "Core"){ //WP-1179 - Tds core results ?>
																<a style="text-decoration: underline;" href="<?php echo site_url('school/core_certificate').'/'.$token->tds_candidate_id; ?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Final Test (opens in a new tab)">Final Test</a>
															<?php } elseif($token->higher_section_one){ //WP-1156 - Higher results ?>
																<a style="text-decoration: underline;" href="<?php echo site_url('school/higher_certificate').'/'.$token->higher_candidate_id; ?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Final Test (opens in a new tab)">Final Test</a>
															<?php } elseif($token->tds_course_type == "Higher"){ //WP-1276 - TDS Higher results ?>
																<a style="text-decoration: underline;" href="<?php echo site_url('school/higher_certificate').'/'.$token->tds_candidate_id.'/'.$token->tds_token; ?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Final Test (opens in a new tab)">Final Test</a>
															<?php }else { ?>
																<span style="display:block;white-space: nowrap;color: #79b2d8;"><?php echo 'Final Test'; ?></span>
															<?php } ?>	
															<?php if(isset($token->audio_reponses) && ($token->audio_reponses == 1)) { ?>
																<?php if($token->audio_available){ ?>
																		<a href="<?php if(isset($token->url) && !empty($token->url)){ echo $token->url; }?>" target="_blank" style="text-decoration:none" data-toggle="tooltip" data-placement="bottom" title="Listen to final test audio responses (opens in a new tab)"><i class="bi bi-volume-up-fill"></i></a>
																<?php }else{ echo @disable_icon();
																 }?>  
															<?php } ?>										
														</td>
													<?php } else { ?>
															<td><?php echo ($token->BS_level && $token->type_of_token == 'benchmarktest') ? $token->BS_level : lang('app.language_school_label_not_available'); ?></td>
															<td><?php echo lang('app.language_school_label_not_available'); ?></td>
															<td><?php echo lang('app.language_school_label_not_available'); ?></td>
															<td style="/*white-space: nowrap;*/">
																<?php if(isset($token->TBR_token) && ($token->TBR_token != '') && ($token->is_used != 0)) { ?>
																	<a style="text-decoration: underline;" href="<?php echo site_url('school/benchmark_certificate').'/'.$token->TBR_candidate_id.'/'.$token->TBR_token; ?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="View result (opens in a new tab)">View Result</a>
																	<?php if(isset($token->audio_reponses) && ($token->audio_reponses == 1)) { ?>
																		<?php if($token->audio_available){?>
																			<a style="text-decoration: underline;" href="<?php if(isset($token->url) && !empty($token->url)){ echo $token->url; }?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Listen to Stepcheck test audio responses (opens in a new tab)"><i class="bi bi-volume-up-fill"></i></a>
																		<?php }else{ 
																			echo @disable_icon();
																		 }?>     
																	<?php } ?>	
																<?php } else { 
																	echo lang('app.language_school_label_not_available');
																} ?>
															</td>
													<?php } ?>											
											</tr>
											<?php } } else {?>
											<tr>
												<td colspan="7">
													<div class="alert alert-danger fade in">
														<a href="#" class="close" data-dismiss="alert">&times;</a>
														<?php echo "No data found on this search"; ?>
													</div>
												</td>
											</tr>
											<?php } ?>
										</tbody>
									</table>
									<!--search in code orders ENDS WP-1354-->
								<?php }else{ ?>
								<table class="table table-bordered institution_table">
								<thead>
									<!-- WP-1374 checkbox added -->
									<tr><?php if ((!empty($orders)) && (count($orders) > 1)) {?>
										<th><input type="checkbox" name="checkall_order" id="checkall_order" value=""/></th>
									 <?php } else{?> 
										<th><input type="checkbox"  style="visibility:hidden; " name="checkall_order" /></th>
										<?php }?>
									<!--<th>Order date</th>-->
									<th width="17%">
									
									
									<?php if (!empty($orders) && count($orders) > 1) { ?>									
										<?php 
										echo anchor(current_url() . "?order=" . (($this->request->getVar('order') == 'ASC') ? 'DESC' : 'ASC'), lang('app.language_school_label_order_date') . (($this->request->getVar('order') == 'DESC' || $this->request->getVar('order') == NULL) ? '&nbsp;<span class="glyphicon glyphicon-arrow-up"></span>' : '&nbsp;<span class="glyphicon glyphicon-arrow-down"></span>'), array('style' => 'color:white;')); ?>

 
									<?php } else { ?>
										<?php echo lang('app.language_school_label_order_date'); ?>
									<?php } ?>
	
									</th>
									<th><?php echo lang('app.language_tbl_label_ordername'); ?></th>
									<th><?php echo lang('app.language_tbl_label_ordertype'); ?></th>
									<th width="8%" align="center" ><?php echo lang('app.language_school_label_no'); ?></th>
									<th width="12%"><?php echo lang('app.language_school_label_results_available'); ?></th>
									<th width="12%"><?php echo lang('app.language_school_label_tokens_available'); ?></th>
										</tr>
									</thead>
									<tbody>
									<?php
									if (!empty($orders)) {
										foreach ($orders as $order) {
									?>
									<tr>
										<td align="center"> <input type="checkbox" class="radio_order" name="radio_order[]" value="<?php echo $order->id;?>"></td>
										<td><a href="<?php echo site_url('school/tokenlist/'.$order->id); ?>"><?php echo $order->order_date; ?></a></td>
										<td><?php echo $order->order_name; ?></td>
										<td>
											<?php
                                            $is_supervised = (isset($order->is_supervised) && $order->is_supervised == 1)? " (supervised)" : " (unsupervised)";
											if ($order->type_of_token == 'catslevel') {
												echo 'CATs Step level'.$is_supervised;
											} elseif ($order->type_of_token == 'cats_core') {
												echo 'CATs Step Core'.$is_supervised;
											} elseif ($order->type_of_token == 'cats_higher') {
												echo 'CATs Step Higher'.$is_supervised;
											} elseif ($order->type_of_token == 'cats_core_or_higher') {
												echo 'CATs Step Core or CATs Step Higher'.$is_supervised;
											} elseif ($order->type_of_token == 'benchmarktest') {
												echo 'Benchmarking test';
											 } elseif ($order->type_of_token == 'speaking_test') {
												echo 'Speaking Test'; //WP-1109 speaking disabled 
											} elseif($order->test_name != '') {
												echo $order->test_name;
											}else{
												echo '-';
											}
											?>
										</td>
										<td align="center"><?php 

										
										$get_is_used_count = $this->schoolmodel->get_is_used_count($order->id);
										
										echo $get_is_used_count[0]->is_used_count; ?> / <?php   echo $order->number_of_tests; ?></td>
										<td><?php echo $order->results; ?></td>
										<td>
											<?php echo $order->tokens_status ? lang('app.language_school_label_not_available') : lang('app.language_school_label_available'); ?>
										</td>
									</tr>                                       
									<?php
										}
										} else {
										?>                                      
									<tr>
										<td colspan="7">
											<div class="alert alert-danger fade in">
												<a href="#" class="close" data-dismiss="alert">&times;</a>
												<?php echo lang('app.language_school_order_no_order'); ?>
											</div>
										</td>
									</tr>                                       
									<?php } ?>
									</tbody>
								</table>
								<?php } ?>
							</div>

								
								<?php if(isset($search_order_pager) && $search_order_pager != "") {?>
								<div class="institution_pagination">
								<nav class="text-right">
									<?php if ($search_order_pager) :?>
									<?= $search_order_pager->links('pagination_search_order_list') ?>
									<?php endif ?> 
								</nav>
								</div>	
								<?php } 
								 else { ?>
								<div class="institution_pagination">
								<nav class="text-right">
									<?php if ($orders_data_pager) :?>
									<?= $orders_data_pager->links('pagination_list_orders') ?>
									<?php endif ?> 
								</nav>
								</div>	
								<?php } ?>

							</div>							
						</div>
						
						<!-- U13 learner tab -->
						<div role="tabpanel" class="tab-pane code_orders_tab <?php echo ($this->session->get('tab_u13entries')) ? 'active' : ''; ?>" id="u13_entry">    
						<div class="row">                       
							<div class="text-left mt20">
								<form class="form-inline" action="<?php echo site_url('school/dashboard'); ?>" id="searchForm_u13">
									<div class="form-group">
										<input maxlength="50" type="text" placeholder="Enter search term" name="u13learner_search" class="form-control clearable search" id="search" value="<?php echo $u13learner_data['u13learner_search_item']; ?>">
									</div>
									<button type="submit" class="btn btn-success">Search</button>
									<button type="button" id="u13learner_clearBtn" class="btn btn-default">Clear</button>
								</form>
							</div>
							<div class="clearfix"></div>
							<form action="<?php echo site_url('school/export_access_details'); ?>" method="POST">
								<?php if ($this->session->setFlashdata('access_errors')) { ?>
									<div class="alert alert-danger fade in mt20">
										<a href="#" class="close" data-dismiss="alert">&times;</a>
										<?php echo $this->session->setFlashdata('access_errors'); ?>
									</div>
								<?php } ?>
								

								<div class="col-sm-12 text-right mt30">
									<?php if((in_array('1', $institute_courseTypes))|| (in_array('2', $institute_courseTypes))){?>
									<button type="button" class="btn btn-sm btn-continue" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#addupdateModal_u13" id="addBtn_u13" > <i class="fa fa-plus fa-fw"></i>Add</button>
									<?php }else{?>
									<button type="button" class="btn btn-sm btn-continue" data-toggle="modal" disabled> <i class="fa fa-plus fa-fw"></i>Add</button>
									<?php }?>
									
									<button type="button" class="btn btn-sm btn-continue" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#updateaddModal_u13" id="editBtn_u13"><i class="fa fa-edit fa-fw"></i>Edit</button>
									<button type="button" class="btn btn-sm btn-continue" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#nextlevelModal" id="nextlevelbtn_u13">Next Level</button>
									<?php if(((in_array('1', $institute_courseTypes))|| (in_array('2', $institute_courseTypes))) || (!empty($u13learner_data['u13_learners']))){?>
										<button type="submit" data-toggle="tooltip" data-placement="top" title="<?php echo lang('app.language_school_access_details_info'); ?>" class="btn btn-sm btn-continue pull-right" id="acsdetailsBtn_u13">Access Details</button>
									<?php }else{?>
										<button type="submit" data-toggle="tooltip" data-placement="top" class="btn btn-sm btn-continue pull-right" disabled>Access Details</button>
									<?php }?>
								</div>

								<!--U13 learners listing -->
								<div class="col-sm-12">
								<div class="table-responsive view-tokens mt40">
									<table class="table table-bordered institution_table">
										<thead>
										<tr> 

											<?php if ((!empty($u13learner_data['u13_learners'])) && (sizeof($u13learner_data['u13_learners']) > 1)) {?>
											<th><input type="checkbox"   name="checkall" id="checkall"  value=""  /></th>
											<?php } else {?>
											<th><input type="checkbox"  style="visibility:hidden; " name="checkall"  /></th>
											<?php }?>
											<th><?php echo lang('app.language_school_teacher_label_name'); ?></th>
											<th><?php echo lang('app.language_school_u13learner_label_pass_generated'); ?></th>
											<th><?php echo lang('app.language_school_level'); ?></th>
											<th><?php echo lang('app.language_school_u13learner_course_progress'); ?></th>
											<th><?php echo lang('app.language_tbl_label_test_booking'); ?></th>
											<th><?php echo lang('app.language_tbl_label_test_results'); ?></th>
										</tr>
										</thead>
										<tbody>
										<?php
										if (!empty($u13learner_data['u13_learners'])) {
											$i = 0;
											foreach ($u13learner_data['u13_learners'] as $u13learner) {
												?>      
										<tr>
											<!-- Under 16 .. CHECK BOX -->
											<td align="center"><input type="checkbox" data-classstatus="<?php // echo isset($teachersData['class_associated_data'][$teacher->id]) ? @get_class_status($teachersData['class_associated_data'][$teacher->id]) : 'inactive';  ?>"  name="u13_learner_ids[]" class="u13_learner_ids"  value="<?php echo  base64_encode($this->encrypter->encrypt($u13learner['id'])); ?>" <?php echo ($i == 0) ? 'checked="checked"' : '';  ?>  /></td>
											<td><?php echo $u13learner['name']; ?> </br> <?php echo $u13learner['username']; ?> </br> <?php echo date("d-M-Y", $u13learner['dob']); ?></td>
											<td><?php echo date("d-m-Y", $u13learner['creation_time']); ?></td>
											<td><?php
											if (isset($u13learner['productname'])) {
												echo $u13learner['productname'];
											} else {
												echo '-';
											}
											?>
											<?php if (isset($u13learner['num_history_results']) && $u13learner['num_history_results'] > 1): ?>
											<br/><a style="text-decoration: underline;" href="#" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#history_modal" id="<?php echo base64_encode($this->encrypter->encrypt($u13learner['id'])); ?>" class="history_link">View history</a>
											<?php endif; ?>
											</td>
											<td style="">
											<?php if ($u13learner['thirdparty_id'] != '' && $u13learner['thirdparty_id'] > 0 && $u13learner['course_progress'] != NULL) {  ?>
											<div class="" style="">
												<div class="progress">
													<div class="progress-bar active" role="progressbar" aria-valuenow="<?php echo $u13learner['course_progress']; ?>" aria-valuemin="0" aria-valuemax="100" style="background-color: #99c8f1; width:<?php echo $u13learner['course_progress']; ?>%">
                                                                                                            <span><?php echo round($u13learner['course_progress']); ?>%</span>
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
												if(isset($u13learner['booking_status']) && $u13learner['booking_status'] != NULL ){
													if($u13learner['booking_status'] == 1 && $u13learner['start_date_time']) { 
												
														$institution_zone_values = @get_institution_zone_from_utc($tz_to, $u13learner['start_date_time'], $u13learner['end_date_time']);	

														$dt = $institution_zone_values['institute_event_date'];
														echo $dt.' '.$u13learner['city'] ;
														
														?>
														<?php if($u13learner['event_status'] == 1 ){ ?>
															<br><a style="text-decoration: underline;" href="<?php echo site_url();?>school/learner_allocation/<?php echo $u13learner['event_id'];?>">View</a>
														<?php } ?>
														<?php 
													}else{
														   echo lang('app.language_school_label_not_available');
													} 
												}else {
													echo lang('app.language_school_label_not_available');
												}
											?>											
											</td>
											<td>
											<?php 
										
											if ($u13learner['cats_product'] == 'cats_primary') {

												if (isset($u13learner['practice_test']['practice_test']) && $u13learner['practice_test']['practice_test'] == 1) {
													echo "<a href='#' style='text-decoration:none;cursor:default;pointer-events: none;'>Practice Test (" . $u13learner['practice_test']['percent']['percentage'] . ")</a>";
													echo "<br>";
												} elseif (isset($u13learner['practice_test_tds']['tds_practice_test']) && $u13learner['practice_test_tds']['tds_practice_test'] == 1) {
													echo "<a href='#' style='text-decoration:none;cursor:default;pointer-events: none;'>Practice Test (" . $u13learner['practice_test_tds']['percent']['percentage'] . ")</a>";
													echo "<br>";
												} else {
													echo '<span style="display:block;white-space: nowrap;">Practice Test</span>';
												}
												if (isset($u13learner['final_test']['final_result_status']) && $u13learner['final_test']['final_result_status'] == 1) {
												 echo '<a href="javascript:void(0)" class="primary_results" id="' . base64_encode($this->encrypter->encrypt($u13learner['id'])) . '" data-thirdpartyid="' . base64_encode($this->encrypter->encrypt($u13learner['thirdparty_id'])) . '">Final Test</a>';
												 echo " " . "(" . ($u13learner['final_test']['percent']['percentage']) . ")";
												} else {

													echo '<span style="display:block;white-space: nowrap;">Final Test</span>';
												}
											} else {
											if($u13learner['productid'] <= 9){
												//Tds Practice Test 
												if(isset($u13learner['practice_test_tds']) && $u13learner['practice_test_tds'] != NULL ){

													//WP-1308 Starts
													if(isset($u13learner['practice_count']) && $u13learner['practice_count'] == 1){
														$practice_label = 'Practice Test';
													}else{
														$practice_label = 'Practice Test 1';
													}

													//Tds practice test 1
													if(isset($u13learner['practice_test_tds']['practice_test1'])){
														$practice_test1 = $u13learner['practice_test_tds']['practice_test1']; 
													}
													if(!empty($practice_test1) && !empty($practice_test1['processed_data'])){
														echo '<div><a  style="display:inline-block;white-space: nowrap;" href="#" data-toggle="modal" data-target=".practice-test-results" data-backdrop="static" data-keyboard="false" 
															data-id="'.$practice_label.'|' . $practice_test1['token'] . '" class="practice-test-button-tds">'.$practice_label.'</a>'; 
																if(isset($practice_test1['audio_reponses']) && ($practice_test1['audio_reponses'] == 1)) { 
																	if($practice_test1['audio_available']){?>
																		<a href="<?php if(isset($practice_test1['url']) && !empty($practice_test1['url'])){ echo $practice_test1['url']; }?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Listen to practice test audio responses (opens in a new tab)"><i class="bi bi-volume-up-fill"></i></a>
																	<?php }else{ 
																		echo @disable_icon();
																	}
																} 
																echo '</div>';
														}else{
														echo '<span style="display:block;white-space: nowrap;">'.$practice_label.'</span>'; 
													}
													//Tds practice test 2
													if(isset($u13learner['practice_count']) && $u13learner['practice_count'] > 1){
														if(isset($u13learner['practice_test_tds']['practice_test2'])){
															$practice_test2 = $u13learner['practice_test_tds']['practice_test2'];
														}
														if(!empty($practice_test2) && !empty($practice_test2['processed_data'])){
															echo '<div><a  style="display:inline-block;white-space: nowrap;" href="#" data-toggle="modal" data-target=".practice-test-results" data-backdrop="static" data-keyboard="false" 
																data-id="Practice Test 2|' . $practice_test2['token'] . '" class="practice-test-button-tds">Practice Test 2</a>'; 
																if(isset($practice_test2['audio_reponses']) && ($practice_test2['audio_reponses'] == 1)) { 
																	if($practice_test2['audio_available']){?>
																		<a href="<?php if(isset($practice_test2['url']) && !empty($practice_test2['url'])){ echo $practice_test2['url']; }?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Listen to practice test audio responses (opens in a new tab)"><i class="bi bi-volume-up-fill"></i></a>
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
												elseif (isset($u13learner['practice_test'])) {

													//WP-1308 Starts
													if(isset($u13learner['practice_count']) && $u13learner['practice_count'] == 1){
														$practice_label = 'Practice Test';
													}else{
														$practice_label = 'Practice Test 1';
													}

													if (isset($u13learner['practice_test']['practice_test1']) && $u13learner['practice_test']['practice_test1'] == 1) {
														if (!empty($u13learner['practice_test']['session_number1'])) {
														echo '<div><a  style="display:inline-block;white-space: nowrap;" href="#" data-toggle="modal" data-target=".practice-test-results" data-backdrop="static" data-keyboard="false" 
														data-id="'.$practice_label.'|' . $u13learner['practice_test']['session_number1'] . '|' . $u13learner['thirdparty_id'] . '"
														class="practice-test-button">'.$practice_label.'</a>'; 
														echo @disable_icon();	
														}
														echo '</div>';
													} else {
													echo '<span style="display:block;white-space: nowrap;">'.$practice_label.'</span>';
													}
													if(isset($u13learner['practice_count']) && $u13learner['practice_count'] > 1){
														if (isset($u13learner['practice_test']['practice_test2']) && $u13learner['practice_test']['practice_test2'] == 1) {
															if (!empty($u13learner['practice_test']['session_number2'])) {
															echo '<div><a  style="display:inline-block;white-space: nowrap;" href="#" data-toggle="modal" data-target=".practice-test-results" data-backdrop="static" data-keyboard="false" 
															data-id="Practice Test 2|' . $u13learner['practice_test']['session_number2'] . '|' . $u13learner['thirdparty_id'] . '"
															class="practice-test-button">Practice Test 2</a>';
															echo @disable_icon();
															}
															echo '</div>';
														} else {
														echo '<span style="display:block;white-space: nowrap;">Practice Test 2</span>';
														}
													}
												} else {
												//WP-1102 - Added condition based on core, To hide Practice Test links for products higher.
													if (($u13learner['productid']) <= 9) {
														if(isset($u13learner['practice_count']) && $u13learner['practice_count'] == 1){
															echo '<span style="display:block;white-space: nowrap;">Practice Test</span>';
														}else{
															echo '<span style="display:block;white-space: nowrap;">Practice Test 1</span>';
															echo '<span style="display:block;white-space: nowrap;">Practice Test 2</span>';
														}
													}
												}
											}
											if (isset($u13learner['final_test']) && $u13learner['final_test']['final_result_status'] == 1 && isset($u13learner['thirdparty_id'])) {
												if($u13learner['product_course_type'] === 'Higher'){
                                                                                                    $link = FALSE;
                                                                                                    if(isset($u13learner['final_test']['final_result_token'])){
                                                                                                        $link = isset($u13learner['final_test']['final_result_higherdata']) && !empty($u13learner['final_test']['final_result_higherdata']) ? TRUE : FALSE;
                                                                                                        $url = site_url('school/higher_certificate') . "/" . $u13learner['final_test']['final_result_candidate_id']. "/" . $u13learner['final_test']['final_result_token'];
                                                                                                    }else{
                                                                                                        $link = isset($u13learner['final_test']['final_result_higherdata']) && !empty($u13learner['final_test']['final_result_higherdata']) ? TRUE : FALSE;
                                                                                                        $url = site_url('school/higher_certificate') . "/" . $u13learner['final_test']['final_result_candidate_id'];
                                                                                                    }
                                                                                                    echo  $link ? "<a href='" . $url . "' target='_blank' data-toggle='tooltip' data-placement='bottom' title='Final Test (opens in a new tab)'>Final Test</a>" : '<span style="display:block;white-space: nowrap;";>Final Test</span>';
																									if(isset($u13learner['final_test']['audio_reponses']) && ($u13learner['final_test']['audio_reponses'] == 1)) { 
																										if($u13learner['final_test']['audio_available']){ ?>
																											 <a href="<?php if(isset($u13learner['final_test']['url']) && !empty($u13learner['final_test']['url'])){ echo $u13learner['final_test']['url']; }?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Listen to final test audio responses (opens in a new tab)"><i class="bi bi-volume-up-fill"></i></a>
																										 <?php }else{ 
																											echo @disable_icon();
																									      }
																									}
												}else{
                                                                                                    echo "<a href='" . site_url('school/core_certificate') . "/" . $u13learner['final_test']['final_result_candidate_id'] . "' target='_blank' data-toggle='tooltip' data-placement='bottom' title='Final Test (opens in a new tab)'>Final Test</a>";
																									if(isset($u13learner['final_test']['audio_reponses']) && ($u13learner['final_test']['audio_reponses'] == 1)) { 
																										if($u13learner['final_test']['audio_available']){ ?>
																											 <a href="<?php if(isset($u13learner['final_test']['url']) && !empty($u13learner['final_test']['url'])){ echo $u13learner['final_test']['url']; }?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Listen to final test audio responses (opens in a new tab)"><i class="bi bi-volume-up-fill"></i></a>
																										 <?php }else{ 
																											echo @disable_icon();
																									      }
																									}
												}
											} else {
                                                                                            echo '<span style="display:block;white-space: nowrap;">Final Test</span>';
											}
											}
											?>
											</td>
										</tr>
										<?php
										$i++;
										}
										} else {
										?>
										<tr>
											<td colspan="7">
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
								<!-- under 13 pagniation end -->	
								<div class="institution_pagination">
                                <nav class="text-right">
									<?php if ($u13learner_data['pager']) :?>
									<?= $u13learner_data['pager']->links('pagination_u13_learners') ?>
									<?php endif ?> 
                                </nav>
								</div>
								<!-- under 13 pagniation end -->

								</div>
							</form>
						</div>	
						</div>
						<div role="tabpanel" class="tab-pane  <?php echo ($this->session->get('tab_distributors')) ? 'active' : ''; ?>" id="distributors">                
							<div class="text-right mb10">
								<button type="button" id="_set_default_btn" class="btn-sub"><?php echo lang('language_school_button_set_as_default'); ?></button>&nbsp;&nbsp;<img alt="loading" class="loading" style="display:none;" src="<?php echo base_url('public/images/loading.gif'); ?>">
							</div>
							<div class="table-responsive">
								<table class="table table-bordered" id="list_dist_table">
									<thead>
										<tr> <th>&nbsp;</th>

											<th>
												<?php if (!empty($distributors) && count($distributors) > 1) { ?>
													<?php echo anchor(current_url() . "?order=" . (($this->request->getPost('order') == 'DESC') ? 'ASC' : 'DESC'), lang('language_school_label_name') . (($this->request->getPost('order') == 'DESC') ? '&nbsp;<span class="glyphicon glyphicon-arrow-up"></span>' : '&nbsp;<span class="glyphicon glyphicon-arrow-down"></span>'), array('style' => 'color:white;')); ?>
										<?php } else { ?>
											<?php echo lang('app.language_school_label_name'); ?> 
										<?php } ?>
											</th>
											<th><?php echo lang('app.language_school_label_address'); ?></th>
											<th><?php echo lang('app.language_school_label_contact_details'); ?></th>
											<th><?php echo lang('app.language_school_label_city_town_for_test'); ?></th>
										</tr>
<?php
if (!empty($distributors_highlight)) {
	foreach ($distributors_highlight as $distributor) {
		?>
															<?php if ($distributor->distributor_id == $make_default && !$this->uri->segment(4)): ?> 
													<tr style="<?php echo ($distributor->distributor_id == $make_default) ? "background: #e0f2f5;" : ""; ?>" >
														<td align="center"> <input type="radio" <?php echo ($distributor->distributor_id == $make_default) ? "checked" : ""; ?> name="testOrder" value="<?php echo base64_encode($distributor->distributor_id); ?>"></td>
														<td><?php echo $distributor->distributor_name; ?><?php echo ($distributor->distributor_id == $make_default) ? '<br/><span class="text-center">(default)</span>' : ''; ?></td>
														<td class="address_details"><?php echo $distributor->address_line1; ?>
															<a style="text-decoration:none;" class="pull-right information fa fa-caret-down" data-toggle="collapse" data-parent="#accordion" href="#_address<?php echo sha1($distributor->distributor_id); ?>"></a>
															<div class="address collapse"  id="_address<?php echo sha1($distributor->distributor_id); ?>">
			<?php if ($distributor->address_line2 != ''): ?>
																	<span><?php echo $distributor->address_line2; ?></span>
			<?php endif; ?>
															</div>
														</td>
														<td class="contact_details"><?php echo $distributor->firstname . '&nbsp;' . $distributor->lastname; ?>
															<a style="text-decoration:none;" class="pull-right information fa fa-caret-down" data-toggle="collapse" data-parent="#accordion" href="#_contact<?php echo sha1($distributor->distributor_id); ?>"></a>
															<div class="contact collapse"  id="_contact<?php echo sha1($distributor->distributor_id); ?>">
																<span class="fa fa-envelope">&nbsp;<a href="mailto:<?php echo $distributor->email; ?>"><?php echo $distributor->email; ?></a></span>
																<span class="fa fa-phone">&nbsp;<?php echo $distributor->contact_number; ?></span>
															</div>
														</td>
														<td><?php echo ($schoolC->get_venues_of_distributors($distributor->distributor_id) != '') ? @implode(',', $schoolC->get_venues_of_distributors($distributor->distributor_id)) : '-'; ?></td>
													</tr>
												<?php endif; ?>
												<?php
											}
										}
										?>
<?php
if (!empty($distributors)) {
	foreach ($distributors as $distributor) {
		?>
															<?php if ($distributor->distributor_id != $make_default): ?> 
													<tr style="<?php echo ($distributor->distributor_id == $make_default) ? "background: #e0f2f5;" : ""; ?>" >
														<td align="center"> <input type="radio" <?php echo ($distributor->distributor_id == $make_default) ? "checked" : ""; ?> name="testOrder" value="<?php echo base64_encode($distributor->distributor_id); ?>"></td>
														<td><?php echo $distributor->distributor_name; ?><?php echo ($distributor->distributor_id == $make_default) ? '<br/><span class="text-center">(default)</span>' : ''; ?></td>
														<td class="address_details"><?php echo $distributor->address_line1; ?>
															<a style="text-decoration:none;" class="pull-right information fa fa-caret-down" data-toggle="collapse" data-parent="#accordion" href="#_address<?php echo sha1($distributor->distributor_id); ?>"></a>
															<div class="address collapse"  id="_address<?php echo sha1($distributor->distributor_id); ?>">
			<?php if ($distributor->address_line2 != ''): ?>
																	<span><?php echo $distributor->address_line2; ?></span>
			<?php endif; ?>
															</div>
														</td>
														<td class="contact_details"><?php echo $distributor->firstname . '&nbsp;' . $distributor->lastname; ?>
															<a style="text-decoration:none;" class="pull-right information fa fa-caret-down" data-toggle="collapse" data-parent="#accordion" href="#_contact<?php echo sha1($distributor->distributor_id); ?>"></a>
															<div class="contact collapse"  id="_contact<?php echo sha1($distributor->distributor_id); ?>">
																<span class="fa fa-envelope">&nbsp;<a href="mailto:<?php echo $distributor->email; ?>"><?php echo $distributor->email; ?></a></span>
																<span class="fa fa-phone">&nbsp;<?php echo $distributor->contact_number; ?></span>
															</div>
														</td>
														<td><?php echo ($schoolC->get_venues_of_distributors($distributor->distributor_id) != '') ? @implode(',', $schoolC->get_venues_of_distributors($distributor->distributor_id)) : '-'; ?></td>
													</tr>
		<?php endif; ?>
										<?php
									}
								}
								?>
									</thead>
								</table>
							</div>
							<nav class="text-right">

							</nav>            
						</div>
						<div role="tabpanel" class="tab-pane <?php echo ($this->session->get('tab_reports')) ? 'active' : ''; ?>" id="report">
							<?php if(isset($this->yellowfin_access) && $this->yellowfin_access == 1){ ?> 
							<section class="col-sm-12">
								<p>
									<span class="glyphicon glyphicon-alert"></span>
									<?php echo lang('app.language_yellowfin_report_tab_info'); ?>
								</p>
								<button type="button" class="btn btn-sm btn-continue" id="school_report" style="margin: 0 auto; clear: both; display: block; padding: 0px 50px; margin-top: 30px;"><?php echo lang('app.language_yellowfin_click_report_btn_txt'); ?></button>
							</section>
							<?php } else { ?>
							<div class="alert alert-danger fade in">
                                <a href="#" class="close" data-dismiss="alert">&times;</a><?php echo 'Report currently unavailable!'; ?>
                            </div>
							<section class="col-sm-12">
								<p><span class="glyphicon glyphicon-alert"></span><?php echo lang('app.language_yellowfin_report_tab_info'); ?></p>
								<button type="button" class="btn btn-sm btn-continue" disabled style="margin: 0 auto; clear: both; display: block; padding: 0px 50px; margin-top: 30px;"><?php echo lang('app.language_yellowfin_click_report_btn_txt'); ?></button>
							</section>
							<?php }?>
						</div>  
						<div role="tabpanel" class="tab-pane <?php echo ($this->session->get('tab_teachers')) ? 'active' : ''; ?>" id="teachers">
							<div class="text-left mt20">
								<form class="form-inline" action="<?php echo site_url('school/dashboard'); ?>" id="searchForm" >
									<div class="form-group" >
										<input maxlength="50"  type="text" placeholder="<?php echo lang('app.language_admin_institutions_teacher_enter_search_term'); ?>" name="search" class="form-control clearable search" id="search" value="<?php echo @$search_item; ?>">
									</div>
									<button type="submit" class="btn btn-success" ><?php echo lang('app.language_admin_institutions_search_btn'); ?></button>
									<button type="button" id="clearBtn" class="btn btn-default" ><?php echo 'Clear'; ?></button>
								</form>

							</div> 
							<div class="clearfix"></div>
							<?php
							function get_class_status($class_associated_data) {

								if (isset($class_associated_data)) {

									foreach ($class_associated_data as $class):
										if ($class->number_in_class > 0):
											return 'active';
											break;
										else:
											return 'inactive';
										endif;
									endforeach;
								}else {
									return 'inactive';
								}
							}
							?>
							<div class="col-sm-12 text-right mt30">
								<button type="button" class="btn btn-sm btn-continue"  data-toggle="modal"  data-backdrop="static" data-keyboard="false"  data-target="#addupdateModal" id="addBtn"><i class="fa fa-plus fa-fw"></i><?php echo lang('app.language_school_teachers_add_btn'); ?></button>
								<button type="button" <?php echo (empty($teachersData['teachers'])) ? 'disabled' : '' ?> class="btn btn-sm btn-continue"  data-toggle="modal" data-backdrop="static" data-keyboard="false"  data-target="#updateaddModal" id="editBtn"><i class="fa fa-edit fa-fw"></i><?php echo lang('app.language_school_teachers_viewedit_btn'); ?></button>
								<button type="button" <?php echo (empty($teachersData['teachers'])) ? 'disabled' : '' ?> class="btn btn-sm btn-continue"  id="deleteBtn"><i class="fa fa-trash-o fa-fw"></i><?php echo lang('app.language_school_teachers_delete_btn'); ?></button>
							</div>
							<div class="col-sm-12">
							<div class="table-responsive mt40">
								<table class="table table-bordered institution_table">
									<thead>
										<tr> 
											<th>&nbsp;</th>
											<th><?php echo lang('app.language_school_teacher_label_name'); ?></th>
											<th><?php echo lang('app.language_school_teacher_label_email_address'); ?></th>
											<th><?php echo lang('app.language_school_teacher_label_department'); ?></th>
											<th><?php echo lang('app.language_school_teacher_label_classes'); ?></th>
											<th><?php echo lang('app.language_school_teacher_label_date_logged'); ?></th>
										</tr>
									</thead>
									<tbody>
									<?php
									if (!empty($teachersData['teachers'])) {
									$i = 0;
									foreach ($teachersData['teachers'] as $teacher) {

									?>
									<tr>
										<td align="center">
											<input type="radio" data-classstatus="<?php echo isset($teachersData['class_associated_data'][$teacher->id]) ? @get_class_status($teachersData['class_associated_data'][$teacher->id]) : 'inactive'; ?>"  name="teacher_id"  value ="<?php echo base64_encode($this->encrypter->encrypt($teacher->id)); ?>" <?php echo ($i == 0) ? 'checked="checked"' : ''; ?>  />
										</td>
										<td>
											<a href="#"  data-toggle="tooltip" title="<?php echo $teacher->firstname . ' ' . $teacher->lastname; ?>">
											<?php echo $user_name = strlen($teacher->firstname.' '.$teacher->lastname) > 25 ? mb_substr($teacher->firstname.' '.$teacher->lastname,0,25)."..." : $teacher->firstname.' '.$teacher->lastname;?></a> 
										<td>
											<a href="#"  data-toggle="tooltip" title="<?php echo $teacher->email; ?>">  
											<?php echo strlen($teacher->email) > 30 ? substr($teacher->email, 0, 30) . "..." : $teacher->email; ?></td>
										<td>
											<a href="#"  data-toggle="tooltip" title="<?php echo $teacher->department; ?>">  
											<?php echo strlen($teacher->department) > 30 ? mb_substr($teacher->department, 0, 30) . "..." : $teacher->department; ?>
										</td>
										<td>
											<?php
											if ($teacher->classes != "NULL") {


												$classes = explode('@', $teacher->classes);
												$classes_ids = explode('@', $teacher->class_ids);
												$teacher_class_ids = explode('@', $teacher->teacher_class_id);
												$teacher_class_status = explode('@', $teacher->class_status);

												for ($j = 0; $j < count($classes); $j++) {
													for ($n = 0; $n < count($teacher_class_status) - 1; $n ++) {

														if ($teacher_class_status[$n] < $teacher_class_status[$n + 1]) {
															//teacher_class_status - sorted based on active class first
															$temp = $teacher_class_status[$n + 1];
															$teacher_class_status[$n + 1] = $teacher_class_status[$n];
															$teacher_class_status[$n] = $temp;
															//teacher_class_ids - sorted based on active class first
															$temp1 = $teacher_class_ids[$n + 1];
															$teacher_class_ids[$n + 1] = $teacher_class_ids[$n];
															$teacher_class_ids[$n] = $temp1;
															//class_ids - sorted based on active class first
															$temp2 = $classes_ids[$n + 1];
															$classes_ids[$n + 1] = $classes_ids[$n];
															$classes_ids[$n] = $temp2;
															//classes - sorted based on active class first
															$temp3 = $classes[$n + 1];
															$classes[$n + 1] = $classes[$n];
															$classes[$n] = $temp3;
														}
													}
												}
												?>          
												<?php
													// echo '<pre>'; print_r($classes); echo '</pre>';
												$disp_shw_inact_cls = 1;
												for ($k = 0; $k < count($classes); $k++) {

													if ($teacher_class_status[$k] == 1) {
														?>                  
														<span class="act_class" >
															<a href="<?php echo site_url('school/dashboard') . '/?view=classview&classid=' . $classes_ids[$k] . '&teacher_class_id=' . $teacher_class_ids[$k] . '&class_name=' . $classes[$k]; ?>" style="text-decoration: underline;"><?php echo $classes[$k]; ?> <?php echo '- Active'; ?> </a>
														</span>
														</br>

														<?php
													} else {
														if ($disp_shw_inact_cls == 1) {
															?>
															<a href="#inact_class<?php echo $i; ?>" class="pull-right fa fa-caret-down showInactive" id="show_inactive<?php echo $i; ?>" onclick = "showInactive(<?php echo $i ?>)" >show inactive groups</a>
															</br>                       
														<?php
														}
														$disp_shw_inact_cls ++;
														?>   
														<span class="inact_class<?php echo $i; ?>"  id ="showActiveClassId" style = "display:none" >
															<a href="<?php echo site_url('school/dashboard') . '/?view=classview&classid=' . $classes_ids[$k] . '&teacher_class_id=' . $teacher_class_ids[$k] . '&class_name=' . $classes[$k]; ?>" style="text-decoration: underline;"><?php echo $classes[$k]; ?> <?php echo '- Inactive'; ?> </a>             
														</span>
														</br>
													<?php } ?>
												<?php
												}
											}
											?>

											
										</td>
										<td><?php echo (isset($teacher->last_logged) && $teacher->last_logged != 0) ? date('d-m-Y', $teacher->last_logged) : ''; ?></td>
									</tr>   
									<?php
									$i++;
									}	
									} else {
									?>                                      
									<tr>
										<td colspan="6">
											<div class="alert alert-danger fade in">
												<a href="#" class="close" data-dismiss="alert">&times;</a>
												<?php echo lang('app.language_school_no_teachers'); ?>
											</div>
										</td>
									</tr>                                       
									<?php } ?>
									</tbody>
								</table>
							</div>
							<div class="institution_pagination">
                                <nav class="text-right">
									   <?php if ($teachersData['pager']) :?>
									<?= $teachersData['pager']->links('pagination_teachers') ?>
									<?php endif ?> 
                                </nav>
							</div>
							</div>
						</div>
						<div role="tabpanel" class="tab-pane <?php echo ($this->session->get('tab_venues')) ? 'active' : ''; ?>" id="venues">
							<div class="col-sm-12 text-right">
								<?php if(((in_array('1', $institute_courseTypes))|| (in_array('2', $institute_courseTypes)) || (in_array('3', $institute_courseTypes)))){ ?>
								<button type="button" class="btn btn-sm btn-continue"  data-toggle="modal"  data-backdrop="static" data-keyboard="false"  data-target="#addupdateModalVenue" id="addBtnVenue"><i class="fa fa-plus fa-fw"></i><?php echo lang('app.language_distributor_add'); ?></button>
								<?php }else{ ?>
								<button type="button" disabled class="btn btn-sm btn-continue" id="addBtnVenue"><i class="fa fa-plus fa-fw"></i><?php echo lang('app.language_distributor_add'); ?></button>
								<?php } ?>
								<button type="button" <?php echo (empty($venueData['results'])) ? 'disabled' : '' ?> class="btn btn-sm btn-continue"  data-toggle="modal" data-backdrop="static" data-keyboard="false"  data-target="#updateaddModalVenue" id="editBtnVenue"><i class="fa fa-edit fa-fw"></i><?php echo lang('app.language_distributor_edit'); ?></button>
								<button type="button" <?php echo (empty($venueData['results'])) ? 'disabled' : '' ?> class="btn btn-sm btn-continue"  id="deleteBtnVenue"><i class="fa fa-trash-o fa-fw"></i><?php echo lang('app.language_distributor_delete'); ?></button>
							</div>
							<div class="col-sm-12">
							<div class="table-responsive mt40">
								<table class="table table-bordered institution_table">
									<thead>
									<tr>
										<th>&nbsp;</th>
										<th>
										<?php if(!empty($results) && count($results) > 1){ ?>
											<?php echo anchor(current_url()."?order=" .(($this->request->getGet('order') == 'DESC') ? 'ASC' : 'DESC'), lang('app.language_distributor_venue_name'). (($this->request->getGet('order') == 'DESC') ? '&nbsp;<span class="glyphicon glyphicon-arrow-up"></span>' : '&nbsp;<span class="glyphicon glyphicon-arrow-down"></span>')); ?>
										<?php }else{ ?>
											<?php echo lang('app.language_distributor_venue_name');?> 
										<?php } ?>
										</th>
										<th><?php echo lang('app.language_distributor_venue_address'); ?></th>
										<th><?php echo lang('app.language_distributor_label_city'); ?></th>
										<th><?php echo lang('app.language_distributor_label_firstname'); ?></th>
										<th><?php echo lang('app.language_distributor_label_lastname'); ?></th>
										<th><?php echo lang('app.language_distributor_label_email'); ?></th>
									</tr>
									</thead>
									<tbody>
									<?php
									if (!empty($venueData['results'])) {
										$i = 0;
										foreach ($venueData['results'] as $venuer) { ?>
										<tr>
											<td><input type="radio" name="venue_id"  value="<?php echo base64_encode($this->encrypter->encrypt($venuer->id)); ?>" <?php echo ($i==0) ? 'checked="checked"' :''; ?>  /></td>
											<td>
												<a href="#"  data-toggle="tooltip" title="<?php echo $venuer->venue_name; ?>">
												<?php echo $user_name = strlen($venuer->venue_name) > 25 ? substr($venuer->venue_name,0,25)."..." : $venuer->venue_name;?></a> 
											<td>
												<a href="#"  data-toggle="tooltip" title="<?php echo  ($venuer->address_line2!='') ? $venuer->address_line1.',&nbsp;'.$venuer->address_line2 : $venuer->address_line1;?>">  
												<?php
												$ven_addrs1 = strlen($venuer->address_line1) > 18 ? substr($venuer->address_line1,0,18)."..." : $venuer->address_line1;
												$ven_addrs2 = strlen($venuer->address_line2) > 18 ? substr($venuer->address_line2,0,18)."..." : $venuer->address_line2;
												echo  ($venuer->address_line2!='') ? $ven_addrs1.',&nbsp;'.$ven_addrs2 : $ven_addrs1;?></td>
											<td>
												<a href="#"  data-toggle="tooltip" title="<?php echo $venuer->city;?>">  
												<?php echo $venuer->city; ?>
											</td>
											<td>
												<a href="#"  data-toggle="tooltip" title="<?php echo $venuer->first_name;?>">  
												<?php echo $venuer->first_name; ?>
											</td>
											<td>
												<a href="#"  data-toggle="tooltip" title="<?php echo $venuer->last_name;?>">  
												<?php echo $venuer->last_name; ?>
											</td>
											<td>
												<a href="#"  data-toggle="tooltip" title="<?php echo $venuer->email;?>">  
												<?php echo $venuer->email; ?>
											</td>
										</tr> <?php
										$i++;
										}
									} else { ?>                                      
										<tr>
											<td colspan="8">
												<div class="alert alert-danger fade in">
													<a href="#" class="close" data-dismiss="alert">&times;</a>
													<?php echo lang('app.language_distributor_no_venue_available_msg'); ?>
												</div>
										   </td>
									   </tr>                                       
									<?php } ?>
									</tbody>
								</table>
							</div>
							<div class="institution_pagination">
                                <nav class="text-right">
									   <?php if ($venueData['pager']) :?>
									<?= $venueData['pager']->links('pagination_list_venues') ?>
									<?php endif ?> 
                                </nav>
							</div>
							</div>
					   </div>
					   <div role="tabpanel" class="tab-pane <?php echo ($this->session->get('tab_events')) ? 'active' : ''; ?>" id="events">
							<div class="col-sm-12 text-right">
								<?php
								$checked = ''; 
								if($this->session->get('event_list') == 0 || $this->session->get('event_list') == NULL){
									$checked = 'checked';
								} ?>
								<label class="checkbox-inline"><input type="checkbox" name="status_events" id="status_events"  <?php echo $checked; ?> value="<?php echo 'active'; ?>"><?php echo lang('app.language_school_event_checkbox_filter_future'); ?></label>
								<?php if(((in_array('1', $institute_courseTypes))|| (in_array('2', $institute_courseTypes)) || (in_array('3', $institute_courseTypes)))){?>
								<button type="button"<?php echo ($test_events_data['venues'] == 0) ? 'disabled' : '' ?> class="btn btn-sm btn-continue"  data-toggle="modal"  data-backdrop="static" data-keyboard="false"  data-target="#addModalEvents" id="addBtnEvents"><i class="fa fa-plus fa-fw"></i><?php echo lang('app.language_school_events_add_btn'); ?></button>
								<?php }else{ ?>
								<button type="button" disabled class="btn btn-sm btn-continue" id="addBtnEvents"><i class="fa fa-plus fa-fw"></i><?php echo lang('app.language_school_events_add_btn'); ?></button>
								<?php } ?>
								<button type="button" <?php echo (empty($test_events_data['results'])) ? 'disabled' : '' ?> class="btn btn-sm btn-continue"  data-toggle="modal" data-backdrop="static" data-keyboard="false"  data-target="#updateModalEvents" id="editBtnEvents"><i class="fa fa-edit fa-fw"></i><?php echo lang('app.language_school_events_viewedit_btn'); ?></button>
								<button type="button" <?php echo (empty($test_events_data['results'])) ? 'disabled' : '' ?> class="btn btn-sm btn-continue"  id="deleteBtnEvents"><i class="fa fa-trash-o fa-fw"></i><?php echo lang('app.language_school_events_delete_btn'); ?></button>
							</div>
							<div class="col-sm-12">
								<div class="table-responsive mt40">
									<table class="table table-bordered institution_table">
									<thead>
										<tr> 
											<th>&nbsp;</th>
											<th><?php if (!empty($test_events_data['results']) && count($test_events_data['results']) > 1) { ?>
											<?php echo anchor(current_url() . "?events=" . (($this->request->getGet('events') == 'ASC') ? 'DESC' : 'ASC'), lang('app.language_school_events_label_date_time') . (($this->request->getGet('events') == 'ASC') ? '&nbsp;<span class="glyphicon glyphicon-arrow-up"></span>' : '&nbsp;<span class="glyphicon glyphicon-arrow-down"></span>'), array('style' => 'color:white;')); ?>
											<?php } else { ?>
												<?php echo lang('app.language_school_events_label_date_time'); ?>
											<?php } ?></th>
											<th><?php echo lang('app.language_school_events_label_venue'); ?></th>
											<th><?php echo lang('app.language_school_events_label_products'); ?></th>
											<th><?php echo lang('app.language_school_events_label_no_learners'); ?></th>
											
										</tr>
									</thead>
									<tbody>
										<?php
										if (!empty($test_events_data['results'])) {
										$i = 0;
										foreach ($test_events_data['results'] as $event_create) {
										?>      
										<tr>
											<td align="center"> 
												<input type="radio" class="radio_event" name="radio_event" value="<?php echo  base64_encode($this->encrypter->encrypt($event_create['id']));?>" <?php echo ($i == 0) ? 'checked ="checked"' : ''; ?>>
												<?php
												$result = $this->eventmodel->fetch_event_by_id($event_create['id']);
                                                                                                $current_utc_details = @get_current_utc_details();
                                                                                                $current_utc_timestamp = $current_utc_details['current_utc_timestamp']; 
												if ($current_utc_timestamp > $result['start_date_time']) {
													$status = "1"; //event is past
												} 
												elseif ($test_events_data['addedLearnersCount'][$event_create['id']] > 0) {
													$status = "2"; //some learners are there
												} else {
													$status = "3"; //no learners
												}
												?>
												<input type = "hidden" class="eventDeleteHidden" name = "eventDeleteHidden" value="<?php echo $status; ?>">
											</td>
											<td>
                                                                                            <?php
                                                                                            $institution_zone_values = @get_institution_zone_from_utc($tz_to, $event_create['start_date_time'], $event_create['end_date_time']);
                                                                                            ?>
                                                                                            <?php echo $institution_zone_values['institute_event_date']; ?> </br> <?php echo $institution_zone_values['institute_start_time'] . " - " . $institution_zone_values['institute_end_time']; ?>
											</td>
											<?php 
											$hover = $event_create['venue_name']." ".$event_create['address_line1'].",".$event_create['city'].",".$event_create['countryName'].",".$event_create['contact_no'].",".$event_create['location_URL'].",".$event_create['notes'];
											?>
											<td > <a href = "#" data-toggle="tooltip" title = "<?php echo $hover;?>">
												<?php echo $user_name = strlen($event_create['venue_name']) > 50 ? substr($event_create['venue_name'],0,50)."..." : $event_create['venue_name'];?> 
												</a>
											</td>
											<td><?php
												$productname = explode(',', $event_create['product_name']);
												foreach ($productname as $val) {
													echo $val . '</br>';
												}
												?>                                                                
											</td>  
											<td>
											 <?php 
											   $eventtimediff = $result['start_date_time'] - $current_utc_timestamp;
											   if($test_events_data['addedLearnersCount'][$event_create['id']] == 0){
												   if($eventtimediff > 1800){
												   
													   ?>  <span class="badge"> <?php echo $test_events_data['addedLearnersCount'][$event_create['id']]; ?></span>&nbsp;<a style="text-decoration: underline;" href="<?php echo site_url('school/learner_allocation/'.$event_create['id']); ?>"><?php echo "Add Learners"; ?></a> <?php
												   
												   }else{
												  ?>  <span class="badge"> <?php echo $test_events_data['addedLearnersCount'][$event_create['id']]; ?></span>&nbsp;<a style="text-decoration: underline;" href="<?php echo site_url('school/learner_allocation/'.$event_create['id']); ?>"><?php echo "View Learners"; ?></a> <?php  
												   }
												 
											   } elseif($eventtimediff > 1800){
												  ?> <span class="badge"> <?php echo $test_events_data['addedLearnersCount'][$event_create['id']]; ?></span>&nbsp;<a style="text-decoration: underline;" href="<?php echo site_url('school/learner_allocation/'.$event_create['id']); ?>"><?php echo "View/add learners"; ?></a><?php  
												  
											   } else{
												?> <span class="badge"> <?php echo $test_events_data['addedLearnersCount'][$event_create['id']]; ?></span>&nbsp;<a style="text-decoration: underline;" href="<?php echo site_url('school/learner_allocation/'.$event_create['id']); ?>"><?php echo "View learners"; ?></a><?php   
											   }
?>
											</td>
										</tr>
										<?php
										$i++;
											}
										} else {
										?>
										<tr>
											<td colspan="7">
												<div class="alert alert-danger fade in">
													<a href="#" class="close" data-dismiss="alert">&times;</a>
													<?php
													if ($test_events_data['venues'] > 0) {
														echo lang('app.language_school_no_event');
													} else {
														echo lang('app.language_school_no_event_venue');
													}
													?>
												</div>
											</td>
										</tr>                                       
										<?php } ?> 
									</tbody>
									</table>
								</div>
								<div class="institution_pagination">
								<nav class="text-right">
								<?php if (($test_events_data['pager'])) :?>
									<?= $test_events_data['pager']->links('pagination_list_events') ?>
									<?php endif ?> 
								</nav>
								</div>
							</div>
						</div>
						<div role="tabpanel" class="tab-pane <?php echo ($this->session->get('tab_results')) ? 'active' : ''; ?>" id="results">
							<div class="list_view">
							<div style="padding:0px 10px;">
								<form id="results_tds" action="<?php echo site_url('school/export_tds_results'); ?>" method="POST">
									<div class="row">
									<div class="form-group">
										<div class="col-sm-12 col-xs-12">
											<h4>Products to include in report</h4>
											<!-- WP-1197 START AND WP-1204-->
											<?php if(isset($courses)){
											foreach ($courses as $key => $value){ 
											$course_name =  strtolower($key); ?>
												<div class="accord">
													<div class="row" style="margin:0px;">
    													<div class="pull-left radio_btn">
    														<input type="radio" name="product_type" id="<?php echo ($course_name != 'stepcheck') ?  $course_name."_type" : "benchmark_type"; ?>" <?php if(isset($product_type) && $product_type == $course_name){echo 'checked';} ?> value="<?php echo $course_name; ?>" />
    														<label for="<?php echo ($course_name != 'stepcheck') ?  $course_name."_type" : "benchmark_type"; ?>" style="font-weight: normal; margin-left: 10px; font-size: 15px;"><?php echo $key; ?></label>
    													</div>
													</div>
												</div>
												<div class="clearfix"></div> <?php
												}
											} ?>
                                                <!-- WP-1197 END-->
										</div>
									</div>	
									</div>
									<div class="date_picker">
										<div class="row mt20">
											<div class="col-sm-4 col-xs-12">
												<div class="form-group">
												<label>Start Date</label>
												<div class="input-group">
													<input type="text" id='result_startdate' class="form-control input-sm" name="result_startdate" value="<?php if(isset($result_startdate)) echo $result_startdate; ?>" />
													<label for='result_startdate' class="input-group-addon">
															<span class="glyphicon glyphicon-calendar"></span>
													</label>
												</div>
												</div>
											</div>
											<div class="col-sm-4 col-xs-12">
												<div class="form-group">
												<label>End Date</label>
												<div class="input-group">
													<input type="text" id='result_enddate' class="form-control input-sm" name="result_enddate" value="<?php if(isset($result_enddate)) echo $result_enddate; ?>" />
													<label for='result_enddate' class="input-group-addon">
														<span class="glyphicon glyphicon-calendar"></span>
													</label>
												</div>
												</div>
											</div>
											<div class="col-sm-12">
												<p>The maximum period for which a report can be requested is 1 month.</p>
											</div>
											<div class="clearfix"></div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-8">
											<div class="radio_group mt20">
												<h4>Output</h4>
												<div class="form-group">
												<ul class="print_list">
													<li>
														<input type="radio" name="result_type" value="csv" id="result_csv" <?php if(isset($result_type) && $result_type == 'csv'){echo 'checked';} ?> <?php echo (isset($product_type) && $product_type != "stepcheck") ? 'disabled' : ''; ?>/>
														<label for="result_csv" class="result_csv">CSV - this will produce a CSV file with one row per learner result</label>
													</li>
													<li>
														<input type="radio" name="result_type" value="pdf" id="result_pdf" <?php if(isset($result_type) && $result_type == 'pdf'){echo 'checked';} ?> <?php echo (isset($product_type) && $product_type == "stepcheck") ? 'disabled' : ''; ?>/>
														<label for="result_pdf" class="result_pdf">PDF - this will produce a result statement per learner</label>
													</li>
												</ul>
												</div>
											</div>
										</div>
										<div class="col-sm-8 text-right">
										<?php //if (array_key_exists("Benchmarking", $courses)) { /*WP1204*/?>
											<button type="submit" class="btn btn-sm btn-continue" id="tds_results">Submit</button>
										<?php //}?>
										</div>
									</div>
								</form>
							</div>	
							<!-- WP-1221 PDF Bulk download  -->
							<div class="col-sm-12">
							<h3><?php echo lang('app.language_school_pdf_results_download_title'); ?></h3>
							<div class="table-responsive mt40">
								<table class="table table-bordered institution_table">
									<thead>
										<tr> 
											<th><?php echo lang('app.language_school_pdf_results_download_table_taskid'); ?></th>
											<th><?php echo lang('app.language_school_pdf_results_download_table_product_grp'); ?></th>
											<th><?php echo lang('app.language_school_pdf_results_download_table_start_date'); ?></th>
											<th><?php echo lang('app.language_school_pdf_results_download_table_end_date'); ?></th>
											<th><?php echo lang('app.language_school_pdf_results_download_table_submitted_on'); ?></th>
											<th><?php echo lang('app.language_school_pdf_results_download_table_status'); ?></th>
											<th><?php echo lang('app.language_school_pdf_results_download_table_available_until'); ?></th>
										</tr>
									</thead>
									<tbody>
									<?php if ($pdf_result_tasks !=FALSE && count($pdf_result_tasks) > 0) { 
										foreach($pdf_result_tasks as $pdf_result_task){ ?>
										<tr>
											<td> <?php echo $pdf_result_task->id; ?> </td>
											<td> <?php echo ucfirst($pdf_result_task->product_group); ?> </td>
											<td> <?php echo date('d-m-Y', strtotime($pdf_result_task->start_date)); ?> </td>
											<td> <?php echo date('d-m-Y', strtotime($pdf_result_task->end_date)); ?> </td>
											<td> <?php echo date('d-m-Y H:i', strtotime($pdf_result_task->created_on)); ?> </td>
											<td> <?php echo ($pdf_result_task->status == 0) ? lang('app.language_school_pdf_results_download_status_txt') : '<a href="'.site_url('school/zip_download/'. $pdf_result_task->id.'/'.substr($pdf_result_task->file_name, 0, strrpos($pdf_result_task->file_name, '.'))).'">'.lang('app.language_school_pdf_results_download_status_txt1').'</a>'; ?> </td> 
											<td> <?php echo date('d-m-Y', strtotime($pdf_result_task->created_on. ' + 2 days')); ?> </td>
										</tr> <?php
										}
									}else{ ?>
										<tr>
											<td colspan="7">
												<div class="alert alert-danger fade in">
													<a href="#" class="close" data-dismiss="alert">×</a>
													<?php echo lang('app.language_school_pdf_results_download_no_record'); ?>
												</div>
											</td>
										</tr> <?php 
									} ?>
									</tbody>
								</table>
							</div>
							</div>							
							</div>
						</div>
							</div>
                        </div>                  
                    </div> 
                </section>
            </div>
			</div>
        </div>
<?php } ?>  

    <!-- Order1 modal box -->
    <div id="order1modal" class="modal fade" role="dialog"">

        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?php echo lang('app.language_school_label_order_tests'); ?></h4>
                </div>
                <div class="modal-body">
<?php echo form_open('school/ordertest', array('role' => 'form bv-form', 'id' => 'order1_form', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>
                    <div class="form-group">
                        <label for="InputFieldA"><?php echo lang('app.language_school_label_order_name'); ?> <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="order_name" name="order_name"  value="" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="InputFieldA"><?php echo lang('app.language_tbl_label_type_of_token'); ?> <span style="color: red;">*</span></label>
						
						<?php $institute_courseTypes = isset($institute_courseType) ? array_map('current', $institute_courseType) : '';?>

						<select class="form-control" name="type_of_token" id="type_of_token">
							
                            <option value="">Please select</option>
                            <?php if (in_array('2', $institute_courseTypes) && in_array('3', $institute_courseTypes)) { ?>
                                <option value="cats_core_or_higher">CATs Step Core/Higher</option>
                            <?php } else { ?>
                                <?php if (in_array('2', $institute_courseTypes)) { ?>
                                    <option value="cats_core">CATs Step Core</option>
                                <?php } elseif (in_array('3', $institute_courseTypes)) { ?>                         
                                    <option value="cats_higher">CATs Step Higher</option>
                                <?php }} ?>
							<!-- <option value="catslevel">CATs level</option>  
                            <option value="benchmarktest">Benchmarking test</option>-->
                            <!-- <option value="speaking_test">Speaking Test</option> WP-1234-->
							<?php if (in_array('4', $institute_courseTypes)) { ?>
								<?php if(isset($order_types) && $order_types != ''){ ?>
									<?php foreach($order_types as $type): ?>
										<?php if($type->status == 1) { ?> <!-- WP-1269 - Display only active product -->
											<option value="<?php echo $type->test_slug; ?>"><?php echo $type->test_name; ?></option>
										<?php } ?>
									<?php endforeach; ?>
								<?php } ?>
							<?php } ?>							
                        </select>			





                    </div>                                      
                    <div class="form-group">
                        <label for="InputFieldA"><?php echo lang('app.language_school_label_no_of_tests'); ?> <span style="color: red;">*</span></label>
                        <input type="text" class="form-control" id="number_of_tests" name="number_of_tests" value="" required>
                    </div>
                    
                    <div class="form-group final_test_arrangement">
                        <label for="InputFieldA"><?php echo lang('app.language_school_label_final_test_arrangement'); ?> <span style="color: red;">*</span></label><br>
                        <input type="radio" name="is_supervised" value="0" > <?php echo lang('app.language_school_label_final_test_arrangement_unsupervised'); ?><br>
                    	<input type="radio" name="is_supervised" value="1" > <?php echo lang('app.language_school_label_final_test_arrangement_supervised'); ?><br><br>
                        <p><?php echo lang('app.language_school_label_final_important_note'); ?></p>
                    </div>
                        
                    <div class="form-group">
                        <label for="InputFieldA"><?php echo lang('app.language_school_label_order_description'); ?> <span style="color: red;">*</span></label>
                        <textarea id="order_desc"  name="order_desc" class="form-control"  required ></textarea>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <input type="submit" id="order1_submit" name="order1_submit" class="btn btn-sm btn-continue" value="<?php echo lang('app.language_school_label_continue'); ?>" />
                        </div>
                    </div>
<?php echo form_close(); ?>                             
                </div>
            </div>
        </div>
    </div>

    <!-- Order2 modal box -->
    <div id="order2modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?php echo lang('app.language_school_label_order_tests'); ?></h4>
                </div>
                <div class="modal-body">
<?php echo form_open('school/order_pay', array('role' => 'form bv-form', 'id' => 'order2_form', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>                                   
                    <div class="form-group">
                        <span><?php echo lang('app.language_school_label_order_text1'); ?> </span>
                    </div>
                    <div class="form-group">
                        <label for="InputFieldA"><?php echo lang('app.language_school_label_order_name'); ?>: </label>
                        <span id="ordername"></span>
                    </div>
                    <div class="form-group">
                        <label for="InputFieldA"><?php echo lang('app.language_tbl_label_token_type'); ?>: </label>
                        <span id="typeoftoken"></span>
                    </div>
                    <div class="form-group">
                        <label for="InputFieldA"><?php echo lang('app.language_school_label_order_description'); ?>:  </label>
                        <span id="orderdesc"></span>
                    </div>
                    <div class="form-group">
                        <label for="InputFieldA"><?php echo lang('app.language_school_label_no_of_tests'); ?>: </label>
                        <span id="no_of_test"></span>
                    </div>
                    <div class="form-group is_supervised">
                        <label for="InputFieldA"><?php echo lang('app.language_school_label_final_test_arrangement'); ?>: </label>
                        <span id="final_test_arrangement"></span>
                    </div>  
                    <!-- <div class="form-group">
                        <label for="InputFieldA"><?php echo lang('app.language_school_label_distributor'); ?>: </label>
                    </div> --><!-- WP-1104 Hided distributors details in Ordercodes popup2 -->
                    <div class="form-group">
                        <span><a href="#" id="setting_change"><?php echo lang('app.language_school_change_details'); ?></a></span>
                    </div>                                      
                    <div class="form-group" style="background-color:#f6f6f6; padding:15px; border-radius:4px;">
                        <!-- WP-1104 Hided distributors details in Ordercodes popup2 -->
                        <span><?php echo lang('app.language_school_label_order_text4'); ?></span><span><a href="<?php echo site_url('pages/terms_conditions'); ?>" target="_blank" id=""><?php echo lang('app.language_school_terms_of_use'); ?></a></span><span><?php echo lang('app.language_school_label_order_text5'); ?></span><span>&nbsp;</span><input type="checkbox" name="order_agree" value="order_agree" required>
                    </div>                                      
                    <input type="submit" id="order2_submit" name="order2_submit" class="btn btn-sm btn-continue" value="<?php echo lang('app.language_school_label_continue'); ?>" />
<?php echo form_close(); ?>                             
                </div>
            </div>
        </div>
    </div>

    <!-- Order3 modal box -->
    <div id="order3modal" class="modal fade" role="dialog" style="position:absolute;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?php echo lang('app.language_school_label_order_tests'); ?></h4>
                </div>
                <div class="modal-body">
<?php echo form_open('school/order_pay', array('role' => 'form bv-form', 'id' => 'order3_form', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>                                   
                    <div class="form-group">
                        <label for="InputFieldA"><?php echo lang('app.language_school_text_order_please_select_payment'); ?></label>
                        </br>
                        <input type="radio" name="payment_method" required value="card" disabled> <?php echo lang('language_school_label_order_card'); ?><br>
                        <input type="radio" name="payment_method" value="paypal"> <?php echo lang('app.language_school_label_order_paypal'); ?><br>
                        <input type="radio" name="payment_method" value="none"> <?php echo 'none'; ?><br>
                    </div>
                    <div id="card-form">
                        <div class="form-group">
                            <label for="InputFieldA"><?php echo 'Card Number (no spaces)'; ?></label>
                            <input type="text" class="form-control" id="card_number" name="card_number" value="" >
                        </div>                                      
                        <div class="form-group">
                            <label for="InputFieldA"><?php echo 'Name (as it appears on card)'; ?></label>
                            <input type="text" class="form-control" id="card_name" name="card_name" value="" >
                        </div>
                        <div class="form-group">
                            <label for="InputFieldA"><?php echo 'Card security code:'; ?></label>
                            <input type="text" class="form-control" id="security_code" name="security_code" value="" >
                        </div>                                      
                    </div>                                      
                    <input type="submit" id="order3_submit" name="order3_submit" class="btn btn-lg btn-warning" value="<?php echo lang('language_school_order_make_payment'); ?>" />
<?php echo form_close(); ?>                             
                </div>
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
        <div class="modal-dialog modal-xs">
            <div class="modal-content" >
                <img class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" />
                <div class="modal-header" style="border-bottom: 0px solid #e5e5e5;">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"
                        style="font-weight: bold; text-align: center;">

<?php //echo lang('app.language_distributor_add_venue');      ?>&nbsp;<img class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" /></h4>
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
        <div class="modal-dialog modal-xs">
            <div class="modal-content" >
                <div class="modal-header" style="border-bottom: 0px solid #e5e5e5;">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title" style="font-weight: bold; text-align: center;">&nbsp;<img class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" />
                    </h4>
                </div>
                <div class="modal-body" >
                        <div class="modal-footer" style="border-top: 0px solid #e5e5e5;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div id="addupdateModalVenue" class="modal fade" role="dialog" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content" >
                <img class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" />
                <div class="modal-header" style="border-bottom: 0px solid #e5e5e5;">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"
                        style="font-weight: bold; text-align: center;">

<?php //echo lang('app.language_distributor_add_venue');      ?>&nbsp;<img class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" /></h4>
                </div>
                                <div class="modal-body" >
							</div>
                <div class="modal-footer" style="border-top: 0px solid #e5e5e5;"></div>
            </div>
        </div>
    </div>

</div>
<div class="container">
    <div id="updateaddModalVenue" class="modal fade" role="dialog" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content" >
                <div class="modal-header" style="border-bottom: 0px solid #e5e5e5;">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title" style="font-weight: bold; text-align: center;">&nbsp;<img class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" />
                    </h4>
                </div>
                <div class="modal-body" >

                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div id="addModalEvents" class="modal fade" role="dialog" >
        <div class="modal-dialog modal-xs">
            <div class="modal-content" >
                <img class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" />
                <div class="modal-header" style="border-bottom: 0px solid #e5e5e5;">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"
                        style="font-weight: bold; text-align: center;">

<?php //echo lang('app.language_distributor_add_venue');      ?>&nbsp;<img class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" /></h4>
                </div>
                <div class="modal-body modal_padding" >

                </div>               
            </div>
        </div>
    </div>

</div>

<div class="container">
    <div id="updateModalEvents" class="modal fade" role="dialog" >
        <div class="modal-dialog modal-xs">
            <div class="modal-content" >
                <div class="modal-header" style="border-bottom: 0px solid #e5e5e5;">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title" style="font-weight: bold; text-align: center;">&nbsp;<img class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" />
                    </h4>
                </div>
                <div class="modal-body" >
                        <div class="modal-footer" style="border-top: 0px solid #e5e5e5;"></div>
                </div>
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
                <h4 class="modal-title"><?php echo lang('app.language_school_learners_history'); ?>&nbsp;<img class="loading_history" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" /></h4>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Done</button>
            </div>
        </div>

    </div>
</div>
<!-- Modal -->
<div id="nextlevelModal" class="modal fade" role="dialog" >
    <div class="modal-dialog modal-xs">
        <div class="modal-content" >
            <div class="modal-header" style="border-bottom: 0px solid #e5e5e5;">
                <button type="button" class="close" data-dismiss="modal"
                        aria-hidden="true">&times;</button>
                <h4 class="modal-title" style="font-weight: bold;"><?php echo lang('app.language_school_u13learner_title'); ?>&nbsp;<img class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" />
                </h4>
            </div>
            <div class="modal-body" >

            </div>
            <div class="modal-footer" style="border-top: 0px solid #e5e5e5;"></div>
        </div>
    </div>
</div>


<!-- Modal for add U13 Learner -->
<div id="addupdateModal_u13" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom: 0px solid #e5e5e5;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" style="font-weight: bold; text-align: center;">&nbsp;<img class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" />

				<?php //echo lang('app.language_distributor_add_venue');      ?>
                </h4>
            </div>
            <div class="modal-body">
                <style>
                    #institute_form p {
                        color: red;
                    }

                    .form-actions {
                        margin: 0;
                        background-color: transparent;
                        text-align: center;
                    }

                    .has-feedback label~.form-control-feedback {
                        top: 0px;
                    }

                    .has-feedback .form-control {}

                    .dropdown-menu {
                        width: 100%;
                    }
                </style>
                <!-- /.row -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-plus fa-fw"></i><?php echo lang('app.language_school_add_u13learner'); ?>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12 clearfix">
                                        <form action="<?php echo site_url('school/post_u13learner'); ?>" class="form bv-form" role="form" id="u13_learner_form"
                                              data-bv-feedbackicons-valid="glyphicon glyphicon-ok" data-bv-feedbackicons-invalid="glyphicon glyphicon-remove"
                                              data-bv-feedbackicons-validating="glyphicon glyphicon-refresh" enctype="multipart/form-data"
                                              method="post" accept-charset="utf-8">
                                            <fieldset>                                          
                                                <legend><?php echo lang('app.language_school_add_u13learner'); ?></legend>
                                                <div class="row">
                                                    <div class="form-group col-xs-12 clearfix">
                                                        <label for="first_name"><?php echo lang('app.language_admin_institutions_first_name'); ?><span>*</span></label>
                                                        <input
                                                            type="text" class="form-control" name="firstname" id="firstname_u13"
                                                            placeholder="First name" value="">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-xs-12 clearfix">
                                                        <label for="last_name"><?php echo lang('app.language_admin_institutions_second_name'); ?><span>*</span></label>
                                                        <input
                                                            type="text" class="form-control" name="lastname" id="lastname_u13"
                                                            placeholder="Second name" value="">
                                                    </div>
                                                </div>                                          
                                                <div class="row">
                                                    <div class="form-group col-xs-12 clearfix">
                                                        <label style="width:100%" for="department"><?php echo lang('app.language_school_u13learner_dob'); ?><span>*</span></label>
                                                        <div class="col-sm-3 nopadleft">
                                                            <input type="number" name="mydob[]" class="form-control mydob mydobdate" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"  placeholder="DD"  min="1" max="31" maxlength="2" value="" >
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <input type="number"  name="mydob[]" class="form-control mydob mydobmonth" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"  min="1" max="12" placeholder="MM"  maxlength="2" value="">
                                                        </div>
                                                        <div class="col-sm-3 width46">
                                                                <?php
                                                                $under13year = date('Y') - 16;
                                                                $yearData = range($under13year, date('Y'));
                                                                rsort($yearData);
                                                                ?>
                                                            <select  name="mydob[]" class="form-control selectpicker mydob mydobyear">
                                                                <option value="">YYYY</option>
<?php
foreach ($yearData as $year):
    ?>
                                                                    <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
<?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                            <div class="clearfix" id="mydob"></div>   
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-xs-12 clearfix">
                                                        <label for="last_name"><?php echo lang('app.language_school_u13learner_gender'); ?><span>*</span></label>                       <select name="mygender" id="mygender"  class="form-control mygender">
                                                            <option value=""><?php echo lang('app.lsetting_please_select'); ?></option>
                                                            <option  value="F"><?php echo lang('app.lsetting_label_gender_female'); ?></option>     
                                                            <option   value="M"><?php echo lang('app.lsetting_label_gender_male'); ?></option>
                                                            <option  value="U"><?php echo lang('app.lsetting_label_gender_not_known'); ?></option>
                                                            <option  value="U"><?php echo lang('app.lsetting_label_gender_not_applicable'); ?></option> 
                                                        </select>                                                   
                                                    </div>
                                                </div>
												<?php $avail_languages = $get_language;  ?>
                                                <div class="row">
                                                    <div class="form-group col-xs-12 clearfix">
                                                        <label for="last_name"><?php echo lang('app.language_admin_institutions_u13_lang_acc_details'); ?><span>*</span></label>                        <select name="lang_acc_det" id="lang_acc_det"  class="form-control mygender">
                                                            <option value=""><?php echo lang('app.lsetting_please_select'); ?></option>
                                                            <?php //CCC -131 - Condition changed to show only the basic languages by using content_status column in language
                                                            foreach ($avail_languages as $avail_lang) {
                                                                    ?>
                                                                    <option  value="<?php echo $avail_lang->language_id; ?>" <?php
                                                                    if (isset($access_language_id) && $access_language_id == $avail_lang->language_id) {
                                                                        echo 'selected="selected"';
                                                                    }
                                                                    ?>> 
                                                                    <?php echo json_decode('"' . $avail_lang->name . '"'); ?></option>   

                                                            <?php }?>
                                                        </select>                                                   
                                                    </div>
                                                </div>                                                  
                                                <?php $institute_courseTypes = isset($institute_courseType) ? array_map('current', $institute_courseType) : '';?>
                                                <div class="row">
                                                    <div class="form-group col-xs-12 clearfix">
                                                        <label for="last_name"><?php echo lang('app.language_school_u13learner_catsproduct'); ?><span>*</span></label>
                                                        <select class="form-control" name="cats_product" id="cats_product">
                                                            <option value="">Please select</option>
                                                            <?php if (in_array('2', $institute_courseTypes)) { ?>
                                                                <option value="cats_core">CATs Step Core</option>
                                                            <?php } if (in_array('1', $institute_courseTypes)) { ?>
                                                                    <option value="cats_primary">CATs Step Primary</option>
                                                                <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-xs-12 clearfix">
                                                        <p style="width: 90%; text-align: justify; display: inline;" ><?php echo lang('app.language_school_u13learner_policy'); ?></p>
                                                        <input type="checkbox" class="" name="cats_data_protection_policy" id="cats_data_protection_policy" >
                                                    </div>
                                                </div>
                                            </fieldset>
                                            <div class="form-group form-actions pull-right" style="clear:both;">
                                                <button type="submit" id="submitBtn_u13" class="btn btn-sm btn-continue"><?php echo lang('app.language_school_u13learner_add_and_finish'); ?></button>
                                                <input type="hidden" name="submit_type"  id="submit_type" value=""/>
                                                <button  id="u13_add_continue" class="btn btn-sm btn-continue"><?php echo lang('app.language_school_u13learner_add_and_continue'); ?></button>                                                   
                                                <img
                                                    id="loading_in" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>">
                                            </div>
                                        </form>
                                    </div>

                                </div>
                                <!-- /.row (nested) -->
                            </div>
                            <!-- /.panel-body -->
                        </div>
                        <!-- /.panel -->
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
                <!-- YEL START -->
                <script>
                $(document).ready(function(){
                	$('#school_report').on('click',function(e){
                		location.href = "<?php echo site_url('report/index'); ?>";
                	});
					$('.changeActive').on("click",function(){
						$( "#tab_u13" ).addClass( "active" );
						$( "#tab_ord" ).removeClass( "active" );
					})
                });
                </script>
                <!-- YEL END -->
                <form action="<?php echo site_url('school/primary_final_result'); ?>" id="primary_results_form" name="primary_results_form" method="POST">
                    <input type="hidden" name="u13id" id="u13id" value="">
                    <input type="hidden" name="u13thirdpartyid" id="u13thirdpartyid" value="">
                </form>

                <?php
                if ($this->session->get('submit_t') == "continue") {
                    $this->session->remove('submit_t', TRUE);
                    $active = ($this->session->get('tab_u13entries') == 1) ? 1 : 0;
                    ?>
                    <script>
                        var active = "<?php echo $active; ?>";
                        if(active == 1){
                            $(function () {
                                $('#addupdateModal_u13').modal('show');
                            });
                        }
                    </script>   
<?php } ?>                  
                <script>
                   $("#addupdateModal_u13").on('hidden.bs.modal', function () {
                        $('#u13_learner_form').find("input[type=text], input[type=hidden],#firstname_u13, #lastname_u13, #mygender, #cats_product,.mydob, .mydobyear").val("");
                        $("input[type=text], input[type=hidden], #mygender, #cats_product,.mydobyear").next('span, p').remove();
                        $("#cats_data_protection_policy"). prop("checked", false);
                        $("#cats_data_protection_policy").next('span, p').remove();
                        $("#mydob").next('span, p').remove();
                    }); 

                    $('.primary_results').on('click', function (e) {
                        $('#u13id').val('');
                        $('#u13id').val($(this).attr("ID"));
						$('#u13thirdpartyid').val('');
                        $('#u13thirdpartyid').val($(this).attr("data-thirdpartyid"));
                        $('#primary_results_form').submit();
                    });

                    $('#u13_add_continue').on('click', function (e) {
                        $('#submit_type').val("add_continue");
                    });
                    
                    $('#submitBtn_u13').on('click', function (e) {
                        $('#submit_type').val("");
						
                    });
					
					$('.mydob').on('change', function() {
						var mydobdate = $('.mydobdate').val();
						var mydobmonth = $('.mydobmonth').val();
						var mydobyear = $('.mydobyear').val();
						$("#mydob").next('span, p').remove();
						if(mydobdate != '' && mydobmonth != '' && mydobyear != ''){
							
							$.ajax({
								type: "POST",
								url: '<?php echo site_url('school/post_checkdob'); ?>',
								data: $('#u13_learner_form').serialize(),
								dataType: 'json',
								success: function (data)
								{
									clear_errors(data);
									if (data.success) {
									} else {
										
										if (data.doberror) {
											 $("#mydob").after("<p style='color:red'>" + data.errors['mydob'] + "</p>");
										}
									}
								}
							});
							return false;
						}
					});

                    $('#u13_learner_form').submit(function (e) {
                        e.preventDefault();
                        $('#submitBtn_u13').attr('disabled', true);
                        $('#u13_add_continue').attr('disabled', true);
                        $('#loading_in').show();
                        $.ajax({
                            type: "POST",
                            url: $(this).attr('action'),
                            data: $(this).serialize(),
                            dataType: 'json',
                            success: function (data)
                            {
                                clear_errors(data);
                                $('#loading_in').hide();
                                if (data.success) {
                                    location.reload();
                                } else {
                                    $('#submitBtn_u13').attr('disabled', false);
                                    $('#u13_add_continue').attr('disabled', false);
                                    if (data.doberror) {
                                        $("#mydob").after("<p style='color:red'>" + data.errors['mydob'] + "</p>");
                                    }else if(data.proderror){
										console.log(data.errors['cats_product']);
										$("#cats_product").after("<p style='color:red'>" + data.errors['cats_product'] + "</p>");
									} else {
                                        set_errors(data);
                                    }
                                }

                            }
                        });
                        return false;
                    });

                    $(".mydobdate").keyup(function (e) {
                        var datevalue = $(this).val();
                        if (datevalue > 31) {
                            $(this).val('');
                            e.preventDefault();
                        }
                    });

                    $(".mydobmonth").keyup(function (e) {
                        var datevalue = $(this).val();
                        if (datevalue > 12) {
                            $(this).val('');
                            e.preventDefault();
                        }
                    });

                    function clear_errors(data)
                    {
                        if (typeof (data.errors) != "undefined" && data.errors !== null) {
                            for (var k in data.errors) {

                                $('#' + k).next('span, p').remove();

                            }
                        }
                    }

                    function set_errors(data)
                    {
                        if (typeof (data.errors) != "undefined" && data.errors !== null) {
                            for (var k in data.errors) {
                                $(data.errors[k]).insertAfter($("#" + k)).css('color', 'red');
                            }
                        }
                    }

                </script>

            </div>

        </div>
    </div>
</div>
<?php if(isset($this->zendesk_access) && $this->zendesk_access == 1){ 
	$user_session_id = (null !== $this->session->get('logged_tier1_userid') && $this->session->get('selected_tierid') != '') ? $this->session->get('logged_tier1_userid') : $this->session->get('user_id'); ?>
	<!-- Start of cats66 Zendesk Widget script WP-1393 -->
	<script type="text/javascript">
		window.zESettings = {
			webWidget: {
				authenticate: {
					jwtFn: function(callback) {
						callback('<?php echo @get_web_widget_token($user_session_id);?>');
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
<style>
	.showInactive
	{
		text-decoration: none;
		color: #2a9682;
		font-size: 18px;
		padding: 7px 0px;
		font-family: 'Montserrat', sans-serif;
	}
	.showInactive.fa-caret-down:before, .show_inactive.fa-caret-up:before {
    font: normal normal normal 14px/1 FontAwesome;
    float: right;
    padding-left: 5px;
    font-size: 18px!important;
}
</style>
