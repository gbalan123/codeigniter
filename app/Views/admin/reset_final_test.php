<?php include_once 'header.php';

use App\Libraries\Encryptinc;
$this->encryptinc = new Encryptinc();
?>
<style>
    .view-tokens{
        padding-top: 10px;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <?php echo form_open('admin/reset_final_test', array('role' => 'form bv-form', 'id' => 'search_form', 'class' => 'form-horizontal', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh", 'method' => 'get')); ?>
                    <div class="col-sm-3 col-md-4">
                        <div class="form-group">
                            <div class="col-sm-12">
                                <label for="month"><?php echo lang('app.language_school_institute_label_name'); ?><span style="color: red;">*</span></label>
                                <select class="form-control" name="institute" id="institute">
                                    <option>Please select</option>
                                    <?php foreach($school_lists as $school): ?>
                                    <option <?php echo (isset($institute_id) && $institute_id == $school->id) ? 'selected' : ''; ?> value="<?php echo $school->id; ?>"><?php echo $school->organization_name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                
                                <div>
                                
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3 col-md-2">
                        <label>&nbsp;</label>
                        <input class="btn btn-grey btn-block" type="submit" name="" value="<?php echo lang('app.language_admin_event_search'); ?>">
                    </div>
                    <div class="clearfix"></div>
                </div>
                <?php echo form_close(); ?>
				
                <?php if(!empty($is_reset_data)) { ?>
                <div id="reset_content">
                    <div class="text-left mt20">
                        <div class="row">
                            <form class="form-horizontal bv-form" action="<?php echo site_url('admin/reset_final_test'); ?>" id="search_learner_form" role="form bv-form">
                                <div class="col-sm-3 col-md-4">
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <input type="text" placeholder="<?php echo lang('app.language_admin_view_reset_final_test_holder');?>" name="search" class="form-control" id="search" value="<?php echo @$search_item; ?>">   
                                            <input type="hidden" name="h_institute" value="<?php echo $institute_id;?>" /> 
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3 col-xs-6 media_btn">
                                    <button type="submit" class="btn btn-success" ><?php echo lang('app.language_admin_institutions_search_btn'); ?></button>
                                    <button type="button" id="clearBtnResetTest" class="btn btn-default" ><?php echo 'Clear'; ?></button>
                                </div>
                                <div class="clearfix"></div>
                            </form>
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="button" class="btn btn-primary" id="resetTestButton" disabled ><?php echo lang('app.language_admin_view_reset_final_test'); ?></button>
                    </div>
                    <div class="table-responsive view-tokens">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <?php if ((!empty($reset_results)) && (sizeof($reset_results) > 1)) {?>
                                    <td align="center"><input type="checkbox"   name="checkall_reset_test" id="checkall_reset_test"  value=""  /></td>
                                    <?php } else {?>
                                    <th align="center"><input type="checkbox"  style="visibility:hidden; " name="checkall_reset_test"  /></th>
                                    <?php }?>
                                    <th><?php echo lang('app.language_admin_view_reset_final_test_name'); ?></th>
                                    <th><?php echo lang('app.language_admin_view_reset_final_test_username'); ?></th>
                                    <th><?php echo lang('app.language_learner_session_label_level'); ?></th>
                                    <th><?php echo lang('app.language_event_view_date_time'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($reset_results)){
                                        foreach($reset_results as $resetTest) {
                                            ?>
                                            <tr>
                                                <td align="center">
                                                <input type="checkbox" name="reset_test_user_third_party_ids[]" class="reset_test_user_third_party_ids"  value="<?php echo $this->encryptinc->encode($resetTest->thirdparty_id); ?>" data-bookingValue="<?php echo $this->encryptinc->encode($resetTest->booking_id); ?>" data-eventValue="<?php echo $this->encryptinc->encode($resetTest->event_id); ?>"/>

                                                </td>
                                                <td><?php echo $resetTest->name;?></td>
                                                <?php if($resetTest->order_type == 'under13') {
                                                ?>
                                                    <td><?php echo $resetTest->username?></td>
                                                <?php
                                                } else {
                                                    ?>
                                                    <td><?php echo $resetTest->email;?></td>
                                                    <?php
                                                }
                                                ?>
                                                <td><?php echo $resetTest->level;?></td>
                                                <td><?php echo $resetTest->event_date.' '.$resetTest->start_time.' - '.$resetTest->end_time?></td>
                                            </tr>
                                        <?php
                                        }
                                    } else {
                                        ?>
                                        <tr>
                                            <td colspan="5">
                                                <div class="alert alert-danger fade in">
                                                    <a href="#" class="close" data-dismiss="alert">&times;</a>
                                                    <?php echo lang('app.language_leaners_available_msg'); ?>
                                                </div>
                                            </td>
                                        </tr> 
                                        <?php
                                    }
                                    ?>
                            </tbody>                    
                        </table>
                    </div>
                    <nav class="text-right">
                    <?php if (($links)) :?>
									<?= $links->links('pagination_reset_final_test') ?>
									<?php endif ?> 
                    </nav>
				<?php 
				} 
					 
				?>
                 </div>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-6-->
</div>

<?php include 'footer.php'; ?>

<script>
    $('#institute').on('change', function(){
        $('#reset_content').hide();
    });

    // Reset Test button
    $("#checkall_reset_test").click(function () {
        if((document.getElementById('checkall_reset_test').checked)==false) {
            $('#resetTestButton').attr('disabled', 'disabled');
            $("input[name='reset_test_user_third_party_ids[]']").not(this).prop('checked', false);
        }
        else{
        $('#resetTestButton').removeAttr("disabled");
        $("input[name='reset_test_user_third_party_ids[]']").not(this).prop('checked', this.checked);
        }
    });

    $('.reset_test_user_third_party_ids').click(function() {
        $('#checkall_reset_test').attr('checked', false);
        checked_reset_teset_learner = $("input[name='reset_test_user_third_party_ids[]']:checkbox:checked").length;
        if(checked_reset_teset_learner == 0) {
            $('#resetTestButton').attr('disabled', 'disabled');
        }
        else{
            
            $('#resetTestButton').removeAttr("disabled");
        }
    }); 

    $('#resetTestButton').click(function(){
        $('#resetTestButton').attr('disabled', 'disabled');
        event.preventDefault();

        var thirdpartyIDs = [] ;
        var checkedValues = $("input[name='reset_test_user_third_party_ids[]']:checkbox:checked").map(function(){
            thirdpartyIDs.push(this.value);
        }).get();

        var obj = {};
        obj.thirdpartyIDs = thirdpartyIDs; 
        $.post("<?php echo site_url('admin/remove_incomplete_test_leaners'); ?>", obj, function (data) {
            location.reload();
        }, "json");
    });
    
    $('#clearBtnResetTest').on('click', function () {
        window.location = "<?php echo site_url('admin/reset_final_test?institute='.$institute_id); ?>";
    });
    
</script>
