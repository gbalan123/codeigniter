<?php
$this->lang = new \Config\MY_Lang();
?>

<div class="panel panel-default">
    <div class="panel-body">
        <strong><?php echo lang('app.language_dashboard_practice_test_title_a'); ?></strong>
        <div class="row mt20">
            <?php if ($green_or_orange >= 7): ?>
                <div class="col-sm-2">
                    <div class="performance_green p3x col-sm-offset-2"></div>
                </div>
                <div class="col-sm-8">
					<?php if (isset($practice_test_count) && $practice_test_count == 1){ ?>
						<p><?php echo lang('app.language_dashboard_practice_test_description_green_single'); ?></p>
					<?php }else { ?>
						<p><?php echo lang('app.language_dashboard_practice_test_description_green'); ?></p>
					<?php } ?>
               </div>
            <?php else: ?>
                <div class="col-sm-2">
                    <div class="performance_yellow p3x col-sm-offset-2"></div>
                </div>
                <div class="col-sm-8">
					<?php if (isset($practice_test_count) && $practice_test_count == 1){ ?>
						<p><?php echo lang('app.language_dashboard_practice_test_description_orange_single'); ?></p>
					<?php }else { ?>
						<p><?php echo lang('app.language_dashboard_practice_test_description_orange'); ?></p>
					<?php } ?>
               </div>
            <?php endif; ?>
           
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-body">
        <strong><?php echo lang('app.language_dashboard_practice_test_title_b'); ?></strong>
        <div class="row mt20">
            <div class="col-sm-4 color1">
                <label class="performance_green p2x"></label>
                <span><?php echo lang('app.language_dashboard_practice_test_icon_a'); ?></span>
            </div>
            <div class="col-sm-8 color1">
                <label class="performance_yellow p2x"></label>
                <span><?php echo lang('app.language_dashboard_practice_test_icon_b'); ?></span>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 mt20">
                <div class="table-responsive">
                    <table class="table table-bordered">

                        <tr>
                            <th class="text-center"><?php echo lang('app.language_dashboard_practice_test_table_label_part'); ?></th>
                            <th class="text-center"><?php echo lang('app.language_dashboard_practice_test_table_label_skills');?></th>
                            <th class="text-center"><?php echo lang('app.language_dashboard_practice_test_table_label_performance');?></th>
                        </tr>
                        
                        <tbody>
                            <?php for ($i = 0; $i < count($results); $i++): ?>
                            
                            <?php
                                $skill_sets_array   = array('Listening', 'Reading', 'Writing', 'Speaking');
                                $skill_sets_ms_array= array(lang('language_dashboard_practice_test_listening'), lang('language_dashboard_practice_test_reading'), lang('language_dashboard_practice_test_writing'), lang('language_dashboard_practice_test_speaking'));

                                if($this->lang->lang() == 'ms'):
                                    $skill_sets = $skill_sets_array;
                                    $skill_sets_ms = $skill_sets_ms_array;
                                    $test_skill = str_replace($skill_sets, $skill_sets_ms, $results[$i]['0']);
                                elseif ($this->lang->lang() == 'sr'):
                                    $skill_sets = $skill_sets_array;
                                    $skill_sets_ms = $skill_sets_ms_array;
                                    $test_skill = str_replace($skill_sets, $skill_sets_ms, $results[$i]['0']);
                                 elseif ($this->lang->lang() == 'pt'):
                                    $skill_sets = $skill_sets_array;
                                    $skill_sets_ms = $skill_sets_ms_array;
                                    $test_skill = str_replace($skill_sets, $skill_sets_ms, $results[$i]['0']);
                                else:
                                    $test_skill =  $results[$i]['0'];
                                endif;
                            ?>
                                <tr class="text-center">
                                    <td style="padding: 0px;vertical-align: middle;<?php echo ($test_skill == "Speaking" || $test_skill == "Writing") ? "border-right-color:#fff" : ""; ?>"><?php echo ($test_skill != "Speaking" && $test_skill != "Writing") ? $i+1 : "" ?></td>
                                    <td style="padding: 0px;vertical-align: middle;"><?php echo $test_skill; ?></td>
                                    <td style="padding: 0px;vertical-align: middle;"> <label class="<?php echo ($results[$i]['2'] == '1') ? 'performance_green' : 'performance_yellow' ?> p1x"></label></td>
                                </tr>
                            <?php endfor; ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


