<?php include_once 'header.php'; ?>
<style>
    /*helper class*/
    .mt20 {
    	margin-top: 20px;
    }
    
    .mtb20 {
    	margin: 20px 0;
    }
    
    .mt24 {
    	margin-top: 24px;
    }
    
    .mt10 {
    	margin-top: 10px;
    }
    /*style starts*/
    a.dashboard_link {
    	text-decoration: underline;
    	color: #117dc1;
    	font-size: 20px;
    }
    
    a.dashboard_link i.fa-long-arrow-left {
    	margin-right: 10px;
    }
    
    .allocver_header h2.heading {
    	font-size: 28px;
    	border-bottom: 1px solid #ddd;
    	padding-bottom: 10px;
    }
    
    .red {
    	color: red;
    }
    
    .allocver_body button.btn-allocver {
    	background-color: #117dc1;
    	color: #fff;
    }
    
    .roll_num li {
    	border-left: 1px solid #ddd;
    	border-right: 1px solid #ddd;
    	border-bottom: 1px solid #ddd;
    	padding: 5px;
    	list-style: none;
    }
    
    .roll_num li label {
        width: 100%;
        margin: 0;
        padding: 5px;
        font-weight: normal;
    }
    
    .roll_num li:first-child {
    	border-top: 1px solid #ddd;
    }
    
    .roll_num li.active {
    	background-color: #117dc1;
    	color: #fff;
    }
    
    .roll_num {
    	height: 210px;
    	margin-bottom: 0;
    	border: 1px solid #ddd;
    	padding: 10px;
    	overflow-y: auto;
    }
    
    .allocver_body h5.bold {
    	font-weight: bold;
    }
    
    .allocver_body label.normal {
    	font-weight: normal;
    }
    
    .radio {
    	margin: 0;
    	font-weight: bold;
    }
    
    .radio label {
    	font-weight: bold;
    }
    
    #cats_test_allocation_form .has-feedback .radio label ~.form-control-feedback {
    	top: -6px;
    	right: -30px;
    }
    
    #cats_test_allocation_form .has-feedback .no_of_exposure .form-control-feedback {
    	right: 12px;
    }
    
    input[type=checkbox] {
    	display: none;
    }
    
    #cats_test_allocation_form_submit {
    	padding: 5px 25px 5px 25px;
    }
    .exposure_section{
        display: none;
    }
</style>

<div class="row">
	<div class="allocver_body mt20">
		<?php echo form_open('admin/set_cats_test_allocation', array('role' => 'form bv-form', 'id' => 'cats_test_allocation_form', 'class' => 'form bv-form', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>
		<div class="col-sm-12">
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-9">
							<label class="col-sm-3 control-label mt10"><?php echo lang('app.language_admin_products'); ?></label>
							<div class="col-sm-9 col-xs-12">
								<div class="form-group">
									<select class="form-control" name="cats_product_id" id="cats_products">
                                    	<?php foreach($products as $product){ ?>
                                    	<option value="<?php echo $product->id; ?>"><?php echo $product->name; ?></option>
                                    	<?php } ?>
                                	</select>
                                	
    							</div>
								<p class="error red"></p>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-9">
							<div class="col-sm-6 column">
								<h5 class="bold"><?php echo lang('app.language_admin_available_form_codes_list_label'); ?></h5>
								<ul id="available_form_codes_cats" class="roll_num text-center form_codes">

								</ul>
								<div class="row">
									<div class="col-sm-12 text-right mt20">
										<button type="button" class="btn btn-sm btn-allocver make_active"><?php echo lang('app.language_admin_make_active_btn_txt'); ?></button>
									</div>
								</div>
							</div>
							<div class="col-sm-6 column">
								<h5 class="bold"><?php echo lang('app.language_admin_active_form_codes_list_label'); ?></h5>
								<ul id="active_form_codes_cats" class="roll_num text-center connected-sortable droppable-area2 form_codes">

								</ul>
								<div class="row">
									<div class="col-sm-12 text-right">
										<button type="button" class="btn btn-sm btn-allocver mt20 make_inactive"><?php echo lang('app.language_admin_make_inactive_btn_txt'); ?></button>
									</div>
								</div>
							</div>
							<div class="col-sm-12 mt20">
								<h5 class="bold"><?php echo lang('app.language_admin_allocation_rule_heading'); ?></h5>
								<p><?php echo lang('app.language_admin_allocation_rule_desc'); ?></p>
							</div>
							<div class="col-sm-12">
								<div class="form-group">
									<div class="col-sm-3">
										<div class="radio">
											<label> <input type="radio" id="random" name="allocation_rule_cats" value="random" /> <?php echo lang('app.language_admin_random_allocation_label'); ?>
											</label>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="radio">
											<label> <input type="radio" id="scheduled" name="allocation_rule_cats" value="scheduled" /><?php echo lang('app.language_admin_scheduled_allocation_label'); ?>
											</label>
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-12 mt10">
								<div class="row">
									<div class="form-group exposure_section">
										<label class="col-sm-4 control-label normal"><?php echo lang('app.language_admin_no_of_exposure_label'); ?></label>
										<div class="col-sm-2 no_of_exposure">
											<input type="text" class="form-control input-sm" id="number_of_exposure_cats" name="number_of_exposure_cats" value="" />
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-12 text-right mt20">
								<button type="submit" id="cats_test_allocation_form_submit" class="btn btn-sm btn-allocver"><?php echo lang('app.language_admin_submit'); ?></button>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
					<input type="hidden" name="current_cats_product_id" id="current_cats_product_id" value="<?php echo (isset($product_id)) ? $product_id : ''; ?>">
					<input type="hidden" name="tds_option" id="tds_option" value="catstds">
				</div>
			</div>
		</div>
		<?php echo form_close(); ?>
	</div>
</div>

<?php include 'footer.php'; ?>