
<?php include_once 'header.php';
 $this->validation =  \Config\Services::validation();
?>
<style>
.form-alignment .form-group label {
 min-width: 130px
}
.form-group p{
        color : red;
        padding-left: 135px;
    }

    #updateaddModal .modal-dialog {
        width : 700px;
}
.placementtable{
    width: 100%;
    margin-top: 70px;
}
</style>
<!-- /.row -->
<div class="row">

    <div class="col-xs-12">
        <div class="panel panel-default">

            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12 form-alignment">
                    
                        <?php echo form_open('admin/tds_placement_configs', array('class' => 'form-horizontal', 'role' => 'form', 'id' => 'tds_placement_form')); ?>
						<?php $active_product_id = (($active_product)) ? $active_product[0]->product_id : 0 ; ?>
						<?php $active_status = (($active_product)) ? $active_product[0]->status : 0 ; ?>
                        
						
                       <div class="form-group col-xs-12">
                            <label for="name"> Choose Product <span> *</span></label>
                            <select class="form-control" name="product_id" style="width: auto; display: inline-block;">
                                <option value="">Please select</option>
                                <?php foreach ($products as $product) { ?>
                                    <option  value="<?php echo $product->id; ?>"<?php echo (isset($product_ids) &&  $product->id == $product_ids ? 'selected' : "");?>><?php echo $product->name; ?></option>
                                <?php }  ?>
                            </select>
    
                            	<?php if ($this->validation->hasError('product_id')) {
                                       echo '<p>'.$this->validation->showError('product_id').'</p>';
                                } ?>
                            <p><?php if(isset($product_id_valid)) echo $product_id_valid;?></p>
                        </div>
                        <div class="form-group col-xs-12">
                            <label for="name"> Organisation <span> *</span></label>
                            <select class="form-control" name="institution_id" style="width: auto; display: inline-block;">
                                <option value="">Please select</option>
                                <?php foreach ($institutions as $institution) { ?>
                                    <option  value="<?php echo $institution->id; ?>"<?php echo (isset($institution_ids) &&  $institution->id == $institution_ids ? 'selected' : "");?>><?php echo $institution->organization_name; ?></option>
                                <?php }  ?>
                            </select>
    
                             <?php if ($this->validation->hasError('institution_id')) {
                                        echo '<p>'.$this->validation->showError('institution_id').'</p>';
                                } ?>
                            <p><?php if(isset($institution_id_valid)) echo $institution_id_valid; ?></p>
                        </div>

                        <div class="form-group col-xs-1">
                            <input type="hidden" name="product_status" value="1"/>
                            <input type="hidden" name="reload" value="load"/>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                        <?php form_close(); ?>
                    </div>
                    
                    <div class="panel-heading" style="padding-bottom: 10px;">
                
                <div class="clearfix"></div>
            </div>

                    <div class="col-xs-12">
                        <legend >Current active product for testing purpose</legend>
                        <div class="btn-group pull-right col-12">
                    <button type="button"<?php echo (empty($active_product)) ? 'disabled' : '' ?> class="btn btn-primary" id="admin_status_change">Active/Inactive</button> 
                    <button type="button"<?php echo (empty($active_product)) ? 'disabled' : '' ?> class="btn btn-success wpsc_button"  style="pointer-events:none;"   data-toggle="modal" data-backdrop="static" data-keyboard="false"  data-target="#updateaddModal" id="editBtn">
                    <i class="fa fa-edit fa-fw"></i>
                    <?php echo lang('app.language_admin_institutions_viewedit_btn'); ?></button>
                    <button type="button"<?php echo (empty($active_product)) ? 'disabled' : '' ?> class="btn btn-danger wpsc_button"  style="pointer-events:none;"   data-toggle="modal" data-backdrop="static" data-keyboard="false" id="deleteBtn">
                    <i class="fa fa-trash fa-fw"></i>
                    <?php echo lang('app.language_admin_delete'); ?></button>
                </div>
        				<div class="table-responsive table-bordered table-striped placementtable">
        					<table class="table">
        						<thead>
        							<tr>
                                        <th></th>
        								<th><?php echo lang('app.language_admin_product_id'); ?></th>
                                        <th><?php echo lang('app.language_ministry_institution'); ?></th>
        								<th><?php echo lang('app.language_admin_product_level'); ?></th>
        								<th><?php echo lang('app.language_admin_product_name'); ?></th>
                                        <th><?php echo lang('app.language_admin_product_active_status'); ?></th>       
        							</tr>
        						</thead>
        						<tbody>
        						<?php if(($active_product) && $active_product != '') {
                                            $i = 0;
                                           foreach ($active_product as $active_products) {
                                               
                                    ?>
        							<tr>
                                        <td><input type="radio" name="institution_row_id" value="<?php echo $active_products->institution_id; ?>" <?php echo ($i == 0) ? 'checked="checked"' : ''; ?>/></td>
        								<td><?php echo $active_products->product_id; ?></td>
                                        <td><?php echo $active_products->institution_name; ?></td>
        								<td><?php echo $active_products->product_level; ?></td>
        								<td><?php echo $active_products->product_name; ?></td>
                                        <td><?php echo ($active_products->status) ? lang('app.language_admin_product_active') : 'Inactive' ?></td>
        							</tr>
        						<?php $i++; } }else{  ?>
        							<tr>
        								<td colspan="7">
                                        <div class="alert alert-danger fade in">
                                                <a href="#" class="close" data-dismiss="alert">&times;</a>
                                                No Product Found
                                                </div>
                                                </td>
        							</tr>
        						<?php } ?>
        						</tbody>
        					</table>
                            <div class="text-center">
                   
                        </div>
        				</div>
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
<div class="container">
    <div id="addupdateModal" class="modal fade" role="dialog" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content" >
                <img class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" />
                <div class="modal-header" style="border-bottom: 0px solid #e5e5e5;">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"
                        style="font-weight: bold; text-align: center;">
                </div>
                <div class="modal-body" >

                </div>
                <div class="modal-footer" style="border-top: 0px solid #e5e5e5;"></div>
            </div>
        </div>
    </div>

</div>
<div class="container">
    <div id="updateaddModal" class="modal fade" role="dialog" >
        <div class="modal-dialog modal-xs">
            <div class="modal-content" >
                <img class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" />
                <div class="modal-header" style="border-bottom: 0px solid #e5e5e5;">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"
                        style="font-weight: bold; text-align: center;">
                </div>
                <div class="modal-body" >

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
    $('#editBtn').click(function (e) {
            e.preventDefault();
            $('.loading_main').show();
            $('#addupdateModal').modal('hide'); 
            var institution_id = $("input[name='institution_row_id']:checked").val();
            $("#updateaddModal").on("show.bs.modal", function () {
                $(this).find(".modal-body").load("<?php echo site_url('admin/tds_placement_configs_edit'); ?>" + '/' + institution_id, function () {
                    $('.loading_main').hide();
                    $('.wpsc_button').css('pointer-events','all');
                    $('.wpsc_button').removeClass('disabled');
                    return false;
                });
            });
        });

    $('#updateaddModal, #addupdateModal').on('hidden.bs.modal', function () {
            location.reload();
        });
        alertify.defaults.glossary.title = "<?php echo lang('app.language_confirm_title'); ?>";
        alertify.defaults.transition = "fade";
        alertify.defaults.theme.ok = "btn btn-primary";
        alertify.defaults.theme.cancel = "btn btn-danger";
        alertify.defaults.theme.input = "form-control";
    $('#admin_status_change').click(function(){
            event.preventDefault();
            obj = {};
            var institution_id = $("input[name='institution_row_id']:checked").val();
            obj.institution_id = institution_id;
            $.post("<?php echo site_url('admin/tds_placement_institute_status'); ?>", obj, function (data) {
                if(data.success == 1){
                    if(data.status == 1){
                        var lang = '<?php echo lang('app.language_admin_placement_configs_inactive_confirm'); ?>';
                    }else{
                        var lang = '<?php echo lang('app.language_admin_placement_configs_active_confirm'); ?>'; 
                    }
                    alertify.confirm(lang,function () {
                        $.post("<?php echo site_url('admin/tds_placement_institute_status_change'); ?>", obj, function (data2) {
                            $('.loading_main').hide();
                            location.reload();
                        }, "json");
                    },
                    function () {
                    }).set('labels', {ok: '<?php echo lang('app.language_confirm_ok'); ?>', cancel: '<?php echo lang('app.language_confirm_cancel'); ?>'});
                }else{
                    $('.loading_main').hide();
                    location.reload();   
                }
            }, "json");
        });

        //delete 
	    $('#deleteBtn').on('click', function (e) {
            var institution_id = $("input[name='institution_row_id']:checked").val();
            e.preventDefault();
            $('.loading_main').show();
            $('#deleteBtn').attr('disabled', true);
            var test = '<?php echo lang('app.language_admin_placement_configs_delete'); ?>';
            alertify.confirm(test,
                function () {
                    obj = {};
                    obj.institution_id = institution_id;
                    $.post("<?php echo site_url('admin/tds_placement_institute_delete'); ?>", obj, function (data) {
                        $('.loading_main').hide();
                        $('#deleteBtn').attr('disabled', true);
                        location.reload();                               
                    }, "json");
                },
            function () {
                $('#deleteBtn').attr('disabled', false);
            }).set({title:"Confirm"}).set('labels', {ok: '<?php echo lang('app.language_confirm_ok'); ?>', cancel: '<?php echo lang('app.language_confirm_cancel'); ?>'});
        });
	
});

</script>