<?php

use App\Models\Usermodel;
use App\Models\Admin\Cmsmodel;

  /*
  * To change this license header, choose License Headers in Project Properties.
  * To change this template file, choose Tools | Templates
  * and open the template in the editor.
  */
  // WP-1363 - Downtime insert UTC start date time and end date time
  function utc_date_time($timezone = FALSE,$date = FALSE, $time = FALSE) {
      if($date != FALSE && $time != FALSE){
          $format = 'Y-m-d H:i';
          $dateTime = new DateTime($date . " ". $time, new DateTimeZone($timezone));
          $utc_time= $dateTime->setTimeZone(new DateTimeZone('UTC'));
          $utc_start_timestamp = strtotime($utc_time->format($format));
          return $utc_start_timestamp;
      }
  }

  // WP-1363 - fetch UTC downtime
  function get_downtime_zone_from_utc($time_zone = FALSE,$utc_start_date_time= FALSE,$utc_end_date_time= FALSE){
      $date_format_start = date("d F Y H:i:s", $utc_start_date_time);
      $date_format_end = date("d F Y H:i:s", $utc_end_date_time);
      $utc_start_time = new DateTime($date_format_start, new DateTimeZone('UTC'));
      $utc_end_time = new DateTime($date_format_end, new DateTimeZone('UTC'));
      
      $downtime_zone_start_timestamp = $utc_start_time->setTimeZone(new DateTimeZone($time_zone));
      $downtime_zone_end_timestamp = $utc_end_time->setTimeZone(new DateTimeZone($time_zone));
      
      $downtime_zone_start_date = $downtime_zone_start_timestamp->format('d-M-Y');
      $downtime_zone_start_time = $downtime_zone_start_timestamp->format('H:i');
      $downtime_zone_end_date = $downtime_zone_end_timestamp->format('d-M-Y');
      $downtime_zone_end_time = $downtime_zone_end_timestamp->format('H:i');
      
      $institution_event_data = array(
        'downtime_start_date' =>  $downtime_zone_start_date,
        'downtime_start_time' =>  $downtime_zone_start_time,
        'downtime_end_date' =>  $downtime_zone_end_date,
        'downtime_end_time' =>  $downtime_zone_end_time
        );
      return $institution_event_data;
  }

 	// WP-1362 Error 404 page redirection
  function role_based_redirection()
  {
    $session = \Config\Services::session();
    $usermodel = new Usermodel();
    $userole =  $usermodel->chk_role($session->get('user_id'));
    if(count($userole) > 0) {
      if($userole['0']['name'] == 'school'){
        $data['home_page_url'] = 'school/dashboard';
      }elseif($userole['0']['name'] == 'teacher'){
        $data['home_page_url'] = 'teacher/dashboard';
      }elseif($userole['0']['name'] == 'tier1'){
        $data['home_page_url'] = 'tier1/dashboard';
      }elseif($userole['0']['name'] == 'tier2'){
        $data['home_page_url'] = 'tier2/dashboard';
      }elseif($userole['0']['name'] == 'learner'){
        $data['home_page_url'] = 'site/dashboard';
      }elseif($userole['0']['name'] == 'admin'){
        $data['home_page_url'] = 'admin/dashboard';
      }
    }
    return  $data;
  }

  //WP-1363 - Show downtime maintenance page
  function downtime_maintenance_page()
  {

    $cmsmodel = new Cmsmodel();
    $current_utc_time = @get_current_utc_details();
    $current_utc_timestamp = $current_utc_time['current_utc_timestamp'];
    $downtime_available = $cmsmodel->down_time_details($current_utc_timestamp); 
    $whitelisted_ip = $cmsmodel->whitelisted_ip();
    if($downtime_available === TRUE && $whitelisted_ip === FALSE){
        return TRUE;
    } else {
      return FALSE;
    }
  }

  //TDS-366 
  function get_audio_common($processed_data, $result_date, $token , $type = false){

    if($type == 'practice'){
        $processed_data_res = json_decode($processed_data,true); 
        $processed_data = $processed_data_res['score'];
    }else{
        $processed_data = json_decode($processed_data,true); 
    }

    $tds_audio['audio_reponses'] = is_array($processed_data) && array_key_exists("speaking",$processed_data) ? 1: 2;
    if($tds_audio['audio_reponses'] == 1){
        $tds_audio['audio_available'] = (strtotime($result_date) > strtotime('-90 days')) ? TRUE : False;
        if($tds_audio['audio_available'] == True){
            $tds_audio['url']=audio_responses($token);     
        }                                                          
    }  
    return $tds_audio;
  }

  function audio_responses($token){
    $oauth = new \Config\Oauth();
    //Api call for audio response url from tds side
    $_token = base64_encode($token);
    $url = $oauth->catsurl('testDeliveryUrl');
    $newurl = dirname($url);
    $_url=$newurl.'/'.'Dashboard'.'/'.'SpeakingResult'.'?'.'token='.$_token.'';
    return $_url;
  }

  function disable_icon(){
    $text ='<a style="text-decoration:none" data-toggle="tooltip" data-placement="bottom" title="Audio no longer available"><i class="bi bi-volume-up-fill disabled-icon"></i></a>';
    return $text;
  }

  function GetClientInfo()
  {
      return json_decode(file_get_contents("https://api.ipify.org/?format=json"))->ip ?: '';
  }
