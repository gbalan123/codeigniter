<?php
$this->oauth = new \Config\Oauth();
 ?>
<div class="bg-lightgrey">
    <div class="container">
        <div class="terms_condtion nav_dashboard">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#terms_and_condtion"><?php echo lang('app.contact_us');?></a></li>

            </ul>
            <div class="tab-content">
                <div id="terms_and_condtion" class="tab-pane fade in active terms_tab">
                    <div class="row">
                        <div class="col-sm-12 col-s-12">
                            
							<div class="row">
								<div class="col-sm-12 mt10">			
											
								<h4><?php echo lang('app.language_site_label_contact_page_details_line1');?></h4>
									<h4><?php echo lang('app.language_site_label_contact_page_details_line2');?></h4>
									<?php 
										helper("form");
									?>           
									
									<div class="mt20">
										<?php if(Null !== session()->get("messages")) { ?> 
											<div class="alert alert-success alert-dismissible" role="alert">
												<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
												<?php echo session("messages"); ?>
											</div>
										<?php } ?>
										<?php echo form_open('site/contact', "class = 'form-horizontal signup' autocomplete='off'"); ?>
										<?php $this->validation =  \Config\Services::validation(); if(isset($this->validation)):?>			
										
                                        <?php endif;?>					
										<div class="form-group">
											<label for="name" class="col-sm-4 control-label"><?php echo lang('app.name');?><span>*</span></label>
											<div class="col-sm-6 col-xs-12">
												<input type="text" name="name" id="name" class="form-control input-sm" value="<?php echo set_value('name');?>" />
												<span class="text-danger"><?php  echo $this->validation->getError('name');?></span>
											</div>
										</div>
										<div class="form-group">
											<label for="email" class="col-sm-4 control-label"><?php echo lang('app.email');?><span>*</span></label>
											<div class="col-sm-6 col-xs-12">
												<input type="text" name="email" id="email" class="form-control input-sm" value="<?php echo set_value('email');?>"/>
												<span class="text-danger"><?php  echo $this->validation->getError('email');?></span>
											</div>
										</div>
										<div class="form-group">
											<label for="country" class="col-sm-4 control-label"><?php echo lang('app.country');?><span>*</span></label>
											<div class="col-sm-6 col-xs-12">
												<select id="Country" class="form-control input-sm" name="country">
													<option value="">select</option>
													<?php foreach($countries as $country){ ?>
													<option value="<?php echo $country['countryCode'];?>" <?php echo set_select('country', $country['countryCode']);?> ><?php echo $country['countryName'];?></option>
													 <?php  }?>
												</select>
											    <span class="text-danger"><?php  echo $this->validation->getError('country');?></span>
											</div>
										</div>
										<div class="form-group">
											<label for="message"  class="col-sm-4 control-label"><?php echo lang('app.message');?><span>*</span></label>
											<div class="col-sm-6 col-xs-12">
												<textarea id="message"  name="message"   class="form-control input-sm" rows="4" ><?php echo set_value('message');?></textarea>
											    <span class="text-danger"><?php  echo $this->validation->getError('message');?></span>
											</div>
										</div>

										<div class="form-group">
											<label class="col-sm-4 control-label">Captcha<span >*</span></label>
											<div class="col-sm-6 col-xs-12">
												<div class="g-recaptcha" data-sitekey="<?php echo $this->oauth->catsurl('google_site_key'); ?>" ></div>   
												<span class="text-danger"><?php  echo $this->validation->getError('g-recaptcha-response');?></span> 
											</div>
										</div> 
										
										<div class="form-group">
										<div class="col-sm-offset-4 col-sm-3">
											<?php echo form_submit( 'Submit', lang("app.submit"), "class = 'btn btn-sm btn-continue btn-block btn btn-lg'");?>
										</div>
										</div>
										 <?php echo form_close();?>
									</div>
									<h4><?php echo lang('app.language_site_label_contact_page_details_line3');?><a target='_blank' href=<?php echo base_url('en/pages/privacy_notice');?>> privacy policy</a>.</h4>
									<h4><?php echo lang('app.language_site_label_contact_page_details_line4');?><a target='_blank' href=<?php echo base_url('en/pages/terms_conditions');?>>terms & conditions</link>.</h4>
								</div>
							</div>
                            <!-- content display above-->
                        </div>
                    </div>
                </div>
            </div>
        </div>        
</div>
</div>

<style>
	.text-danger{
		color:red;
	}
</style>