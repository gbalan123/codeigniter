<?php include_once 'header.php';  ?>

<!-- /.row -->
<div class="row">
	
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading"><i class="fa fa-plus fa-fw"></i><?= esc($admin_heading) ?></div>
			<div class="panel-body">
				<div class="row">
					<div class="col-xs-12">

					<?php
					
					if(isset($categorydatas) && !empty($categorydatas)){ 
						echo form_open('admin/postmailcategory/'.$categorydatas[0]->id,array('class'=>'form bv-form','role'=>'form','id'=>'category_form','data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh"));
					}else{
						echo form_open('admin/postmailcategory',array('class'=>'form bv-form','role'=>'form','id'=>'category_form','data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh"));
					}
					?>


						<div class="form-group">
							<label for="name"><?php echo lang('app.language_admin_category_name'); ?>:</label> <input type="text"
								class="form-control" name="category_name" id="category_name" value="<?php echo (isset($categorydatas) && null !== $categorydatas[0]->category_name) ? $categorydatas[0]->category_name : ''; ?>" required  >
						</div>
                                                 <div class="form-group">
                                                     <label for="name"><?php echo lang('app.language_admin_label_mail_category_description'); ?>:</label> <textarea class="form-control" name="category_description" rows="5" cols="40" required><?php echo (isset($categorydatas) && null !== $categorydatas[0]->category_description) ? $categorydatas[0]->category_description : ''; ?> </textarea>
								
						</div>
						
						<button type="submit" class="btn btn-primary"><?php echo lang('app.language_admin_submit'); ?></button>
							
						<?php form_close(); ?>
					</div>

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
<div class="row">
	<div class="col-xs-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-tasks fa-fw"></i><?php echo lang('app.language_admin_list_category'); ?>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive table-bordered">
                                <table class="table">
                                    <thead>
                                        <tr>
                                        	<th><?php echo lang('app.language_admin_category_name'); ?></th>
                                        	<th><?php echo lang('app.language_admin_label_mail_category_description'); ?></th>
                                        </tr>
                                    </thead>
                                        <tbody>
                                    
                                       	<?php if(isset($categorynames) && !empty($categorynames)):?>
                                       	<?php foreach ($categorynames as $categoryname): ?>
                                       	<tr>
                                       		
                                       	 	<td><?php echo  $categoryname->category_name; ?></td>
                                            <td><?php echo  $categoryname->category_description; ?></td>
      
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>	
                                       	
                                                                               
                                   
                                    </tbody>
                                 </table>
                                
                                                            </div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
</div>

<?php include 'footer.php';  ?>

