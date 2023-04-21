<?php $current_level = $results['cefr_level'];?>
<!DOCTYPE html> 
<html> 
<head>
    <title>CATs Step Core Results</title>
    <link rel="stylesheet" href="<?php echo base_url('public/css/bootstrap.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('public/css/style_updated_ui.css'); ?>">
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
                <div class="section-border">
                    <div class="mb20">
                        <h3 style="font-family: 'kreonregular'; font-weight: 500;"><?php echo @$results['course_name']; ?></h3>
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
            <div class="row chart-body mb20 statement-performance">
                <ul class="list-unstyled performance_result <?php echo ($results['cefr_level'] == "You have not achieved the pass level for the exam and we are unable to award you a result.")? "long": "";?>">
                    <li><span>Overall Performance</span></li>
                    <li><span <?php if($results['result_status'] != "Pass"){ echo "class=achived" ;} ?>>Result</span> <div class="inner-values"><?php echo $results['result_status'];?></div></li>
                    <li><span <?php if($results['result_status'] != "Pass"){ echo "class=achived" ;} ?>>Score</span> <div class="inner-values"><?php echo $results['result_score'];?></div></li>
                    <li><span <?php if($results['result_status'] != "Pass"){ echo "class=achived" ;} ?>>CEFR Level :</span> <div class="inner-values"><?php echo $results['cefr_level'];?></div></li>
                </ul>
             <?php if($results['cefr_level_content'] != ""){ ?>
                <ul class="list-styled">
                    <li><span class="learner">This learner has reached the level of language ability to:</span></li>
                    <li><?php echo $results['cefr_level_content'][0];?></li>
                    <li><?php echo $results['cefr_level_content'][1];?></li>
                    <li><?php echo $results['cefr_level_content'][2];?></li>
                    <li><?php echo $results['cefr_level_content'][3];?></li>
                </ul>
             <?php } ?>
                <div class="clearfix"> </div>
            </div>  
        </div>
        <div class="section-border mb20 statement-performance">
            <p style="font-size: 16px; font-weight: bold;">Performance by Skill</p>
            <span style="font-size: 14px;"><em>Performance in Listening and Reading counts towards the overall result.</em></span>
            <p style="font-size: 14px;"><em>
            <?php 
            if(!empty($results['speaking_content']) && !empty($results['writing_content'])){
                echo lang('app.language_core_certificate_Perfomance_sp_wr');
            }elseif(!empty($results['speaking_content'])){
                echo lang('app.language_core_certificate_Perfomance_sp');
            }elseif(!empty($results['writing_content'])){
                echo lang('app.language_core_certificate_Perfomance_wr');
            }
            ?>
                </em></p>
            <div class="performance-by-skill" style="margin-top: 16px;">
                <div class="listing_score row">
                    <div class="col-sm-6">
                        <p style="font-size: 16px; margin-bottom: 20px;"><span>Listening Score: </span><?php echo $results['listening_score']; ?></p>
                    </div>
                    <div class="col-sm-6">
                        <p style="font-size: 16px; margin-bottom: 20px;padding-left: 7px;"><span>Reading Score:</span> <?php echo $results['reading_score']; ?></p>
                    </div>
                </div>
                <div class="listing_score row">
                    <div class="col-sm-6">
                        <?php if (!empty($results['speaking_content'])) { ?>
                            <ul class="list-unstyled">
                                <li><span>Speaking</span></li>
                                <li><span style="font-size: 12px;"><?php echo $results['sp_wr_types']['speaking']; ?>:</span></li>
                                <li><?php echo $results['speaking_content']; ?></li>
                            </ul>
                        <?php } ?>
                    </div>
                    <div class="col-sm-6">
                        <?php if (!empty($results['writing_content'])) { ?>
                            <ul class="list-unstyled">
                                <li><span>Writing</span></li>
                                <li><span style="font-size: 12px;"><?php echo $results['sp_wr_types']['writing']; ?>:</span></li>
                                <li><?php echo $results['writing_content']; ?></li>
                            </ul>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>  
        <?php if($results['result_status'] == "Pass"){ 
                $level_array = array('A1.1','A1.2','A1.3','A2.1','A2.2','A2.3','B1.1','B1.2','B1.3');
                $p = array_search($current_level, $level_array);
        ?>
        <div class="section-border mb20">
            <div class="row">
                <div class="col-md-4 first_gird">
                    <div class="<?php echo get_grid_class_view($p, 'A1.1'); ?> first_non_popup">
                        <h5>Step Forward 1 (A1.1)</h5>
                        <ul class="list-styled">
                            <li><?php echo $results['cefr_all_content']['A1.1'][0];?></li>
                            <li><?php echo $results['cefr_all_content']['A1.1'][1];?></li>
                            <li><?php echo $results['cefr_all_content']['A1.1'][2];?></li>
                            <li><?php echo $results['cefr_all_content']['A1.1'][3];?></li>
                        </ul>
                    </div>    
                </div>
                <div class="col-md-4 first_gird">
                    <div class="<?php echo get_grid_class_view($p, 'A1.2'); ?> first_non_popup">
                        <h5>Step Forward 2 (A1.2)</h5>
                        <ul class="list-styled">
                            <li><?php echo $results['cefr_all_content']['A1.2'][0];?></li>
                            <li><?php echo $results['cefr_all_content']['A1.2'][1];?></li>
                            <li><?php echo $results['cefr_all_content']['A1.2'][2];?></li>
                            <li><?php echo $results['cefr_all_content']['A1.2'][3];?></li>
                        </ul>
                    </div>    
                </div>
                <div class="col-md-4 first_gird">
                    <div class="<?php echo get_grid_class_view($p, 'A1.3'); ?> first_non_popup">
                        <h5>Step Forward 3 (A1.3)</h5>
                        <ul class="list-styled">
                            <li><?php echo $results['cefr_all_content']['A1.3'][0];?></li>
                            <li><?php echo $results['cefr_all_content']['A1.3'][1];?></li>
                            <li><?php echo $results['cefr_all_content']['A1.3'][2];?></li>
                            <li><?php echo $results['cefr_all_content']['A1.3'][3];?></li>
                        </ul>
                    </div>    
                </div>
                <div class="col-md-4 second_gird">
                    <div class="<?php echo get_grid_class_view($p, 'A2.1'); ?> second_non_popup">
                        <h5>Step Up 1 (A2.1)</h5>
                        <ul class="list-styled">
                            <li><?php echo $results['cefr_all_content']['A2.1'][0];?></li>
                            <li><?php echo $results['cefr_all_content']['A2.1'][1];?></li>
                            <li><?php echo $results['cefr_all_content']['A2.1'][2];?></li>
                            <li><?php echo $results['cefr_all_content']['A2.1'][3];?></li>
                        </ul>
                    </div>    
                </div>
                <div class="col-md-4 second_gird">
                    <div class="<?php echo get_grid_class_view($p, 'A2.2'); ?> second_non_popup">
                        <h5>Step Up 2 (A2.2)</h5>
                        <ul class="list-styled">
                            <li><?php echo $results['cefr_all_content']['A2.2'][0];?></li>
                            <li><?php echo $results['cefr_all_content']['A2.2'][1];?></li>
                            <li><?php echo $results['cefr_all_content']['A2.2'][2];?></li>
                            <li><?php echo $results['cefr_all_content']['A2.2'][3];?></li>
                        </ul>
                    </div>    
                </div>
                <div class="col-md-4 second_gird">
                    <div class="<?php echo get_grid_class_view($p, 'A2.3'); ?> second_non_popup">
                        <h5>Step Up 3 (A2.3)</h5>
                        <ul class="list-styled">
                            <li><?php echo $results['cefr_all_content']['A2.3'][0];?></li>
                            <li><?php echo $results['cefr_all_content']['A2.3'][1];?></li>
                            <li><?php echo $results['cefr_all_content']['A2.3'][2];?></li>
                            <li><?php echo $results['cefr_all_content']['A2.3'][3];?></li>
                        </ul>
                    </div>    
                </div>
                <div class="col-md-4 third_grid">
                    <div class="<?php echo get_grid_class_view($p, 'B1.1'); ?> third_non_popup">
                        <h5>Step Ahead 1 (B1.1)</h5>
                        <ul class="list-styled">
                            <li><?php echo $results['cefr_all_content']['B1.1'][0];?></li>
                            <li><?php echo $results['cefr_all_content']['B1.1'][1];?></li>
                            <li><?php echo $results['cefr_all_content']['B1.1'][2];?></li>
                            <li><?php echo $results['cefr_all_content']['B1.1'][3];?></li>
                        </ul>
                    </div>    
                </div>
                <div class="col-md-4 third_grid">
                    <div class="<?php echo get_grid_class_view($p, 'B1.2'); ?> third_non_popup">
                        <h5>Step Ahead 2 (B1.2)</h5>
                        <ul class="list-styled">
                            <li><?php echo $results['cefr_all_content']['B1.2'][0];?></li>
                            <li><?php echo $results['cefr_all_content']['B1.2'][1];?></li>
                            <li><?php echo $results['cefr_all_content']['B1.2'][2];?></li>
                            <li><?php echo $results['cefr_all_content']['B1.2'][3];?></li>
                        </ul>
                    </div>    
                </div>
                <div class="col-md-4 third_grid">
                    <div class="<?php echo get_grid_class_view($p, 'B1.3'); ?> third_non_popup">
                        <h5>Step Ahead 3 (B1.3)</h5>
                        <ul class="list-styled">
                            <li><?php echo $results['cefr_all_content']['B1.3'][0];?></li>
                            <li><?php echo $results['cefr_all_content']['B1.3'][1];?></li>
                            <li><?php echo $results['cefr_all_content']['B1.3'][2];?></li>
                            <li><?php echo $results['cefr_all_content']['B1.3'][3];?></li>
                        </ul>
                    </div>    
                </div>
            </div>
        </div>
        <?php } ?>
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
    
</div>    
</body>
</html>
<?php 
    function get_grid_class_view($current_position, $level){
        $level_array = array('A1.1','A1.2','A1.3','A2.1','A2.2','A2.3','B1.1','B1.2','B1.3');
        $p = array_search($level, $level_array);
        if($current_position > $p){
            $class = "completed";
        }elseif ($current_position == $p){
            $class = "current";
        }else{
            $class = "grayout";
        }
        return $class;
    }
?>