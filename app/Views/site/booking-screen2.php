<?php include_once 'header-booking.php'; 
use Config\MY_Lang;
use App\Models\Admin\Cmsmodel;
use Config\Oauth;
$this->lang = new \Config\MY_Lang();
$this->cmsmodel = new Cmsmodel();
$this->oauth = new \Config\Oauth();
$this->data['helplinks'] = $this->cmsmodel->helplinks();
?>

<style>
    .mandatory{
        float: left;
        padding-top: 10px;
        margin-right: 3px;
    }
    .password_condition {
	    color: red;
	    font-size: 12px;
	    margin-left: 0px; 
	    position: relative;
	    padding: 0px 25px 0 10px;
	    text-align: justify; 
	    display: none;
	}
</style>

<div class="bg-lightgrey">
    <div class="container">
    
	<div class="get_started">
		<div class="row">
			<div class="col-sm-12">
						<?php include "booking-tabs.php"; ?>
						
			            <div class="main_tab_content">
						<div class="tab-content">
						   <div id="sign_up_login_tab" class="tab-pane sign_login fade in active">
								<div class="row">
									
						                <?php $session_organization = $this->session->get('organization_data');include_once 'messages.php'; ?>
						                 <?php if ($this->session->setflashdata('face_email_err')) { ?>
						                    <div role="alert" class="alert alert-danger alert-dismissible">
						                        <button aria-label="Close" data-dismiss="alert" class="close" type="button"><span aria-hidden="true">x</span></button>
						                        <?php echo $this->session->setflashdata('face_email_err'); ?>
						                    </div>		
						                <?php } ?>
						           	 
			
									<div class="col-md-12 col-sm-12 col-xs-12">
										<div class="">
											<div class="row"><h4><?php echo lang('app.language_site_booking_screen2_title'); ?></h4>
												<div class="col-md-12">
													<div class="panel with-nav-tabs panel-default">
														<div class="panel-heading">
															<ul class="nav nav-tabs">																 
                            									<li role="presentation" class="<?php echo ($this->session->get('tablogin')) ? 'active' : ''; ?>"><a href="#login_tab" aria-controls="profile"
                                                                                                                                     role="tab" data-toggle="tab"><?php echo lang('app.language_site_booking_screen2_tab_login'); ?></a></li>
                                                                <li role="presentation" class="<?php echo ($this->session->get('tabsignup')) ? 'active' : ''; ?>"><a href="#signup_tab"
                                                                                                                                      aria-controls="home" role="tab" data-toggle="tab" aria-expanded="true" ><?php echo lang('app.language_site_booking_screen2_tab_signup'); ?></a></li>
																<?php 
																	if(!empty($this->data['helplinks'])) {
																	$helplinks = $this->data['helplinks'];
																	}
																?>
																<li class="pull-right">
																<span class="help_icon">
																	<a class="help_icon_hide" id="signup_help" href="<?php if($this->lang->lang() == 'en'){ echo $helplinks['1']['target_url'];} elseif($this->lang->lang() == 'ms'){ echo $helplinks['9']['target_url'];}  ?>" target="_blank"  title="help"><img  src="<?php echo base_url() . '/public/images/ico-help.png'; ?>" alt="icon" /></a>
																	<a class="help_icon_hide" id="login_help" style="display:none;" href="<?php if($this->lang->lang() == 'en'){ echo $helplinks['2']['target_url'];} elseif($this->lang->lang() == 'ms'){ echo $helplinks['10']['target_url'];}  ?>" target="_blank"  title="help"><img  src="<?php echo base_url() . '/public/images/ico-help.png'; ?>" alt="icon" /></a>
																</span></li>
																<div class="clearfix"></div>
															</ul>
														</div>
														<div class="panel-body">
															<div class="tab-content">
																<div id="signup_tab" role="tabpanel" class="tab-pane signup fade <?php echo ($this->session->get('tabsignup')) ? 'active in' : ''; ?>" >
																	<?php echo form_open('site/signup-o-login', array('role' => 'form bv-form', 'class' => '', 'autocomplete' => 'off', 'id' => 'signup_form_changes', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh", 'novalidate' => 'true')); ?>
                                                                    <div class="form-group">
                                                                        <div><?php echo lang('app.language_site_booking_screen_m2_signup_text'); ?></div>
                                                                    </div>
																	<div class="row">
																		<div class="col-md-6 col-sm-12 col-xs-12">
																			<div class="form-group">
																				<input type="text" class="form-control input-sm" id="reg_firstname" name="firstname"  value="<?php echo set_value('firstname'); ?>" placeholder=" <?php echo lang('app.language_site_booking_screen2_label_first_name'); ?> *" required />
                                        									
                                                                                <?php if ($this->validation->getError('firstname')) : ?>
	                                                                                <P>
                                                                                      <?= $this->validation->getError('firstname') ?>
                                                                                </P>
	                                                                            <?php endif; ?> 
																			</div>
																		</div>
																		<div class="col-md-6 col-sm-12 col-xs-12">
																			<div class="form-group">
																				
																				<input type="text" class="form-control input-sm" id="reg_secondname" name="secondname" value="<?php echo set_value('secondname'); ?>"
									                                               placeholder=" <?php echo lang('app.language_site_booking_screen2_label_second_name'); ?> *" required />
									                                           
                                                                                 <?php if ($this->validation->getError('secondname')) : ?>
	                                                                                <P>
                                                                                      <?= $this->validation->getError('secondname') ?>
                                                                                      </P>
	                                                                            <?php endif; ?> 
																			</div>
																		</div><div class="clearfix"></div>
																		<div class="col-md-6 col-sm-12 col-xs-12">
																			<div class="form-group">
																				
																				<input type="email" id="reg_email" class="form-control input-sm" name="email" value="<?php echo set_value('email'); ?>"
								                                               placeholder="<?php echo lang('app.language_site_booking_screen2_label_email_address'); ?> *" required />
								                                           
                                                                               <?php if ($this->validation->getError('email')) : ?>
	                                                                                <P>
                                                                                      <?= $this->validation->getError('email') ?>
                                                                                      </P>
	                                                                            <?php endif; ?> 
																			</div>
																		</div>
																		<div class="col-md-6 col-sm-12 col-xs-12">
																			<div class="form-group">
																				<input type="email" id="reg_confirm_email" class="form-control input-sm" name="confirm_email" value="<?php echo set_value('confirm_email'); ?>"
								                                               placeholder="<?php echo lang('app.language_site_booking_screen2_label_confirm_email_address'); ?> *" required />
								                                             
                                                                               <?php if ($this->validation->getError('confirm_email')) : ?>
	                                                                                <P>
                                                                                      <?= $this->validation->getError('confirm_email') ?>
                                                                                      </P>
	                                                                            <?php endif; ?>
																			</div>
																		</div><div class="clearfix"></div>
																		<div class="col-md-6 col-sm-12 col-xs-12">
																			<div class="form-group">
																				<input type="password" id="reg_password" name="password" value="<?php echo set_value('password'); ?>" class="form-control input-sm" placeholder="<?php echo lang('app.language_site_booking_screen2_label_password'); ?> *" required title="" />

										                                     
                                                                                 <?php if ($this->validation->getError('password')) : ?>
	                                                                                <P>
                                                                                      <?= $this->validation->getError('password') ?>
                                                                                 </P>
	                                                                            <?php endif; ?>
																			</div>
																		</div>
																		<div class="col-md-6 col-sm-12 col-xs-12">
																			<div class="form-group">
																				<input type="password" id="reg_confirm_password" value="<?php echo set_value('confirm_password'); ?>" class="form-control input-sm" name="confirm_password"
								                                               placeholder="<?php echo lang('app.language_site_booking_screen2_label_confirm_password'); ?> *" required />
                                                                               <?php if ($this->validation->getError('confirm_password')) : ?>
	                                                                                <P>
                                                                                      <?= $this->validation->getError('confirm_password') ?>
                                                                               </P>
	                                                                            <?php endif; ?>
																			</div>
																		</div><div class="clearfix"></div>
                                                                        <div class="col-md-6 col-sm-12 col-xs-12">
                                                                            <div class="form-group">
                                                                                <div class="input_checkbox">
                                                                                    <span>
                                                                                    <?php $id = 'id="terms_signup" class="checkbox-inline"'; ?><?php echo form_checkbox('terms', '1', set_checkbox('terms', '1'), $id); ?></span>
                                                                                    <label for= "terms_signup"><?php echo lang('app.language_site_booking_screen_m2_terms_service'); ?></label>
                                                                                </div>
                                                                                
                                                                                <p style="display:none;color:red;" class="text-center" id="custom_error_accept_terms"><?php echo lang('app.language_site_booking_screen4_accept_terms'); ?></p>
                                                                                <?php if ($this->validation->getError('terms')) : ?>
	                                                                                <P>
                                                                                      <?= $this->validation->getError('terms') ?>
                                                                                </P>
	                                                                            <?php endif; ?>
                                                                            </div>
                                                                        </div>
																		<div class="col-md-6 col-sm-12 col-xs-12 text-right text_center">
																			<button type="submit" id="reg_submit" name="register_submit" value="test"  class="btn btn-sm btn-continue text-right mt8"><?php echo lang('app.language_site_booking_continue_btn'); ?></button>
																		</div>
																	</div>
																	 <?php echo form_close(); ?>
																</div>
																<div id="login_tab"  role="tabpanel" class="tab-pane login fade <?php echo ($this->session->get('tablogin')) ? 'active in' : ''; ?>" >
																	<?php echo form_open('site/signup-o-login', array('role' => 'form bv-form', 'class' => '', 'autocomplete' => 'off', 'id' => 'login_form_changes', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>
																	<div class="form-group">
                                                                        <div><?php echo lang('app.language_site_booking_screen_m2_login_text'); ?></div>
                                                                    </div>

																	<div class="row">
																		<div class="col-md-6 col-sm-12 col-xs-12">
																			<div class="form-group">
																				<input type="text"  id="log_email" class="form-control input-sm" name="username"
										                                               placeholder=" <?php echo lang('app.language_site_booking_screen2_label_email_address'); ?> *" value="<?php echo set_value('username'); ?>" />
										                
                                                                                <?php if ($this->validation->getError('username')) : ?>
	                                                                                <P>
                                                                                      <?= $this->validation->getError('username') ?>
                                                                                </P>
	                                                                            <?php endif; ?> 
																			</div>
																		</div>
																		<div class="col-md-6 col-sm-12 col-xs-12">
																			<div class="form-group">
																				<input type="password"  id="log_password"  name="password_"  class="form-control input-sm" placeholder=" <?php echo lang('app.language_site_booking_screen2_label_password'); ?> *" />
                                        										
                                                                                <?php if ($this->validation->getError('password_')) : ?>
	                                                                                <P>
                                                                                      <?= $this->validation->getError('password_') ?>
                                                                                </P>
	                                                                            <?php endif; ?>  
																			</div>
																		</div><div class="clearfix"></div>
                                                                        <div class="col-md-6 col-sm-12 col-xs-12">
                                                                            <div class="form-group">
                                                                                <div class="input_checkbox">
                                                                                    <span>
                                                                                        <?php $id1 ='id="terms_login" class="checkbox-inline" '; ?><?php echo form_checkbox('termsL', '1', set_checkbox('termsL', '1'), $id1); ?>
                                                                                    </span>
                                                                                    <label for="terms_login"><?php echo lang('app.language_site_booking_screen_m2_terms_service'); ?>
                                                                                    </label>
                                                                                </div>
                                                                                <p style="display:none;color:red;" class="text-center" id="custom_error_accept_terms"><?php echo lang('app.language_site_booking_screen4_accept_terms'); ?></p>
                                                                              
                                                                            </div>
                                                                        </div>																		
																		<div class="col-md-6 col-sm-12 col-xs-12 text-right text_center mt10-xs">
																			<button type="submit" id="login_submit" name="login_submit" value="test" class="btn btn-sm btn-continue text-right mt8"><?php echo lang('app.language_site_booking_continue_btn'); ?></button>
																			&nbsp;&nbsp;<img alt="loading" class="loading" style="display:none;" src="<?php echo base_url('public/images/loading.gif'); ?>">
																		</div>
                                                                        
																	</div>
                                                                    <div class="row">
                                                                        <div class="col-md-6 col-sm-12 col-xs-12 text_center forgot-text">
																			<a class="forgot_password mt10" href="<?php echo site_url('login/forgot_password'); ?>"><?php echo lang('app.language_site_booking_screen2_forgot_password'); ?></a>
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
							<!-- tab content -->
							</div>
						</div>
			</div>
		</div>
	</div>       
    </div>
</div>

<!-- Modal -->
<div class="modal fade"  id="termsModal" tabindex="-1"  role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom: none;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="bg-lightblue">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-12">
                                <h1 class="blueTitle">Terms and conditions</h1>
                                <hr class="aqua" />
                                    <?= view('terms-conditions'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

<?php include 'footer-booking.php'; ?>

<script>
    var clientId = "";
    var apiKey = "";
    var scopes = 'email profile';
    var google = false;

    $(function () {
        $("#reg_password").focus(function(){
            $(".password_condition").show();
	});

        $(window).keydown(function (event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });
		
		// SignUp submit with enter key
		$("#signup_form_changes input").keydown(function(event) {
			var inputfocus = $('#reg_firstname,#reg_secondname,#reg_email,#reg_confirm_email,#reg_password,#reg_confirm_password').is(':focus');
			if(inputfocus){
				if (event.which == 13) {
					event.preventDefault();
					$("#reg_submit").click();
				}
			}
		});
                
                // Login submit with enter key
		$("#login_form_changes input").keydown(function(event) {
			var inputfocus = $('#log_email,#log_password').is(':focus');
			if(inputfocus){
				if (event.which == 13) {
					event.preventDefault();
					$("#login_submit").click();
				}
			}
		});
		
        //validate each inputs for validation 
        $("#reg_firstname,#reg_secondname,#reg_email,#reg_confirm_email,#reg_password,#reg_confirm_password").on('change',function () {
                            obj = {};
                            obj.field  = $(this).attr('id');
                            obj.name  = $(this).attr('name');
                            //password validation
                            if($(this).attr('name') == 'confirm_password'){
                               obj['password']  = $('#reg_password').val();
                            }
                            //email validation
                            if($(this).attr('name') == 'confirm_email'){
                               obj['email']  = $('#reg_email').val();
                            }
                            obj[$(this).attr('name')]  = $(this).val();
                            $.ajax({
                                type: 'POST',
                                url: "<?php echo site_url('site/valdate_individually_data'); ?>",
                                data: obj,
                                dataType: "json",
                                success: function (fdata) {
                                    $('#'+obj.field).nextAll('span,p').remove();
                                    if (fdata.success == 1) {
                                       $('#'+obj.field).nextAll('span,p').remove();
                                    } else {
                                       $('#'+obj.field).after(fdata.errors[obj.name]);
                                    }
                                },
                                failure: function (errMsg) {
                                    alert(errMsg);
                                    return false;
                                }
                            });
        });

    })
</script>

<script>

    $('a[href$="#signup_tab"]').click(function(){
    	$("#signup_help").show();
    	$("#login_help").hide();   	
    });
    
    $('a[href$="#login_tab"]').click(function(){
    	$("#signup_help").hide();
    	$("#login_help").show();
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