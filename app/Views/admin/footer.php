
</div>

<!-- jQuery -->
<script
	src="<?php echo base_url(); ?>public/admin/bower_components/jquery/dist/jquery.min.js"></script>

<!-- Bootstrap Core JavaScript -->
<script
	src="<?php echo base_url(); ?>public/admin/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Bootstrap validator Plugin JavaScript -->
	<script src="<?php echo base_url('public/js/bootstrapValidator/bootstrapValidator.min.js');?>"></script>
	<script src="<?php echo base_url('public/js/bootstrapValidator/en.js');?>"></script>
	<script src="<?php echo base_url('public/js/moment-with-locales.js');?>"></script>
	<script src="<?php echo base_url('public/js/bootstrap-datetimepicker.js');?>"></script>	
	
<script src="<?php echo base_url('public/js/bootstrap-multiselect.js'); ?>"></script>
<script src="<?php echo base_url('public/js/jquery-ui.js'); ?>"></script> <!-- jQuery UI plugin for drog or sort using JavaScript WP-1138 -->
<!-- Metis Menu Plugin JavaScript -->
<script
	src="<?php echo base_url(); ?>public/admin/bower_components/metisMenu/dist/metisMenu.min.js"></script>

<!-- Morris Charts JavaScript -->
<script
	src="<?php echo base_url(); ?>public/admin/bower_components/raphael/raphael-min.js"></script>
<!-- <script src="public/admin/bower_components/morrisjs/morris.min.js"></script>
     <script src="public/admin/js/morris-data.js"></script>-->

<!-- Custom Theme JavaScript -->
<script
	src="<?php echo base_url(); ?>public/admin/dist/js/sb-admin-2.js"></script>
 
   
    <script>
   
    
	$(document).ready(function() {


		 //category form validation
		 $('#category_form').bootstrapValidator({
				locale : "",
				 // List of fields and their validation rules
		        fields: {
		        	page_name: {
		                validators: {
		                    
		                    stringLength: {
		                        min: 4,
		                        max: 30,
		                       // message: 'The  must be more than 6 and less than 30 characters long'
		                    }
		                    
		                }
		            }
		           
		        }
			
       	 });

		 //cms form validation
		 $('#cms_form').bootstrapValidator({
				locale : "",
				 // List of fields and their validation rules
		        fields: {
		        	title: {
		                validators: {
		                    
		                    stringLength: {
		                        min: 4,
		                        max: 30,
		                       
		                    }
		                    
		                }
		            },
		            content: {
		            	notEmpty: {
                            message: 'The content is required and cannot be empty'
                        },
                        callback: {
                            message: 'The bio must be less than 200 characters long',
                            callback: function(value, validator, $field) {
                                // Get the plain text without HTML
                                var div  = $('<div/>').html(value).get(0),
                                    text = div.textContent || div.innerText;

                                return text.length <= 200;
                            }
                        }
		            },
		            link: {
		                validators: {
		                    uri: {
		                     
		                    }
		                }
		            },
		            page_image: {
		                validators: {
		                    file: {
		                        extension: 'jpg,png,gif',
		                        type: 'image/jpeg,image/gif,image/png',
		                        
		                    }
		                }
		            },
		            download_file: {
		                validators: {
		                    file: {
		                        extension: 'txt,doc,pdf,rtf',
		                        type: 'application/msword,application/pdf,application/rtf,text/plain,',
		                      
		                    }
		                }
		            },
		           
		        }
			
       	 });

		 //helplinks form validation
		 $('#helplinks_form').bootstrapValidator({
				locale : "",
				 // List of fields and their validation rules
		        fields: {
		        	link_id: {
		                validators: {
							notEmpty: {
								message: 'The link_id field is required and cannot be empty'
							},		                    
		                    stringLength: {
		                        min: 4,
		                        max: 30,
		                       
		                    }
		                    
		                }
		            },

		            target_url: {
		                validators: {
							notEmpty: {
								message: 'The target_url field is required and cannot be empty'
							},
		                    uri: {
		                     
		                    }
		                }
		            },
		           
		        }
			
       	 });		 
       	 	
		 //banner form validation
		 $('#banner_form').bootstrapValidator({
				locale : "",
				 // List of fields and their validation rules
		        fields: {
		        	name: {
		                validators: {
		                    
		                    stringLength: {
		                        min: 4,
		                        max: 30,
		                       
		                    }
		                    
		                }
		            },
		            description: {
		            	
		            	validators: {
		                    notEmpty: {
		                       
		                    },
		                    stringLength: {
		                       
		                        min: 10,
		                       
		                    }
		                }
		            },
		            link: {
		                validators: {
		                    uri: {
		                       
		                    }
		                }
		            },
		            banner_image: {
		                validators: {
		                    file: {
		                        extension: 'jpg,png,gif',
		                        type: 'image/jpeg,image/gif,image/png'
		                       
		                    }
		                }
		            }
		           
		        }
			
       	 });


		 //template form validation
		 $('#template_form').bootstrapValidator({
			locale : "",
			fields: {
                            from_email: {
		                validators: {
		                    
		                    notEmpty: {
		                        
		                    },
                                    emailAddress: {							   
                                    
                                    },
                                    stringLength: {
                                       max: 500
		                    }
		                    
		                }
		            }, 
                            cc: {
		                validators: {
		                    
		                   
                                    emailAddress: {							   
                                    
                                    },
                                    stringLength: {
                                       max: 500
		                    }
		                    
		                }
		            },
                            category_id: {
		                validators: {
		                    
		                    notEmpty: {
		                        
		                    }
		                    
		                }
		            },
                            subject: {
		                validators: {
		                    
		                    stringLength: {
		                       
		                        max: 1000
		                       
		                    }
		                    
		                }
		            },
		            content: {
		            	notEmpty: {
                                    message: 'The content is required and cannot be empty'
                                },
                                callback: {
                                    message: 'The bio must be less than 200 characters long',
                                    callback: function(value, validator, $field) {
                                        // Get the plain text without HTML
                                        var div  = $('<div/>').html(value).get(0),
                                            text = div.textContent || div.innerText;

                                        return text.length <= 200;
                                    }
                                }
		            }
		            
		        }
			
       	 });	
		 
		//validate search form
		$('#search_form').bootstrapValidator({
			locale : "",
		
			fields: {
				distributor: {
					validators: {
						notEmpty: {
							   
							}
					},
					message : " "
				},			
				startdate: {
					validators: {
						notEmpty: {
							   
							}
					},
					message : " "
				},
				enddate: {
					validators: {
						notEmpty: {
							   
							}
					},
					message : " "
				}
			}
		});		 
		
		
       	 
	 	$('.custommessages').delay(4000).fadeOut();
	 	
	 	/***************** Script for test version allocation page START WP-1138 ********************/
    	$( init );
    	function init() {
    		$( ".droppable-area1, .droppable-area2" ).sortable({
    	    	connectWith: ".connected-sortable",
    	    	stack: '.connected-sortable ul',
    	    	activate: function( event, ui ) {
    	    		$(this).find('li').siblings().removeClass('active');
        	    },
    			deactivate: function( event, ui ) {
    				$(this).addClass('active');
        		},
        		update: function( event, ui ) {
        			$(this).addClass('active');
            	},
    	    	classes: {
    	    	    "ui-sortable-helper": "active"
    	    	}
	  	    }).disableSelection();
    	}
    	
    	if($('#current_product_id').val() != ''){
    		 product_id = $('#current_product_id').val();
    		 $('[name=product_id] option').filter(function() { 
    		        return ($(this).val() == product_id); //To select Blue
    		    }).prop('selected', true);
        }else{
        	 product_id = ($('#products option:selected').val() != '') ? $('#products option:selected').val() : '';
        }
        
    	if($('#tds_option').val() === 'collegepre'){
    		get_form_codes(product_id);
        }
    	
    	$('#products').on('change',function(){
    		product_id = ($('#products option:selected').val() != '') ? $('#products option:selected').val() : '';
    		$('.exposure_section').hide();
    		get_form_codes(product_id);
        });
        
        $(document).on('click', '#available_form_codes  input[type="checkbox"]', function(){
            if($(this).prop("checked") == true){
            	$(this).closest('li').addClass('active');
            	$(this).closest('li label').append('<input type="hidden" name="active_form_codes[]" value="' +$(this).val()+ '"/>');
            }else if($(this).prop("checked") == false){
            	$(this).next().remove('input[type="hidden"]');
            	$(this).closest('li').removeClass('active');
            }
        });

        $(document).on('click', '#active_form_codes  input[type="checkbox"]', function(){
            if($(this).prop("checked") == true){
            	$(this).closest('li').addClass('active');
            }else if($(this).prop("checked") == false){
            	$(this).closest('li').removeClass('active');
            }
        });
        
        $(document).on('click', 'input[type="radio"]', function(){
            if($(this).val() == "scheduled"){
                $('.exposure_section').show();
                $('#test_allocation_form').bootstrapValidator('revalidateField', 'number_of_exposure');
            }else if($(this).val() == "random"){
              	$('.exposure_section').hide();
            }
        });
        
        /* $(document).on('click', '.exposure_edit', function(){
        	$(this).text('Update');
        	$('.exposure_section .input-sm').prop('disabled', false);
        }); */
        
        $(document).on('click', '.make_active', function(){
        	form_code = $('.form_codes li.active');
        	$('#active_form_codes').prepend(form_code);
        	$('#active_form_codes').find('li').siblings().removeClass('active');
           	$('#active_form_codes').find('li:first-child').addClass('active');
        });
        
        $(document).on('click', '.make_inactive', function(){
        	form_code = $('.form_codes li.active');
        	$('#available_form_codes').prepend(form_code); 
        	$('#available_form_codes').find('li').siblings().removeClass('active');
        	$('#available_form_codes').find('li:first-child').addClass('active');
        });
        
        /* $("body").on('DOMSubtreeModified', "#available_form_codes", function() {
        	$(this).find('li').siblings().removeClass('active');
           	$(this).find('li:first-child').addClass('active');
        }); */ 
        
        $("body").on('DOMSubtreeModified', "#active_form_codes", function() {
        	$(this).find('li .draggable-item').removeAttr('name');
        	$(this).find('li input[type="hidden"]').attr('name', 'active_form_codes[]');
        });
        
        $("body").on('DOMSubtreeModified', "#available_form_codes", function() {
        	$(this).find('li input[type="checkbox"]').attr('name', 'form_codes[]');
        	$(this).find('li input[type="hidden"]').attr('name', 'form_codes[]');
        });

        //Boostrap Validation for Test allocation form
        $('#test_allocation_form').bootstrapValidator({
			locale : "",
			fields: {
				number_of_exposure: {
					validators: {
						notEmpty: {
							   
						},
						greaterThan: {
						    value: 1,
						    message: '<?php echo lang('app.language_admin_no_of_exposure_error_msg1') ?>'
						}
					},
					message : " "
				},
				allocation_rule: {
					validators: {
						notEmpty: {
							message : " "
						}
					}
				}
			}
		});	
        /*****************  Script for test version allocation page END WP-1138 ********************/
        
        /*****************  Script for test version allocation cats page START WP-1202 *************/
        if($('#current_cats_product_id').val() != ''){
        	cats_product_id = $('#current_cats_product_id').val();
   		 	$('[name=cats_product_id] option').filter(function() { 
   		        return ($(this).val() == cats_product_id); //To select Product
   		    }).prop('selected', true);
       	}else{
       		cats_product_id = ($('#cats_products option:selected').val() != '') ? $('#cats_products option:selected').val() : '';
       	}

        if($('#tds_option').val() === 'catstds'){
        	get_form_codes_cats(cats_product_id);
        }
       	
   		$('#cats_products').on('change',function(){
   			cats_product_id = ($('#cats_products option:selected').val() != '') ? $('#cats_products option:selected').val() : '';
    		$('.exposure_section').hide();
    		get_form_codes_cats(cats_product_id);
        });        

   	 	$(document).on('click', '#available_form_codes_cats  input[type="checkbox"]', function(){
        	 if($(this).prop("checked") == true){
	         	$(this).closest('li').addClass('active');
         		$(this).closest('li label').append('<input type="hidden" name="active_form_codes_cats[]" value="' +$(this).val()+ '"/>');
         	}else if($(this).prop("checked") == false){
	         	$(this).next().remove('input[type="hidden"]');
         		$(this).closest('li').removeClass('active');
         	}
     	});
	
    	 $(document).on('click', '#active_form_codes_cats  input[type="checkbox"]', function(){
         	if($(this).prop("checked") == true){
	         	$(this).closest('li').addClass('active');
         	}else if($(this).prop("checked") == false){
	         	$(this).closest('li').removeClass('active');
         	}
     	});
	     
     	$(document).on('click', 'input[type="radio"]', function(){
         	if($(this).val() == "scheduled"){
             	$('.exposure_section').show();
             	$('#cats_test_allocation_form').bootstrapValidator('revalidateField', 'number_of_exposure_cats');
         	}else if($(this).val() == "random"){
	           	$('.exposure_section').hide();
         	}
     	});
	     
     	/* $(document).on('click', '.exposure_edit', function(){
	     	$(this).text('Update');
     		$('.exposure_section .input-sm').prop('disabled', false);
     	}); */
	     
     	$(document).on('click', '.make_active', function(){
	     	form_code = $('.form_codes li.active');
     		$('#active_form_codes_cats').prepend(form_code);
     		$('#active_form_codes_cats').find('li').siblings().removeClass('active');
	       	$('#active_form_codes_cats').find('li:first-child').addClass('active');
     	});
	     
     	$(document).on('click', '.make_inactive', function(){
	     	form_code = $('.form_codes li.active');
     		$('#available_form_codes_cats').prepend(form_code); 
     		$('#available_form_codes_cats').find('li').siblings().removeClass('active');
     		$('#available_form_codes_cats').find('li:first-child').addClass('active');
     	});
	     
     	/* $("body").on('DOMSubtreeModified', "#available_form_codes", function() {
	     	$(this).find('li').siblings().removeClass('active');
        		$(this).find('li:first-child').addClass('active');
     	}); */ 
	     
     	$("body").on('DOMSubtreeModified', "#active_form_codes_cats", function() {
	     	$(this).find('li .draggable-item').removeAttr('name');
     		$(this).find('li input[type="hidden"]').attr('name', 'active_form_codes[]');
     	});
     
     	$("body").on('DOMSubtreeModified', "#available_form_codes_cats", function() {
	     	$(this).find('li input[type="checkbox"]').attr('name', 'form_codes[]');
     		$(this).find('li input[type="hidden"]').attr('name', 'form_codes[]');
     	});

	     //Boostrap Validation for Test allocation form
     	$('#cats_test_allocation_form').bootstrapValidator({
			locale : "",
			fields: {
				number_of_exposure_cats: {
					validators: {
						notEmpty: {
						   	
						},
						greaterThan: {
						    value: 1,
					    	message: '<?php echo lang('app.language_admin_no_of_exposure_error_msg1') ?>'
						}
					},
					message : " "
				},
				allocation_rule_cats: {
					validators: {
						notEmpty: {
							message : " "
						}
					}
				}
			}
		});	   		
                
   		/*****************  Script for test version allocation page cats END WP-1202 *****************/
	});

	// AJAX function to get form codes according to the product id WP-1138
	function get_form_codes(product_id){
    	$('.error, #available_form_codes, #active_form_codes').html('');
    	$('#test_allocation_form_submit, .make_active, .make_inactive').prop('disabled', false);
    	$.ajax({
            url: '<?php echo site_url('admin/get_form_codes'); ?>',
            type: 'POST',
            dataType: "json",
            data: {product_id: product_id},
            success: function (result) {
            	$('#number_of_exposure').val('');
            	$('input[type="radio"]').prop('checked', false);
            	$('#test_allocation_form').bootstrapValidator('resetForm', true);
            	if(result.success){
                	$('#available_form_codes').html(result.all_form_code_html);
                	$('#active_form_codes').html(result.active_form_code_html);
                	$('#number_of_exposure').val(result.number_of_exposure);
                	$('#'+result.allocation_rule).prop('checked', true);
                	if(result.allocation_rule == "scheduled"){
                         $('.exposure_section').show();
                         //$('.exposure_section .input-sm').prop('disabled', true);
                	}else if(result.allocation_rule == "random"){
                      	 $('.exposure_section').hide();
                    }
                	
            	}else{
            		$('.error').html(result.msg);
            		$('#test_allocation_form_submit, .make_active, .make_inactive').prop('disabled', true);
               	}
            }
        });
    }
    
	// AJAX function to get form codes according to the product id WP-1202
	function get_form_codes_cats(cats_product_id){
    	$('.error, #available_form_codes_cats, #active_form_codes_cats').html('');
    	$('#cats_test_allocation_form_submit, .make_active, .make_inactive').prop('disabled', false);
    	$.ajax({
            url: '<?php echo site_url('admin/get_form_codes_cats'); ?>',
            type: 'POST',
            dataType: "json",
            data: {cats_product_id: cats_product_id},
            success: function (result) {
            	$('#number_of_exposure_cats').val('');
            	$('input[type="radio"]').prop('checked', false);
            	$('#cats_test_allocation_form').bootstrapValidator('resetForm', true);
            	if(result.success){
                	$('#available_form_codes_cats').html(result.all_form_code_html);
                	$('#active_form_codes_cats').html(result.active_form_code_html);
                	$('#number_of_exposure_cats').val(result.number_of_exposure);
                	$('#'+result.allocation_rule).prop('checked', true);
                	if(result.allocation_rule == "scheduled"){
                         $('.exposure_section').show();
                         //$('.exposure_section .input-sm').prop('disabled', true);
                	}else if(result.allocation_rule == "random"){
                      	 $('.exposure_section').hide();
                    }
            	}else{
            		$('.error').html(result.msg);
            		$('#cats_test_allocation_form_submit, .make_active, .make_inactive').prop('disabled', true);
               	}
            }
        });
    }
   
	var today = new Date();
	$('#strd,#endd').datetimepicker({
            useCurrent: false,
            minDate:'1940-01-01',
            maxDate: today,
            format: 'DD-MM-YYYY'
        });
		
		$('#strd')
			.on('dp.change', function(e) {
				// Revalidate the date when user change it
				$('#results_tds').bootstrapValidator('revalidateField', 'strd');
				var incrementDay = moment(new Date(e.date));
				var currentDay = moment(today);
				
                $('#endd').data('DateTimePicker').minDate(incrementDay);
                incrementDay.add(365, 'days');
				if(incrementDay > currentDay){
					incrementDay = currentDay;
				}
				$('#endd').data('DateTimePicker').maxDate(incrementDay);
                $(this).data("DateTimePicker").hide();
		});
		
		$('#endd')
			.on('dp.change', function(e) {
				
				$('#results_tds').bootstrapValidator('revalidateField', 'endd');
                $(this).data("DateTimePicker").hide();
		});

		$('#results_tds').bootstrapValidator({
		locale: "",
		fields: {
			strd: {
                validators: {
                    notEmpty: {
                        message: '<?php echo lang('app.language_school_result_start_date_validate'); ?>'
                    }
                }
            },
			endd: {
                validators: {
                    notEmpty: {
                        message: '<?php echo lang('app.language_school_result_end_date_validate'); ?>'
                    }
                }
            },
		
		}, onSuccess: function (e) {
			
		}	
		
	});	
 // export Mail to excel
    $('#export_mail').click(function () {
		var mail_startdate = $('#strd').val();
		var mail_enddate =$('#endd').val();
        window.location.href = "<?php echo site_url('admin/export_mail_excel'); ?>" + '/' + mail_startdate + '/' + mail_enddate;
    });
		
		


</script>
</body>
</html>
