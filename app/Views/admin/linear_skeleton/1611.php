<?php $image_efs = $linear_path == "efs_linear_path" ?  "image_efs" :  "image_efs_preview";?>
<div class="modal-content">
    <div class="modal-header modal_head_bg">
        <button type="button" class="close md_close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h3 class="modal-title rubric text-center">CATs Step placement test</h3>
    </div>
    <div class="modal-body md_bg">
        <div class="container-fluid" id="catsQuizEngine">
            <div class="row mt100"><h1 class="text-center">Key : <?php echo $screen['questions'][0]['answerkey']; ?></h1>
                <div class="col-sm-4">
                    <label class="option">
                        <input type="radio" id="answer_<?php echo $screen['screenid']; ?>" name="answer_<?php echo $screen['screenid']; ?>" value="A">
                        <span class="checkmark_rec p1611"><img src="data:image/svg+xml;base64,<?php echo @$image_efs(str_replace(".svg","",$screen['questions'][0]['answers']['A'])); ?>" alt="opt-1"/></span>
                    </label></div>   
                <div class="col-sm-4">
                    <label class="option">
                        <input type="radio" id="answer_<?php echo $screen['screenid']; ?>" name="answer_<?php echo $screen['screenid']; ?>" value="B">
                        <span class="checkmark_rec p1611"><img src="data:image/svg+xml;base64,<?php echo @$image_efs(str_replace(".svg","",$screen['questions'][0]['answers']['B'])); ?>" alt="opt-2"/></span>
                    </label></div>
                <div class="col-sm-4">
                    <label class="option">
                        <input type="radio" id="answer_<?php echo $screen['screenid']; ?>" name="answer_<?php echo $screen['screenid']; ?>" value="C">
                        <span class="checkmark_rec p1611"><img src="data:image/svg+xml;base64,<?php echo @$image_efs(str_replace(".svg","",$screen['questions'][0]['answers']['C'])); ?>" alt="opt-3"/></span>
                    </label></div>                          
            </div>
             
        </div>
    </div>
</div>
