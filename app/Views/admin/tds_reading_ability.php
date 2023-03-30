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
    .table{
        margin:0;
    }
</style>

<!-- /.row -->
<div class="row">
    
    <div class="col-lg-12">
        <div class="panel panel-default">

            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12">
                         <?php echo form_open_multipart('admin/post_tds_rl_ability', array('class' => 'form', 'role' => 'form', 'method' => 'post', 'id' => 'reading_ability_form')); ?>
                        <div class="form-group">                            
                            <label for="name"><?php echo lang('app.language_admin_higher_reading_dropdown'); ?>:</label>
                            <select class="form-control" name="tds_test_formid" id="tds_test_formid">
                                    	<?php foreach($tds_formids as $tds_formid){ ?>
                                <option value="<?php echo $tds_formid->id; ?>" 
                                    <?php
                                    if ($tds_formid->id == $id) {
                                        echo "selected";
                                    } else {
                                        echo "";
                                    }
                                    ?>><?php echo $tds_formid->test_formid; ?></option>
                                    	<?php }; ?>
                                	</select>
                        </div>                        
                        <div class="form-group">
                            <label for="name"><?php echo lang('app.language_admin_higher_reading_upload_heading'); ?>:</label> 
                            <input type="file" class="form-control input-lg" name="reading_ability" id="reading_ability"  required />
                            <input type="hidden" name="ability" value="reading"/>
                        </div>
                        <div class="form-group">
                            <label for="name"><?php echo lang('app.language_admin_weighting_reason'); ?> <span>*</span></label> 
                            <textarea class="form-control" name="text_area_sp" id ="text_area_sp" rows="2" cols="20" required></textarea>
                        </div>
                        <div class="pd_20">
                            <button  name="reading_ability_submit" type="submit" class="btn btn-primary"><?php echo lang('app.language_admin_upload'); ?></button>&nbsp;&nbsp;<span class="glyphicon glyphicon-alert"></span>&nbsp;<?php echo lang('app.language_admin_tds_download_csv_upload'); ?>
                        <?php echo form_close(); ?>
                            <a class="pull-right mt_5" href="<?php echo site_url('admin/download_csv/readingability') ?>" download><?php echo lang('app.language_admin_tds_download_existing_csv'); ?></a>
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
                <i class="fa fa-tasks fa-fw"></i><?php echo lang('app.language_admin_higher_reading_listing'); ?>
            </div>
            <!-- /.panel-heading -->
           
            <div class="panel-body">
            <?php if(!empty($current_reading_higher)){ ?>
            <div>
                <label for="name" style="margin-top:10px">Select version for Reading ability:</label>
                <select class="form-control" name="version_rd_higher" id="version_rd_higher">
                    <?php foreach($versions_higher_reading as $hr_rd_version){ ?>
                        <option value="<?php echo $hr_rd_version->form_id."_".$hr_rd_version->version; ?>" 
                            <?php echo ($hr_rd_version->form_id == $current_reading_higher['form_code'] && $hr_rd_version->version == $current_reading_higher['version']) ? "selected" : "";?> >
                            <?php echo $hr_rd_version->form_id." ". date('d-M-y',strtotime($hr_rd_version->date)) ." (V". $hr_rd_version->version .")"; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>            
            <?php } ?> <center><img class="loading" style="display:none;width:25px;height:25px;margin-top:2%;" src="<?php echo base_url('public/images/loading.gif'); ?>" /></center>
                <?php if(!empty($current_reading_higher)){ ?>
                <div id="content_load">
                    <div class="form-group"> 
                        <label for="name"  style="margin-top:8px"><?php echo lang('app.language_admin_weighting_reason');?>:</label> <span  id="text"></span>
                    </div>
                    <div class="table-responsive table-bordered fixed-panel">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><?php echo lang('app.language_admin_higher_reading_column1');?></th>
                                    <th><?php echo lang('app.language_admin_higher_reading_column2');?></th>
                                </tr>
                            </thead>
                            <tbody id="tds_rl_ability"></tbody>
                        </table>
                    <!-- /.table-responsive -->
                    </div>
                </div>
                <?php } else { ?>
                <div class="alert alert-danger fade in">
                    <a href="#" class="close" data-dismiss="alert">&times;</a>
                    <?php echo lang('app.language_admin_tds_no_records'); ?>
                </div>
                <?php } ?>
            <!-- /.panel-body -->
            </div>
        <!-- /.panel -->
        </div>
    <!-- /.col-lg-6-->
    </div>  
</div>


<?php include 'footer.php'; ?>
<script type="text/javascript">
    $(document).ready(function (){
        higher_re_ability("reading");
    
        $('#version_rd_higher').on('change', function () {
            higher_re_ability("reading");
        });  
    });

    function higher_re_ability(type){
        version = $('#version_rd_higher :selected').val();
        var obj = {};
        obj.value = version;
        obj.type = type;
        obj.lookup = "ability";
        obj.course = "higher";
        $('#content_load').hide();
        $('.loading').show();
        $.post("<?php echo site_url('admin/rl_ability_version'); ?>", obj, function (data) {
            if (data) {
                $('.loading').hide();
                $('#content_load').show();
                $("#tds_rl_ability > tr").remove();
                $('#text').text(data.notes);
                arr = JSON.stringify(data);
                var array = $.map(data.valid, function(value, index) {
                    $("#tds_rl_ability").append("<tr><td>"+value.score+"</td><td>"+value.ability_estimate+"</td></tr>")
                });
            }
        }, "json");
    }
   
         //WP-1224 validation for sw_weighting
		$('#reading_ability_form').bootstrapValidator({
			locale : "<?php echo $this->lang->lang(); ?>",
				// List of fields and their validation rules
				fields: {
                reading_ability: {
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

