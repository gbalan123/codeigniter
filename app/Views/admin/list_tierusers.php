<?php include_once 'header.php'; ?>
<style>
    hr{
        border-top: 0px solid #eee;
    }
    .clearable{
        background: #fff url(https://i.stack.imgur.com/mJotv.gif) no-repeat right -10px center;
        border: 1px solid #999;
        padding: 3px 18px 3px 4px;     /* Use the same right padding (18) in jQ! */
        border-radius: 3px;
        transition: background 0.4s;
    }
    .clearable.x  { background-position: right 5px center; } /* (jQ) Show icon */
    .clearable.onX{ cursor: pointer; }              /* (jQ) hover cursor style */
    .clearable::-ms-clear {display: none; width:0; height:0;} /* Remove IE default X */
    
    .switch {
  position: relative;
  display: inline-block;
  width: 35px;
  height: 22px;
  vertical-align: middle;
}
.switch + .text-center{
  font-size:20px;
  margin-left:5px;

}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 15px;
  width: 15px;
  left: 3px;
  bottom: 3.5px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(22px);
  -ms-transform: translateX(22px);
  transform: translateX(22px);
  left: -4px;
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}

.active_label{
    font-size: 15px;
}
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading" style="padding-bottom:25px;">
                <em class="fa fa-building fa-fw"></em><?= esc($admin_heading) ?>
				<a  href="<?php echo site_url('admin/institutions'); ?>" class="pull-right"><em class="fa fa-tasks fa-fw"></em><?php echo lang('app.language_admin_institutions_heading'); ?></a>
                <form class="form-inline" action="<?php echo site_url('admin/list_tierusers').'/'.$tier_id; ?>" style="margin-top:20px;" id="searchForm" >
                    <div class="form-group" style="width:50%;">

                        <input maxlength="50" style="width:100%;" type="text" placeholder="<?php echo lang('app.language_admin_institutions_tieruser_enter_search_term'); ?>" name="search" class="form-control clearable search" id="search" value="<?php echo @$search_item; ?>">
                    </div>
                    <button type="submit" class="btn btn-default" ><?php echo lang('app.language_admin_institutions_search_btn'); ?></button>
                    <button type="button" id="clearBtn" class="btn btn-default" ><?php echo 'Clear'; ?></button>
                      
                    <div class="btn-group pull-right" style="margin-top:3px;">
                        <button type="button" class="btn btn-success wpsc_button"  style="pointer-events:all;" data-toggle="modal"  data-backdrop="static" data-keyboard="false"  data-target="#addupdateModal" id="addBtn">
                            <em class="fa fa-plus fa-fw"></em>
                            <?php echo lang('app.language_admin_institutions_add_btn'); ?></button>
                        <button type="button"

                                <?php echo (empty($results)) ? 'disabled' : '' ?>
                                class="btn btn-primary wpsc_button"  style="pointer-events:all;"   data-toggle="modal" data-backdrop="static" data-keyboard="false"  data-target="#updateaddModal" id="editBtn">
                            <em class="fa fa-edit fa-fw"></em>



                            <?php echo lang('app.language_admin_institutions_viewedit_btn'); ?></button>
                            <button type="button" class="btn btn-primary" id="admin_status_change">Active/Inactive</button>
                    </div><br><br>
                    <label class="switch">
                 
                            <input type="checkbox"  id="customRadio" value="<?php echo 'active';?>" <?php echo $checked; ?>>
                           <span class="slider round"></span>
                      </label>
                      <span class="text-center"><?php echo lang('app.language_admin_institutions_manage users');?></span>
                    <div class="clearfix"></div>
                </form>

            </div>

            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="table-responsive table-bordered table-striped">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>&nbsp;</th>
                                <th><?php echo lang('app.language_admin_institutions_name'); ?></th>
                                <th><?php echo lang('app.language_admin_email'); ?></th>
                                <th><?php echo lang('app.language_admin_tieruser_department'); ?></th>
                                <th><?php echo lang('app.language_admin_tieruser_status'); ?></th>
                                <th><?php echo lang('app.language_school_teacher_label_date_logged'); ?></th>
                            </tr>
                        </thead>
						<?php 
							if(!empty($results)) { 
								$i = 0;
								foreach ($results as $result){
						?>
						<tbody>
							<tr>
								<td><input type="radio" name="tieruser_id"  value="<?php echo $result->user_id; ?>" <?php echo ($i == 0) ? 'checked="checked"' : ''; ?> /></td>
								<td><?php echo $result->firstname.' '.$result->lastname; ?></td>
								<td><?php echo $result->email; ?></td>
								<td><?php echo $result->department; ?></td>
                                <td><?php echo $result->status == 1 ? "Active" : "Inactive"; ?></td>
								<td>
								<?php 
									if($result->last_logged > 0){
										echo date('d-m-Y', $result->last_logged);
									} else {
										echo '-';
									}
								?>
								</td>
							</tr>
						</tbody>
						    <?php $i++;
                                } 
                                } 
                                else { 
                            ?>
                            <tbody>
								<td colspan="7" style="text-align:center;"><?php echo lang('app.language_admin_no_tier_user_available_msg'); ?></td>
                            </tbody>
						<?php } ?>
                    </table>
                </div>
                <div class="text-center">
                    	<?php if ($pagination) :?>
                        <?php $check = $pagination->setPath($_SERVER['PHP_SELF']);?>
                        <?= $pagination->links() ?>
                        <?php endif ?> 
                </div>
            </div>
        </div>
    </div>
</div>


<div class="container">
    <div id="addupdateModal" class="modal fade" role="dialog" >
        <div class="modal-dialog modal-md">
            <div class="modal-content" >
                <img class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" alt="..." />
                <div class="modal-header" style="border-bottom: 0px solid #e5e5e5;">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"
                        style="font-weight: bold; text-align: center;">
                    </h4>
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
        <div class="modal-dialog modal-md">
            <div class="modal-content" >
                <img class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" alt="..." />
                <div class="modal-header" style="border-bottom: 0px solid #e5e5e5;">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"
                        style="font-weight: bold; text-align: center;">
                    </h4>
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
        $('.wpsc_button').removeClass('disabled');
    });
    $(function () {
        var tieruserid;

        tieruserid = $("input[name='tieruser_id']:checked").val();

        $('input[type=radio][name=tieruser_id]').change(function () {
            if (this.value != '') {
                tieruserid = this.value;
            } else {
                tieruserid = $("input[name='tieruser_id']:checked").val();
            }
            return tieruserid;
        });

        //add
        $('#addBtn').click(function (e) {
            $('.loading_main').show();
            $('#updateaddModal').modal('hide'); 
            $("#addupdateModal").on("shown.bs.modal", function (e) {
                $(this).find(".modal-body").load("<?php echo site_url('admin/tieruser?tierid='.$tier_id); ?>", function () {
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
            $("#updateaddModal").on("show.bs.modal", function () {
                $(this).find(".modal-body").load("<?php echo site_url('admin/tieruser?tierid='.$tier_id.'&tieruserid='); ?>"+tieruserid, function () {
                    $('.loading_main').hide();
                    $('.wpsc_button').css('pointer-events','all');
                    $('.wpsc_button').removeClass('disabled');
                    return false;
                });
            });
        });

        $('#customRadio').on('click', function(){
         if ($(this).attr('id') == "customRadio") {
             if(this.checked){
                 status = $(this).val();
             }else{
                 status = 'inactive';
             }
            var obj = {};
            obj.status = status;
                $.post("<?php echo site_url('admin/list_tierusers/'.$tier_id); ?>", obj, function (data) {
                      window.location.href = data.url;
                 }, "json");
         }
     });

     $('#clearBtn').on('click', function(){
             window.location = "<?php echo site_url('admin/list_tierusers/'.$tier_id); ?>";
        });
        
        
		
        $(document).on('hidden.bs.modal', function (e) {
            var target = $(e.target);
            target.removeData('bs.modal')
            .find(".modal-body").html('');
        });

        //WP-1350 - Disable institution admin accounts - starts
        alertify.defaults.glossary.title = "<?php echo lang('app.language_confirm_title'); ?>";
        alertify.defaults.transition = "fade";
        alertify.defaults.theme.ok = "btn btn-primary";
        alertify.defaults.theme.cancel = "btn btn-danger";
        alertify.defaults.theme.input = "form-control";
        $('#admin_status_change').click(function(){
            event.preventDefault();
            obj = {};
            tier_user_admin_id = $("input[name='tieruser_id']:radio:checked").val();
            obj.tier_user_admin_id = tier_user_admin_id;
            $.post("<?php echo site_url('admin/institute_admin_lang_status'); ?>", obj, function (data) {
                if(data.success == 1){
                    if(data.status == 1){
                        var lang = '<?php echo lang('app.language_admin_list_tier_users_inactive_confirm'); ?>';
                    }else{
                        var lang = '<?php echo lang('app.language_admin_list_tier_users_active_confirm'); ?>'; 
                    }
                    alertify.confirm(lang,function () {
                        $.post("<?php echo site_url('admin/institute_admin_condition_change'); ?>", obj, function (data2) {
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
        //WP-1350 - Ends
        
    });
</script>
