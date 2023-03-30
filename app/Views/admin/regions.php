<?php include_once 'header.php';  ?>
<?php
    $formattributes = array(
        'class' => 'form bv-form',
        'role' => 'form',
        'id' => 'region_form',
        'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok',
        'data-bv-feedbackicons-invalid' => 'glyphicon glyphicon-remove',
        'data-bv-feedbackicons-validating' => 'glyphicon glyphicon-refresh'
    );
    
    $countryOptions = array();
    $countryOptions[''] = lang('app.language_admin_please_select');
    
    foreach ($countries as $country) :
        $countryOptions[$country->countryCode] = $country->countryName;
    endforeach;
	
	foreach ($otherCountries as $other_country):
		$countryOptions[$other_country->countryCode] = $other_country->countryName;
	endforeach;
    
    $p = (isset($region)) ? current($region)->id : '';
    $disable = (isset($region)) ? 'disabled' : '';
?>
<div class="row">
	<p class="lead"></p>
	<div class="col-lg-6">
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="row">

					<div class="col-lg-12">		
					<?php if (isset($region) && !empty($region)): ?>
						<?php echo form_open('admin/postregion/'.$p, $formattributes); ?>
                        <?php else: ?>
                            <?php echo form_open('admin/postregion'); ?>
                        <?php endif; ?>

						<div class="form-group">
						<?php
						    echo form_label(lang('app.language_admin_region_country_field').'<span>*</span>', 'countryname') 
                            . form_dropdown('country_code', $countryOptions, (isset($region)) ? current($region)->countryCode : '', "class = 'form-control' $disable");
                        ?>
						</div>

						<div class="form-group">
						<?php
						    echo form_label(lang('app.language_admin_region_field').' <span>*</span>', 'regionname') 
                            . form_input('region_name', (isset($region)) ? current($region)->name : '' , "class = 'form-control' required");
                        ?>
						</div>						
						
					<?php echo form_submit('regionsubmit', lang('app.language_admin_submit'), "class = 'btn btn-primary'" ). form_close(); ?>
					</div>

				</div>
				<!-- /.row (nested) -->
			</div>
			<!-- /.panel-body -->
		</div>
		<!-- /.panel -->
	</div>
	<!-- /.col-lg-12 -->

	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<i class="fa fa-map-marker fa-fw"></i><?= esc($admin_heading) ?>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="table-responsive table-bordered">
					<table class="table">
						<thead>
							<tr>
								<th><?php echo lang('app.language_admin_region_tablehead_si_field'); ?></th>
								<th><?php echo lang('app.language_admin_region_country_field'); ?></th>
								<th><?php echo lang('app.language_admin_region_field'); ?></th>
								<th><?php echo lang('app.language_admin_action'); ?></th>
							</tr>
						</thead>
                        <?php if(!empty($regionlists)): ?>
                        <tbody>
                       		<?php foreach ($regionlists as $key => $region) : ?>
                            <tr>
                                <td><?php echo $key+1; ?></td>
                                <td><?php echo $region->countryCode; ?></td>
                                <td><?php echo $region->name; ?></td>
                                <td>
                                	<div class="btn-group">
                                    	<button type="button" class="btn btn-primary" onclick="window.location='<?php echo site_url('admin/regions/'.$region->id); ?>'"><?php echo lang('app.language_admin_edit'); ?></button>
    									<button  type="button" class="btn btn-danger delete_region" id="region_<?php echo $region->id; ?>"><?php echo lang('app.language_admin_delete'); ?></button>
                                	</div>
                                </td>
                            </tr>
							<?php endforeach; ?>
						</tbody>
					<?php endif; ?>
					</table>
                </div>
				<!-- /.table-responsive -->
			</div>
			<!-- /.panel-body -->
		</div>
		<!-- /.panel -->
	</div>
	<!-- /.col-md-12-->
</div>
<!-- /.row -->

<?php include 'footer.php';  ?>
<script>
$(function(){
	$('.delete_region').click(function(){
		 if (confirm("<?php echo lang('app.language_admin_are_you_sure'); ?>")) {
		        var region_str = $(this).attr('id');
				var region_id = region_str.replace("region_", "");
		        window.location  = "<?php echo site_url('admin/deleteregion'); ?>" + '/' + region_id;
		 }
		 return false;
	});
});
</script>