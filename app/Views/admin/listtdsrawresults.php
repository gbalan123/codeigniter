<?php include_once 'header.php';  ?>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<em class="fa fa-tasks fa-fw"></em><?= esc($admin_heading) ?>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="table-responsive table-bordered">
					<table class="table">
						<thead>
							<tr>
								<th><?php echo lang('app.language_admin_start_date'); ?></th>
								<th><?php echo lang('app.language_admin_end_date'); ?></th>
								<th><?php echo "XML"; ?></th>
								<th><?php echo lang('app.language_admin_action'); ?></th>
							</tr>
						</thead>
						<?php if(!empty($results)): ?>
                        <tbody>
                        	<?php foreach($results as $result): ?>	
                            <tr>
								<td><?php echo date('d-m-Y', $result->start); ?></td>
								<td><?php echo date('d-m-Y', $result->end); ?></td>
								<td><?php echo str_replace("zip", "xml", $result->local_uri); ?></td>
								<td>
									<div class="btn-group">
								    	<a target="_blank" href="<?php echo site_url('admin/view_xml/'. $result->task_id); ?>" class="btn btn-primary"><?php echo lang('app.language_admin_view_details'); ?></a>
									</div>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
 						<?php endif; ?>
					</table>
					<?php if ($pager) :?>
					<?= $pager->links('pagination_tds_raw_results'); ?>
					<?php endif ?> 
				</div>
			</div>
		</div>
	</div>
</div>
<?php include 'footer.php';  ?>