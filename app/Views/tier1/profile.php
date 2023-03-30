<?php $this->validation =  \Config\Services::validation(); ?>
<style>
    .bg-green > .form-group > div > p{
        color: red;
    }
    .bg-orange > .form-group > div > p{
        color: #FFFF;
    }
</style>
<div class="bg-lightgrey">
    <div class="container">
		<div class="get_started">
		<div class="col-sm-12" style="margin-bottom:40px;">
			<div class="main_tab">
                <p class="p20"><a href="<?php echo site_url('tier1/dashboard'); ?>"><span class="fa fa-long-arrow-left"></span> <?php echo lang('app.language_search_events_back_to_dash'); ?></a></p>
			</div>
			<div class="main_tab_content">
				<div class="tab-content">
					<div id="sign_up_login_tab" class="tab-pane sign_login fade in active">
						<div class="row">
						<div class="col-sm-12">
								<!--<h1><?php echo lang('app.site_label_drop_profile') ?></h1>-->
							<?php include 'messages.php'; ?>
							<div class="panel with-nav-tabs panel-default">
								<div class="panel-heading">
									<ul class="nav nav-tabs">
										<li class="<?php echo ($this->session->get('tabprofile')) ? 'active' : ''; ?>"><a href="#profile_tab" data-toggle="tab"><?php echo lang('app.site_label_drop_profile') ?></a></li>
										<li class="<?php echo ($this->session->get('tabpass')) ? 'active' : ''; ?>"><a href="#password_tab" data-toggle="tab"><?php echo lang('app.change_title'); ?> </a></li>
										<div class="clearfix"></div>
									</ul>
								</div>
								<div class="panel-body">
									<div class="tab-content">
										<div id="profile_tab" class="tab-pane login fade <?php echo ($this->session->get('tabprofile')) ? 'in active' : ''; ?>">
										<div class="row">
											<div class="col-sm-12 profile-form">
												<?php echo form_open('tier1/profile', array('role' => 'form bv-form', 'class' => 'form-horizontal', 'autocomplete' => 'off', 'method' => 'POST', 'id' => '', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>
												<div class="form-group">
													<label class="col-sm-4 control-label"><?php echo lang('app.language_site_booking_screen2_label_first_name'); ?> <span class="required">*</span></label>
													<div class="col-sm-6 col-xs-12">
														<input type="text" class="form-control" name="firstname" value="<?php echo isset($profile) ? $profile[0]->firstname : ''; ?>" placeholder=""  />
															   
															   <?php if($this->validation->getError('firstname')){
														?> <p><?php echo $this->validation->getError('firstname') ?></p><?php
													}
												?>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-4 control-label"><?php echo lang('app.language_site_booking_screen2_label_second_name'); ?> <span class="required">*</span></label>
													<div class="col-sm-6 col-xs-12">
														<input type="text" class="form-control" name="secondname" value="<?php echo isset($profile) ? $profile[0]->lastname : '' ?>" placeholder=""  />

														<?php if($this->validation->getError('secondname')){
														?> <p><?php echo $this->validation->getError('secondname') ?></p><?php
													}
												?>  
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-4 control-label"><?php echo lang('app.language_site_booking_screen2_label_email_address'); ?> <span class="required">*</span></label>
													<div class="col-sm-6 col-xs-12">
														<input type="email" class="form-control" name="email" value="<?php echo $this->session->get('username'); ?>"
															   placeholder=""  />
															   <?php if($this->validation->getError('email')){
														?> <p><?php echo $this->validation->getError('email') ?></p><?php
													}
												?>
													</div>
												</div>
												<div class="form-group text-center">
													<div class="col-sm-offset-4 col-sm-3">
														<button type="submit" name="profile_submit" value="test"  class="btn btn-sm btn-continue"><?php echo lang('app.language_admin_submit'); ?></button>
														&nbsp;&nbsp;<img alt="loading" class="loading" style="display:none;" src="<?php echo base_url('public/images/loading.gif'); ?>">
													</div>
												</div>

												<?php echo form_close(); ?>
											</div>
										</div>	
										</div>
										<div id="password_tab" class="tab-pane login fade <?php echo ($this->session->get('tabpass')) ? 'in active' : ''; ?>">
											<div class="row">
												<div class="col-sm-12 changepass-form">
													<?php echo form_open('tier1/profile', array('role' => 'form bv-form', 'class' => 'form-horizontal', 'autocomplete' => 'off', 'method' => 'POST', 'id' => '', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>

													<div class="form-group">
														<label class="col-sm-4 control-label"><?php echo lang('app.current_password'); ?><span class="required">*</span></label>
														<div class="col-sm-6 col-xs-12">
															<input type="password" name="current_password" class="form-control" placeholder=""    />
															<?php if($this->validation->getError('current_password')){
														?> <p><?php echo $this->validation->getError('current_password') ?></p><?php
													}
												?></div>
													</div>

													<div class="form-group">
														<label class="col-sm-4 control-label"><?php echo lang('app.new_password'); ?> <span class="required">*</span></label>
														<div class="col-sm-6 col-xs-12">
															<input type="password" name="new_password" class="form-control" placeholder=""   data-toggle="tooltip" title="<?php echo str_replace('{field}', 'Password', lang('app.language_site_booking_screen2_password_check')); ?>" />
															<?php if($this->validation->getError('new_password')){
														?> <p><?php echo $this->validation->getError('new_password') ?></p><?php
													}
												?></div>
													</div>
													<div class="form-group">
														<label class="col-sm-4 control-label"><?php echo lang('app.confirm_new_password'); ?> <span class="required">*</span></label>
														<div class="col-sm-6 col-xs-12">
															<input type="password" class="form-control" name="confirm_new_password" placeholder=""  />
															<?php if($this->validation->getError('confirm_new_password')){
														?> <p><?php echo $this->validation->getError('confirm_new_password') ?></p><?php
													}
												?>
														</div>
													</div>
													<div class="form-group text-center">
														<div class="col-sm-offset-4 col-sm-3">
															<button type="submit" name="changepass_submit" value="test"  class="btn btn-sm btn-continue"><?php echo lang('app.language_admin_submit'); ?></button>
															&nbsp;&nbsp;<img alt="loading" class="loading" style="display:none;" src="<?php echo base_url('public/images/loading.gif'); ?>">
														</div>
													</div>


													<?php echo form_close(); ?>
												</div>
											</div>
										</div>		
									</div>
								</div>	
							</div>	
						</div>
						</div>
					</div>
				</div>
				<div style="margin-top:40px"></div>				
			</div>
			</div>
		</div>
    </div>
</div>