<div class="bg-footer">
    <div class="container-fluid">
        <div class="footer-box">
            <div class="row">
                <div class="col-sm-4 col-xs-12">
                    <a class="footer_link" href="#"><img alt="" class="footer_logo" src="<?php echo base_url('public/images/footer-logo.png'); ?>"></a>
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
<script type="text/javascript" src="<?php echo base_url('public/js/moment.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('public/js/bootstrap.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('public/js/daterangepicker.js'); ?>"></script>


<!-- IE10 viewport hack for Surface/desktop Windows 8 bug  -->

<script type="text/javascript">
  
    $(function () {
        //onload - disable remove button in teacher
      $('#deleteLearnerBtn').attr('disabled', 'true');
       //tooltips 
        $('[data-toggle="tooltip"]').tooltip();
        //user icon click
        $('#userDropdown .dropdown-menu').on({
            "click": function (e) {
                e.stopPropagation();
            }
        });

        //setting tabs active
        $('.teacher-dashboard a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("id") // activated tab
            obj = {};
            obj.tab_active = target;
            $.ajax({
                url: "<?php echo site_url('teacher/set_session_tab'); ?>",
                data: obj,
                dataType: 'json',
                type: 'POST',
                success: function (result) {
                    window.location.href = "<?php echo site_url('teacher/dashboard'); ?>";
                }


            });
        });

        $('#logoutbtn').click(function (event) {
            event.preventDefault();

            sitelogout();
        });


        function sitelogout() {
            window.location = "<?php echo site_url('teacher/logout'); ?>";
        }
    });

</script>
<script>
    $(function () {

        var class_id;

        class_id = $("input[name='class_id']:checked").val();

        $('input[type=radio][name=class_id]').change(function () {
            if (this.value != '') {
                class_id = this.value;
            } else {
                class_id = $("input[name='class_id']:checked").val();
            }
            return class_id;
        });
        
        var class_candidates;
        class_candidates = $("input[name='class_id']:checked").attr('data-numberinclass');
        $('input[type=radio][name=class_id]').change(function () {
            if (this.value != '') {
                class_candidates = $(this).attr("data-numberinclass");
            } else {
                class_candidates = $("input[name='class_id']:checked").attr('data-numberinclass');
            }
            return class_candidates;
        });
        
        
        
        var learner_id;

        learner_id = $("input[name='learner_id']:checked").val();

        $('input[type=radio][name=learner_id]').change(function () {
            if (this.value != '') {
                learner_id = this.value;
            } else {
                learner_id = $("input[name='learner_id']:checked").val();
            }
            return learner_id;
        });
        
        
        var thirdparty_id;
        thirdparty_id = $("input[name='learner_id']:checked").attr('data-thirdparty-id');
        $('input[type=radio][name=learner_id]').change(function () {
            if (this.value != '') {
                thirdparty_id = $(this).attr("data-thirdparty-id");
            } else {
                thirdparty_id = $("input[name='learner_id']:checked").attr('data-thirdparty-id');
            }
            return thirdparty_id;
        });


        //add
        $('#addBtn').off('click').on('click',function (e) {
            e.preventDefault();
            $('.loading_main').show();
            $('#updateaddModal').find(".modal-body").html('');
            $('#updateaddModal').modal('hide');
            var obj = {};
            obj.class_id = '';
            $("#addupdateModal").on("shown.bs.modal", function (e) {
                    $(this).off('shown.bs.modal'); 
                    var  addBodyHtml = $(this).find(".modal-body");
                     $.post("<?php echo site_url('teacher/class_view'); ?>", obj, function (data) {
                         addBodyHtml.html(data.html);
                         $('.loading_main').hide();
                         return false;
                     }, "json");
                     return false;
               
            });

        });
		
        //show only active class
        $('input[type="checkbox"]').click(function(){
			if($(this).attr('name') === 'studentClassId1'){				
		 		$(this).attr('checked',true);
                    $('#deleteLearnerBtn').prop('disabled',$("input:checked" ).length == 0);
			}else{
				if($(this).prop("checked") == true){			
					$.ajax({
						url: '<?php echo site_url('teacher/dashboard'); ?>',
						type: 'POST',
						dataType: "json",
						data: {
							status: '<?php echo 'active'; ?>',
						},
						success: function (result) {
							location.reload();
						}
					});				
	            }
	            else if($(this).prop("checked") == false){
					$.ajax({
						url: '<?php echo site_url('teacher/dashboard'); ?>',
						type: 'POST',
						dataType: "json",
						data: {
							status: '<?php echo 'notactive'; ?>',
						},
						success: function (result) {
							location.reload();
						}
					});
	            }
            
			}
            
        });	
		
			  //to show practice test results
			$('a.practice-test-button').click(function (ev) {							
				ev.preventDefault();
				var fetch_id = $(this).data('id').split('|');
				$('#loading_modal' + fetch_id['2'] + fetch_id['1']).show();
				$.get("<?php echo site_url('teacher/gen_practicetest_result'); ?>" + '/' + fetch_id['1'] + '/' + fetch_id['2'], function (html) {
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
                            $.get("<?php echo site_url('teacher/gen_practicetest_result_tds'); ?>" + '/' + fetch_id['1'], function (html) {
                                $('.practice-test-results .modal-title').text(fetch_id['0']);
                                $('.practice-test-results .modal-body').html(html);
                                $('.practice-test-results').modal('show', {backdrop: 'static'});
                            });
                        });
        
        
			 $('#addLearnerBtn').off('click').on('click',function (e) {
		            e.preventDefault();
		            $('#addLearnerBtn').attr('disabled', true);
		            $('.loading_main').show();
		            var obj = {};
		            obj.learner_id = learner_id;
		            obj.teacherClassId = $('#teacher_class').val();
		            obj.thirdparty_id = thirdparty_id;
		            if(typeof obj.learner_id != 'undefined' && typeof obj.teacherClassId != 'undefined' && typeof obj.thirdparty_id != 'undefined'){
		                $.post("<?php echo site_url('teacher/dashboard'); ?>", obj, function (data) {
		                      location.reload();
		                 }, "json");
		             }else{
		                location.reload();
		             }
		            
		        });


        //edit
        $('#editBtn').off('click').on('click', function (e) {
            e.preventDefault();
            $('.loading_main').show();
            $('#addupdateModal').find(".modal-body").html('');
            $('#addupdateModal').modal('hide');
            var obj = {};
            obj.class_id = class_id;
            $("#updateaddModal").on("shown.bs.modal", function () {
               $(this).off('shown.bs.modal'); 
               var  updateBodyHtml = $(this).find(".modal-body");
                $.post("<?php echo site_url('teacher/class_view'); ?>", obj, function (data) {
                    updateBodyHtml.html(data.html);
                    $('.loading_main').hide();
                    return false;
                }, "json");
                return false;
            });
           
            
        });
        
        //delete
        $('#deleteBtn').on('click', function(e){

             e.preventDefault();
             if(parseInt(class_candidates) > 0){
                 alertify.alert('Warning!', 'The class can be removed only when it has no learners associated to them. Please do remove all leaners from class then repeat the delete process.', function(){  });
                 return false;
             }else{
                $('.loading_main').show();
                $('#deleteBtn').attr('disabled', true);
                alertify.confirm('<?php echo lang('app.language_confirm_to_remove_class_from_teacher'); ?>', 
                function(){
                   obj = {};
                   obj.class_id = class_id;
                   $.post("<?php echo site_url('teacher/remove_class'); ?>", obj, function (data) {
                       $('.loading_main').hide();
                       $('#deleteBtn').attr('disabled', false);
                       location.reload();
                   }, "json");
                },              
                function(){
                   $('#deleteBtn').attr('disabled', false);

                }).set('labels', {ok: '<?php echo lang('app.language_confirm_ok'); ?>', cancel: '<?php echo lang('app.language_confirm_cancel'); ?>'});
            }
        });
 
        
        $("#updateaddModal .modal").on("hidden.bs.modal", function(e){
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
            window.location = "<?php echo site_url('teacher/dashboard'); ?>";
        });

        if ($('#search').val() != '') {
            $('#search').addClass('x');
        } else {
            $('#search').removeClass('x');
        }
        
        $('#clearBtn').on('click', function(){
             window.location = "<?php echo site_url('teacher/dashboard'); ?>";
        });
        
        $('#clearLearnerBtn').on('click', function(){
             $('#search').val('');
             $('#searchForm').submit();
        });      
        alertify.defaults.glossary.title = "<?php echo lang('app.language_confirm_title'); ?>";
        alertify.defaults.transition = "fade";
        alertify.defaults.theme.ok = "btn btn-primary";
        alertify.defaults.theme.cancel = "btn btn-danger";
        alertify.defaults.theme.input = "form-control";
        $('#deleteLearnerBtn').on('click', function(e){
        	 e.preventDefault();
        	  var studentClassId = [];        	  
              $(".studentClassId1:checked").each(function() {
                 studentClassId.push(this.value);
              });  
             if(typeof studentClassId != 'undefined'){
            	if(studentClassId.length > 1){
                  var alertMessage =  '<?php  echo lang('app.language_confirm_to_remove_learners_from_class'); ?>';
                }else{
                    var alertMessage =  '<?php  echo lang('app.language_confirm_to_remove_learner_from_class'); ?>';
                }
            	alertify.confirm(alertMessage, 
                function(){
                   obj = {};
                   obj.studentClassId = studentClassId;                 
                   $.post("<?php echo site_url('teacher/remove_learner'); ?>", obj, function (data) {
                       $('.loading_main').hide();
                      location.reload();
                   }, "json");
                },              
                function(){
                    }).set('labels', {ok: '<?php echo lang('app.language_confirm_ok'); ?>', cancel: '<?php echo lang('app.language_confirm_cancel'); ?>'});
            }else{
                return false;
            }
        });
        //history of purchases
        $('.history_link').off('click').on('click', function (e) {   
            e.preventDefault();     
            $('.loading_history').show();
            $('#history_modal').modal('show');          
            var obj = {};
            obj.u13_learner_id = this.id;           
            if(obj.u13_learner_id) {
                $("#history_modal").on("shown.bs.modal", function () {      
                    $(this).off('shown.bs.modal');
                    var updateBodyHtml = $(this).find(".modal-body");
                    $.post("<?php echo site_url('teacher/purchased_history'); ?>", obj, function (data) {
                        updateBodyHtml.html(data.html);
                        $('.loading_history').hide();
                        return false;
                    }, "json");
                    return false;
                });         
            }
            return false;
        });  
        
                    //zendesk url change
        $('.zendesk').click(function (e) {
            var zen_user_id = $('#zen_user_id').val();
            $.ajax({
                    url: '<?php echo site_url('teacher/get_zend_desk_url_changing'); ?>',
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