<!DOCTYPE html> 
<html> 
<head>
    <title>CATs Step Core Results</title>
    <link rel="stylesheet" href="<?php echo base_url('public/css/bootstrap.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/style_updated_ui.css'); ?>">
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url('public/js/bootstrap.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('public/js/Chart.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>/public/js/Chart.min.js"></script>
    <style>
        body *{
            font-family: Verdana, Geneva, sans-serif;
        }
        .section-border{
            border:1px solid #ddd;
            padding:20px;
        }
    </style>
</head> 
<body style="font-size: 14px; font-family: Verdana, Geneva, sans-serif;padding: 5px 30px;width: 1024px;margin: 0 auto;"> 
<div class="container-fluid">
    <div class="box-border">
        <div class="row chart-body mb20">
            <div class="col-sm-9">
                <div class="pdf-logo mb20">
                    <img src="<?php echo base_url('public/images/logo_new.svg'); ?>" alt="cats-logo" class="img-responsive">
                </div>
                <?php
                if ($results['result_display'] == 'logit') {
                    $level_label = array ('A11' => 'A1.1','A12' => 'A1.2','A13' => 'A1.3','A21' => 'A2.1','A22' => 'A2.2','A23' => 'A2.3','B11' => 'B1.1','B12' => 'B1.2','B13' => 'B1.3');
                } elseif ($results['result_display'] == 'threshold') {
                    $level_label = array ('A11' => 'A1.1</br>0-50','A12' => 'A1.2</br>51-100','A13' => 'A1.3</br>101-150','A21' => 'A2.1</br>151-200','A22' => 'A2.2</br>201-250','A23' => 'A2.3</br>251-300','B11' => 'B1.1</br>301-350','B12' => 'B1.2</br>351-400','B13' => 'B1.3</br>401-450');
                }
                ?>
                <div class="section-border">
                    <div class="mb20">
                        <h3 style="font-family: 'kreonregular';font-weight: 500;"><?php echo @$results['course_name']; ?></h3>
                        <h4 style="font-family: 'kreonregular';font-weight: 500;"><?php echo ucfirst(@$results['candidate_name']); ?></h4>
                    </div>
                    <div class="course-info">
                        <div class="row chart-body">
                            <label class="col-sm-4  col-xs-6 control-label label-spacing1">
                                <strong>Date of exam</strong>:
                            </label>
                            <div class="col-sm-8  col-xs-6 ">
                                <?php echo@$results['exam_date']; ?>
                            </div>
                        </div>
                        <div class="row chart-body">
                            <label class="col-sm-4 col-xs-6 control-label label-spacing1">
                                <strong>Invigilation status</strong>:
                            </label>
                            <div class="col-sm-8  col-xs-6">
                                <?php echo $results['is_supervised']; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-3 QR-content">
                <p class="t-indext" style="text-indent: 18px;font-size: 19px;margin-bottom: 0px; white-space: nowrap;">ID:  <?php echo @$results['thirdparty_id']; ?></p>
            </div>
        </div> 
        <div class="row">
            <div class="col-sm-12 text-center">
                <p class="mb20" style="font-size:20px;font-weight:600;"><?php echo lang('app.language_pdf_certicate_statement');?></p>
            </div>
        </div>
        <div class="section-border mb20">
            <div class="row chart-body mb20">
                <div class="col-sm-12">
                    <h4 class="couse-title-text" style="border-bottom:0"><?php echo lang('app.language_formal_test_result_performance_overall'); ?></h4>
                    <div class="course-info mb20">
                        <div class="row chart-body">
                            <label class="col-sm-2  col-xs-6 control-label label-spacing2">
                                <strong>Score</strong>:
                            </label>
                            <div class="col-sm-10  col-xs-6 ">
                                <?php echo @$results['score']; ?>
                            </div>
                        </div>
                        <div class="row chart-body">
                            <label class="col-sm-2  col-xs-6 control-label label-spacing2">
                                <strong>CEFR Level</strong>:
                            </label>
                            <div class="col-sm-10  col-xs-6 ">
                               <?php echo @$results['cefr_level']; ?>
                            </div>
                        </div>
                    </div>
                    <div class="timeline">
                        <span class="yel"><?php echo $level_label['A11']; ?>
                            <div id="A11" <?php if($results['cefr_level'] == 'A1.1'){ echo 'style="display: block;"'; } else { echo 'style="display: none;"';} ?>>
                                <!--POPUP CONTENT BEGINS-->
                                <span class="line">
                                    <div class="popUp">
                                        <p><?php echo lang('app.language_core_certificate_level_headings'); ?></p>
                                        <ul>
                                            <li><?php echo lang('app.language_core_certificate_level_a1_line1');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_a1_line2');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_a1_line3');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_a1_line4');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_a1_line5');?></li>
                                        </ul>
                                    </div>
                                </span> 
                                <!--POPUP CONTENT ENDS-->
                            </div>
                        </span>
                        <span class="yel"><?php echo $level_label['A12']; ?>
                            <div id="A12" <?php if($results['cefr_level'] == 'A1.2'){ echo 'style="display: block;"'; } else { echo 'style="display: none;"';} ?> >
                                <!--POPUP CONTENT BEGINS-->
                                <span class="line">
                                    <div class="popUp">
                                        <p><?php echo lang('app.language_core_certificate_level_headings'); ?></p>
                                        <ul>
                                            <li><?php echo lang('app.language_core_certificate_level_a1_line1');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_a1_line2');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_a1_line3');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_a1_line4');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_a1_line5');?></li>
                                        </ul>
                                    </div>
                                </span> 
                                <!--POPUP CONTENT ENDS-->
                            </div>
                        </span> 
                        <span class="yel"><?php echo $level_label['A13']; ?>
                         <div id="A13" <?php if($results['cefr_level'] == 'A1.3'){ echo 'style="display: block;"'; } else { echo 'style="display: none;"';} ?> >
                                <!--POPUP CONTENT BEGINS-->
                                <span class="line">
                                    <div class="popUp">
                                        <p><?php echo lang('app.language_core_certificate_level_headings'); ?></p>
                                        <ul>
                                            <li><?php echo lang('app.language_core_certificate_level_a1_line1');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_a1_line2');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_a1_line3');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_a1_line4');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_a1_line5');?></li>
                                        </ul>
                                    </div>
                                </span> 
                                <!--POPUP CONTENT ENDS-->
                            </div>
                        </span>
                        <span class="orange"><?php echo $level_label['A21']; ?>
                         <div id="A21" <?php if($results['cefr_level'] == 'A2.1'){ echo 'style="display: block;"'; } else { echo 'style="display: none;"';} ?> >
                            <!--POPUP CONTENT BEGINS-->
                            <span class="line">
                                <div class="popUp">
                                    <p><?php echo lang('app.language_core_certificate_level_headings'); ?></p>
                                    <ul>
                                        <li><?php echo lang('app.language_core_certificate_level_a2_line1');?></li>
                                        <li><?php echo lang('app.language_core_certificate_level_a2_line2');?></li>
                                        <li><?php echo lang('app.language_core_certificate_level_a2_line3');?></li>
                                        <li><?php echo lang('app.language_core_certificate_level_a2_line4');?></li>
                                        <li><?php echo lang('app.language_core_certificate_level_a2_line5');?></li>
                                    </ul>
                                </div>
                            </span> 
                            <!--POPUP CONTENT ENDS-->
                        </div>
                        </span>
                        <span class="orange"><?php echo $level_label['A22']; ?>
                            <div id="A22" <?php if($results['cefr_level'] == 'A2.2'){ echo 'style="display: block;"'; } else { echo 'style="display: none;"';} ?> >
                                <!--POPUP CONTENT BEGINS-->
                                <span class="line">
                                    <div class="popUp">
                                        <p><?php echo lang('app.language_core_certificate_level_headings'); ?></p>
                                        <ul>
                                            <li><?php echo lang('app.language_core_certificate_level_a2_line1');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_a2_line2');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_a2_line3');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_a2_line4');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_a2_line5');?></li>
                                        </ul>
                                    </div>
                                </span> 
                                <!--POPUP CONTENT ENDS-->
                            </div>
                        </span>
                        <span class="orange"><?php echo $level_label['A23']; ?>
                            <div id="A23" <?php if($results['cefr_level'] == 'A2.3'){ echo 'style="display: block;"'; } else { echo 'style="display: none;"';} ?> >
                                <!--POPUP CONTENT BEGINS-->
                                <span class="line">
                                    <div class="popUp">
                                        <p><?php echo lang('app.language_core_certificate_level_headings'); ?></p>
                                        <ul>
                                            <li><?php echo lang('app.language_core_certificate_level_a2_line1');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_a2_line2');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_a2_line3');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_a2_line4');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_a2_line5');?></li>
                                        </ul>
                                    </div>
                                </span> 
                                <!--POPUP CONTENT ENDS-->
                            </div>
                        </span>
                        <span class="red"><?php echo $level_label['B11']; ?>
                            <div id="B11" <?php if($results['cefr_level'] == 'B1.1'){ echo 'style="display: block;"'; } else { echo 'style="display: none;"';} ?> >
                                <!--POPUP CONTENT BEGINS-->
                                <span class="line">
                                    <div class="popUp">
                                        <p><?php echo lang('app.language_core_certificate_level_headings'); ?></p>
                                        <ul>
                                            <li><?php echo lang('app.language_core_certificate_level_b1_line1');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_b1_line2');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_b1_line3');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_b1_line4');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_b1_line5');?></li>
                                        </ul>
                                    </div>
                                </span> 
                                <!--POPUP CONTENT ENDS-->
                            </div>
                        </span>
                        <span class="red"><?php echo $level_label['B12']; ?>
                            <div id="B12" <?php if($results['cefr_level'] == 'B1.2'){ echo 'style="display: block;"'; } else { echo 'style="display: none;"';} ?> >
                                <!--POPUP CONTENT BEGINS-->
                                <span class="line">
                                    <div class="popUp">
                                        <p><?php echo lang('app.language_core_certificate_level_headings'); ?></p>
                                        <ul>
                                            <li><?php echo lang('app.language_core_certificate_level_b1_line1');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_b1_line2');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_b1_line3');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_b1_line4');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_b1_line5');?></li>
                                        </ul>
                                    </div>
                                </span> 
                                <!--POPUP CONTENT ENDS-->
                            </div>
                        </span>
                        <span class="red"><?php echo $level_label['B13']; ?>
                            <div id="B13" <?php if($results['cefr_level'] == 'B1.3'){ echo 'style="display: block;"'; } else { echo 'style="display: none;"';} ?> >
                                <!--POPUP CONTENT BEGINS-->
                                <span class="line">
                                    <div class="popUp">
                                        <p><?php echo lang('app.language_core_certificate_level_headings'); ?></p>
                                        <ul>
                                            <li><?php echo lang('app.language_core_certificate_level_b1_line1');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_b1_line2');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_b1_line3');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_b1_line4');?></li>
                                            <li><?php echo lang('app.language_core_certificate_level_b1_line5');?></li>
                                        </ul>
                                    </div>
                                </span> 
                                <!--POPUP CONTENT ENDS-->
                            </div>
                        </span>
                    </div>
                </div>
            </div>  
        </div>
        <div class="section-border mb20">
            <div class="row chart-body">
                <div class="col-sm-12">
                    <h4 class="couse-title-text"><?php echo lang('app.language_formal_test_result_performance_eachpart'); ?></h4>
                    <?php echo @$results['bar_graph']; ?>
                </div>
            </div>
        </div>
        <?php 
            $url = 'teacher/core_certificate_pdf';
            if(!empty($results['candidate_id'])){ ?>
                <a href="<?php echo site_url($url) . "/" . $results['candidate_id']; ?>" class="pdfdownload">Download as PDF</a>
        <?php }?>
                <div class="row">
        <div class="col-sm-12 text-center">
            <p style="font-size:14px;margin-bottom: 4px;font-weight: 400;color:#000"><?php echo lang('app.language_pdf_certicate_find_out');?></p>
            <?php echo lang('app.language_cats_step_website_link');?>
        </div>
    </div>
</div>    
</body>
</html>
