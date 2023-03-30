
    <?php
      use Config\Oauth;
      $this->oauth = new \Config\Oauth(); 
      $this->zendesk_access = $this->oauth->catsurl('zendesk_access');
      $this->zendesk_domain_url = $this->oauth->catsurl('zendesk_domain_url');
    ?>
    <div class="modal-dialog modal-md modal_login zendesk_login">
        <div class="modal-content contact-form">
            <div class="modal-header">
                
                <h1 class="modal-title text-center" id="myModalLabel"><?php echo lang('app.language_site_booking_screen2_tab_login'); ?></h1>
            </div>
            <div class="modal-body tab-holder">


                <div class="tab-content">

                    <div role="tabpanel" class="tab-pane   active" id="login">
                        <?php echo form_open('site/login?sc=popup&hl=' . $this->lang->lang(), array('autocomplete' => 'off', 'role' => 'form bv-form', 'class' => '', 'id' => 'login_form', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>

                        <div class="row terms_box" >
                            <?php if(isset($this->zendesk_access) && $this->zendesk_access == 1){ ?>
                                <div class="col-sm-12">                                    
                                    <?php $styles = 'id="terms_login"'; ?>
                                    <?php echo form_checkbox('termsL', '1', set_checkbox('terms', '1'), $styles); ?>                                    
                                    <label for= "terms_login"><?php echo lang('app.language_site_booking_screen_m2_front_terms_service'); ?></label>
                                </div>
                            <?php }else{ ?>
                                <div class="form-group text-center mt20">
                                <p style="color:red;" ><?php echo "Please Enable Zendesk Access for this Server"; ?></p>
                                </div>
                            <?php }  ?>

                            <p style="display:none;color:red;" class="text-center" id="custom_error"></p>
                            <p style="display:none;color:red;" class="text-center" id="custom_error_accept_terms"><?php echo lang('app.language_site_booking_screen4_accept_terms'); ?></p>


                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label><?php echo lang('app.language_site_booking_screen2_label_email_username_address'); ?> <span class="required">*</span></label>
                                    <input type="text" class="form-control input-sm" id="username" name="username" placeholder="" value="<?php echo set_value('username'); ?>" >
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label><?php echo lang('app.language_site_booking_screen2_label_password'); ?> <span class="required">*</span></label>
                                    <input type="password" id="password"  name="password" class="form-control input-sm" placeholder="" >
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>   
                        <div class="text-center">
                            <a class="forgot_password" href="<?php echo site_url('login/forgot_password'); ?>"><?php echo lang('app.language_site_booking_screen2_forgot_password'); ?></a>
                        </div>
                        <div class="form-group text-center mt20">
                            <button type="submit" id="login_submit" name="login_submit" value="test" class="btn btn-sm btn-continue btn_login"><?php echo lang('app.language_site_booking_continue_btn'); ?></button>
                            &nbsp;&nbsp;<img alt="loading" class="loading" style="display:none;"  src="<?php echo base_url('public/images/loading.gif'); ?>">
                        </div>
                        <input type="hidden" id="custId" name="zendesk" value="zendesk">
                        <input type="hidden" id="custId" name="return_url" value=<?php echo isset($_GET['return_to']) && ($_GET['return_to'] != "") ? $_GET['return_to'] : "false";?>>
                      


                        <?php echo form_close(); ?>   
                    </div>
                </div>

            </div>
            <div class="modal-footer" style="border-top: 0px solid #e5e5e5;padding:0px;">


                <?php form_close(); ?>
            </div>
        </div>
    </div>
