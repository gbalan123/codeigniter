<?php include_once 'header.php';  ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                	<div class="col-md-12">
                		<?php $p = (isset($institution_value)) ? current($institution_value)->institutionGroupId : ''; ?>
                        <?php if (isset($institution_value) && !empty($institution_value)): ?>
                            <?php echo form_open_multipart('admin/postInstitution/'.$p, array('class' => 'form-inline')); ?>

                        <?php else: ?>
                            <?php echo form_open_multipart('admin/postInstitution', array('class' => 'form-inline')); ?>
                        <?php endif; ?>
                        <div class="form-group">
                           
                                <label for="type"><?= esc($admin_title) ?></label>
                                <input type="text" class="form-control" name="institution_types" value="<?php if(isset($institution_value)) {echo $institution_value[0]->englishTitle;}else{ echo " ";}?>"/>
                            
                            <input class="btn btn-primary" type="submit" name="" value="<?php echo lang('app.language_admin_submit'); ?>">
                           
                        </div>
                         <?php echo form_close(); ?>
                  
                </div> </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
	<div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-tasks fa-fw"></i><?= esc($admin_heading) ?>
                        </div>

                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive table-bordered">
                                <table class="table">
                                    <thead>
                                        <tr>
                                        	<th><?php echo lang('app.language_admin_institution_type'); ?></th>
                                            <th><?php echo lang('app.language_admin_action'); ?></th>
                                        </tr>
                                    </thead>
                                    <?php if(!empty($results)): ?>
                                    <tbody>
                                    
                                       <?php foreach($results as $result): ?>	
                                       	<tr>
                                        <td><?php echo $result->englishTitle; ?></td>
                                            <td>
                                            	<div class="btn-group">
												  <button type="button" class="btn btn-primary" onclick="window.location='<?php echo site_url('admin/institution_types/'.$result->institutionGroupId); ?>'"><?php echo lang('app.language_admin_edit'); ?></button>
												  <button  type="button" class="btn btn-danger delete_cms" id="<?php echo $result->institutionGroupId; ?>"><?php echo lang('app.language_admin_delete'); ?></button>
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
	<!-- /.col-lg-6-->
</div>

<?php include 'footer.php';  ?>
<script>
$(function(){
	$('.delete_cms').click(function(){
		 if (confirm("<?php echo lang('app.language_admin_are_you_sure'); ?>")) {
		        var institute_id = $(this).attr('id');
		        window.location  = "<?php echo site_url('admin/deleteInstitutionType'); ?>" + '/' + institute_id;
		 }
		 return false;
	});	
	
	
});
</script>