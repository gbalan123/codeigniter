<?php 
$this->encrypter = \Config\Services::encrypter();   
$this->validation =  \Config\Services::validation();
?>
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

    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <em class="fa fa-plus fa-fw"></em><?= esc($teacher_heading) ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12 clearfix">
                        <?php if (isset($teacher) && !empty($teacher)): ?>
                            <?php echo form_open_multipart('school/postteacher', array('class' => 'form bv-form', 'role' => 'form', 'id' => 'teacher_form', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>
                            <input type="hidden" name="teacher_id"  id="teacher_id" value="<?php echo base64_encode($this->encrypter->encrypt($teacher['id'])); ?>"/>
                        <?php else: ?>
                            <?php echo form_open_multipart('school/postteacher', array('class' => 'form bv-form', 'role' => 'form', 'id' => 'teacher_form', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>
                            <input type="hidden" name="teacher_id" id="teacher_id" value=""/>
                        <?php endif; ?>

                        <fieldset>

                            <legend><?php echo lang('app.language_school_teacher_user_details'); ?></legend>
                            <div class="row">
                                <div class="form-group col-xs-12 clearfix" >
                                    <label for="first_name"><?php echo lang('app.language_admin_institutions_first_name'); ?> <span>*</span></label> <input type="text"
                                                                                                                                                        class="form-control" name="firstname"  id="firstname" placeholder="<?php echo lang('app.language_admin_institutions_first_name'); ?>"
                                                                                                                                                        value="<?php echo set_value('firstname', isset($teacher) ? $teacher['firstname'] : ''); ?>"
                                                                                                                                                        >
                                    <?php if($this->validation->getError('firstname')){?>
                                    <p><?php echo $this->validation->getError('firstname') ?></p>
                                    <?php  } ?>	
                                </div>
                                </div>
                                 <div class="row">
                                <div class="form-group col-xs-12 clearfix">
                                    <label for="last_name"><?php echo lang('app.language_admin_institutions_second_name'); ?> <span>*</span></label> <input type="text"
                                                                                                                                                        class="form-control" name="lastname" id="lastname" placeholder="<?php echo lang('app.language_admin_institutions_second_name'); ?>"
                                                                                                                                                        value="<?php echo set_value('lastname', isset($teacher) ? $teacher['lastname'] : ''); ?>"
                                                                                                                                                        >
                                          <?php if($this->validation->getError('lastname')){?>
                                    <p><?php echo $this->validation->getError('lastname') ?></p>
                                    <?php  } ?>	
                                </div> 

                            </div>
                            <div class="row">
                                <div class="form-group col-xs-12 clearfix" >
                                    <label for="department"><?php echo lang('app.language_admin_institutions_department'); ?> </label> 

                                    <input type="text"
                                           class="form-control" name="department" id="department" placeholder="<?php echo lang('app.language_admin_institutions_department'); ?>"
                                           value="<?php echo set_value('department', isset($teacher) ? $teacher['department'] : ''); ?>"
                                           >
                                           <?php if($this->validation->getError('department')){?>
                                    <p><?php echo $this->validation->getError('department') ?></p>
                                    <?php  } ?>	

                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-xs-12 clearfix">
                                    <label for="email"><?php echo lang('app.language_admin_institutions_email_address'); ?> <span>*</span></label> <input type="text"
                                                                                                                                                      class="form-control" name="email" id="email"  placeholder="<?php echo lang('app.language_admin_institutions_email_address'); ?>"
                                                                                                                                                      value="<?php echo set_value('email', isset($teacher) ? $teacher['email'] : ''); ?>"
                                                                                                                                                      >
                                        <?php if($this->validation->getError('email')){?>
                                    <p><?php echo $this->validation->getError('email') ?></p>
                                    <?php  } ?>	
                                </div> 
                                </div>
                            <div class="row">
                                <div class="form-group col-xs-12 clearfix">
                                    <label for="email"><?php echo lang('app.language_admin_institutions_confirm_email_address'); ?> <span>*</span></label> <input type="text"
                                                                                                                                                              class="form-control" name="confirm_email" id="confirm_email" placeholder="<?php echo lang('app.language_admin_institutions_confirm_email_address'); ?>"
                                                                                                                                                              value="<?php echo set_value('confirm_email', isset($teacher) ? $teacher['email'] : ''); ?>"
                                                                                                                                                              >
                                     <?php if($this->validation->getError('confirm_email')){?>
                                    <p><?php echo $this->validation->getError('confirm_email') ?></p>
                                    <?php  } ?>	
                                </div> 
                            </div>
                        </fieldset>



                        <div class="form-group form-actions pull-right" style="clear:both;">
                            <?php if (isset($teacher['email'])): ?>
                            <?php endif; ?>
                            <button type="submit" id="submitBtn" class="btn btn-sm btn-continue" ><?php echo lang('app.language_admin_institutions_submit_btn'); ?></button><img alt="" id="loading_in" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" />
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

    $('#teacher_form').submit(function (e) {
        e.preventDefault(); 
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
                $('#' + k).next('span').remove();
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
    //resend email click
    $('.sendMailBtn').click(function (e) {
        e.preventDefault();
        var result = confirm("Are you sure?");
        if (result) {
            obj = {};
            obj.institution_id = $(this).attr('id');
            $('#' + $(this).attr('id')).attr('disabled', true);
            $('#loading_in').show();
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('school/resend_teacher_account_email'); ?>",
                data: obj,
                dataType: 'json',
                success: function (data)
                {
                    $('#' + $(this).attr('id')).attr('disabled', false);
                    $('#loading_in').hide();
                    if (data.success) {
                        location.reload();
                    } else {
                    }
                }
            });
        }
        return false;
    });
</script>