<?php 
use Carbon\Carbon;
//initialization of request
$this->request = \Config\Services::request();
?>

<style>
img.iconPlus {
    margin-bottom: -3.5px;
}
.disabled-icon{
	opacity: 0.5;
}
</style>
<div class="bg-lightgrey"> 
    <div class="container">
        <div class="row">
            <?php include_once 'messages.php';?>
            <section class="col-sm-12">
                <h1 class="user_name"><?php echo lang('app.language_dashboard_welcome') . ', ' . ucfirst($this->session->get('user_firstname')." ".$this->session->get('user_lastname')); ?> </h1>
				<p class="p20"><a href="<?php echo site_url('school/dashboard'); ?>"><span class="fa fa-long-arrow-left"></span> <?php echo lang('app.language_search_events_back_to_dash'); ?></a></p>
                <div class="institution_tab nav_dashboard">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="<?php echo ($this->session->get('tab_orders')) ? 'active' : ''; ?>"><a href="#test_orders" id="tab_orders" aria-controls="home" role="tab" data-toggle="tab"><?php echo lang('app.language_school_order_detail'); ?></a></li>						
					</ul>
                    <!-- Tab panes -->
					<div class="institution_content">
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane <?php echo ($this->session->get('tab_orders')) ? 'active' : ''; ?>" id="test_orders">
                            <div class="row">
                                <div class="col-sm-6">
                                    <p class="text-left order_text" style="font-size: 16px;">
                                        <?php if(!empty($tokens)) { ?>						
                                        <strong>Order: </strong><?php echo $tokens['0']->order_name.', '.$tokens['0']->order_date; ?>
                                        <?php } ?>
                                    </p>
                                       
                                </div>
                                <div class="col-sm-6 text-right">
                                     <button type="button" class="btn btn-sm btn-continue" id ="export_codes" ><?php echo lang('app.language_school_export_codes'); ?></button>
                                </div>
                            </div>                           
                            <div class="table-responsive view-tokens mt40">
                                <table class="table table-bordered institution_table">
                                    <thead>
                                        <tr>
                                            <!--<th>Order date</th>-->
											<th width="12%">                                               
												<?php if(!empty($tokens) && count($tokens) > 1) { ?>												
												<?php  echo anchor(current_url()."?order=" .(($this->request->getGet('order') == 'DESC') ? 'ASC' : 'DESC').'&val=token', lang('app.language_tbl_label_token_id'). ((($this->request->getGet('order') == 'DESC') && ($this->request->getGet('val') == 'token')) ? '&nbsp;<span class="glyphicon glyphicon-arrow-up"></span>' : ((( ($this->request->getGet('order') == 'ASC') && ($this->request->getGet('val') == 'token') )) ? '&nbsp;<span class="glyphicon glyphicon-arrow-down"></span>' : '')), array('style' => 'color:white;')); ?>												
												<?php } else { ?>
												<?php  	echo lang('app.language_tbl_label_token_id');?> 
												<?php } ?>
                                            </th>
                                            <th><?php echo lang('app.language_tbl_label_email') ?></th>
											<?php if(!empty($tokens) ) { ?>
											<?php if(($tokens['0']->type_of_token== "cats_core") || ($tokens['0']->type_of_token == "cats_higher") || ($tokens['0']->type_of_token == "cats_core_or_higher") || ($tokens['0']->type_of_token == "catslevel")){ ?>
                                            <th width="14%">
												<?php if(!empty($tokens) && count($tokens) > 1) { ?>
												<?php  echo anchor(current_url()."?order=" .(($this->request->getGet('order') == 'DESC') ? 'ASC' : 'DESC').'&val=product', lang('app.language_tbl_label_level'). ((($this->request->getGet('order') == 'DESC') && ($this->request->getGet('val') == 'product')) ? '&nbsp;<span class="glyphicon glyphicon-arrow-up"></span>' : ((( ($this->request->getGet('order') == 'ASC') && ($this->request->getGet('val') == 'product') )) ? '&nbsp;<span class="glyphicon glyphicon-arrow-down"></span>' : '')), array('style' => 'color:white;')); ?>
												<?php } else { ?>
												<?php  	echo lang('app.language_tbl_label_level');?> 
												<?php } ?>											
											</th>
                                            <th><?php echo 'Course Progress'; ?></th>
                                            <th>											
												<?php if(!empty($tokens) && count($tokens) > 1) { ?>
												<?php  echo anchor(current_url()."?order=" .(($this->request->getGet('order') == 'DESC') ? 'ASC' : 'DESC').'&val=test_date', lang('app.language_tbl_label_test_booking'). ((($this->request->getGet('order') == 'DESC') && ($this->request->getGet('val') == 'test_date')) ? '&nbsp;<span class="glyphicon glyphicon-arrow-up"></span>' : ((( ($this->request->getGet('order') == 'ASC') && ($this->request->getGet('val') == 'test_date') )) ? '&nbsp;<span class="glyphicon glyphicon-arrow-down"></span>' : '')), array('style' => 'color:white;')); ?>
												<?php } else { ?>
												<?php  	echo lang('app.language_tbl_label_test_booking');?> 
												<?php } ?>												
											</th>
                                            <th><?php echo lang('app.language_tbl_label_test_results') ?></th>
											<?php } else if(isset($tokens['0']->order_type) && $tokens['0']->order_type == "benchmarking") { ?>
											<th><?php echo lang('app.language_tbl_label_token_type'); ?></th>
											<th><?php echo lang('app.language_tbl_label_test_results'); ?></th>
											<?php } else { ?>
											<th><?php echo lang('app.language_tbl_label_level_achieved') ?></th>
											<th><?php echo lang('app.language_tbl_label_token_type'); ?></th>
											<?php }  ?>
											<?php }  ?>
                                        </tr>
									</thead>
									<tbody>	
										<?php 
										if(!empty($tokens)) { 
										foreach ($tokens as $token) {
										?>
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
												<?php
													} elseif($token->level) { 
														echo '-';
													} else {
														echo 'Unregistered';
													}
												?>
											</td>
											<?php if(($tokens['0']->type_of_token== "cats_core") || ($tokens['0']->type_of_token == "cats_higher") || ($tokens['0']->type_of_token == "cats_core_or_higher") || ($tokens['0']->type_of_token == "catslevel")){ ?>
                                                <td><?php if ($token->level) {
                                                    echo $token->level;
                                                } else {
                                                    echo 'Not available';
                                                } ?></td>
											<td style="">
												<?php if(isset($token->course_progress) && $token->course_progress !== NULL) { ?>
												<div class="" style="">
													<div class="progress">
														<div class="progress-bar active" role="progressbar" aria-valuenow="<?php echo $token->course_progress; ?>" aria-valuemin="0" aria-valuemax="100" style="background-color: #99c8f1; width:<?php echo $token->course_progress; ?>%">
                                                                                                                    <span><?php echo round($token->course_progress); ?>%</span>
														</div>
													</div>
												</div>
												<?php } else { 
												echo lang('app.language_school_label_not_available');
												?>							
												<?php }  ?>							
											</td>											
                                            <td>
											
											
											
											
												<?php 
                                                    if(isset($token->booking_status) && $token->booking_status != NULL ){
                                                        if($token->booking_status == 1 && $token->start_date_time) {
                                                            $tz_to = $institutionTierId['timezone'];
                                                            $institution_zone_values = @get_institution_zone_from_utc($tz_to, $token->start_date_time, $token->end_date_time);																		
                                                            $dt = $institution_zone_values['institute_event_date'];
                                                            echo $dt.' '.$token->city ;
															
															?>
                                                            <?php if($token->event_status == 1 ){ ?>
                                                                <br><a style="text-decoration: underline;" href="<?php echo site_url();?>/school/learner_allocation/<?php echo $token->event_id?>">View</a>
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
                                                    } ?>
                                                    <div><?php
                                                    if(isset($token->practiceresults) && !empty($token->practiceresults['0'])) { ?>
                                                        <a  style="display:inline-block;white-space: nowrap;" href="#" data-toggle="modal" data-target=".practice-test-results" data-backdrop="static" data-keyboard="false" data-id="<?php echo $practice_label; ?>|<?php if(!empty($token->practiceresults['0']['session_number'])){ echo $token->practiceresults['0']['session_number']; } ?>|<?php if(!empty($token->practiceresults['0']['thirdparty_id'])){ echo $token->practiceresults['0']['thirdparty_id']; } ?>" class="practice-test-button"  id="loading_modal<?php if(!empty($token->practiceresults['0']['thirdparty_id'])){ echo $token->practiceresults['0']['thirdparty_id']; } ?><?php if(!empty($token->practiceresults['0']['session_number'])){ echo $token->practiceresults['0']['session_number']; } ?>"  ><?php echo $practice_label; ?></a>
                                                        <?php if(isset($token->practiceresults['0']['audio_reponses']) && ($token->practiceresults['0']['audio_reponses'] == 1)) { 
                                                            echo @disable_icon();
                                                             } ?>
                                                    <?php  } elseif(isset($token->practiceresults_tds) && !empty($token->practiceresults_tds['practice_test1']) && !empty($token->practiceresults_tds['practice_test1']['processed_data'])){?> 
                                                        <a  style="display:inline-block;white-space: nowrap;" href="#" data-toggle="modal" data-target=".practice-test-results" data-backdrop="static" data-keyboard="false" data-id="<?php echo $practice_label; ?>|<?php if(!empty($token->practiceresults_tds['practice_test1']['token'])){ echo $token->practiceresults_tds['practice_test1']['token']; } ?> " class="practice-test-button-tds"  id="loading_modal<?php if(!empty($token->practiceresults_tds['practice_test1'])){ echo $token->practiceresults_tds['practice_test1']['token']; } ?>"><?php echo $practice_label; ?></a>
                                                            <?php if(isset($token->practiceresults_tds['practice_test1']['audio_reponses']) && ($token->practiceresults_tds['practice_test1']['audio_reponses'] == 1)) { ?>
                                                                <?php if($token->practiceresults_tds['practice_test1']['audio_available']){?>
                                                                   <a href="<?php if(isset($token->practiceresults_tds['practice_test1']['url']) && !empty($token->practiceresults_tds['practice_test1']['url'])){ echo $token->practiceresults_tds['practice_test1']['url']; }?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Listen to practice test audio responses (opens in a new tab)"><i class="bi bi-volume-up-fill"></i></a>
                                                                   <?php }else{
                                                                        echo @disable_icon();
                                                                        }?>  
                                                            <?php } ?>
                                                        <?php } else { ?>
                                                            <?php if ($token->type_of_token != "cats_higher") { ?>
                                                                <span style="display:block;white-space: nowrap;"><?php echo $practice_label; ?></span>
                                                            <?php } ?>
                                                    <?php } ?> 
                                                    </div>           
                                                <?php }?>
                                                <!--Practice Test 2-->                                                     
												<?php if ($token->productid < 10 && $token->practice_count > 1) { ?>
                                                    <div> <?php	
                                                    if(isset($token->practiceresults) && !empty($token->practiceresults['1'])) { 
                                                    ?>
                                                    <a  style="display:inline-block;white-space: nowrap;" href="#" data-toggle="modal" data-target=".practice-test-results" data-backdrop="static" data-keyboard="false" 
                                                    data-id="Practice Test 2|<?php if(!empty($token->practiceresults['1']['session_number'])){ echo $token->practiceresults['1']['session_number']; } ?>|<?php if(!empty($token->practiceresults['1']['thirdparty_id'])){ echo $token->practiceresults['1']['thirdparty_id']; } ?>" 
                                                    class="practice-test-button"  
                                                    id="loading_modal<?php if(!empty($token->practiceresults['1']['thirdparty_id'])){ echo $token->practiceresults['1']['thirdparty_id']; } ?><?php if(!empty($token->practiceresults['1']['session_number'])){ echo $token->practiceresults['1']['session_number']; } ?>" >Practice Test2</a>
                                                        <?php if(isset($token->practiceresults['1']['audio_reponses']) && ($token->practiceresults['1']['audio_reponses'] == 1)) { 
                                                           // echo @disable_icon();
                                                             } ?>
                                                    <?php  }  elseif(isset($token->practiceresults_tds) && !empty($token->practiceresults_tds['practice_test2']) && !empty($token->practiceresults_tds['practice_test2']['processed_data'])){ ?>   
                                                    <a  style="display:inline-block;white-space: nowrap;" href="#" data-toggle="modal" data-target=".practice-test-results" data-backdrop="static" data-keyboard="false" data-id="Practice Test 2|<?php if(!empty($token->practiceresults_tds['practice_test2']['token'])){ echo $token->practiceresults_tds['practice_test2']['token']; } ?> " class="practice-test-button-tds"  id="loading_modal<?php if(!empty($token->practiceresults_tds['practice_test2'])){ echo $token->practiceresults_tds['practice_test2']['token']; } ?>" >Practice Test2</a>
                                                    <?php if(isset($token->practiceresults_tds['practice_test2']['audio_reponses']) && ($token->practiceresults_tds['practice_test2']['audio_reponses'] == 1)) { ?>
                                                        <?php if($token->practiceresults_tds['practice_test2']['audio_available']){?>
                                                             <a href="<?php if(isset($token->practiceresults_tds['practice_test1']['url']) && !empty($token->practiceresults_tds['practice_test1']['url'])){ echo $token->practiceresults_tds['practice_test2']['url']; }?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Listen to practice test audio responses (opens in a new tab)"><i class="bi bi-volume-up-fill"></i></a>
                                                        <?php }else{
                                                            echo @disable_icon();
                                                            }?>  
                                                    <?php } ?>
                                                    <?php }else { ?>
                                                        <?php if ($token->type_of_token != "cats_higher") { ?>
                                                            <span style="display:block;white-space: nowrap;"><?php echo 'Practice Test2'; ?></span>
                                                        <?php } ?>
                                                    <?php }  ?>
                                                </div>
                                                <?php } ?>
                                                <!--Final Test-->  
												<?php  if($token->section_one) { ?>
													<a href="<?php echo site_url('school/core_certificate').'/'.$token->candidate_id; ?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Final Test (opens in a new tab)">Final Test</a>
                                                <?php } elseif($token->tds_course_type == "Core"){ //WP-1179 - Tds core results ?>
                                                    <a href="<?php echo site_url('school/core_certificate').'/'.$token->tds_candidate_id; ?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Final Test (opens in a new tab)">Final Test</a>
												<?php } elseif($token->higher_section_one){ //WP-1156 - Higher results ?>
												    <a href="<?php echo site_url('school/higher_certificate').'/'.$token->higher_candidate_id; ?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Final Test (opens in a new tab)">Final Test</a>
                                                <?php } elseif($token->tds_course_type == "Higher"){ //WP-1276 - TDS Higher results ?>
												    <a href="<?php echo site_url('school/higher_certificate').'/'.$token->tds_candidate_id.'/'.$token->tds_token; ?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Final Test (opens in a new tab)">Final Test</a>
												<?php }else { ?>
													<span style="display:block;white-space: nowrap;"><?php echo 'Final Test'; ?></span>
												<?php } ?>											
                                                <?php if(isset($token->audio_reponses) && ($token->audio_reponses == 1)) { ?>
                                                    <?php if($token->audio_available){ ?>
                                                           <a href="<?php if(isset($token->url) && !empty($token->url)){ echo $token->url; }?>" target="_blank" style="text-decoration:none" data-toggle="tooltip" data-placement="bottom" title="Listen to final test audio responses (opens in a new tab)"><i class="bi bi-volume-up-fill"></i></a>
                                                    <?php }else{
                                                        echo @disable_icon();
                                                        }?>  
                                                <?php } ?>											
											</td>
											<?php } else if(isset($token->order_type) && $token->order_type == "benchmarking"){ ?>
												<td><?php echo $token->test_name; ?></td>
												<td>
												<?php if(isset($token->result_token) && ($token->result_token != '') && ($token->is_used != 0)) { ?>
												<a href="<?php echo site_url('school/benchmark_certificate').'/'.$token->candidate_id.'/'.$token->token; ?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="View result (opens in a new tab)">View Result</a>
                                                <?php if(isset($token->audio_reponses) && ($token->audio_reponses == 1)) { ?>
                                                    <?php if($token->audio_available){?>
                                                        <a href="<?php if(isset($token->url) && !empty($token->url)){ echo $token->url; }?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Listen to Stepcheck test audio responses (opens in a new tab)"><i class="bi bi-volume-up-fill"></i></a> 
                                                    <?php }else{
                                                        echo @disable_icon();
                                                        }?>  
                                                <?php } ?>	
                                                </td>
												<?php } else { 
													echo 'Not available';
												} ?>
											<?php } else { ?>
											<td><?php if($token->benchmark_cefr_level) { echo $token->benchmark_cefr_level ;} else { echo 'Not available';} ?></td>
											<td><?php echo $token->type_of_token; ?></td>
											<?php }  ?>											
                                        </tr>
 										<?php } } ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- ci4 pagination start -->				
                            
                            <div class="institution_pagination">
                            <nav class="text-right">
                                    <?php if ($tokenlst_data_pager) :?>
                                <?= $tokenlst_data_pager->links('pagination_token_list') ?>
                                <?php endif ?> 
                            </nav>
                            </div>	
                        </div>
                    </div>
					</div>
                </div> 
            </section>
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


    <script>
        <?php  $url_segment = $this->request->uri->getSegments(2); ?>

        $('#export_codes').click(function () {
            window.location.href = "<?php echo site_url('school/export_codes'); ?>" + '/' + '<?php echo $url_segment['3']; ?>';
        });
    </script>
