<?php include_once 'header.php'; ?>


<!-- /.row -->
<style>
.fixed-panel {
	min-height: 130px;
	max-height: 130px;
	overflow-y: scroll;
}
</style>

<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-plus fa-fw"></i><?= esc($admin_heading) ?>
				<p class="pull-right">
					<strong>Current Server Time: <?php echo date('H:i'); ?></strong>
				</p>
			</div>
			<div class="panel-body">
				<div class="row">
	
					
					<div class="col-xs-12">
						<div class="panel panel-success">
							<div class="panel-heading">
								<h3 class="panel-title"><?php echo lang('app.language_admin_successful_run'); ?>:</h3>
							</div>
							<div class="panel-body fixed-panel">
								<?php if(!empty($success_logs)): ?>
                                <table class="table">
									<tr>
										<th>Date</th>
										<th>Time</th>
										<th>Attempt</th>
										<th>Message</th>
									</tr>
									<?php foreach($success_logs as $log): ?>
                                    	<?php if($log->status == 1): ?>
        									<tr>
        										<td><?php echo date('d-m-Y',$log->date_run); ?></td>
        										<td><?php echo $log->time_run; ?></td>
        										<td><?php echo $log->attempt; ?></td>
        										<td><?php echo $log->message; ?></td>
        									</tr>
                                    	<?php endif; ?>
									<?php endforeach; ?>
								</table>
                                <?php endif; ?>
							</div>
						</div>

						<div class="panel panel-danger">
							<div class="panel-heading">
								<h3 class="panel-title"><?php echo lang('app.language_admin_failure_run'); ?>:</h3>
							</div>
							<div class="panel-body fixed-panel">
                            <?php if(!empty($failure_logs)): ?>
								<table class="table">
									<tr>
										<th>Date</th>
										<th>Time</th>
										<th>Attempt</th>
										<th>Message</th>
									</tr>
									<?php foreach($failure_logs as $log): ?>
	                                	<?php if($log->status == 0): ?>
                                    <tr>
										<td><?php echo date('d-m-Y',$log->date_run); ?></td>
										<td><?php echo $log->time_run; ?></td>
										<td><?php echo $log->attempt; ?></td>
										<td><?php echo $log->message; ?></td>
									</tr>
										<?php endif; ?>
									<?php endforeach; ?>
								</table>
							<?php endif; ?>
                            </div>
						</div>
						
						<button type="button" class="btn btn-primary pull-right" onclick="window.location.reload()"><?php echo "Refresh"; ?></button>

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
</div>

<?php include 'footer.php';  ?>
<script>
    $(function(){
       $('#url').keyup(function(){
           if($(this).val()!=''){
             var href = "<a href='"+'https://'+$(this).val().trim().replace(/ /g,'')+'.catsstep.education'+"' target='_blank' >"+'https://'+$(this).val()+'.catsstep.education'+"</a>"  
             $('#preview').html(href);
           }else{
               $('#preview').html('');
           }
       }); 
    });
</script>