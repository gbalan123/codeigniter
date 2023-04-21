<div class="bg-footer">
    <div class="container-fluid">
        <div class="footer-box">
            <div class="row">
                <div class="col-sm-4 col-xs-12">
                    <a class="footer_link" href="#">
						<img class="footer_logo" src="<?php echo base_url('public/images/footer-logo.png'); ?>" alt="Footer Logo">
					</a>
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
<script type="text/javascript" src="<?php echo base_url('public/js/moment.min.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('public/js/bootstrap.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('public/js/bootstrap-multiselect.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('public/js/bootstrap-datetimepicker.js'); ?>"></script>
<!-- Bootstrap validator Plugin JavaScript -->
	<script type="text/javascript" src="<?php echo base_url('public/js/bootstrapValidator/bootstrapValidator.min.js');?>"></script>
	<script type="text/javascript" src="<?php echo base_url('public/js/bootstrapValidator/'.$this->lang->lang().'.js');?>"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ajaxtransport-xdomainrequest/1.0.3/jquery.xdomainrequest.min.js"> </script>
        <script src="<?php echo base_url('public/js/enscroll.min.js');?>"></script>

<script type="text/javascript">

    $(function () {
      
        //tooltips 
        $('[data-toggle="tooltip"]').tooltip();   
        //user icon click
        $('#userDropdown .dropdown-menu').on({
                "click":function(e){
              e.stopPropagation();
            }
        });

        $('#logoutbtn').click(function (event) {
            event.preventDefault();
           
            sitelogout();
        });			
     
		function sitelogout() {
			window.location = "<?php echo site_url('tier2/logout'); ?>";
		}
		
		$('input:radio[name=tier_id]').click(function () {
			var tier_id = $('input[type="radio"][name="tier_id"]:checked').val();
			if (typeof tier_id === "undefined") {
			} else {
				$('#continue_btn_tier3').removeAttr('disabled');
			}
		});
		
		$('input:radio[name=tier2_option]').click(function () {
			var tier2_option = $('input[type="radio"][name="tier2_option"]:checked').val();
			console.log('tier_id : '+tier2_option);
			if(tier2_option == 'administer'){
				$('#institute_list').show();
			}else{
				$('#institute_list').hide();
			}
		});
		
		$('#continue_btn_tier3').click(function () {
			var tier_id = $('input[type="radio"][name="tier_id"]:checked').val();
			obj = {};
			obj.tier_id = tier_id;
			$.ajax({
				url: "<?php echo site_url('tier2/redirect_school'); ?>",
				data: obj,
				dataType: 'json',
				type: 'POST',
				success: function (result) {
					//alert(result.active);
					if(result.success > 0)
					window.location.href = "<?php echo site_url('school/dashboard'); ?>";
				}


			});
		});
		
		$('#institute_clearBtn').on('click', function () {
            window.location = "<?php echo site_url('tier2/dashboard'); ?>";
        });
		
		$('input:radio[name=tier2_option]').click(function () {
			var selected_option = $('input[type="radio"][name="tier2_option"]:checked').val();
			if(selected_option == 'report'){
				$('#view_tier_report').show();
				$('#view_tier_report').removeAttr('disabled');
			}else {
				$('#view_tier_report').hide();
			}
			
			obj = {};
			obj.selected_option = selected_option;
			$.ajax({
				url: "<?php echo site_url('tier2/set_session'); ?>",
				data: obj,
				dataType: 'json',
				type: 'POST',
				success: function (result) {
					
				}
			});
		});
		
		var selected_tier_option = $('input[type="radio"][name="tier2_option"]:checked').val();
		console.log('selected_tier_option : '+selected_tier_option);
		if(selected_tier_option == 'report'){
			$('#view_tier_report').show();
			$('#view_tier_report').removeAttr('disabled');
		}else if(selected_tier_option == 'administer') {
			$('#view_tier_report').hide();
		}else{
			
		}
		
		$('#view_tier_report').on('click', function () {
            window.location = "<?php echo site_url('report/index'); ?>";
        });
		
		//login form validation
		$('#profile_form').bootstrapValidator({
			locale: "<?php echo $this->lang->lang(); ?>",
			onSuccess: function (e) {
				//this section before submit
				e.preventDefault();

				$('.loading').hide();
				var $form = $(e.target),
						fv = $(e.target).data('bootstrapValidator');
				// Then submit the form as usual
				fv.defaultSubmit();

			}

		});
		//change pass form validation
		$('#changepass_form').bootstrapValidator({
			locale: "<?php echo $this->lang->lang(); ?>",
			onSuccess: function (e) {
				//this section before submit
				e.preventDefault();

				$('.loading').hide();
				var $form = $(e.target),
						fv = $(e.target).data('bootstrapValidator');
				// Then submit the form as usual
				fv.defaultSubmit();

			}

		});

		//zendesk url change

		$('.zendesk').click(function (e) {
        var zen_user_id = $('#zen_user_id').val();
        $.ajax({
				url: '<?php echo site_url('tier2/get_zend_desk_url_changing'); ?>',
				type: 'POST',
				data: {tier_id: zen_user_id},
				success: function (result) {
                    $(".zendesk").attr("href", result);

				}
			});

		});

		
    });

</script>

</body>
</html>