<?php if (isset($purchased_results) && !empty($purchased_results)): ?>
    <?php $i = 1; ?>
    <?php foreach ($purchased_results as $result): ?>
        <?php if ($i == 1): ?>
            <p class="text-center" style="font-weight:bold;padding-bottom: 15px;margin-bottom:0px;border-bottom: 1px solid #e5e5e5;">Current level <?php echo $result->product_name ?></p>
        <?php else: ?>
            <?php if ($result->course_type == 'Primary') { ?>
                <div class="learner_history">
                    <table style="width:100%;">
                        <tr>
                            <td style="text-align:right; width:45%;">Course</td>
                            <td style="text-align:center; width:5%;">:</td>
                            <td style="width:50%;"><?php echo $result->product_name; ?></td>
                        </tr>
                        <tr>
                            <td style="text-align:right; width:45%;">Course progress</td>
                            <td style="text-align:center; width:5%;">:</td>
                            <td style="width:50%;"><?php echo ($result->course_progress != NUll) ? round($result->course_progress)."%" : "Not Available"; ?></td>
                        </tr>
                        <tr>
                            <td style="text-align:right; width:45%;">Practice test</td>
                            <td style="text-align:center; width:5%;">:</td>
                            <td style="width:50%;">
                                <?php
                                //for tds test
                                $tds_practice_test_processed_data =   $result->tds_practice_test_processed_data;

                                //For collegepre test
                                $session_numbers = @explode(',', $result->session_numbers);
                                $test_types = @explode(',', $result->test_types);
                                $section1_results = @explode('|', $result->section1_results);
                                $section2_results = @explode('|', $result->section2_results);
                                $is_practice_test = in_array('Practice test', $test_types) ? 'true' : 'false';
                                $is_live_test = in_array('Live test', $test_types) ? 'true' : 'false';
                                if (!empty($test_types) && isset($test_types[0]) && $test_types[0] == 'Practice test') {
                                    $practiceArray ['section1'] = @$section1_results['0'];
                                    $practiceArray ['section2'] = @$section2_results['0'];
                                    if (!empty($test_types) && isset($test_types[1]) && $test_types[1] == 'Live test') {
                                        $liveArray ['section1'] = @$section1_results['1'];
                                        $liveArray ['section2'] = @$section2_results['1'];
                                    }
                                } elseif (!empty($test_types) && isset($test_types[0]) && $test_types[0] == 'Live test') {
                                    if (!empty($test_types) && isset($test_types[1]) && $test_types[1] == 'Practice test') {
                                        $practiceArray ['section1'] = @$section1_results['1'];
                                        $practiceArray ['section2'] = @$section2_results['1'];
                                    }
                                    $liveArray ['section1'] = @$section1_results['0'];
                                    $liveArray ['section2'] = @$section2_results['0'];
                                }

                                if(!empty($tds_practice_test_processed_data) && isset($tds_practice_test_processed_data)) {
                                    $processed_practice_result = (array)json_decode($tds_practice_test_processed_data);
                                    $practicePercentage = (array)$processed_practice_result['overall'];
                                    echo isset($practicePercentage['percentage'])?$practicePercentage['percentage']:'Not Available';
                                } else {    
                                    if (isset($is_practice_test) && $is_practice_test == 'true' && isset($result->thirdparty_id)) {
                                        $score_data = @get_primary_results($practiceArray ['section1'], $practiceArray ['section2']);

                                    }
                                    $practicePercentage = @$score_data['percentage'];
                                    echo isset($practicePercentage) ? $practicePercentage : 'Not Available';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align:right; width:45%;">Final test</td>
                            <td style="text-align:center; width:5%;">:</td>
                            <td style="width:50%;">
                                <?php
                                $tds_final_test_processed_data =   $result->tds_final_test_processed_data;
                                if(!empty($tds_final_test_processed_data) && isset($tds_final_test_processed_data)) {
                                    $processed_final_result = (array)json_decode($tds_final_test_processed_data);
                                    $finalPercentage = (array)$processed_final_result['overall'];
                                    echo isset($finalPercentage['percentage'])?$finalPercentage['percentage']:'Not Available';
                                } else {
                                    if (isset($is_live_test) && $is_live_test == 'true' && isset($result->thirdparty_id)) {
                                        $fi_data = @get_primary_results($liveArray ['section1'],$liveArray ['section2'] );
                                    }
                                    $finalPercentage = @$fi_data['percentage'];
                                    echo isset($finalPercentage) ? $finalPercentage : 'Not Available';
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
            <?php } else { ?>
                <div class="learner_history">
                    <table style="width:100%;">
                        <tr>
                            <td style="text-align:right; width:45%;">Course</td>
                            <td style="text-align:center; width:5%;">:</td>
                            <td style="width:50%;"><?php echo $result->product_name; ?></td>
                        </tr>
                        <tr>
                            <td style="text-align:right; width:45%;">Course progress</td>
                            <td style="text-align:center; width:5%;">:</td>
                            <td style="width:50%;"><?php echo ($result->course_progress != NUll) ? round($result->course_progress)."%" : "Not Available"; ?></td>
                        </tr>
                        <tr>
                            <td style="text-align:right; width:45%;">Final test</td>
                            <td style="text-align:center; width:5%;">:</td>
                            <?php 
                                                    if( ($result->product_id > 9) && ($result->product_id < 13) ){
                                                       if($result->tds_token && $result->tds_candidate_id){
                                                            $url = 'teacher/higher_certificate/'.$result->tds_candidate_id.'/'.$result->tds_token;
                                                        }elseif($result->higher_candidate_id){
                                                           $url = 'teacher/higher_certificate/'.$result->higher_candidate_id; 
                                                        }
                                                    } else {
                                                        $candidate_id = !empty($result->candidate_id) ? $result->candidate_id : $result->tds_candidate_id;
                                                        $url = 'teacher/core_certificate/'.$candidate_id;
                                                    }
                                                ?>
                            <td style="width:50%;"><button class="btn btn-primary" style="padding: 0px 12px;"  name="final" onclick="window.open('<?php echo site_url($url); ?>', '_blank');" >Final test</button>
                            </td>
                        </tr>
                    </table>
                </div>
            <?php } ?>
        <?php endif; ?>
        <?php $i++; ?>
    <?php endforeach; ?>
<?php endif; ?>
