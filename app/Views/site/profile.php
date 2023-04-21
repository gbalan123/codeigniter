<?php

    $activeDatas = "active";
    $in_activeDatas = "in active";
    $site_profileDatas = "site/profile";
    $form_bv_formDatas = "form bv-form";
    $language_admin_submitLang = lang('app.language_admin_submit');
    $loadingImage = base_url('public/images/loading.gif');
?>
<!-- steps code -->
<div class="bg-lightgrey">
    <div class="container">

        <div class="get_started">
            <div class="row">
                <div class="col-sm-12">
                    <div class="main_tab">
                        <p class="p20 back_txt"><a href="<?php echo site_url('site/dashboard'); ?>"><span class="fa fa-long-arrow-left"></span> <?php echo lang('app.language_search_events_back_to_dash'); ?></a></p>
                    </div>
                        <div class="forgot_content tab-content">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <?php include 'messages.php'; ?>
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="col-sm-12">
                                        <div class="">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="nav_dashboard">
                                                        <div class="overflow_x">
                                                            <ul class="nav nav-tabs">
                                                            <li class="<?php echo ($this->session->get('tabprofile')) ? $activeDatas : ''; ?>"><a href="#profile_tab" data-toggle="tab"><?php echo lang('app.site_label_drop_profile') ?></a></li>
                                                                <?php if ($profile[0]->facebook_id != '' || $profile[0]->google_id != ''): ?>
                                                                <?php else : ?>
                                                                    <li class="<?php echo ($this->session->get('tabpass')) ? $activeDatas : ''; ?>"><a href="#password_tab" data-toggle="tab"><?php echo lang('app.change_title'); ?> </a></li>
                                                                <?php endif; ?>
                                                                <li class="<?php echo ($this->session->get('tablang')) ? $activeDatas : ''; ?>"><a href="#lang_tab" data-toggle="tab"><?php echo lang('app.lsetting_panel_header_title'); ?> </a></li>

                                                                <div class="clearfix"></div>
                                                            </ul>
                                                        </div>
                                                        
                                                        <div class="tab-content">
                                                            <div id="profile_tab" class="tab-pane login fade <?php echo ($this->session->get('tabprofile')) ? $in_activeDatas : ''; ?>">
                                                                <div class="row">
                                                                    <div class="col-sm-12 profile-form">

                                                                        <?php echo form_open($site_profileDatas, array('role' => $form_bv_formDatas, 'class' => 'form-horizontal', 'autocomplete' => 'off', 'method' => 'POST', 'id' => '', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>
                                                                        <div class="form-group">
                                                                            <label class="col-sm-4 control-label"><?php echo lang('app.language_site_booking_screen2_label_first_name'); ?><span class="required">*</span></label>
                                                                            <div class="col-sm-6 col-xs-12">
                                                                                <input type="text" class="form-control input-sm" name="firstname" value="<?php echo set_value('firstname', isset($profile) ? $profile[0]->firstname : ''); ?>"
                                                                                       placeholder=""  />
                                                                                       <p><?php  echo $this->validation->getError('firstname');?></p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="col-sm-4 control-label"><?php echo lang('app.language_site_booking_screen2_label_second_name'); ?><span class="required">*</span></label>
                                                                            <div class="col-sm-6 col-xs-12">
                                                                                <input type="text" class="form-control input-sm" name="secondname" value="<?php echo set_value('secondname', isset($profile) ? $profile[0]->lastname : ''); ?>"
                                                                                       placeholder=""  />
                                                                                       <p><?php  echo $this->validation->getError('secondname');?></p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="col-sm-4 control-label"><?php echo lang('app.language_site_booking_screen2_label_email_address'); ?><span class="required">*</span></label>
                                                                            <div class="col-sm-6 col-xs-12">
                                                                                <input type="email" class="form-control input-sm" name="email" value="<?php echo set_value('email', $this->session->get('username')); ?>"
                                                                                       placeholder=""  />
                                                                                       <p><?php  echo $this->validation->getError('email');?></p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="text-center">
                                                                            <div class="col-sm-12 col-xs-12">
                                                                                <button type="submit" name="profile_submit" value="test"  class="btn btn-sm btn-continue"><?= $language_admin_submitLang; ?></button>
                                                                                <img alt="loading" class="loading" style="display:none;" src="<?= $loadingImage; ?>">
                                                                            </div>
                                                                        </div>


                                                                        <?php echo form_close(); ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                           
                                                            <?php if ($profile[0]->facebook_id != '' || $profile[0]->google_id != ''): ?>
                                                            <?php else : ?>
                                                                <div id="password_tab" class="tab-pane login fade <?php echo ($this->session->get('tabpass')) ? $in_activeDatas : ''; ?>">
                                                                    <div class="row">
                                                                        <div class="col-sm-12 changepass-form">

                                                                            <?php echo form_open($site_profileDatas, array('role' => $form_bv_formDatas, 'class' => 'form-horizontal', 'autocomplete' => 'off', 'method' => 'POST', 'id' => '', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>

                                                                            <div class="form-group">
                                                                                <label class="col-sm-4 control-label"><?php echo lang('app.current_password'); ?><span class="required">*</span></label>
                                                                                <div class="col-sm-6 col-xs-12">
                                                                                    <input type="password" name="current_password" class="form-control input-sm" placeholder=""    />
                                                                                    <p><?php  echo $this->validation->getError('current_password');?></p>
                                                                                </div>
                                                                            </div>

                                                                            <div class="form-group">
                                                                                <label class="col-sm-4 control-label"><?php echo lang('app.new_password'); ?><span class="required">*</span></label>
                                                                                <div class="col-sm-6 col-xs-12">
                                                                                    <input type="password" name="new_password" class="form-control input-sm" placeholder=""   data-toggle="tooltip" title="<?php echo str_replace('{field}', 'Password', lang('app.language_site_booking_screen2_password_check')); ?>" />
                                                                                    <p><?php  echo $this->validation->getError('new_password');?></p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="col-sm-4 control-label"><?php echo lang('app.confirm_new_password'); ?><span class="required">*</span></label>
                                                                                <div class="col-sm-6 col-xs-12">
                                                                                    <input type="password" class="form-control input-sm" name="confirm_new_password" placeholder=""  />
                                                                                    <p><?php  echo $this->validation->getError('confirm_new_password');?></p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="text-center">
                                                                                <div class="col-sm-12 col-xs-12">
                                                                                    <button type="submit" name="changepass_submit" value="test"  class="btn btn-sm btn-continue"><?= $language_admin_submitLang; ?></button>
                                                                                    <img alt="loading" class="loading" style="display:none;" src="<?= $loadingImage; ?>">
                                                                                </div>
                                                                            </div>
                                                                            <?php echo form_close(); ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
               
                                                            <div id="lang_tab" class="tab-pane login fade <?php echo ($this->session->get('tablang')) ? $in_activeDatas : ''; ?>">
                                                                <div class="row">
                                                                    <div class="col-sm-12">
                                                                        <div class="form-group">
                                                                            <span>
                                                                                <?php echo lang('app.lsetting_panel_header_desc'); ?>
                                                                            </span>
                                                                        </div>

                                                                        <?php echo form_open($site_profileDatas, array('role' => $form_bv_formDatas, 'class' => 'form-horizontal', 'autocomplete' => 'off', 'method' => 'POST')); ?>
                                                                        <div class="form-group">
                                                                            <label class="col-sm-4 control-label"><?php echo lang('app.lsetting_label_language'); ?> <span class="required">*</span></label>
                                                                            <div class="col-sm-6 col-xs-12">
                                                                                <select name="language" class="form-control mylanguage input-sm">
                                                                                <?php foreach ($all_languages as $language): ?>
                                                                                        <option value="<?php echo base64_encode($language->language_id); ?>" <?php echo ( intval($language->language_id) == intval(@$profile[0]->language_id)) ? 'selected' : ''; ?> ><?php echo json_decode('"' . $language->name . '"'); ?></option>
                                                                                    <?php endforeach; ?>
                                                                                </select>
                                                                                
                                                                            </div>

                                                                        </div>

                                                                        <div class="text-center">
                                                                            <div class="col-sm-12 col-xs-12">
                                                                                <button type="submit" name="language_submit" value="test"   class="btn btn-sm btn-continue"><?= $language_admin_submitLang; ?></button>
                                                                                <img alt="loading" class="loading" style="display:none;" src="<?= $loadingImage; ?>">
                                                                            </div>
                                                                        </div>
                                                                        <?php echo form_close(); ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                </div>
            </div>
        </div>
          </div>
</div>


<script>
            $(document).ready(function() {
    tabScroll();
    });
            $(window).resize(function(){
    tabScroll();
    });
            $(window).load(function(){               
            tabScroll();
            });
            
        function tabScroll(){          
           var listWidth = 0;                  
              $('.nav.nav-tabs li').each(function(){
                  listWidth += $(this).outerWidth();
              })

        if($(window).width() < 480){
              $('.nav-tabs').width(listWidth)  ;
            }
        else{
             $('.nav-tabs').width('auto')  ;
        }
       }

            
            

</script>