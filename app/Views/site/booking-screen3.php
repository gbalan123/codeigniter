<?php include_once 'header-booking.php'; 
use Config\MY_Lang;
$this->lang 		= new \Config\MY_Lang();
$session 			= \Config\Services::session();
$target_urlDatas 	= "target_url";
?>
<style>
    .clickable{
        cursor: pointer;
    }
    form label span {
        color: #555555; 
    }
    form label  {
        color: #555555; 
    }
    .has-error .help-block, .has-error .control-label, .has-error .radio, .has-error .checkbox, .has-error .radio-inline, .has-error .checkbox-inline, .has-error.radio label, .has-error.checkbox label, .has-error.radio-inline label, .has-error.checkbox-inline label {
        color :red;
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
							
							<div id="right_level_tab" class="tab-pane right_level fade active in">
							<div class="row">
									<div class="col-sm-12 col-xs-12">
									 <?php if(Null !== session()->get("errors")) : ?>
										<div class="alert alert-danger alert-dismissible" role="alert">
											<button type="button" class="close" data-dismiss="alert"
												aria-label="Close">
												<span aria-hidden="true">&times;</span>
											</button>
										<?php echo session("errors"); ?>
									</div>
									<?php endif; ?>
									<?php 
											if(!empty($this->data['helplinks'])) {
											$helplinks = $this->data['helplinks'];
											}
									?>	
									 <?php if ($organization_data['type_of_token'] == 'benchmarktest'): ?>
										<?php if($launchTestService == 0){?>
											<h4 class="right_level_head"><?php echo lang('app.language_site_booking_screen3_title_benchmark'); ?>
											<span class="pull-right"><a class="help_icon_hide" href="<?php if($this->lang->lang() == 'en'){ echo $helplinks['3'][$target_urlDatas];} elseif($this->lang->lang() == 'ms'){ echo $helplinks['11'][$target_urlDatas];}  ?>"  target="_blank"  title="help"><img src="<?php echo base_url() . 'public/images/ico-help.png'; ?>" alt="icon" /></a></span>
											<div class="clearfix"></div>
											</h4>
											<p><?php echo lang('app.language_site_booking_screen3_description_benchmark'); ?></p>
										<?php } else{?>
											<h4 class="right_level_head"><?php echo lang('app.language_site_booking_screen3_title_benchmark'); ?>
											<span class="pull-right"><a class="help_icon_hide" href="<?php if($this->lang->lang() == 'en'){ echo 'en';} elseif($this->lang->lang() == 'ms'){ echo 'ms';}  ?>"  target="_blank"  title="help"><img src="<?php echo base_url() . 'public/images/ico-help.png'; ?>" alt="icon" /></a></span>
											<div class="clearfix"></div>
											</h4>
											<p><?php echo lang('app.language_site_booking_screen3_description_benchmark_tds'); ?></p>
										<?php } ?>
					                <?php elseif ($organization_data['type_of_token'] == 'speaking_test'): ?>
					                    <h4 class="right_level_head"><?php echo lang('app.language_site_booking_screen3_title_speaking'); ?>
					                    <span class="pull-right"><a class="help_icon_hide" href="<?php if($this->lang->lang() == 'en'){ echo 'en';} elseif($this->lang->lang() == 'ms'){ echo 'ms';}  ?>"  target="_blank" title="help"><img src="<?php echo base_url() . 'public/images/ico-help.png'; ?>" alt="icon" /></a></span>
										<div class="clearfix"></div>
					                    </h4>
					                    <p><?php echo lang('app.language_site_booking_screen3_description_speaking'); ?></p>
									<?php elseif (isset($test_type) && $test_type == 'benchmarking'): ?>
					                    <h4 class="right_level_head"><?php echo lang('app.language_site_booking_screen3_title_4skill_benchmark'); ?>
					                    <span class="pull-right"><a class="help_icon_hide" href="<?php if($this->lang->lang() == 'en'){ echo site_url();} elseif($this->lang->lang() == 'ms'){ echo $helplinks['11'][$target_urlDatas];}  ?>"  target="_blank"  title="help"><img src="<?php echo base_url() . 'public/images/ico-help.png'; ?>" alt="icon" /></a></span>
										<div class="clearfix"></div>
					                    </h4>
                                        <div class="step-check">
                                            <p><?php echo lang('app.language_benchmarking_lang1'); ?></p>
                                            <ul>
                                                <li><?php echo lang('app.language_benchmarking_lang2'); ?></li>
                                                <li><?php echo lang('app.language_benchmarking_lang3'); ?></li>
                                            </ul>
                                            <p><?php echo lang('app.language_benchmarking_lang4'); ?></p>
                                        </div>
					                <?php else: ?>
					                    <h4 class="right_level_head"><?php echo lang('app.language_site_booking_screen3_title'); ?>
					                    <span class="pull-right"><a class="help_icon_hide" href="<?php if($this->lang->lang() == 'en'){ echo'en';} elseif($this->lang->lang() == 'ms'){ echo $helplinks['11'][$target_urlDatas];}  ?>"  target="_blank"  title="help"><img src="<?php echo base_url() . 'public/images/ico-help.png'; ?>" alt="icon" /></a></span>
										<div class="clearfix"></div>
					                    </h4>
					                    <p><?php echo lang('app.language_site_booking_screen3_description'); ?></p>
					                <?php endif; ?>
									
										
									</div>
									<div class="col-sm-12 col-xs-12 <?php echo (isset($test_type) && $test_type == 'benchmarking') ? "text-center" : "text-right"?>">
										
										<?php 
                                                $bt_disable = (!empty($launchUrl)) ? "":"disabled";
                                                if($launchTestService): ?>
											<a href="<?php echo $launchUrl; ?>" class="btn btn-sm btn-continue text-right mt10 disabled" <?php echo $bt_disable;?> id="continue_btn" style="pointer-events:none;"><?php echo lang('app.language_site_booking_continue_btn'); ?></a>
										<?php else: ?>
                                              <a href="#" class="btn btn-sm btn-continue text-right mt10 disabled" disabled id="continue_btn" style="pointer-events:none;"><?php echo lang('app.language_site_booking_continue_btn'); ?></a>
										<?php endif; ?>
									</div>
							</div>

							</div>
							
						</div>
					</div>
				</div>
			</div>
	</div>
		<div style="margin-top:40px"></div> 
        
    </div>
</div>


<!------------------------- Modal  End------------------------->

<?php include 'footer-booking.php'; ?>


