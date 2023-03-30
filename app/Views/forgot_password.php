<?php
	$this->validation =  \Config\Services::validation();
?>
<style>
body{
		background-color:#d3d3d3;
	}
</style>
<div class="bg-lightgrey">
    <div class="container">
        <div class="terms_condtion nav_dashboard">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#terms_and_condtion"><?php echo lang('app.forgot_title');?></a></li>

            </ul>
            <div class="tab-content">
                <div id="terms_and_condtion" class="tab-pane fade in active terms_tab">
                <?php if($this->session->getFlashdata('message')) {?>
					<div role="alert" class="alert alert-danger alert-dismissible">
					  <button aria-label="Close" data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span></button>
					  <?php echo $this->session->getFlashdata('message');  ?>
					</div>		
				<?php } ?>
				<?php if($this->session->getFlashdata('successmessage')) {?>
					<div role="alert" class="alert alert-success alert-dismissible">
					  <button aria-label="Close" data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span></button>
					  <?php echo $this->session->getFlashdata('successmessage');  ?>
					</div>	
				<?php } ?>	
                    <div class="row">
                        <div class="col-sm-12 col-s-12 ">
                            <!-- content display below -->
                            <p><?php echo lang('app.forgot_description');?></p>
							<?php echo form_open('login/forgot_password', "id='forgot_password_form' class = 'form-horizontal signup' autocomplete='off'"); ?>
								<div class="form-group">
									<label for="email" class="col-sm-3 control-label"><?php echo lang('app.email');?><span>*</span></label>
									<div class="col-sm-6 col-xs-12">
										<input type="text" name="email" id="email" class="form-control input-sm" value="<?php echo set_value('email');?>"/>
										<?php if ($this->validation->getError('email')){?>
										<p></p>
                                        <p class="alert danger" ><?= $this->validation->getError('email') ?></p>
										<p></p>
									<?php }else { echo '<p></p>'; }?>
									</div>
								</div>	
		                              <div class="form-group">
									<div class="col-sm-9 col-sm-offset-3">
										<button class="btn btn-sm btn-continue" type="submit" id="forgot_password_submit"><?php echo lang("app.submit"); ?></button>
		                                  <a href="<?php echo site_url(); ?>" class="btn btn-sm btn-continue"><?php echo lang("app.cancel"); ?></a>
									</div>
								</div>	
									
							<?php echo form_close();?>
                            <!-- content display above-->
                        </div>
                    </div>
                </div>
            </div>
        </div>        
</div>
</div>