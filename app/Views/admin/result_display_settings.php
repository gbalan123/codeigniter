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
                        <button type="button" class="btn btn-success wpsc_button" style="pointer-events: all;" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#addResultSettingsModal" id="addResultSettings"><em class="fa fa-plus fa-fw"></em>Add New</button>
                    </div>
            	</div>
            </div>
            <div class="panel-body">
                <?php if(!empty($result_dispaly_current_version)){ ?>
                    <div>
                        <label for="name">Select version:</label>
                        <select class="form-control" name="version_result_dispaly" id="version_result_dispaly">
                            <?php foreach($result_dispaly_all_versions as $rds_version){ ?>
                                <option value="<?php echo $rds_version->version; ?>" 
                                    <?php echo ($rds_version->version == $result_dispaly_current_version['version']) ? "selected" : "";?> >
                                    <?php echo date('d-M-y',strtotime($rds_version->date)) ." (V". $rds_version->version .")"; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                <?php } ?>
                
                <div class="row" style="margin-top: 10px;">
                    <div class="form-group col-xs-12">
                        <label for="name"><?php echo lang('app.language_admin_result_display_reason'); ?>:</label> 
                        <span id="message"><?php echo $settings['message']; ?></span>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label for="name">A(lower threshold) &lt; or =:</label>
                            <input type="text" class="form-control attr-disable" name="lower_threshold" id="lower_threshold" value="<?php echo $settings['lower_threshold']; ?>" >
                        </div>

                        <div class="form-group">
                            <label for="name">B(passing threshold) &gt; or =:</label> 
							<input type="text" class="form-control attr-disable" name="passing_threshold" id="passing_threshold" value="<?php echo $settings['passing_threshold']; ?>" >
                        </div>
                        <legend>Base score levels</legend>
                        <?php $logit_values = unserialize($settings['logit_values']); ?>
                        <div class="col-xs-12 col-sm-6 col-lg-3">
                            <div class="form-group">
                                <label for="A1.1" class="">A1.1</label>
                                <div class="">
                                    <input type="text" class="form-control attr-disable" id="A1_1" name="logit_values[]" placeholder="A1.1" value="<?php echo $logit_values['A1.1']; ?>" required>
                                </div>
                                <label for="A1.2" class="">A1.2</label>
                                <div class="">
                                    <input type="text" class="form-control attr-disable" id="A1_2" name="logit_values[]" placeholder="A1.2" value="<?php echo $logit_values['A1.2']; ?>" required>
                                </div>
                                <label for="A1.3" class="">A1.3</label>
                                <div class="">
                                    <input type="text" class="form-control attr-disable" id="A1_3" name="logit_values[]" placeholder="A1.3" value="<?php echo $logit_values['A1.3']; ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-lg-3">
                            <div class="form-group">
                                <label for="A2.1" class="">A2.1</label>
                                <div class="">
                                    <input type="text" class="form-control attr-disable" id="A2_1" name="logit_values[]" placeholder="A2.1" value="<?php echo $logit_values['A2.1']; ?>" required>
                                </div>
                                <label for="A2.2" class="">A2.2</label>
                                <div class="">
                                    <input type="text" class="form-control attr-disable" id="A2_2" name="logit_values[]" placeholder="A2.2" value="<?php echo $logit_values['A2.2']; ?>" required>
                                </div>
                                <label for="A2.3" class="">A2.3</label>
                                <div class="">
                                    <input type="text" class="form-control attr-disable" id="A2_3" name="logit_values[]" placeholder="A2.3" value="<?php echo $logit_values['A2.3']; ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-lg-3">
                            <div class="form-group">
                                <label for="B1.1" class="">B1.1</label>
                                <div class="">
                                    <input type="text" class="form-control attr-disable" id="B1_1" name="logit_values[]" placeholder="B1.1" value="<?php echo $logit_values['B1.1']; ?>" required>
                                </div>
                                <label for="B1.2" class="">B1.2</label>
                                <div class="">
                                    <input type="text" class="form-control attr-disable" id="B1_2" name="logit_values[]" placeholder="B1.2" value="<?php echo $logit_values['B1.2']; ?>" required>
                                </div>
                                <label for="B1.3" class="">B1.3</label>
                                <div class="">
                                    <input type="text" class="form-control attr-disable" id="B1_3" name="logit_values[]" placeholder="B1.3" value="<?php echo $logit_values['B1.3']; ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-lg-3">
                            <div class="form-group">
                                <label for="B1.1" class="">B2.1</label>
                                <div class="">
                                    <input type="text" class="form-control attr-disable" id="B2_1" name="logit_values[]" placeholder="B1.1" value="<?php echo $logit_values['B2.1']; ?>" required>
                                </div>
                                <label for="B1.2" class="">B2.2</label>
                                <div class="">
                                    <input type="text" class="form-control attr-disable" id="B2_2" name="logit_values[]" placeholder="B1.2" value="<?php echo $logit_values['B2.2']; ?>" required>
                                </div>
                                <label for="B1.3" class="">B2.3</label>
                                <div class="">
                                    <input type="text" class="form-control attr-disable" id="B2_3" name="logit_values[]" placeholder="B1.3" value="<?php echo $logit_values['B2.3']; ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.row -->
<div class="container">
    <div id="addResultSettingsModal" class="modal fade" role="dialog" >
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
<script type="text/javascript">
    $('#addResultSettings').click(function (e) {
	    e.preventDefault();
	    $('.loading_main').show();
	    $("#addResultSettingsModal").on("show.bs.modal", function () {
	        $(this).find(".modal-body").load("<?php echo site_url('admin/add_result_display_settings'); ?>", function () {
	            $('.loading_main').hide();
	            return false;
	        });
	    });

	});

    $(document).ready(function(){
        $('.attr-disable').attr('disabled', true);
    });
    $('#version_result_dispaly').on('change', function () {
        result_display_details();
    });
    function result_display_details(){
        version = $('#version_result_dispaly :selected').val();
        var obj = {};
        obj.value = version;
        $.post("<?php echo site_url('admin/get_result_display_settings_details'); ?>", obj, function (data) {
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