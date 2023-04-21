<?php
function generateQRCodePath($controller, $course_type, $uid, $token = false){

    $efsfile = new \Config\Efsfilepath();
    $efsfilepath = $efsfile->get_Efs_path();

    $efs_custom_log_path = $efsfilepath->efs_custom_log;
    $efs_qrcode_path = $efsfilepath->efs_uploads;

    define("LOG_FILE_PDF", $efs_custom_log_path . "pdf_log.txt");
    $short_url = FALSE;
    if ($uid != false) {
        if($course_type === 'benchmark'){
            $url = site_url($controller.'/benchmark_certificate/' . $uid.'/'.$token);
            $file_folder = 'qrcodes_benchmark';
            $file_name = $uid . '_' . $token.'.png';
        }elseif ($course_type === 'higher'){
            if($token){
                //tds higher
               $url = site_url($controller.'/higher_certificate/' . $uid.'/'.$token); 
            }else{
                //collegepre higher
               $url = site_url($controller.'/higher_certificate/' . $uid);  
            }
            $file_folder = 'qrcodes_higher';
            $file_name = $uid.'.png';
        }elseif ($course_type === 'primary'){
            $url = site_url($controller.'/u13qrverify/' . $uid);
            $file_folder = 'qrcodes';
            $file_name = $uid.'.png';
        }elseif($course_type === 'core'){
            $url = site_url($controller.'/core_certificate/' . $uid);
            $file_folder = 'qrcodes';
            $file_name = $uid.'.png';
        }else{
            if($controller === 'site'){
                $url = site_url($controller.'/qrverify/' . $uid);
                $file_folder = 'qrcodes';
                $file_name = $uid.'.png';
            }else{
                $url = site_url($controller.'/qrverify_u13/' . $uid);
                $file_folder = 'qrcodes';
                $file_name = $uid.'.png';
            }
        }
        
        $short_url = $file_abs_path = FALSE;
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.$url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $tinyurl = curl_exec($ch);
            if (curl_errno($ch)) {
                $error_msg = curl_error($ch);
                error_log(date('[Y-m-d H:i e]') .'CURL Error ' . $error_msg .  PHP_EOL, 3, LOG_FILE_PDF);
            }
            
            
            if (filter_var($tinyurl, FILTER_VALIDATE_URL)) {
                $file_abs_path = $efs_qrcode_path .$file_folder.'/'.$file_name;
                $short_url = $tinyurl;
            }else{
                error_log(date('[Y-m-d H:i e]') .'Tiny url Error ' . $tinyurl .  PHP_EOL, 3, LOG_FILE_PDF);
            }
            curl_close($ch);
        }else{
            error_log(date('[Y-m-d H:i e]') . $url . ' not valid ' . PHP_EOL, 3, LOG_FILE_PDF);
        }
        
        return array('short_url' => $short_url, 'file_abs_path' => $file_abs_path);
    } else {
        return array('code' => 1002, 'message' => 'Not a valid GUID');
    }
}