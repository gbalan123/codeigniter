<?php //include_once 'header.php';      ?>
<style>
    #institute_form p {
        color : red;
    }
    .form-actions {
        margin: 0;
        background-color: transparent;
        text-align: center;
    }
    .has-feedback label~.form-control-feedback {
        top: 0px;
    }
    .has-feedback .form-control {

    }
    .dropdown-menu
    {
        width: 100%; 
    }
</style>
<!-- /.row -->
<div class="row">
    <p class="lead">
        <?php //echo validation_errors(); ?></p>
    <div class="col-xs-12">
        <div class="panel panel-default">   
            <div class="panel-heading">
                <i class="fa fa-plus fa-fw"></i><?php echo $class_heading; ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12 clearfix">
                        <?php if (isset($class) && !empty($class)): ?>
                            <?php echo form_open_multipart('teacher/postclass', array('class' => 'form bv-form', 'role' => 'form', 'id' => 'class_form', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>
                            <input type="hidden" name="class_id"  id="class_id" value="<?php echo $class['classId']; ?>"/>
                        <?php else: ?>
                            <?php echo form_open_multipart('teacher/postclass', array('class' => 'form bv-form', 'role' => 'form', 'id' => 'class_form', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>
                            <input type="hidden" name="class_id" id="class_id" value=""/>
                        <?php endif; ?>




                        <div class="row">
                            <div class="form-group col-xs-8 clearfix" >
                                <label for="classname"><?php echo lang('app.language_teacher_class_label_name'); ?> <span>*</span></label> 
                                
                                <input type="text" class="form-control" name="classname"  id="classname" placeholder="<?php echo lang('app.language_teacher_class_label_name'); ?>" value="<?php echo set_value('classname', isset($class) ? $class['englishTitle'] : ''); ?>"
                                                                                                                                              >
                                                                                                                                              <?php //echo form_error('classname'); ?>
                            </div>

                        </div>
                        <?php if(isset($class)): ?>    
                            <div class="row">
                                <div class="form-group col-xs-8 clearfix" >
                                    <label class="checkbox-inline"><input type="checkbox" name="status" <?php if($class['status'] == '0' ){ echo 'checked'.' ';} ?> <?php if(($enable == 'no') && ($class['status'] != '1') ){ echo 'disabled';} ?> value="<?php echo 'inactive'; ?>"><?php echo 'Mark as inactive'; ?></label>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="form-group form-actions pull-right" style="clear:both;">
                            <button type="submit" id="submitBtn" class="btn btn-lg btn-primary wpsc_button" ><?php echo lang('app.language_admin_institutions_submit_btn'); ?></button><img id="loading_in" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" />
                        </div>	 
                        <?php form_close(); ?>
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
<script>

    $('#class_form').submit(function (e) {

        $('#submitBtn').attr('disabled', true);
        $('#loading_in').show();
        $.ajax({
            type: "POST",
            url: $(this).attr('action'),
            data: $(this).serialize(),
            dataType: 'json',
            success: function (data)
            {
                clear_errors(data);
                $('#submitBtn').attr('disabled', false);
                $('#loading_in').hide();
                $( ".help-block" ).empty()
                if (data.success) {
                    location.reload();
                } else {
                    set_errors(data);
                }

            }
        });
        return false;
    });

    function clear_errors(data)
    {
        if (typeof (data.errors) != "undefined" && data.errors !== null) {
            for (var k in data.errors) {

                $('#' + k).next('p').remove();

            }
        }
    }

    function set_errors(data)
    {
        if (typeof (data.errors) != "undefined" && data.errors !== null) {
            for (var k in data.errors) {

                $(data.errors[k]).insertAfter($("#" + k)).css('color', 'red');

            }
        }
    }

</script>