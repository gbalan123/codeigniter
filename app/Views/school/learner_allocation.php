<?php

$this->encrypter = \Config\Services::encrypter();
$this->request = \Config\Services::request();

$event_details = $session_details['detail'];
$event_products = $session_details['products'];
$event_capacity = $session_details['detail']->fixed_capacity;
$learner_allocated = $session_details['allocated'];
$allocation_available = $event_capacity - $learner_allocated;
if ($allocation_available > 0) {
    $btn_enable = '';
} else {
    $btn_enable = 'disabled';
}
$filter_products = $filter_teacher = array();
$filter_ppm = '';
if (isset($filter_items_data)) {
    $enablemanual = 'checked';
    $enableauto = '';
    if (array_key_exists("level", $filter_items_data)) {
        $filter_products = $filter_items_data['level'];
    }
    if (array_key_exists("teacher", $filter_items_data)) {
        $filter_teacher = $filter_items_data['teacher'];
    }
    if (array_key_exists("ppm", $filter_items_data)) {
        $filter_ppm = $filter_items_data['ppm'];
    }
} elseif ($learner_search_item) {
    $enablemanual = 'checked';
    $enableauto = '';
} else {
    if (null !== $this->session->getFlashdata('enable_manual')) {
        $enablemanual = 'checked';
        $enableauto = '';
    } else {
        $enablemanual = '';
        $enableauto = 'checked';
    }
}
$eventtimediff = $event_details->start_date_time - $current_utc_timestamp;
$allocate_disable = 0;
if ($eventtimediff > 1800) {
    $allocate_disable = 0;
} else {
    $allocate_disable = 1;
}
$unallocate_disable = ($event_details->start_date_time < $current_utc_timestamp) ? 1 : 0; //Disable unallocate button when event starts
?>
<style>
    #filter_form button.multiselect{
        height: 30px !important;
    }
    button.multiselect .caret{
        top: 8px;
        border-top: 6px dashed;
    }
    #learner_allocation_export{
        margin-bottom: 15px;
    }
    #manual_allocate {
        font-size: 14px;
    }
    #filter_form  .ppm{
        padding: 6px 0px 3px 12px;
    }
    .multiselect-selected-text{
        padding: 0;
    }
</style>
<div class="bg-lightgrey learner_allocation">
    <div class="container">
        <div class="header_block">
            <p class="p20" style="margin-bottom:0px"><a href="<?php echo site_url('school/dashboard'); ?>"><span class="fa fa-long-arrow-left"></span> <?php echo lang('app.language_search_events_back_to_dash'); ?></a></p>
            
        </div>
        <div class="terms_condtion alloc_block nav_dashboard">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#alloc_block"><?php echo lang('app.language_learner_session_summary_title'); ?></a></li>
            </ul>
            <div class="tab-content">
                <div id="alloc_block" class="tab-pane fade in active terms_tab">
                    <!--<div class="message_block">-->
                        <!--<div class="col-md-12">-->
                            <?php if (null !== $this->session->getFlashdata('successmessage')) { ?>
                                <div role="alert" class="alert alert-success alert-dismissible" style="margin-top:20px;">
                                    <button aria-label="Close" data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span></button>
                                    <?php echo $this->session->getFlashdata('successmessage'); ?>
                                </div>	
                            <?php } ?>
                            <?php if (null !== $this->session->getFlashdata('errors')): ?>
                                <div class="alert alert-danger alert-dismissible" role="alert" style="margin-top:20px;">
                                    <button type="button" class="close" data-dismiss="alert"
                                            aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <?php echo $this->session->getFlashdata('errors'); ?>
                                </div>
                            <?php endif; ?>
                        <!--</div>-->
                    <!--</div>-->
                    <div class="session_header">
                        <p><span class="title"><?php echo lang('app.language_learner_session_date_time'); ?>: </span><?php echo $event_date . '  ' . $start_time . ' - ' . $end_time; ?></p>
                        <p>
                            <span class="title"><?php echo lang('app.language_learner_session_products'); ?>:</span>
                            <?php
                            $output = "";
                            foreach ($event_products as $products) {
                                $output.= $products->name . ', ';
                            }
                            echo rtrim($output, ' ,');
                            ?>
                        </p>
                        <p><span class="title"><?php echo lang('app.language_learner_session_venue'); ?>: </span><?php echo $event_details->venue_name; ?></p>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-s-12">
                            <!-- content display below -->
                            <div class="allocation_block">
                                <?php if ($allocate_disable == 0) { ?>
                                    <div class="form-group clearfix" style="font-size: 15px;">
                                        <div class="radio_block">
                                            <div class="row">
                                                <div class="col-sm-9 col-xs-12">
                                                    <label>
                                                        <input type="radio" class="radio_order" name="allocate_method" <?php echo $enableauto; ?> value="auto">
                                                        <?php echo lang('app.language_learner_session_auto_allocate_label'); ?>
                                                    </label>
                                                </div>
                                                <div class="col-sm-3 col-xs-12 text-right">
                                                    <button type="button" class="btn btn-sm btn-continue" <?php echo $btn_enable; ?> id="auto_allocate_btn">Auto allocate</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="radio_block mt20">
                                            <div class="row">
                                                <div class="col-sm-9 col-xs-12">
                                                    <label>
                                                        <input type="radio" class="radio_order" <?php echo $enablemanual; ?> name="allocate_method" value="manual">
                                                        <?php echo lang('app.language_learner_session_manual_allocate_label'); ?>
                                                    </label>
                                                </div>
                                                <div class="col-sm-12" id="manual_block" style="display:none;">
                                                    <p class="mt20" style="font-size: 15px;"><?php echo lang('app.language_learner_session_manual_allocate_description'); ?></p>
                                                    <form class="form-inline" action="#" id="search_learner_form">
                                                        <div class="row">
                                                            <div class="col-sm-1 col-xs-12">
                                                                <label class="search">Search</label>
                                                            </div>
                                                            <div class="col-sm-8 col-xs-12">
                                                                <input type="text" placeholder="<?php echo lang('app.language_school_search_placeholder'); ?>" name="search" class="form-control clearable search" id="search" value="<?php echo @$learner_search_item; ?>">
                                                            </div>
                                                            <div class="col-sm-3 col-xs-12 text-right media_btn">
                                                                <button type="submit" class="btn btn-success"><?php echo lang('app.language_school_search_btn'); ?></button>
                                                                <button type="button" id="lsearch_clr_btn" class="btn btn-default"><?php echo lang('app.language_school_clear_btn'); ?></button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                    <div class="clearfix"></div>
                                                    <div class="alloc_select mt20">
                                                        <form class="form-inline" action="<?php echo site_url('school/learner_allocation/' . $session_id); ?>" class="form bv-form" role="form" id="filter_form" data-bv-feedbackicons-valid="glyphicon glyphicon-ok" data-bv-feedbackicons-invalid="glyphicon glyphicon-remove"
                                                              data-bv-feedbackicons-validating="glyphicon glyphicon-refresh" enctype="multipart/form-data"
                                                              method="post" accept-charset="utf-8">
                                                            <div class="row">
                                                                <div class="col-sm-4 col-xs-12">
                                                                    <div class="form-group">
                                                                        <label class="control-label"><?php echo lang('app.language_school_filter_level_label'); ?></label>
                                                                        <select name="level[]" id="level"  class="form-control level myoptions filter-multi" multiple>  
                                                                            <?php foreach ($event_products as $event_product) { ?>
                                                                                <option  value="<?php echo $event_product->id; ?>" <?php echo (in_array($event_product->id, $filter_products)) ? 'selected' : ""; ?>><?php echo $event_product->name; ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-3 col-xs-12">
                                                                    <div class="form-group">
                                                                        <label class="control-label"><?php echo lang('app.language_school_filter_teacher_label'); ?></label>
                                                                        <?php
                                                                        $flag = true;
                                                                        $teacher_id = $teacher_class_id = '';
                                                                        $class_details = array();
                                                                        if ($supervisor_details != '' && $supervisor_details !== null) {
                                                                            foreach ($supervisor_details as $key => $learner_detail) {
                                                                                if ($learner_detail->teacherId != '') {
                                                                                    if ('' != $teacher_id && '' != $teacher_class_id) {
                                                                                        if ($teacher_id == $learner_detail->teacherId) {
                                                                                            if ($teacher_class_id == $learner_detail->teacherClassId) {
                                                                                                $flag = false;
                                                                                            }
                                                                                        } else {
                                                                                            if (in_array($learner_detail->teacherId, $teacher_id_array) && in_array($learner_detail->teacherClassId, $teacher_class_id_array)) {
                                                                                                $flag = false;
                                                                                            } else {
                                                                                                $flag = true;
                                                                                            }
                                                                                        }
                                                                                    }

                                                                                    if ($flag) {
                                                                                        $class_details [] = $learner_detail;
                                                                                    }
                                                                                    $teacher_id_array[] = $learner_detail->teacherId;
                                                                                    $teacher_class_id_array[] = $learner_detail->teacherClassId;
                                                                                    $teacher_id = $learner_detail->teacherId;
                                                                                    $teacher_class_id = $learner_detail->teacherClassId;
                                                                                }
                                                                            }
                                                                        }
                                                                        ?>
                                                                        <select name="teacher[]" id="teacher"  class="form-control teacher myoptions filter-multi" multiple>                                    
                                                                            <?php
                                                                            if ($class_details != '' && $class_details !== null) {
                                                                                //Supervisor dropdown unquie
                                                                                $class_details_unique = [];
                                                                                foreach($class_details as $class_detail){
                                                                                    $class_details_unique[$class_detail->teacherClassId] = $class_detail;
                                                                                }
                                                                                foreach ($class_details_unique as $class_detail) {
                                                                                    $teacher_class_id = (null != $class_detail->teacherClassId) ? $class_detail->teacherClassId : '';
                                                                                    $teacher_class_name = (null != $class_detail->englishTitle) ? $class_detail->englishTitle : '';
                                                                                    $teacher_id = (null != $class_detail->teacherId) ? $class_detail->teacherId : '';
                                                                                    $teacher_firstname = (null != $class_detail->teacherfirstname) ? $class_detail->teacherfirstname : '';
                                                                                    $teacher_lastname = (null != $class_detail->teacherlastname) ? $class_detail->teacherlastname : '';
                                                                                    $teacher_name = $teacher_firstname . ' ' . $teacher_lastname;
                                                                                    if (null != $class_detail->teacherId && null != $class_detail->teacherClassId) {
                                                                                        ?>
                                                                                        <option  value="<?php echo $teacher_class_id; ?>" <?php echo (in_array($teacher_class_id, $filter_teacher)) ? 'selected' : ""; ?>><?php echo $teacher_class_name . '/' . $teacher_name; ?></option> <?php
                                                                                    }
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2 col-xs-12">
                                                                    <div class="form-group">
                                                                        <label class="control-label"><?php echo lang('app.language_school_filter_ppm_label'); ?></label>
                                                                        <select name="ppm" id="ppm" class="form-control ppm myoptions">
                                                                            <option value=''><?php echo lang('app.language_school_filter_please_select'); ?></option>
                                                                            <?php foreach (range(10, 90, 10) as $number) { ?>
                                                                                <option  value="<?php echo $number; ?>" <?php echo ($number == $filter_ppm) ? 'selected' : ""; ?>>><?php echo $number; ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <input type="hidden" name="allocate" id="allocate" value="filter">
                                                                <div class="col-sm-3 col-xs-12 mt24 text-right media_filter">
                                                                    <button type="button" class="btn btn-success" id="filter_form_btn"><?php echo lang('app.language_school_apply_filter_btn'); ?></button>
                                                                    <button type="button" id="lfilter_clr_btn" class="btn btn-default"><?php echo lang('app.language_school_clear_btn'); ?></button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="col-sm-12 col-xs-12">
                                                        <form action="<?php echo site_url('school/learner_allocation/' . $session_id . '?allocate=manual'); ?>" class="form bv-form" role="form" id="manual_allocate"
                                                              data-bv-feedbackicons-valid="glyphicon glyphicon-ok" data-bv-feedbackicons-invalid="glyphicon glyphicon-remove"
                                                              data-bv-feedbackicons-validating="glyphicon glyphicon-refresh" enctype="multipart/form-data"
                                                              method="post" accept-charset="utf-8">
                                                            <div class="table-fixed institution_content mt20" id="table_fixhd">
                                                                <table width="100%" class="table table-bordered institution_table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th width="5%" style="text-align:center;"><input type="checkbox" name="allocate_all" id="allocate_all" value=""></th>
                                                                            <th width="20%"><?php echo lang('app.language_learner_session_label_name'); ?></th>
                                                                            <th width="25%"><?php echo lang('app.language_learner_session_label_username'); ?></th>
                                                                            <th width="21%"><?php echo lang('app.language_learner_session_label_level'); ?></th>
                                                                            <th width="19%"><?php echo lang('app.language_learner_session_label_class_teacher'); ?></th>
                                                                            <th width="10%"><?php echo lang('app.language_learner_session_label_ppm'); ?></th>
                                                                        </tr>
                                                                    </thead>
                                                                </table>
                                                            </div>
                                                            <div class="table-scroll institution_content" id="table_scrollbd">
                                                                <table width="100%" class="table table-bordered table-stripped institution_table">
                                                                    <tbody>
                                                                        <?php
                                                                        if ($learner_details != '' && $learner_details !== null) {
                                                                            foreach ($learner_details as $learners):
                                                                                ?>
                                                                                <tr>
                                                                                    <td width="5%" align="center">
                                                                                        <input type="checkbox" name="thirdparty_ids[]" class="thirdparty_ids"  value="<?php echo base64_encode($this->encrypter->encrypt($learners->thirdparty_id)); ?>"/>
                                                                                    </td>
                                                                                    <td width="20%"><?php echo $learners->firstname . ' ' . $learners->lastname; ?></td>
                                                                                    <td width="25%"><?php
                                                                                        if ($learners->order_type == 'under13') {
                                                                                            echo $learners->username;
                                                                                        } else {
                                                                                            echo $learners->email;
                                                                                        }
                                                                                        ?>
                                                                                    </td>
                                                                                    <td width="22%"><?php echo $learners->level; ?></td>
                                                                                    <td width="19%"><?php
                                                                                        if ($learners->englishTitle) {
                                                                                            echo $learners->englishTitle . '/' . $learners->teacherfirstname . ' ' . $learners->teacherlastname;
                                                                                        } else {
                                                                                            echo '';
                                                                                        }
                                                                                        ?></td>
                                                                                    <td width="9%"><?php
                                                                                        if ($learners->course_progress) {
                                                                                            echo round($learners->course_progress);
                                                                                        } else {
                                                                                            echo '0';
                                                                                        }
                                                                                        ?>
                                                                                    </td>
                                                                                </tr>
                                                                                <?php
                                                                            endforeach;
                                                                        } else {
                                                                            ?>
                                                                            <tr>
                                                                                <td colspan="6">
                                                                                    <div class="alert alert-danger fade in">
                                                                                        <a href="#" class="close" data-dismiss="alert">&times;</a>
                                                                                        <?php
                                                                                        echo lang('app.language_learner_session_no_learners_for_allocation');
                                                                                        ?>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
    <?php } ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>	
                                                        </form>	
                                                    </div>
                                                    <div class="col-sm-12 col-xs-12 mt20 text-right">
                                                        <button type="button" class="btn btn-sm btn-continue"<?php echo $btn_enable; ?> id="manual_allocate_btn" >Allocate selected</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
<?php } ?>
                                <div class="row">
                                    <div class="col-sm-12 col-xs-12">
                                        <p style="font-size: 15px;"><?php echo lang('app.language_learner_session_allocated_description'); ?></p>
                                    </div>
                                    <div class="col-sm-12 col-xs-12">
                                        <p style="font-size: 15px;float:left;width:25%;margin-top: 20px;">Total capacity:&nbsp;<span><b><?php echo $event_capacity; ?></b></span></p>
                                        <p style="font-size: 15px;float:left;width:25%;margin-top: 20px;">Allocated:&nbsp;<span><b><?php echo $learner_allocated; ?></b></span></p>
                                        <p style="font-size: 15px;float:left;width:25%;margin-top: 20px;">Available:&nbsp;<span><b><?php echo $allocation_available; ?></b></span></p>

                                        <?php if ($allocate_disable == 1) { ?>
                                            <button type="button" class="btn btn-sm btn-continue pull-right" id ="learner_allocation_export" <?php echo ($learner_alloted_details === FALSE) ? 'disabled' : ''; ?>><?php echo lang('app.language_school_generate_excel'); ?></button>
<?php } ?>
                                    </div>
                                    <div class="col-sm-12 col-xs-12" id="allocated_block">
                                        <form action="<?php echo site_url('school/learner_allocation/' . $session_id . '?allocate=unallocate'); ?>" class="form bv-form" role="form" id="unallocate"
                                              data-bv-feedbackicons-valid="glyphicon glyphicon-ok" data-bv-feedbackicons-invalid="glyphicon glyphicon-remove"
                                              data-bv-feedbackicons-validating="glyphicon glyphicon-refresh" enctype="multipart/form-data"
                                              method="post" accept-charset="utf-8">
                                            <div class="table-responsive">
                                                <div class="table-fixed institution_content">
                                                    <table width="100%" class="table table-bordered institution_table">
                                                        <thead>
                                                            <tr>
                                                                <th width="5%" style="text-align:center;"><input type="checkbox" name="unallocate_all" id="unallocate_all" value=""></th>
                                                                <th width="23%"><?php echo lang('app.language_learner_session_label_name'); ?></th>
                                                                <th width="26%"><?php echo lang('app.language_learner_session_label_username'); ?></th>
                                                                <th width="23%"><?php echo lang('app.language_learner_session_label_password'); ?></th>
                                                                <th width="23%"><?php echo lang('app.language_learner_session_label_level'); ?></th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                                <div class="table-scroll institution_content">
                                                    <table width="100%" class="table table-bordered table-stripped institution_table">
                                                        <tbody>
                                                            <?php
                                                            if ($learner_alloted_details != '' && $learner_alloted_details !== null) {
                                                                foreach ($learner_alloted_details as $learners_alloted):
                                                                    ?>
                                                                    <tr>
                                                                        <td width="5%" align="center">
                                                                            <input type="checkbox" name="thirdparty_ids[]" class="thirdparty_ids"  value="<?php echo base64_encode($this->encrypter->encrypt($learners_alloted->thirdparty_id)); ?>"/>
                                                                        </td>
                                                                        <td width="23%"><?php echo $learners_alloted->firstname . ' ' . $learners_alloted->lastname; ?></td>
                                                                        <td width="26%"><?php
                                                                            if ($learners_alloted->order_type == 'under13') {
                                                                                echo $learners_alloted->username;
                                                                            } else {
                                                                                echo $learners_alloted->email;
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                        <td width="23%"><?php
                                                                            if ($learners_alloted->order_type == 'under13') {
                                                                                echo $this->encrypter->decrypt(base64_decode($learners_alloted->password_visible));
                                                                            } else {
                                                                                echo 'NA';
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                        <td width="23%"><?php echo $learners_alloted->product_name; ?></td>
                                                                    </tr>
                                                                    <?php
                                                                endforeach;
                                                            } else {
                                                                ?>
                                                                <tr>
                                                                    <td colspan="6">
                                                                        <div class="alert alert-danger fade in">
                                                                            <a href="#" class="close" data-dismiss="alert">&times;</a>
                                                                            <?php
                                                                            echo lang('app.language_learner_session_no_learners_allocated');
                                                                            ?>
                                                                        </div>
                                                                    </td>
                                                                </tr> 
<?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>	
                                            </div>
                                            <div class="row">
                                            <div class="col-sm-12 col-xs-12">
                                                <?php if ($learner_alloted_details != '' && $learner_alloted_details !== null && $unallocate_disable === 0) { ?>
                                                    <button type="button" class="btn btn-sm btn-continue" id="unallocate_btn" style="float:right;margin-top: 15px;margin-bottom:25px;">Unallocate selected</button>
                                                <?php } else { ?>
                                                    <button type="button" class="btn btn-sm btn-continue" id="unallocate_btn" disabled style="float:right; margin-top: 15px;margin-bottom:25px;">Unallocate selected</button>	
<?php } ?>
                                            </div>
                                    </div>
                                        </form>
                                    </div>
                                </div>	
                            </div>
                            <!-- content display above-->
                        </div>
                    </div>
                </div>
            </div>
        </div> 



    </div>		
</div>
<script>
    $('#manual_allocate_btn').click(function (e) {
        $('#manual_allocate_btn').attr('disabled', true);
        $('#manual_allocate').submit();
    });
    $('#unallocate_btn').click(function (e) {
        $('#unallocate_btn').attr('disabled', true);
        $('#unallocate').submit();
    });
    $('#lsearch_clr_btn, #lfilter_clr_btn').on('click', function () {
        window.location = "<?php echo site_url('school/learner_allocation') . "/" . $session_id . '?allocate=clear'; ?>";
    });
    $('#filter_form_btn').click(function (e) {
        $('#filter_form').submit();
    });
    $('#auto_allocate_btn').click(function (e) {
        $('#auto_allocate_btn').attr('disabled', true);
        window.location = "<?php echo site_url('school/learner_allocation/' . $session_id . '?allocate=auto'); ?>";
    });

    var allocate_method = $('input[name=allocate_method]:checked').val();
    console.log(allocate_method);
    if (allocate_method == 'auto') {
        $('#manual_block').hide();
    }

    if (allocate_method == 'manual') {
        $('#manual_block').show();
    }

    $('input[type=radio][name=allocate_method]').change(function () {
        if (this.value == 'auto') {
            $('#manual_block').hide();
        }
        else if (this.value == 'manual') {
            $('#manual_block').show();
        }
    });

    $("#allocate_all").click(function () {
        $("#manual_block input[name='thirdparty_ids[]']").not(this).prop('checked', this.checked);
    });

    $("#unallocate_all").click(function () {
        $("#allocated_block input[name='thirdparty_ids[]']").not(this).prop('checked', this.checked);
    });

</script>