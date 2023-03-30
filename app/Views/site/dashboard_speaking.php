<style>
body{
		background-color:#d3d3d3;
	}
</style>
<div class="bg-lightgrey">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="welcome_user"> <?php echo lang('app.language_dashboard_welcome') . ', ' . ucfirst($this->session->userdata('user_firstname')); ?>&nbsp;&nbsp;<img alt="loading" id="dropdown_loading" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>"> </h1>
            </div>
        </div>
        <?php if (!empty($tds_placement_tests)) {  ?>
        <div class="alert alert-success alert-dismissible" >
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            </button>
            <p><?php echo lang('app.language_site_tds_placement_empty_dashboard');?></p>
            <p><?php echo lang('app.language_site_tds_placement_empty_dashboard_msg_1');?></p>
        </div>
        <?php }?>
        <div class="welcome_content">
            <div class="row">
                <div class="col-sm-4 custom-form ">
                    <div class="form-group">
                            <label class="control-label text-dashboard"><img src="<?php echo base_url().'public/images/dashboard.png';?>" alt="icon"> </label>
                            <label class="control-label"><strong><span id="product_name"><?php echo (isset($prval['name']) && $prval['name'] != '' ) ? $prval['name'] : ''; ?></span></strong></label>
                    </div>
                </div>
                <div class="col-sm-8 custom-form form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-6 control-label cats_booking "><?php echo lang('app.language_dashboard_see_other_cats');?>:</label>
                        <div class="col-sm-6">
                        <form class="form-horizontal row" id="product_form" action="<?php echo site_url('site/booktest'); ?>" method="post">
                            <select id="product_id" class="form-control" name="product_id">
                                    <?php foreach ($speakingtests as $key => $speakingtest) { ?>
                                            <option value="<?php echo 's-'.$speakingtest['id']; ?>" <?php echo $selected = ($key == 0) ? 'selected': '' ?>><?php echo 'Speaking '.$speakingtest['formatdate']; ?> </option>
                                    <?php } ?>
                            </select>
                        </form>	
                        </div>						
                    </div>			
                </div>
            </div>
        </div>
        <div id="bloc1-dash">
            <div class="nav_dashboard">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#your_result"><?php echo lang('app.language_dashboard_your_result'); ?></a></li>
                    <li class="pull-right"></li> 
                </ul>
                <div class="tab-content">
                    <div id="your_result" class="tab-pane fade in active learning_tab">
                        <div class="row">
                            <p><?php echo lang('app.language_dashboard_speaking_thank_you_for_taking');?></p>
                        </div>
                        <div class="col-sm-4 result-content"></div>
                    </div>
                </div>
            </div>
        </div>
     </div>
</div></div>

