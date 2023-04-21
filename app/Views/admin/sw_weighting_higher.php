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
    .speaking_title{
        padding: 10px 0;
    }
    .fa-download{
        margin-top:9px;
    }
    .mt_5{
        margin-top:5px;
    }
    .pd_20{
        padding-right:20px;
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
                        <?php echo form_open_multipart('admin/post_weighting', array('class' => 'form', 'role' => 'form', 'method' => 'post', 'id' => 'speaking_weighting_form')); ?>
                        <div class="form-group">
                            <label for="name"><?php echo lang('app.language_admin_tds_bulk_speaking'); ?> <span>*</span></label> 
                            <input type="file" class="form-control input-lg" name="speaking_weighting" id="speaking_weighting"  required />
                            <input type="hidden" name="weighting" value="speaking"/>
                             <input type="hidden" name="type" value="higher"/>
                        </div>
                        <div class="form-group">
                              <label for="name"><?php echo lang('app.language_admin_weighting_reason'); ?> <span>*</span></label> 
                              <textarea class="form-control" name="text_area_sp" id ="text_area_sp" rows="2" cols="20" required></textarea>
						</div>
                        <div class="pd_20">
                            <button  name="speaking_weighting_submit" type="submit" class="btn btn-primary"><?php echo lang('app.language_admin_upload'); ?></button>&nbsp;&nbsp;<span class="glyphicon glyphicon-alert"></span>&nbsp;<?php echo lang('app.language_admin_tds_download_csv_upload'); ?>
                        <?php echo form_close(); ?>
                            <a class="pull-right mt_5" href="<?php echo site_url('admin/download_csv/speakingweighting'); ?>" download><?php echo lang('app.language_admin_tds_download_existing_csv'); ?>
                            </a>
                            <span  class="fa fa-download pull-right">&nbsp;</span>
                        </div> 
                    </div> 
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.row -->


<div class="row">
	<div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <em class="fa fa-tasks fa-fw"></em><?php echo lang('app.language_admin_tds_speaking_weighting'); ?>
            </div>
             
            <div class="panel-body">
            <?php if(!empty($higher_current_sp_weight)){ ?>
            <div>
                <label for="name" style="margin-top:10px">Select version for speaking weight:</label>
                <select class="form-control" name="version_sp_higher" id="version_sp_higher">
                    <?php foreach($higher_sp_version_details as $s_version){ ?>
                        <option value="<?php echo $s_version->version; ?>" 
                            <?php echo ($s_version->version == $higher_current_sp_weight['version']) ? "selected" : "";?> >
                            <?php echo date('d-M-y',strtotime($s_version->date)) ." (V". $s_version->version .")"; ?>
                        </option>
                    <?php } ?>
                </select>
            </div> 
            <?php } ?> <img class="loading" style="display:none;width:25px;height:25px;margin-top:2%;margin-left:50%;" src="<?php echo base_url('public/images/loading.gif'); ?>" alt="..." />    
                <?php if(!empty($higher_current_sp_weight)){ ?>
                <div id="content_load">
                    <div class="form-group">
                        <label for="name"  style="margin-top:8px"><?php echo lang('app.language_admin_weighting_reason') . ':';?></label> <span  id="text"></span>
                    </div>
                    <div class="table-responsive table-bordered fixed-panel">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><?php echo lang('app.language_admin_tds_speaking_weighting_column1');?></th>
                                    <th><?php echo lang('app.language_admin_tds_speaking_weighting_column2');?></th>
                                </tr>
                            </thead>
                            <tbody id="higher_sp_weight_values"></tbody>
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

<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?php echo lang('app.language_admin_tds_writing_weighting'); ?></h1>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12">
                        <?php echo form_open_multipart('admin/post_weighting', array('class' => 'form', 'role' => 'form', 'method' => 'post', 'id' => 'writing_weighting_form')); ?>
                        <div class="form-group">
                            <label for="name"><?php echo lang('app.language_admin_tds_bulk_writing'); ?> <span>*</span></label> 
                            <input type="file" class="form-control input-lg" name="writing_weighting" id="writing_weighting"  required />
                            <input type="hidden" name="weighting" value="writing"/>
                            <input type="hidden" name="type" value="higher"/>
                        </div>
                        <div class="form-group">
                              <label for="name"><?php echo lang('app.language_admin_weighting_reason'); ?> <span>*</span></label> 
                              <textarea class="form-control" name="text_area_sp" id ="text_area_2" rows="2" cols="20" required></textarea>
						</div>
                        <div class="pd_20">
                            <button  name="writing_weighting_submit" type="submit" class="btn btn-primary"><?php echo lang('app.language_admin_upload'); ?></button>&nbsp;&nbsp;<span class="glyphicon glyphicon-alert"></span>&nbsp;<?php echo lang('app.language_admin_tds_download_csv_upload'); ?>
                        <?php echo form_close(); ?>
                            <a class="pull-right mt_5" href="<?php echo site_url('admin/download_csv/writingweighting'); ?>" download><?php echo lang('app.language_admin_tds_download_existing_csv'); ?></a>
                            <span class="fa fa-download pull-right">&nbsp;</span>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.row -->

<div class="row">
	<div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <em class="fa fa-tasks fa-fw"></em><?php echo lang('app.language_admin_tds_writing_weighting'); ?>
            </div>
            
            <div class="panel-body">
            <?php if(!empty($higher_current_wr_weight)){ ?>
            <div>
                <label for="name" style="margin-top:10px">Select version for writing weight:</label>
                <select class="form-control" name="version_wr_higher" id="version_wr_higher">
                    <?php foreach($higher_wr_version_details as $wr_version){ ?>
                        <option value="<?php echo $wr_version->version; ?>" 
                            <?php echo ($wr_version->version == $higher_current_wr_weight['version']) ? "selected" : "";?> >
                            <?php echo date('d-M-y',strtotime($wr_version->date)) ." (V". $wr_version->version .")"; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <?php }?> <img class="loading1" style="display:none;width:25px;height:25px;margin-top:2%;margin-left:50%;" src="<?php echo base_url('public/images/loading.gif'); ?>" alt="..." />
                <?php if(!empty($higher_current_wr_weight)){ ?>
                <div id="content_load1">
                    <div class="form-group">
                        <label for="name" style="margin-top:8px"><?php echo lang('app.language_admin_weighting_reason');?>:</label> <span  id="text2"></span>
                    </div>
                    <div class="table-responsive table-bordered fixed-panel">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><?php echo lang('app.language_admin_tds_writing_weighting_column1');?></th>
                                    <th><?php echo lang('app.language_admin_tds_writing_weighting_column2');?></th>
                                </tr>
                            </thead>
                            <tbody id="higher_wr_weight_values"></tbody>
                        </table>
                    </div>
                </div>
                <?php } else { ?>
                    <div class="alert alert-danger fade in">
                        <a href="#" class="close" data-dismiss="alert">&times;</a>
                        <?php echo lang('app.language_admin_tds_no_records'); ?>
                    </div>
                <?php }?>
            </div>
        </div>
    </div>
</div>


<?php include 'footer.php'; ?>
<script type="text/javascript">
    $(document).ready(function () {

        higher_sp_weighting("speaking");
        higher_wr_weighting("writing");  
       
        $('#version_sp_higher').on('change', function () {
            higher_sp_weighting("speaking");           
        });  

        $('#version_wr_higher').on('change', function () {
            higher_wr_weighting("writing");
        });
    });

    function higher_sp_weighting(type){
        version = $('#version_sp_higher :selected').val();
        var obj = {};
        obj.value = version;
        obj.type = type;
        obj.course = "higher";
        obj.lookup = "weighting";
        $('#content_load').hide();
        $('.loading').show();  
        $.post("<?php echo site_url('admin/sw_weighting_version'); ?>", obj, function (data) {
            if (data) {
                $('.loading').hide();
                $('#content_load').show();
                $("#higher_sp_weight_values > tr").remove();
                $('#text').text(data.notes);
                arr = JSON.stringify(data);
                var array = $.map(data.valid, function(value, index) {
                    $("#higher_sp_weight_values").append("<tr><td>"+value.qnumber+"</td><td>"+value.weight+"</td></tr>")
                });
            }
        }, "json");
    }

    function higher_wr_weighting(type){
        version = $('#version_wr_higher :selected').val();
        var obj = {};
        obj.value = version;
        obj.type = type;
        obj.course = "higher";
        obj.lookup = "weighting";
        $('#content_load1').hide();
        $('.loading1').show();
        $.post("<?php echo site_url('admin/sw_weighting_version'); ?>", obj, function (data) {
            if (data) {
                $('.loading1').hide();
                $('#content_load1').show();
                $("#higher_wr_weight_values > tr").remove();
                $('#text2').text(data.notes);
                arr = JSON.stringify(data);
                var array = $.map(data.valid, function(value, index) {
                    $("#higher_wr_weight_values").append("<tr><td>"+value.qnumber+"</td><td>"+value.weight+"</td></tr>")
                });
            }
        }, "json");

    }
    
     //WP-1224 validation for sw_weighting
		$('#speaking_weighting_form').bootstrapValidator({
			locale : "<?php echo $this->lang->lang(); ?>",
				// List of fields and their validation rules
				fields: {
				speaking_weighting: {
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
        $('#writing_weighting_form').bootstrapValidator({
			locale : "<?php echo $this->lang->lang(); ?>",
				// List of fields and their validation rules
				fields: {
                writing_weighting: {
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

