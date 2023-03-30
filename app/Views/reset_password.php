
<?php
use Config\MY_Lang; 

$this->lang = new \Config\MY_Lang();
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
                <li class="active"><a data-toggle="tab" href="#terms_and_condtion"><?php echo lang('app.reset_title');?></a></li>
            </ul>
            <div class="tab-content">
                <div id="terms_and_condtion" class="tab-pane fade in active terms_tab">
                <?php if($this->session->getFlashdata('reset_failure')) {?>
					<div role="alert" class="alert alert-danger alert-dismissible">
					  <button aria-label="Close" data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span></button>
					  <?php echo $this->session->getFlashdata('reset_failure');  ?>
					</div>
				<?php } ?>
				<?php if($this->session->getFlashdata('reset_success')) {?>
					<div role="alert" class="alert alert-success alert-dismissible">
					  <button aria-label="Close" data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span></button>
					  <?php echo $this->session->getFlashdata('reset_success');  ?>
					</div>
				<?php } ?>	
                    <div class="row">
                        <div class="col-sm-12 col-s-12">
						<?php echo form_open('login/reset_password', "id='reset_password_form' class = 'form-horizontal signup' autocomplete='off'"); ?>
						<div class="form-group">
							<label for="newpassword" class="col-sm-4 control-label"><?php echo lang('app.new_password'); ?><span>*</span></label>
							<div class="col-sm-6 col-xs-12">
								<input type="password" class="form-control input-sm" name="newpassword" id="newpassword"  data-toggle="tooltip" title="<?php echo str_replace('{field}','Password',lang('app.language_site_booking_screen2_password_check')); ?>">
								<?php if ($this->validation->getError('newpassword')){?>
										<p></p>
                                        <p class="alert danger" ><?= $this->validation->getError('newpassword') ?></p>
										<p></p>
									<?php }else { echo '<p></p>'; }?>
							</div>
						</div>
						
						<input type="hidden"  name="token" id="token" value="<?php if(!empty($token)) {echo $token;} ?>" >		  
						<input type="hidden"  name="id" id="id" value="<?php if(!empty($id)) {echo $id;} ?>" >
						
						<div class="form-group">
							<label for="confirmpassword" class="col-sm-4 control-label"><?php echo lang('app.confirm_password'); ?><span>*</span></label>
							<div class="col-sm-6 col-xs-12">
								<input type="password" class="form-control input-sm"  name="confirmpassword" id="confirmpassword" >
								<?php if ($this->validation->getError('confirmpassword')){?>
										<p></p>
                                        <p class="alert danger" ><?= $this->validation->getError('confirmpassword') ?></p>
										<p></p>
									<?php }else { echo '<p></p>'; }?>
							</div>
						</div>
						<div class="form-group text-left" >
							<div class="col-sm-3 col-sm-offset-4">
							<?php $button = $this->session->getFlashdata('reset_failure') ? "disabled" : "";?>
								<button class="btn btn-sm btn-continue" <?php echo $button;?> type="submit" id="reset_password_submit"><?php echo lang("app.submit"); ?></button>
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
	$("#reset_password_form input").keydown(function (event) {
		var inputfocus = $('#newpassword,#confirmpassword').is(':focus');
		if (inputfocus) {
			if (event.which == 13) {
				event.preventDefault();
				$("#reset_password_form").submit();
			}
		}
	});
</script>