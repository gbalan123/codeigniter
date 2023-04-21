<?php include_once 'header.php'; 

  $this->validation =  \Config\Services::validation();
?>
<style>
    .form-group p{
        color : red;
    }
</style>
<!-- /.row -->
<div class="row">

	<div class="col-lg-8">
		<div class="panel panel-default">
			
			<div class="panel-body">
				<div class="row">
					<div class="col-lg-6">
						<?php echo form_open('admin/profile',array('class'=>'form','role'=>'form','method'=>'post')); ?>


							<div class="form-group">
								<label for="name"><?php echo lang('app.language_admin_email'); ?>:</label> 
								<input type="email" class="form-control" name="email" value="<?php echo (!empty($profiledatas) && null !== $profiledatas[0]->email) ? $profiledatas[0]->email : ''; ?>" required >
							</div>
							
                                            <button type="submit"  value="miaw" name="btn-email" class="btn btn-primary">Submit</button>
							
						<?php form_close(); ?>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
<!-- /.row -->
 
<div class="row">
	<div class="col-lg-8">
                <h1 class="panel-header">Change password</h1>
		<div class="panel panel-default">
                           
			<div class="panel-body">
				<div class="row">
					<div class="col-lg-6">
						<?php echo form_open('admin/profile',array('class'=>'form','role'=>'form','id'=>'password_form', 'method'=>'post')); ?>
                            <div class="form-group">
								<label for="name"><?php echo lang('app.current_password'); ?>:</label>                              
								<input type="password" class="form-control" name="current_password" value="<?php echo set_value('current_password'); ?>"  >
								
								   <?php if ($this->validation->getError('current_password')) : ?>
									<P>
                                        <?= $this->validation->getError('current_password') ?>
                                    <P>
									<?php endif; ?>
										
							        </div>
                                    <div class="form-group">
							    	<label for="name"><?php echo lang('app.new_password'); ?>:</label> 
                                     <input type="password" class="form-control" name="new_password" value="<?php echo set_value('new_password'); ?>"  >
                                                              
									<?php if ($this->validation->getError('new_password')) : ?>
									<P>
                                        <?= $this->validation->getError('new_password') ?>
                                    <P>
									<?php endif; ?>

															
							</div>
                                                        <div class="form-group">
								<label for="name"><?php echo lang('app.confirm_new_password'); ?>:</label> 
                                                                <input type="password" class="form-control" name="confirm_new_password" value="<?php echo set_value('confirm_new_password'); ?>"  >
									<?php if ($this->validation->getError('confirm_new_password')) : ?>
									<P>
                                        <?= $this->validation->getError('confirm_new_password') ?>
                                    <P>
									<?php endif; ?>                           
																
							</div>
							
                                            <button type="submit" value="miaw"  name="btn-password" class="btn btn-primary">Submit</button>
							
						<?php form_close(); ?>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
<!-- /.row -->

<?php include 'footer.php';  ?>
