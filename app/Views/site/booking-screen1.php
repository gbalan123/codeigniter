<?php include_once 'header-booking.php';
use Config\MY_Lang;
use App\Models\Admin\Cmsmodel;
$this->lang                 = new \Config\MY_Lang();
$language                   = $this->lang->lang();
$this->cmsmodel             = new Cmsmodel();
$this->data['helplinks']    = $this->cmsmodel->helplinks();
$user_nameDatas             = "user_name";
$user_passDatas             = "user_password";
$this->session              = \Config\Services::session();
$validation                 = \Config\Services::validation();
?>
<?php 
$mobile_err = 0;
if(isset($_GET['is_mobile'])) { $mobile_err = 1; } 
?> 

<!-- steps code -->
<div class="bg-lightgrey">
    <div class="container">

        <div class="get_started">
            <div class="row">
                <div class="col-sm-12">
                    <?php include "booking-tabs.php"; ?>

                    <div class="main_tab_content">
                        <div class="tab-content">
                            <div id="get_started_tab" class="tab-pane get_start fade in active">
                                <div class="row">

                                    <?php if (Null !== session()->get("message")) { ?>
                                        <div class="alert alert-danger fade in">
                                            <a href="#" class="close" data-dismiss="alert">&times;</a>
                                            <?php echo session("message"); ?>
                                        </div>
                                    <?php } ?>
                                    <div class="col-sm-12">

                                        <?php if ($validation->getError('code') != NULL || (session()->getFlashdata("errors"))):
                                                                           
                                            ?>
 
                                            <div class="alert alert-danger fade in">
                                                <a href="#" class="close" data-dismiss="alert">&times;</a>
                                                <?php
                                                if ($validation->getError('code')) {
                                                    echo lang('app.language_site_booking_screen_m1_enter_code_error') . '<br />';
                                                }
                                                ?>

                                                <?php echo session()->getFlashdata("errors"); ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($mobile_err): ?>
                                        <div class="alert alert-danger fade in">
                                                <a href="#" class="close" data-dismiss="alert">&times;</a>
                                                <?php echo lang('app.language_mobile_user_test_not_to_take_error'); ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-sm-12 primary_errors">
                                    <?php if( Null !== session()->get("primaryerrors") || $validation->getError($user_nameDatas) != NULL || $validation->getError($user_passDatas) ):

                                             ?>
                                                <div class="alert alert-danger fade in">
                                                    <a href="#" class="close" data-dismiss="alert">&times;</a>
                                                    <?php
                                                    if ($validation->getError($user_nameDatas)) {
                                                         echo "<p>".$validation->getError($user_nameDatas)."</p>";
                                                    }
                                                    if ($validation->getError($user_passDatas)) {
                                                        echo "<p>".$validation->getError($user_passDatas)."</p>";
                                                    }
                                                    ?>
                                                    <p><?php echo session('primaryerrors'); ?></p>
                                                   
                                                </div>

                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6 col-sm-12 col-xs-12 header-started-<?php echo $language; ?>">
                                        <div class="btn_alignment mb30-xs">
                                        <h4 class="left_heading"><?php echo lang('app.language_site_booking_screen_m1_title_one'); ?>&nbsp;&nbsp;<img alt="loading" id="loading" style="display:none;" src="<?php echo base_url('public/images/loading.gif'); ?>">
                                                <span class="pull-right">
                                                    <?php
                                                    if (!empty($this->data['helplinks'])) {
                                                        $helplinks = $this->data['helplinks'];
                                                    }
                                                    ?>
                                                    <a class="help_icon_hide" href="<?php if ($this->lang->lang() == 'en') {
                                                        echo $helplinks['0']['target_url'];
                                                    } elseif ($this->lang->lang() == 'ms') {
                                                        echo $helplinks['8']['target_url'];
                                                    } ?>"  target="_blank"  title="help">
                                                        <img src="<?php echo base_url() . '/public/images/ico-help.png'; ?>" alt="icon" /></a>
                                                </span>
                                                <div class="clearfix"></div>
                                            </h4>
                                            <p class="above_16">
<?php echo lang('app.language_site_booking_screen_m1_content_one'); ?>
                                            </p>
<?php echo form_open_multipart('site/is-cat-available-for-me', array('class' => '', 'role' => 'form', 'id' => 'token_form', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh", 'autocomplete' => 'off')); ?>
                                            <div class="btn_bottom">
                                                <div class="row">
                                                    <div class="col-md-10 col-sm-12 col-xs-12 text-right text_center">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control input-sm" name="code" id="code" maxlength="9"  value="<?php echo set_value('code'); ?>" placeholder="<?php echo lang('app.language_site_booking_screen_m1_label'); ?>"/>
                                                        </div>
                                                        <button type="submit" id="continue_btn_1" name="getStarted" value="over13" class="btn btn-sm btn-continue text-right" ><?php echo lang('app.language_site_booking_continue_btn'); ?></button>
                                                    </div>
                                                </div>
                                            </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12 col-xs-12 footer-primary-login-<?php echo $language; ?>" id="primary_login">
                                        <div class="btn_alignment">
                                            <h4><?php echo lang('app.language_site_booking_screen_m1_title_two'); ?></h4>
                                            <p class="under_16"><?php echo lang('app.language_site_booking_screen_m1_content_two'); ?></p>
<?php echo form_open_multipart('site/is-cat-available-for-me', array('class' => '', 'role' => 'form', 'id' => 'primary_login_form', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh", 'autocomplete' => 'off')); ?>
                                            <div class="btn_bottom">
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label><?php echo lang('app.language_site_booking_screen_m1_username_label'); ?></label>
                                                        <input type="text" id="user_name"  name="user_name" value="<?php echo set_value($user_nameDatas); ?>"  class="form-control input-sm"  />
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label><?php echo lang('app.language_site_booking_screen_m1_password_label'); ?></label>
                                                        <input type="password" id="user_password" name="user_password" value="<?php echo set_value($user_passDatas); ?>"  class="form-control input-sm"  />
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 col-xs-12 text-right text_center">
                                                    <input type="hidden" name="formname" value="primary"/>
                                                    <button type="submit" id="confirm_login" name="getStarted" value="under13"  class="btn btn-sm btn-continue text-right"><?php echo lang('app.language_site_booking_continue_btn'); ?></button>
                                                </div>
                                            </div>
                                        </div>
                                            </form>
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


<?php include 'footer-booking.php'; ?>
<script>

    $(document).ready(function () {

        $('#continue_btn_1').click(function (e) {
            localStorage.setItem('oldtoken', $('#code').val());

            $('#token_form').submit();
        });

        //$('#code').focus();
        $('#code').on('input', function (e) {
            if (e.which === 8 && !$(e.target).is("input")) {
                e.preventDefault();
                $(this).val($(this).val().toUpperCase()).css({'font-family': 'courier', 'font-size': '18px', 'letter-spacing': '2px'});
            }
        });

        if (typeof localStorage.getItem('oldtoken') != 'undefined') {
            $('#code').val(localStorage.getItem('oldtoken'));
        } else {
            $('#code').val('');
        }

        $('#confirm_login').click(function (e) {
            localStorage.setItem('primary_user', $('#user_name').val());
            localStorage.setItem('primary_password', $('#user_password').val());
        });

        if (typeof localStorage.getItem('primary_user') != 'undefined') {
            $('#user_name').val(localStorage.getItem('primary_user'));
        } else {
            $('#user_name').val('');
        }

        if (typeof localStorage.getItem('primary_password') != 'undefined') {
            $('#user_password').val(localStorage.getItem('primary_password'));
        } else {
            $('#user_password').val('');
        }



    });


</script>

<script>
            $(document).ready(function() {
    tabScroll();
   
    });
            $(window).resize(function(){
    tabScroll();
    
    });
        function tabScroll(){          
           var listWidth = 0;                  
              $('.main_tab .nav.nav-tabs li').each(function(){
                  listWidth += $(this).outerWidth();
              })

        if($(window).width() < 768){
              $('.main_tab .nav-tabs').width(listWidth + 30) ;
              if(!$('.overflow_x ul li:first-child').hasClass('active')){
              scrollLefts();
            }
        }
        else{
             $('.main_tab .nav-tabs').width('auto')  ;
        }
       }
function scrollLefts(){
     $('.overflow_x').scrollLeft(0);
     
    $('.overflow_x').scrollLeft($('.overflow_x ul li.active').siblings()[0].clientWidth - 20);
}
            
            

</script>