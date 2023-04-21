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
                        <?php
                       if($post_type == "higher"){
                           $url_post = "admin/higher_post_ability";
                       }else{
                           $url_post = "admin/post_ability";
                       }
                        ?>
                        <?php echo form_open_multipart("admin/post_ability", array('class' => 'form', 'role' => 'form', 'method' => 'post', 'id' => 'speaking_ability_form')); ?>
                        <div class="form-group">
                            <label for="name"><?php echo lang('app.language_admin_tds_bulk_speaking_ability'); ?><span>*</span></label> 
                            <input type="file" class="form-control input-lg" name="speaking_ability" id="speaking_ability"  required />
                            <input type="hidden" name="ability" value="speaking"/>
                            <input type="hidden" name="type" value="higher"/>
                        </div>
                        <div class="form-group">
                              <label for="name"><?php echo lang('app.language_admin_weighting_reason'); ?> <span>*</span></label> 
                              <textarea class="form-control" name="text_area_sp" id ="text_area_sp" rows="2" cols="20" required></textarea>
							</div>
                        <div class="pd_20">
                            <button  name="speaking_ability_submit" type="submit" class="btn btn-primary"><?php echo lang('app.language_admin_upload'); ?></button>&nbsp;&nbsp;<span class="glyphicon glyphicon-alert"></span>&nbsp;<?php echo lang('app.language_admin_tds_download_csv_upload'); ?>
                        <?php echo form_close(); ?>
                            <a class="pull-right mt_5" href="<?php echo site_url('admin/download_csv/speakingability'); ?>" download><?php echo lang('app.language_admin_tds_download_existing_csv'); ?></a>
                            <span class="fa fa-download pull-right">&nbsp;</span>
                        </div>
                        
                    </div>
                   
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
	<div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <em class="fa fa-tasks fa-fw"></em><?php echo lang('app.language_admin_tds_speaking_ability'); ?>
            </div>
           
            <div class="panel-body">
            <?php if(!empty($hr_speaking_ability_current)){ ?>
            <div>
                <label for="name" style="margin-top:10px">Select version for Speaking ability:</label> 
                <select class="form-control" name="version_sp_hr_ability" id="version_sp_hr_ability">
                    <?php foreach($hr_speaking_ability_version_details as $sp_ab_version){ ?>
                        <option value="<?php echo $sp_ab_version->version; ?>" 
                            <?php echo ($sp_ab_version->version == $hr_speaking_ability_current['version']) ? "selected" : "";?> >
                            <?php echo date('d-M-y',strtotime($sp_ab_version->date)) ." (V". $sp_ab_version->version .")"; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <?php } ?> <img class="loading" style="display:none;width:25px;height:25px;margin-top:2%;margin-left:50%;" src="<?php echo base_url('public/images/loading.gif'); ?>" alt="..." />
                <?php if(!empty($hr_speaking_ability_current)){ ?>
                <div id="content_load">
                    <div class="form-group">
                        <label for="name" style="margin-top:8px"><?php echo lang('app.language_admin_weighting_reason');?>:</label> <span  id="text"></span>
                    </div>
                    <div class="table-responsive table-bordered fixed-panel">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><?php echo lang('app.language_admin_tds_speaking_ability_column1');?></th>
                                    <th><?php echo lang('app.language_admin_tds_speaking_ability_column2');?></th>
                                </tr>
                            </thead>
                            <tbody id="sp_ab_higher_values"></tbody>
                        </table>
                    </div>
                </div>
                <?php } else { ?>
                <div class="alert alert-danger fade in">
                    <a href="#" class="close" data-dismiss="alert">&times;</a>
                    <?php echo lang('app.language_admin_tds_no_records'); ?>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>


<?php include 'footer.php'; ?>
<script type="text/javascript">
    $(document).ready(function () {
        
        higher_sp_ability("speaking");        

        $('#version_sp_hr_ability').on('change', function () {
            higher_sp_ability("speaking");            
        }); 

    });
    function higher_sp_ability(type){
        version = $('#version_sp_hr_ability :selected').val();
        var obj = {};
        obj.value = version;
        obj.type = type;
        obj.course = "higher";
        obj.lookup = "ability";
        $('#content_load').hide();
        $('.loading').show();
        $.post("<?php echo site_url('admin/sw_ability_version'); ?>", obj, function (data) {
            if (data) {
                $('.loading').hide();
                $('#content_load').show();
                $("#sp_ab_higher_values > tr").remove();
                $('#text').text(data.notes);
                arr = JSON.stringify(data);
                var array = $.map(data.valid, function(value, index) {
                    $("#sp_ab_higher_values").append("<tr><td>"+value.adjusted_score+"</td><td>"+value.ability_estimate	+"</td></tr>")
                });
            }
        }, "json");
    } 
    //WP-1224 validation for sw_weighting
		$('#speaking_ability_form').bootstrapValidator({
			locale : "<?php echo $this->lang->lang(); ?>",
				// List of fields and their validation rules
				fields: {
                speaking_ability: {
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
		
		}, onSuccess: function (e) {
		
		}			
		});
</script>

