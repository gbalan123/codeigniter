<?php include_once 'header.php'; ?>
<!-- /.row -->
<div class="row">
    <div class="col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading" style="overflow:hidden">
				<div class="pull-left">
                    <?= esc($admin_heading) ?>
				</div>
                <div class="pull-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-success wpsc_button" style="pointer-events: all;" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#addPrimaryPlacementModal" id="addPrimaryPlacement"><i class="fa fa-plus fa-fw"></i>Add New</button>
                    </div>
            	</div>
            </div>
            <div class="panel-body">
                <?php if(!empty($primary_placement_current_version)){ ?>
                    <div>
                        <label for="name">Select version:</label>
                        <select class="form-control" name="primary_placement_settings" id="primary_placement_settings">
                            <?php foreach($primary_placement_all_versions as $primary_placement_version){ ?>
                                <option value="<?php echo $primary_placement_version->version; ?>" 
                                    <?php echo ($primary_placement_version->version == $primary_placement_current_version['version']) ? "selected" : "";?> >
                                    <?php echo date('d-M-y',strtotime($primary_placement_version->date)) ." (V". $primary_placement_version->version .")"; ?>
                                </option>
                            <?php }; ?>
                        </select>
                    </div>
                <?php } ?>
                <div class="row">
                    <div class="form-group col-xs-12" style="margin-top: 10px;">
                        <label for="name"><?php echo lang('app.language_admin_result_display_reason'); ?>:</label> 
                        <span id="message"><?php echo $settings['message']; ?></span>
                    </div>
                    <div class="col-xs-12">
                            <?php @$logit_values = unserialize($settings['logit_values']); 
                            ?>
                            <div class="col-xs-12 col-sm-4">
                                <div class="form-group">
                                    <label for="A0.1" class="">Primary Step Into 1</label>
                                    <div class="">
                                        <input type="number" class="form-control attr-disable" name="A0_1" id="A0_1" placeholder="A0.1" value="<?php echo @$logit_values['A0_1']; ?>" <?php echo ($products_primary[0]->active == 0 ) ? 'disabled' : '';?> >
                                    </div>
                                    <label for="A0.2" class="">Primary Step Into 2</label>
                                    <div class="">
                                        <input type="number" class="form-control attr-disable" name="A0_2" id="A0_2" placeholder="A0.2" value="<?php echo @$logit_values['A0_2']; ?>" <?php echo ($products_primary[1]->active == 0 ) ? 'disabled' : '';?>  >
                                    </div>
                                    <label for="A0.3" class="">Primary Step Into 3</label>
                                    <div class="">
                                        <input type="number" class="form-control attr-disable" name="A0_3" id="A0_3" placeholder="A0.3" value="<?php echo @$logit_values['A0_3']; ?>" <?php echo ($products_primary[2]->active == 0 ) ? 'disabled' : '';?> >
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-4">
                                <div class="form-group">
                                    <label for="A1.1" class="">Primary Step Forward 1</label>
                                    <div class="">
                                        <input type="number" class="form-control attr-disable" name="A1_1" id="A1_1" placeholder="A1.1" value="<?php echo @$logit_values['A1_1']; ?>" <?php echo ($products_primary[3]->active == 0 ) ? 'disabled' : '';?> >
                                    </div>
                                    <label for="A1.2" class="">Primary Step Forward 2</label>
                                    <div class="">
                                        <input type="number" class="form-control attr-disable" name="A1_2" id="A1_2" placeholder="A1.2" value="<?php echo @$logit_values['A1_2']; ?>" <?php echo ($products_primary[4]->active == 0 ) ? 'disabled' : '';?> >
                                    </div>
                                    <label for="A1.3" class="">Primary Step Forward 3</label>
                                    <div class="">
                                        <input type="number" class="form-control attr-disable" name="A1_3" id="A1_3" placeholder="A1.3" value="<?php echo @$logit_values['A1_3']; ?>" <?php echo ($products_primary[5]->active == 0 ) ? 'disabled' : '';?> >
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-4">
                                <div class="form-group">
                                    <label for="A2.1" class="">Primary Step Up 1</label>
                                    <div class="">
                                        <input type="number" class="form-control attr-disable" name="A2_1" id="A2_1" placeholder="A2.1" value="<?php echo @$logit_values['A2_1']; ?>" <?php echo ($products_primary[6]->active == 0 ) ? 'disabled' : '';?> >
                                    </div>
                                    <label for="A2.2" class="">Primary Step Up 2</label>
                                    <div class="">
                                        <input type="number" class="form-control attr-disable" name="A2_2" id="A2_2" placeholder="A2.2" value="<?php echo @$logit_values['A2_2']; ?>" <?php echo ($products_primary[7]->active == 0 ) ? 'disabled' : '';?> >
                                    </div>
                                    <label for="A2.3" class="">Primary Step Up 3</label>
                                    <div class="">
                                        <input type="number" class="form-control attr-disable" name="A2_3" id="A2_3" placeholder="A2.3" value="<?php echo @$logit_values['A2_3']; ?>" <?php echo ($products_primary[8]->active == 0 ) ? 'disabled' : '';?>>
                                    </div>
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
    <div id="addPrimaryPlacementModal" class="modal fade" role="dialog" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content" >
                <img class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" />
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

<script type="text/javascript">
    $('#addPrimaryPlacement').click(function (e) {
	    e.preventDefault();
	    $('.loading_main').show();
	    $("#addPrimaryPlacementModal").on("show.bs.modal", function () {
	        $(this).find(".modal-body").load("<?php echo site_url('admin/add_primary_placement_configs'); ?>", function () {
	            $('.loading_main').hide();
	            return false;
	        });
	    });

	});

    $(document).ready(function(){
        $('.attr-disable').attr('disabled', true);
    });
    $('#primary_placement_settings').on('change', function () {
        primary_placement_details();
    });
    function primary_placement_details(){
        version = $('#primary_placement_settings :selected').val();
        var obj = {};
        obj.value = version;
        $.post("<?php echo site_url('admin/get_primary_placement_configs_details'); ?>", obj, function (data) {
            if (data) {
                var array = $.map(data.data, function(value, index) {
                    $('#'+index).val(value);
                    if(index == 'logit_values') {
                        var logit_values_array = $.map(value, function(l_value, l_index) {
                            $('#'+l_index).val(l_value);
                        });
                    }
                    if(index == 'message') {
                        $('#'+index).text(value); 
                    }
                });
            }
        }, "json");
    }
</script>