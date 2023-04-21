<?php
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 
function generatehigherResultsPDF($values_higher_pdf = FALSE){
    if($values_higher_pdf){
    ob_start();
    ?>
<!DOCTYPE html>    
<html>
<head>
    <title>CATs Step Higher Results - Pdf</title>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/style.css'); ?>">
</head>
<body style="font-size:14px;font-family:Verdana, Geneva, sans-serif; padding:5px 10px; border: 1px solid #ddd; border-radius: 4px;padding:30px;">
    <table width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td width="50%">
                <table width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top:20px;">
                    <tr>
                        <td>
                            <img src="<?php echo base_url('public/images/cats_logo_pdf.png'); ?>" style="width: 210px;" alt="..." />
                        </td>
                    </tr>
                </table>
                <table width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top:20px;">
                    <tr>
                        <td colspan="2" style="font-size:30px;color:#e98523;margin-top:10px;margin-bottom:10px;font-weight:600;font-family: 'kreonregular';">
                            <?php echo $values_higher_pdf['data']['product_name']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="font-size:25px;font-weight:600;font-family: 'kreonregular';">
                            <?php echo $values_higher_pdf['data']['user_name']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top:20px;font-weight:600;">Date of Exam:</td>
                        <td style="font-weight:400;padding-top:20px;"><?php echo isset($values_higher_pdf['data']['event_date']) ? $values_higher_pdf['data']['event_date']: $values_higher_pdf['data']['result_date'];?></td>
                    </tr>
                    <tr>
                        <td style="font-weight:600;">Invigilation Status:</td>
                        <td style="font-weight:400;"><?php echo $values_higher_pdf['data']['is_supervised'];?></td>
                    </tr>
                </table>
            </td>
            <td align="right" width="50%">
                <p style="margin:0px 7px 5px 0;">ID:  <?php echo $values_higher_pdf['data']['id']; ?></p>
                <?php if($values_higher_pdf['qr_code_url'] != "" && $values_higher_pdf['google_url'] != ""){
                     $_url = explode("/", $values_higher_pdf['qr_code_url']);
                     $file_name = end($_url);
                     $parts = explode('.',$file_name);
                     ?>
                    <div style="margin:0px 23px 5px 0;" class="qr_pic"> <img style="width:100px" src="<?php echo @image_png($parts[0],$parts[1],'qrcodes_higher'); ?>" alt="..."></div>
                    <p style="margin:0px 5px 5px 0; font-size: 11px;"><a style="color:#117dc1; text-decoration: none;" href="<?php echo $values_higher_pdf['google_url']; ?>" target="_blank"><?php echo $values_higher_pdf['google_url']; ?></a></p>
                <?php } ?>
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
    <table width="34%" cellspacing="0" cellpadding="0" border="0" style="margin-top:20px;">
        <tr>
            <td style="font-weight:600;">
                Overall score
            </td>
            <td style="font-weight:600;">
                <?php echo $values_higher_pdf['data']['higher_certificate_data']['overall']['score']?>
            </td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td style="font-weight:600;">
                CEFR Level
            </td>
            <td style="font-weight:600;">
                <?php
                $overall_level = substr($values_higher_pdf['data']['higher_certificate_data']['overall']['level'], 0, 2);
                echo $overall_level;
                ?>
            </td>
            <td>&nbsp;</td>
        </tr>
    </table>
    <table width="40%" cellspacing="0" cellpadding="0" border="0" style="margin-top:20px;">
        <tr style="font-weight:600;">
            <td>Listening</td>
            <td><?php echo $values_higher_pdf['data']['higher_certificate_data']['listening']['score'];?></td>
            <td><?php echo substr($values_higher_pdf['data']['higher_certificate_data']['listening']['level'], 0, 2);?></td>
        </tr>
        <tr style="font-weight:600;">
            <td>Reading</td>
            <td><?php echo $values_higher_pdf['data']['higher_certificate_data']['reading']['score'];?></td>
            <td><?php echo substr($values_higher_pdf['data']['higher_certificate_data']['reading']['level'], 0, 2);?></td>
        </tr>
        <tr style="font-weight:600;">
            <td>Writing</td>
            <td><?php echo $values_higher_pdf['data']['higher_certificate_data']['writing']['score'];?></td>
            <td><?php echo substr($values_higher_pdf['data']['higher_certificate_data']['writing']['level'], 0, 2);?></td>
        </tr>
        <tr style="font-weight:600;">
            <td>Speaking</td>
            <td><?php echo $values_higher_pdf['data']['higher_certificate_data']['speaking']['score'];?></td>
            <td><?php echo substr($values_higher_pdf['data']['higher_certificate_data']['speaking']['level'], 0, 2);?></td>
        </tr>
    </table>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top:20px;">
        <tr>
            <td align="center" style="<?php echo $overall_level == "A0" ? "visibility:visible" : "visibility:hidden"; ?>"><img src="<?php echo base_url('public/images/higher_certificate/A0.png'); ?>"  alt="..." /></td>
            <td align="center" style="<?php echo $overall_level == "A1" ? "visibility:visible" : "visibility:hidden"; ?>"><img src="<?php echo base_url('public/images/higher_certificate/A1.png');  ?>" alt="..." /></td>
            <td align="center" style="<?php echo $overall_level == "A2" ? "visibility:visible" : "visibility:hidden"; ?>"><img src="<?php echo base_url('public/images/higher_certificate/A2.png');  ?>" alt="..." /></td>
            <td align="center" style="<?php echo $overall_level == "B1" ? "visibility:visible" : "visibility:hidden"; ?>"><img src="<?php echo base_url('public/images/higher_certificate/B1.png');  ?>" alt="..." /></td>
            <td align="center" style="<?php echo $overall_level == "B2" ? "visibility:visible" : "visibility:hidden"; ?>"><img src="<?php echo base_url('public/images/higher_certificate/B2.png');  ?>" alt="..." /></td>
            <td align="center" style="<?php echo $overall_level == "C1" ? "visibility:visible" : "visibility:hidden"; ?>"><img src="<?php echo base_url('public/images/higher_certificate/C1.png');  ?>" alt="..." /></td>
            <td align="center" style="<?php echo $overall_level == "C2" ? "visibility:visible" : "visibility:hidden"; ?>"><img src="<?php echo base_url('public/images/higher_certificate/C2.png');  ?>" alt="..." /></td>
        </tr>
        <tr>
            <td align="center" style="background-color:#f9d790;padding:5px;border-color:#ffffff;border-width:1px;border-style:solid;color:#ffffff;">A0</td>
            <td align="center" style="background-color:#fbc259;padding:5px;border-color:#ffffff;border-width:1px;border-style:solid;color:#ffffff;">A1</td>
            <td align="center" style="background-color:#e79033;padding:5px;border-color:#ffffff;border-width:1px;border-style:solid;color:#ffffff;">A2</td>
            <td align="center" style="background-color:#e16251;padding:5px;border-color:#ffffff;border-width:1px;border-style:solid;color:#ffffff;">B1</td>
            <td align="center" style="background-color:#01bfdf;padding:5px;border-color:#ffffff;border-width:1px;border-style:solid;color:#ffffff;">B2</td>
            <td align="center" style="background-color:#035086;padding:5px;border-color:#ffffff;border-width:1px;border-style:solid;color:#ffffff;">C1</td>
            <td align="center" style="background-color:#7f4194;padding:5px;border-color:#ffffff;border-width:1px;border-style:solid;color:#ffffff;">C2</td>
        </tr>
    </table>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top:20px;">
        <style>
            .tr_colour_A0{background-color:#f9d790;color:#ffffff;}
            .tr_colour_A1{background-color:#fbc259;color:#ffffff;}
            .tr_colour_A2{background-color:#e79033;color:#ffffff;}
            .tr_colour_B1{background-color:#e16251;color:#ffffff;}
            .tr_colour_B2{background-color:#01bfdf;color:#ffffff;}
            .tr_colour_C1{background-color:#035086;color:#ffffff;}
            .tr_colour_C2{background-color:#7f4194;color:#ffffff;}
        </style>
        <tr class="<?php echo "tr_colour_".substr($values_higher_pdf['data']['higher_certificate_data']['listening']['level'],0,2);?>">
            <td style="padding:20px;border-style:solid;border-color:#ffffff;border-bottom-width:15px;border-left-width:0;border-right-width:0;border-top-width:0;">Listening:</td>
            <td style="border-style:solid;border-color:#ffffff;border-bottom-width:15px;border-left-width:0;border-right-width:0;border-top-width:0;"><?php echo $values_higher_pdf['data']['lang_content_level_higher']['listening'][0];?></td>
        </tr>
        <tr class="<?php echo "tr_colour_".substr($values_higher_pdf['data']['higher_certificate_data']['reading']['level'],0,2);?>">
            <td style="padding:20px;border-style:solid;border-color:#ffffff;border-bottom-width:15px;border-left-width:0;border-right-width:0;border-top-width:0;">Reading:</td>
            <td style="border-style:solid;border-color:#ffffff;border-bottom-width:15px;border-left-width:0;border-right-width:0;border-top-width:0;"><?php echo $values_higher_pdf['data']['lang_content_level_higher']['reading'][0];?></td>
        </tr>
        <tr class="<?php echo "tr_colour_".substr($values_higher_pdf['data']['higher_certificate_data']['writing']['level'],0,2);?>">
            <td style="padding:20px;border-style:solid;border-color:#ffffff;border-bottom-width:15px;border-left-width:0;border-right-width:0;border-top-width:0;">Writing:</td>
            <td style="border-style:solid;border-color:#ffffff;border-bottom-width:15px;border-left-width:0;border-right-width:0;border-top-width:0;"><?php echo $values_higher_pdf['data']['lang_content_level_higher']['writing'][0];?></td>
        </tr>
        <tr class="<?php echo "tr_colour_".substr($values_higher_pdf['data']['higher_certificate_data']['speaking']['level'],0,2);?>">
            <td style="padding:20px;border-style:solid;border-color:#ffffff;border-bottom-width:15px;border-left-width:0;border-right-width:0;border-top-width:0;">Speaking:</td>
            <td style="border-style:solid;border-color:#ffffff;border-bottom-width:15px;border-left-width:0;border-right-width:0;border-top-width:0;"><?php echo $values_higher_pdf['data']['lang_content_level_higher']['speaking'][0];?></td>
        </tr>
    </table>
    <table width="100%"  cellspacing="0" cellpadding="0" border="0" style="margin-top:30px;">
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
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($pdf_content);
        
        $dompdf->set_option('isRemoteEnabled', TRUE);
        
        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->getOptions()->setIsFontSubsettingEnabled(true);
        
        // Render the HTML as PDF
        $dompdf->render();
        
        $name = "Higher result on - " . date("d-m-Y");
        $dompdf->stream($name);
        exit();
        
    }
}