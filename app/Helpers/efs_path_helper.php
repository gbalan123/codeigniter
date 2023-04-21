<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

  function get_efs_path(){
      $efsfile = new \Config\Efsfilepath();
      $efsfilepath = $efsfile->get_Efs_path();
      return $efsfilepath;
  }

  // EFS scaling path to get svg image function
  function image_efs($image_name) {
    $path_efs_array = get_efs_path();
    $filename = $image_name.'.svg';
    $image    =  $path_efs_array->efs_linear_path.$filename;
    return base64_encode(file_get_contents($image));
  }

  function image_efs_preview($image_name) {
    $path_efs_array = get_efs_path();
    $filename = $image_name.'.svg';
    $image    =  $path_efs_array->efs_linear_preview_path.$filename;
    return base64_encode(file_get_contents($image));
  }

  function audio_efs_sounds($image_name) {
    $path_efs_array = get_efs_path();
    $filename = $image_name.'.mp3';
    $audio_path    =  $path_efs_array->efs_linear_sounds_path.$filename;
    return "data:audio/mp3;base64,".base64_encode(file_get_contents($audio_path));
  }

  function audio_efs($image_name) {
    $path_efs_array = get_efs_path();
    $filename = $image_name.'.mp3';
    $audio_path    =  $path_efs_array->efs_linear_path.$filename;
    return "data:audio/mp3;base64,".base64_encode(file_get_contents($audio_path));
  }

  function image_banner($lang,$image_name,$type) {
    $path_efs_array = get_efs_path();
    $filename = $image_name.'.'.$type;
    $image =  $path_efs_array->efs_banner_path.$lang.'/'.$filename;
    return "data:image/".$type.";base64,".base64_encode(file_get_contents($image));
  }
  function image_pages($lang,$image_name,$type) {
    $path_efs_array = get_efs_path();
    $filename = $image_name.'.'.$type;
    $image =  $path_efs_array->efs_pages_path.$lang.'/'.$filename;
    return "data:image/".$type.";base64,".base64_encode(file_get_contents($image));
  }

  function image_png($image_name,$type,$file_folder,$pathname=false) {
    $path_efs_array = get_efs_path();
    $filename = $image_name.'.'.$type;
    if($pathname != false){
      $image =  $path_efs_array->efs_uploads.$file_folder.'/'.$pathname.'/'.$filename;
    }else{
      $image =  $path_efs_array->efs_uploads.$file_folder.'/'.$filename;
    }
  
    return "data:image/".$type.";base64,".base64_encode(file_get_contents($image));
  }



