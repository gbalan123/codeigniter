<style>
body{
		background-color:#d3d3d3;
	}
</style>
<div class="bg-lightgrey">
    <div class="container">  
        <div class="row">
            <div class="col-sm-12">
                <h1 class="welcome_user"><?php echo lang('app.language_dashboard_welcome') . ', ' . ucfirst($this->session->get('user_firstname')); ?></h1>
            </div>
        </div>
        <div id="bloc1-dash">
            <div class="nav_dashboard">
                <div class="overflow_x single_tab">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#dashboard"><?php echo lang('app.language_dashboard'); ?></a></li>
                    <li class="pull-right"></li> 
                </ul>
                </div>
                <div class="tab-content">
                    <div id="your_result" class="tab-pane fade in active learning_tab">
                        <div class="row"> 
                            <div class="col-sm-12" >
                                <p><?php echo lang('app.language_site_tds_placement_empty_dashboard');?></p>
                                <p><?php echo lang('app.language_site_tds_placement_empty_dashboard_msg_1');?></p>
                            </div>
                        </div>
                        <div class="col-sm-4 result-content"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>