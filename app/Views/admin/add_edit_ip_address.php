
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-plus fa-fw"></i><?= esc($admin_heading) ?>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="row">
					<div class="col-xs-12 clearfix">
                        <?php echo form_open('admin/post_ip_add_edit', array('id' => 'ip_add_edit_form')); ?>
                            <fieldset>
                                <div class="row">
                                    <div class="form-group col-xs-6 clearfix">
                                        <label><?php echo lang('app.language_admin_downtime_ip_address'); ?> </label><span class="error">*</span>
                                        <input type="text" class="form-control" name="ip_address" id="ip_address" maxlength="120"  value="<?php echo(($ip_data == False)) ? '' : $ip_data['ip_address']; ?>" />	
                                        <span style="float:right; margin-top: 5px; font-weight: bold">
                                    </div>
                                    <div class="form-group col-xs-6 clearfix">
                                        <label><?php echo lang('app.language_admin_downtime_ip_name'); ?> </label><span class="error">*</span>
                                        <input type="text" class="form-control" name="ip_name" id="ip_name" maxlength="120"  value="<?php echo(($ip_data == False)) ? '' : $ip_data['ip_name']; ?>" />	
                                        <span style="float:right; margin-top: 5px; font-weight: bold">
                                    </div>
                                    <input type="hidden" name="id" value="<?php echo(($ip_data == False)) ? '' : $ip_data['id']; ?>"  />
                                </div>
                            </fieldset>
                        
						<div class="form-group form-actions pull-right" style="clear: both;">
							<button type="submit" id="submitBtn" class="btn btn-sm btn-primary wpsc_button" style="pointer-events: none;"><?php echo lang('app.language_admin_institutions_submit_btn'); ?></button>
						</div>
                        <?php echo form_close();?>
                    </div>
				</div>
				<!-- /.row (nested) -->
			</div>
			<!-- /.panel-body -->
		</div>
		<!-- /.panel -->
	</div>
	<!-- /.col-lg-6-->
</div>

<script>
$('#ip_add_edit_form').submit(function (e) {
	e.preventDefault();   
    $('#submitBtn').attr('disabled', true);
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