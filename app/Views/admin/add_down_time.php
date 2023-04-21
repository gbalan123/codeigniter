<?php include_once 'header.php';  ?>
<?php $action_url = 'admin/save_downtime';?>
<?php 
use App\Models\Admin\Cmsmodel;
$this->cmsmodel = new Cmsmodel();
?>
<!-- /.row -->
<style>
    p {
        color: red;
    }
    .error {
        color: red;
    }
    hr {
        border-top: 0px solid #eee;
    }
    .fixed-panel {
        min-height: 200px;
        max-height: 200px;
        overflow-y: scroll;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading" style="padding: bottom 10px;px;">
                <em class="fa fa-building fa-fw"></em><?php echo lang('app.language_admin_downtime_list');?>
                <div class="btn-group pull-right" style="margin-top:3px;">
                    <button type="button" class="btn btn-success wpsc_button"  style="pointer-events:none;" data-toggle="modal"  data-backdrop="static" data-keyboard="false"  data-target="#addupdateModal" id="addDownTimeBtn">
                    <em class="fa fa-plus fa-fw"></em>
                    <?php echo lang('app.language_admin_institutions_add_btn'); ?></button>
                    <button type="button" <?php echo (empty($down_time_lists)) ? 'disabled' : '' ?> class="btn btn-primary wpsc_button"  style="pointer-events:none;"   data-toggle="modal" data-backdrop="static" data-keyboard="false"  data-target="#updateaddModal" id="editDownTimeBtn">
                    <em class="fa fa-edit fa-fw"></em>
                    <?php echo lang('app.language_admin_institutions_viewedit_btn'); ?></button>
                    <button type="button" <?php echo (empty($down_time_lists)) ? 'disabled' : '' ?> class="btn btn-danger wpsc_button" id="status_change">Active/Inactive</button>
                </div>
                <div class="clearfix"></div>
            </div>
            
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="table-responsive table-bordered table-striped">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>&nbsp;</th>
                                <th><?php echo lang('app.language_admin_downtime_list_startdate_time'); ?></th>
                                <th><?php echo lang('app.language_admin_downtime_list_enddate_time'); ?></th>
                                <th><?php echo lang('app.language_admin_institutions_timezone'); ?></th>
                                <th><?php echo lang('app.language_admin_tieruser_status'); ?></th>
                            </tr>
                        </thead>
                        <?php if (!empty($down_time_lists)): $i = 0; ?>
                            <tbody>
                                <?php foreach ($down_time_lists as $time_list): ?>	
                                <tr>
                                    <td><input type="radio" name="status_id" value="<?php echo $time_list['id']; ?>" <?php echo ($i == 0) ? 'checked="checked"' : ''; ?>  /></td>
                                    <td>
                                        <?php $downtime_zone_values = @get_downtime_zone_from_utc($time_list['timezone'], $time_list['start_date_time'], $time_list['end_date_time']);
                                        echo $downtime_zone_values['downtime_start_date'] ." ". $downtime_zone_values['downtime_start_time'];?>
                                    </td>
                                    <td>
                                        <?php echo $downtime_zone_values['downtime_end_date'] ." ". $downtime_zone_values['downtime_end_time'];?>
                                    </td>
                                    <td><?php echo $time_list['timezone'] ?></td>
                                    <td><?php echo $time_list['status'] == 1 ? "Active" : "Inactive"; ?></td>
                                </tr>
                                <?php $i++; endforeach; ?>
                            </tbody>
                        <?php else: ?>
                            <tbody>
                                <td colspan="7" style="text-align:center;"><?php echo lang('app.language_admin_downtime_list_empty'); ?></td>
                            </tbody>
                        <?php endif; ?>
                    </table>
                </div>
                <!-- /.table-responsive -->
                <div class="text-right">
                    <?php if ($pager) :?>
                    <?php $pager->setPath($_SERVER['PHP_SELF']);?>
                    <?= $pager->links() ?>
                    <?php endif ?> 
                </div>
            </div>
            
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading" style="padding: bottom 10px;px;">
                <em class="fa fa-building fa-fw"></em><?php echo lang('app.language_admin_downtime_ip_list');?>
                <div class="btn-group pull-right" style="margin-top:3px;">
                    <button type="button" class="btn btn-success wpsc_button"  style="pointer-events:none;" data-toggle="modal"  data-backdrop="static" data-keyboard="false"  data-target="#addupdateModal" id="addBtn">
                    <em class="fa fa-plus fa-fw"></em>
                    <?php echo lang('app.language_admin_institutions_add_btn'); ?></button>
                    <button type="button"<?php echo (empty($ip_address_lists)) ? 'disabled' : '' ?> class="btn btn-primary wpsc_button"  style="pointer-events:none;"   data-toggle="modal" data-backdrop="static" data-keyboard="false"  data-target="#updateaddModal" id="editBtn">
                    <em class="fa fa-edit fa-fw"></em>
                    <?php echo lang('app.language_admin_institutions_viewedit_btn'); ?></button>
                    <button type="button"<?php echo (empty($ip_address_lists)) ? 'disabled' : '' ?> class="btn btn-danger wpsc_button"  style="pointer-events:none;"   data-toggle="modal" data-backdrop="static" data-keyboard="false" id="deleteBtn">
                    <em class="fa fa-trash fa-fw"></em>
                    <?php echo lang('app.language_admin_delete'); ?></button>
                </div>
                <div class="clearfix"></div>
            </div>
            
            <!-- /.panel-heading -->
            <div class="panel-body fixed-panel">
                <div class="table-responsive table-bordered table-striped">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>&nbsp;</th>
                                <th><?php echo lang('app.language_admin_downtime_ip_address');?></th>
                                <th><?php echo lang('app.language_admin_downtime_ip_name');?></th>
                            </tr>
                        </thead>
                        <?php if (!empty($ip_address_lists)): $i = 0; ?>
                            <tbody>
                                <?php foreach ($ip_address_lists as $ip_list): ?>	
                                <tr>
                                    <td><input type="radio" name="ip_id" value="<?php echo $ip_list['id']; ?>" <?php echo ($i == 0) ? 'checked="checked"' : ''; ?>  /></td>
                                    <td><?php echo $ip_list['ip_address'] ?></td>
                                    <td><?php echo $ip_list['ip_name'] ?></td>
                                </tr>
                                <?php $i++; endforeach; ?>
                            </tbody>
                        <?php else: ?>
                            <tbody>
                                <td colspan="7" style="text-align:center;"><?php echo lang('app.language_admin_downtime_no_ip_list'); ?></td>
                            </tbody>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div id="addupdateModal" class="modal fade" role="dialog" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content" >
                <img class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" alt="..." />
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content" >
                <img class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" alt="..." />
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
<?php include 'footer.php';  ?>

<script type="text/javascript">
    $(window).load(function(){
        $('.wpsc_button').css('pointer-events','all');
        $('.wpsc_button').removeClass('disabled');
    });
    $(function () {
        $('#addDownTimeBtn').click(function (e) {
            $('.loading_main').show();
            $('#updateaddModal').modal('hide'); 
            $("#addupdateModal").on("shown.bs.modal", function (e) {
                $(this).find(".modal-body").load("<?php echo site_url('admin/down_time_popup'); ?>", function () {
                    $('.loading_main').hide();
                    $('.wpsc_button').css('pointer-events','all');
                    $('.wpsc_button').removeClass('disabled');
                    return false;
                });
            });
        }); 

        $('#editDownTimeBtn').click(function (e) {
            $('.loading_main').show();
            $('#addupdateModal').modal('hide'); 
            var down_time_id = $("input[name='status_id']:checked").val();
            $("#updateaddModal").on("shown.bs.modal", function (e) {
                $(this).find(".modal-body").load("<?php echo site_url('admin/down_time_popup'); ?>" + '/' + down_time_id, function () {
                    $('.loading_main').hide();
                    $('.wpsc_button').css('pointer-events','all');
                    $('.wpsc_button').removeClass('disabled');
                    return false;
                });
            });
        }); 
        //add
        $('#addBtn').click(function (e) {
            $('.loading_main').show();
            $('#updateaddModal').modal('hide'); 
            $("#addupdateModal").on("shown.bs.modal", function (e) {
                $(this).find(".modal-body").load("<?php echo site_url('admin/ip_add_edit'); ?>", function () {
                    $('.loading_main').hide();
                    $('.wpsc_button').css('pointer-events','all');
                    $('.wpsc_button').removeClass('disabled');
                    return false;
                });
            });
        });

        //edit
        $('#editBtn').click(function (e) {
            e.preventDefault();
            $('.loading_main').show();
            $('#addupdateModal').modal('hide'); 
            var ip_id = $("input[name='ip_id']:checked").val();
            $("#updateaddModal").on("show.bs.modal", function () {
                $(this).find(".modal-body").load("<?php echo site_url('admin/ip_add_edit'); ?>" + '/' + ip_id, function () {
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

	    $('#deleteBtn').on('click', function (e) {
            var ip_id = $("input[name='ip_id']:checked").val();
            e.preventDefault();
            $('.loading_main').show();
            $('#deleteBtn').attr('disabled', true);
            var test = '<?php echo lang('app.language_admin_downtime_ip_delete'); ?>';
            alertify.confirm(test,
                function () {
                    obj = {};
                    obj.ip_id = ip_id;
                    $.post("<?php echo site_url('admin/delete_ip_address'); ?>", obj, function (data) {
                        $('.loading_main').hide();
                        $('#deleteBtn').attr('disabled', true);
                        location.reload();                               
                    }, "json");
                },
            function () {
                $('#deleteBtn').attr('disabled', false);
            }).set({title:"Confirm"}).set('labels', {ok: '<?php echo lang('app.language_confirm_ok'); ?>', cancel: '<?php echo lang('app.language_confirm_cancel'); ?>'});
        });
      
        $(document).on('hidden.bs.modal', function (e) {
            var target = $(e.target);
            target.removeData('bs.modal')
            .find(".modal-body").html('');
        });
    });

    alertify.defaults.glossary.title = "<?php echo lang('app.language_confirm_title'); ?>";
        alertify.defaults.transition = "fade";
        alertify.defaults.theme.ok = "btn btn-primary";
        alertify.defaults.theme.cancel = "btn btn-danger";
        alertify.defaults.theme.input = "form-control";
        $('#status_change').click(function(){
            event.preventDefault();
            obj = {};
            status_id = $("input[name='status_id']:radio:checked").val();
            obj.status_id = status_id;
            $.post("<?php echo site_url('admin/status_changes'); ?>", obj, function (data) {
              
                if(data.success == 1){
                    if(data.status == 1){
                       
                        var lang = '<?php echo lang('app.language_admin_downtime_list_inactive'); ?>';
                    }else{
                        var lang = '<?php echo lang('app.language_admin_downtime_list_active'); ?>'; 
                    }
                    alertify.confirm(lang,function () {
                        $.post("<?php echo site_url('admin/status_update'); ?>", obj, function (data2) {
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

        $('#downtime_check').on('change', function(){
            var downtime_check;
            if($("#downtime_check").prop('checked') == true){
                downtime_check = 1;
            } else {
                downtime_check = 0;
            }
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('admin/downtime_check'); ?>",
                data: {downtime_check: downtime_check},
                dataType: 'json',
                success: function (data)
                {
                    
                }
            });
        });
</script>