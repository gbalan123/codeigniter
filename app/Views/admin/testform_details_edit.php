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
		<div class="panel panel-default">
			<div class="panel-heading">
				<em class="fa fa-plus fa-fw"></em><?= esc($admin_heading) ?>
			</div>
			<div class="panel-body">
				<div class="row">

					<div class="col-xs-12 clearfix">
					<?php echo form_open_multipart('admin/testform_details_update', array('class' => 'form bv-form', 'role' => 'form', 'id' => 'testdetail_form', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>
					<fieldset>  
							<legend><?php echo lang('app.language_admin_testform_details'); ?></legend>
							<span id="show_error"></span>
							<div class="row">	
								<div class="form-group col-xs-6 clearfix">
									<label for="test_purpose"><?php echo lang('app.language_admin_testform_purpose'); ?> </label>
									<select class="form-control" <?php if(isset($test_data)){ echo 'disabled'; }?> name="test_purpose" id="test_purpose">
										<option value="">Please select</option>
									<?php foreach($test_purposes as $test_purpose):?>
										<option <?php echo set_select('test_purpose', isset($test_data) ? $test_data['tds_group_id'] : '', ( isset($test_data) && $test_data['tds_group_id'] == $test_purpose->id ? TRUE : FALSE)); ?> value="<?php echo $test_purpose->id; ?>"><?php echo $test_purpose->test_type; ?></option>
									<?php endforeach; ?>
									</select>
									<input type="hidden" name="test_purpose" value="<?php echo $test_data['tds_group_id'] ?>" />	
								</div>
								<div class="form-group col-xs-6 clearfix">
									<label for="form_version"><?php echo lang('app.language_admin_test_product'); ?> </label>
									<select disabled class="form-control" name="test_product" id="test_product">
										<option value="">Please select</option>
									<?php foreach($products as $product):?>
										<?php if($product->id == 10) { ?>
										<option <?php echo set_select('test_product', isset($test_data) ? $test_data['test_product_id'] : '', ( isset($test_data) && $test_data['test_product_id'] == $product->id ? TRUE : FALSE)); ?> value="<?php echo $product->id; ?>"><?php echo 'Step Higher'; ?></option>	
										<?php }elseif($product->id <= 9 || $product->id > 12) { ?>
										<option <?php echo set_select('test_product', isset($test_data) ? $test_data['test_product_id'] : '', ( isset($test_data) && $test_data['test_product_id'] == $product->id ? TRUE : FALSE)); ?> value="<?php echo $product->id; ?>"><?php echo $product->name; ?></option>
										<?php } ?>
									<?php endforeach; ?>
									</select>
									<input type="hidden" name="test_product" value="<?php echo $test_data['test_product_id'] ?>" />
								</div>
							</div>
							<div class="row">	
								<div class="form-group col-xs-6 clearfix">
									<label for="form_id"><?php echo lang('app.language_admin_testform_id'); ?><span>*</span> </label>
									<select class="form-control" name="form_id" id="form_id" onchange="changeFormId()">
										<option>Please select</option>
									</select>
									<input type="hidden" name="replace_id" value="<?php echo set_value('id', isset($test_data) ? $test_data['id'] : ''); ?>" id="hidden_test_detail_id"/>	
								</div>
								<div class="form-group col-xs-6 clearfix">
									<label for="form_version"><?php echo lang('app.language_admin_testform_version'); ?><span>*</span></label>
									<select class="form-control" name="form_version" id="form_version" disabled>
										<option>Please select</option>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="form-group col-xs-6 clearfix">
									<label for="test_name"><?php echo lang('app.language_admin_testform_name'); ?> </label>
									<input type="text" class="form-control" name="test_name" id="test_name" value="" />	
								</div>
								<div class="form-group col-xs-6 clearfix">
									<label for="form_id"><?php echo lang('app.language_admin_testform_type'); ?> </label>
									<select <?php if(isset($test_data)){ echo 'disabled'; }?> class="form-control" name="test_type" id="test_type">
										<option value="">Please select</option>
									<?php foreach($types as $type):?>
										<option <?php echo set_select('test_type', isset($test_data) ? $test_data['test_type'] : '', ( isset($test_data) && $test_data['test_type'] == $type ? TRUE : FALSE)); ?> value="<?php echo $type; ?>"><?php echo $type; ?></option>
									<?php endforeach; ?>
									</select>
									<input type="hidden" name="test_type" value="<?php echo $test_data['test_type'] ?>" />	
								</div>
							</div>
							
							<div class="row">
                                <div class="form-group col-xs-6 clearfix">
                                    <label for="test_parts"> <?php echo lang('app.language_admin_test_parts'); ?></label>
									<div class="col-xs-12" id="test_parts">
									<?php foreach ($parts as $part) { ?>
									<input name="test_parts[]" type="checkbox" value="<?php echo $part; ?>"  />
										<strong> <?php echo $part; ?> </strong>
										<br>
                                    <?php } ?>
									</div>
                                </div>
								<div class="form-group col-xs-6 clearfix">
									<label for="test_parts"> <?php echo lang('app.language_admin_test_status'); ?></label>
									<div class="col-xs-12" id="active_status">
                                    <input type="radio" disabled <?php echo set_radio('active_status', isset($test_data) ? $test_data['status'] : '', ( isset($test_data) && $test_data['status'] == 1 ? TRUE : FALSE)); ?> name="active_status" value="1" id="test_active" data-bv-field="active">
									<label for="test_active">Active</label>
									<input type="radio" disabled name="active_status" value="0" id="test_inactive" data-bv-field="active">
									<label for="test_inactive">Inactive</label>
									<input type="hidden" name="active_status" value="<?php echo $test_data['status'] ?>" />
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
</script>