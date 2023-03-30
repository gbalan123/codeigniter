<?php
$this->lang = new \Config\MY_Lang();
?>
<style>
    #tieruser_form p {
        color : red;
    }
    .form-actions {
        margin: 0;
        background-color: transparent;
        text-align: center;
    }
    .has-feedback label~.form-control-feedback {
        top: 0px;
    }
    .has-feedback .form-control {

    }
    .dropdown-menu
    {
        width: 100%; 
    }
</style>
<!-- /.row -->
<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
                <i class="fa fa-plus fa-fw"></i> <?= esc($admin_heading) ?>
            </div>
			<div class="panel-body">
				<div class="row">
                    <div class="col-xs-12 clearfix">
						<?php if (isset($tieruser) && !empty($tieruser)): ?>
                            <?php echo form_open_multipart('admin/post_tieruser/' . $tieruser->id, array('class' => 'form bv-form', 'role' => 'form', 'id' => 'tieruser_form', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>
                            <input type="hidden" name="tieruser_id"  id="tieruser_id" value="<?php echo $tieruser->id; ?>"/>
							<input type="hidden" name="tier_id" value="<?php echo $tier_id; ?>"/>
							<input type="hidden" name="tier_type" value="<?php echo $tier_type; ?>"/>
                        <?php else: ?>
                            <?php echo form_open_multipart('admin/post_tieruser', array('class' => 'form bv-form', 'role' => 'form', 'id' => 'tieruser_form', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>
                            <input type="hidden" name="tieruser_id" id="tieruser_id" value=""/>
							<input type="hidden" name="tier_id" value="<?php echo $tier_id; ?>"/>
							<input type="hidden" name="tier_type" value="<?php echo $tier_type; ?>"/>
                        <?php endif; ?>
						
						<fieldset>

						

							<div class="form-group col-xs-12 clearfix" >
								<label for="first_name"><?php echo lang('app.language_admin_tieruser_firstname'); ?><span> *</span></label> 
								<input type="text"
									   class="form-control" name="first_name"  id="first_name" placeholder="<?php echo lang('app.language_admin_tieruser_firstname'); ?>"
									   value="<?php echo(($tieruser == False)) ? '' : $tieruser->firstname ;?>"
									> 
							</div>
							<div class="form-group col-xs-12 clearfix" >
								<label for="second_name"><?php echo lang('app.language_admin_tieruser_second_name'); ?><span> *</span></label> 
								<input type="text"
									   class="form-control" name="second_name"  id="second_name" placeholder="<?php echo lang('app.language_admin_tieruser_second_name'); ?>"
									   value="<?php echo(($tieruser == False)) ? '' : $tieruser->lastname ;?>"
									  >
							</div>
							<div class="form-group col-xs-12 clearfix" >
								<label for="department"><?php echo lang('app.language_admin_tieruser_department'); ?><span> *</span></label> 
								<input type="text"
									   class="form-control" name="department"  id="department" placeholder="<?php echo lang('app.language_admin_tieruser_department'); ?>"
									   value="<?php echo(($tieruser == False)) ? '' : $tieruser->department ;?>"
									 >  
							</div>
							<div class="form-group col-xs-12 clearfix" >
								<label for="email"><?php echo lang('app.language_admin_tieruser_email'); ?><span> *</span></label> 
								<input type="text"
									   class="form-control" name="email"  id="email" placeholder="<?php echo lang('app.language_admin_tieruser_email'); ?>"
									   value="<?php echo(($tieruser == False)) ? '' : $tieruser->email ;?>"
									  >
							</div>
							<div class="form-group col-xs-12 clearfix" >
								<label for="confirm_email"><?php echo lang('app.language_admin_tieruser_confirm_email'); ?><span> *</span></label> 
								<input type="text"
									   class="form-control" name="confirm_email"  id="confirm_email" placeholder="<?php echo lang('app.language_admin_tieruser_confirm_email'); ?>"
									   value="<?php echo(($tieruser == False)) ? '' : $tieruser->email ;?>"
								>
							</div>
							<?php if($tier_type != 3) { ?>
								<div class="form-group col-xs-12 clearfix" >
								<label for="access_institute"><?php echo lang('app.language_admin_tieruser_access_institute'); ?>: <span> *</span></label> 
								<select name="access_institute[]" id="access_institute" class="form-control"  multiple>                                    
                                    <?php if(isset($institutionGroupId)){?>
									<?php foreach ($institution_groups as $type): 
										?>
										<option value="<?php echo $type->institutionGroupId; ?>" <?php echo (@in_array($type->institutionGroupId,$institutionGroupId)) ? 'selected' : '' ?> ><?php echo $type->englishTitle; ?></option>
									<?php endforeach; ?>
									<?php }else{ ?>
									<?php foreach ($institution_groups as $type): ?>
										<option value="<?php echo $type->institutionGroupId; ?>"><?php echo $type->englishTitle; ?></option>
									<?php endforeach; ?>
									<?php } ?>
                                </select>
							</div>
							<?php } ?>
							<div class="form-group form-actions pull-right" style="clear:both;">
								<button type="submit" id="submitBtn" class="btn btn-lg btn-primary wpsc_button" style="pointer-events:none;"><?php echo lang('app.language_admin_institutions_submit_btn'); ?></button><img id="loading_in" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" />
							</div>
						</fieldset>
						
						<?php form_close(); ?>
					</div>
				</div>	
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(function () {
		$('#access_institute').multiselect({
			inheritClass: true,
			buttonWidth: '100%',
			buttonText: function (options, select) {
				if (options.length === 0) {
					return "<?php echo lang('app.lsetting_please_select'); ?>";
				} else if (options.length >= 1) {
					return options.length + " <?php
					if ($this->lang->lang() == 'ms') {
						echo 'pilihan';
					} elseif ($this->lang->lang() == 'sr') {
						echo 'odabran';
					} elseif ($this->lang->lang() == 'pt') {
						echo 'selecionado';
					} else {
						echo 'selected';
					}
					?>";
				}
			}
		});
		
		$('#tieruser_form').submit(function (e) {

			$('#submitBtn').attr('disabled', true);
			$('#loading_in').show();
			$( ".help-block" ).empty();
			$.ajax({
				type: "POST",
				url: $(this).attr('action'),
				data: $(this).serialize(),
				dataType: 'json',
				success: function (data)
				{
					clear_errors(data);
					$('#submitBtn').attr('disabled', false);
					$('#loading_in').hide();
					if (data.success) {
						location.reload();
					} else {
						set_errors(data);
					}

				}
			});
			return false;
		});

		function clear_errors(data)
		{
			if (typeof (data.errors) != "undefined" && data.errors !== null) {
				for (var k in data.errors) {
					if (k == 'sectors') {
						$('.multiselect').next('p').remove();
					} else {
						$('#' + k).next('p').remove();
					}
				}
			}
		}

		function set_errors(data)
		{
			if (typeof (data.errors) != "undefined" && data.errors !== null) {
				for (var k in data.errors) {
					if (k == 'sectors') {
						$(data.errors[k]).insertAfter($('.multiselect'));
					} else {
						$(data.errors[k]).insertAfter($("#" + k));
					}
				}
			}
		}
	});	
</script>