<div class="bg-footer">
    <div class="container-fluid">
        <div class="footer-box">
            <div class="row">
                <div class="col-sm-4 col-xs-12">
                    <a class="footer_link" href="#"><img class="footer_logo" src="<?php echo base_url('/public/images/footer-logo.png'); ?>"></a>
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

<?php 
//initialization of request
$this->request = \Config\Services::request();

?>

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script type="text/javascript" src="<?php echo base_url('/public/js/moment.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('/public/js/bootstrap.min.js'); ?>"></script>

<script type="text/javascript" src="<?php echo base_url('/public/js/bootstrap-multiselect.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('/public/js/bootstrap-datetimepicker.js'); ?>"></script>	

<!-- IE10 viewport hack for Surface/desktop Windows 8 bug 
<script src="<?php //echo base_url();   ?>public/js/ie10-viewport-bug-workaround.js"></script>-->
<!-- Bootstrap validator Plugin JavaScript -->
<script type="text/javascript" src="<?php echo base_url('/public/js/bootstrapValidator/bootstrapValidator.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('/public/js/bootstrapValidator/en.js'); ?>"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ajaxtransport-xdomainrequest/1.0.3/jquery.xdomainrequest.min.js"></script>
<script src="<?php echo base_url('/public/js/enscroll.min.js'); ?>"></script>

<script type="text/javascript">

// Under 16 checkbox
$(document).ready(function() {       
     $('.u13_learner_ids').prop('checked', false); 
});

$(function () {

    //tooltips 
    $('[data-toggle="tooltip"]').tooltip();
    //user icon click
    $('#userDropdown .dropdown-menu').on({
        "click": function (e) {
            e.stopPropagation();
        }
    });

    //setting tabs active
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
       
        var target = $(e.target).attr("id") // activated tab
        obj = {};
        obj.tab_active = target;
        obj.segment = '<?php echo $this->request->uri->getSegment(4) ?>';
        obj.order_string_query = '<?php echo isset($_GET['order_list_search']) ? 1 : 0; ?>';
        if(obj.segment !='' || obj.order_string_query == 1){
         $('.tab-content').html('<p style="text-align:center;font-size:25px;margin-top:50px;"> Loading... </p>');   
        }else{
         $('.tab-content').show();
        }
        $.ajax({
            url: "<?php echo site_url('school/set_session_tab'); ?>",
            data: obj,
            dataType: 'json',
            type: 'POST',
            success: function (result) {
                //alert(result.active);
                if(result.segment > 0 || result.order_string_query == 1)
                window.location.href = "<?php echo site_url('school/dashboard'); ?>";
            }


        });
    });


    var my_dist = '';
    $('#list_dist_table').on('change', ':radio', function () {
        set_test($(this).val());
    });
    function set_test(newval) {
        my_dist = newval;
    }
    $('#_set_default_btn').on('click', function () {
        $('.loading').show();
        if (my_dist != '') {
            obj = {};
            obj.dist_id = my_dist;
            $.ajax({
                url: "<?php echo site_url('school/makedefault'); ?>",
                data: obj,
                dataType: 'json',
                type: 'POST',
                success: function (result) {
                    $('.loading').hide();
                    if (result.success) {
                        window.location.reload();
                    }

                }
            });

        } else {
            $('.loading').hide();
        }
    });

    $('#logoutbtn').click(function (event) {
        event.preventDefault();

        sitelogout();
    });
	
	//TDS Results download validation
	$('#results_tds').bootstrapValidator({
		locale: "",
		fields: {
            product_type: {
                validators: {
                    notEmpty: {
                        message: '<?php echo lang('app.language_school_result_product_type_validate'); ?>'
                    }
                }
            },
			result_startdate: {
                validators: {
                    notEmpty: {
                        message: '<?php echo lang('app.language_school_result_start_date_validate'); ?>'
                    }
                }
            },
			result_enddate: {
                validators: {
                    notEmpty: {
                        message: '<?php echo lang('app.language_school_result_end_date_validate'); ?>'
                    }
                }
            },
			result_type: {
                validators: {
                    notEmpty: {
                        message: '<?php echo lang('app.language_school_result_type_validate'); ?>'
                    }
                }
            },
		}, onSuccess: function (e) {
			
		}	
		
	});

    // order test modal validation
    $('#order1_form').bootstrapValidator({
        locale: "",
        fields: {
            order_name: {
                validators: {
                    notEmpty: {
                        message: '<?php echo lang('app.language_school_order_validation_name'); ?>'
                    },
                    stringLength: {
                        max: 50,
                        message: '<?php echo lang('app.language_school_order_validation_name_between'); ?>'
                    }
                }
            },
            type_of_token: {
                validators: {
                    notEmpty: {
                        message: '<?php echo lang('app.language_school_order_validation_token_type'); ?>'
                    }
                }
            },
            number_of_tests: {
                validators: {
                    regexp: {
                        regexp: /^([1-9][0-9]{0,2}|999)$/i,
                        message: '<?php echo lang('app.language_school_order_validation_no_of_tests_between'); ?>'
                    },
                    notEmpty: {
                        message: '<?php echo lang('app.language_school_order_validation_no_of_tests'); ?>'
                    }


                }
            },
            is_supervised: {
                validators: {
                    notEmpty: {
                        message: '<?php echo lang('app.language_school_order_validation_final_test_arrangement'); ?>'
                    }
                }
            },
            order_desc: {
                validators: {
                    notEmpty: {
                        message: '<?php echo lang('app.language_school_order_validation_desc'); ?>'
                    },
                    stringLength: {
                        max: 150,
                        message: '<?php echo lang('app.language_school_order_validation_desc_between'); ?>'
                    }
                }
            }
        }, onSuccess: function (e) {

            // setup some local variables
            var $form = $('#order1_form');

            // Let's select and cache all the fields
            var $inputs = $form.find("input, select, button, textarea");

            // Serialize the data in the form
            var serializedData = $form.serialize();

            // Let's disable the inputs for the duration of the Ajax request.
            // Note: we disable elements AFTER the form data has been serialized.
            // Disabled form elements will not be serialized.
            $inputs.prop("disabled", true);

            // Fire off the request to /form.php
            $.ajax({
                url: '<?php echo site_url('school/ordertest'); ?>',
                type: "post",
                data: serializedData,
                success: function (result) {
                    console.log('success');
                    $(".is_supervised").show();
                    //console.log($('#order_name').val());
                    $('#ordername').text($('#order_name').val());
                    //console.log($('#type_of_token').val());
                    //WP-1060
                  var typeoftoken;
                    if($('#type_of_token').val()){
                        if($('#type_of_token').val() == 'catslevel'){
                           typeoftoken = 'CATs Step level (this code will give access to a full CATs Step level, including a CATs Step course)';
                        }else if($('#type_of_token').val() == 'cats_core'){
                             typeoftoken = 'CATs Step Core (this code will give access to a full CATs Step Core, including a CATs Step course)'; 
                        }else if($('#type_of_token').val() == 'cats_higher'){
                           typeoftoken = 'CATs Step Higher (this code will give access to a full CATs Step Higher, including a CATs Step course)'; 
                        }else if($('#type_of_token').val() == 'cats_core_or_higher'){
                          typeoftoken = 'CATs Step Core or CATs Step Higher (this code will give access to a full CATs Step level, including a CATs Step course)'; 
                        }else if($('#type_of_token').val() == 'speaking_test'){
                          typeoftoken = 'Speaking test (this code will give access to a test only. Important: this code type does not give access to a CATs Step course)'; 
                        }else{
                           typeoftoken = 'StepCheck test (this code will give access to a test only. Important: this code type does not give access to a CATs Step course)';
                           $(".is_supervised").hide();
                        }
                    }
                    //WP-1060 ends
                    //var typeoftoken = ($('#type_of_token').val() == 'catslevel') ? 'CATs level (this code will give access to a full CATs level, including a CATs course)' : 'Benchmarking test (this code will give access to a test only. Important: this code type does not give access to a CATs course)';
                    $('#typeoftoken').text(typeoftoken);
                    //console.log($('#number_of_tests').val());
                    $('#no_of_test').text($('#number_of_tests').val());
                    var final_test_arrangement;
                    if($("input[name='is_supervised']:checked"). val() == 1){
                    	final_test_arrangement = "Supervised";
                    }else{
                    	final_test_arrangement = "Unsupervised";
                    }
                    
                    $('#final_test_arrangement').text(final_test_arrangement);
                    //console.log($('#order_desc').val());
                    $('#orderdesc').text($('#order_desc').val());
                    $('#order1modal').modal('toggle');
                    $("#order2modal").modal({backdrop: 'static', keyboard: false});
                }
            });

            //this section before submit
            e.preventDefault();
        }
    });



    // Sprint 26 - WP-756 - order2 modal box changes done	
    $('#order2_form').bootstrapValidator({
        locale: "",
        fields: {
            order_agree: {
                validators: {
                    notEmpty: {
                        message: '<?php echo lang('app.language_school_order_validation_terms'); ?>'
                    }
                }
            }
        }
    });

    // order modal 1 box click here changes
    var clicked = 0;
    $('#click_here').click(function () {
        clicked = 1;
        $('#order1modal').modal('toggle');
        $('#tab_ord').removeClass('active');
        $('#tab_dis').addClass('active');
        $('#distributors').addClass('active');
        $('#test_orders').removeClass('active');
    });

    // order modal 2 box change these details changes		
    $('#setting_change').click(function () {
        $('#order2modal').modal('toggle');
        $('#order1_submit').removeAttr("disabled");
        $("input[name='is_supervised']").removeAttr("disabled");
        $('.form-control').prop("disabled", false);
        $("#order1modal").modal({backdrop: 'static', keyboard: false});
    });

    $('#order1modal').on('shown.bs.modal', function() {
    	$(".final_test_arrangement").hide();
        $('#order1_form').bootstrapValidator('resetForm', true);
     });

    // modal box closing and redirecting to dashboard												
    $("#order1modal").on('hidden.bs.modal', function () {
       
        if ($('#order2modal').hasClass('in')) {
        } else {
            if (clicked == 0)
            {
                //window.location.reload();
            }
        }
    });

    $("#order2modal").on('hidden.bs.modal', function () {
        if ($('#order1modal').hasClass('in') || $('#order3modal').hasClass('in')) {
        } else {
            window.location.reload();
        }
    });

    $("#order3modal").on('hidden.bs.modal', function () {
        window.location.reload();
    });


    $("#card-form").hide();
    $('input[type="radio"]').click(function () {
        if ($(this).attr("value") == "card") {
            $("#card-form").show();
        }
        if ($(this).attr("value") == "paypal") {
            $("#card-form").hide();
        }
        if ($(this).attr("value") == "blue") {
            $(".box").not(".blue").hide();
            $(".blue").show();
        }
    });

    // enabling view and download tokens of order
    $("input:checkbox[name='radio_order[]']").prop('checked', false);
    $('#order_view').attr('disabled', 'true');
    $('#download_tokens').attr('disabled', 'true');
    $("input:checkbox[name='radio_order[]']").click(function () {
        var orderid = $('input[type="checkbox"][name="radio_order[]"]:checked').val();
        if (typeof orderid === "undefined") {
        } else {
            $('#order_view').removeAttr('disabled');
            $('#download_tokens').removeAttr('disabled');
        }
    });

    // getting tokens of order 
    $('#order_view').click(function () {
        orderid = $('input[type="checkbox"][name="radio_order[]"]:checked').val();
        if (typeof orderid === "undefined") {
        } else {
            if (orderid.length > 0) {
                window.location = "<?php echo site_url('school/tokenlist'); ?>" + '/' + orderid;
                $("input:checkbox[name='radio_order[]']").prop('checked', false);
            }
        }
    });

    // export tokens to excel
    $('#download_tokens').click(function () {
        orderid = $('input[type="checkbox"][name="radio_order[]"]:checked').val();
        if (typeof orderid === "undefined") {
        } else {
            if (orderid.length > 0) {
                window.location.href = "<?php echo site_url('school/export_tokens'); ?>" + '/' + orderid;
            }
        }
    });

    //WP-1374 view order and download codes hide
    $("#checkall_order").click(function() {
        $('#order_view').attr('disabled', 'disabled');
        $('#download_tokens').attr('disabled', 'disabled');
        $("input[name='radio_order[]']").not(this).prop('checked', this.checked);
    });
    //WP-1374 hide view order and download code more than 1 
    $('.radio_order').click(function() {
        $('#checkall_order').attr('checked', false);
        checked_order = $("input[name='radio_order[]']:checkbox:checked").length;
        if (checked_order == 1) {
            $('#order_view').removeAttr("disabled");
            $('#download_tokens').removeAttr("disabled");
        } else {
            $('#order_view').attr('disabled', 'disabled');
            $('#download_tokens').attr('disabled', 'disabled');
        }
    });

    // WP-1374 export codes learner details to excel in dashboard
    $('#export_orders').click(function() {
        var order_ids = [];
        var checkedOrderIds = $("input[name='radio_order[]']:checkbox:checked").map(function() {
            order_ids.push(this.value);
        }).get();
        if (order_ids != '') {
            var encoded_id = encodeURIComponent(order_ids);
            window.location.href = "<?php echo site_url('school/export_orders'); ?>" + '/' + encoded_id;
        }
    });
    // WP-1374 export details disable condition
    $("#checkall_order").click(function() {
        if ((document.getElementById('checkall_order').checked) == false) {
            $('#export_orders').attr('disabled', 'disabled');
        } else {
            $('#export_orders').removeAttr("disabled");
            $("input[name='radio_order[]']").not(this).prop('checked', this.checked);
        }
    });
    $('.radio_order').click(function() {
        $('#checkall_order').attr('checked', false);
        order_exort_btn = $("input[name='radio_order[]']:checkbox:checked").length;
        if (order_exort_btn == 0) {
            $('#export_orders').attr('disabled', 'disabled');
        } else {
            $('#export_orders').removeAttr("disabled");
        }
    });
    //events page
    $('.information').click(function () {
        $(this).toggleClass('fa-caret-down fa-caret-up');
        $(this).parent().siblings(".informationTxt").slideToggle();
    });

    //to show practice test results
    $('a.practice-test-button').click(function (ev) {
        ev.preventDefault();
        var fetch_id = $(this).data('id').split('|');
        $('#loading_modal' + fetch_id['2'] + fetch_id['1']).show();
        $.get("<?php echo site_url('school/gen_practicetest_result'); ?>" + '/' + fetch_id['1'] + '/' + fetch_id['2'], function (html) {
            // $('#loading_modal' + fetch_id['2'] + fetch_id['1']).hide();
            $('.practice-test-results .modal-title').text(fetch_id['0']);
            $('.practice-test-results .modal-body').html(html);
            $('.practice-test-results').modal('show', {backdrop: 'static'});
        });
    });
    
    //to show TDS practice test results
    $('a.practice-test-button-tds').click(function (ev) {
        ev.preventDefault();
        var fetch_id = $(this).data('id').split('|');
        $('#loading_modal' + fetch_id['1']).show();
        $.get("<?php echo site_url('school/gen_practicetest_result_tds'); ?>" + '/' + fetch_id['1'], function (html) {
            // $('#loading_modal' + fetch_id['2'] + fetch_id['1']).hide();
            $('.practice-test-results .modal-title').text(fetch_id['0']);
            $('.practice-test-results .modal-body').html(html);
            $('.practice-test-results').modal('show', {backdrop: 'static'});
        });
    });

    // export codes to excel in view tokens page
    /* $('#export_codes').click(function () {
        window.location.href = "<?php echo site_url('school/export_codes'); ?>" + '/' + '';
    }); */

    /*WP-1122 - Generate excel for allocated learners list in learner allocation page START*/
    $('#learner_allocation_export').click(function () {
        window.location.href = "<?php echo site_url('school/learner_allocation_export'); ?>" + '/' + '<?php echo $this->request->uri->getSegment(4); ?>';
    });

    function sitelogout() {
        window.location = "<?php echo site_url('school/logout'); ?>";
    }

    /*WP-1221 PDF Bulk download validation*/
    $("#results_tds input[name=product_type]").on('click',function (){
    	$("#result_csv, #result_pdf").attr('disabled', false);
    	$("#results_tds input[name=result_type]").attr('checked', false);
    	$(".result_csv, .result_pdf").css("color","#117cc2");
    	if($(this).val() != "stepcheck"){
    		//$('#results_tds').bootstrapValidator('revalidateField', 'result_type');
    		$("#result_csv").attr('disabled', true);
    		$(".result_csv").css("color","#aacae6");
        }  	
    });

    $('#type_of_token').on('change', function () {
    	$(".final_test_arrangement").hide();
        if($(this).val() === 'cats_core' || $(this).val() === 'cats_higher' || $(this).val() === 'cats_core_or_higher'){
			$(".final_test_arrangement").show();
			//$('#order1_form').bootstrapValidator('is_supervised', true);
        }
    });

});

</script>
<script>

    function showInactive($i)
    {
        $(".inact_class"+$i).toggle();
    }

    // $('.ministry-multi').multiselect({
    //     inheritClass: true,
    //     includeSelectAllOption: true,
    //     selectAllText: 'All',
    //     buttonWidth: '100%',
    //     buttonText: function (options, select) {
    //         if (options.length === 0) {
    //             return "<?php echo lang('app.language_ministry_dashboard_lbl_please_select'); ?>";
    //         } else if (options.length >= 1) {
    //             return options.length + " selected";
    //         }
    //     }
    // });
    /* WP-1122 - Filter & search implementation in learner allocation page START */
    $('.filter-multi').multiselect({
        inheritClass: true,
        buttonWidth: '100%',
        buttonText: function (options, select) {
            if (options.length === 0) {
                return "<?php echo lang('app.language_school_filter_please_select'); ?>";
            } else if (options.length >= 1) {
                return options.length + " <?php
         
                    echo 'selected';
                 ?>";
            }
        }
    });
    /* WP-1122 - Filter & search implementation in learner allocation page END */
   //new date time picker
    $(function () {
        
        var before_12_month_date = moment().add(-365, 'd').format("YYYY/MM/DD");
        var default_date = moment('2017-01-01').format("YYYY/MM/DD");
        var min_date = moment('2017-01-01').format("YYYY/MM/DD");
        if(moment(default_date).isAfter(moment(before_12_month_date)))
        {   
            min_date = before_12_month_date;
        }else if(moment(default_date).isBefore(moment(before_12_month_date))){
            default_date = before_12_month_date;
        }
        var today = new Date();
        $('#datetimepicker6').datetimepicker({
            defaultDate: default_date,
            minDate: min_date,
            maxDate: today,
            format: 'DD/MM/YYYY'
        });
        $('#datetimepicker7').datetimepicker({
            defaultDate: today,
            useCurrent: false,
            minDate: min_date,
            maxDate: today,
            format: 'DD/MM/YYYY'
        });
        $("#datetimepicker6").on("dp.change", function (e) {
            $('#datetimepicker7').data("DateTimePicker").minDate(e.date);
        });
        $("#datetimepicker7").on("dp.change", function (e) {
            $('#datetimepicker6').data("DateTimePicker").maxDate(e.date);
        });

        $('#datetimepicker8').datetimepicker({
            defaultDate : '01/01/2000',
            minDate:'01/01/1940',
            maxDate: today,
            format: 'DD/MM/YYYY'
        });
        $('#datetimepicker9').datetimepicker({
            defaultDate : today,
            useCurrent: false,
            minDate:'01/01/1940',
            maxDate: today,
            format: 'DD/MM/YYYY'
        });
        $("#datetimepicker8").on("dp.change", function (e) {
            $('#datetimepicker9').data("DateTimePicker").minDate(e.date);
        });
        $("#datetimepicker9").on("dp.change", function (e) {
            $('#datetimepicker8').data("DateTimePicker").maxDate(e.date);
        });
        $('#datetimepicker6,#datetimepicker7,#datetimepicker8,#datetimepicker9').datetimepicker().on('dp.show dp.update', function () {
            $(".datepicker-years .picker-switch").removeAttr('title')
                //.css('cursor', 'default')
                //.css('background', 'inherit')
                .on('click', function (e) {
                    e.stopPropagation();
                });
        });
		
		// date picker for U13 learner -dob
	    // current date + 7 days	
		var thirteenyears = moment().subtract(13, 'years').format("MM/DD/YYYY"); 
	    // current date 
		var max = moment().add(0, 'd').format("MM/DD/YYYY");
		
		$('#datetimepicker11').datetimepicker({
			format: 'L',
		});			
		$("#datetimepicker11").on("dp.change dp.show", function (e) {	
            $('#datetimepicker11').data("DateTimePicker").maxDate(max);
            $('#datetimepicker11').data("DateTimePicker").minDate(thirteenyears);
        });

		$('#result_startdate,#result_enddate').datetimepicker({
            useCurrent: false,
            minDate:'01/01/1940',
            maxDate: today,
            format: 'DD/MM/YYYY'
        });
		
		$('#result_startdate')
			.on('dp.change', function(e) {
				// Revalidate the date when user change it
				$('#results_tds').bootstrapValidator('revalidateField', 'result_startdate');
				var incrementDay = moment(new Date(e.date));
				var currentDay = moment(today);
                $('#result_enddate').data('DateTimePicker').minDate(incrementDay);
                incrementDay.add(30, 'days');
				if(incrementDay > currentDay){
					incrementDay = currentDay;
				}
				$('#result_enddate').data('DateTimePicker').maxDate(incrementDay);
                $(this).data("DateTimePicker").hide();
		});
		
		$('#result_enddate')
			.on('dp.change', function(e) {
				// Revalidate the date when user change it
				$('#results_tds').bootstrapValidator('revalidateField', 'result_enddate');
                $(this).data("DateTimePicker").hide();
		});
		
    });



</script>
<script>
    $(function () {
        var teacher_id;
		 var venue_id;
                 var check;
        teacher_id = $("input[name='teacher_id']:checked").val();
	   // venue_id = $("input[name='venue_id']:checked").val();
        
        $('input[type=radio][name=teacher_id]').change(function () {
            if (this.value != '') {
                teacher_id = this.value;
            } else {
                teacher_id = $("input[name='teacher_id']:checked").val();
            }
            return teacher_id;
        });
        
        var class_status;
        class_status = $("input[name='teacher_id']:checked").attr('data-classstatus');
        $('input[type=radio][name=teacher_id]').change(function () {
            if (this.value != '') {
                class_status = $(this).attr("data-classstatus");
            } else {
                class_status = $("input[name='teacher_id']:checked").attr('data-classstatus');
            }
            return class_status;
        });

        //add
        $('#addBtn').off('click').on('click', function (e) {
            e.preventDefault();
            $('.loading_main').show();
            $('#updateaddModal').modal('hide');
            var obj = {};
            obj.teacher_id = '';
            $("#addupdateModal").on("shown.bs.modal", function (e) {
                $(this).off('shown.bs.modal');
                var addBodyHtml = $(this).find(".modal-body");
                $.post("<?php echo site_url('School/teacher'); ?>", obj, function (data) {
                    addBodyHtml.html(data.html);
                    $('.loading_main').hide();
                    return false;
                }, "json");
                return false;

            });

        });

        // Under 13 add button form reset


		 $('#addBtnVenue').off('click').on('click', function (e) {
            e.preventDefault();
            $('.loading_main').show();
            $('#updateaddModalVenue').modal('hide');
            var obj = {};
            venue_id = $("input[name='venue_id']:radio:checked").val();
            obj.venue_id = '';
            $("#addupdateModalVenue").on("shown.bs.modal", function (e) {
                $(this).off('shown.bs.modal');
                var addBodyHtml = $(this).find(".modal-body");
                $.post("<?php echo site_url('school/venue'); ?>", obj, function (data) {
                    addBodyHtml.html(data.html);
                    $('.loading_main').hide();
                    use_bootstrap_validation('add'); //WP-1128
                    return false;
                }, "json");
               
                return false;

            });

        });
		
        //add_form_close
        //edit
        $('#editBtnVenue').off('click').on('click', function (e) {
            e.preventDefault();
            $('.loading_main').show();
            $('#addupdateModalVenue').modal('hide');
            var obj = {};
            venue_id = $("input[name='venue_id']:radio:checked").val();
            obj.venue_id = venue_id;
            $("#updateaddModalVenue").on("shown.bs.modal", function () {
                $(this).off('shown.bs.modal');
                var updateBodyHtml = $(this).find(".modal-body");
                $.post("<?php echo site_url('school/venue'); ?>", obj, function (data) {
                    updateBodyHtml.html(data.html);
                    $('.loading_main').hide();
                    use_bootstrap_validation('edit'); //WP-1128
                    return false;
                }, "json");
                return false;
            });
        });

        function  use_bootstrap_validation(a){
//venue form validation
 $('#'+a+'_venue_form').bootstrapValidator({

		locale : "",
		// List of fields and their validation rules
        fields: {
        	venue_name: {
                validators: {
                	 notEmpty: {
	                       
	                    },
                    stringLength: {
                        max: 100,
                    },
                 	// Place the remote validator in the last
                    remote: {
                    	message: '<?php echo lang('app.language_school_venue_already_available_msg'); ?>',
                        url: '<?php echo site_url('school/checkvenue'); ?>',
                        type: 'POST',
                        data: function(validator, $field, value) {
                            
                            // Return an object
                            return {
                               
                                'venue_id': ($('.'+a+'_venue_id').val()!='') ? $('.'+a+'_venue_id').val() : '',
                            };
                       }
                    }
                    
                }
            },
            address_line1: {
                validators: {
	                	notEmpty: {
		                       
		                },
	                    stringLength: {
	                        max: 100,
	                    }
            	}
            }, 
            address_line2: {
                validators: {
	                	stringLength: {
	                        max: 100,
	                    }
            	}
            },
            city: {
                validators: {
	                	notEmpty: {
		                       
		                },
	                	stringLength: {
	                        max: 100,
	                    },
	                    remote: {
	                    	message: '<?php echo lang('app.language_site_booking_screen1_select_city_error_two'); ?>',
	                        url: '<?php echo site_url('school/city_check'); ?>',
	                        type: 'POST'
	                    }
            	}
            }, 
            area_code: {
                validators: {
	                	notEmpty: {
		                       
		                },
	                	stringLength: {
	                        max: 50,
	                    }
            	}
            }, 
            firstname: {
                validators: {
	                	notEmpty: {
		                       
		                },
	                	stringLength: {
	                        max: 100,
	                    },
	                    remote: {
	                    	message: '<?php echo lang('app.language_school_venue_firstname_check_error'); ?>',
	                        url: '<?php echo site_url('school/firstname_check'); ?>',
	                        type: 'POST'
	                    }
            	}
            },  
            lastname: {
                validators: {
	                	notEmpty: {
		                       
		                },
		                string : {
		                       
		                },
	                	stringLength: {
	                        max: 100,
	                    },
	                    remote : {
		                    message : '<?php echo lang('app.language_school_venue_lastname_check_error'); ?>',
		                    url: '<?php echo site_url('school/lastname_check'); ?>',
		                    type: 'POST'
	                    }
            	}
            },
            email: {
                validators: {
                	notEmpty: {
	                       
	                },
                	emailAddress: {
	                       
	                    },
	                    stringLength: {
	                        max: 100,
	                    }
                }
            }, 
            contact_no: {
                validators: {
	                	notEmpty: {
		                       
		                },
	                	stringLength: {
	                        max: 100,
	                    }
            	}
            },               
            location_URL: {
                validators: {
                   
                    stringLength: {
                        max: 1000,
                    }
                }
            },
           
            notes: {
                validators: {
                    
                    stringLength: {
                        max: 1000,
                    }
                }
            }
            
        },
        onSuccess: function(e, data) {
        	e.preventDefault();	       	
	                
        	$('#add_submitBtn').attr("disabled", "disabled");
        	$('#edit_submitBtn').attr("disabled", "disabled");
        	          var id = $('#venue_id').val();
			$.ajax({
				type: "POST",
				url: $('#'+a+'_venue_form').attr('action'),
				data: $('#'+a+'_venue_form').serialize(),
				dataType: 'json',
				success: function (data)
				{
					if(data.success){
					  location.reload();
					}
				}
			});
			return false;
		}
});
}

        //Edit Teacher
        $('#editBtn').off('click').on('click', function (e) {
            e.preventDefault();
            $('.loading_main').show();
            $('#addupdateModal').modal('hide');
            var obj = {};
            obj.teacher_id = teacher_id;
            $("#updateaddModal").on("shown.bs.modal", function () {
                $(this).off('shown.bs.modal');
                var updateBodyHtml = $(this).find(".modal-body");
                $.post("<?php echo site_url('school/teacher'); ?>", obj, function (data) {
                    updateBodyHtml.html(data.html);
                    $('.loading_main').hide();
                    return false;
                }, "json");
                return false;
            });
        });

        alertify.defaults.glossary.title = "<?php echo lang('app.language_confirm_title'); ?>";
        alertify.defaults.transition = "fade";
        alertify.defaults.theme.ok = "btn btn-primary";
        alertify.defaults.theme.cancel = "btn btn-danger";
        alertify.defaults.theme.input = "form-control";


        //delete
        $('#deleteBtn').on('click', function (e) {
            e.preventDefault();
            
            if(class_status == 'active'){
                 alertify.alert('Warning!', '<?php echo lang('app.language_school_teacher_has_class_learners_delete_message'); ?>', function(){  });
                 return false;
             }else{
                $('.loading_main').show();
                $('#deleteBtn').attr('disabled', true);
                alertify.confirm('<?php echo lang('app.language_school_confirm_to_remove_teacher_from_institute'); ?>',
                    function () {
                        obj = {};
                        obj.teacher_id = teacher_id;
                        // alert(obj.teacher_id);
                        $.post("<?php echo site_url('school/remove_teacher'); ?>", obj, function (data) {
                            $('.loading_main').hide();
                            $('#deleteBtn').attr('disabled', false);
                            location.reload();
                        }, "json");
                    },
                    function () {
                        $('#deleteBtn').attr('disabled', false);

                    }).set('labels', {ok: '<?php echo lang('app.language_confirm_ok'); ?>', cancel: '<?php echo lang('app.language_confirm_cancel'); ?>'});
             }
        });
			
        $('#deleteBtnVenue').on('click', function (e) {
            e.preventDefault();
            $('.loading_main').show();
            obj = {};
            venue_id = $("input[name='venue_id']:radio:checked").val();
            obj.venue_id = venue_id;
            $.post("<?php echo site_url('school/check_event_venue'); ?>", obj, function (data) {
                if(data.success == 3){
                    alertify.alert('<?php echo lang('app.language_school_venue_deleted_failure_msg'); ?>');   
                }else{
                    if(data.success == 2){
                        var lang = '<?php echo lang('app.language_school_are_you_sure_venue_all_past_events'); ?>';
                    }else{
                        var lang = '<?php echo lang('app.language_distributor_are_you_sure_venue'); ?>';
                    }
                    alertify.confirm(lang,function () {
                        obj = {};
                        venue_id = $("input[name='venue_id']:radio:checked").val();
                        obj.venue_id = venue_id;
                        $.post("<?php echo site_url('school/remove_venue'); ?>", obj, function (data) {
                            $('.loading_main').hide();
                            location.reload();
                        }, "json");
                    },
                    function () {
                    }).set('labels', {ok: '<?php echo lang('app.language_confirm_ok'); ?>', cancel: '<?php echo lang('app.language_confirm_cancel'); ?>'});  
                }
            }, "json");  
        });

        $("#updateaddModal .modal").on("hidden.bs.modal", function (e) {
            var target = $(e.target);
            target.removeData('bs.modal').find(".modal-body").html('');
        });


        // CLEARABLE INPUT
        function tog(v) {
            return v ? 'addClass' : 'removeClass';
        }
        $(document).on('input', '.clearable', function () {
            $(this)[tog(this.value)]('x');
        }).on('mousemove', '.x', function (e) {
            $(this)[tog(this.offsetWidth - 18 < e.clientX - this.getBoundingClientRect().left)]('onX');
        }).on('touchstart click', '.onX', function (ev) {
            ev.preventDefault();
            $(this).removeClass('x onX').val('').change();
            window.location = "<?php echo site_url('school/dashboard'); ?>";
            //$(".results tbody tr").attr('visible','true').css({'display':'block'});
        });

        if ($('#search').val() != '') {
            $('#search').addClass('x');
        } else {
            $('#search').removeClass('x');
        }

        $('#clearBtn').on('click', function () {
            window.location = "<?php echo site_url('school/dashboard'); ?>";
        });

        $('#clearBtnResetTest').on('click', function () {
            window.location = "<?php echo site_url('school/dashboard'); ?>";
        });
		
		/* U13 learner */
        $('#u13learner_clearBtn').on('click', function () {
            window.location = "<?php echo site_url('school/dashboard'); ?>";
        });	
        
        $('#code_orders_clearBtn').on('click', function () {
            window.location = "<?php echo site_url('school/dashboard'); ?>";
        });
		
		$("#checkall").click(function () {
			$('#editBtn_u13').attr('disabled', 'disabled');	
			$("input[name='u13_learner_ids[]']").not(this).prop('checked', this.checked);
		});
        
 
		checked_u13learner = $("input[name='u13_learner_ids[]']:checkbox:checked").length; 
		if(checked_u13learner == 1) {
			$('#editBtn_u13').removeAttr("disabled");
		} else {
			$('#editBtn_u13').attr('disabled', 'disabled');
		}
		

		$('.u13_learner_ids').click(function() {
			$('#checkall').attr('checked', false);
			checked_u13learner = $("input[name='u13_learner_ids[]']:checkbox:checked").length;
			if(checked_u13learner == 1) {
				$('#editBtn_u13').removeAttr("disabled");
			} else {
				$('#editBtn_u13').attr('disabled', 'disabled');
			}
		});	
        
        
        // next level button
        $("#checkall").click(function () {

         if((document.getElementById('checkall').checked)==false) {
                $('#nextlevelbtn_u13').attr('disabled', 'disabled');
            }
            else{
            $('#nextlevelbtn_u13').removeAttr("disabled");
            $("input[name='u13_learner_ids[]']").not(this).prop('checked', this.checked);
            }
        });
        

 
        checked_u13learner = $("input[name='u13_learner_ids[]']:checkbox:checked").length; 
        if(checked_u13learner) {
            $('#nextlevelbtn_u13').removeAttr("disabled");
        } else {
            $('#nextlevelbtn_u13').attr('disabled', 'disabled');
        }
        

        $('.u13_learner_ids').click(function() {
            $('#checkall').attr('checked', false);
            checked_u13learner = $("input[name='u13_learner_ids[]']:checkbox:checked").length;
            if(checked_u13learner == 0) {
                $('#nextlevelbtn_u13').attr('disabled', 'disabled');
            }
            else{
                
                $('#nextlevelbtn_u13').removeAttr("disabled");
            }
        });  
        
           //Access details button
           $("#checkall").click(function () {

         if((document.getElementById('checkall').checked)==false) {
                $('#acsdetailsBtn_u13').attr('disabled', 'disabled');
            }
            else{
            $('#acsdetailsBtn_u13').removeAttr("disabled");
            $("input[name='u13_learner_ids[]']").not(this).prop('checked', this.checked);
            }
        });

checked_u13learner = $("input[name='u13_learner_ids[]']:checkbox:checked").length; 
        if(checked_u13learner) {
            $('#acsdetailsBtn_u13').removeAttr("disabled");
        } else {
            $('#acsdetailsBtn_u13').attr('disabled', 'disabled');
        }



 $('.u13_learner_ids').click(function() {
            $('#checkall').attr('checked', false);
            checked_u13learner = $("input[name='u13_learner_ids[]']:checkbox:checked").length;
            if(checked_u13learner == 0) {
                $('#acsdetailsBtn_u13').attr('disabled', 'disabled');
            }
            else{
                
                $('#acsdetailsBtn_u13').removeAttr("disabled");
            }
        }); 

        /*-----------------*/  


        //edit U13 learner
        $('#editBtn_u13').off('click').on('click', function (e) {   
            e.preventDefault();		
            $('.loading_main').show();
            $('#updateaddModal').find(".modal-body").html('');
            $('#updateaddModal').modal('show');		
            u13_id = $("input[name='u13_learner_ids[]']:checkbox:checked").val();
            var obj = {};
            obj.u13_learner_id = u13_id;			
			if(u13_id) {
				$("#updateaddModal").on("shown.bs.modal", function () {		
					$(this).off('shown.bs.modal');
					var updateBodyHtml = $(this).find(".modal-body");
					$.post("<?php echo site_url('school/u13learner'); ?>", obj, function (data) {
						updateBodyHtml.html(data.html);
						$('.loading_main').hide();
						return false;
					}, "json");
					return false;
				});			
			}			
        });

        $('#nextlevelbtn_u13').off('click').on('click', function (e) {  
                e.preventDefault();        
                $('.loading_main').show();
                $('#nextlevelModal').find(".modal-body").html('');
                $('#nextlevelModal').modal('show');
                var u13_ids = [] ;
                var checkedValues = $("input[name='u13_learner_ids[]']:checkbox:checked").map(function(){
                    u13_ids.push(this.value);
                }).get();
                var obj = {};
                obj.u13_learner_ids = u13_ids;            
                if(obj.u13_learner_ids != '') {
                    $("#nextlevelModal").on("shown.bs.modal", function () {     
                        $(this).off('shown.bs.modal');
                        var updateBodyHtml = $(this).find(".modal-body");
                        $.post("<?php echo site_url('school/nextlevel'); ?>", obj, function (data) {
                            updateBodyHtml.html(data.html);
                            $('.loading_main').hide();
                            return false;
                        }, "json");
                        return false;
                    });         
                }   
                return false;        
        });
        
        /* sprint-38 WP-1121-Test setup by the Institution starts*/
        //add events
        $('#addBtnEvents').off('click').on('click', function (e) {
          e.preventDefault();
            $('.loading_main').show();
            $('#addModalEvents').modal('hide');
           var obj = {};
           $("#addModalEvents").on("shown.bs.modal", function (e) {
               $(this).off('shown.bs.modal');
               var addBodyHtml = $(this).find(".modal-body");
                $.post("<?php echo site_url('school/addevent'); ?>", obj, function (data) {
                    addBodyHtml.html(data.html);
                    $('.loading_main').hide();
                    return false;
                }, "json");
                return false;

            });

        });
        
        //edit test event
        $('#editBtnEvents').off('click').on('click', function (e) {   
            e.preventDefault();		
            $('.loading_main').show();
             $('#updateModalEvents').modal('hide');
            event_id = $("input[name='radio_event']:radio:checked").val();
            var obj = {};
            obj.event_test_id = event_id;
			if(event_id) {
				$("#updateModalEvents").on("shown.bs.modal", function () {		
					$(this).off('shown.bs.modal');
					var updateEventBodyHtml = $(this).find(".modal-body");
					$.post("<?php echo site_url('school/editevent'); ?>", obj, function (data) {
						updateEventBodyHtml.html(data.html);
						$('.loading_main').hide();
						return false;
					}, "json");
					return false;
				});			
			}			
        });
        
        //delete events
	$('#deleteBtnEvents').on('click', function (e) {
            var event_status = $("input[name='radio_event']:checked").parent().find( ".eventDeleteHidden" ).val();
            e.preventDefault();
                $('.loading_main').show();
                $('#deleteBtnEvents').attr('disabled', true);
                if(event_status == 1){
                    alertify.alert('<?php echo lang('app.language_school_confirm_to_remove_past_event_from_institute'); ?>');
                    $('#deleteBtnEvents').attr('disabled', false);
                }else{
                    if(event_status == 3){
                      var test = '<?php echo lang('app.language_school_confirm_to_remove_event_from_institute'); ?>';
                    }else if(event_status == 2){
                      var test = '<?php echo lang('app.language_school_event_has_attached_learners_delete_message'); ?>';
                    }
                    alertify.confirm(test,
                        function () {
                            event_delete_id = $("input[name='radio_event']:checked").val();
                            obj = {};
                            obj.event_delete_id = event_delete_id;
                            $.post("<?php echo site_url('school/remove_event'); ?>", obj, function (data) {
                                $('.loading_main').hide();
                                $('#deleteBtnEvents').attr('disabled', true);
                                location.reload();                               
                            }, "json");
                        },
                        function () {
                            $('#deleteBtnEvents').attr('disabled', false);

                        }).set('labels', {ok: '<?php echo lang('app.language_confirm_ok'); ?>', cancel: '<?php echo lang('app.language_confirm_cancel'); ?>'});
                }

             
        });
        
         //To show eventlists based on checkbox in events
          $('input[type="checkbox"]').click(function () {
         if ($(this).attr('id') == "status_events") {
             if(this.checked){
                 status = $(this).val();
             }else{
                 status = 'inactive';
             }
            var obj = {};
            obj.status = status;
                $.post("<?php echo site_url('school/listevents'); ?>", obj, function (data) {
                      window.location.href = data.url;
                 }, "json");
         }
     });
        
        /* sprint-38 WP-1121-Test setup by the Institution ends*/
         //history of purchases
        $('.history_link').off('click').on('click', function (e) {   
            e.preventDefault();		
            $('.loading_history').show();
            $('#history_modal').find(".modal-body").html('');
            $('#history_modal').modal('show');			
            var obj = {};
            obj.u13_learner_id = this.id;			
			if(obj.u13_learner_id) {
				$("#history_modal").on("shown.bs.modal", function () {		
					$(this).off('shown.bs.modal');
					var updateBodyHtml = $(this).find(".modal-body");
                                        $.post("<?php echo site_url('school/purchased_history'); ?>", obj, function (data) {
						updateBodyHtml.html(data.html);
						$('.loading_history').hide();
						return false;
					}, "json");
					return false;
				});			
			}
            return false;
        });
    });

        //zendesk url change   
        
        $('.zendesk').click(function (e) {
        var zen_user_id = $('#zen_user_id').val();
        $.ajax({
				url: '<?php echo site_url('school/get_zend_desk_url_changing'); ?>',
				type: 'POST',
				data: {tier_id: zen_user_id},
				success: function (result) {
                    $(".zendesk").attr("href", result);

				}
			});

	});
    
</script>
<!--  WP-1128 Update venue success message -- function add from venue.php -->

<?php if($this->session->get('logged_tier1_userid') != "" && null !== $this->session->get('logged_tier1_userid')){ ?>
    <script>
        $('#tier_switch_account').click(function () {
            obj = {};
            obj.tier_user_id = <?php echo $this->session->get('logged_tier1_userid'); ?>;
            $.ajax({
                url: "<?php echo site_url('school/redirect_tier'); ?>",
                data: obj,
                dataType: 'json',
                type: 'POST',
                success: function (result) {
                    //alert(result.active);
                    if(result.success > 0 && result.tier_type == 'tier1'){
                        window.location.href = "<?php echo site_url('tier1/dashboard'); ?>";
                    } else if(result.success > 0 && result.tier_type == 'tier2') {
                        window.location.href = "<?php echo site_url('tier2/dashboard'); ?>";
                    }
                }
            });
        });
    </script>   
<?php }?>

</body>
</html>