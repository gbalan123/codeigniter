<?php 
$this->encrypter = \Config\Services::encrypter();
?>

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
				<i class="fa fa-plus fa-fw"></i><?php echo $teacher_heading; ?> 
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-xs-12 clearfix">
						<form action="<?php echo site_url('school/post_u13learner'); ?>" class="form bv-form" role="form" id="u13_learner_form"
							data-bv-feedbackicons-valid="glyphicon glyphicon-ok" data-bv-feedbackicons-invalid="glyphicon glyphicon-remove"
							data-bv-feedbackicons-validating="glyphicon glyphicon-refresh" enctype="multipart/form-data"
							method="post" accept-charset="utf-8">
							<?php if (isset($u13_learner) && !empty($u13_learner)) { ?>
								<input type="hidden" name="user_id"  id="user_id" value="<?php  echo base64_encode($this->encrypter->encrypt($u13_learner['user_id'])); ?>"/> 
								<?php } ?>							
							<fieldset>											
								<legend><?php echo lang('app.language_school_u13learner_user_details'); ?></legend>
								<div class="row">
									<div class="form-group col-xs-12 clearfix">
										<label for="first_name"><?php echo lang('app.language_admin_institutions_first_name'); ?><span>*</span></label>
										<input
											type="text" class="form-control" name="firstname" id="firstname_u13"
											placeholder="First name" value="<?php echo set_value('firstname', isset($u13_learner) ? $u13_learner['firstname'] : ''); ?>">
									</div>
								</div>
								<div class="row">
									<div class="form-group col-xs-12 clearfix">
										<label for="last_name"><?php echo lang('app.language_admin_institutions_second_name'); ?><span>*</span></label>
										<input
											type="text" class="form-control" name="lastname" id="lastname_u13"
											placeholder="Second name" value="<?php echo set_value('lastname', isset($u13_learner) ? $u13_learner['lastname'] : ''); ?>">
									</div>
								</div>											
								<div class="row">
									<div class="form-group col-xs-12 clearfix">
										<label style="width:100%" for="department"><?php echo lang('app.language_school_u13learner_dob'); ?><span>*</span></label>
										<div class="col-sm-3 nopadleft">
											<input type="number" name="mydob[]"  class="form-control mydob mydobdate"  placeholder="DD"  min="1" max="31" maxlength="2" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" value="<?php echo (isset($u13_learner['dob']) && $u13_learner['dob'] != '0') ? date('d', $u13_learner['dob']) : '' ?>" >
										</div>
										<div class="col-sm-3">
											<input type="number"  name="mydob[]"  class="form-control mydob mydobmonth"  min="1" max="12" placeholder="MM"  maxlength="2" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" value="<?php echo (isset($u13_learner['dob']) && $u13_learner['dob'] != '0') ? date('m', $u13_learner['dob']) : '' ?>">
										</div>
										<div class="col-sm-3 width46">
											<?php
											$under13year = date('Y') - 16;	
											$yearData = range($under13year, date('Y'));
											rsort($yearData); ?>
											<select  name="mydob[]" class="form-control mydob mydobyear">
												<option value="">YYYY</option>
												<?php
												foreach ($yearData as $year):
													if (isset($u13_learner['dob']) && $u13_learner['dob'] != '0' && date('Y', $u13_learner['dob']) == $year) {
														$selected = $year;
													}
													?>
													<option <?php echo (isset($selected) && $selected == $year) ? 'selected' : ''; ?> value="<?php echo $year; ?>"><?php echo $year; ?></option>
	<?php endforeach; ?>
											</select>
										</div>
										
										<div class="clearfix" id="mydob"></div>
									</div>
								</div>	
                                <div class="row">
                                    <div class="form-group col-xs-12 clearfix">
                                        <label for="last_name"><?php echo lang('app.language_school_u13learner_gender'); ?><span>*</span></label>                       <select name="mygender" id="mygender"  class="form-control mygender">
                                            <option value="" <?php if (isset($u13_learner['gender']) && $u13_learner['gender'] == "") {echo 'selected="selected"';}?>><?php echo lang('app.lsetting_please_select'); ?></option>
                                            <option  value="F" <?php if (isset($u13_learner['gender']) && $u13_learner['gender'] == "F") {echo 'selected="selected"';}?>><?php echo lang('app.lsetting_label_gender_female'); ?></option>     
                                            <option   value="M" <?php if (isset($u13_learner['gender']) && $u13_learner['gender'] == "M") {echo 'selected="selected"';}?>><?php echo lang('app.lsetting_label_gender_male'); ?></option>
                                            <option  value="U" <?php if (isset($u13_learner['gender']) && $u13_learner['gender'] == "U") {echo 'selected="selected"';}?>><?php echo lang('app.lsetting_label_gender_not_known'); ?></option>
                                            <option  value="U"><?php echo lang('app.lsetting_label_gender_not_applicable'); ?></option> 
                                        </select>                                                   
                                    </div>
                                </div>								
								<div class="row">
									<div class="form-group col-xs-12 clearfix">
										<label for="last_name"><?php echo lang('app.language_school_u13learner_catsproduct'); ?></label>
										<?php echo (($u13_learner['cats_product'] == 'cats_core') ? 'CATs Step Core' : 'CATs Step Primary'); ?>
									</div>
								</div>
							</fieldset>
							<div class="form-group form-actions pull-right" style="clear:both;">
								<button type="" id="" data-dismiss="modal" class="btn btn-sm btn-continue"><?php echo lang('app.language_school_u13learner_cancel'); ?></button>
								<button type="submit" id="submitBtn_u13" class="btn btn-sm btn-continue"><?php echo lang('app.language_admin_institutions_submit_btn'); ?></button>
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

<script>

		// date picker for U13 learner -dob
	    // current date + 7 days	
		var thirteenyears = moment().subtract(13, 'years').format("MM/DD/YYYY"); 
	    // current date 
		var max = moment().add(0, 'd').format("MM/DD/YYYY");
		
		$('#datetimepicker12').datetimepicker({
			format: 'L',
		});			
		$("#datetimepicker12").on("dp.change dp.show", function (e) {	
            $('#datetimepicker12').data("DateTimePicker").maxDate(max);
            $('#datetimepicker12').data("DateTimePicker").minDate(thirteenyears);
        });

	$('.mydob').on('change', function() {
		var mydobdate = $('.mydobdate').val();
		var mydobmonth = $('.mydobmonth').val();
		var mydobyear = $('.mydobyear').val();
		if(mydobdate != '' && mydobmonth != '' && mydobyear != ''){
			
			$.ajax({
				type: "POST",
				url: '<?php echo site_url('school/post_checkdob'); ?>',
				data: $('#u13_learner_form').serialize(),
				dataType: 'json',
				success: function (data)
				{
					//console.log(data);
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
		$('#loading_in').show();
	    $("#mydob").next('span, p').remove();
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
					if(data.doberror){
					console.log(data.errors['mydob']);
					$("#mydob").after("<p style='color:red'>"+data.errors['mydob']+"</p>");
					}else{
					 set_errors(data);
					}
				}

			}
		});
		return false;
	});
	
	$(".mydobdate").keyup(function(e){
		var datevalue = $(this).val();
		if(datevalue > 31){
			$(this).val('');
			e.preventDefault();
		}
	});
	
	$(".mydobmonth").keyup(function(e){
		var datevalue = $(this).val();
		if(datevalue > 12){
			$(this).val('');
			e.preventDefault();
		}
	});

	function clear_errors(data)
	{
		if (typeof (data.errors) != "undefined" && data.errors !== null) {
			for (var k in data.errors) {

				$('#' + k).next('span').remove();

			}
		}
	}

	function set_errors(data)
	{
		// clear_errors(data);
		console.log(data);
		if (typeof (data.errors) != "undefined" && data.errors !== null) {
			for (var k in data.errors) {

				$(data.errors[k]).insertAfter($("#" + k)).css('color', 'red');

			}
		}
	}


</script>