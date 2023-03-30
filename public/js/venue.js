                //venue form validation
		 $('#venue_form').bootstrapValidator({
				locale : "<?php echo $this->lang->lang(); ?>",
				
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
		                    	message: '<?php echo lang('language_distributor_venue_already_available_msg'); ?>',
		                        url: '<?php echo site_url('distributor/checkvenue'); ?>',
		                        type: 'POST',
		                        data: function(validator, $field, value) {
		                            
		                            // Return an object
		                            return {
		                               
		                                'venue_id': ($('#venue_id').val()!='') ? $('#venue_id').val() : '',
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
			                    	message: '<?php echo lang('language_distributor_venue_firstname_check_error'); ?>',
			                        url: '<?php echo site_url('distributor/firstname_check'); ?>',
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
				                    message : '<?php echo lang('language_distributor_venue_lastname_check_error'); ?>',
				                    url: '<?php echo site_url('distributor/lastname_check'); ?>',
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
		                    uri: {
		                       
		                    },
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
                    //this section before submit
                   id = $('#venue_id').val();
                   use_in_event = $('#use_in_event').val();
                   
                   if(id != '' && use_in_event == '1'){
                	  	if (window.confirm("<?php echo lang('language_distributor_are_you_sure_update_venue'); ?>")) {
   		    				//submits the form
	   		            }else{
		   		            $('#submitBtn').attr('disabled',false);
	   		            	e.preventDefault();
	   		            	window.location = "<?php echo site_url('distributor/listvenues'); ?>";
	   		            }
                   }
		        	
		    		
                }
			
       	 });


