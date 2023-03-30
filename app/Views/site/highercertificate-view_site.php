<?php 
$style_popup = "padding: 0px 20px 20px 20px;height: 520px;overflow: auto;";
$style_none_popup = " font-size: 14px; font-family: Verdana, Geneva, sans-serif;padding: 5px 30px;border: 1px solid #117cc2;border-radius: 4px;width: 1024px;margin: 0 auto; padding: 0px 20px 20px 20px;";
 ?>
<div class="panel panel-default" style="<?php echo ($higher_results_view['display'] == "popup") ? $style_popup : $style_none_popup;?>">
<title>CATs Step Higher Results</title>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
    <tr>
        <td width="50%">
            <table width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top:20px;">
                <tr>
                    <td>
                        <img src="<?php echo base_url('public/images/cats_logo_pdf.png'); ?>" style="width: 210px;" />
                    </td>
                </tr>
            </table>
            <table width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top:20px;">
                <tr>
                    <td colspan="2" style="font-size: 30px;color: #e98523;font-weight: 600;font-family: 'kreonregular';">
                        <?php echo $higher_results_view['product_name'];?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 25px;font-weight: 600;font-family: 'kreonregular';padding-top:10px;">
                        <?php echo $higher_results_view['user_name']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="padding-top:20px;font-weight:600;">Date of Exam:</td>
                    <td style="padding-top:20px;font-weight:400;"><?php echo isset($higher_results_view['event_date']) ? $higher_results_view['event_date']: $higher_results_view['result_date']; ?></td>
                </tr>
                <tr>
                    <td style="font-weight:600;padding-right:10px;">Invigilation Status: </td>
                    <td style="font-weight:400;"><?php echo $higher_results_view['is_supervised'];?></td>
                </tr>
            </table>
        </td>
        <td align="right" width="50%" style="padding-top:33px;vertical-align:top;">
            <p class="t-indext" style="text-indent: 18px;font-size: 19px;margin-bottom: 0px; white-space: nowrap;font-family:Verdana, Geneva, sans-serif;">ID:  <?php echo $higher_results_view['id']; ?></p>
        </td>
    </tr>
</table>
<table width="100%"  cellspacing="0" cellpadding="0" border="0" style="margin:20px 0 0;">
    <tr>
        <td align="center">
            <p style="font-size:20px;font-weight:600;margin: 0"><?php echo lang('app.language_pdf_certicate_statement');?></p>
        </td>
    </tr>
</table>
<table width="31.5%" cellspacing="0" cellpadding="0" border="0" style="margin-top:20px;">
    <tr>
        <td style="font-weight:600;">
            Overall score
        </td>
        <td style="font-weight:600;">
                <?php  echo @$higher_results_view['higher_certificate_data']['overall']['score']?>
        </td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td style="font-weight:600;">
            CEFR Level
        </td>
        <td style="font-weight:600;">
            <?php
                $overall_level = substr(@$higher_results_view['higher_certificate_data']['overall']['level'], 0, 2);
            echo $overall_level;
            ?>
        </td>
        <td>&nbsp;</td>
    </tr>
</table>
<table width="40%" cellspacing="0" cellpadding="0" border="0" style="margin-top:20px;">
    <tr style="font-weight:600;">
        <td>Listening</td>
            <td><?php echo @$higher_results_view['higher_certificate_data']['listening']['score'];?></td>
            <td><?php echo @substr($higher_results_view['higher_certificate_data']['listening']['level'], 0, 2);?></td>
    </tr>
    <tr style="font-weight:600;">
        <td>Reading</td>
            <td><?php echo @$higher_results_view['higher_certificate_data']['reading']['score'];?></td>
            <td><?php echo @substr($higher_results_view['higher_certificate_data']['reading']['level'], 0, 2);?></td>
    </tr>
    <tr style="font-weight:600;">
        <td>Writing</td>
            <td><?php echo @$higher_results_view['higher_certificate_data']['writing']['score'];?></td>
            <td><?php echo @substr($higher_results_view['higher_certificate_data']['writing']['level'], 0, 2);?></td>
    </tr>
    <tr style="font-weight:600;">
        <td>Speaking</td>
            <td><?php echo @$higher_results_view['higher_certificate_data']['speaking']['score'];?></td>
            <td><?php echo @substr($higher_results_view['higher_certificate_data']['speaking']['level'], 0, 2);?></td>
    </tr>
</table>
<table width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top:20px;">
    <tr>
        <td align="center" style="<?php echo $overall_level == "A0" ? "visibility:visible" : "visibility:hidden"; ?>"><img src="<?php echo base_url('public/images/higher_certificate/A0.png'); ?>" /></td>
        <td align="center" style="<?php echo $overall_level == "A1" ? "visibility:visible" : "visibility:hidden"; ?>"><img src="<?php echo base_url('public/images/higher_certificate/A1.png');  ?>" /></td>
        <td align="center" style="<?php echo $overall_level == "A2" ? "visibility:visible" : "visibility:hidden"; ?>"><img src="<?php echo base_url('public/images/higher_certificate/A2.png');  ?>" /></td>
        <td align="center" style="<?php echo $overall_level == "B1" ? "visibility:visible" : "visibility:hidden"; ?>"><img src="<?php echo base_url('public/images/higher_certificate/B1.png');  ?>" /></td>
        <td align="center" style="<?php echo $overall_level == "B2" ? "visibility:visible" : "visibility:hidden"; ?>"><img src="<?php echo base_url('public/images/higher_certificate/B2.png');  ?>" /></td>
        <td align="center" style="<?php echo $overall_level == "C1" ? "visibility:visible" : "visibility:hidden"; ?>"><img src="<?php echo base_url('public/images/higher_certificate/C1.png');  ?>" /></td>
        <td align="center" style="<?php echo $overall_level == "C2" ? "visibility:visible" : "visibility:hidden"; ?>"><img src="<?php echo base_url('public/images/higher_certificate/C2.png');  ?>" /></td>
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
        <tr class="<?php echo "tr_colour_".substr(@$higher_results_view['higher_certificate_data']['listening']['level'],0,2);?>">
        <td style="padding:20px;border-style:solid;border-color:#ffffff;border-bottom-width:15px;border-left-width:0;border-right-width:0;border-top-width:0;">Listening:</td>
            <td style="border-style:solid;border-color:#ffffff;border-bottom-width:15px;border-left-width:0;border-right-width:0;border-top-width:0;"><?php echo @$higher_results_view['lang_content_level_higher']['listening'][0];?></td>
    </tr>
        <tr class="<?php echo "tr_colour_".substr(@$higher_results_view['higher_certificate_data']['reading']['level'],0,2);?>">
        <td style="padding:20px;border-style:solid;border-color:#ffffff;border-bottom-width:15px;border-left-width:0;border-right-width:0;border-top-width:0;">Reading:</td>
            <td style="border-style:solid;border-color:#ffffff;border-bottom-width:15px;border-left-width:0;border-right-width:0;border-top-width:0;"><?php echo @$higher_results_view['lang_content_level_higher']['reading'][0];?></td>
    </tr>
        <tr class="<?php echo "tr_colour_".substr(@$higher_results_view['higher_certificate_data']['writing']['level'],0,2);?>">
        <td style="padding:20px;border-style:solid;border-color:#ffffff;border-bottom-width:15px;border-left-width:0;border-right-width:0;border-top-width:0;">Writing:</td>
            <td style="border-style:solid;border-color:#ffffff;border-bottom-width:15px;border-left-width:0;border-right-width:0;border-top-width:0;"><?php echo @$higher_results_view['lang_content_level_higher']['writing'][0];?></td>
    </tr>
        <tr class="<?php echo "tr_colour_".substr(@$higher_results_view['higher_certificate_data']['speaking']['level'],0,2);?>">
        <td style="padding:20px;border-style:solid;border-color:#ffffff;border-bottom-width:15px;border-left-width:0;border-right-width:0;border-top-width:0;">Speaking:</td>
            <td style="border-style:solid;border-color:#ffffff;border-bottom-width:15px;border-left-width:0;border-right-width:0;border-top-width:0;"><?php echo @$higher_results_view['lang_content_level_higher']['speaking'][0];?></td>
    </tr>
    <tr>
                    <td valign="top" colspan="2"  style="font-size:12px;font-weight:600;padding:0 0 10px;">
                             <?php 
                                $url = 'site/higher_certificate_pdf/';
                                if($higher_results_view['type'] == 'tds'){
                                    $link = site_url($url).'/'.$higher_results_view['candidate_id'].'/'.$higher_results_view['token'];
                                }else{
                                    $link = site_url($url).'/'.$higher_results_view['candidate_id'];
                                }
                            
                            ?>
                             <a href="<?php echo $link;?>" class="pdfdownload_higher" style="margin-top:10px;font: 14px Verdana, Geneva, sans-serif;color:#337ab7;">Download as PDF</a>
                    </td>

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

</div>