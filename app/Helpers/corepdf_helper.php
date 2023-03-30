<?php
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 
function generatecoreResultsPDF($values_core_pdf = FALSE){
    if($values_core_pdf){
        ob_start();
        ?>
<!DOCTYPE html> 
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/style_updated_ui.css'); ?>">
    <title>CATs Step Core Results - Pdf</title>
</head>
<body>
    <table width="100%"  border="0" cellspacing="0" cellpadding="10" align="center">
        <tr>
            <td colspan="2">
                  <img alt="logo" src="<?php echo base_url('public/images/cats_logo_pdf.png'); ?>" style="height:50px;">
            </td>
        </tr>
        <tr>
			<td align="left" valign="top"> 
			<table width="100%"  border="0" cellspacing="0" cellpadding="0" align="center" style="border:1px solid #ddd;">
				<tr>
				<td>
				<?php
				 if ($values_core_pdf['result_display'] == 'logit') {
					 $level_label = array ('A11' => 'A1.1','A12' => 'A1.2','A13' => 'A1.3','A21' => 'A2.1','A22' => 'A2.2','A23' => 'A2.3','B11' => 'B1.1','B12' => 'B1.2','B13' => 'B1.3');
				 } elseif ($values_core_pdf['result_display'] == 'threshold') {
					 $level_label = array ('A11' => '&nbsp;&nbsp;&nbsp;A1.1&nbsp;&nbsp;</br> 0-50','A12' => '&nbsp;&nbsp;&nbsp;A1.2&nbsp;&nbsp;&nbsp;</br> 51-100','A13' => 'A1.3 </br>101-150','A21' => 'A2.1 </br>151-200','A22' => 'A2.2 </br>201-250','A23' => 'A2.3 </br>251-300','B11' => 'B1.1 </br>301-350','B12' => 'B1.2 </br>351-400','B13' => 'B1.3 </br>401-450');
				 }
				?>
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
				<p style="font-size:20px;font-weight:600;margin: 0;"><?php echo lang('app.language_pdf_certicate_statement');?></p>
			</td>
		</tr>
        <tr>
            <td align="left" valign="top" colspan="2">
                <table width="100%" border="0" cellspacing="0" cellpadding="0" style=" padding-top:2px;border: 1px solid #ddd;">
                    <tr>
                        <td align="left" valign="top" style="width:100%; font-size:18px; color:#444;font-family:Verdana, Geneva, sans-serif;padding-bottom:10px;">
                          <div style="height:95px; font-size:14px;">
                                <h4 style="margin:5px 0 15px 10px; font-size: 18px; font-weight:normal; padding:3px 10px 10px 0; font-weight:bold; font-family: Verdana, Geneva, sans-serif; "><?php echo lang('app.language_formal_test_result_performance_overall'); ?></h4>
                                <p style="margin:0 0 5px;"></p>
                                <p style="margin:0 0 5px 10px;"><span style="width:90px;display:inline-block;"><strong>Score:</span> </strong><?php echo @$values_core_pdf['score']; ?></p>
                                <p style="margin:0 0 3px 10px;"><span style="width:90px;display:inline-block;"><strong>CEFR Level:</span> </strong><?php echo @$values_core_pdf['cefr_level']; ?></p>
                          </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <!--POPUP AND ARROW BEGINS-->
                            <div id="A11" <?php if($values_core_pdf['cefr_level'] == 'A1.1'){ echo 'style="height:85px;display: block;"'; } else { echo 'style="height:85px;display: none;"';} ?>>
                                <table width="100%" border="0" cellspacing="0" cellpadding="0" >
                                    <tr>
                                        <td align="left" valign="top" style="width:100%; font-size:10px; color:#444; font-family:Verdana, Geneva, sans-serif;">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td align="left" valign="top" style="width:12px;">&nbsp;</td>
                                                    <td align="left" valign="top" style="width:330px; border: 1px solid #ddd;font-size:10px; padding:10px;">
                                                      <p style="margin:0 0 10px;"><?php echo lang('app.language_core_certificate_level_headings'); ?></p>
                                                      <ul style="padding:0 20px 0; margin:0;">
                                                            <li><?php echo lang('app.language_core_certificate_level_a1_line1');?></li>
                                                            <li><?php echo lang('app.language_core_certificate_level_a1_line2');?></li>
                                                            <li><?php echo lang('app.language_core_certificate_level_a1_line3');?></li>
                                                            <li><?php echo lang('app.language_core_certificate_level_a1_line4');?></li>
                                                            <li><?php echo lang('app.language_core_certificate_level_a1_line5');?></li>
                                                      </ul>
                                                    </td>
                                                    <td align="left" valign="top">&nbsp;</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left" valign="top">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 10px;">
                                                <tr>
                                                      <td align="left" valign="top" style="width:10%;"></td>
                                                      <td align="left" valign="top">
                                                            <a href="#"><img alt="arrow" src="<?php echo base_url('public/images/arrow.png'); ?>"></a>
                                                      </td>
                                                </tr>               
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div id="A12" <?php if($values_core_pdf['cefr_level'] == 'A1.2'){ echo 'style="height:85px;display: block;"'; } else { echo 'style="height:85px;display: none;"';} ?>>
                                <table width="100%" border="0" cellspacing="0" cellpadding="0" align="left">
                                    <tr>
                                        <td align="left" valign="top" style="width:100%; font-size:10px; color:#444; font-family:Verdana, Geneva, sans-serif;">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                              <tr>
                                                    <td align="left" valign="top" style="width:12px;">&nbsp;</td>
                                                    <td align="left" valign="top" style="width:330px; font-size:10px; padding:10px; border: 1px solid #ddd;">
                                                        <p style="margin:0 0 10px;">You are here and you can:</p>
                                                        <ul style="padding:0 20px 0; margin:0;">
                                                            <li><?php echo lang('app.language_core_certificate_level_a1_line1');?></li>
                                                            <li><?php echo lang('app.language_core_certificate_level_a1_line2');?></li>
                                                            <li><?php echo lang('app.language_core_certificate_level_a1_line3');?></li>
                                                            <li><?php echo lang('app.language_core_certificate_level_a1_line4');?></li>
                                                            <li><?php echo lang('app.language_core_certificate_level_a1_line5');?></li>
                                                        </ul>
                                                    </td>
                                                    <td align="left" valign="top">&nbsp;</td>
                                              </tr>
                                            </table>
                                        </td>
                                    </tr>
                                      <tr>
                                        <td align="left" valign="top">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 10px;">
                                                <tr>
                                                    <td align="left" valign="top" style="width:20%;"></td>
                                                    <td align="left" valign="top">
                                                        <a href="#"><img alt="arrow" src="<?php echo base_url('public/images/arrow.png'); ?>"></a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                      </tr>
                                    </table>
                                    </div>
                                    <div id="A13" <?php if($values_core_pdf['cefr_level'] == 'A1.3'){ echo 'style="height:85px;display: block;"'; } else { echo 'style="height:85px;display: none;"';} ?>>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="left"  >
                                      <tr>
                                            <td align="left" valign="top" style="width:100%; font-size:10px; color:#444; font-family:Verdana, Geneva, sans-serif;">
                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                      <tr>
                                                            <td align="left" valign="top" style="width:12px;">&nbsp;</td>
                                                                    <td align="left" valign="top" style="width:330px; border: 1px solid #ddd;font-size:10px; padding:10px;">
                                                                            <p style="margin:0 0 10px;">You are here and you can:</p>
                                                                            <ul style="padding:0 20px 0; margin:0;">
                                                                                <li><?php echo lang('app.language_core_certificate_level_a1_line1');?></li>
                                                                                <li><?php echo lang('app.language_core_certificate_level_a1_line2');?></li>
                                                                                <li><?php echo lang('app.language_core_certificate_level_a1_line3');?></li>
                                                                                <li><?php echo lang('app.language_core_certificate_level_a1_line4');?></li>
                                                                                <li><?php echo lang('app.language_core_certificate_level_a1_line5');?></li>
                                                                            </ul>
                                                                    </td>
                                                            <td align="left" valign="top">&nbsp;</td>
                                                      </tr>
                                                    </table>
                                            </td>
                                      </tr>
                                      <tr>
                                            <td align="left" valign="top">
                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 10px;">
                                                      <tr>
                                                            <td align="left" valign="top" style="width:30%;"></td>
                                                            <td align="left" valign="top">
                                                                    <a href="#"><img alt="arrow" src="<?php echo base_url('public/images/arrow.png'); ?>"></a>
                                                            </td>
                                                      </tr>
                                                    </table>
                                            </td>
                                      </tr>
                                    </table>
                                    </div>
                                    <div id="A21" <?php if($values_core_pdf['cefr_level'] == 'A2.1'){ echo 'style="height:85px;display: block;"'; } else { echo 'style="height:85px;display: none;"';} ?>>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="left"  >
                                      <tr>
                                            <td align="left" valign="top" style="width:100%; font-size:10px; color:#444; font-family:Verdana, Geneva, sans-serif;">
                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                      <tr>
                                                            <td align="left" valign="top" style="width:150px;">&nbsp;</td>
                                                                    <td align="left" valign="top" style="width:400px; border: 1px solid #ddd;font-size:10px; padding:10px;">
                                                                            <p style="margin:0 0 10px;">You are here and you can:</p>
                                                                            <ul style="padding:0 20px 0; margin:0;">
                                                                                <li><?php echo lang('app.language_core_certificate_level_a2_line1');?></li>
                                                                                <li><?php echo lang('app.language_core_certificate_level_a2_line2');?></li>
                                                                                <li><?php echo lang('app.language_core_certificate_level_a2_line3');?></li>
                                                                                <li><?php echo lang('app.language_core_certificate_level_a2_line4');?></li>
                                                                                <li><?php echo lang('app.language_core_certificate_level_a2_line5');?></li>
                                                                            </ul>
                                                                    </td>
                                                            <td align="left" valign="top">&nbsp;</td>
                                                      </tr>
                                                    </table>
                                            </td>
                                      </tr>
                                      <tr>
                                            <td align="left" valign="top">
                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 10px;">
                                                      <tr>
                                                            <td align="left" valign="top" style="width:40%;"></td>
                                                            <td align="left" valign="top">
                                                                    <a href="#"><img alt="arrow" src="<?php echo base_url('public/images/arrow.png'); ?>"></a>
                                                            </td>
                                                      </tr>
                                                    </table>
                                            </td>
                                      </tr>
                                    </table>
                                    </div>
                                    <div id="A22" <?php if($values_core_pdf['cefr_level'] == 'A2.2'){ echo 'style="height:85px;display: block;"'; } else { echo 'style="height:85px;display: none;"';} ?>>
                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="left"  >
                                      <tr>
                                            <td align="right" valign="top" style="width:100%; font-size:10px; color:#444; font-family:Verdana, Geneva, sans-serif;">
                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                      <tr>
                                                            <td align="left" valign="top" style="width:150px;"></td>
                                                                    <td align="left" valign="top" style="width:400px; border: 1px solid #ddd;font-size:10px; padding:10px;">
                                                                            <p style="margin:0 0 10px;">You are here and you can:</p>
                                                                            <ul style="padding:0 20px 0; margin:0;">
                                                                                <li><?php echo lang('app.language_core_certificate_level_a2_line1');?></li>
                                                                                <li><?php echo lang('app.language_core_certificate_level_a2_line2');?></li>
                                                                                <li><?php echo lang('app.language_core_certificate_level_a2_line3');?></li>
                                                                                <li><?php echo lang('app.language_core_certificate_level_a2_line4');?></li>
                                                                                <li><?php echo lang('app.language_core_certificate_level_a2_line5');?></li>
                                                                            </ul>
                                                                    </td>
                                                            <td align="left" valign="top">&nbsp;</td>
                                                      </tr>
                                                    </table>
                                            </td>
                                      </tr>
                                      <tr>
                                            <td align="left" valign="top">
                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 10px;">
                                                      <tr>
                                                            <td align="left" valign="top" style="width:50%;"></td>
                                                            <td align="left" valign="top">
                                                                    <a href="#"><img alt="arrow" src="<?php echo base_url('public/images/arrow.png'); ?>"></a>
                                                            </td>
                                                      </tr>
                                                    </table>
                                            </td>
                                      </tr>
                                    </table>
                                    </div>
                                    <div id="A23" <?php if($values_core_pdf['cefr_level'] == 'A2.3'){ echo 'style="height:85px;display: block;"'; } else { echo 'style="height:85px;display: none;"';} ?>>
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0" align="left"  >
                                            <tr>
                                                <td align="left" valign="top" style="width:100%; font-size:10px; color:#444; font-family:Verdana, Geneva, sans-serif;">
                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                      <tr>
                                                        <td align="left" valign="top" style="width:150px;">&nbsp;</td>
                                                            <td align="left" valign="top" style="width:400px; border: 1px solid #ddd;font-size:10px; padding:10px;">
                                                                <p style="margin:0 0 10px;">You are here and you can:</p>
                                                                <ul style="padding:0 20px 0; margin:0;">
                                                                    <li><?php echo lang('app.language_core_certificate_level_a2_line1');?></li>
                                                                    <li><?php echo lang('app.language_core_certificate_level_a2_line2');?></li>
                                                                    <li><?php echo lang('app.language_core_certificate_level_a2_line3');?></li>
                                                                    <li><?php echo lang('app.language_core_certificate_level_a2_line4');?></li>
                                                                    <li><?php echo lang('app.language_core_certificate_level_a2_line5');?></li>
                                                                </ul>
                                                            </td>
                                                        <td align="left" valign="top">&nbsp;</td>
                                                      </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="left" valign="top">
                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 10px;">
                                                      <tr>
                                                        <td align="left" valign="top" style="width:60%;"></td>
                                                        <td align="left" valign="top">
                                                                <a href="#"><img alt="arrow" src="<?php echo base_url('public/images/arrow.png'); ?>"></a>
                                                        </td>
                                                      </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div id="B11" <?php if($values_core_pdf['cefr_level'] == 'B1.1'){ echo 'style="height:85px;display: block;"'; } else { echo 'style="height:85px;display: none;"';} ?>>
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0" align="left"  >
                                            <tr>
                                                <td align="left" valign="top" style="width:100%; font-size:10px; color:#444; font-family:Verdana, Geneva, sans-serif;">
                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                        <tr>
                                                            <td align="left" valign="top" style="width:280px;">&nbsp;</td>
                                                                <td align="left" valign="top" style="width:330px; border: 1px solid #ddd;font-size:10px; padding:10px;">
                                                                    <p style="margin:0 0 10px;">You are here and you can:</p>
                                                                    <ul style="padding:0 20px 0; margin:0;">
                                                                        <li><?php echo lang('app.language_core_certificate_level_b1_line1');?></li>
                                                                        <li><?php echo lang('app.language_core_certificate_level_b1_line2');?></li>
                                                                        <li><?php echo lang('app.language_core_certificate_level_b1_line3');?></li>
                                                                        <li><?php echo lang('app.language_core_certificate_level_b1_line4');?></li>
                                                                        <li><?php echo lang('app.language_core_certificate_level_b1_line5');?></li>
                                                                    </ul>
                                                                </td>
                                                            <td align="left" valign="top">&nbsp;</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="left" valign="top">
                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 10px;">
                                                      <tr>
                                                        <td align="left" valign="top" style="width:70%;"></td>
                                                        <td align="left" valign="top">
                                                            <a href="#"><img alt="arrow" src="<?php echo base_url('public/images/arrow.png'); ?>"></a>
                                                        </td>
                                                      </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div id="B12" <?php if($values_core_pdf['cefr_level'] == 'B1.2'){ echo 'style="height:85px; display: block;"'; } else { echo 'style="height:85px; display: none;"';} ?>>
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0" align="left"  >
                                            <tr>
                                                <td align="left" valign="top" style="width:100%; font-size:10px; color:#444; font-family:Verdana, Geneva, sans-serif;">
                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                        <tr>
                                                            <td align="left" valign="top" style="width:280px;">&nbsp;</td>
                                                            <td align="left" valign="top" style="width:330px; border: 1px solid #ddd;font-size:10px; padding:10px;">
                                                                <p style="margin:0 0 10px;">You are here and you can:</p>
                                                                <ul style="padding:0 20px 0; margin:0;">
                                                                    <li><?php echo lang('app.language_core_certificate_level_b1_line1');?></li>
                                                                    <li><?php echo lang('app.language_core_certificate_level_b1_line2');?></li>
                                                                    <li><?php echo lang('app.language_core_certificate_level_b1_line3');?></li>
                                                                    <li><?php echo lang('app.language_core_certificate_level_b1_line4');?></li>
                                                                    <li><?php echo lang('app.language_core_certificate_level_b1_line5');?></li>
                                                                </ul>
                                                            </td>
                                                            <td align="left" valign="top">&nbsp;</td>
                                                          </tr>
                                                    </table>
                                                </td>
                                          </tr>
                                          <tr>
                                            <td align="left" valign="top">
                                                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 10px;">
                                                  <tr>
                                                    <td align="left" valign="top" style="width:80%;"></td>
                                                    <td align="left" valign="top">
                                                        <a href="#"><img alt="arrow" src="<?php echo base_url('public/images/arrow.png'); ?>"></a>
                                                    </td>
                                                  </tr>
                                                </table>
                                            </td>
                                          </tr>
                                        </table>
                                    </div>
                                    <div id="B13" <?php if($values_core_pdf['cefr_level'] == 'B1.3'){ echo 'style="height:85px;display: block;"'; } else { echo 'style="height:85px;display: none;"';} ?>>
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0" align="left"  >
                                            <tr>
                                                <td align="right" valign="top" style="width:100%; font-size:10px; color:#444; font-family:Verdana, Geneva, sans-serif;">
                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                      <tr>
                                                        <td align="left" valign="top" style="width:280px;">&nbsp;</td>
                                                            <td align="left" valign="top" style="width:330px; border: 1px solid #ddd;font-size:10px; padding:10px;">
                                                                <p style="margin:0 0 10px;">You are here and you can:</p>
                                                                <ul style="padding:0 20px 0; margin:0;">
                                                                    <li><?php echo lang('app.language_core_certificate_level_b1_line1');?></li>
                                                                    <li><?php echo lang('app.language_core_certificate_level_b1_line2');?></li>
                                                                    <li><?php echo lang('app.language_core_certificate_level_b1_line3');?></li>
                                                                    <li><?php echo lang('app.language_core_certificate_level_b1_line4');?></li>
                                                                    <li><?php echo lang('app.language_core_certificate_level_b1_line5');?></li>
                                                                </ul>
                                                            </td>
                                                            <td align="left" valign="top">&nbsp;</td>
                                                      </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="left" valign="top">
                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 10px;">
                                                      <tr>
                                                        <td align="left" valign="top" style="width:90%;"></td>
                                                        <td align="left" valign="top">
                                                            <a href="#"><img alt="arrow" src="<?php echo base_url('public/images/arrow.png'); ?>"></a>
                                                        </td>
                                                      </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <!--POPUP AND ARROW ENDS-->
                        </td>
                    </tr>
                    <tr>
                        <td align="center" valign="top" style="font-family:Verdana, Geneva, sans-serif; padding-top:60px; padding-bottom:10px;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center" valign="middle" style="width:5%;">&nbsp;</td>
                                    <td align="center" valign="middle" style="width:10%; background-color: #d9851b; color:#fff; border-right: 1px dashed #fff; padding: 5px; text-align: center; font-size:14px;"><?php echo $level_label['A11']; ?></td>
                                    <td align="center" valign="middle" style="width:10%; background-color: #d9851b; color:#fff; border-right: 1px dashed #fff; padding: 5px; text-align: center; font-size:14px;"><?php echo $level_label['A12']; ?></td>
                                    <td align="center" valign="middle" style="width:10%; background-color: #d9851b; color:#fff; border-right: 1px dashed #fff; padding: 5px; text-align: center; font-size:14px;"><?php echo $level_label['A13']; ?></td>
                                    <td align="center" valign="middle" style="width:10%; background-color: #c6503f; color:#fff; border-right: 1px dashed #fff; padding: 5px; text-align: center; font-size:14px;"><?php echo $level_label['A21']; ?></td>
                                    <td align="center" valign="middle" style="width:10%; background-color: #c6503f; color:#fff; border-right: 1px dashed #fff; padding: 5px; text-align: center; font-size:14px;"><?php echo $level_label['A22']; ?></td>
                                    <td align="center" valign="middle" style="width:10%; background-color: #c6503f; color:#fff; border-right: 1px dashed #fff; padding: 5px; text-align: center; font-size:14px;"><?php echo $level_label['A23']; ?></td>
                                    <td align="center" valign="middle" style="width:10%; background-color: #811612; color:#fff; border-right: 1px dashed #fff; padding: 5px; text-align: center; font-size:14px;"><?php echo $level_label['B11']; ?></td>
                                    <td align="center" valign="middle" style="width:10%; background-color: #811612; color:#fff; border-right: 1px dashed #fff; padding: 5px; text-align: center; font-size:14px;"><?php echo $level_label['B12']; ?></td>
                                    <td align="center" valign="middle" style="width:10%; background-color: #811612; color:#fff; padding: 5px; text-align: center; font-size:14px;"><?php echo $level_label['B13']; ?></td>
                                    <td align="center" valign="middle" style="width:5%;">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
               </table>
            </td>
        </tr>       
        <tr>
            <!-- graph --> 
            <td align="left" valign="top" colspan="2"  style="width:100%; font-size:18px; color:#444; font-family:Verdana, Geneva, sans-serif;">
                <table width="100%" style="border: 1px solid #ddd; style="width: 100%;">
                    <tr>
                        <td>
                            <h4 style="margin:5px 0 15px 10px; font-size: 18px; font-weight:bold; font-family: Verdana, Geneva, sans-serif; background-color:#fff; ">Your performance in each part</h4>
                            <div style="margin:0 auto; width: 100%;">
                            <?php 
                            $_url = explode("/", $values_core_pdf['chart_name']);
                            if(in_array("results", $_url) && !empty($_url))
                            {
                                $folder_name = "results";
                            }
                            $file_name = end($_url);
                            $parts = explode('.',$file_name);

                            // print_r($parts);
                            // exit;

                            ?>
	                            <img width="625" alt="Chart" src="<?php echo @image_png($parts[0],$parts[1],'charts',$folder_name); ?>">
                            </div>
                              <p style="margin-bottom: 2px;font-weight: 400;color:#000;font-size: 12px;text-align: center;"><?php echo lang('app.language_pdf_certicate_find_out');?></p>
                              <?php echo lang("app.language_cats_step_website_link_core_pdf");?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
<?php

        // print '<pre>'; print_r($level_label); print '</pre>'; die;
        $pdf_content = ob_get_clean();
        $options = new Options();
        $options->set('isRemoteEnabled', TRUE);
        // $options->set('isHtml5ParserEnabled', TRUE);
        $dompdf = new Dompdf($options);
         //echo $pdf_content; die;
        
        $dompdf->loadHtml($pdf_content);
        $dompdf->set_option('isRemoteEnabled', TRUE);
       // $dompdf->set_option('isHtml5ParserEnabled', TRUE);  

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->getOptions()->setIsFontSubsettingEnabled(true);

        // Render the HTML as PDF
        $dompdf->render();

        $name = "Core result on - " . date("d-m-Y");
        $dompdf->stream($name);
        exit();

    }
}
?>