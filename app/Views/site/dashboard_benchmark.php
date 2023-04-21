<style>
body{
		background-color:#d3d3d3;
	}
</style>
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

                    <div class="col-sm-4 result-content">

                    </div>
              </div>
            </div>
        </div>  
    </div>
</div>