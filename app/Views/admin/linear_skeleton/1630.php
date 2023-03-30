<style>
   .bv-form .help-block {
    color: red;
   }
    .simple{
  	width:30px;
        padding-left:5px;
      
  }
  .question, .input-group{
      //display: inline;
      //float:left;
      font-size: 25px;
  }
  .question .help-block{
    display: inline;
    margin-left: 5px;
  }
  .assesmentTest .modal-body p {
     font: 14px Verdana, Geneva, sans-serif;
   }
  .EnScroll{
      font-size: 25px;
  }
  .assesmentTest input[type="text"] {
      width: 35px;
      padding: 0px 0px 4px 8px;
      margin: 0px 0; 
  }
  
    </style>
<?php
$missinganswer = $screen['questions'][0]['missinganswer'];

@preg_match('/\[(.*?)\]/',$screen['questions'][0]['missinganswer'], $output);
                                    $newdata = @explode('/', $output[1]);
                                    if(is_array($newdata) && !empty($newdata)){
                                         
                                        
                                               if(count($newdata)>=2){
                                                   $lengh_str = strlen(max($newdata));
                                               }else{
                                                   $lengh_str =  strlen($newdata[0]);
                                               } 
                                        
                                    }
isset($screen['questions'][0]['missinganswer']) ? $screen['questions'][0]['missinganswer'] = preg_replace("/\[[^)]+\]/","[$lengh_str]",$screen['questions'][0]['missinganswer']) : '';


preg_match('/\[(.*?)\]/', $screen['questions'][0]['missinganswer'], $textboxes);

if (!function_exists('replace_between_1133')) {
    function replace_between_1133($str, $needle_start, $needle_end, $replacement) {
        $pos = strpos($str, $needle_start);
        $start = $pos === false ? 0 : $pos + strlen($needle_start);

        $pos = strpos($str, $needle_end, $start);
        $end = $start === false ? strlen($str) : $pos;

        return substr_replace($str, $replacement, $start, $end - $start);
    }
}
$screens_id = $screen['screenid'];
//print_r($screens['questions'][0]['missinganswer']);
if (!function_exists('replacement_textboxes_1133')) {
    function replacement_textboxes_1133($textboxes,$screens_id){

        $replacement = '<div class="form-group input-group">';
        if(isset($textboxes[1]) && $textboxes[1]!=''):
            for($j=1;$j <= $textboxes[1];$j++):
                $replacement .= '<input type="text" id="answers" class="answers simple" maxlength="1" style="color:black;border-radius: 5px;" >';

            endfor;
        endif;
        $replacement .= '<input type="submit"  class="ansSubmit"  style="display: none;" form="'.$screens_id.'" /></div>';
        return $replacement;
    }
}


$replaceString = array('[' => '', ']' => '');
if (!function_exists('str_lreplace_1133')) {
    function str_lreplace_1133($search, $replace, $subject)
    {
       // substr("testers", -1);
        //return $subject;

        $pos = strrpos($subject, $search);

        if($pos !== false)
        {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }

        return $subject;
    }
}
$whole_start_string =  explode('[', $screen['questions'][0]['missinganswer']);
$whole_string_before_square  = $whole_start_string['0'];
$new_replaced_before_string = str_lreplace_1133(substr($whole_string_before_square, -1), '<span style="display: inline-block;">'.substr($whole_string_before_square, -1),$whole_string_before_square);

$whole_end_string =  explode(']', $screen['questions'][0]['missinganswer']);
$whole_string_after_square  = $whole_end_string['1'];

?>
<div class="modal-content">
    <div class="modal-header modal_head_bg">
        <button type="button" class="close md_close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h3 class="modal-title rubric text-center">CATs Step placement test</h3>
    </div>
    <div class="modal-body md_bg">
        <div class="container-fluid" id="catsQuizEngine">
            <h1 class="text-center">Key : <?php echo $missinganswer; ?></h1>
            <div class="row mt50">
                <div class="col-sm-12">
                    <div class="sys_qus_block text-center">
                        <?php $screens_id = $screen['screenid']?>
                        <p><?php echo "<form class='form_1133 bv-form form-inline' id='".$screens_id."' autocomplete='off' ><div class='question' style='color:white; text-align:left;'>". $new_replaced_before_string. replacement_textboxes_1133($textboxes, $screens_id) ."</span>".$whole_string_after_square."</div></form>"; ?> </p>                                            
                    </div></div>
            </div>
        </div>
    </div>
</div>
