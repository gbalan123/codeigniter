<?php
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 
function generatebenchmarkResultsZipPDF($values_bench_pdf = FALSE){
    $efsfile = new \Config\Efsfilepath();
    $efsfilepath = $efsfile->get_Efs_path();
    $bulk_download_path = $efsfilepath->efs_uploads_bulk_dwn;
    $efs_custom_log_path = $efsfilepath->efs_custom_log;
    define("LOG_FILE_PDF", $efs_custom_log_path . "pdf_log.txt");
    if($values_bench_pdf){
        ob_start();
        ?>
<html>

<head>
    <title>CATs Stepcheck Results - Pdf</title>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/style.css'); ?>">
</head>

<body style="font-size:14px;font-family:Verdana, Geneva, sans-serif; padding:5px 10px; border: 1px solid #ddd; border-radius: 4px; width: 700px; margin: 0 auto; ">
    <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#fff; padding:20px 30px 0;;font-family:Kreon,Regular">
        <tr>
            <td width="70%">
                <img src="<?php echo base_url('public/images/cats_logo_pdf.png'); ?>" style="width: 210px;" />
                <p style="font-size:30px;color:#e98523;margin-top:10px;margin-bottom:10px;font-weight:600;font-family: 'kreonregular';">StepCheck report</p>
                <span style="font-size:25px;font-weight:600;font-family: 'kreonregular';"><?php echo $values_bench_pdf['data']['user_name']; ?></span>
            </td>
            <td align="right" valign="top">
                <p style="margin:0px 20px 5px 0; font-family:Verdana, Geneva, sans-serif; font-size:15px;"> 
                    <strong>ID:</strong><?php echo $values_bench_pdf['data']['id']; ?></p>
                <?php if($values_bench_pdf['qr_code_url'] != "" && $values_bench_pdf['google_url'] != ""){ 
                     $_url = explode("/", $values_bench_pdf['qr_code_url']);
                     $file_name = end($_url);
                     $parts = explode('.',$file_name);
                    ?>
                    <div style="margin:0px 23px 5px 0;" class="qr_pic"> <img style="width:100px" src="<?php echo @image_png($parts[0],$parts[1],'qrcodes_benchmark'); ?>"></div>
                <p style="margin:0px 5px 5px 0; font-size: 11px;"><a style="color:#117dc1; text-decoration: none;" href="<?php echo $values_bench_pdf['google_url']; ?>" target="_blank"><?php echo $values_bench_pdf['google_url']; ?></a></p>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td width="50%" colspan="2">
                <table width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top:10px">
                    <tr>
                        <td width="50%" style="font-weight:600;">
                            <span style="font: 14px Verdana, Geneva, sans-serif; font-weight:bold;">Date of Exam: &nbsp;&nbsp;&nbsp;<?php echo $values_bench_pdf['data']['result_date'];?></span> 
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table width="100%"  cellspacing="0" cellpadding="0" border="0" style="margin:20px 0 0;">
        <tr>
            <td align="center">
                <p style="font-size:18px;font-weight:600;margin: 0"><?php echo lang('app.language_pdf_certicate_statement');?></p>
            </td>
        </tr>
    </table>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#fff; padding:20px 30px 20px 30px;">
        <thead>
            <tr style="background-color:#a6a6a6;">
                <th width="50%" style="border-bottom-width:4px;border-left-width:0px;border-right-width:2px;border-top-width:0px;border-color:#ffffff;border-style:solid;padding:10px;color:#ffffff">SKILL</th>
                <th width="50%" style="border-bottom-width:4px;border-left-width:0px;border-right-width:0px;border-top-width:0px;border-color:#ffffff;border-style:solid;padding:10px;color:#ffffff">CEFR LEVEL</th>
            </tr>
        </thead>
        <tbody style="font-weight:bold;">
            <?php 
            $cefr_level = $values_bench_pdf['data']['benchmark_tds_data'];
            $msg_not_tested = lang('app.language_school_benchmark_certificate_level_not_tested');
            $css_yes = "border-bottom-width:2px;border-left-width:0px;border-right-width:2px;border-top-width:0px;border-color:#ffffff;border-style:solid;padding:10px;text-align:center;";
            $css_no = "border-bottom-width:2px;border-left-width:0px;border-right-width:2px;border-top-width:0px;border-color:#ffffff;border-style:solid;padding:10px;text-align:center;color:#cbcbcb;";
            ?>
            <tr style="background-color:#e2e2e2;">
                <td style="<?php echo isset($cefr_level['listening']) ? $css_yes : $css_no; ?>">
                    <?php echo lang('app.language_school_benchmark_certificate_level_skill_listening'); ?>
                </td>
                <td style="<?php echo isset($cefr_level['listening']) ? $css_yes : $css_no; ?>">
                    <?php echo isset($cefr_level['listening']) ? substr($cefr_level['listening']['level'], 0, 2) : $msg_not_tested; ?>
                </td>
            </tr>
            <tr style="background-color:#f0f0f0;">
                <td style="<?php echo isset($cefr_level['reading']) ? $css_yes : $css_no; ?>">
                    <?php echo lang('app.language_school_benchmark_certificate_level_skill_reading'); ?>
                </td>
                <td style="<?php echo isset($cefr_level['reading']) ? $css_yes : $css_no; ?>">
                   <?php echo isset($cefr_level['reading']) ? substr($cefr_level['reading']['level'], 0, 2) : $msg_not_tested; ?>
                </td>
            </tr>
            <tr style="background-color:#e2e2e2;">
                <td style="<?php echo isset($cefr_level['writing']) ? $css_yes : $css_no; ?>">
                   <?php echo lang('app.language_school_benchmark_certificate_level_skill_writing'); ?>
                </td>
                <td style="<?php echo isset($cefr_level['writing']) ? $css_yes : $css_no; ?>">
                   <?php echo isset($cefr_level['writing']) ? substr($cefr_level['writing']['level'], 0, 2) : $msg_not_tested; ?>
                </td>
            </tr>
            <tr style="background-color:#f0f0f0;">
                <td style="<?php echo isset($cefr_level['speaking']) ? $css_yes : $css_no; ?>">
                   <?php echo lang('app.language_school_benchmark_certificate_level_skill_speaking'); ?>
                </td>
                <td style="<?php echo isset($cefr_level['speaking']) ? $css_yes : $css_no; ?>">
                   <?php echo isset($cefr_level['speaking']) ? substr($cefr_level['speaking']['level'], 0, 2) : $msg_not_tested;  ?>
                </td>
            </tr>
        </tbody>
    </table>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#fff; padding:15px 30px 15px 30px;">
        <tbody>
            <?php  
            $cefr_level_content = $values_bench_pdf['data']['lang_content_level'];
            $css_yes_content = "border-bottom-width:1px;border-left-width:1px;border-right-width:1px;border-top-width:1px;border-color:#d9d9d9;border-style:solid;padding:10px;text-align:center;font-size:16px; font-weight:600;text-align:left;";
            $css_no_content = "border-bottom-width:1px;border-left-width:1px;border-right-width:1px;border-top-width:1px;border-color:#d9d9d9;border-style:solid;padding:10px;text-align:center;font-size:16px; font-weight:600;text-align:left;color:#cbcbcb;";
            $css_yes_content_p = "font-size:14px;font-weight:400;text-align:left;margin-top:5px;";
            $css_no_content_p = "font-size:14px;font-weight:400;text-align:left;color:#cbcbcb;margin-top:5px;";
            ?>
            <tr>
                <td style="<?php echo isset($cefr_level_content['listening']) ? $css_yes_content : $css_no_content; ?>">
                   <?php echo lang('app.language_school_benchmark_certificate_level_skill_listening'); ?>
                    <p style="<?php echo isset($cefr_level_content['listening']) ? $css_yes_content_p : $css_no_content_p; ?>">
                        <?php echo isset($cefr_level_content['listening']) ? $cefr_level_content['listening'][0] : $msg_not_tested; ?>
                    </p>
                </td>
            </tr>
            <tr>
                <td style="<?php echo isset($cefr_level_content['reading']) ? $css_yes_content : $css_no_content; ?>">
                    <?php echo lang('app.language_school_benchmark_certificate_level_skill_reading'); ?>
                    <p style="<?php echo isset($cefr_level_content['reading']) ? $css_yes_content_p : $css_no_content_p; ?>">
                        <?php echo isset($cefr_level_content['reading']) ? $cefr_level_content['reading'][0] : $msg_not_tested; ?>
                    </p>
                </td>
            </tr>
            <tr>
                <td style="<?php echo isset($cefr_level_content['writing']) ? $css_yes_content : $css_no_content; ?>">
                     <?php echo lang('app.language_school_benchmark_certificate_level_skill_writing'); ?>
                    <p style="<?php echo isset($cefr_level_content['writing']) ? $css_yes_content_p : $css_no_content_p; ?>">
                        <?php echo isset($cefr_level_content['writing']) ? $cefr_level_content['writing'][0] : $msg_not_tested; ?>
                    </p>
                </td>
            </tr>
            <tr>
                    <td style="<?php echo isset($cefr_level_content['speaking']) ? $css_yes_content : $css_no_content; ?>">
                            <?php echo lang('app.language_school_benchmark_certificate_level_skill_speaking'); ?>                          
                        <p style="<?php echo isset($cefr_level_content['speaking']) ? $css_yes_content_p : $css_no_content_p; ?>">
                                <?php echo isset($cefr_level_content['speaking']) ? $cefr_level_content['speaking'][0] : $msg_not_tested; ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="font-size:12px;font-weight:600;padding:10px;">
                            The CATs StepCheck Service offers independent assessment of all 4 Language skills; listening; reading; writing; speaking. This learner has been assessed in the skills highlighted in black.
                    </td>
                </tr>
        </tbody>
    </table>
    <table width="100%"  cellspacing="0" cellpadding="0" border="0" style="margin-top:20px;">
        <tr>
            <td align="center">
                <p style="font-size:14px;margin-bottom: 4px;font-weight: 400;color:#000"><?php echo lang('app.language_pdf_certicate_find_out');?></p>
                <?php echo lang('app.language_cats_step_website_link');?>
            </td>
        </tr>
    </table>
</body>

</html>

<?php
    
        $pdf_content = ob_get_clean();
        $options = new Options();
        $options->set('isRemoteEnabled', TRUE);
        $options->set('isHtml5ParserEnabled', TRUE);
        $dompdf = new Dompdf($options);
        //echo $pdf_content; die;

        $dompdf->loadHtml($pdf_content);
        
        $dompdf->set_option('isRemoteEnabled', TRUE);
        $dompdf->set_option('isHtml5ParserEnabled', TRUE);  
        
        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->getOptions()->setIsFontSubsettingEnabled(true);
        
        // Render the HTML as PDF
        $dompdf->render();
        $output = $dompdf->output();
        $name = $values_bench_pdf['data']['id'].'-'.$values_bench_pdf['data']['token'];
        
        //Creating folder using taskid 
        $dir = $bulk_download_path.$values_bench_pdf['task_id'];
        $dir1 = $bulk_download_path.$values_bench_pdf['task_id'].'/'.$values_bench_pdf['file_name'];
        $oldmask = umask(0);
        if (!is_dir($dir)){
            if (!mkdir($dir, 0777, TRUE)) {
                error_log(date('[Y-m-d H:i e]') . ' StepCheck folder fails '. $dir .PHP_EOL, 3, LOG_FILE_PDF);
            }else{
                error_log(date('[Y-m-d H:i e]') .'StepCheck folder created ' . $dir . PHP_EOL, 3, LOG_FILE_PDF);
                if (!mkdir($dir1, 0777, TRUE)) {
                    error_log(date('[Y-m-d H:i e]') . ' StepCheck folder fails '. $dir1 .PHP_EOL, 3, LOG_FILE_PDF);
                }else{
                    error_log(date('[Y-m-d H:i e]') .'StepCheck sub folder created ' . $dir1 . PHP_EOL, 3, LOG_FILE_PDF);
                }
            }
        }
        umask($oldmask);
        
        $path = $bulk_download_path.$values_bench_pdf['task_id'].'/'.$values_bench_pdf['file_name'].'/';
        //writing files inside folder using dompdf
        if ( !write_file($path . $name . '.pdf', $dompdf->output())){
            error_log(date('[Y-m-d H:i e]') .'StepCheck File not written' . $path . $name . '.pdf' . PHP_EOL, 3, LOG_FILE_PDF);
        }else{
            error_log(date('[Y-m-d H:i e]') .'StepCheck File written ' . $path . $name . '.pdf' . PHP_EOL, 3, LOG_FILE_PDF);
        }
        
    }
}