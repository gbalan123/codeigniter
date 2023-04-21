<?php include_once 'header.php';  
?>



<div class="row">
	<div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <em class="fa fa-tasks fa-fw"></em><?= esc($admin_heading) ?>
                            <a  href="<?php echo site_url('admin/product'); ?>" class="pull-right"><em class="fa fa-upload fa-fw"></em><?php echo lang('app.language_admin_product_upload_label'); ?></a>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive table-bordered table-striped">
                                <table class="table">
                                    <thead>
                                        <tr>
                                        	
                                        	<th><?php echo lang('app.language_admin_product_id'); ?></th>
                                            <th><?php echo lang('app.language_admin_product_name'); ?></th>
                                            <th><?php echo lang('app.language_admin_product_level'); ?></th>
                                            <th><?php echo lang('app.language_admin_product_progression'); ?></th>
                                            <th><?php echo lang('app.language_admin_product_pgroup'); ?></th>
                                            <th><?php echo lang('app.language_admin_product_audience'); ?></th>
                                            <th><?php echo lang('app.language_admin_product_active'); ?></th>
                                            <th><?php echo lang('app.language_admin_product_action'); ?></th>
                                           
                                        </tr>
                                    </thead>
                                    <?php if(!empty($results)): ?>
                                    <tbody>
                                    
                                       <?php foreach($results as $result): ?>	
                                       	<tr>
                                       		
                                            <td><?php echo $result->id; ?></td>
                                            <td><?php echo $result->name; ?></td>
                                            <td><?php echo $result->level; ?></td>
                                            <td><?php echo $result->progression; ?></td>
                                            <td><?php echo $result->pgroup; ?></td>
                                            <td><?php echo $result->audience; ?></td>
                                            <?php
                                                $active_status = ((int)$result->active === 1) ? 'Yes' : 'No';  
                                                $button_attr = ($result->audience === "Primary") ? " " : "disabled";
                                                $action_link = '<div class="btn-group"><button type="button" class="btn btn-primary wpsc_button listeditBtn" style="pointer-events:none;" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#updateProductListModal"  data-value="'. $result->id .'"  '. $button_attr .'> <i class="fa fa-edit fa-fw"></i> '. lang("app.language_admin_edit") .'</button></div>';
                                            ?>
                                            <td><?php echo $active_status; ?></td>
                                            <td><?php echo $action_link; ?></td>
                                            
                                        </tr>
                                        <?php endforeach; ?>
                                       
                                   
                                    </tbody>
 <?php endif; ?>
                                </table>
                            

                            </div>
                        </div>
                    </div>
                </div>
</div>

<!-- Added to show the Edit form in model popup START-->
<div class="container">
    <div id="updateProductListModal" class="modal fade" role="dialog" >
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

<?php include 'footer.php';  ?>

<script>
$(window).load(function(){
    $('.wpsc_button').css('pointer-events','all');
    $('.wpsc_button').removeClass('disabled');
});

$(function () {
	var institutions_id;

	$('.listeditBtn').click(function (e) {
	    e.preventDefault();
	    institutions_id = $(this).attr('data-value');	    
	    $('.loading_main').show();
	    $("#updateProductListModal").on("show.bs.modal", function () {
	        $(this).find(".modal-body").load("<?php echo site_url('admin/listproducts_edit'); ?>" + '/' + institutions_id, function () {
	            $('.loading_main').hide();
	            $('.wpsc_button').css('pointer-events','all');
	            $('.wpsc_button').removeClass('disabled');
	            return false;
	        });
	    });

	});
});

</script>
