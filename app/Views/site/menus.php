<style>
#custom_error > .errors > ul > li {
     list-style-type: none; 
     padding: 3px;  
    }
#custom_error > .errors > ul { 
     padding: 0px;  
 }
</style>
<?php 

$this->session = session();
use Config\MY_Lang;
$this->myconfig = new \Config\MY_Lang(); 

$learnertype =  $this->session->get('learnertype');
$uri = service('uri');
		$url_element[] =  $uri->getSegment(1);
        $this->acl_auth = new App\Libraries\Acl_auth();
  
// $url_element = $this->uri->segments;
$array_pages = ['cats_stepcheck', 'cats_steps', 'cats_solution', 'cats_stepcheck_employers', 'cats_stepcheck_education', 'cats_stepcheck_goverment', 'cats_stepcheck_format','about_us'];
?>
<!-- NAVBAR
================================================== -->

<?php if( (count($url_element) == 1) || ( isset($url_element[3]) && in_array($url_element[3],$array_pages)) ) { ?>
        <header class="fixed_header">
  <?php }?>
        <div class="bg-white">
    <div class="container-fluid">
        <div class="row">
        <div class="col-md-6 col-sm-4 col-xs-4">
            <div class="navbar-header">
            <div class="logo"> 
            <a class="navbar-brand" href="<?php echo ($this->acl_auth->logged_in()) ? site_url('site/dashboard') : site_url(); ?>"><img src="<?php echo base_url() . '/public/images/logo_new.svg'; ?>" class="img-responsive" alt="CATs Logo" /></a> 
                <!--  -->
            </div>
        </div>
        </div>
        <div class="col-md-6 col-sm-8 col-xs-8">
            <div class="langeage-box lang_custom">
            <div class="dropdown language_select" id="langDropdown">
                <a class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="l-icon">
                        <?php
                      $this->config = new \Config\MY_Lang();
                   
                      if (is_array($languages) || is_object($languages))
{
                        foreach ($languages as $item):
                        
                            if ($item->code == $this->data['lang_code']):
                                echo json_decode('"' . $item->name . '"');
                            endif;
                        endforeach;
                    }
                        ?>
                    </span>

                </a>
                <div class="dropdown-menu  dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                    <?php
                      if (is_array($languages) || is_object($languages))
                      {
                    foreach ($languages as $item) {
                            ?>  <!-- CCC -131 - Condition changed to show only the basic languages by using content_status column in language-->
                            <!-- <a class="dropdown-item" href="#" onclick="languagedropdown('<?php echo $this->config->switch_uri($item->code); ?>')"><?php echo json_decode('"' . $item->name . '"'); ?></a>  -->
                            <a class="dropdown-item" href="<?=site_url("lang/$item->code")?>"><?php echo json_decode('"' . $item->name . '"'); ?></a> 
                    <?php } }?>
                </div>
            </div>
            
            <?php 
   
   if ($this->acl_auth->logged_in()) { 
       
           if (strlen(ucfirst($this->session->get('user_firstname')." ".$this->session->get('user_lastname'))) > 5) {
               $striped_user = mb_substr(ucfirst($this->session->get('user_firstname')." ".$this->session->get('user_lastname')), 0, 5) . '...';
           } else {
               $striped_user = mb_substr(ucfirst($this->session->get('user_firstname')." ".$this->session->get('user_lastname')),0,5,'UTF-8');
           }
          
        ?>  
       <div class="dropdown user_logout" id="userDropdown">
           <a class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
               <i class="fa fa-user fa-2x"></i> <span class="user_name"><?php echo $striped_user; ?></span>
           </a>
           <div class="dropdown-menu  dropdown-menu-right" aria-labelledby="dropdownMenuButton">
               <a class="dropdown-item" href="<?php echo site_url('site/dashboard'); ?>"><span class="fa fa-dashboard " style="color: #fff;"></span>&nbsp;<?php echo lang('app.language_dashboard'); ?></a>
                <?php if ($learnertype != 'under13'): ?>
                    <a class="dropdown-item" href="<?php echo site_url('site/profile'); ?>"><span class="fa fa-user " style="color: #fff;"></span>&nbsp;<?php echo lang('app.site_label_drop_profile'); ?></a>
                <?php endif; ?>
               <a class="dropdown-item" id="logoutbtn" href="#"><span class="fa fa-sign-out" style="color: #fff;"></span>&nbsp;<?php echo lang('app.site_label_drop_logout'); ?></a>
           </div>
       </div>
   <?php } else { ?>
       <input name="" type="submit" class="loginbtn" value="<?php echo lang('Login'); ?>"  data-toggle="modal" data-target="#loginModal" data-backdrop="static" data-keyboard="false">
   <?php } ?>
           
        </div>
        </div>
        
        

    </div><div class="clear"></div>
       
    </div>

</div>
<?php if( (count($url_element) == 1) || ( isset($url_element[3]) && in_array($url_element[3],$array_pages)) ) { ?>
     </header>
<?php }?>


<!-- Login Modal Starts -->
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  aria-hidden="true">
    <div class="modal-dialog modal-md modal_login">
        <div class="modal-content contact-form">
            <div class="modal-header">
                <button type="button" class="close btn_close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times"></i></span></button>
                <h1 class="modal-title text-center" id="myModalLabel"><?php echo lang('app.language_site_booking_screen2_tab_login'); ?></h1>
            </div>
            <div class="modal-body tab-holder">


                <div class="tab-content">

                    <div role="tabpanel" class="tab-pane   active" id="login">
                        <?php echo form_open('site/login?sc=popup&hl=' . $this->data['lang_code'], array('autocomplete' => 'off', 'role' => 'form bv-form', 'class' => '', 'id' => 'login_form', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>

                        <div class="row terms_box" >
                            <div class="col-sm-12">                                    
                                <?php $styles = 'id="terms_login"'; ?>
                                <?php echo form_checkbox('termsL', '1', set_checkbox('terms', '1'), $styles); ?>                                    
                                <label for= "terms_login"><?php echo lang('app.language_site_booking_screen_m2_front_terms_service'); ?></label>
                            <p style="display:none;color:red;" class="text-center" id="custom_error"></p>
                            <p style="display:none;color:red;" class="text-center" id="custom_error_accept_terms"><?php echo lang('app.language_site_booking_screen4_accept_terms'); ?></p>

                            </div>
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

                        <?php echo form_close(); ?>   
                    </div>
                </div>

            </div>
            <div class="modal-footer" style="border-top: 0px solid #e5e5e5;padding:0px;">


                <?php form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script language="javascript">
    function languagedropdown(gohere) {
        
        myLocation = "<?php echo base_url(); ?>/" + gohere;
        window.location = myLocation;
    }
</script>
