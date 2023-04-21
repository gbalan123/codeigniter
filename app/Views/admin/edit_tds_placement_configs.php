<style>
.form-alignment .form-group label {
 min-width: 130px
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
					<div class="col-xs-12 form-alignment clearfix">
                        <?php echo form_open('admin/tds_placement_configs', array('id' => 'edit_tds_placement_configs_form')); ?>
                        <?php $active_product_id = ($active_product) ? $active_product['product_id'] : 0 ; ?>
                        <!-- <?php $active_institution = ($active_product) ? $active_product['institution_id'] : 0 ; ?> -->
                            <legend style="font-size: 14px;">
                            <div class="row">	
								<div class="form-group col-xs-12 clearfix">
                            <label for="name"> Institution  </label> 
                            <select disabled class="form-control" name="institution_id" style="width: auto; display: inline-block; min-width: 300px;">
                                    <option  value="<?php echo $active_product['institution_id']; ?>"><?php echo $active_product['institution_name']; ?></option>
                            </select>
							<input type="hidden" name="institution_id" value="<?php echo $active_product['institution_id']?>"/>
                        </div>						
								<div class="form-group col-xs-12 clearfix">
                                <label for="name"> Choose Product <span> *</span></label>
                            <select class="form-control" name="product_id" style="width: auto; display: inline-block;">
                                <?php foreach ($products as $product) { ?>
                                    <option <?php echo ($active_product_id == $product->id) ? "selected" : "" ; ?> value="<?php echo $product->id; ?>"><?php echo $product->name; ?></option>
                                <?php }  ?>
                            </select>
								</div>
							</div>
                                    
                            <div class="row">     
                            <div class="form-group col-xs-12 clearfix">
									<label for="test_parts"> <?php echo lang('app.language_admin_test_status'); ?><span> *</span></label>
                                    <select class="form-control" name="product_status" style="width: auto; display: inline-block;">
											<option <?php echo (isset($active_product) && $active_product['status'] == 1) ? "selected" : "" ; ?> value="1">Active</option>
											<option <?php echo (isset($active_product) && $active_product['status'] == 0) ? "selected" : "" ; ?> value="2">Inactive</option> 
											</select>
                                </div>
                                </div>
                            </legend>
                        
						<div class="form-group form-actions pull-right" style="clear: both;">
							<button type="submit" id="submitBtn" class="btn btn-md btn-primary wpsc_button" style="pointer-events: none;"><?php echo lang('app.language_admin_institutions_submit_btn'); ?></button>
						</div>
                        <?php echo form_close();?>
                    </div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$('#edit_tds_placement_configs_form').submit(function (e) {
	e.preventDefault();   
    $('#submitBtn').attr('disabled', true);
    $.ajax({
    	type: "POST",
        url: $(this).attr('action'),
        data: $(this).serialize(),
        dataType: 'json',
        success:function(data)
        {
            $('#submitBtn').attr('disabled', false);
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
