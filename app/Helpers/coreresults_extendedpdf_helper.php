<?php
use Dompdf\Dompdf;
use Dompdf\Options;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 
function generatecoreExtendedResultsZipPDF($values_core_pdf = FALSE){
    $efsfile = new \Config\Efsfilepath();
    $efsfilepath = $efsfile->get_Efs_path();
    $bulk_download_path = $efsfilepath->efs_uploads_bulk_dwn;
    $efs_custom_log_path = $efsfilepath->efs_custom_log;
    define("LOG_FILE_PDF", $efs_custom_log_path . "pdf_log.txt");
    if($values_core_pdf){
        ob_start();
        $current_level = $values_core_pdf['cefr_level'];
        $cefr_fail_content = "You have not achieved the pass level for the exam and we are unable to award you a result.";
        ?>
<!DOCTYPE html> 
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('/public/css/style_updated_ui.css'); ?>">
        <title>CATs Step Core Results - Pdf</title>
    </head>
    <body>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">    
            <tr>
                <td colspan="2">
                  <img alt="logo" src="<?php echo base_url('/public/images/cats_logo_pdf.png'); ?>" style="height:50px;">
                </td>
            </tr>
            <tr>
                <td align="left" valign="top">
                   <table width="100%"  border="0" cellspacing="0" cellpadding="0" align="center" style="border:1px solid #ddd; margin: 20px 0px">
                        <tr>
                            <td>
                                <div style=" padding:10px 0;">
                                    <h3 style="margin:0 0 5px 10px; color:#000; font-size: 20px;font-weight:bold; font-family:Verdana, Geneva, sans-serif;"><?php echo @$values_core_pdf['course_name']; ?></h3>
                                    <h3 style="margin:0 0 15px 10px;color:#000; font-weight:bold; font-size:16px;font-family:Verdana, Geneva, sans-serif;"><?php echo ucfirst(@$values_core_pdf['candidate_name']); ?></h3>
                                    <p style="margin:0 0 0 10px; color:#000;font-size:14px;font-family:Verdana, Geneva, sans-serif;"><span style="width:130px;display:inline-block;"><strong>Date of exam:</span> </strong><?php echo@$values_core_pdf['exam_date']; ?></p>
                                    <p style="margin:0 0 0 10px;color:#000;font-size:14px;font-family:Verdana, Geneva, sans-serif;"><span style="width:130px;display:inline-block;"><strong>Invigilation status:</span></strong> <?php echo $values_core_pdf['is_supervised']; ?></p>
                                </div>
                            </td>
                        </tr>
                   </table>
                </td>
                <td  align="right" valign="top">
                    <p style="margin:0px 25px 5px 0; font-family:Verdana, Geneva, sans-serif; font-size:10px;">
                        <strong>ID:</strong> <?php echo @$values_core_pdf['thirdparty_id']; ?>
                    </p>
                    <?php if($values_core_pdf['qr_code'] != "" && $values_core_pdf['google_url'] != ""){
                         $_url = explode("/", $values_core_pdf['qr_code']);
                         $file_name = end($_url);
                         $parts = explode('.',$file_name);
                        ?>
                        <p style="margin:0px 35px 5px 0;"><img alt="qr" src="<?php echo @image_png($parts[0],$parts[1],'qrcodes'); ?>" style="height:80px;"></p>
                        <p style="margin:0px 10px 5px 0; font-size: 11px;"><a href="<?php echo @$values_core_pdf['google_url']; ?>" style="color:#117dc1; text-decoration: none;"><?php echo @$values_core_pdf['google_url']; ?></a></p>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                   <p style="font-size:20px;font-weight:600;margin:0;margin-bottom: 20px;"><?php echo lang('app.language_pdf_certicate_statement');?></p>
                </td>
            </tr>
            <tr>
                <td align="left" valign="top" colspan="2">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" style=" padding-top:2px;border: 1px solid #ddd;">
                        <tr>
                            <td align="left" valign="top" style="width:100%; font-size:18px; color:#444;font-family:Verdana, Geneva, sans-serif;padding-bottom:10px;">
                                <tr>
                                    <td style="padding:10px;">
                                        <table>
                                            <tr>
                                                <?php 
                                                    if($values_core_pdf['result_status'] == "Pass" && $values_core_pdf['cefr_level'] == "A1.1"){
                                                        $status = "Pass&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                                                    }else{
                                                        $status = $values_core_pdf['result_status'];
                                                    }
                                                ?>
                                               <td width=35%; style="font-size:15px;"><strong>Overall Performance:</strong></td>
                                               <?php if($values_core_pdf['cefr_level'] != $cefr_fail_content){ ?>
                                               <td width=65%; style="font-size:15px;padding-left: 26px;"><strong>This learner has reached the level of language ability to:</strong></td>
                                                <?php } ?>
                                            </tr>
                                            <tr>
                                                <td style="vertical-align: text-bottom;"> 
                                                  <table>
                                                     <tr>
                                                        <td style="font-size:13px;padding: 5px 0px;"><strong>Result:</strong></td>
                                                        <td style="font-size:13px;padding: 5px 5px;"><?php echo $status;?></td>
                                                     </tr>
                                                     <tr>
                                                        <td style="font-size:13px;padding: 5px 0px;"><strong>Score</strong></td>
                                                        <td style="font-size:13px;padding: 5px 5px;"><?php echo $values_core_pdf['result_score'];?></td>
                                                     </tr>
                                                     <tr>
                                                        <td style="font-size:13px;padding: 5px 0px;"><strong>CEFR Level:</strong></td>
                                                        <td style="font-size:13px;padding: 5px 5px;"><?php echo $values_core_pdf['cefr_level'];?></td>
                                                     </tr>
                                                  </table>
                                               </td>
                                                   <?php if($values_core_pdf['cefr_level_content'] != ""){ ?>
                                               <td>
                                                    <ul class="list-styled" style="margin:0px;">
                                                        <li style="padding:1px 0px;font-size:13px;"><?php echo $values_core_pdf['cefr_level_content'][0];?><br/></li>
                                                        <li style="padding:1px 0px;font-size:13px;"><?php echo $values_core_pdf['cefr_level_content'][1];?></li>
                                                        <li style="padding:1px 0px;font-size:13px;"><?php echo $values_core_pdf['cefr_level_content'][2];?></li>
                                                        <li style="padding:1px 0px;font-size:13px;"><?php echo $values_core_pdf['cefr_level_content'][3];?></li>
                                                    </ul>
                                                </td>
                                                    <?php } ?>
                                            </tr>
                                        </table>
                                    </td>
                               </tr>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
            <td align="left" valign="top" colspan="2"  style="width:100%; font-size:18px; color:#444; font-family:Verdana, Geneva, sans-serif;">
            <table width="100%" style="border: 1px solid #ddd;margin:20px 0px;">   
                <tr>
                    <td>
                        <tr>
                            <td style="padding:10px;">
                                <table style="width:100%;border-spacing: 10px;">
                                   <tr>
                                      <td colspan="4" style="font-size:13px;">
                                         <strong style="font-size:20px;">Performance by Skill</strong>
                                         <p style="font-size:12px;margin:0;padding:5px 0px;"><em>Performance in Listening and Reading counts towards the overall result.</em></p>
                                         <p style="font-size:12px;margin:0;padding-bottom:0px;"><em>
                                         <?php 
                                            if(!empty($values_core_pdf['speaking_content']) && !empty($values_core_pdf['writing_content'])){
                                                echo lang('app.language_core_certificate_Perfomance_sp_wr');
                                            }elseif(!empty($values_core_pdf['speaking_content'])){
                                                echo lang('app.language_core_certificate_Perfomance_sp');
                                            }elseif(!empty($values_core_pdf['writing_content'])){
                                                echo lang('app.language_core_certificate_Perfomance_wr');
                                            }?>
                                             </em></p>
                                      </td>
                                   </tr>
                                   <tr>
                                       <td style="width:50%;">
                                           <table>
                                               <tr>
                                                    <td style="padding: 10px 0px;font-size:13px;" ><strong>Listening Score:</strong></td>
                                                    <td style="padding: 10px 0px;font-size:13px;"><?php echo $values_core_pdf['listening_score'];?></td>
                                               </tr>
                                           </table>
                                       </td>
                                       <td style="width:50%;">
                                           <table>
                                               <tr>
                                                    <td style="padding: 10px 0px;font-size:13px;"><strong>Reading Score:</strong></td>
                                                    <td style="padding: 10px 0px;font-size:13px;"><?php echo $values_core_pdf['reading_score'];?></td> 
                                               </tr>
                                           </table>
                                       </td>
                                   </tr>
                                    <tr>
                                        <?php if(!empty($values_core_pdf['speaking_content'])){ ?>
                                        <td  colspan="1"  style="border-radius: 10px;border: 1px solid #d8cece63;width:70%; padding: 0px 10px;vertical-align: text-top;">
                                            <table>
                                                <tr>
                                                    <td style="padding: 5px 0px;font-size:13px;"><strong>Speaking</strong></td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 5px 0px;font-size:12px;"><strong><?php echo $values_core_pdf['sp_wr_types']['speaking'];?>:</strong></td>
                                                </tr>
                                                <tr>
                                                    <td style="font-size:12px;padding: 5px 0px;vertical-align: text-top;"colspan="2"><?php echo $values_core_pdf['speaking_content'];?></td>
                                                </tr>
                                            </table>
                                        </td>
                                        <?php }?>
                                        <?php if(!empty($values_core_pdf['writing_content'])){ ?>
                                        <td colspan="3" style="border-radius: 10px;width:30%;border: 1px solid #d8cece63; padding: 0px 10px;vertical-align: text-top;" >
                                            <table>
                                                <tr>
                                                    <td style="padding: 5px 0px;font-size:13px;"><strong>Writing</strong></td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 5px 0px;font-size:12px;"><strong><?php echo $values_core_pdf['sp_wr_types']['writing'];?>:</strong></td>
                                                </tr>
                                                <tr>
                                                <td  style="font-size:12px;padding: 5px 0px;vertical-align: text-top;"><?php echo $values_core_pdf['writing_content'];?></td>
                                                </tr>
                                            </table>
                                        </td>
                                        <?php } ?>   
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </td>
                </tr>
            </table>
            </td>
            </tr>
            <tr>
                <?php 
                if($values_core_pdf['result_status'] == "Pass"){
                    $min_bottom  = ['A1.1','A1.2','A1.3','A2.1'];
                    $max_bottom  = ['A2.2','A2.3','B1.1','B1.2','B1.3'];
                    if(($values_core_pdf['writing_content'] == "") && ($values_core_pdf['speaking_content'] == "")){
                        $style = "vertical-align:bottom; padding-top:280px;"; 
                    }else{
                        if(in_array($current_level, $min_bottom)){
                            $style = "vertical-align:bottom; padding-top:122px;";
                        }elseif(in_array($current_level, $max_bottom)){
                            $style = "vertical-align:bottom; padding-top:100px;";
                        }else{
                           $style = "vertical-align:bottom;padding-top:20px;";
                        }
                    }
                } ?>
                <td colspan="2" style="<?php echo $style;?>">
                    <p style="margin-bottom: 2px;font-weight: 400;color:#000;font-size: 12px;text-align: center;"><?php echo lang('app.language_pdf_certicate_find_out');?></p>
                    <?php echo lang('app.language_cats_step_website_link_core_pdf');?>
                </td>
            </tr>
        </table>
        <?php if($values_core_pdf['result_status'] == "Pass"){ 
                $level_array = array('A1.1','A1.2','A1.3','A2.1','A2.2','A2.3','B1.1','B1.2','B1.3');
                $p = array_search($current_level, $level_array);
        ?>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
            <tr>
                <td colspan="2">
                    <table width="100%;" Style="border-spacing:15px">
                        <tr>
                            <td style="border:2px solid <?php echo get_grid_class($p, 'A1.1'); ?>;padding:8px 8px;vertical-align: text-top;">
                                <strong style="color:<?php echo get_grid_text_color($p, 'A1.1')?>">Step Forward 1 (A1.1)</strong>
                                <ul style="padding: 0; padding-left: 20px;font-size:12px;color:<?php echo get_grid_text_color($p, 'A1.1')?>">
                                    <li><?php echo $values_core_pdf['cefr_all_content']['A1.1'][0];?></li>
                                    <li><?php echo $values_core_pdf['cefr_all_content']['A1.1'][1];?></li>
                                    <li><?php echo $values_core_pdf['cefr_all_content']['A1.1'][2];?></li>
                                    <li><?php echo $values_core_pdf['cefr_all_content']['A1.1'][3];?></li>
                                </ul>
                            </td>
                            <td style="border:2px solid <?php echo get_grid_class($p, 'A1.2'); ?>;padding:8px 8px;vertical-align: text-top;">
                                <strong style="color:<?php echo get_grid_text_color($p, 'A1.2')?>">Step Forward 2 (A1.2)</strong>
                                <ul style="padding: 0;padding-left: 20px;font-size:12px;color:<?php echo get_grid_text_color($p, 'A1.2')?>">
                                    <li><?php echo $values_core_pdf['cefr_all_content']['A1.2'][0];?></li>
                                    <li><?php echo $values_core_pdf['cefr_all_content']['A1.2'][1];?></li>
                                    <li><?php echo $values_core_pdf['cefr_all_content']['A1.2'][2];?></li>
                                    <li><?php echo $values_core_pdf['cefr_all_content']['A1.2'][3];?></li>
                                </ul>
                            </td>
                            <td style="border:2px solid <?php echo get_grid_class($p, 'A1.3'); ?>;padding:8px 8px;vertical-align: text-top;">
                                <strong style="color:<?php echo get_grid_text_color($p, 'A1.3')?>">Step Forward 3 (A1.3)</strong>
                                <ul style="padding: 0; padding-left: 20px;font-size:12px;color:<?php echo get_grid_text_color($p, 'A1.3')?>">
                                    <li><?php echo $values_core_pdf['cefr_all_content']['A1.3'][0];?></li>
                                    <li><?php echo $values_core_pdf['cefr_all_content']['A1.3'][1];?></li>
                                    <li><?php echo $values_core_pdf['cefr_all_content']['A1.3'][2];?></li>
                                    <li><?php echo $values_core_pdf['cefr_all_content']['A1.3'][3];?></li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td style="border:2px solid <?php echo get_grid_class($p, 'A2.1'); ?>;padding:8px 8px;vertical-align: text-top;">
                                <strong style="color:<?php echo get_grid_text_color($p, 'A2.1')?>">Step Up 1 (A2.1)</strong>
                                <ul style="padding: 0; padding-left: 20px;font-size:12px;color:<?php echo get_grid_text_color($p, 'A2.1')?>">
                                    <li><?php echo $values_core_pdf['cefr_all_content']['A2.1'][0];?></li>
                                    <li><?php echo $values_core_pdf['cefr_all_content']['A2.1'][1];?></li>
                                    <li><?php echo $values_core_pdf['cefr_all_content']['A2.1'][2];?></li>
                                    <li><?php echo $values_core_pdf['cefr_all_content']['A2.1'][3];?></li>	
                                </ul>
                            </td>
                            <td style="border:2px solid <?php echo get_grid_class($p, 'A2.2'); ?>;padding:8px 8px;vertical-align: text-top;">
                                <strong style="color:<?php echo get_grid_text_color($p, 'A2.2')?>">Step Up 2 (A2.2)</strong>
                                <ul style="padding: 0;padding-left: 20px;font-size:12px;color:<?php echo get_grid_text_color($p, 'A2.2')?>">
                                    <li><?php echo $values_core_pdf['cefr_all_content']['A2.2'][0];?></li>
                                    <li><?php echo $values_core_pdf['cefr_all_content']['A2.2'][1];?></li>
                                    <li><?php echo $values_core_pdf['cefr_all_content']['A2.2'][2];?></li>
                                    <li><?php echo $values_core_pdf['cefr_all_content']['A2.2'][3];?></li>
                                </ul>
                            </td>
                            <td style="border:2px solid <?php echo get_grid_class($p, 'A2.3'); ?>;padding:8px 8px;vertical-align: text-top;">
                                <strong style="color:<?php echo get_grid_text_color($p, 'A2.3')?>">Step Up 3 (A2.3)</strong>
                                <ul style="padding: 0; padding-left: 20px;font-size:12px;color:<?php echo get_grid_text_color($p, 'A2.3')?>">
                                    <li><?php echo $values_core_pdf['cefr_all_content']['A2.3'][0];?></li>
                                    <li><?php echo $values_core_pdf['cefr_all_content']['A2.3'][1];?></li>
                                    <li><?php echo $values_core_pdf['cefr_all_content']['A2.3'][2];?></li>
                                    <li><?php echo $values_core_pdf['cefr_all_content']['A2.3'][3];?></li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td style="border:2px solid <?php echo get_grid_class($p, 'B1.1'); ?>;padding:8px 8px;vertical-align: text-top;">
                                <strong style="color:<?php echo get_grid_text_color($p, 'B1.1')?>">Step Ahead 1 (B1.1)</strong>
                                <ul style="padding: 0; padding-left: 20px;font-size:12px;color:<?php echo get_grid_text_color($p, 'B1.1')?>">
                                    <li><?php echo $values_core_pdf['cefr_all_content']['B1.1'][0];?></li>
                                    <li><?php echo $values_core_pdf['cefr_all_content']['B1.1'][1];?></li>
                                    <li><?php echo $values_core_pdf['cefr_all_content']['B1.1'][2];?></li>
                                    <li><?php echo $values_core_pdf['cefr_all_content']['B1.1'][3];?></li>
                                </ul>
                            </td>
                            <td style="border:2px solid <?php echo get_grid_class($p, 'B1.2'); ?>;padding:8px 8px;vertical-align: text-top;">
                                <strong style="color:<?php echo get_grid_text_color($p, 'B1.2')?>">Step Ahead 2 (B1.2)</strong>
                                <ul style="padding: 0;padding-left: 20px;font-size:12px;color:<?php echo get_grid_text_color($p, 'B1.2')?>">
                                    <li><?php echo $values_core_pdf['cefr_all_content']['B1.2'][0];?></li>
                                    <li><?php echo $values_core_pdf['cefr_all_content']['B1.2'][1];?></li>
                                    <li><?php echo $values_core_pdf['cefr_all_content']['B1.2'][2];?></li>
                                    <li><?php echo $values_core_pdf['cefr_all_content']['B1.2'][3];?></li>
                                </ul>
                            </td>
                            <td style="border:2px solid <?php echo get_grid_class($p, 'B1.3'); ?>;padding:8px 8px;vertical-align: text-top;">
                                <strong style="color:<?php echo get_grid_text_color($p, 'B1.3')?>">Step Ahead 3 (B1.3)</strong>
                                <ul style="adding: 0; padding-left: 20px;font-size:12px;color:<?php echo get_grid_text_color($p, 'B1.3')?>">
                                    <li><?php echo $values_core_pdf['cefr_all_content']['B1.3'][0];?></li>
                                    <li><?php echo $values_core_pdf['cefr_all_content']['B1.3'][1];?></li>
                                    <li><?php echo $values_core_pdf['cefr_all_content']['B1.3'][2];?></li>
                                    <li><?php echo $values_core_pdf['cefr_all_content']['B1.3'][3];?></li>
                                </ul>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <p style="margin-bottom: 2px;font-weight: 400;color:#000;font-size: 12px;text-align: center;"><?php echo lang('app.language_pdf_certicate_find_out');?></p>
                    <?php echo lang('app.language_cats_step_website_link_core_pdf');?>
                </td>
            </tr>
        </table>
        <?php } ?>
    </body>
</html>
<?php
        $pdf_content = ob_get_clean();
        $options = new Options();
        $options->set('isRemoteEnabled', TRUE);
        $options->set('isHtml5ParserEnabled', TRUE);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($pdf_content);
        $dompdf->set_option('isRemoteEnabled', TRUE);
        $dompdf->set_option('isHtml5ParserEnabled', TRUE);  
        // (Optional) Setup the paper size and orientation 
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->getOptions()->setIsFontSubsettingEnabled(true);
        
        // Render the HTML as PDF
        $dompdf->render();
        $output = $dompdf->output();
        $name = $values_core_pdf['thirdparty_id'];
        
        //Creating folder using taskid
        $dir = $bulk_download_path.$values_core_pdf['task_id'];
        $dir1 = $bulk_download_path.$values_core_pdf['task_id'].'/'.$values_core_pdf['file_name'];
        $oldmask = umask(0);
        if (!is_dir($dir)){
            if (!mkdir($dir, 0777, TRUE)) {
                error_log(date('[Y-m-d H:i e]') . ' Core folder fails '. $dir .PHP_EOL, 3, LOG_FILE_PDF);
            }else{
                error_log(date('[Y-m-d H:i e]') .'Core folder created ' . $dir . PHP_EOL, 3, LOG_FILE_PDF);
                if (!mkdir($dir1, 0777, TRUE)) {
                    error_log(date('[Y-m-d H:i e]') . ' Core folder fails '. $dir1 .PHP_EOL, 3, LOG_FILE_PDF);
                }else{
                    error_log(date('[Y-m-d H:i e]') .'Core sub folder created ' . $dir1 . PHP_EOL, 3, LOG_FILE_PDF);
                }
            }
        }
        umask($oldmask);
        
        $path = $bulk_download_path.$values_core_pdf['task_id'].'/'.$values_core_pdf['file_name'].'/';
        //writing files inside folder using dompdf
        if ( !write_file($path . $name . '.pdf', $dompdf->output())){
            error_log(date('[Y-m-d H:i e]') .'Core File not written' . $path . $name . '.pdf' . PHP_EOL, 3, LOG_FILE_PDF);
        }else{
            error_log(date('[Y-m-d H:i e]') .'Core File written ' . $path . $name . '.pdf' . PHP_EOL, 3, LOG_FILE_PDF);
        }
    }
}

function get_grid_class($current_position, $level){
    $level_array = array('A1.1','A1.2','A1.3','A2.1','A2.2','A2.3','B1.1','B1.2','B1.3');
    $p = array_search($level, $level_array);
    if($current_position > $p){
        $class = "#ff9528";
    }elseif ($current_position == $p){
        $class = "#fa47a2";
    }else{
        $class = "#dee2e1";
    }
    return $class;
}
function get_grid_text_color($current_position, $level){
    $level_array = array('A1.1','A1.2','A1.3','A2.1','A2.2','A2.3','B1.1','B1.2','B1.3');
    $p = array_search($level, $level_array);
    if($current_position >= $p){
        $class = "#000000";
    }else{
        $class = "'';opacity: 0.5;";
    }
    return $class;
}
?>