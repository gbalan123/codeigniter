
<?php 

$this->validation =  \Config\Services::validation();

?>
<div class="bg-lightgrey">
    <div class="container">
        <div class="terms_condtion nav_dashboard">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#terms_and_condtion"><?php echo lang('app.password_setup_title');?></a></li>

            </ul>
            <div class="tab-content">
                <div id="terms_and_condtion" class="tab-pane fade in active terms_tab">
                <?php if($this->session->setFlashdata('reset_failure')) {?>
					<div role="alert" class="alert alert-danger alert-dismissible">
					  <button aria-label="Close" data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span></button>
					  <?php echo $this->session->setFlashdata('reset_failure');  ?>
					</div>
				<?php } ?>
				<?php if($this->session->setFlashdata('reset_success')) {?>
					<div role="alert" class="alert alert-success alert-dismissible">
					  <button aria-label="Close" data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span></button>
					  <?php echo $this->session->setFlashdata('reset_success');  ?>
					</div>
				<?php } ?>	
                    <div class="row">
                        <div class="col-sm-12 col-s-12">
							<p><?php echo lang('app.password_setup_description');?></p>
							
							<?php echo form_open('login/password_setup', "class = 'form-horizontal signup' autocomplete='off'"); ?>
								<div class="form-group">
									<label for="newpassword" class="col-sm-4 control-label"><?php echo lang('app.password_setup_password'); ?><span>*</span></label>
									<div class="col-sm-6 col-xs-12">
										<input type="password" class="form-control input-sm" name="password" id="newpassword"  >
										<input type="hidden" name = "hiddenvalue" value = "1">
										<div class="password_condition">
											<?php echo str_replace('{field}', 'Password', lang('app.language_site_booking_screen2_password_check')); ?>
		                                </div>

										<?php if ($this->validation->getError('password')) : ?>
											<P>
													<?= $this->validation->getError('password') ?>
												<P>
											<?php endif; ?> 
								

									</div>
								</div>
								
								<input type="hidden"  name="token" id="token" value="<?php if(!empty($token)) {echo $token;} ?>" >		  
								<input type="hidden"  name="id" id="id" value="<?php if(!empty($id)) {echo $id;} ?>" >
								
								<div class="form-group">
									<label for="newpassword" class="col-sm-4 control-label"><?php echo lang('app.confirm_password'); ?><span>*</span></label>
									<div class="col-sm-6 col-xs-12">
										<input type="password" class="form-control input-sm"  name="confirmpassword" id="confirmpassword" >
										
										<?php if ($this->validation->getError('confirmpassword')) : ?>
											<P>
													<?= $this->validation->getError('confirmpassword') ?>
												<P>
											<?php endif; ?> 	
									</div>
								</div>
								<div class="form-group text-left" >
									<div class="col-sm-3 col-sm-offset-4">
										<button class="btn btn-sm btn-continue" type="submit"><?php  echo lang("submit");  ?></button>
									</div>
								</div>
							<?php echo form_close();?>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
</div>
</div>
<script>
    $(function () {
        $("#newpassword").focus(function(){
            $(".password_condition").show();
		});
	})
</script>