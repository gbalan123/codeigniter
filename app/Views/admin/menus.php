<?php

$this->cms_model = new \App\Models\Admin\Cmsmodel();
$this->lang = new \Config\MY_Lang();

?>
<!-- Navigation -->
<nav
	class="navbar navbar-default navbar-static-top" role="navigation"
	style="margin-bottom: 0">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle pull-left" style="margin-left: 15px;" data-toggle="collapse"
			data-target=".navbar-collapse">
			<span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span>
			<span class="icon-bar"></span> <span class="icon-bar"></span>
		</button>
		<a class="navbar-brand" style="padding: 5px 15px;" href="<?php echo site_url('admin'); ?>"><img src="<?php echo base_url('/public/images/logo_new.svg'); ?>"   style="height:35px;" class="img-responsive" alt="..." /></a>
	</div>
	<!-- /.navbar-header -->

	<ul class="nav navbar-top-links navbar-right">
		<li class="dropdown">
		<a class="dropdown-toggle" data-toggle="dropdown"
			href="#"><em class="fa fa-language fa-fw"></em><span id="selected_language">
			<?php
			$selected_language = $this->cms_model->get_language($this->lang->lang());
			echo json_decode('"'.$selected_language[0]->name.'"'); 
			
			?></span> <em
				class="fa fa-caret-down"></em> </a>
			<ul class="dropdown-menu dropdown-user" id="language_list">
			<?php
			$allanguages = $this->cms_model->get_language();
			foreach($allanguages as $language){?> <!-- CCC -131 - Condition changed to show only the basic languages by using content_status column in language-->
					<li> <?php
						echo anchor(site_url("lang/$language->code"), json_decode('"'.$language->name.'"'));
					?> </li> 
                        <?php } ?>
			</ul> <!-- /.dropdown-language -->
		</li>
		<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown"
			href="#"> <em class="fa fa-user fa-fw"></em> <em
				class="fa fa-caret-down"></em> </a>
			<ul class="dropdown-menu dropdown-user">
				 <li><a href="<?php echo site_url('admin/profile'); ?>"><em class="fa fa-user fa-fw"></em> <?php echo lang('app.language_admin_profile'); ?></a>
				</li>
		

				<li><a href="<?php echo site_url('admin/logout'); ?>"><em
						class="fa fa-sign-out fa-fw"></em> Logout</a>
				</li>
			</ul> 
		</li>
	</ul>

	<div class="navbar-default sidebar" role="navigation">
		<div class="sidebar-nav navbar-collapse">
			<ul class="nav" id="side-menu">
				<li class="sidebar-search">
					<div class="input-group custom-search-form">
						<input type="text" class="form-control" placeholder="<?php echo lang('app.language_admin_search'); ?>"> <span
							class="input-group-btn">
							<button class="btn btn-default" type="button">
								<em class="fa fa-search"></em>
							</button> </span>
					</div> 
				</li>
				<li><a href="<?php echo site_url('admin/dashboard'); ?>"><em
						class="fa fa-dashboard fa-fw"></em>&nbsp;&nbsp;<?php echo lang('app.language_admin_dashboard'); ?></a>
				</li>

				<li><a href="#"><em class="fa fa-files-o fa-fw"></em>&nbsp;&nbsp;<?php echo lang('app.language_admin_pages'); ?><span
						class="fa arrow"></span> </a>
					<ul class="nav nav-second-level">

                                             <li>
							<a href="<?php echo site_url('admin/list_applinks'); ?>">
								<em class="fa fa-android fa-fw"></em>
								<?php echo lang('app.language_admin_list_applinks'); ?>
							</a>
						</li>
					</ul> 
				</li>
	
				
				
				<li><a href="#"><em class="fa fa-book fa-fw"></em>&nbsp;&nbsp;<?php echo lang('app.language_admin_products'); ?><span
						class="fa arrow"></span> </a>
					<ul class="nav nav-second-level">
						
						<li><a href="<?php echo site_url('admin/product'); ?>"><em
								class="fa fa-upload fa-fw"></em><?php echo lang('app.language_admin_product_upload_label'); ?></a>
						</li>

						<li><a href="<?php echo site_url('admin/listproducts'); ?>"><em
								class="fa fa-tasks fa-fw"></em><?php echo lang('app.language_admin_list_products'); ?></a>
						</li>
                                                
       
                                                
                                        </ul> 
				</li>
				<!---WP-1363 start --> 
				<li>
					<a href="<?php echo site_url('admin/get_down_time'); ?>">
					<em class="glyphicon glyphicon-wrench fa-fw"></em>&nbsp;&nbsp;<?php echo lang('app.language_admin_add_downtime'); ?>
					<span class="fa arrow"></span> 
					</a>
				</li>
				<!---WP-1363 end -->
		
					  <li><a href="#"><em class="fa fa-building fa-fw"></em>&nbsp;&nbsp;<?php echo lang('app.language_admin_institutions_heading'); ?><span
						class="fa arrow"></span> </a>
					<ul class="nav nav-second-level">
						
						<li><a href="<?php echo site_url('admin/institutions'); ?>"><em class="fa fa-cog fa-fw"></em><?php echo lang('app.language_admin_menu_setup'); ?></a></li>
                                                <li><a href="<?php echo site_url('admin/institution_types'); ?>"><em class="fa fa-tasks fa-fw"></em><?php echo lang('app.language_admin_institution_types'); ?></a></li>
                                                <li><a href="<?php echo site_url('admin/regions'); ?>"><em class="fa fa-map-marker fa-fw"></em><?php echo lang('app.language_admin_region'); ?></a></li>
						

					</ul>
				</li>
				
				
                                <li><a href="#"><em class="fa fa-envelope-o fa-fw"></em>&nbsp;&nbsp;<?php echo lang('app.language_admin_email_templates'); ?><span
						class="fa arrow"></span> </a>
					<ul class="nav nav-second-level">
						<li><a href="<?php echo site_url('admin/mailcategory'); ?>"><em
								class="fa fa-plus fa-fw"></em><?php echo lang('app.language_admin_mail_category'); ?></a>
						</li>
						<li><a href="<?php echo site_url('admin/template'); ?>"><em
								class="fa fa-plus fa-fw"></em><?php echo lang('app.language_admin_add_mail_template'); ?></a>
						</li>
						<li><a href="<?php echo site_url('admin/listemailtemplates'); ?>"><em
								class="fa fa-files-o fa-fw"></em><?php echo lang('app.language_admin_list_templates'); ?></a>
						</li>
						<li><a href="<?php echo site_url('admin/get_mail_count'); ?>"><em
								class="fa fa-envelope-o"></em><?php echo lang('app.language_admin_email_logs'); ?></a>
						</li>
						<li><a href="<?php echo site_url('admin/email_senders_settings'); ?>"><em
								class="glyphicon glyphicon-wrench"></em><?php echo lang('app.language_admin_email_senders_setting'); ?></a>
						</li>
					</ul> 
                                        </li>
                        
                                <li>
                                    <a href="<?php echo site_url('admin/testform_details'); ?>"><em class="fa fa-tasks fa-fw"></em>&nbsp;<?php echo lang('app.language_admin_import_formcodes_tds'); ?><span
						class="fa arrow"></span></a>
                                </li>
				<!-- New admin menu for test version allocation WP-1140 -->
	
				<!-- New admin menu for test version allocation CAT's WP-1202 -->
				<li>
				 	<a href="<?php echo site_url('admin/cats_test_allocation'); ?>">
				 		<em class="fa fa-users fa-fw"></em>&nbsp;&nbsp;<?php echo lang('app.language_admin_test_version_allocation_cats'); ?>
				 		<span class="fa arrow"></span> 
				 	</a>
				</li>
				
                                 <li><a href="<?php echo site_url('admin/view_learner_progress'); ?>"><em class="fa fa-users fa-fw"></em>&nbsp;&nbsp;<?php echo lang('app.language_school_view_learner_progress'); ?><span
						class="fa arrow"></span> </a>
					
				</li>
				<li><a href="<?php echo site_url('admin/reset_final_test'); ?>"><em class="fa fa-users fa-fw"></em>&nbsp;&nbsp;<?php echo lang('app.language_admin_view_reset_final_test'); ?><span
						class="fa arrow"></span> </a>
					
				</li>
                                <li>
				        <a href="#"><em class="glyphicon glyphicon-education fa-fw"></em>&nbsp;&nbsp;<?php echo lang('app.language_admin_tds_settings'); ?><span
						class="fa arrow"></span> 
						</a>
                    <ul class="nav nav-second-level">  
		
                                            <li>
							<a href="<?php echo site_url('admin/sw_weighting_table'); ?>"><em class="glyphicon glyphicon-wrench fa-fw"></em>&nbsp;<?php echo lang('app.language_admin_tds_speaking_writing_weighting');?></a>
					    </li>
                                            <li>
							<a href="<?php echo site_url('admin/speaking_adjusting_table'); ?>"><em class="glyphicon glyphicon-wrench fa-fw"></em>&nbsp;<?php echo lang('app.language_admin_tds_speaking_adjusting_ability');?></a>
					    </li>
                                            <li>
							<a href="<?php echo site_url('admin/writing_adjusting_table'); ?>"><em class="glyphicon glyphicon-wrench fa-fw"></em>&nbsp;<?php echo lang('app.language_admin_tds_writing_adjusting_ability');?></a>
					    </li>
                                            <li>
							<a href="<?php echo site_url('admin/cefr_ability_table'); ?>"><em class="glyphicon glyphicon-wrench fa-fw"></em>&nbsp;<?php echo lang('app.language_admin_tds_menu_cefr');?></a>
					    </li>

                    </ul>                   
                </li>
                                <li><a href="#"><em class="glyphicon glyphicon-education fa-fw"></em>&nbsp;&nbsp;<?php echo lang('app.language_admin_placement_test'); ?><span
                                                class="fa arrow"></span> </a>
                                        <ul class="nav nav-second-level">
                                    
                                        
                                            <li><a href="<?php echo site_url('admin/tds_placement_configs'); ?>"><em
                                                        class="glyphicon glyphicon-wrench fa-fw"></em>&nbsp;<?php echo lang('app.language_admin_placement_configs'); ?></a>
                                            </li>
                                       
											<li><a href="<?php echo site_url('admin/list_adaptive_placement_test'); ?>"><em
                                                        class="glyphicon glyphicon-wrench fa-fw"></em>&nbsp;<?php echo lang('app.language_admin_set_placement_test'); ?></a>
                                            </li>
                                        </ul> 
                                    </li>
                                  <li><a href="#"><em class="glyphicon glyphicon-education fa-fw"></em>&nbsp;&nbsp;<?php echo lang('app.language_admin_primary_placement_test'); ?><span
						class="fa arrow"></span> </a>
					<ul class="nav nav-second-level">
						<li><a href="<?php echo site_url('admin/primary_question_upload_form'); ?>"><em
								class="fa fa-cloud-upload fa-fw"></em>&nbsp;<?php echo lang('app.language_admin_question_upload'); ?></a>
						</li>
                                                <li><a href="<?php echo site_url('admin/primary_placement_configs'); ?>"><em
                                                            class="glyphicon glyphicon-wrench fa-fw"></em>&nbsp;<?php echo lang('app.language_admin_primary_placement_configs'); ?></a>
						</li>
                                                <li><a href="<?php echo site_url('admin/linear_export'); ?>"><em
                                                            class="glyphicon glyphicon-download fa-fw"></em>&nbsp;<?php echo lang('app.language_admin_adp_export_menu'); ?></a>
						</li>
						
					</ul> 
				</li>

				<li><a href="#"><em class="glyphicon glyphicon-education fa-fw"></em>&nbsp;&nbsp;<?php echo lang('app.language_admin_formal_test'); ?><span
						class="fa arrow"></span> </a>
					<ul class="nav nav-second-level">
						<li>
                            <a href="<?php echo site_url('admin/core_sw_weighting_table'); ?>"><em class="glyphicon glyphicon-wrench fa-fw"></em>&nbsp;<?php echo lang('app.language_admin_tds_speaking_writing_weighting'); ?></a>
                        </li>
                        <li>
                            <a href="<?php echo site_url('admin/core_speaking_adjusting_table'); ?>"><em class="glyphicon glyphicon-wrench fa-fw"></em>&nbsp;<?php echo lang('app.language_admin_tds_speaking_adjusting_ability'); ?></a>
                        </li>
                        <li>
                            <a href="<?php echo site_url('admin/core_writing_adjusting_table'); ?>"><em class="glyphicon glyphicon-wrench fa-fw"></em>&nbsp;<?php echo lang('app.language_admin_tds_writing_adjusting_ability'); ?></a>
                        </li>
                        <li>
							<a href="<?php echo site_url('admin/core_cefr_ability_table'); ?>"><em class="glyphicon glyphicon-wrench fa-fw"></em>&nbsp;<?php echo lang('app.language_admin_tds_menu_cefr');?></a>
					    </li>
						</li>
                            <li><a href="<?php echo site_url('admin/result_display_settings'); ?>"><em class="glyphicon glyphicon-wrench fa-fw"></em>&nbsp;<?php echo lang('app.language_admin_formal_test_display'); ?></a>
						</li>
						
					</ul>
				</li>
                                
                                <li><a href="#"><em class="glyphicon glyphicon-education fa-fw"></em>&nbsp;&nbsp;<?php echo lang('app.language_admin_formal_test_higher'); ?><span
						class="fa arrow"></span> </a>
					<ul class="nav nav-second-level">
						<li>
                                                <a href="<?php echo site_url('admin/higher_sw_weighting_table'); ?>"><em class="glyphicon glyphicon-wrench fa-fw"></em>&nbsp;<?php echo lang('app.language_admin_tds_speaking_writing_weighting'); ?></a>
                                            </li>
                                            <li>
                                                <a href="<?php echo site_url('admin/higher_speaking_adjusting_table'); ?>"><em class="glyphicon glyphicon-wrench fa-fw"></em>&nbsp;<?php echo lang('app.language_admin_tds_speaking_adjusting_ability'); ?></a>
                                            </li>
                                            <li>
                                                <a href="<?php echo site_url('admin/higher_writing_adjusting_table'); ?>"><em class="glyphicon glyphicon-wrench fa-fw"></em>&nbsp;<?php echo lang('app.language_admin_tds_writing_adjusting_ability'); ?></a>
                                            </li>
                                    
                                            <li><a href="<?php echo site_url('admin/tds_reading_ability_table'); ?>"><em
                                                        class="glyphicon glyphicon-wrench fa-fw"></em>&nbsp;<?php echo lang('app.language_admin_higher_tds_reading_menu'); ?></a>
                                            </li>
                                  
                                             <li><a href="<?php echo site_url('admin/tds_listening_ability_table'); ?>"><em
                                                        class="glyphicon glyphicon-wrench fa-fw"></em>&nbsp;<?php echo lang('app.language_admin_higher_tds_listening_menu'); ?></a>
                                            </li>
                                            <li>
                                                <a href="<?php echo site_url('admin/higher_cefr_ability_table'); ?>"><em class="glyphicon glyphicon-wrench fa-fw"></em>&nbsp;<?php echo lang('app.language_admin_tds_menu_cefr'); ?></a>
                                            </li>

					</ul>
				</li>

				<li><a href="<?php echo site_url('admin/tds_jobs'); ?>"><em
					class="glyphicon glyphicon-wrench fa-fw"></em>&nbsp;<?php echo lang('app.language_admin_scheduler_settings'); ?></a>
				</li> 
				<li><a href="<?php echo site_url('admin/tds_raw_results'); ?>"><em
					class="glyphicon glyphicon-download fa-fw"></em>&nbsp;<?php echo lang('app.language_admin_formal_tds_view_raw_result'); ?></a>
				</li>

				<li>
					<a href="<?php echo site_url('admin/update_unit_progress'); ?>">
					<em class="fa fa-tasks fa-fw"></em>&nbsp;&nbsp;<?php echo lang('app.language_admin_update_unit_progress'); ?>
					<span class="fa arrow"></span> 
					</a>
				</li>
				
      
			</ul>
		</div>
		<!-- /.sidebar-collapse -->
	</div>
	<!-- /.navbar-static-side -->
</nav>
