<?php include_once 'header.php'; ?>
<style>
    #update_unit_test_form p {
	color: red;
}
</style>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="row">
					<div class="col-xs-12 clearfix">
                    <form id="update_unit_test_form">
                        <legend style="font-size: 14px;">  
                            <span id="showMessage"></span>
                            <img class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" alt="..." />
							<div class="row">	
								<div class="form-group col-xs-6 clearfix">
									<label for="update_for"><?php echo lang('app.language_admin_unit_progress_update_for'); ?><span>*</span></label>
									<select class="form-control" name="update_for" id="update_for">
										<option value="">Please select</option>
										<option value="1">Unit</option>
										<option value="2">Level</option>
                                    </select>
								</div>
								<div class="form-group col-xs-6 clearfix">
                                    <label for="user_id"><?php echo lang('app.language_admin_unit_progress_user_id'); ?><span>*</span></label>
                                    <input type="text" name="user_id" class="form-control" id="user_id" />									
								</div>
							</div>
							<div class="row">	
								<div class="form-group col-xs-6 clearfix">
                                    <label for="unit_id"><?php echo lang('app.language_admin_unit_progress_unit_id'); ?><span id="unit_id_mandatory" style="display: none;">*</span></label>
                                    <input type="text" name="unit_id" class="form-control" id="unit_id" disabled/>									
								</div>
								<div class="form-group col-xs-6 clearfix">
                                    <label for="level_id"><?php echo lang('app.language_admin_unit_progress_level_id'); ?><span id="level_id_mandatory" style="display: none;">*</span></label>
                                    <input type="text" name="level_id" class="form-control" id="level_id" disabled/>									
								</div>
							</div>
                       	</legend>
                       	
						<div class="form-group form-actions pull-right" style="clear: both;">
							<button type="submit" id="submitBtn" class="btn btn-lg btn-primary wpsc_button"><?php echo lang('app.language_admin_unit_progress_update'); ?></button>
						</div>	 
                    </form>
                    </div>
                    
				</div>
			</div>
		</div>
	</div>
</div>

<?php include 'footer.php'; ?>

<script>
    $('#update_for').change(function(){
        $("p").text("");
        if($(this).val() == 1) {
            $('#unit_id').attr('disabled', false);
            $('#unit_id_mandatory').show("span");
            $('#level_id').attr('disabled', true);
            $('#level_id_mandatory').hide("span");
            $('#level_id').val('');
        } else if($(this).val() == 2) {
            $('#level_id').attr('disabled', false);
            $('#level_id_mandatory').show("span");
            $('#unit_id').attr('disabled', true);
            $('#unit_id_mandatory').hide("span");
            $('#unit_id').val('');
        } else {
            $('#level_id').attr('disabled', true);
            $('#level_id_mandatory').hide("span");
            $('#unit_id').attr('disabled', true);
            $('#unit_id_mandatory').hide("span");
            $('#unit_id').val('');
            $('#level_id').val('');
        }
    });

    $('#submitBtn').click(function(e) {
        e.preventDefault();
        $('#submitBtn').attr('disabled', true);
        $('.loading_main').show();
        $( ".help-block" ).empty();
        $.ajax({
            type:"POST",
            url:"<?php echo site_url('admin/post_update_unit_progress'); ?>",
            data:$("#update_unit_test_form").serialize(),
            success:function (data) {
                $('#submitBtn').attr('disabled', false);
                $('.loading_main').hide();
                $( ".help-block" ).empty();
                if(data.errors){
                    clear_errors(data);
                    set_errors(data);
                    $('#showMessage').html('');
                }else{
                    if (data.result.code == 1003) {
                        $('#showMessage').html('<div class="alert alert-danger" role="alert">'+data.result.message +'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
                    } else {
                        $('#showMessage').html('<div class="alert alert-success" role="alert">'+data.result.message +'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>')
                    }
                    $('#update_unit_test_form').find('p').remove();
                    $('#update_unit_test_form').find('input:text').val('');
                    $('#update_for').prop('selectedIndex',0);
                }
            }
        });
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