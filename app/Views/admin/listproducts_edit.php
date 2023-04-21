<style>
#product_form p {
	color: red;
}
</style>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<em class="fa fa-plus fa-fw"></em><?= esc($admin_heading) ?>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="row">

					<div class="col-xs-12 clearfix">
                    <?php echo form_open_multipart('admin/listproducts_update', array('class' => 'form bv-form', 'role' => 'form', 'id' => 'product_form', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>
                    	<fieldset>  <?php
                        if (! empty($products)) {
                            foreach ($products as $product) :  ?>	
                            	<legend><?php echo lang('app.language_admin_product_details'); ?></legend>
								<div class="row">
									<div class="form-group col-xs-6 clearfix">
										<label for="product_name"><?php echo lang('app.language_admin_product_name'); ?> </label>
										<input type="text" class="form-control" name="product_name" id="product_name" disabled value="<?php echo $product->name; ?>" /> 
										<input type="hidden" name="product_id" value="<?php echo $product->id; ?>" />
                                                                                <input type="hidden" name="audience" value="<?php echo $product->audience; ?>" />
									</div>
									<div class="form-group col-xs-6 clearfix">
										<label for="product_status"><?php echo lang('app.language_admin_product_active_status'); ?> <span>*</span></label>
										<select class="form-control" name="product_status" id="product_status">
											<option value=""><?php echo lang('app.language_admin_please_select'); ?></option>
											<option value="1" <?php echo ($product->active == 1 ) ? 'selected' : '' ?>><?php echo 'Yes' ?></option>
											<option value="0" <?php echo ($product->active == 0 ) ? 'selected' : '' ?>><?php echo 'No' ?></option>
										</select>
									</div>
								</div><?php
                            endforeach;
                        } ?>
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
$('#product_form').submit(function (e) {
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
			if(k == 'sectors'){
				$('.multiselect').next('p').remove();
			}else{
				$('#' + k).next('p').remove();
			}
		}
	}
}

function set_errors(data){
	if (typeof (data.errors) != "undefined" && data.errors !== null) {
		for (var k in data.errors) {
			if(k == 'sectors'){
				$(data.errors[k]).insertAfter($('.multiselect'));  
			}else{
				$(data.errors[k]).insertAfter($("#" + k));
			}
		}
	}
}
</script>