<?php include_once 'header.php';?>
<div class="row">
	<div class="col-xs-12">
        <div class="panel panel-default">
			<?php echo form_open_multipart('admin/set_adaptive_placement', array('class' => 'form bv-form', 'role' => 'form', 'id' => 'set_placement_test', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>
            <div class="panel-heading" style="overflow:hidden">
				<div class="pull-left" style="margin-top: 10px;">
                <em class="glyphicon glyphicon-wrench fa-fw"></em><?= esc($admin_heading) ?>
				</div>
            </div>
            <div class="panel-body">
				<div class="table-responsive table-bordered table-striped">
					<table class="table">
						<thead>
							<tr>
								<th>&nbsp;</th>
								<th><?php echo lang('app.language_admin_testform_id'); ?></th>
								<th><?php echo lang('app.language_admin_testform_version'); ?></th>
                                <th><?php echo lang('app.language_admin_testform_name'); ?></th>
                                <th><?php echo lang('app.language_admin_testform_type'); ?></th>
                                <th><?php echo lang('app.language_admin_testform_version_parts');?></th>
							</tr>
						</thead>
						<tbody>
						<?php if(isset($placement_details) && $placement_details != '') { ?>
						<?php  foreach($placement_details as $testFormDetail): ?>
							<tr>
								<td><input type="radio" name="testformdetail_id"  value="<?php echo $testFormDetail->id; ?>" <?php  echo ($active_placement == $testFormDetail->id) ? 'checked="checked"' : ''; ?>  /></td>
								<td><?php echo $testFormDetail->test_formid; ?></td>
								<td><?php echo $testFormDetail->test_formversion; ?></td>
								<td><?php echo ($testFormDetail->test_name != "") ? $testFormDetail->test_name : "N/A"; ; ?></td>
                                <td><?php echo $testFormDetail->test_type; ?></td>
                                <td><?php $parts = $testFormDetail->parts; echo isset($parts)? implode(',', json_decode($parts)) : "N/A";  ?></td>
							</tr>
						<?php endforeach;  ?>
						<?php }else{  ?>
							<tr>
								<td><h4>No Records Found</h4></td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
				</div>
				<div class="btn-group pull-right" style="margin-top:10px;">
					<button type="submit" id="submitBtn" class="btn wpsc_button btn-allocver" style="pointer-events: none;background-color: #117dc1; color: #fff;">Make Active</button>	
				</div>
			</div>
			<?php form_close(); ?>
			<div class="text-center">
			
			</div>
		</div>
	</div>
</div>
<div class="container">
    <div id="addTestFormDetail" class="modal fade" role="dialog" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content" >
                <img class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" alt="..." />
                <div class="modal-header" style="border-bottom: 0px solid #e5e5e5;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" style="font-weight: bold; text-align: center;"></h4>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer" style="border-top: 0px solid #e5e5e5;"></div>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div id="updateTestFormDetail" class="modal fade" role="dialog" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content" >
                <img class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" alt="..." />
                <div class="modal-header" style="border-bottom: 0px solid #e5e5e5;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" style="font-weight: bold; text-align: center;"></h4>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer" style="border-top: 0px solid #e5e5e5;"></div>
            </div>
        </div>
    </div>
</div>
<div class="container">
<div id="formid_exist_alert" class="modal fade" role="dialog" >
	<div class="modal-dialog modal-lg">
		<div class="modal-content" >
			<img class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" alt="..." />
			<div class="modal-header" style="border-bottom: 0px solid #e5e5e5;">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" style="font-weight: bold; text-align: center;"></h4>
			</div>
			<div class="modal-body">
			</div>
			<div class="modal-footer" style="border-top: 0px solid #e5e5e5;"></div>
		</div>
	</div>
</div>
</div>
<div class="container">
<div id="update_practice_inactive" class="modal fade" role="dialog" >
	<div class="modal-dialog modal-lg">
		<div class="modal-content" >
			<img class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" alt="..." />
			<div class="modal-header" style="border-bottom: 0px solid #e5e5e5;">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" style="font-weight: bold; text-align: center;"></h4>
			</div>
			<div class="modal-body">
			</div>
			<div class="modal-footer" style="border-top: 0px solid #e5e5e5;"></div>
		</div>
	</div>
</div>
</div>
<?php include 'footer.php'; ?>
<script>
$(window).load(function(){
    $('.wpsc_button').css('pointer-events','all');
});
</script>