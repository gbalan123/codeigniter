<style>
#testdetail_form p {
	color: red;
}
</style>
<?php 
$types = array('Adaptive','Linear');
$parts = array('Reading','Writing','Listening','Speaking');
?>
<div class="row">
	<div class="col-md-12">
		<div class="alert alert-danger" id="warning_message" style="display:none;">
			<p><?php echo lang('app.language_admin_test_form_id_already_associated_practice'); ?></p>
			<a href="#" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#update_practice_inactive" id="practice_update_add"  data-value=""><em class="fa fa-plus fa-fw"></em>Add Test</a>
		</div>
		<div class="alert alert-danger" id="warning_message_final" style="display:none;">
			<p><?php echo lang('app.language_admin_test_form_id_already_associated'); ?></p>
		</div>
		<div class="alert alert-danger" id="warning_message_placement" style="display:none;">
			<p><?php echo lang('app.language_admin_test_form_id_active_placement'); ?></p>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<em class="fa fa-plus fa-fw"></em><?= esc($admin_heading) ?>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="row">

					<div class="col-xs-12 clearfix">
					<?php if (isset($test_data) && !empty($test_data)): ?>
					<?php echo form_open_multipart('admin/testform_details_update/'. $test_data['id'], array('class' => 'form bv-form', 'role' => 'form', 'id' => 'testdetail_form', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>
					<?php else: ?>
                    <?php echo form_open_multipart('admin/testform_details_update', array('class' => 'form bv-form', 'role' => 'form', 'id' => 'testdetail_form', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>
					<?php endif; ?>
                    	<fieldset>  
							<legend><?php echo lang('app.language_admin_testform_details'); ?></legend>
							<span id="show_error"></span>
							<div class="row">	
								<div class="form-group col-xs-6 clearfix">
									<label for="test_purpose"><?php echo lang('app.language_admin_testform_purpose'); ?><span>*</span></label>
									<select class="form-control" <?php if(isset($test_data)){ echo 'disabled'; }?> name="test_purpose" id="test_purpose" onchange="getFormId()">
										<option value="">Please select</option>
									<?php foreach($test_purposes as $test_purpose):?>
										<option <?php echo set_select('test_purpose', isset($test_data) ? $test_data['tds_group_id'] : '', ( isset($test_data) && $test_data['tds_group_id'] == $test_purpose->id ? TRUE : FALSE)); ?> value="<?php echo $test_purpose->id; ?>"><?php echo $test_purpose->test_type; ?></option>
									<?php endforeach; ?>
									</select>						
								</div>
								<div class="form-group col-xs-6 clearfix">
                                    <label for="form_version"><?php echo lang('app.language_admin_test_product'); ?><span id="test_product_header_span">*</span></label>
									<select class="form-control" name="test_product" id="test_product" disabled onchange="getFormId()">
										<option value="">Please select</option>
									<?php foreach($products as $product):?>
										<?php if($product->id == 10) { ?>
										<option <?php echo set_select('test_product', isset($test_data) ? $test_data['test_product_id'] : '', ( isset($test_data) && $test_data['test_product_id'] == $product->id ? TRUE : FALSE)); ?> value="<?php echo $product->id; ?>"><?php echo 'Step Higher'; ?></option>	
										<?php }elseif($product->id <= 9 || $product->id > 12) { ?>
										<option <?php echo set_select('test_product', isset($test_data) ? $test_data['test_product_id'] : '', ( isset($test_data) && $test_data['test_product_id'] == $product->id ? TRUE : FALSE)); ?> value="<?php echo $product->id; ?>"><?php echo $product->name; ?></option>
										<?php } ?>
									<?php endforeach; ?>
									</select>
								</div>
							</div>
							<div class="row">	
								<div class="form-group col-xs-6 clearfix">
									<label for="form_id"><?php echo lang('app.language_admin_testform_id'); ?><span>*</span> </label>
									<select class="form-control" name="form_id" id="form_id" disabled onchange="changeFormId()">
										<option <?php echo set_select('form_id', isset($test_data) ? $test_data['test_formid'] : ''); ?> value="<?php echo isset($test_data) ? $test_data['test_formid'] : ''; ?>"><?php echo isset($test_data) ? $test_data['test_formid'] : 'Please select'; ?></option>
									</select>
									<input type="hidden" name="id" value="<?php echo set_value('id', isset($test_data) ? $test_data['id'] : ''); ?>" id="hidden_test_detail_id"/>										
								</div>
								<div class="form-group col-xs-6 clearfix">
									<label for="form_version"><?php echo lang('app.language_admin_testform_version'); ?><span>*</span></label>
									<input type="hidden" class="form-control" name="hidden_form_version" id="hidden_form_version" value="<?php echo set_value('form_version', isset($test_data) ? $test_data['test_formversion'] : ''); ?>" /> 
									<select class="form-control" name="form_version" id="form_version" disabled>
										<option>Please select</option>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-xs-6 clearfix">
									<label for="test_name" id="test_name_header"><?php echo lang('app.language_admin_testform_name'); ?> </label>
									<input type="text" <?php if(isset($test_data)){ echo 'disabled'; }?> class="form-control" name="test_name" id="test_name" maxlength="120" value="<?php echo set_value('test_name', isset($test_data) ? $test_data['test_name'] : ''); ?>"  />	
                                                                        <span style="float:right; margin-top: 5px; font-weight: bold"><span id ="chars">120</span> characters left</span>
								</div>
								<div class="form-group col-xs-6 clearfix">
									<label for="form_id"><?php echo lang('app.language_admin_testform_type'); ?><span>*</span></label>
									<select <?php if(isset($test_data)){ echo 'disabled'; }?> class="form-control" name="test_type" id="test_type">
										<option value="">Please select</option>
									<?php foreach($types as $type):?>
										<option <?php echo set_select('test_type', isset($test_data) ? $test_data['test_type'] : '', ( isset($test_data) && $test_data['test_type'] == $type ? TRUE : FALSE)); ?> value="<?php echo $type; ?>"><?php echo $type; ?></option>
									<?php endforeach; ?>
									</select>											
								</div>
							</div>
							
							<div class="row">
                                <div class="form-group col-xs-6 clearfix">
                                    <label for="test_parts"> <?php echo lang('app.language_admin_test_parts'); ?><span>*</span></label>
									<div class="col-xs-12" id="test_parts">
									<?php foreach ($parts as $part) { ?>
									<input <?php if(isset($test_data)){ echo 'disabled'; }?> name="test_parts[]" <?php if(isset($test_data['parts']) && in_array($part,json_decode($test_data['parts']))){ echo 'checked'; } ?> type="checkbox" value="<?php echo $part; ?>"  />
										<strong> <?php echo $part; ?> </strong>
										<br>
                                    <?php } ?>
									</div>
                                </div>
								<div class="form-group col-xs-6 clearfix">
									<label for="test_parts"> <?php echo lang('app.language_admin_test_status'); ?><span>*</span></label>
									<div class="col-xs-12" id="active_status">
                                    <input type="radio" <?php echo set_radio('active_status', isset($test_data) ? $test_data['status'] : '', ( isset($test_data) && $test_data['status'] == 1 ? TRUE : FALSE)); ?> name="active_status" value="1" id="test_active" data-bv-field="active">
									<label for="test_active">Active</label>
									<input type="radio" <?php echo set_radio('active_status', isset($test_data) ? $test_data['status'] : '', ( isset($test_data) && $test_data['status'] == 0 ? TRUE : FALSE)); ?> name="active_status" value="0" id="test_inactive" data-bv-field="active">
									<label for="test_inactive">Inactive</label>
									</div>
                                </div>
                            </div>
                       	</fieldset>
                       	
						<div class="form-group form-actions pull-right" style="clear: both;">
							<button type="submit" id="submitBtn" class="btn btn-lg btn-primary wpsc_button" style="pointer-events: none;"><?php echo lang('app.language_admin_institutions_submit_btn'); ?></button>
							<img id="loading_in" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" alt="..." />
						</div>	 
                    <?php form_close(); ?>
                    </div>
                    
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$('#testdetail_form').submit(function (e) {
	e.preventDefault();
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
            }else {
				set_errors(data);
				if(data.errors.form_already_exist){
					$('#warning_message_final').show();
				}else{
					$('#warning_message_final').hide();
				}
				if(data.errors.test_type){
					if(data.errors.test_type == 'practice'){
						$('#warning_message #practice_update_add').attr('data-value',data.errors.test_detail_id);
						$('#warning_message').show();
					}
				}else{
					$('#warning_message').hide();
				}
				if(data.errors.active_placement){
					$('#warning_message_placement').show();
				}else{
					$('#warning_message_placement').hide();
				}
            }
        }
    });
    return false;
});

function clear_errors(data){
	if (typeof (data.errors) != "undefined" && data.errors !== null) {
		for (var k in data.errors) {
			$('#' + k).next('p').remove();
		}
	}
}

function set_errors(data){
	if (typeof (data.errors) != "undefined" && data.errors !== null) {
		for (var k in data.errors) {
			$(data.errors[k]).insertAfter($("#" + k));
		}
	}
}
$(function () {
	$('#practice_update_add').click(function (e) {
		e.preventDefault();
	    $('.loading_main').show();
		testdetail_id = $(this).attr('data-value');
		$('#updateTestFormDetail').modal('hide');
		$('#updateTestFormDetail .modal-body').html("");
		$('#updateTestFormDetail').on('hidden.bs.modal', function () {
			$('#update_practice_inactive').modal('show');
		});
		$("#update_practice_inactive").on("show.bs.modal", function () {
			$(this).find(".modal-body").load("<?php echo site_url('admin/testform_details_edit'); ?>" + '/' + testdetail_id, function () {
				$('.loading_main').hide();
				$('.wpsc_button').css('pointer-events','all');
				$('.wpsc_button').removeClass('disabled');
				getFormId();
				$('#test_product').attr('disabled', true);
				return false;
			});
		});
		
	});
});
$( "#test_purpose" ).change(function() {
    if($( this ).val() == 1){
    $( "#test_name_header" ).append( "<span>*</span>" );
    $("#test_product_header_span").hide("span");
    } else if($( this ).val() == 5 || $( this ).val() == 6) {
		$("#test_product_header_span").hide("span");
		$("#test_name_header").find("span").remove();
	}
	else{
         $("#test_name_header").find("span").remove();
          $("#test_product_header_span").show("span");
    }
});

$('#test_name').keyup(function() {
    var maxLength = 120;
    var length = $(this).val().length;
    var length = maxLength-length;
    $('#chars').text(length);
});
if($("#test_purpose").val() == 1){
    $( "#test_name_header" ).append( "<span>*</span>" );
    $("#test_product_header_span").hide("span");
}else if($("#test_purpose").val() == 5 || $("#test_purpose").val() == 6) {
	$("#test_product_header_span").hide("span");
	$("#test_name_header").find("span").remove();
}
else{
	$("#test_name_header").find("span").remove();
	$("#test_product_header_span").show("span");
}



function getFormId() {
	
	var test_purpose = $('#test_purpose').val();
	var test_detail_id = $('#hidden_test_detail_id').val();

	if(test_purpose == '') {
		$('#show_error').html("");
		$('#test_product').prop('selectedIndex',0);
		$('#test_product').attr('disabled', true);
		$('#form_id').prop('selectedIndex',0);
		$('#form_id').find('option').not(':first').remove();
		$('#form_id').attr('disabled', true);
		$('#form_version').prop('selectedIndex',0);
		$('#form_version').find('option').not(':first').remove();
		$('#form_version').attr('disabled', true);
	} 

	if(test_purpose == 3 || test_purpose == 4) {
		$('#test_product').attr('disabled', false);
		var test_product_id = $('#test_product').val();
	} else {
		$('#test_product').prop('selectedIndex',0);
		$('#test_product').attr('disabled', true);
	}

	$.ajax({
		type: "POST",
		url: "<?php echo site_url("admin/get_valid_test_form_id")?>",
		data: {
			test_product_id : test_product_id,
			test_purpose : test_purpose,
			test_detail_id : test_detail_id
		},
		dataType: 'html',
		success: function (data)
		{
			if(data) {
				if((test_purpose == 3 || test_purpose == 4) && test_product_id == '') {
					$('#show_error').html("");
					$('#form_id').prop('selectedIndex',0);
					$('#form_id').find('option').not(':first').remove();
					$('#form_id').attr('disabled', true);
					$('#form_version').prop('selectedIndex',0);
					$('#form_version').find('option').not(':first').remove();
					$('#form_version').attr('disabled', true);
				} else {
					$('#show_error').html("");
					$('#form_id').attr('disabled', false);
					$('#form_id').html(data);
					$('#form_version').prop('selectedIndex',0);
					$('#form_version').find('option').not(':first').remove();
					$('#form_version').attr('disabled', true);
				}
				
			}
			else {
			 if(test_purpose != '') {
				$('#show_error').html('<div class="alert alert-danger" role="alert">Form ID not available.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
				$('#form_id').prop('selectedIndex',0);
				$('#form_id').find('option').not(':first').remove();
				$('#form_id').attr('disabled', true);
				$('#form_version').prop('selectedIndex',0);
				$('#form_version').find('option').not(':first').remove();
				$('#form_version').attr('disabled', true);
			}
			
			}
		}
	});
}

function changeFormId() {
	var test_product_id = $('#test_product').val();
	var test_purpose = $('#test_purpose').val();
	var form_id = $('#form_id').val();

	$.ajax({
		type: "POST",
		url: "<?php echo site_url("admin/check_form_id_exist")?>",
		data: {
			form_id : form_id,
		},
		dataType: 'html',
		success: function (data)
		{
			var parsedJson = JSON.parse(data);
			if(parsedJson.response == 'failure') {
			$.ajax({
			type: "POST",
			url: "<?php echo site_url("admin/get_valid_test_form_version")?>",
			data: {
				test_product_id : test_product_id,
				test_purpose : test_purpose,
				form_id : form_id,
				edit : false
			},
			dataType: 'html',
			success: function (data)
			{
				$('#show_error').html("");
				if(data) {
					$('#form_version').attr('disabled', false);
					$('#form_version').html(data);
				} else {
					$('#form_version').prop('selectedIndex',0);
					$('#form_version').attr('disabled', true);
				}
			}
			});
			} else {
				$('#show_error').html('<div class="alert alert-danger" role="alert">Form ID already exist. If you want to update form version please edit that form.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
				$('#form_version').prop('selectedIndex',0);
				$('#form_version').attr('disabled', true);
			}
		}
	});
}

function changeFormIdEdit() {
	var test_detail_id = $('#hidden_test_detail_id').val();
	$.ajax({
	type: "POST",
	url: "<?php echo site_url("admin/get_valid_test_form_version")?>",
	data: {
		test_detail_id : test_detail_id,
		edit : true
	},
	dataType: 'html',
	success: function (data)
	{
		if(data) {
			$('#form_version').attr('disabled', false);
			$('#form_version').html(data);
		}
	}
	});
}

$('#test_purpose').change(function() {
	if($('#test_purpose').val() == 3 || $('#test_purpose').val() == 4) {
		$.ajax({
		type: "POST",
		url: "<?php echo site_url("admin/get_test_product")?>",
		data: {
			test_purpose : $('#test_purpose').val()
		},
		dataType: 'html',
		success: function (data)
		{
			if(data) {
				$('#test_product').attr('disabled', false);
				$('#test_product').html(data);
			}
		}
		});
	}
});
</script>