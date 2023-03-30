<?php include_once 'header.php'; ?>
<?php use Carbon\Carbon; 

$this->session = \Config\Services::session();
use App\Models\Admin\Cmsmodel;
$this->cmsmodel = new Cmsmodel();
?>
<?php if (isset($results) && (empty($results))) { ?>
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <?php echo "No test events match the entered search criteria."; ?>
    </div>
<?php } ?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-body">


                <div class="row">
                    <?php echo form_open('admin/view_learner_progress', array('role' => 'form bv-form', 'id' => 'search_form', 'class' => 'form-horizontal', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh", 'method'=>'get')); ?>
                    <div class="col-sm-3 col-md-4">

                        <div class="form-group">
                            <div class="col-sm-12">
                                <label for="month"><?php echo lang('app.language_school_institute_label_name'); ?><span style="color: red;">*</span></label>
                                <select class="form-control" name="institute"><?php echo  $this->session->get('institute') ; ?>
                                    <option>Please select</option>
                                    <?php foreach($school_lists as $school): ?>
                                    <option <?php echo (isset($institute_id) && $institute_id == $school->id) ? 'selected' : ''; ?> value="<?php echo $school->id; ?>"><?php echo $school->organization_name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                
                                <div>
                              
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3 col-md-2">
                        <label>&nbsp;</label>
                        <input class="btn btn-grey btn-block" type="submit" name="" value="<?php echo lang('app.language_admin_event_search'); ?>">
                    </div>
                    <div class="clearfix"></div>
                </div>
                <?php echo form_close(); ?>
				
				<?php if(!empty($tokens)) { ?>
				<div class="table-responsive view-tokens">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th width="12%"><?php  	echo lang('app.language_tbl_label_token_id');?> </th>
								<th><?php echo 'User details'; ?></th>
								<th width="14%"><?php  	echo lang('app.language_tbl_label_level');?></th>
								<th><?php echo 'Course Progress'; ?></th>
								<th><?php echo 'Practice test 1'; ?></th>
								<th><?php echo 'Practice test 2'; ?></th>
								<th><?php echo lang('app.language_tbl_label_test_booking');?></th>
								<th><?php echo 'Score out of 50';?></th>
								<th><?php echo 'CEFR'; ?></th>
								<th><?php echo 'Score on CATs Step scale' ?></th>
							</tr>
							<?php 

							foreach ($tokens as $token) {
						
							?>
							<tr>
								<td class="text-token"><?php echo $token->token; ?></td>
								<td>
									<?php if($token->user_name) { ?>
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
								<td><?php if($token->level) { echo $token->level ;} else { echo 'Not available';} ?></td>
								<td style="">
									<?php if(isset($token->progress)) { ?>
									<div class="" style="">
										<div class="progress" style="background-color: #e7f5f9">
											<div class="progress-bar  active" role="progressbar"  aria-valuenow="<?php echo $token->progress; ?>" aria-valuemin="0" aria-valuemax="100" style="background-color: #99c8f1; width:<?php echo $token->progress; ?>%">
											  <span style="color: #000000;"><?php echo $token->progress; ?>%</span>
											</div>
										</div>
									</div>
									<?php } else { 
									echo 'Not available';
									?>							
									<?php }  ?>							
								</td>
								<td>
									<?php 
                                         if(isset($token->practicetest1_tds)){
                                          echo $token->practicetest1_tds;
                                                                                }
										elseif(isset($token->practicetest1)) { 
											echo $token->practicetest1; 
										} else { 
											echo 'Not taken';
										}  
									?>							
								</td>
								<td>
									<?php 
                                                                                if(isset($token->practicetest2_tds)){
                                                                                    echo $token->practicetest2_tds;
                                                                                }
										elseif(isset($token->practicetest2)) { 
											echo $token->practicetest2; 
										} else { 
											echo 'Not taken';
										}  
									?>							
								</td>												
								<td>
									<?php 
										if($token->start_date_time) { 
											$institution_zone_values = @get_downtime_zone_from_utc($token->timezone, $token->start_date_time,False);	
                                            $dt = $institution_zone_values['downtime_start_date'];
											echo $dt.' '.$token->city ;
										} else {
											echo 'Not available';
										}
									?>											
								</td>
								<td>
									<?php 
										if(isset($token->score_outof50)) { 
											echo $token->score_outof50; 
										} else { 
											echo 'Not available';
										}  
									?>							
								</td>
								<td>
									<?php 
										if(isset($token->cefr_level)) { 
											echo $token->cefr_level; 
										} else { 
											echo 'Not available';
										}  
									?>							
								</td>
								<td>
									<?php 
										if(isset($token->cats_scale)) { 
											echo $token->cats_scale; 
										} else { 
											echo 'Not available';
										}  
									?>							
								</td>												
								<?php } ?>										
							</tr>
							<?php }  ?>
						</thead>
					</table>
				</div>
				<nav class="text-right">
				<?php if (($token_links)) :?>
									<?= $token_links->links('pagination_view_learner_progress') ?>
									<?php endif ?> 
				</nav>
				<?php 
				} else { 
					if($aftersubmit) {
			    ?>
						<div class="alert alert-danger alert-dismissible" role="alert">
							<?php echo "No records found"; ?>
						</div>				
				<?php	
				 }					 
				
				 }  ?>

            </div>
          
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-6-->
</div>




<?php include 'footer.php'; ?>

