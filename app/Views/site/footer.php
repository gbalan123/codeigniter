<?php
use Config\MY_Lang;
use Config\Oauth;
$this->myconfig = new \Config\MY_Lang(); 
$this->lang = new \Config\MY_Lang(); 
$this->oauth = new \Config\Oauth();
?>

<?php if (isset($recent_type_of_token['type_of_token']) && isset($recent_type_of_token['questionnaire_done']) && substr($recent_type_of_token['type_of_token'],0,12) != "benchmarking"  && intval($recent_type_of_token['questionnaire_done']) == 0): ?>
<div class="modal fade modal_questionaire modal_quest_xs" id="language-selection-modal" role="dialog" tabindex="-1"
    aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content contact-form">
            <form style="width:100%" method="post"
                action="<?php echo site_url('site/set_language_without_questionaire'); ?>" id="language_box_setting">
                <div class="modal-header">
                    <h4 class="modal-title">
                        <?php
                        echo (isset($recent_type_of_token['type_of_token']) && (($recent_type_of_token['type_of_token'] == 'catslevel') || ($recent_type_of_token['type_of_token'] == 'cats_core') || ($recent_type_of_token['type_of_token'] == 'cats_higher') || ($recent_type_of_token['type_of_token'] == 'cats_core_or_higher'))) ?
                                lang('app.lsetting_modal_header_title') : lang('app.lsetting_modal_header_title_benchmark');
                        ?>
                    </h4>
                </div>
                <div class="modal-body">
                    <p style="margin-bottom: 20px;">
                        <?php
                        echo (isset($recent_type_of_token['type_of_token']) && (($recent_type_of_token['type_of_token'] == 'catslevel') || ($recent_type_of_token['type_of_token'] == 'cats_core') || ($recent_type_of_token['type_of_token'] == 'cats_higher') || ($recent_type_of_token['type_of_token'] == 'cats_core_or_higher'))) ?
                                lang('app.lsetting_modal_header_desc') : lang('app..lsetting_modal_header_desc_benchmark');
                        ?>
                        <img id="loading_la" style="display: none;"
                            src="<?php echo base_url('/public/images/loading.gif'); ?>" />
                    </p>

                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-4 col-sm-6 col-xs-12 text-right">
                                <!--<div class="form-group">-->
                                <label class="control-label"><?php echo lang('app.lsetting_label_language'); ?> <span
                                        class="required">*</span></label>
                                <!--</div>-->
                            </div>
                            <div class="col-sm-6">
                                <!--<div class="form-group">-->
                                <select name="mylanguage" class="form-control mylanguage">
                                    <option value=""><?php echo lang('app.lsetting_please_select'); ?></option>
                                    <?php foreach ($all_languages as $language): ?>
                                    <option value="<?php echo base64_encode($language->language_id); ?>"
                                        <?php echo ( intval($language->language_id) == intval(@$profile[0]->language_id)) ? 'selected' : ''; ?>>
                                        <?php echo json_decode('"' . $language->name . '"'); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="error-language"></div>
                                <!--</div>-->
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-4 col-sm-6 col-xs-12 text-right">
                                <label class="control-label"><?php echo lang('app.lsetting_label_gender'); ?> <span
                                        class="required">*</span></label>
                            </div>
                            <div class="col-sm-6">
                                <select name="mygender" class="form-control mygender">
                                    <option value=""><?php echo lang('app.lsetting_please_select'); ?></option>
                                    <option
                                        <?php echo (isset($profile[0]->gender) && $profile[0]->gender == 'F') ? 'selected' : '' ?>
                                        value="<?php echo base64_encode('F'); ?>">
                                        <?php echo lang('app.lsetting_label_gender_female'); ?></option>
                                    <option
                                        <?php echo (isset($profile[0]->gender) && $profile[0]->gender == 'M') ? 'selected' : '' ?>
                                        value="<?php echo base64_encode('M'); ?>">
                                        <?php echo lang('app.lsetting_label_gender_male'); ?></option>
                                    <option
                                        <?php echo (isset($profile[0]->gender) && $profile[0]->gender == 'U') ? 'selected' : '' ?>
                                        value="<?php echo base64_encode('U'); ?>">
                                        <?php echo lang('app.lsetting_label_gender_not_known'); ?></option>
                                    <option
                                        <?php echo (isset($profile[0]->gender) && $profile[0]->gender == 'U') ? 'selected' : '' ?>
                                        value="<?php echo base64_encode('U'); ?>">
                                        <?php echo lang('app.lsetting_label_gender_not_applicable'); ?></option>
                                </select>
                                <div class="error-gender"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-4 col-sm-6 col-xs-12 text-right">
                                <label class="control-label"><?php echo lang('app.lsetting_label_dob'); ?> <span
                                        class="required">*</span></label>
                            </div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="form-inline">
                                        <div class="col-sm-3 col-xs-3">
                                            <input type="number" name="mydob[]" class="form-control mydob"
                                                placeholder="DD" min="1" max="31" maxlength="2"
                                                onkeyup="this.value = minmax(this.value, 1, 31)"
                                                value="<?php echo (isset($profile[0]->dob) && $profile[0]->dob != '0') ? date('d', $profile[0]->dob) : '' ?>">
                                        </div>
                                        <div class="col-sm-3 col-xs-3">
                                            <input type="number" name="mydob[]" class="form-control mydob" min="1"
                                                max="12" placeholder="MM" maxlength="2"
                                                onkeyup="this.value = minmax(this.value, 1, 12)"
                                                value="<?php echo (isset($profile[0]->dob) && $profile[0]->dob != '0') ? date('m', $profile[0]->dob) : '' ?>">
                                        </div>
                                        <div class="col-sm-6 col-xs-6">
                                    
                                            <?php
                                                $yearData = range(1900, date('Y'));
                                                rsort($yearData);
                                                ?>
                                            <select name="mydob[]" class="form-control mydob">
                                                <?php
                                                    foreach ($yearData as $year):
                                                            if (isset($profile[0]->dob) && $profile[0]->dob != '0' && date('Y', $profile[0]->dob) == $year) {
                                                                    $selected = $year;
                                                            } elseif ($profile[0]->dob == '0') {
                                                                    $selected = '2000';
                                                            }
                                                            ?>
                                                <option
                                                    <?php echo (isset($selected) && $selected == $year) ? 'selected' : ''; ?>
                                                    value="<?php echo $year; ?>"><?php echo $year; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="error-dob"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="clearfix"></div>
                <div class="modal-footer modal_quest_foot btn_center">
                    <div class="col-sm-12 text-right">
                        <button class="btn btn-sm btn-continue" type="submit"
                            id="language_box_btn"><?php echo lang('app.language_dashboard_continue'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<!-- end language pop up -->

<!--End Questionarie2 pop up-->
<div class="bg-footer">
    <div class="container-fluid">
        <div class="footer-box">
            <div class="row">
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <a class="footer_link" href="#"><img class="footer_logo"
                            src="<?php echo base_url('public/images/footer-logo.png'); ?>"></a>
                </div>
                <div class="col-md-3 col-sm-4 col-xs-12">
                    <div class="footer-links">
                        <ul>
                            <li> <a
                                    href="<?php echo site_url("/"); ?>"><?php echo lang('app.footer_home'); ?></a>
                            </li>
                            <li> <a
                                    href="<?php echo site_url("/pages/cats_stepcheck"); ?>"><?php echo lang('app.footer_stepcheck'); ?></a>
                            </li>
                            <li> <a
                                    href="<?php echo site_url("/pages/cats_steps"); ?>"><?php echo lang('app.footer_steps'); ?></a>
                            </li>
                            <li> <a
                                    href="<?php echo site_url("/pages/cats_solution"); ?>"><?php echo lang('app.footer_solution'); ?></a>
                            </li>

                        </ul>

                    </div>
                </div>
                <div class="col-md-3 col-sm-4 col-xs-12">
                    <div class="footer-links">
                        <ul>
                            <li><a
                                    href="<?php echo site_url("/pages/privacy_notice"); ?>"><?php echo lang('app.footer_privacy'); ?></a>
                            </li>
                            <li><a
                                    href="<?php echo site_url("/pages/terms_conditions"); ?>"><?php echo lang('app.footer_terms_and_conditions'); ?></a>
                            </li>
                            <li><a
                                    href="<?php echo site_url("/pages/about_us"); ?>"><?php echo lang('app.language_site_about_us'); ?></a>
                            </li>
                            <!-- <li><a
                                    href="<?php //echo $this->myconfig->site_url("/site/contact"); ?>"><?php //echo lang('app.contact_us'); ?></a>
                            </li> -->

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

<script type="text/javascript" src="<?php echo base_url(); ?>/public/js/bootstrap.min.js"></script>
<!-- Bootstrap validator Plugin JavaScript -->
<script type="text/javascript" src="<?php echo base_url('public/js/bootstrapValidator/bootstrapValidator.min.js'); ?>">
</script>
<script type="text/javascript"
    src="<?php echo base_url('public/js/bootstrapValidator/'.$this->request->getLocale().'.min.js'); ?>"></script>
<script type="text/javascript"
    src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ajaxtransport-xdomainrequest/1.0.3/jquery.xdomainrequest.min.js">
</script>
<script src="<?php echo base_url('public/js/enscroll.min.js'); ?>"></script>
<script src="<?php echo base_url('public/js/bootstrap-multiselect.js'); ?>"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>/public/js/Chart.min.js"></script>


<script type="text/javascript">
	function minmax(value, min, max)
	{
		if (parseInt(value) < min || isNaN(parseInt(value)))
			return false;
		else if (parseInt(value) > max)
			return false;
		else
			return value;
	}
	// iOS check...ugly but necessary
	if (navigator.userAgent.match(/iPhone|iPad|iPod/i)) {

		$('.modal').on('show.bs.modal', function () {
			// Position modal absolute and bump it down to the scrollPosition
			$(this)
					.css({
						position: 'absolute',
						marginTop: $(window).scrollTop() + 'px',
						bottom: 'auto'
					});
			// Position backdrop absolute and make it span the entire page
			// Also dirty, but we need to tap into the backdrop after Boostrap 
			// positions it but before transitions finish.
			//
			setTimeout(function () {
				$('.modal-backdrop').css({
					position: 'absolute',
					top: 0,
					left: 0,
					width: '100%',
					height: Math.max(
							document.body.scrollHeight, document.documentElement.scrollHeight,
							document.body.offsetHeight, document.documentElement.offsetHeight,
							document.body.clientHeight, document.documentElement.clientHeight
							) + 'px'
				});
			}, 0);
		});
	}

	$(function () {
		// Questionarie2 starts
		$("#questionnaire2-selection-modal").on("show", function () {
			$("body").addClass("modal-open");
			$('body').css('overflow', 'hidden');
		}).on("hidden", function () {
			$("body").removeClass("modal-open");
			$('body').css('overflow', 'none');
		});
		//trigger questionarie2 setup box in modal language  starts
		$(window).load(function () {
			$('#questionnaire2-selection-modal').modal({
				show: true,
				backdrop: 'static',
				keyboard: false
			});
		});

		// trigger other option text box
		$('#questionnaire2-selection-modal .myCountry').on('change', function () {
			if (this.value == 251) {
				$('#questionnaire2-selection-modal .myCountryOther').show();
			} else {
				$('#questionnaire2-selection-modal .myCountryOther').hide();
			}
		});

		$('#questionnaire2-selection-modal .myLanguage').on('change', function () {
			if (this.value == 8) {
				$('#questionnaire2-selection-modal .myLanguageOther').show();
			} else {
				$('#questionnaire2-selection-modal .myLanguageOther').hide();
			}
		});

		
		$('#questionnaire2-selection-modal .myFemaleJob').on('change', function () {
			if (this.value == 11) {
				$('#questionnaire2-selection-modal .myFemaleJobOther').show();
			} else {
				$('#questionnaire2-selection-modal .myFemaleJobOther').hide();
			}
		});

		$('#questionnaire2-selection-modal .myMaleJob').on('change', function () {
			if (this.value == 11) {
				$('#questionnaire2-selection-modal .myMaleJobOther').show();
			} else {
				$('#questionnaire2-selection-modal .myMaleJobOther').hide();
			}
		});

		var text_max = 200;
		$('#comments_remaining').html(text_max + ' / 200');

		$('#myComments').keyup(function () {
			var text_length = $('#myComments').val().length;
			if (text_length > 150) {
				$('#comments_remaining').addClass('chars_low');
			} else {
				$('#comments_remaining').removeClass('chars_low');
			}
			var text_remaining = text_max - text_length;

			$('#comments_remaining').html(text_remaining + ' / 200');
		});

		<?php if (isset($questionaireTwoExist) && intval($questionaireTwoExist) == 1) { ?>
			var commentsLength = $('#myComments').val().length;
			if (commentsLength > 150) {
				$('#comments_remaining').addClass('chars_low');
			}
			var textRemaining = text_max - commentsLength;
			$('#comments_remaining').html(textRemaining + ' / 200');
		<?php } ?>	
	// Questionarie2 ends
		$("#language-selection-modal").on("show", function () {
			$("body").addClass("modal-open");
			$('body').css('overflow', 'hidden');
		}).on("hidden", function () {
			$("body").removeClass("modal-open");
			$('body').css('overflow', 'none');
		});
		//trigger language setup box in modal language  starts
		$(window).load(function () {
			$('#language-selection-modal').modal({
				show: true,
				backdrop: 'static',
				keyboard: false
			});
		});
		$("#language_box_setting").submit(function (e) {
			$('#language_box_btn').attr('disabled', true);
			$('#loading_la').show();
			$.ajax({
				type: "POST",
				url: $(this).attr('action'),
				data: $(this).serialize(),
				dataType: 'json',
				success: function (data)
				{
					$('#language_box_btn').attr('disabled', false);
					$('#loading_la').hide();
					if (data.success) {
						$('.mylanguage').val(data.selected);
						$('.error-language').hide();
						$('.error-spm').hide();
						$('.error-dep').hide();
						$('.error-lstudy').hide();

						$('.error-muet').hide();
						$('.error-faculty').hide();
						$('.error-univdepartment').hide();
						$('.error-univprogramme').hide();

						$('.error-gender').hide();
						$('.error-dob').hide();
						$('.error-reason').hide();
						$('.error-noofyear').hide();
						$('.error-accessEng').hide();
						$('.error-learningPerweek').hide();
						$('.error-diffinEng').hide();
						$('#language-selection-modal').modal('hide');
					} else {
						$('.error-language').html(data.errors.mylanguage).find('p').css('color', 'red').show();
						$('.error-spm').html(data.errors.myspmresult).find('p').css('color', 'red').show();
						$('.error-dep').html(data.errors.mydepartment).find('p').css('color', 'red').show();
						$('.error-lstudy').html(data.errors.mylevelofstudy).find('p').css('color', 'red').show();

						$('.error-muet').html(data.errors.mymuetresult).find('p').css('color', 'red').show();
						$('.error-faculty').html(data.errors.myfaculty).find('p').css('color', 'red').show();
						$('.error-univdepartment').html(data.errors.myunivdepartment).find('p').css('color', 'red').show();
						$('.error-univprogramme').html(data.errors.myunivprogramme).find('p').css('color', 'red').show();

						$('.error-gender').html(data.errors.mygender).find('p').css('color', 'red').show();
						$('.error-dob').html(data.errors.mydob).find('p').css('color', 'red').show();
					}
					$('.error-reason').html(data.errors.myreason).find('p').css('color', 'red').show();
					$('.error-noofyear').html(data.errors.mynoofyear).find('p').css('color', 'red').show();
					$('.error-accessEng').html(data.errors.myaccessenglish).find('p').css('color', 'red').show();
					$('.error-learningPerweek').html(data.errors.mylearninginweek).find('p').css('color', 'red').show();
					$('.error-diffinEng').html(data.errors.mydifficultinenglish).find('p').css('color', 'red').show();

				}
			});

			e.preventDefault();
		});
		$(".mydob").keydown(function (e) {
			// Allow: backspace, delete, tab, escape, enter and .
			if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
					// Allow: Ctrl+A, Command+A
							(e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
							// Allow: home, end, left, right, down, up
									(e.keyCode >= 35 && e.keyCode <= 40)) {
						// let it happen, don't do anything
						return;
					}
					// Ensure that it is a number and stop the keypress
					if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
						e.preventDefault();
					}
				});

		//ends language setttings

		localStorage.removeItem('oldtoken');
		localStorage.removeItem('primary_user');
		localStorage.removeItem('primary_password');
		//for dashboard
		$('.EnScroll_CERT').enscroll({
			showOnHover: false,
			verticalScrolling: true,
			verticalTrackClass: 'track',
			verticalHandleClass: 'handle',
		});
		//tooltips 
		$('[data-toggle="tooltip"]').tooltip();
		//user icon click
		$('#userDropdown .dropdown-menu').on({
			"click": function (e) {
				e.stopPropagation();
			}
		});


		//Refer cats to a friend
		$('#referal_form').bootstrapValidator({
			locale: "<?php echo $this->lang->lang(); ?>",
			fields: {
				your_name: {
					validators: {
						regexp: {
							regexp: /^[a-z\s]+$/i,
							message: 'The name can consist of alphabetical characters and spaces only'
						},
						notEmpty: {
						}
					}
				},
				friends_name: {
					validators: {
						regexp: {
							regexp: /^[a-z\s]+$/i,
							message: 'The name can consist of alphabetical characters and spaces only'
						},
						notEmpty: {
						}
					}
				},
				friends_email: {
					validators: {
						notEmpty: {
						},
						emailAddress: {
						},
						stringLength: {
							max: 100
						}
					}
				},
				your_message: {
					validators: {
						notEmpty: {
						},
						stringLength: {
							min: 20,
							max: 150
						}
					}
				}
			}

		});

		$("#referalModal").on('hidden.bs.modal', function () {
		});

		//start learn btn 
		$(document).on("click", ".start-btn > a, .btn-orange > p >a, .read-more-cont > h1 > a ,.start-learning-cont > .btn-main, .start-learning-cont > .btn-slider, #get_started", function(e){
			localStorage.removeItem('oldcountry');
			localStorage.removeItem('oldcity');
			localStorage.removeItem('result');
			window.location = "<?php echo site_url('site/is-cat-available-for-me'); ?>"
		});

		$('#logoutbtn').click(function (event) {
			event.preventDefault();
			webclientlogout();
			sitelogout();
		});


		function toggleChevron(e) {
			$(e.target)
					.prev('.panel-heading')
					.find("i.indicator")
					.toggleClass('fa-chevron-down fa-chevron-up');
		}
		function togglePlus(e) {
			$(e.target)
					.prev('.panel-heading')
					.find("i.faq")
					.toggleClass('fa-plus fa-minus');
		}

		var l1 = $("#faq > .panel > .panel-heading h4 a");
		var l2 = $("#faq > .panel > .panel-collapse .panel-heading h4 a");
		$(l1).append("<i class='indicator fa fa-chevron-down'></i>");
		$(l2).append("<i class='faq fa fa-plus'></i>");

		$('#faq').on('hidden.bs.collapse', toggleChevron);
		$('#faq').on('shown.bs.collapse', toggleChevron);
		$('#faq').on('hidden.bs.collapse', togglePlus);
		$('#faq').on('shown.bs.collapse', togglePlus);


		var $active = $('#accordion .panel-collapse.in').prev().addClass('active');
		$active.find('a').append('<span class="fa fa-chevron-down pull-right"></span>');
		$('#accordion .panel-heading').not($active).find('a').prepend('<span class="fa fa-chevron-up pull-right"></span>');
		$('#accordion').on('show.bs.collapse', function (e)
		{
			$('#accordion .panel-heading.active').removeClass('active').find('.fa').toggleClass('fa-chevron-up fa-chevron-down');
			$(e.target).prev().addClass('active').find('.fa').toggleClass('fa-chevron-up fa-chevron-down');
		});
		$('#accordion').on('hide.bs.collapse', function (e)
		{
			$(e.target).prev().removeClass('active').find('.fa').removeClass('fa-chevron-down').addClass('fa-chevron-up');
		});


		//to show practice test results
		$('a.practice-test-button').click(function (ev) {
			ev.preventDefault();

			var fetch_id = $(this).data('id').split('|');
			$('#loading_modal' + fetch_id['2'] + fetch_id['1']).show();
			$.get("<?php echo site_url('site/gen_practicetest_result'); ?>" + '/' + fetch_id['1'] + '/' + fetch_id['2'], function (html) {
				$('#loading_modal' + fetch_id['2'] + fetch_id['1']).hide();
				$('.practice-test-results .modal-title').text(fetch_id['0']);
				$('.practice-test-results .modal-body').html(html);
				$('.practice-test-results').modal('show', {backdrop: 'static'});
			});
		});

		//to show practice test results
		$('button.practice-test-button').click(function (ev) {
			ev.preventDefault();

			var fetch_id = $(this).data('id').split('|');
			$('#loading_modal' + fetch_id['2'] + fetch_id['1']).show();
			$.get("<?php echo site_url('site/gen_practicetest_result'); ?>" + '/' + fetch_id['1'] + '/' + fetch_id['2'], function (html) {
				$('#loading_modal' + fetch_id['2'] + fetch_id['1']).hide();
				$('.practice-test-results .modal-title').text(fetch_id['0']);
				$('.practice-test-results .modal-body').html(html);
				$('.practice-test-results').modal('show', {backdrop: 'static'});
			});
		});

	    //to show TDS practice test results
		$('button.practice-test-button-tds').click(function (ev) {
			ev.preventDefault();

			var fetch_id = $(this).data('id').split('|');
			$('#loading_modal' + fetch_id['1']).show();
			$.get("<?php echo site_url('site/gen_practicetest_result_tds'); ?>" + '/' + fetch_id['1'], function (html) {
				$('#loading_modal' + fetch_id['1']).hide();
				$('.practice-test-results .modal-title').text(fetch_id['0']);
				$('.practice-test-results .modal-body').html(html);
				$('.practice-test-results').modal('show', {backdrop: 'static'});
			});
		});

	});

	//to show final test results - Higher
	$('a.final_result_higher').click(function (ev) {
		ev.preventDefault();
		var fetch_id = $(this).attr('id');
		var token = $(this).attr('token');
		var display = "popup";
		$.get("<?php echo site_url('site/higher_certificate'); ?>" + '/' + fetch_id + '/' + token + '/' + display, function (html) {
			$('#final-test-higher .modal-body').html(html);
			$('#final-test-higher').modal('show', {backdrop: 'static'});
		});
	});
													
													//to show final test results WP-1279 - core & tds process
	$('a.final_result_core').click(function (ev) {
		ev.preventDefault();
		var fetch_id = $(this).attr('id');
		var display = "popup";
		$.get("<?php echo site_url('site/core_certificate'); ?>" + '/' + fetch_id + '/' + display, function (html) {
			$('#final-test-core .modal-body').html(html);
			$('#final-test-core').modal('show', {backdrop: 'static'});
		});
	});

	//WC-15 - Divert learners to app version for non-desktop access
	$('a.mobile-popup-link').click(function(ev) {
		ev.preventDefault();
		var fetch_id = $(this).attr('id');
		$.get("<?php echo site_url('site/play_store_link'); ?>" + '/' + fetch_id, function(html) {
			$('#mobile-playstore-link-modal .modal-body').html(html);
			$('#mobile-playstore-link-modal').modal('show', {
				backdrop: 'static'
			});
		});
	});

	//WP-1202 AJAX call to set latest Practice test token in PHP session
	$(document).on("click", ".practice_test1_token", function () {
		$.ajax({
			url: '<?php echo site_url('site/set_tdstoken_sessions'); ?>',
			type: 'POST',
			dataType: "JSON",
			data: {latest_tds_token: $("#practice_test1_token").val()},
			success: function (result) {
				console.log('Practise test 1 token set');
			}
		});
	});
	$(document).on("click", ".practice_test2_token", function () {
		$.ajax({
			url: '<?php echo site_url('site/set_tdstoken_sessions'); ?>',
			type: 'POST',
			dataType: "JSON",
			data: {latest_tds_token: $("#practice_test2_token").val()},
			success: function (result) {
				console.log('Practise test 2 token set');
			}
		});
	});
	$(document).on("click", ".final_test_token", function () {
		$.ajax({
			url: '<?php echo site_url('site/set_tdstoken_sessions'); ?>',
			type: 'POST',
			dataType: "JSON",
			data: {latest_tds_token: $("#final_test_token").val()},
			success: function (result) {
				console.log('Practise test 2 token set');
			}
		});
	});
	//WP-1202 AJAX call to set latest Practice test token in PHP session END

</script>

<script>

	$(function () {
		// courses page course listing css adjustment - start
		var curHeadHeight, initHeadHeight = 0;
		$('.course-details-cont .course-header').each(function () {
			curHeadHeight = $(this).innerHeight();
			if (initHeadHeight < curHeadHeight) {
				initHeadHeight = curHeadHeight;
			}
		});
		$('.course-details-cont .course-header').css('height', initHeadHeight);

		var curHeight, initHeight = 0;
		$('.course-details-cont .course-content ul').each(function () {
			curHeight = $(this).innerHeight();
			if (initHeight < curHeight) {
				initHeight = curHeight;
			}
		});
		$('.course-details-cont .course-content ul').css('height', initHeight);
		// courses page course listing css adjustment - end

		//Webclient changes to login when click Opencourse button --START
		$(document).on("click", "#courseBtnWc", function () {
			
			var recentProductId = parseInt(<?php echo $this->session->get('recent_product_id'); ?>);
			var higherCourseArray = <?php echo json_encode($this->session->get('higher_type_ids'), JSON_NUMERIC_CHECK) ?>;

			if (higherCourseArray.indexOf(recentProductId) == -1) {
				webclientCoreHigherURL = '<?php echo $this->oauth->catsurl('webclient_url'); ?>';
			} else {
				webclientCoreHigherURL = '<?php echo $this->oauth->catsurl('higherwebclient_url'); ?>';
			}

			$.ajax({
				url: webclientCoreHigherURL + 'api/ssologin.php?callback=?',
				type: 'GET',
				data: {data: JSON.stringify({WCLTK: '<?php echo $this->session->get('encryptedToken');?>', WCLTT: 'WEBCLIENT'})},
				dataType: 'jsonp',
				crossDomain: true,
				jsonpCallback: "logResults",
				success: function (result) {
					var obj = result;
					localStorage.setItem('sso', 'enable');
					if (obj.code) {
						window.location = webclientCoreHigherURL + "login.html";
					}
				}
			});
		});
		//Webclient changes to login --END

		$(window).keydown(function (event) {
			if (event.keyCode == 13) {
				event.preventDefault();
				return false;
			}
		});
		//start login to accept terms and conditions
		do_me_a_terms_checked_check();
		$("#terms_login").change(function () {
			do_me_a_terms_checked_check();
		});
		function do_me_a_terms_checked_check()
		{
			var LogintermsChecked = $('#terms_login:checked').length > 0;
			if (LogintermsChecked) {
				$('#custom_error').hide();
				$('#custom_error_accept_terms').hide();
				$("#login_form #username, #password").css('background', '#FFF');
				$("#login_form #username, #password, #login_submit, .social_media_login").attr("disabled", false);
			} else {
				$("#login_form #username, #password").css('background', '#e6e7e9');
				$("#login_form #username, #password, #login_submit, .social_media_login").attr("disabled", true);
			}
		}

		//login form top

		$("#login_form input").keydown(function (event) {
			var inputfocus = $('#username,#password').is(':focus');
			if (inputfocus) {
				if (event.which == 13) {
					event.preventDefault();
					$("#login_form").submit();
				}
			}
		});

		$("#login_form").submit(function (e) {
			e.preventDefault();
			$('#custom_error_accept_terms').hide();
			$('.loading').show();
			$('#login_submit').prop('disabled', true);
			$.ajax({
				type: "POST",
				url: $(this).attr('action'),
				data: $("#login_form").serialize() + "&login_submit=login_submit", // serializes the form's elements.
				timeout: 5000,
				dataType: 'json',
				success: function (data)
				{
					$('.loading').hide();
					if (data.success == '1') {
						if (data.login_type == 'zendesk') {
							$('#custom_error').html('');
							window.location = data.url;
						}else if (data.login_type == 'school') {
							$('#custom_error').html('');
							window.location = "<?php echo site_url('school/dashboard'); ?>";
						} else if (data.login_type == 'ministry') {
							$('#custom_error').html('');
							window.location = "<?php echo site_url('ministry/dashboard'); ?>";
						} else if (data.login_type == 'teacher') {
							$('#custom_error').html('');
							window.location = "<?php echo site_url('teacher/dashboard'); ?>";
						} else if (data.login_type == 'tier1') {
							$('#custom_error').html('');
							window.location = "<?php echo site_url('tier1/dashboard'); ?>";
						} else if (data.login_type == 'tier2') {
							$('#custom_error').html('');
							window.location = "<?php echo site_url('tier2/dashboard'); ?>";
						} else if (data.login_type == 'suspended') {
							$('#custom_error').show();
							$('#login_submit').attr('disabled', false);
							$('#custom_error').html(data.msg);
							//window.location = "<?php echo site_url('/'); ?>";
						} else {
							$('#custom_error').html('');
							window.location = "<?php echo site_url('site/dashboard'); ?>";
						}
					} else {
						$("#login_submit").prop('disabled', false);
						$('#custom_error').show();
						$('#custom_error').html(data.msg);
					}
				},
				error: function (jqXHR, exception, errorThrown)
				{
					var msg = '';
					if (jqXHR.status === 0) {
						msg = 'Trying to establish network connection. Please wait.';
					} else if (jqXHR.status == 404) {
						msg = 'Requested page not found. [404]';
					} else if (jqXHR.status == 500) {
						msg = 'Internal Server Error [500].';
					} else if (exception === 'parsererror') {
						msg = 'Requested JSON parse failed.';
					} else if (exception === 'timeout') {
						msg = 'Time out error.';
					} else if (exception === 'abort') {
						msg = 'Ajax request aborted.';
					} else {
						msg = 'Uncaught Error.\n' + jqXHR.responseText;
					}

					$('#custom_error').html(msg);
					$("#login_submit").prop('disabled', false);
					$('.loading').hide();
				}
			});
		});

		$("#loginModal").on('hidden.bs.modal', function () {
                        $("#terms_login"). prop("checked", false);
                        $('#custom_error').hide();
                        $("input#username,input#password").val("");
                        $("#login_submit").attr("disabled", true);
		});


		//enrolstatus
		var enrolstatus = <?php echo isset($enrolstatus) ? @$enrolstatus : "undefined"; ?>;
		if (typeof enrolstatus === "undefined") {
		} else {
			$pinfoout = $('#product_id').val().split('-');
			//alert( enrolstatus[$pinfoout[0]] )
			if (typeof enrolstatus[$pinfoout[0]] === "undefined") {
				$('.courseBtn_' + $pinfoout[0]).css('pointer-events', 'visible');
				$('.courseBtn_' + $pinfoout[0] + ' :input').removeAttr('disabled');

			} else {
				$('.moodle_course_id').removeAttr('value');
				$('.course_url').removeAttr('href');
				$('.courseBtn_' + $pinfoout[0] + ' :input').attr('disabled', true);
				$('.courseBtn_' + $pinfoout[0]).css('cursor', 'not-allowed');
			}
		}
		$('.coursename').text($("#product_name").text());
		$('#product_name').text($("#product_id option:selected").text());
		if ($("#product_id option:selected").val()) {
			course_selected = $("#product_id option:selected").val();
			$pinfo = course_selected.split('-');
			if ($pinfo['0'] != 'b') {
				$('#viewtestBtn').attr('href', "<?php echo site_url('site/view_test'); ?>" + '/' + $pinfo[0]);
				$mo_id = window.atob($pinfo[1]).split('>');
				$('.course_url').attr('href', "<?php $this->oauth->catsurl('moodle_course_url_by_name'); ?>" + 'Level ' + $mo_id[1]);
				$('.moodle_course_id').val($pinfo[1]);
			} else {
			}
		}
		def_prod_id = $('#highest_product_id').val();
		var productarray = new Array();
		productarray[1] = "Step Forward 1";
		productarray[2] = "Step Forward 2";
		productarray[3] = "Step Forward 3";
		productarray[4] = "Step Up 1";
		productarray[5] = "Step Up 2";
		productarray[6] = "Step Up 3";
		productarray[7] = "Step Ahead 1";
		productarray[8] = "Step Ahead 2";
		productarray[9] = "Step Ahead 3";
		productarray[10] = "Step Higher 1";
		productarray[11] = "Step Higher 2";
		productarray[12] = "Step Higher 3";
		$('#next_course').text(productarray[parseInt(def_prod_id)]);
		$('#product_id').change(function () {
			if (this.value) {
				$('#product_name').text($("#product_id option:selected").text());
				$('.product_name').text($("#product_id option:selected").text());
				$('.coursename').text($("#product_id option:selected").text());
				$pinfo = this.value.split('-');
				$('#recent_product_id').val($pinfo[0]);
				if ($pinfo['0'] != 'b' && $pinfo['0'] != 's') {
					$mo_id = window.atob($pinfo[1]).split('>');
					$('.course_url').attr('href', "<?php $this->oauth->catsurl('moodle_course_url_by_name'); ?>" + 'Level ' + $mo_id[1]);
					$('.moodle_course_id').val($pinfo[1]);


					if (typeof enrolstatus === "undefined") {
					} else {
						$('.courseBtn').attr('id', 'courseBtn_' + $pinfo[0]);
						if (typeof enrolstatus[$pinfo[0]] === "undefined") {
							$('.courseBtn_' + $pinfo[0]).css('pointer-events', 'visible');
							$('.courseBtn_' + $pinfo[0] + ' :input').removeAttr('disabled');
						} else {
							$('.moodle_course_id').removeAttr('value');
							$('.course_url').removeAttr('href');
							$('.courseBtn_' + $pinfo[0] + ' :input').attr('disabled', true);
							$('.courseBtn_' + $pinfo[0]).css('cursor', 'not-allowed');
							//$('.courseBtn_'+$pinfo[0]).css('pointer-events','none');
						}
					}

					$('#viewtestBtn').attr('href', "<?php echo site_url('site/view_test'); ?>" + '/' + $pinfo[0]);
					$('#dropdown_loading').show();
					$.ajax({
						url: '<?php echo site_url('site/onchange_products'); ?>',
						type: 'POST',
						dataType: "html",
						data: {
							user_id: '<?php echo $this->session->get('user_id'); ?>',
							productid: $pinfo[0],
						},
						success: function (result) {
							if (result == "") {
								window.location.replace('<?php echo site_url('site/'); ?>');
							}
							$('#dropdown_loading').hide();
							$("#bloc1-dash").replaceWith(result);
							$('.product_name').text($("#product_id option:selected").text());
							def_prod_id = $('#highest_product_id').val();
							$('#next_course').text(productarray[parseInt(def_prod_id)]);

						}
					});
				} else if ($pinfo['0'] == 's') {
					$.ajax({
						url: '<?php echo site_url('site/onchange_speaking_products'); ?>',
						type: 'POST',
						dataType: "html",
						data: {
							user_id: '<?php echo $this->session->get('user_id'); ?>',
							speakingid: $pinfo[1],
						},
						success: function (result) {
							if (result == "") {
								window.location.replace('<?php echo site_url('site/'); ?>');
							}
							$('#dropdown_loading').hide();
							$("#bloc1-dash").replaceWith(result);
							$('.product_name').text($("#product_id option:selected").text());

						}
					});
				} else {
					$.ajax({
						url: '<?php echo site_url('site/onchange_benchmark_products'); ?>',
						type: 'POST',
						dataType: "html",
						data: {
							user_id: '<?php echo $this->session->get('user_id'); ?>',
							benchmarkid: $pinfo[1],
						},
						success: function (result) {
							if (result == "") {
								window.location.replace('<?php echo site_url('site/'); ?>');
							}
							$('#dropdown_loading').hide();
							$("#bloc1-dash").replaceWith(result);
							$('.product_name').text($("#product_id option:selected").text());

						}
					});
				}

			} else {
				location.reload();
			}

		});

		$('.pdfdownload').click(function (e) {
			idval = $(this).attr("ID");
			pdf_val = $('#values' + idval).val();
			window.location = "<?php echo site_url('site/result_pdf/?q='); ?>" + pdf_val;
		});
		//dashboard form
		$('#monthname').text($("#month option:selected").text());
		$('#cityname').text($("#location option:selected").text());
		$('#getmonth').text($("#month option:selected").val());
		$('.booktest').click(function (e) {
			console.log($(this).attr('id'));
			$('#thirdpartyid').val($(this).attr('id'));
			e.preventDefault();
			$('#product_form').submit();
		});

		//events page
		$('.information').click(function () {
			$(this).toggleClass('fa-caret-down fa-caret-up');
			$(this).parent().siblings(".informationTxt").slideToggle();
		});

		$('.modal-content').click(function () {
			$('#my_access_english .btn-group.open').removeClass('open');
		});

		$('#my_access_english .btn-group').click(function () {
			$(this).addClass('open');
			event.stopPropagation();
		});

		//WP-1301 - Unsupervised leaener final test start button with alert message
		alertify.defaults.glossary.title = "<?php echo lang('app.language_confirm_title'); ?>";
	        alertify.defaults.transition = "fade";
	        alertify.defaults.theme.ok = "btn btn-primary";
	        alertify.defaults.theme.cancel = "btn btn-danger";
	        alertify.defaults.theme.input = "form-control";
		$(document).on("click", "#final_test_self", function (e) {
	            e.preventDefault();
	            $('.loading_main').show();
	            $('#final_test_self').attr('disabled', true);
            
            	alertify.confirm("<?php echo lang('app.language_dashboard_finaltest_self_start_confirm_text'); ?>",
                function () {
                	$.ajax({
    					url: '<?php echo site_url('site/set_tdstoken_sessions'); ?>',
    					type: 'POST',
    					dataType: "JSON",
    					data: {latest_tds_token: $("#final_test_token").val()},
    					success: function (result) {
    					console.log('Final test token set unsupervised');
    					}
    				});
            		window.open($('#final_test_url').val(), '_self');
                },
                function () {
                    $('#final_test_self').attr('disabled', false);
                }).set('labels', {ok: '<?php echo lang('app.language_confirm_ok'); ?>', cancel: '<?php echo lang('app.language_confirm_cancel'); ?>'});
        });
        
	})


</script>

    <!-- facebook login and signup -->
    <script>
    
        function webclientlogout() {
            var recentProductId = parseInt(<?php echo $this->session->get('recent_product_id');  ?>);
            var higherCourseArray = <?php echo json_encode($this->session->get('higher_type_ids'), JSON_NUMERIC_CHECK) ?>;

            var learnertype = "<?php echo $this->session->get('learnertype') ?>";

            if (learnertype === 'under13') {
                webclientCoreHigherURL = '<?php echo $this->oauth->catsurl('primarywebclient_url'); ?>';
            } else {
                if (isNaN(recentProductId) === false) {
                    if (higherCourseArray.indexOf(recentProductId) == -1) {
                        webclientCoreHigherURL = '<?php echo $this->oauth->catsurl('webclient_url'); ?>';
                    } else {
                        webclientCoreHigherURL = '<?php echo $this->oauth->catsurl('higherwebclient_url');?>';
                    }
                }
            }

            if (typeof (webclientCoreHigherURL) != 'undefined') {
                $.ajax({
                    url: webclientCoreHigherURL + 'api/ssologout.php?callback=?',
                    type: "GET",
                    data: {data: JSON.stringify({WCLTK: '<?php echo $this->session->get('encryptedToken'); ?>'})},
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

        }

        function sitelogout() {
            window.location = "<?php echo site_url('site/logout'); ?>";
        }

	$( "#forgot_password_form" ).submit(function( ) {
		$('#forgot_password_submit').css('pointer-events','none');
		$('#forgot_password_submit').css('opacity',0.5);
	});

	$( "#reset_password_form" ).submit(function( ) {
		$('#reset_password_submit').css('pointer-events','none');
		$('#reset_password_submit').css('opacity',0.5);
	});
    </script>

</body>
</html>