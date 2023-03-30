<?php 

include_once 'header-booking.php'; 
$this->session = session();
?>
<div class="bg-lightgrey">
    <div class="container">
        <div class="row">	
            <?php
                $tokentype = $this->session->get('organization_data');
                $check_token = $tokentype['type_of_token'];
                $next_level_id = $this->session->get('next_cats_product_id');
            ?>
            <div class="terms_condtion nav_dashboard nextstep">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#next_step">Your Next Step</a></li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">
                    <div id="next_step" class="tab-pane fade in active terms_tab">
                        <div class="row">
                            
                            <?php  if (($next_level_id > '9') && $check_token == 'cats_core') { ?>
                            
                                <div class="col-sm-12 col-s-12">	
                                    <p><?php echo lang('token_error_higher');?></p>
                                </div>
                            
                            <?php } elseif ((($next_level_id < '10') && $check_token == 'cats_higher')) { ?>
                                <div class="col-sm-12 col-s-12">	
                                    <p><?php echo lang('app.token_error_core'); ?></p>
                                </div>
                            
                            <?php } else { ?>
                            
                            <?php if((($next_level_id > 9) && (!in_array('3', $product_eligiblity_institute)))||(($next_level_id < 10) && (!in_array('2', $product_eligiblity_institute)))){?>
                                    <div class="col-md-8 col-sm-12 col-xs-12">
                                            <p><?php echo str_replace('##STEPCHECK_NAME##',$next_stepcheck_name, lang('app.language_cats_next_step_by_stepcheck'));?></p>
                                    </div>
                                    <div class="col-md-4 col-sm-12 col-xs-12 text_center mt10-xs">
                                            <a href="#" class="btn btn-main btn_cats" data-toggle="modal" disabled><?php echo "Book Level"; ?></a>
                                    </div>
                                    <div class="col-sm-12">
                                          <p><?php echo lang('token_error_eligiblity'); ?></p>
                                    </div>
                            <?php } else { ?>
                                    <div class="col-md-8 col-sm-12 col-xs-12">
                                            <p><?php echo str_replace('##STEPCHECK_NAME##',$next_stepcheck_name, lang('app.language_cats_next_step_by_stepcheck'));?></p>
                                    </div>
                                    <div class="col-md-4 col-sm-12 col-xs-12 text_center mt10-xs">
                                            <a href="#" class="btn btn-main btn_cats" data-toggle="modal" data-target="#terms_of_use_Modal" data-backdrop="static" data-keyboard="false"><?php echo "Book Level"; ?></a>
                                    </div>
                            <?php } ?>
                         
                          
                            <?php } ?>
                        </div>
                    </div>		
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Terms of us modal popup-->
    <div class="modal fade" id="terms_of_use_Modal"  role="dialog" >
        <div class="modal-dialog">
            <div class="modal-content bg-lightblue">
                <div class="modal-header ">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h5 class="modal-title" id="myModalLabel"><?php echo 'You are about to book CATs '.$next_stepcheck_name; ?></h5>
                </div>
                <?php echo form_open('site/book_next_cat', array('role' => 'form bv-form', 'id' => 'booknextcat', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>	
                <div class="modal-body">
					
                    <div class="form-group">					
                    <p>
                            <span><?php echo "I have read the";?></span>
                            <a href="<?php echo site_url('site/index/terms-conditions' ); ?>" target="_blank" id=""><?php echo lang('app.language_school_terms_of_use');?></a>
                            <span><?php echo  "and I agree to be bound by these ";?></span>
                            <input class="checkbox-inline" type="checkbox" name="agree_terms" value="agree_terms" style="margin:0;"> 
                    </p>
                    </div>
                    <input type="hidden" name="benchmark_placement" value="<?php echo $benchmark_placement; ?>">
                    <input type="hidden" name="benchmarkid" value="<?php echo $processed_data; ?>">
                       <?php            
                   /* foreach($processed_data['overall'] as $value)
                    {
                        echo '<input type="hidden" name="overall[]" value="'. $value. '">';
                    } */?>
                </div> 
				
                <div class="modal-footer text-center">
                    <input type="submit" id="order2_submit" name="order2_submit" class="btn-main btn_cats" value="<?php echo lang('app.language_school_label_continue'); ?>" />
                </div>
                <?php echo form_close(); ?> 
            </div>
        </div>
    </div>
<?php include 'footer-booking.php'; ?>



