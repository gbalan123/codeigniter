<?php $image_efs = $linear_path == "efs_linear_path" ?  "image_efs" :  "image_efs_preview";?>
<div class="modal-content">
    <div class="modal-header modal_head_bg">
        <button type="button" class="close md_close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h3 class="modal-title rubric text-center">CATs Step placement test</h3>
    </div>
    <div class="modal-body md_bg">
        <div class="container-fluid" id="catsQuizEngine">
            <h1 class="text-center">Key : <?php echo $screen['questions'][0]['answerkey']; ?></h1>
            <div class="row">
                <div class="col-sm-12 qus_block text-center">
                    <h3><?php echo $screen['questions'][0]['question']; ?></h3>
                </div>
            </div>
            <div class="row mt20">
                <div class="col-sm-12 text-center">
                    <div class="question_img p1627">
                        <img src="data:image/svg+xml;base64,<?php echo @$image_efs(str_replace(".svg","",$screen['image'])); ?>" alt="1627"/>
                    </div></div>
            </div>
            <div class="row mt50">
                <div class="col-sm-offset-4 col-sm-4">
                    <div class="">
                        <label class="check_btn-yes">
                            <input type="radio" id="<?php echo 'answer_' . $screen['screenid']; ?>" name="<?php echo 'answer_' . $screen['questions'][0]['questionno'] . '_' . $screen['screenid']; ?>"  value="YES">
                            <span class="btn-yes"><img src="<?php echo base_url('public/images/tick.png'); ?>" alt="tick"></span>
                        </label>
                        <label class="check_btn-no">
                            <input type="radio" id="<?php echo 'answer_' . $screen['screenid']; ?>" name="<?php echo 'answer_' . $screen['questions'][0]['questionno'] . '_' . $screen['screenid']; ?>"  value="NO">
                            <span class="btn-no"><img src="<?php echo base_url('public/images/cross.png'); ?>" alt="cross"></span>
                        </label>
                    </div>
                </div>
            </div>       
        </div>
    </div>
</div>
