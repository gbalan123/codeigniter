<?php include_once 'header.php';
use Config\MY_Lang;
$this->lang = new \Config\MY_Lang();
?>
<style>
    .fixed-panel {
      min-height: 130px;
      max-height: 400px;
      overflow-y: auto;
    }
    .mt_5{
        margin-top:5px;
    }
    .pd_20{
        padding-right:20px;
    }
    .fa-download{
        margin-top:9px;
    }
</style>

<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12">
                        <?php
                       if($post_type == "higher"){
                           $url_post = "admin/higher_post_cefr_ability";
                       }elseif ($post_type == "core"){
                           $url_post = "admin/core_post_cefr_ability";
                       }else{
                           $url_post = "admin/post_cefr_ability";
                       }
                        ?>
                        <?php echo form_open_multipart($url_post, array('class' => 'form', 'role' => 'form', 'method' => 'post', 'id' => 'formcodes_form')); ?>
                        <div class="form-group">
                            <label for="name"><?php echo lang('app.language_admin_tds_cefr_bulk'); ?>:</label> 
                            <input type="file" class="form-control input-lg" name="cefr_ability" id="cefr_ability"  required />
                        </div>
                        <div class="form-group">
                            <label for="name"><?php echo lang('app.language_admin_weighting_reason'); ?> <span>*</span></label> 
                            <textarea class="form-control" name="text_area_sp" id ="text_area_sp" rows="2" cols="20" required></textarea>
                        </div>
                        <div class="pd_20">
                            <button  name="cefr_ability_submit" type="submit" class="btn btn-primary"><?php echo lang('app.language_admin_upload'); ?></button>&nbsp;&nbsp;<span class="glyphicon glyphicon-alert"></span>&nbsp;<?php echo lang('app.language_admin_tds_download_csv_upload'); ?>
                        <?php echo form_close(); ?>
                            <a class="pull-right mt_5" href="<?php echo site_url('admin/download_csv/cefrability'); ?>" download><?php echo lang('app.language_admin_tds_download_existing_csv'); ?></a>
                            <span class="fa fa-download pull-right">&nbsp;</span>
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

<div class="row">
	<div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-tasks fa-fw"></i><?php echo lang('app.language_admin_tds_cefr_ability'); ?>
            </div>
            <!-- /.panel-heading -->           
            <div class="panel-body">
            <?php if(!empty($cefr_ability_current)){ ?>
            <div>
                <label for="name" style="margin-top:10px">Select version for CATs Step scale:</label>
                <select class="form-control" name="version_cefr_scale" id="version_cefr_scale">
                    <?php foreach($cefr_ability_versions as $cefr_version){ ?>
                        <option value="<?php echo $cefr_version->version; ?>" 
                            <?php echo ($cefr_version->version == $cefr_ability_current['version']) ? "selected" : "";?> >
                            <?php echo date('d-M-y',strtotime($cefr_version->date)) ." (V". $cefr_version->version .")"; ?>
                        </option>
                    <?php }; ?>
                </select>
            </div>
            <?php } ?>  <center><img class="loading" style="display: none;width:25px;height:25px;margin-top:2%;" src="<?php echo base_url('public/images/loading.gif'); ?>" /></center>
                <?php if(!empty($cefr_ability_current)){ ?>
                <div id="content_load">
                    <div class="form-group">
                        <label for="name" style="margin-top:8px"><?php echo lang('app.language_admin_weighting_reason');?> :</label> <span id="text"></span>
                    </div>
                    <div class="table-responsive table-bordered fixed-panel">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><?php echo lang('app.language_admin_tds_cefr_level_column1');?></th>
                                    <th><?php echo lang('app.language_admin_tds_cefr_level_column2');?></th>
                                    <th><?php echo lang('app.language_admin_tds_cefr_level_column3');?></th>
                                </tr>
                            </thead>
                            <tbody id="cefr_level_values"></tbody>
                        </table>
                    </div>
                </div>
                <?php } else { ?>
                <div class="alert alert-danger fade in">
                    <a href="#" class="close" data-dismiss="alert">&times;</a>
                    <?php echo lang('app.language_admin_tds_no_records'); ?>
                </div>
                <?php } ?>
                <!-- /.table-responsive -->
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
	<!-- /.col-lg-6-->
</div>


<?php include 'footer.php'; ?>
<script type="text/javascript">
    $(document).ready(function () {
        cefr_scale_details("all");
        
        $('#version_cefr_scale').on('change', function () {
            cefr_scale_details("all");           
        });
    });
    function cefr_scale_details(type){
        version = $('#version_cefr_scale :selected').val();
        var obj = {};
        obj.value = version;
        obj.type = type;
        obj.course = "cefrlevel";
        obj.lookup = "scale";
        $('#content_load').hide();
        $('.loading').show();
        $.post("<?php echo site_url('admin/cefr_scale_values'); ?>", obj, function (data) {
            if (data) {
                $('.loading').hide();
                $('#content_load').show();
                $("#cefr_level_values > tr").remove();
                $('#text').text(data.notes);
                arr = JSON.stringify(data);
                var array = $.map(data.valid, function(value, index) {
                    $("#cefr_level_values").append("<tr><td>"+value.scale+"</td><td>"+value.cefr_level+"</td><td>"+value.ability_estimate+"</td></tr>")
                });
            }
        }, "json");
    }

        //WP-1224 validation for sw_weighting
		$('#formcodes_form').bootstrapValidator({
			locale : "<?php echo $this->lang->lang(); ?>",
				// List of fields and their validation rules
				fields: {
                cefr_ability: {
				validators: {
					notEmpty: {
						message: 'Please choose a file'
					}
				}
			},
			text_area_sp: {
				validators: {
                    stringLength: {		                       
                        max: 500,   
                    }
				}
			},
		
		}, onSuccess: function (e) {}			
		});
  
</script>

