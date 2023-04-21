<?php
use Config\MY_Lang;
use Config\Oauth;
$this->lang     = new \Config\MY_Lang();
$this->oauth    = new \Config\Oauth();
?>
<style>
body{
		background-color:#d3d3d3;
	}
</style>
<div class="bg-footer">
    <div class="container-fluid">
        <div class="footer-box">
            <div class="row">
                <div class="col-sm-4 col-xs-12">
                    <a class="footer_link" href="#"><img class="footer_logo" src="<?php echo base_url('public/images/footer-logo.png'); ?>" alt="Footer Logo"></a>
                </div>
                <div class="col-sm-3 col-xs-12">
                    <div class="footer-links">
                        <ul>
                           <li> <a href="<?php echo site_url('/'); ?>"><?php echo lang('app.footer_home'); ?></a></li>
                           <li> <a href="<?php echo site_url('pages/cats_stepcheck'); ?>"><?php echo lang('app.footer_stepcheck'); ?></a></li>
                           <li> <a href="<?php echo site_url('pages/cats_steps'); ?>"><?php echo lang('app.footer_steps'); ?></a></li>
                            <li> <a href="<?php echo site_url('pages/cats_solution'); ?>"><?php echo lang('app.footer_solution'); ?></a></li>

                        </ul>

                    </div>
                </div>
                <div class="col-sm-3 col-xs-12">
                    <div class="footer-links">
                        <ul>
                            <li><a href="<?php echo site_url('pages/privacy_notice'); ?>"><?php echo lang('app.footer_privacy'); ?></a></li>
                            <li><a href="<?php echo site_url('pages/terms_conditions'); ?>"><?php echo lang('app.footer_terms_and_conditions'); ?></a></li>
                            <li><a href="<?php echo site_url('pages/about_us'); ?>"><?php echo lang('app.about_us'); ?></a></li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<div class="footer_copyright">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 col-xs-12">
                <div class="copy-right">
                    <p>&copy; CATs Step <?php echo @date('Y'); ?></p>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="<?php echo base_url('public/js/jquery.min.js').'?'.  time(); ?>"></script>
<script src="<?php echo base_url('public/js/jquery.cookie.js').'?'.  time(); ?>"></script>
<script src="<?php echo base_url('public/js/fastclick.js').'?'.  time(); ?>"></script>
<!-- Bootstrap validator Plugin JavaScript -->
	<script src="<?php echo base_url('public/js/bootstrapValidator/bootstrapValidator.min.js').'?'.  time();?>"></script>
	<script src="<?php echo base_url('public/js/bootstrapValidator/'.$this->lang->lang().'.js').'?'.  time();?>"></script>
<script src="<?php echo base_url('public/js/bootstrap.min.js').'?'.  time(); ?>"></script>
<script src="<?php echo base_url('public/js/enscroll.min.js').'?'.  time();?>"></script>
<!-- for linear test -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/knockout/3.4.2/knockout-min.js"></script>



<script type="text/javascript">
           
            $(function(){
				// recommended-level page course listing css adjustment - start
				var curHeadHeight,initHeadHeight=0; 
				$('.course-details-cont .course-header').each(function(){
					curHeadHeight = $(this).innerHeight();
					if(initHeadHeight < curHeadHeight){
						initHeadHeight = curHeadHeight;
					} 
				});
				$('.course-details-cont .course-header').css('height',initHeadHeight);
				
				var curHeight,initHeight=0; 
				$('.course-details-cont .course-content ul').each(function(){
					curHeight = $(this).innerHeight();
					if(initHeight < curHeight){
						initHeight = curHeight;
					} 
				});
				$('.course-details-cont .course-content ul').css('height',initHeight);
				// recommended-level page course listing css adjustment - end        
                 $(window).load(function(){
                     $('#continue_btn').css('pointer-events','all');
                     $('#continue_btn').css('background','');
                     $('#continue_btn').removeClass('disabled');
                     $('#continue_btn').text("<?php echo lang('app.language_site_booking_continue_btn'); ?>");   
                 });
                 
               //start login to accept terms and conditions
                do_me_a_terms_checked_check();
                function do_me_a_terms_checked_check(fromwhere)
                {
                    if(fromwhere!=false && fromwhere == 'signup'){
                        var SignuptermsChecked = $('#terms_signup:checked').length > 0;
                        if(SignuptermsChecked){
                            $("#reg_submit").attr("disabled", false);
                        }else{
                            $("#reg_submit").attr("disabled", true);
                        }
                    }else if(fromwhere!=false && fromwhere == 'login'){
                        var LogintermsChecked = $('#terms_login:checked').length > 0;
                        if(LogintermsChecked){
                            $("#login_submit").attr("disabled", false);
                        }else{
                            $("#login_submit").attr("disabled", true);
                        }
                    }else{
                        var SignuptermsChecked = $('#terms_signup:checked').length > 0;
                        if(SignuptermsChecked){
                            $("#reg_submit").attr("disabled", false);
                        }else{
                            $("#reg_submit").attr("disabled", true);
                        }
                        var LogintermsChecked = $('#terms_login:checked').length > 0;
                        if(LogintermsChecked){
                            $("#login_submit").attr("disabled", false);
                        }else{
                            $("#login_submit").attr("disabled", true);
                        }
                    }
                }
                $("#terms_signup").change(function(){
                    do_me_a_terms_checked_check('signup');
                });
                $("#terms_login").change(function(){
                    do_me_a_terms_checked_check('login');
                });
                
                //end login to accept terms and conditions
		$('#token_number').keyup(function() {
			$(this).val($(this).val().toUpperCase());
		});		
		// payment page card form hide 	
		$("#carddetails").hide();			

        // payment through tokens changes starts		
		$("#token").hide();		
		if($("#token_number").val()) {
			$("#token").show();
			var $radios = $('input:radio[name=card]');
		}
		if($('input:radio[name=card]:checked').val() == 'paypal') {
			$("#token").hide();
		} else if($('input:radio[name=card]:checked').val() == 'token') {	
        }

		$('#level_form').bootstrapValidator({
			locale : "<?php echo $this->lang->lang(); ?>",						
			fields: {                                                       
				token_number: {		            	
					validators: {
						notEmpty: {		
							message: '<?php echo lang('app.language_school_token_validation_required'); ?>'					
						},                                    
						stringLength: {
							max: 9,
							message: '<?php echo lang('app.language_school_token_validation'); ?>'
						}
					}
				}			            		           
			},
			onSuccess: function(e) {
				//this section before submit
				e.preventDefault();                            
				$('.loading').hide();
				var $form = $(e.target),
				fv    = $(e.target).data('bootstrapValidator');
				// Then submit the form as usual
				fv.defaultSubmit();
			}					
		});			   

		$('#booknextcat').bootstrapValidator({
			locale : "<?php echo $this->lang->lang(); ?>",						
			fields: {                                                       
				agree_terms: {		            	
					validators: {
						notEmpty: {		
							message: '<?php echo lang('app.language_school_order_validation_terms'); ?>'					
						},
					}
				}			            		           
			}						
		});			
        $('input[type="radio"][name="card"]').click(function(){
			if($(this).val() == 'token') {
				console.log($(this).val());
				$("#token_number").val('');		
				$("#token").show();					
			} else if($(this).val() == 'paypal') {
				$('#level_form').bootstrapValidator('disableSubmitButtons', false)  // Enable the submit buttons
                .bootstrapValidator('resetForm', true);
				$("#token").hide();			
			} else {
				$("#token").hide();	
			}
		});
        // payment through tokens changes ends		
		
		$("input[name$='card']").click(function() {
				var test = $(this).val();
				$("#carddetails").hide();
			});
                //tooltips 
                  $('[data-toggle="tooltip"]').tooltip();   
                 //login form validation
		 $('#signup_form').bootstrapValidator({
			locale : "<?php echo $this->lang->lang(); ?>",
				
		        fields: {
                        email :{
		            	validators: {
                                identical: {
                                    field: 'confirm_email',
                                    message: "<?php echo lang('language_site_booking_screen2_label_email_confirm_email_mismatch'); ?>"
                                },
		                    }
		                    },
                        confirm_email :{
		            	 validators: {
                            identical: {
                                field: 'email',
                                    message: "<?php echo lang('language_site_booking_screen2_label_confirm_email_email_mismatch'); ?>"
                                },         
		                     }
		                  },
                           
		            password: {
		            	validators: {
                            identical: {
                                field: 'confirm_password',
                                message: "<?php echo lang('language_site_booking_screen2_label_password_confirm_password_mismatch'); ?>"
                                        },
                        stringLength: {
					                min: 8,
                                    max: 20
				                }
		                    }
		                },
                        confirm_password: {
		            	validators: {
                                identical: {
                                    field: 'password',
                                     message: "<?php echo lang('language_site_booking_screen2_label_confirm_password_password_mismatch'); ?>"
                                        },
                                stringLength: {
					                    min: 8,
                                        max: 20
				                    }   
		                        }
		                    }
		                },
                        onSuccess: function(e) {
                            //this section before submit
                             e.preventDefault();
                            $('.loading').hide();
                            var $form = $(e.target),
                            fv    = $(e.target).data('bootstrapValidator');
                            // Then submit the form as usual
                            fv.defaultSubmit();
                        }
			
                   }); 
                 //login form validation
		 $('#login_form').bootstrapValidator({
			locale : "<?php echo $this->lang->lang(); ?>",
				
		        fields: {
                    username: { 
		            },
		            password_: {
		            	validators: {
                            stringLength: {
					        min: 8,
                            max: 20
				        } 
		                }
		            }
		        },
                onSuccess: function(e) {
                    //this section before submit
                        e.preventDefault();
                        $('.loading').hide();
                        var $form = $(e.target),
                        fv    = $(e.target).data('bootstrapValidator');
                        fv.defaultSubmit();
                        }
                   });
                });
            
            $('#logoutbtn').click(function (event) {
                    event.preventDefault();
                    webclientlogout();
                    sitelogout();
            });
            
            function webclientlogout() {
                $.ajax({
                    url: '<?php echo $this->oauth->catsurl('webclient_url'); ?>api/ssologout.php?callback=?',
                    type: "GET",
                    data: {data: JSON.stringify({WCLTK: '<?php echo @$encryptedToken; ?>'})},
                    async: false,
                    dataType: "jsonp",
                    crossDomain: true,
                    jsonpCallback: "logoutResults",
                    success: function (result) {
                        var obj = $.parseJSON(result);
                        if (obj.code) {
                            localStorage.removeItem("sso");
                        }
                    }
                });
            }
            function sitelogout() {
                window.location = "<?php echo site_url('site/logout'); ?>";
            }
            </script>
</body>
</html>
