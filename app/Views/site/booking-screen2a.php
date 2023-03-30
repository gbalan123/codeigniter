<?php 
include_once 'header-booking.php';
$this->encrypter = \Config\Services::encrypter();

?>
<style>
    .clickable{
        cursor: pointer;
    }
    form label span {
        color: #555555; 
    }
    form label  {
        color: #555555; 
    }
    .has-error .help-block, .has-error .control-label, .has-error .radio, .has-error .checkbox, .has-error .radio-inline, .has-error .checkbox-inline, .has-error.radio label, .has-error.checkbox label, .has-error.radio-inline label, .has-error.checkbox-inline label {
        color :red;
    }
    .playback_effect{
        //color: red; font-weight: bold; text-decoration: underline; border-style: ridge; border: 1px ridge red
        box-shadow: 0 0 30px 10px #0FF;
    }
    .next-btn{
        display:none;
    }

                                                  .answers_1630 {
                                                                  width: 60px;
                                                                  padding-left: 5px;
                                                                  border-radius: 5px;
                                                                  border: solid 1px #ccc;
                                                                  padding: 1px 1px;
                                                                  margin: 2px 0;
                                                                  color: white; 
                                                              }
                                                              .sys_qus_block p{
                                                                 display: inline-block;
                                                              }
                                                      
</style>
<div class="bg-lightgrey">
    <div class="container">
    <div class="primary_dashboard">
			<div class="get_started">
				<div class="row">
					<div class="col-sm-12">
						 <?php include "booking-tabs.php"; ?>
						<div class="main_tab_content">
							<div class="tab-content">							
								
								<div id="learning_tab" class="tab-pane start_learning fade in active">
									<div class="nav_dashboard">
										<ul class="nav nav-tabs">
											<li class="active"><a data-toggle="tab" href="#Preparation_tab"><?php echo lang('app.language_site_booking_screen2a_primary_placment_title'); ?></a></li>
											
										</ul>
										<div class="start_learning_content institution_content">
											<div class="tab-content">
												<div id="Preparation_tab" class="tab-pane preparation_practice fade in active">
													<div class="row">
														<div class="col-sm-12">
														 <p><?php echo lang('app.language_site_booking_screen2a_primary_placment_description'); ?></p>
														</div>
														
														<div class="col-sm-12 text-center mt30">
								                            <a href="#"  tabindex="-1" data-backdrop="static" data-keyboard="false" class="btn btn-sm btn-continue btn-continue-child rubricrotate contact-btn disabled"  data-toggle="modal"   data-target="#linearId" id="continue_btn"  style="pointer-events:none;background: #6fa3b0;">Loading...</a>
								                        </div>
													</div>
												</div>
												
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
         <div style="margin-top:40px"></div>      
    </div>
</div>
	
<div class="modal fade" id="linearId" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog linear_test modal-lg" style="letter-spacing: 0.5px;">
        <div class="modal-content">
            <div class="modal-header modal_head_bg">
                <button type="button" class="close md_close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                <h3 class="modal-title rubric text-center"><?php echo lang('app.language_linear_headphone_title'); ?></h3>
            </div>
            <div class="modal-body md_bg">
                <div class="container-fluid" id="catsQuizEngine" >
                    <section id="title" data-bind="visible: !quizStarted()">
                        <div class="row mt100">
                            <div class="headphone-section text-center col-sm-12 quiz-start-headings">

                                <h2><?php echo lang('app.language_linear_headphone_put_on_title'); ?></h2>
                                <img src="<?php echo base_url('public/images/headphone.png'); ?>" alt="headphone">
                                <p>
                                    <?php echo lang('app.language_linear_headphone_put_on_proceed_title'); ?>
                                </p>
                                <div class="text-right">
                                    <button type="button" class="btn btn-sm btn-continue btn-continue-child" onclick="go_to_sound_test()"><?php echo lang('app.language_linear_continue_btn'); ?>&nbsp;<i class="fa fa-arrow-right" aria-hidden="true"></i></button>
                                </div>  
                            </div>
                            <div class="sound-test-section quiz-start-headings" style="display:none;">
                                <div class="col-xs-10">
                                    <h2><?php echo lang('app.language_linear_headphone_step1'); ?></h2>
                                    <p><?php echo lang('app.language_linear_headphone_please_click_before'); ?> <img src="<?php echo base_url('public/images/play-small.png'); ?>" /> <?php echo lang('app.language_linear_headphone_please_click_after'); ?></p>
                                </div>
                                <div class="col-xs-2">
                                    <img class="pull-right mt20 clearly-ok" src="<?php echo base_url('public/images/play-big.png'); ?>" onclick="play_clearly_sound();" />
                                </div>

                                <hr/>

                                <div class="col-xs-12">
                                    <h2><?php echo lang('app.language_linear_headphone_step2'); ?></h2>
                                    <p><?php echo lang('app.language_linear_headphone_voulume_adjust'); ?></p>
                                </div>

                                <?php
                                $sound_script_beep = "<script>
                                                        function go_to_sound_test(){
                                                           $('.headphone-section').hide(); 
                                                           $('.sound-test-section').show(); 
                                                        }
                                                        
                                                        function play_clearly_sound(){
                                                            var clearly = new Howl({
                                                            src: ['" . @audio_efs_sounds('clearly') . "'],
                                                            onload: function() {
                                                                alertify.defaults.transition = 'normal';
                                                                alertify.confirm('" . lang('app.language_linear_can_you_hear_confirm_title') . "',
                                                                    function () {
                                                                        $('.sound-test-section').hide();
                                                                        $('.rubric').text('CATs Step Primary Placement Test');
                                                                        $('.start-test-section').show();
                                                                    },
                                                                    function (e) {
                                                                        alertify.alert('" . lang('app.language_linear_can_you_hear_confirm_repeat_message') . "', function(){}).set('labels', {ok: '" . lang('app.language_linear_can_you_hear_confirm_ok_btn') . "'});

                                                                    }).set('labels', {ok: '" . lang('app.language_linear_can_you_hear_confirm_yes_btn') . "', cancel: '" . lang('app.language_linear_can_you_hear_confirm_no_btn') . "'});       
                                                            } 
                                                            });
                                                            clearly.play();
                                                        }
                                                    </script>";
                                echo $sound_script_beep;
                                ?>
                            </div>
                            <div class="start-test-section quiz-start-headings" style="display:none;">
                                <h2><?php echo lang('app.language_linear_keep_quiet_title'); ?></h2>
                                <div class="text-right mt200">
                                    <button type="button" class="btn btn-sm btn-continue btn-continue-child" onclick="go_to_introduction_section()"><?php echo lang('app.language_linear_start_btn'); ?>&nbsp;<i class="fa fa-arrow-right" aria-hidden="true"></i></button>
                                </div>  
                                <?php
                                $sound_script_intro = "<script>
                                                        function go_to_introduction_section(){
                                                           $('.start-test-section').hide(); 
                                                           $('.introduction-test-section').show(); 
                                                           setTimeout(function(){
                                                                intro_test_sound();
                                                           }, 1000)
                                                        }
                                                        
                                                        function intro_test_sound(){
                                                            var intro = new Howl({
                                                            src: ['" . @audio_efs_sounds('intro') . "'],
                                                            onload: function() {
                                                            },
                                                            onend: function(){
                                                               setTimeout(function(){
                                                                    $('.start-btn').click();
                                                               }, 2000)
                                                            }
                                                            });
                                                            intro.play();
                                                        }
                                                    </script>";
                                echo $sound_script_intro;
                                ?>
                            </div>
                            <div class="introduction-test-section quiz-start-headings" style="display:none;">
                                <p><?php echo lang('app.language_linear_welcome_title'); ?> CATs Step Primary Placement Test.</p>
                                <p><?php echo lang('app.language_linear_listening_title'); ?></p>
                                <p><?php echo lang('app.language_linear_hear_sound_title'); ?></p>
                                <p>[BEEP]</p>
                                <p><?php echo lang('app.language_linear_hear_sound_twice_title'); ?></p>
                                <p><button class="btn btn-primary btn-lg pull-right start-btn" style="display:none;" data-bind="click: startQuiz"><?php echo lang('app.language_linear_start_btn');?></button></p>
                                <?php
                                $sound_script_beep_again = "<script>
                                                        var make_beep = function(){
                                                            var beep = new Howl({src: ['" . @audio_efs_sounds('beep') . "']});
                                                            return beep;
                                                        }
                                                        var make_again = function(){
                                                           var again = new Howl({src: ['" . @audio_efs_sounds('again') . "']});
                                                           return again;    
                                                        }
                                                        
                                                    </script>";
                                echo $sound_script_beep_again;
                                ?>
                            </div>
                        </div>
                    </section>
                    <section id="reading-test-section" data-bind="visible: listeningComplete">
                        <div class="quiz-start-headings text-center mt150" >
                            <p><?php echo 'That is the end of the Listening questions.'; ?></p>
                            <p><?php echo 'You can take off your headphones.'; ?></p>
                            <p><?php echo 'The Reading questions will start soon.'; ?></p>
                        </div>
                    </section>
                    <!--Question block starts-->
                    <section class="quiz" data-bind="visible: quizStarted() && !quizComplete() && !listeningComplete()">
                        <div class="question-pool">
                            <section class="quiz" data-title="" data-subtitle="<?php echo lang('app.language_initial_message'); ?>">
                                <?php
                                if (isset($linear)) {
                                    $qId = 1;
                                    ?>
                                    <?php foreach ($linear['screens'] as $lk => $lv): ?>
                                        <section class="question" name="" id="<?php echo $lv['screenid'] . '_' . $lv['part'] . '_' . $qId; ?>">
                                            <?php if ($lv['part'] == '1611'): ?>

                                                <div class="row mt100">
                                                    <div class="col-sm-4">
                                                        <label class="option">
                                                            <input type="radio" id="answer_<?php echo $lv['screenid']; ?>" name="answer_<?php echo $lv['screenid']; ?>" value="A">
                                                            <span class="checkmark_rec p1611"><img src="data:image/svg+xml;base64,<?php echo @image_efs(str_replace(".svg","",$lv['questions'][0]['answers']['A'])); ?>" alt="opt-1"/></span>
                                                        </label></div>   
                                                    <div class="col-sm-4">
                                                        <label class="option">
                                                            <input type="radio" id="answer_<?php echo $lv['screenid']; ?>" name="answer_<?php echo $lv['screenid']; ?>" value="B">
                                                            <span class="checkmark_rec p1611"><img src="data:image/svg+xml;base64,<?php echo @image_efs(str_replace(".svg","",$lv['questions'][0]['answers']['B'])); ?>" alt="opt-2"/></span>
                                                        </label></div>
                                                    <div class="col-sm-4">
                                                        <label class="option">
                                                            <input type="radio" id="answer_<?php echo $lv['screenid']; ?>" name="answer_<?php echo $lv['screenid']; ?>" value="C">
                                                            <span class="checkmark_rec p1611"><img src="data:image/svg+xml;base64,<?php echo @image_efs(str_replace(".svg","",$lv['questions'][0]['answers']['C'])); ?>" alt="opt-3"/></span>
                                                        </label></div>                          
                                                </div>

                                                <?php
                                                // Primary placement test - question start
                                                $sound_script_1611 = "<script>function play_sound_" . $lv['screenid'] . '_' . $lv['part'] . '_' . $qId . "(){
                                                                                                var counter_1611 = 0;
                                                                                                var maxIterations_1611 = 2;

                                                                                                var q_1611 = new Howl({
                                                                                                  src: ['" . @audio_efs(str_replace(".mp3","",$lv['questions'][0]['questionaudio'])) . "'],
                                                                                                  onend: function() {
                                                                                                     counter_1611++;
                                                                                                     console.log('Finished iteration ' + counter_1611);
                                                                                                     if (counter_1611 == maxIterations_1611) {
                                                                                                          console.log('Last time!')
                                                                                                          q_1611.loop(false);
                                                                                                          timedCount();
                                                                                                      }else{
                                                                                                     
                                                                                                        var again = make_again();
                                                                                                        again.on('end', function(){
                                                                                                            q_1611.play();
                                                                                                        });
                                                                                                        again.play();
                                                                                                     
                                                                                                     }
                                                                                                  }  
                                                                                                  });
                                                                                                  var beep = make_beep();
                                                                                                  beep.on('end', function(){
                                                                                                      q_1611.play();
                                                                                                  });
                                                                                                  beep.play();
                                                                                                  
                                                                                                }
                                                                                              </script>";
                                                echo $sound_script_1611;
                                                ?>


                                            <?php endif; ?>
                                            <?php if ($lv['part'] == '1612'): ?>

                                                <div class="row mt30">
                                                    <div class="col-sm-12 text-center">
                                                        <div class="question_img p1612">
                                                            <img src="data:image/svg+xml;base64,<?php echo @image_efs(str_replace(".svg","",$lv['questions'][0]['questionimage'])); ?>" alt="1612"/>
                                                        </div></div>
                                                </div>
                                                <div class="row mt50">
                                                    <div class="col-sm-4 text-center">
                                                        <label class="option-circle">
                                                            <input type="radio" id="answer_<?php echo $lv['screenid']; ?>" name="answer_<?php echo $lv['screenid']; ?>" value="A">
                                                            <span class="checkmark a_ans_playback_<?php echo $lv['screenid']; ?>">A</span>
                                                        </label></div>   
                                                    <div class="col-sm-4 text-center">
                                                        <label class="option-circle">
                                                            <input type="radio" id="answer_<?php echo $lv['screenid']; ?>" name="answer_<?php echo $lv['screenid']; ?>" value="B">
                                                            <span class="checkmark b_ans_playback_<?php echo $lv['screenid']; ?>">B</span>
                                                        </label></div>
                                                    <div class="col-sm-4 text-center">
                                                        <label class="option-circle">
                                                            <input type="radio" id="answer_<?php echo $lv['screenid']; ?>" name="answer_<?php echo $lv['screenid']; ?>" value="C">
                                                            <span class="checkmark c_ans_playback_<?php echo $lv['screenid']; ?>">C</span>
                                                        </label></div>                          
                                                </div>
                                                <?php
                                                $sound_script_1612 = "<script>function play_sound_" . $lv['screenid'] . '_' . $lv['part'] . '_' . $qId . "(){
                                                                            var counter_1612 = 0;
                                                                            var maxIterations_1612 = 2;

                                                                            var q_1612 = new Howl({
                                                                              src: ['" . @audio_efs(str_replace(".mp3","",$lv['questions'][0]['questionaudio'])) . "'],
                                                                              onend: function() {
                                                                                 counter_1612++;
                                                                                 var a_1612 = new Howl({
                                                                                 src: ['" . @audio_efs(str_replace(".mp3","",$lv['questions'][0]['answeraudios']['A'])) . "'],
                                                                                 onload: function() { 
                                                                                                   $('.a_ans_playback_" . $lv['screenid'] . "').addClass('audio');
                                                                                 },    
                                                                                 onend: function() {
                                                                                         var b_1612 = new Howl({
                                                                                         src: ['" . @audio_efs(str_replace(".mp3","",$lv['questions'][0]['answeraudios']['B'])) . "'],
                                                                                         onload: function() { 
                                                                                                $('.a_ans_playback_" . $lv['screenid'] . "').removeClass('audio');
                                                                                                $('.b_ans_playback_" . $lv['screenid'] . "').addClass('audio');
                                                                                         },    
                                                                                         onend: function() {
                                                                                                 var c_1612 = new Howl({
                                                                                                                     src: ['" . @audio_efs(str_replace(".mp3","",$lv['questions'][0]['answeraudios']['C'])) . "'],
                                                                                                                     onload: function() {
                                                                                                                        $('.b_ans_playback_" . $lv['screenid'] . "').removeClass('audio');
                                                                                                                        $('.c_ans_playback_" . $lv['screenid'] . "').addClass('audio');
                                                                                                                     },    
                                                                                                                     onend: function() {
                                                                                                                                console.log('Finished iteration ' + counter_1612);
                                                                                                                                    if (counter_1612 == maxIterations_1612) {
                                                                                                                                            console.log('Last time!')
                                                                                                                                            q_1612.loop(false);
                                                                                                                                            $('.c_ans_playback_" . $lv['screenid'] . "').removeClass('audio');
                                                                                                                                            timedCount();
                                                                                                                                    }else{
                                                                                                                                        $('.c_ans_playback_" . $lv['screenid'] . "').removeClass('audio');
                                                                                                                                                var again = make_again();
                                                                                                                                                again.on('end', function(){
                                                                                                                                                    q_1612.play();
                                                                                                                                                });
                                                                                                                                                again.play();
                                                                                                                                        
                                                                                                                                    }
                                                                                                                            }
                                                                                                                     });
                                                                                                
                                                                                                    c_1612.play();
                                                                                                                 
                                                                                            }
                                                                                         });
                                                                                         
                                                                                                    b_1612.play();
                                                                                        
                                                                                 }
                                                                              });
                                                                                        
                                                                                                    a_1612.play();
                                                                                         
                                                                              }
                                                                            });
                                                                            var beep = make_beep();
                                                                            beep.on('end', function(){
                                                                                q_1612.play();
                                                                            });
                                                                            beep.play();
                                                                            }</script>";
                                                echo $sound_script_1612;
                                                ?>

                                            <?php endif; ?>
                                            <?php if ($lv['part'] == '1613'): ?>
                                                <div class="row">
                                                    <div class="col-sm-12 text-center">
                                                        <div class="p1613">
                                                            <img src="data:image/svg+xml;base64,<?php echo @image_efs(str_replace(".svg","",$lv['image'])); ?>" alt="1613"/>
                                                        </div></div>
                                                </div>
                                                <div class="row mt50">
                                                    <div class="col-sm-offset-4 col-sm-4">
                                                        <div class="">
                                                            <label class="check_btn-yes">
                                                                <input type="radio" id="<?php echo 'answer_' . $lv['screenid']; ?>" name="<?php echo 'answer_' . $lv['questions'][0]['questionno'] . '_' . $lv['screenid']; ?>"  value="YES">
                                                                <span class="btn-yes"><img src="<?php echo base_url('public/images/tick.png'); ?>" alt="tick"></span>
                                                            </label>
                                                            <label class="check_btn-no">
                                                                <input type="radio" id="<?php echo 'answer_' . $lv['screenid']; ?>" name="<?php echo 'answer_' . $lv['questions'][0]['questionno'] . '_' . $lv['screenid']; ?>"  value="NO">
                                                                <span class="btn-no"><img src="<?php echo base_url('public/images/cross.png'); ?>" alt="cross"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>                            



                                                <?php
                                                $sound_script_1613 = "<script>function play_sound_" . $lv['screenid'] . '_' . $lv['part'] . '_' . $qId . "(){
                                                                                                var counter_1613 = 0;
                                                                                                var maxIterations_1613 = 2;

                                                                                                var q_1613 = new Howl({
                                                                                                  src: ['" . @audio_efs(str_replace(".mp3","",$lv['questions'][0]['questionaudio'])) . "'],
                                                                                                  onend: function() {
                                                                                                     counter_1613++;
                                                                                                     console.log('Finished iteration ' + counter_1613);
                                                                                                     if (counter_1613 == maxIterations_1613) {
                                                                                                          console.log('Last time!')
                                                                                                          q_1613.loop(false);
                                                                                                          timedCount();
                                                                                                     }else{
                                                                                                                    var again = make_again();
                                                                                                                    again.on('end', function(){
                                                                                                                        q_1613.play();
                                                                                                                    });
                                                                                                                    again.play();
                                                                                                                   
                                                                                                     }
                                                                                                  }  
                                                                                                  });
                                                                                                  var beep = make_beep();
                                                                                                  beep.on('end', function(){
                                                                                                      q_1613.play();
                                                                                                  });
                                                                                                  beep.play();
                                                                                                }
                                                                                              </script>";
                                                echo $sound_script_1613;
                                                ?>


                                            <?php endif; ?>
                                            <?php if ($lv['part'] == '1622'): ?>
                                                <div class="row mt150">
                                                    <div class="col-sm-4 text-center">                                  
                                                        <label class="option-circle">
                                                            <input type="radio" id="answer_<?php echo $lv['screenid']; ?>" name="answer_<?php echo $lv['screenid']; ?>" value="A">
                                                            <span class="checkmark a_ans_playback_<?php echo $lv['screenid']; ?>">A</span>
                                                        </label>
                                                    </div>   
                                                    <div class="col-sm-4 text-center">
                                                        <label class="option-circle">
                                                            <input type="radio" id="answer_<?php echo $lv['screenid']; ?>" name="answer_<?php echo $lv['screenid']; ?>" value="B">
                                                            <span class="checkmark b_ans_playback_<?php echo $lv['screenid']; ?>">B</span>
                                                        </label>
                                                    </div>
                                                    <div class="col-sm-4 text-center">
                                                        <label class="option-circle">
                                                            <input type="radio" id="answer_<?php echo $lv['screenid']; ?>" name="answer_<?php echo $lv['screenid']; ?>" value="C">
                                                            <span class="checkmark c_ans_playback_<?php echo $lv['screenid']; ?>">C</span>
                                                        </label> </div>     
                                                </div>
                                                <?php
                                                $sound_script_1622 = "<script>function play_sound_" . $lv['screenid'] . '_' . $lv['part'] . '_' . $qId . "(){
                                                                            var counter_1622 = 0;
                                                                            var maxIterations_1622 = 2;

                                                                            var q_1622 = new Howl({
                                                                              src: ['" . @audio_efs(str_replace(".mp3","",$lv['questions'][0]['questionaudio'])) . "'],
                                                                              onend: function() {
                                                                                 counter_1622++;
                                                                                 var a_1622 = new Howl({
                                                                                 src: ['" . @audio_efs(str_replace(".mp3","",$lv['answeraudios']['A'])) . "'],
                                                                                 onload: function() { 
                                                                                                   $('.a_ans_playback_" . $lv['screenid'] . "').addClass('audio');
                                                                                 },    
                                                                                 onend: function() {
                                                                                         var b_1622 = new Howl({
                                                                                         src: ['" . @audio_efs(str_replace(".mp3","",$lv['answeraudios']['B'])) . "'],
                                                                                         onload: function() { 
                                                                                                $('.a_ans_playback_" . $lv['screenid'] . "').removeClass('audio');
                                                                                                $('.b_ans_playback_" . $lv['screenid'] . "').addClass('audio');
                                                                                         },    
                                                                                         onend: function() {
                                                                                                 var c_1622 = new Howl({
                                                                                                                     src: ['" . @audio_efs(str_replace(".mp3","",$lv['answeraudios']['C'])) . "'],
                                                                                                                     onload: function() {
                                                                                                                        $('.b_ans_playback_" . $lv['screenid'] . "').removeClass('audio');
                                                                                                                        $('.c_ans_playback_" . $lv['screenid'] . "').addClass('audio');
                                                                                                                     },    
                                                                                                                     onend: function() {
                                                                                                                                console.log('Finished iteration ' + counter_1622);
                                                                                                                                    if (counter_1622 == maxIterations_1622) {
                                                                                                                                            console.log('Last time!')
                                                                                                                                            q_1622.loop(false);
                                                                                                                                            $('.c_ans_playback_" . $lv['screenid'] . "').removeClass('audio');
                                                                                                                                            timedCount();
                                                                                                                                    }else{
                                                                                                                                        $('.c_ans_playback_" . $lv['screenid'] . "').removeClass('audio');
                                                                                                                                                var again = make_again();
                                                                                                                                                again.on('end', function(){
                                                                                                                                                    q_1622.play();
                                                                                                                                                });
                                                                                                                                                again.play();
                                                                                                                                        
                                                                                                                                    }
                                                                                                                            }
                                                                                                                     });
                                                                                                
                                                                                                    c_1622.play();
                                                                                                                 
                                                                                            }
                                                                                         });
                                                                                         
                                                                                                    b_1622.play();
                                                                                        
                                                                                 }
                                                                              });
                                                                                        
                                                                                                    a_1622.play();
                                                                                         
                                                                              }
                                                                            });
                                                                            var beep = make_beep();
                                                                            beep.on('end', function(){
                                                                                q_1622.play();
                                                                            });
                                                                            beep.play();
                                                                            }</script>";
                                                echo $sound_script_1622;
                                                ?>

                                            <?php endif; ?>  

                                            <?php if ($lv['part'] == '1627'): ?>
                                                <div class="row">
                                                    <div class="col-sm-12 qus_block text-center">
                                                        <h3><?php echo $lv['questions'][0]['question']; ?></h3>
                                                    </div>
                                                </div>
                                                <div class="row mt20">
                                                    <div class="col-sm-12 text-center">
                                                        <div class="question_img p1627">
                                                            <img src="data:image/svg+xml;base64,<?php echo @image_efs(str_replace(".svg","",$lv['image'])); ?>" alt="1627"/>
                                                        </div></div>
                                                </div>
                                                <div class="row mt50">
                                                    <div class="col-sm-offset-4 col-sm-4">
                                                        <div class="">
                                                            <label class="check_btn-yes">
                                                                <input type="radio" id="<?php echo 'answer_' . $lv['screenid']; ?>" name="<?php echo 'answer_' . $lv['questions'][0]['questionno'] . '_' . $lv['screenid']; ?>"  value="YES">
                                                                <span class="btn-yes"><img src="<?php echo base_url('public/images/tick.png'); ?>" alt="tick"></span>
                                                            </label>
                                                            <label class="check_btn-no">
                                                                <input type="radio" id="<?php echo 'answer_' . $lv['screenid']; ?>" name="<?php echo 'answer_' . $lv['questions'][0]['questionno'] . '_' . $lv['screenid']; ?>"  value="NO">
                                                                <span class="btn-no"><img src="<?php echo base_url('public/images/cross.png'); ?>" alt="cross"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div> 
                                                <?php
                                                $time_limit_1627 = (isset($lv['timelimit'])) ? $lv['timelimit'] + 1 : '15';
                                                $timing_script_1627 = "<script>function play_timing_" . $lv['screenid'] . '_' . $lv['part'] . '_' . $qId . "(){
                                                                                var timeleft_1627 = " . $time_limit_1627 . ";
                                                                                function timedCount_1627() {
                                                                                    if(timeleft_1627 <= 0){
                                                                                    timeleft_1627 = " . $time_limit_1627 . ";
                                                                                    
                                                                                        clearTimeout(timer_1627);
                                                                                        return false;
                                                                                    }
                                                                                    sectext_1627 = '" . lang('app.language_linear_timer_seconds') . "';
                                                                                    timeleft_1627 = timeleft_1627 - 1;
                                                                                    if(timeleft_1627 <= 1){
                                                                                        sectext_1627 = '" . lang('app.language_linear_timer_second') . "';
                                                                                    }
                                                                                    $('#countdowntimer').text(timeleft_1627+' '+sectext_1627);
                                                                                    var timer_1627 = setTimeout(function(){ timedCount_1627() }, 1000);
                                                                                } 
                                                                                
                                                                                
                                                                                
                                                                                setTimeout(function(){
                                                                                    $('.countdowntimer_global').show();
                                                                                    setTimeout(function(){
                                                                                        $('.countdowntimer_global').hide();
                                                                                        if(" . $linear['questions'] . " == " . $qId . "){
                                                                                             $('.finish-btn').click();  
                                                                                        }else{
                                                                                             $('.next-btn').click();  
                                                                                        }
                                                                                    }, " . $time_limit_1627 . '000' . ");
                                                                                    timedCount_1627();
                                                                                },2000);
                                                            
                                                                       }</script>";
                                                echo $timing_script_1627;
                                                ?>
                                            <?php endif; ?>

                                            <?php if ($lv['part'] == '1628'): ?>

                                                <div class="row mt20">
                                                    <div class="col-sm-12">
                                                        <div class="sys_QA_block">
                                                            <?php echo $lv['passage']; ?>
                                                        </div></div>
                                                </div>
                                                <div class="row mt50">
                                                    <div class="col-sm-12">
                                                        <div class="sys_qus_block text-center">
                                                            <p><?php echo $lv['questions'][0]['question']; ?></p>                                              
                                                        </div></div>
                                                </div>
                                                <div class="row mt30">
                                                    <div class="col-sm-offset-4 col-sm-4">
                                                        <div class="">
                                                            <label class="check_btn-yes">
                                                                <input type="radio" id="<?php echo 'answer_' . $lv['screenid']; ?>" name="<?php echo 'answer_' . $lv['questions'][0]['questionno'] . '_' . $lv['screenid']; ?>"  value="YES">
                                                                <span class="btn-yes"><img src="<?php echo base_url('public/images/tick.png'); ?>" alt="tick"></span>
                                                            </label>
                                                            <label class="check_btn-no">
                                                                <input type="radio" id="<?php echo 'answer_' . $lv['screenid']; ?>" name="<?php echo 'answer_' . $lv['questions'][0]['questionno'] . '_' . $lv['screenid']; ?>"  value="NO">
                                                                <span class="btn-no"><img src="<?php echo base_url('public/images/cross.png'); ?>" alt="cross"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div> 
                                                <?php
                                                $time_limit_1628 = (isset($lv['timelimit'])) ? $lv['timelimit'] + 1 : '30';
                                                $timing_script_1628 = "<script>function play_timing_" . $lv['screenid'] . '_' . $lv['part'] . '_' . $qId . "(){
                                                                                var timeleft_1628 = " . $time_limit_1628 . ";
                                                                                function timedCount_1628() {
                                                                                    if(timeleft_1628 <= 0){
                                                                                    timeleft_1628 = " . $time_limit_1628 . ";
                                                                                    
                                                                                        clearTimeout(timer_1628);
                                                                                        return false;
                                                                                    }
                                                                                    sectext_1628 = '" . lang('app.language_linear_timer_seconds') . "';
                                                                                    timeleft_1628 = timeleft_1628 - 1;
                                                                                    if(timeleft_1628 <= 1){
                                                                                        sectext_1628 = '" . lang('app.language_linear_timer_second') . "';
                                                                                    }
                                                                                    $('#countdowntimer').text(timeleft_1628+' '+sectext_1628);
                                                                                    var timer_1628 = setTimeout(function(){ timedCount_1628() }, 1000);
                                                                                } 
                                                                                 //WP-1087 - Implementation for primary placement test question page css issue when dom contains larger data
                                                                                var partId = ".$lv['part'].";
                                                                                var qusId = '" . $lv['screenid'] . '_' . $lv['part'] . '_' . $qId . "';
                                                                                if(partId == '1628'){  
                                                                                    if($('#'+qusId).innerHeight() > 512){
                                                                                        $('#'+qusId).parents('.modal-body.md_bg').css('min-height', '619px')
                                                                                        $('#'+qusId+' .mt50').css('margin-top', '40px')
                                                                                        $('#'+qusId+' .mt30').css('margin-top', '20px')
                                                                                    }
                                                                                }
                                                                                setTimeout(function(){
                                                                                    $('.countdowntimer_global').show();
                                                                                    setTimeout(function(){
                                                                                        $('.countdowntimer_global').hide();
                                                                                        if(" . $linear['questions'] . " == " . $qId . "){
                                                                                             $('.finish-btn').click();  
                                                                                        }else{
                                                                                             $('.next-btn').click();  
                                                                                        }
                                                                                    }, " . $time_limit_1628 . '000' . ");
                                                                                    timedCount_1628();
                                                                                },2000); 
                                                                       }</script>";
                                                echo $timing_script_1628;
                                                ?>

                                            <?php endif; ?>
                                            <?php if ($lv['part'] == '1629'): ?>
                                            
                                                <?php
                                                if (!function_exists('set_select_box_1029')) {

                                                    function set_select_box_1029($missing_answer, $screenid = false) {
                                                        //$pattern = "/\[(.*?)\]/si";
                                                        $replaceString = array('[' => '', ']' => '');
                                                        $pattern = "/\[(.*?)\]/si";

                                                        preg_match_all($pattern, $missing_answer, $matches);
                                                        //$explodeArr = explode('/',$matches[0]);
                                                        $replaceString = array('[' => '', ']' => '');
                                                        $plselect = array(' ' => '');

                                                        $attr = array('id' => 'answer_' . $screenid, 'autocomplete' => "off");

                                                        foreach ($matches[0] as $key => $value) {

                                                            $explodeArr[$matches[0][$key]] = explode('/', str_replace(array_keys($replaceString), $replaceString, trim($matches[0][$key])));
                                                            $form_options[$matches[0][$key]] = '<span class="form-group" style="color:black;">' . form_dropdown('answer_' . $screenid, array_merge($plselect, array_combine(array_map('trim', $explodeArr[$matches[0][$key]]), array_map('trim', $explodeArr[$matches[0][$key]]))), '', $attr) . "</span>";
                                                        }

                                                        return $form_options;
                                                    }

                                                }
                                                ?>
                                                <div class="row">
                                                    <div class="col-sm-12 sys_qus_block text-center" style="display:inline-block; margin-top:8%; border-radius: 5px;">
            <!--                                                        <h3><?php echo $lv['questions'][0]['missinganswer']; ?></h3>-->
                                                        <?php
                                                        $question['missinganswer'] = $lv['questions'][0]['missinganswer'];
                                                        $screenid = $lv['screenid']
                                                        ?>
                                                        <ol>
                                                            <li style="list-style: none;"><p class="question1" style="text-align: left;"><?php echo str_replace(array_keys(set_select_box_1029($question['missinganswer'])), set_select_box_1029($question['missinganswer'], $screenid), $question['missinganswer']); ?></p></li>
                                                        </ol>
                                                    </div>
                                                </div>
                                                <?php
                                                $time_limit_1629 = (isset($lv['timelimit'])) ? $lv['timelimit'] + 1 : '15';
                                                $timing_script_1629 = "<script>function play_timing_" . $lv['screenid'] . '_' . $lv['part'] . '_' . $qId . "(){
                                                                                var timeleft_1629 = " . $time_limit_1629 . ";
                                                                                function timedCount_1629() {
                                                                                    if(timeleft_1629 <= 0){
                                                                                    timeleft_1629 = " . $time_limit_1629 . ";
                                                                                    
                                                                                        clearTimeout(timer_1629);
                                                                                        return false;
                                                                                    }
                                                                                    sectext_1629 = '" . lang('app.language_linear_timer_seconds') . "';
                                                                                    timeleft_1629 = timeleft_1629 - 1;
                                                                                    if(timeleft_1629 <= 1){
                                                                                        sectext_1629 = '" . lang('app.language_linear_timer_second') . "';
                                                                                    }
                                                                                    $('#countdowntimer').text(timeleft_1629+' '+sectext_1629);
                                                                                    var timer_1629 = setTimeout(function(){ timedCount_1629() }, 1000);
                                                                                } 
                                                                                
                                                                                
                                                                                
                                                                                setTimeout(function(){
                                                                                    $('.countdowntimer_global').show();
                                                                                    setTimeout(function(){
                                                                                        $('.countdowntimer_global').hide();
                                                                                        if(" . $linear['questions'] . " == " . $qId . "){
                                                                                             $('.finish-btn').click();  
                                                                                        }else{
                                                                                             $('.next-btn').click();  
                                                                                        }
                                                                                    }, " . $time_limit_1629 . '000' . ");
                                                                                    timedCount_1629();
                                                                                },2000); 
                                                            
                                                                       }</script>";
                                                echo $timing_script_1629;
                                                ?>
        <?php endif; ?>
                                            
        <?php if ($lv['part'] == '1630'): ?>
                                              
                                                <?php
                                                //$missinganswer = $lv['questions'][0]['missinganswer'];

                                                @preg_match('/\[(.*?)\]/', $lv['questions'][0]['missinganswer'], $output);
                                                $newdata = @explode('/', $output[1]);
                                                if (is_array($newdata) && !empty($newdata)) {


                                                    if (count($newdata) >= 2) {
                                                        $lengh_str = strlen(max($newdata));
                                                    } else {
                                                        $lengh_str = strlen($newdata[0]);
                                                    }
                                                }
                                                isset($lv['questions'][0]['missinganswer']) ? $lv['questions'][0]['missinganswer'] = preg_replace("/\[[^)]+\]/", "[$lengh_str]", $lv['questions'][0]['missinganswer']) : '';


                                                preg_match('/\[(.*?)\]/', $lv['questions'][0]['missinganswer'], $textboxes);

                                                if (!function_exists('replace_between_1630')) {

                                                    function replace_between_1630($str, $needle_start, $needle_end, $replacement) {
                                                        $pos = strpos($str, $needle_start);
                                                        $start = $pos === false ? 0 : $pos + strlen($needle_start);

                                                        $pos = strpos($str, $needle_end, $start);
                                                        $end = $start === false ? strlen($str) : $pos;

                                                        return substr_replace($str, $replacement, $start, $end - $start);
                                                    }

                                                }
                                                $screens_id = $lv['screenid'];
//print_r($screens['questions'][0]['missinganswer']);
                                                if (!function_exists('replacement_textboxes_1630')) {

                                                    function replacement_textboxes_1630($textboxes, $screens_id) {
                                                        $replacement = '<div class="form-group input-group inline">';
                                                        if (isset($textboxes[1]) && $textboxes[1] != ''):
                                                            for ($j = 1; $j <= $textboxes[1]; $j++):
                                                                $replacement .= '<input type="text" id="answer_' . $screens_id.'" name= "answer_' . $screens_id . '[]" class="answers simple text-center answers_1630" maxlength="1" style="color:black" autofocus>';
                                                            endfor;
                                                        endif;
                                                        $replacement .= '</div>';
                                                        return $replacement;
                                                    }

                                                }


                                                $replaceString = array('[' => '', ']' => '');
                                                if (!function_exists('str_lreplace_1630')) {

                                                    function str_lreplace_1630($search, $replace, $subject) {
                                                        // substr("testers", -1);
                                                        //return $subject;

                                                        $pos = strrpos($subject, $search);

                                                        if ($pos !== false) {
                                                            $subject = substr_replace($subject, $replace, $pos, strlen($search));
                                                        }

                                                        return $subject;
                                                    }

                                                }
                                                $whole_start_string = explode('[', $lv['questions'][0]['missinganswer']);
                                                $whole_string_before_square = $whole_start_string['0'];
                                                $new_replaced_before_string = str_lreplace_1630(substr($whole_string_before_square, -1), '<span style="color:#000;display: inline-block;">' . substr($whole_string_before_square, -1), $whole_string_before_square);
                                                $whole_end_string = explode(']', $lv['questions'][0]['missinganswer']);
                                                $whole_string_after_square = $whole_end_string['1'];
                                                ?>
                                                <div class="row mt50">
                                                    <div class="col-sm-12">
                                                        <div class="col-sm-12 sys_qus_block form-inline text-center" style = "text-align:left; ">
                                                            <?php $screens_id = $lv['screenid'] ?>
                                                                <?php echo "<span style='color:white; font-size:22px;'>".$new_replaced_before_string . "" . replacement_textboxes_1630($textboxes, $screens_id) . "</span>" . $whole_string_after_square.""; ?> 
                                                        </div></div>
                                                </div>
                                                <?php
                                                $time_limit_1630 = (isset($lv['timelimit'])) ? $lv['timelimit'] + 1 : '15';
                                                $timing_script_1630 = "<script>function play_timing_" . $lv['screenid'] . '_' . $lv['part'] . '_' . $qId . "(){
                                                   $('.modal-body.md_bg').css('min-height', '512px');
                                                                                var timeleft_1630 = " . $time_limit_1630 . ";
                                                                                function timedCount_1630() {
                                                                                    if(timeleft_1630 <= 0){
                                                                                    timeleft_1630 = " . $time_limit_1630 . ";
                                                                                    
                                                                                        clearTimeout(timer_1630);
                                                                                        return false;
                                                                                    }
                                                                                    sectext_1630 = '" . lang('app.language_linear_timer_seconds') . "';
                                                                                    timeleft_1630 = timeleft_1630 - 1;
                                                                                    if(timeleft_1630 <= 1){
                                                                                        sectext_1630 = '" . lang('app.language_linear_timer_second') . "';
                                                                                    }
                                                                                    $('#countdowntimer').text(timeleft_1630+' '+sectext_1630);
                                                                                    var timer_1630 = setTimeout(function(){ timedCount_1630() }, 1000);
                                                                                } 
                                                                                
                                                                                
                                                                                
                                                                                setTimeout(function(){
                                                                                    $('.countdowntimer_global').show();
                                                                                    setTimeout(function(){
                                                                                        $('.countdowntimer_global').hide();
                                                                                        if(" . $linear['questions'] . " == " . $qId . "){
                                                                                             $('.finish-btn').click();  
                                                                                        }else{
                                                                                             $('.next-btn').click();  
                                                                                        }
                                                                                    }, " . $time_limit_1630 . '000' . ");
                                                                                    timedCount_1630();
                                                                                },2000);
                                                            
                                                                       }</script>";
                                                echo $timing_script_1630;
                                                ?>
                                        <?php endif; ?>
                                        </section>
                                        <?php
                                        $qId++;
                                    endforeach;
                                    ?>

                                <?php } else { ?>
                                    <p>No tests found.</p>
<?php } ?> 

                        </div>
                        <div class="row mt50 quiz-btn-area" style="display:none;">
                            <div class="col-sm-12">
                                <div class="text-right">
                                    <button class="btn btn-sm btn-continue btn-continue-child next-btn" data-bind="click: moveNextQuestion, disable: currentQuestionIsLast, visible: !currentQuestionIsLast()" ><?php echo lang('app.language_linear_next_btn'); ?><i class="fa fa-arrow-right" aria-hidden="true"></i></button>
                                    <button class="btn btn-sm btn-continue btn-continue-child finish-btn" data-bind="click: calculateScore, visible: currentQuestionIsLast"><?php echo lang('app.language_linear_finish_btn'); ?><i class="fa fa-arrow-right" aria-hidden="true"></i></button>&nbsp;&nbsp;<img class="loading" style="display:none;" src="<?php echo base_url('public/images/loading.gif'); ?>">
                                </div>
                            </div>                           
                        </div>

                    </section>


                    <!--Question block ends-->

                    <section class="score" data-bind="visible: quizComplete">
                        <p>Quiz Results:</p>
                        <h2 data-bind="text: quizTitle"></h2>
                        <h3 data-bind="text: quizSubTitle"></h3>
                        <div>Questions: <span data-bind="text: questionCount"></span></div>
                        <div>Date: <span data-bind="text: calculatedScoreDate"></span></div>
                        <div>Overall Score: <span data-bind="text: calculatedScore"></span>%</div>
                        <div>Correct Questions: <span data-bind="text: totalQuestionsCorrect"></span></div>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" data-bind="attr: { 'aria-valuenow': calculatedScore }, style: { width: calculatedScore() + '%' }, css: { 'progress-bar-success': quizPassed, 'progress-bar-danger': !quizPassed() }"></div>
                        </div>
                        <div class="pass-indicator">
                            <h1 data-bind="css: { 'text-success': quizPassed, 'text-danger': !quizPassed() }">
                                <span data-bind="visible: quizPassed">PASS</span>
                                <span data-bind="visible: !quizPassed()">FAIL</span>
                            </h1>
                        </div>
                    </section>
                </div>
            </div>
            <span class="countdowntimer_global" style="display:none;"><img alt="loading" src="<?php echo base_url('public/images/sandclock.gif'); ?>">&nbsp;&nbsp;<span id="countdowntimer" ></span>&nbsp;<?php //echo lang('app.language_linear_timer_seconds');  ?></span>
            <span style="color: red;font-size: 18px;line-height: 44px;display: none;"  id="failure-msg"></span>
        </div>  
    </div>  
</div>  


<?php include 'footer-booking.php'; ?>
<script>

    (function (window, $) {

        function getCurrentQuiz(container) {
            return container.find('.question-pool > .quiz');
        }
        function getAllQuestions(container) {
            return container.find('.question-pool > .quiz .question');
        }
        function getQuestionByIndex(container, index) {
            return container.find('.question-pool > .quiz .question:nth-child(' + index + ')');
        }
        function getNowDateTimeStamp() {
            var dt = new Date();
            return dt.getMonth() + '/' + dt.getDate() + '/' + dt.getFullYear() + ' ' + dt.getHours() + ':' + (dt.getMinutes() >= 10 ? dt.getMinutes() : '0' + dt.getMinutes());
        }

        var ViewModel = function (elem, options) {
            var self = this;
            self.element = $(elem);
            self.options = $.extend({}, engine.defaultOptions, options);

            self.element.find('.question-pool').load(self.options.quizUrl, function () {
                // quiz loaded into browser from HTML file

                getCurrentQuiz(self.element).find('.question').each(function (i, e) {
                    var question = $(this),
                            questionIndex = i,
                            answers = question.find('.answer'),
                            correctAnswerCount = question.find('.answer[data-correct]').length;

                    question.find('.hint a, .description a').attr('target', '_blank');

                    answers.each(function (ai, ae) {
                        var answer = $(this),
                                newAnswer = $('<label></label>').addClass('answer').append('<input type=\'checkbox\'/>').append($('<div></div>').html(answer.html()));
                        if (answer.is('[data-correct]')) {
                            newAnswer.attr('data-correct', '1');
                        }
                        if (correctAnswerCount <= 1) {
                            newAnswer.find('input').attr('type', 'radio').attr('name', 'question' + questionIndex);
                        }
                        answer.replaceWith(newAnswer);
                    });
                });


                self.questionCount(getAllQuestions(self.element).length);
                self.quizTitle(getCurrentQuiz(self.element).attr('data-title'));
                self.quizSubTitle(getCurrentQuiz(self.element).attr('data-subtitle'));
            });

            self.quizStarted = ko.observable(false);
            self.quizComplete = ko.observable(false);
            self.listeningComplete = ko.observable(false);
            self.listeningTemplate = 0;

            self.quizTitle = ko.observable('');
            self.quizSubTitle = ko.observable('');
            self.questionCount = ko.observable(0);

            self.currentQuestionIndex = ko.observable(0);
            self.currentQuestionIndex.subscribe(function (newValue) {
                if (newValue < 1) {
                    self.currentQuestionIndex(1);
                } else if (newValue > self.questionCount()) {
                    self.currentQuestionIndex(self.questionCount());

                } else {
                    getAllQuestions(self.element).hide()
                    getQuestionByIndex(self.element, newValue).show();

                    var play_screen_playback = 'play_sound_' + getQuestionByIndex(self.element, newValue).attr('id') + '()';
                    try {
                        eval(play_screen_playback);
                    } catch (e) {
                        var play_timing_playback = 'play_timing_' + getQuestionByIndex(self.element, newValue).attr('id') + '()';
                        var q_indexes = getQuestionByIndex(self.element, newValue).attr('id');
                        var res = q_indexes.split('_');
                        if (self.listeningTemplate == 0) {
                            self.listeningComplete(true);
                            setTimeout(function () {
                                self.listeningComplete(false);
                                self.listeningTemplate = 1;
                                eval(play_timing_playback);
                            }, 10000);
                        } else {
                            eval(play_timing_playback);
                        }
                        if (e instanceof SyntaxError) {

                        }
                    }
                }

                if (self.questionCount() !== 0) {
                    self.currentProgress(self.currentQuestionIndex() / self.questionCount() * 100);
                }

            });
            self.currentProgress = ko.observable(0);

            self.currentQuestionIsFirst = ko.computed(function () {
                return self.currentQuestionIndex() === 1;
            });
            self.currentQuestionIsLast = ko.computed(function () {
                return self.currentQuestionIndex() === self.questionCount();
            });
            self.currentQuestionHasHint = ko.computed(function () {
                var q = getQuestionByIndex(self.element, self.currentQuestionIndex());
                return (q.find('.hint').length > 0);
            });

            self.startQuiz = function () {
                // reset quiz to start state
                self.currentQuestionIndex(0);
                self.currentQuestionIndex(1);
                self.quizStarted(true);
            }
            self.moveNextQuestion = function () {
                $('.simple:first').focus();
                foo = 1;
                var sessionData = {'check': '1'};
                $.ajax({
                    type: 'GET',
                    url: "<?php echo site_url('site/check_session'); ?>",
                    data: sessionData,
                    dataType: 'json',
                    success: function (data) {
                        try {
                            if (data.session_found == 1) {

                            } else {
                                window.location = data.redirect;
                            }
                        } catch (p) {
                        }
                    },
                    error: function (jqXHR, exception, errorThrown) {
                        throw_error(jqXHR, exception);
                        return false;
                    }
                });

                $('.loading').hide();
                self.currentQuestionIndex(self.currentQuestionIndex() + 1);
            };
            self.movePreviousQuestion = function () {
                self.currentQuestionIndex(self.currentQuestionIndex() - 1);
            };
            self.showCurrentQuestionHint = function () {
                var q = getQuestionByIndex(self.element, self.currentQuestionIndex());
                q.find('.hint').slideDown();
            };
            self.showCurrentQuestionAnswer = function () {
                var q = getQuestionByIndex(self.element, self.currentQuestionIndex());
                q.find('.answer[data-correct]').addClass('highlight');
                q.find('.description').slideDown();
            };



            self.calculateScore = function () {
                var correctQuestions = [];
                var answer_pool = {};
                getAllQuestions(self.element).each(function (i, e) {
                    var q = $(this);
                    
                    // select radio buttons group (same name)
                    var radioButtons = q.find("input[type='radio']");
                    // save initial ckecked states
                    var k = 0;
                    answer_pool[q.find("input[type='radio']").attr("id")] = {};
                    $.each(radioButtons, function (index, rd) {
                        answer_pool[q.find("input[type='radio']").attr("id")][rd.value] = $(rd).is(':checked');
                    });
                    
                    //get all text boxes 
                    var textBoxes = q.find("input[type='text']");
                    //save inputed texts
                    answer_pool[q.find("input[type='text']").attr("id")] = {};
                    $.each(textBoxes, function (index, rd) {
                        answer_pool[q.find("input[type='text']").attr("id")][index] = rd.value;
                    }); 
                    
                    //get all text boxes 
                    var selecttBoxes = q.find("select");
                    //save inputed texts
                    answer_pool[q.find("select").attr("id")] = {};
                    $.each(selecttBoxes, function (index, rd) {
                        answer_pool[q.find("select").attr("id")][index] = rd.value;
                    }); 
                    
                    console.log(answer_pool);
                    

                });
                $('.finish-btn').attr('disabled', true);
                var objData = {};

                // Primary test once complete redirect this function 
                objData.user_reponses = answer_pool;
                // objData.token = "<?php // echo $this->encrypter->encrypt('UNDER_13'); ?>";
                objData.token = "<?php echo base64_encode($this->encrypter->encrypt('UNDER_13')); ?>";
                $('.loading').show();
                $.ajax({
                    type: 'POST',
                    url: "<?php echo site_url('site/get-the-right-primary-level'); ?>",
                    data: objData,
                    dataType: 'json',
                    success: function (data) {
                        $('.finish-btn').attr('disabled', false);
                        try {
                            if (data.success == 1) {
                                $('.loading').hide();
                                window.location = data.redirect;
                            } else {
                                window.location = data.redirect;
                            }
                        } catch (p) {
                        }
                    },
                    error: function (jqXHR, exception, errorThrown) {
                        throw_error(jqXHR, exception);
                        return false;
                    }
                });

                //console.log(answer_pool);
                /*self.totalQuestionsCorrect(correctQuestions.length);
                 
                 if (self.questionCount() !== 0) {
                 self.calculatedScore(Math.round((self.totalQuestionsCorrect() / self.questionCount() * 100) * 10) / 10);
                 }
                 
                 self.calculatedScoreDate(getNowDateTimeStamp());
                 
                 self.quizComplete(true);*/
            };
            self.totalQuestionsCorrect = ko.observable(0);
            self.calculatedScore = ko.observable(0);
            self.calculatedScoreDate = ko.observable('');
            self.quizPassed = ko.computed(function () {
                return self.calculatedScore() >= 50;
            });
        };


        var engine = window.catsQuizEngine = function (elem, options) {
            return new engine.fn.init(elem, options);
        };
        engine.defaultOptions = {
            quizUrl: 'original.htm'
        };
        engine.fn = engine.prototype = {
            version: 0.2,
            init: function (elem, options) {
                var vm = new ViewModel(elem[0], options);
                ko.applyBindings(vm, elem[0]);
            }
        };
        engine.fn.init.prototype = engine.fn;


    })(window, jQuery);

    var quizEngine = null;
    $(function () {
        quizEngine = catsQuizEngine($('#catsQuizEngine'));
    });
</script>

<script>
    // iOS check...ugly but necessary
    if (navigator.userAgent.match(/iPhone|iPad|iPod/i)) {

        $('.modal').on('show.bs.modal', function () {
            // Position modal absolute and bump it down to the scrollPosition
            $(this)
                    .css({
                        position: 'absolute',
                        marginTop: $(window).scrollTop() + 'px',
                        bottom: 'auto'
                    });
            // Position backdrop absolute and make it span the entire page
            //
            // Also dirty, but we need to tap into the backdrop after Boostrap 
            // positions it but before transitions finish.
            //
            setTimeout(function () {
                $('.modal-backdrop').css({
                    position: 'absolute',
                    top: 0,
                    left: 0,
                    width: '100%',
                    height: Math.max(
                            document.body.scrollHeight, document.documentElement.scrollHeight,
                            document.body.offsetHeight, document.documentElement.offsetHeight,
                            document.body.clientHeight, document.documentElement.clientHeight
                            ) + 'px'
                });
            }, 0);
        });
    }


    function throw_error(jqXHR, exception)
    {
        //$('#next').on('click touchstart');
        var msg = '';
        if (jqXHR.status === 0) {
            msg = 'Trying to establish network connection. Please wait.';
        } else if (jqXHR.status == 404) {
            msg = 'Requested page not found. [404]';
        } else if (jqXHR.status == 500) {
            msg = 'Internal Server Error [500].';
        } else if (exception === 'parsererror') {
            msg = 'Requested JSON parse failed.';
        } else if (exception === 'timeout') {
            msg = 'Time out error.';
        } else if (exception === 'abort') {
            msg = 'Ajax request aborted.';
        } else {
            msg = 'Uncaught Error.\n' + jqXHR.responseText;
        }
        $('#failure-msg').show();
        $("#next").prop('disabled', false);
        $('.loading').hide();
        $('.finish-btn').attr('disabled', false);
        $('#failure-msg').html(msg);

    }

    $(function () {

        $('input:radio').change(function () {
            // Only remove the class in the specific `box` that contains the radio
            $('div.ansImg').removeClass('selected');
            $(this).closest('div.ansImg').addClass('selected');

        });

        if (typeof history.pushState === "function") {

            history.pushState("catscmedt", null, null);

            window.onpopstate = function () {
                history.pushState('newcatscmedt', null, null);
                // Handle the back (or forward) buttons here
                // Will NOT handle refresh, use onbeforeunload for this.
                if (foo) {
                    alertify.confirm("<?php echo lang('app.language_warning_back_button_pressed'); ?>",
                            function () {
                                window.location = "<?php echo site_url('site/is-cat-available-for-me'); ?>";
                            },
                            function (e) {
                                localStorage.setItem('skip', 1);

                                $('#linearId').modal('show');

                            }).set('labels', {ok: '<?php echo lang('app.language_confirm_ok'); ?>', cancel: '<?php echo lang('app.language_confirm_cancel'); ?>'});
                    ;
                }

            };
        }
        else {

        }
        FastClick.attach(document.body);
        FastClick.attach(document.getElementById('linearId'));


        localStorage.setItem("attempts", 1);
        localStorage.setItem('skip', 0);
        alertify.defaults.glossary.title = "";
        alertify.defaults.transition = "slide";
        alertify.defaults.theme.ok = "btn btn-primary";
        alertify.defaults.theme.cancel = "btn btn-danger";
        alertify.defaults.theme.input = "form-control";


        foo = 0;
        //close attempt by user with close icon
        $('#linearId').on('hidden.bs.modal', function (e) {
            e.stopPropagation();
            e.preventDefault();
            if (foo) {
                alertify.confirm("<?php echo lang('app.language_confirm_message'); ?>",
                        function () {
                            window.location.reload();
                        },
                        function (e) {
                            localStorage.setItem('skip', 1);
                            $('#linearId').modal('show');

                        }).set('labels', {ok: '<?php echo lang('app.language_confirm_ok'); ?>', cancel: '<?php echo lang('app.language_confirm_cancel'); ?>'});
                ;
            } else {
                window.location.reload();
            }
        });



        $(document).on('focus', ':input', function () {
            $(this).attr('autocomplete', 'off');
            $(this).attr('autocorrect', 'off');
            $(this).attr('autocapitalize', 'off');
            $(this).attr('spellcheck', 'false');
        });


    });

</script>  
<script>
    $(function () {
  $('.simple:first').focus();
  $('.simple').on('keyup', function (e) {

            if (!e)
                var e = window.event; // some browsers don't pass e, so get it from the window
            if (e.keyCode)
                code = e.keyCode; // some browsers use e.keyCode
            else if (e.which)
                code = e.which;  // others use e.which

            if (code == 8 || code == 46 || code == 9 || code == 32) {
                return false;
            } else {
                $(this).next('.simple').focus();
            }

        });
 });
        
       

</script>
