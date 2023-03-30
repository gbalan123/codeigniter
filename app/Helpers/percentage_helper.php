<?php 

    use App\Models\Site\Bookingmodel;
    use App\Models\Admin\Tdsmodel;
    use App\Models\Admin\Cmsmodel;
    use App\Models\Admin\Placementmodel;


    function db_check(){
        $db = \Config\Database::connect();
        return $db;
    }

    //calculate percentage for primary user results
    function get_primary_results($section1 = FALSE, $section2 = FALSE) {
        if ($section1 != FALSE || $section2 != FALSE) {
            $parse_section_one_data = json_decode($section1);
            $parse_section_two_data = json_decode($section2);
            if (isset($parse_section_one_data) && !empty($parse_section_one_data)) {
                foreach ($parse_section_one_data->item as $items):
                    foreach ($items->question as $question):
                        if (isset($question->score)):
                            $first_section[] = $question->score;
                        elseif (isset($question->{'@attributes'}->score)):
                            $first_section[] = $question->{'@attributes'}->score;
                        else:
                        endif;
                    endforeach;
                endforeach;
            }
            if (isset($parse_section_two_data) && !empty($parse_section_two_data)) {
            foreach($parse_section_two_data->item as $items):
                    foreach($items->question as $question):
                            if(isset($question->score)):
                                $second_section[] = $question->score;
                            elseif(isset($question->{'@attributes'}->score)):
                                $second_section[] = $question->{'@attributes'}->score;
                            else:    
                            endif;
                    endforeach;
                endforeach;
            }
            
            $merge_two_sections = array_merge((array) @$first_section, (array)@$second_section);
            array_unshift($merge_two_sections,"");
            unset($merge_two_sections[0]);
                
            if (isset($merge_two_sections) && !empty($merge_two_sections)) {
                $score = number_format(array_sum($merge_two_sections));
                $total = count($merge_two_sections);
                $percentage = number_format(($score / $total) * 100);
                $results = array('score' => $score, 'total' => $total, 'percentage' => $percentage . '%');
               
            } else {
                $results = array('score' => 0, 'total' => 0, 'percentage' => 0 . '%');
            }
            return $results;
        }
    }

    //to get the course progress
    function get_course_progress($alp_id = FALSE, $user_app_id = FALSE) {
        if ($user_app_id != FALSE && $alp_id != FALSE):
            $dataNeeded = array("token" => "cts47a7264afdwh", "courseid" => $alp_id, "userid" => $user_app_id);
            $data_string = json_encode($dataNeeded);
            $response = http_ws_call_moodle($data_string);
            $res_json = json_decode($response);
            if (!empty($res_json)) {
                if (isset($res_json->result->coursegrade)) {
                    $progress = round($res_json->result->coursegrade) . '%';
                } else {
                    $progress = '0%';
                }
            } else {
                $progress = '0%';
            }
        endif;
        return $progress;
    }


    function get_next_core_level($level = false, $section_one = FALSE, $section_two = FALSE, $thirdparty_id= false) {

      
        // $next_product  = '';
        if($section_one != FALSE && $section_one != FALSE){
            $section1 = json_decode($section_one);
            $section2 = json_decode($section_two);
            $section1_result = @process_results($section1->item);
            $section2_result = @process_results($section2->item);
            $questions = count($section1_result) + count($section2_result);
            $score = array_sum($section1_result) + array_sum($section2_result);
            $score_for_lookup = $score / $questions;
        }else{
            $db = db_check();
            $builder = $db->table('tds_results as TR');
            $builder->select('TR.processed_data as data');
            $builder->where('TR.candidate_id', $thirdparty_id);
            $builder->limit(1);
            $query = $builder->get();
  
            if ($query->getNumRows() > 0) {
                $coreresults = $query->getRowArray();
                $tds_core_score = json_decode($coreresults['data'], true);
                $score = $tds_core_score['total']['score'];

            }
        }

        
        //$score = 13;
        $cefr_array = array(
            'A1.1',
            'A1.2',
            'A1.3',
            'A2.1',
            'A2.2',
            'A2.3',
            'B1.1',
            'B1.2',
            'B1.3',
            'B2.1',
            'B2.2',
            'B2.3'
        );

        $bookingmodel = new Bookingmodel();
        $res_score_settings = $bookingmodel->result_display_settings($thirdparty_id);
        if (!empty($res_score_settings)) {
            if ($score >= $res_score_settings['passing_threshold']) {
                $key = array_search($level, $cefr_array);

                if ($cefr_array[$key + 1]) {
                    $product_array = @get_products($cefr_array[$key + 1]);
                    if (!empty($product_array)) {
                        $next_product = $product_array['0']['name'];
                    }
                    $result = array('level' => $next_product, 'score' => $score, 'product_id' => $product_array['0']['id'] );
                }
            } elseif (($score < $res_score_settings['passing_threshold']) && ($score > $res_score_settings['lower_threshold'])) {
                $key = array_search($level, $cefr_array);
                $product_array = @get_products($cefr_array[$key]);
                if (!empty($product_array)) {
                    $next_product = $product_array['0']['name'];
                }
                $result = array('level' => $next_product, 'score' => $score, 'product_id' => $product_array['0']['id'] );
            } else {
                $key = array_search($level, $cefr_array);
                if ($key == 0) {
                    $next_search_prod_id = $key;
                } elseif ($key == 1) {
                    $next_search_prod_id = $key - 1;
                } else {
                    $next_search_prod_id = $key - 1;
                }
                if ($cefr_array[$next_search_prod_id]) {
                    $product_array = @get_products($cefr_array[$next_search_prod_id]);
                    if (!empty($product_array)) {
                        $next_product = $product_array['0']['name'];
                    }
                    $result = array('level' => $next_product, 'score' => $score, 'product_id' => $product_array['0']['id'] );
    
                } else {
                    $result = array('level' => '-', 'score' => $score, 'product_id' => $product_array['0']['id'] );
                }
            }
        } else {
            $result = array('level' => '-', 'score' => $score);
        }


        return $result;
    }

    function get_next_higher_level($product_id) {
        if($product_id == 12){
            $result =  array('product_id' => "13", 'level' => "max_higher", 'cefr_level' => "max_higher");
        }else{
            $level = @get_products_name($product_id + 1);
            $level['cefr_level'] = @get_products_name($product_id);
        $result  = array('product_id' => $product_id + 1, 'level' => $level['0']['name'], 'cefr_level' => $level['cefr_level'][0]['level']);
        }
        return $result;
    }

    //process score for counting
    function process_results($results) {


        if (!empty($results)) {
            $score = array();
            foreach ($results as $key => $val) {

                if (count((array)$val->question) == '1') {
                    $score[] += $val->question->{'@attributes'}->score;
                } else {
                    foreach ($val->question as $key3 => $val3) {
                        // $score[] += $val3->{'@attributes'}->score;
                        
                        if (isset($val3->score)) {
                            $score[] += $val3->score;
                        } else{
                                if (isset($val3->{'@attributes'})) {
                                    $score[] += $val3->{'@attributes'}->score;
                                }
                        }
                    }
                }
            }
            // exit;
            return $score;
        } else {
            return false;
        }
    }


    function get_products($level) {
        if ($level) {
            $db = db_check();
            $builder = $db->table('products');
            $builder->select('*');
            $builder->where('level', $level);
            $builder->where('id <', '13');		
            $query = $builder->get();
            return $query->getResultArray();
        }
    }

    function get_products_name($id) {
        if ($id) {
            $db = db_check();
            $builder = $db->table('products');    
            $builder->select('*');
            $builder->where('id', $id);
            $builder->where('id <', '13');		
            $query = $builder->get();

            return $query->getResultArray();
        }
    }

    function get_institution_zone_from_utc($time_zone = FALSE,$utc_start_date_time= FALSE,$utc_end_date_time= FALSE){
        $date_format_start = date("d F Y H:i:s", $utc_start_date_time);
        $date_format_end = date("d F Y H:i:s", $utc_end_date_time);
        $utc_start_time = new DateTime($date_format_start, new DateTimeZone('UTC'));
        $utc_end_time = new DateTime($date_format_end, new DateTimeZone('UTC'));
        
        $institute_zone_start_timestamp = $utc_start_time->setTimeZone(new DateTimeZone($time_zone));
        $institute_zone_end_timestamp = $utc_end_time->setTimeZone(new DateTimeZone($time_zone));
        
        $institute_zone_date = $institute_zone_start_timestamp->format('d-M-Y');
        $institute_zone_start_time = $institute_zone_start_timestamp->format('H:i');
        $institute_zone_end_time = $institute_zone_end_timestamp->format('H:i');
        
        $institution_event_data = array(
            'institute_event_date' =>  $institute_zone_date,
            'institute_start_time' =>  $institute_zone_start_time,
            'institute_end_time' =>  $institute_zone_end_time
            );
        return $institution_event_data;
    }


    //WP-1403 switch email sender
    function get_email_config_provider($category_id){
        $encrypter = \Config\Services::encrypter();
        $tdsmodel = new Tdsmodel();
        $sender_provider_data = $tdsmodel->get_email_sender_provider_by_category($category_id);
        $config = json_decode($sender_provider_data[0]['key_value'], true);
        $smtp_config_array_decrypt = array();
        foreach($config as $key => $value){
            if($key == 'smtp_user'){
                  $value = $encrypter->decrypt(base64_decode($value)); 
            }
            if($key == 'smtp_pass'){
                  $value = $encrypter->decrypt(base64_decode($value));
            }
            $smtp_config_array_decrypt += [$key => $value];
        }
        return $smtp_config_array_decrypt;   
    }

    function get_sender_id_by_category($category_id){
        $tdsmodel = new Tdsmodel();
        $sender_data = $tdsmodel->get_email_sender_list_by_category($category_id);
        return $sender_data;
    }

    //WP-1363 - Show downtime maintenance page
    function get_current_utc_details(){
        //Current UTC details
        $current_utc_details = new DateTime("now", new DateTimeZone('UTC'));
        $current_utc_date = $current_utc_details->format('Y-m-d');
        $current_utc_time = $current_utc_details->format('H:i');
        $current_utc_timestamp = strtotime($current_utc_date . " ". $current_utc_time);
        $current_utc_data = array(
            'current_utc_date' =>  $current_utc_date,
            'current_utc_time' =>  $current_utc_time,
            'current_utc_timestamp' =>  $current_utc_timestamp
            );
        return $current_utc_data;  
    }

/* TDS-368 StepCheck -> Step - Assigned level added condition up one level */
function get_next_level_byStepcheck($level,$results){

    $placementmodel = new Placementmodel();
    $db = db_check();
    $query = $db->query("SELECT level FROM products WHERE course_type != 'primary'");
    $res_array = $query->getResultArray();
    foreach ($res_array as $key) {
        $cefr_array[] = $key['level'];
    }
   
    $result_display_settings = $placementmodel->get_result_display_settings();
      
    if($level == 'A1.1' && ($results['score'] < $result_display_settings['lower_threshold'])){
            $next_level = 'A1.1';
    }elseif($level == 'B2.1'){
        $next_level = 'B2.1';
    }else{
        $key = array_search($level, $cefr_array);
        $next_level = $cefr_array[$key + 1];
    }
    return $next_level;
}

function get_search_thirdparty_count($thirdparty_id = FALSE) {
    if ($thirdparty_id) {

        $db = db_check();
        $builder = $db->table('collegepre_results');
        $builder->select('*');
        $builder->where('thirdparty_id', $thirdparty_id);
        return $builder->countAllResults();
    }
}