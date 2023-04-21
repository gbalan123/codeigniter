<?php include_once 'header.php';?>
<div class="row">
	<div class="col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading" style="overflow:hidden">
				<div class="pull-left" style="margin-top: 10px;">
                <em class="glyphicon glyphicon-wrench fa-fw"></em><?= esc($admin_heading) ?>
				</div>
				<div class="btn-group pull-right" style="margin-top:3px;">
                        <button type="button" class="btn btn-success wpsc_button" style="pointer-events: all;" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#addTestFormDetail" id="addBtn">
                            <em class="fa fa-plus fa-fw"></em>
                            Add</button>
                    </div>
            </div>
            <div class="panel-body">
				<div class="table-responsive table-bordered table-striped">
					<table class="table">
						<thead>
							<tr>
								<th><?php echo lang('app.language_admin_testform_id'); ?></th>
								<th><?php echo lang('app.language_admin_testform_version'); ?></th>
                                <th><?php echo lang('app.language_admin_testform_name'); ?></th>
                                <th><?php echo lang('app.language_admin_testform_version_product'); ?></th>
                                <th><?php echo lang('app.language_admin_testform_type'); ?></th>
                                <th><?php echo lang('app.language_admin_testform_version_purpose'); ?></th>
                                <th><?php echo lang('app.language_admin_testform_version_parts');?></th>
								<th><?php echo lang('app.language_admin_testform_version_active'); ?></th>
                                <th><?php echo lang('app.language_admin_testform_version_edit'); ?></th>
							</tr>
						</thead>
						<tbody>
						<?php foreach($testFormDetails as $testFormDetail): ?>
							<tr <?php if($testFormDetail->status == 0){ echo 'style="opacity:0.5"'; } ?>>
								<td><?php echo $testFormDetail->test_formid; ?></td>
								<td><?php echo $testFormDetail->test_formversion; ?></td>
                                                                <td><?php echo ($testFormDetail->test_name != "") ? $testFormDetail->test_name : "N/A"; ?></td>
                                                                <td><?php
                                                                        if (isset($testFormDetail->product)) {
                                                                            if ($testFormDetail->course_type == "Higher") {
                                                                                echo "Step Higher";
                                                                            } else {
                                                                                echo $testFormDetail->product;
                                                                            }
                                                                        } else {
                                                                            echo "N/A";
                                                                        }
                                                                        ?></td>
                                <td><?php echo $testFormDetail->test_type; ?></td>
                                <td><?php echo $testFormDetail->purpose; ?></td>
                                <td><?php $parts = $testFormDetail->parts; echo isset($parts)? implode(',', json_decode($parts)) : "N/A";  ?></td>
								<td><?php echo $testFormDetail->status == 0 ? "NO": "Yes"; ?></td>
								<td><?php echo '<div class="btn-group"><button type="button" class="btn btn-primary wpsc_button listeditBtn" style="pointer-events:none;" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#updateTestFormDetail"  data-value="'. $testFormDetail->id .'"  > <i class="fa fa-edit fa-fw"></i> '. lang("app.language_admin_edit") .'</button></div>' ; ?></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="text-center">
			<?php if ($pager) :?>
			<?php $pager->setPath($_SERVER['PHP_SELF']);?>
			<?= $pager->links() ?>
			<?php endif ?> 
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
			<img class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>"  alt="..." />
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

$(function () {
	var testdetail_id;

	$('.listeditBtn').click(function (e) {
	    e.preventDefault();
	    testdetail_id = $(this).attr('data-value');	    
	    $('.loading_main').show();
		$('#addTestFormDetail').modal('hide');
		$('#addTestFormDetail .modal-body').html("");
	    $("#updateTestFormDetail").on("show.bs.modal", function () {
			$(this).find(".modal-body").html("");
	        $(this).find(".modal-body").load("<?php echo site_url('admin/testform_detail'); ?>" + '/' + testdetail_id, function () {
				changeFormIdEdit();
	            $('.loading_main').hide();
	            $('.wpsc_button').css('pointer-events','all');
	            $('.wpsc_button').removeClass('disabled');
	            return false;
	        });
	    });

	});
	$('#addBtn').click(function (e) {
	    e.preventDefault();
	    $('.loading_main').show();
		$('#updateTestFormDetail').modal('hide');
		$('#updateTestFormDetail .modal-body').html("");
	    $("#addTestFormDetail").on("show.bs.modal", function () {
	        $(this).find(".modal-body").load("<?php echo site_url('admin/testform_detail'); ?>", function () {
	            $('.loading_main').hide();
	            $('.wpsc_button').css('pointer-events','all');
	            $('.wpsc_button').removeClass('disabled');
	            return false;
	        });
	    });

	});
	
	$('#addTestFormDetail').on('show.bs.modal', function (e) {
	   $(document.body).addClass('modal-open');
	});
	
	$('#addTestFormDetail').on('hidden.bs.modal', function (e) {
	   $('#addTestFormDetail .modal-body').html("");
	});
	
	$('#update_practice_inactive').on('show.bs.modal', function (e) {
	   $(document.body).addClass('modal-open');
	});
	
	$('#update_practice_inactive').on('hidden.bs.modal', function (e) {
	   $('#addTestFormDetail .modal-body').html("");
	});
});

</script>