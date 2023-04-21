<?php

$this->session = \Config\Services::session();
?>

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
         <?php if (!empty($tds_placement_tests)) {  ?>
        <div class="alert alert-success alert-dismissible" >
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            </button>
            <p><?php echo lang('app.language_site_tds_placement_empty_dashboard');?></p>
            <p><?php echo lang('app.language_site_tds_placement_empty_dashboard_msg_1');?></p>
        </div>
        <?php }?>
        <?php if(!empty($benchmarks)) { ?>
        <div class="welcome_content">
            <div class="row">
                <div class="col-sm-4 custom-form ">
                    <div class="form-group">
                            <label class="control-label text-dashboard "><img src="<?php echo base_url().'/public/images/dashboard.png';?>" alt="icon"></label>
                            <label class="control-label"><strong><span id="product_name"><?php //echo (isset($prval['name']) && $prval['name'] != '' ) ? $prval['name'] : ''; ?></span></strong></label>
                    </div>
                </div>				

                <div class="col-sm-8 custom-form form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-6 control-label text-mediumblue "><?php echo lang('app.language_dashboard_see_other_cats');?>:</label>
                        <div class="col-sm-6">
                            <form class="form-control-static" id="product_form" action="<?php echo site_url('site/booktest'); ?>" method="post">
                                <?php $count = 1; ?>
                                <select id="product_id" class="form-control" name="product_id">
                                        <?php foreach ($benchmarks as $key => $benchmark) { ?>
                                            <option value="<?php echo 'b-' . $benchmark['id']; ?>" <?php
                                            if ($count == 1) {
                                                echo "selected";
                                            }
                                            ?>><?php echo 'StepCheck ' . $benchmark['formatdate']; ?></option>
                                                    <?php $count++;
                                                } ?>
                                        <?php
                                        if (!empty($speakingtests)) {
                                            foreach ($speakingtests as $key => $speakingtest) {
                                                ?>
                                                <option value="<?php echo 's-' . $speakingtest['id']; ?>"><?php echo 'Speaking ' . $speakingtest['formatdate']; ?></option> <?php
                                            }
                                        }?>
                                </select>
                            </form>
                        </div>    
                    </div>	
                </div>
        <?php } ?>
            </div>
        </div>
        <?php if(!empty($benchmarks)) { ?>
        <div id="bloc1-dash">
            <div class="nav_dashboard">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#your_result"><?php echo lang('app.language_dashboard_your_result'); ?></a></li>
                    <li class="pull-right"></li> 
                </ul>
                <div class="tab-content">
                    <div id="your_result" class="tab-pane fade in active learning_tab">
                        <div class="row">
                            <?php if(!empty($benchmarks['0']['type_of_token']) && $benchmarks['0']['type_of_token'] != 'benchmarktest') { ?>
                            <div class="col-sm-12" >
                                    <p><?php echo lang('app.language_dashboard_benchmark_thank_you_for_tds_4skills');?></p>
                            </div>
                            <?php }elseif(!empty($benchmarks['0']['test_driver']) && $benchmarks['0']['test_driver'] == 'RN') { ?>
                            <div class="col-sm-12" >
                                    <p><?php echo lang('app.language_dashboard_benchmark_thank_you_for_new_tds');?></p>
                            </div>
                            <?php }else{ ?>
                            <div class="col-sm-12" >
                                    <p><?php echo lang('app.language_dashboard_benchmark_thank_you_for_taking');?></p>
                                    <h1><?php echo lang('app.language_dashboard_benchmark_you_have_achieved').$benchmarks['0']['benchmark_cefr_level']; ?></h1>
                                    <p><?php echo lang('app.language_dashboard_benchmark_important_note');?></p>
                            </div>
                            <?php } ?>
                            <div class="col-sm-4 result-content"></div>
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
        <?php } ?> 

<?php   if(empty($benchmarks)) { ?>
<div class="bg-lightgrey">
    <div class="container">
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
                           <div class="col-sm-12 start-btn">
                                <p><?php echo lang('app.language_dashboard_empty_heading');?></p>
                                <a href="#" class="btn-main btn_cats" style="padding: 4px 10px; text-align: center;"><?php echo lang('app.language_dashboard_book_cats'); ?></a>
                            </div> 
                        </div>
                        <div class="col-sm-4 result-content"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>  	
  <!-- Three columns of Course Features -->