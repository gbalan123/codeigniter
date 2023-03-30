<div class="modal-content">
    <div class="modal-header modal_head_bg">
        <button type="button" class="close md_close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h3 class="modal-title rubric text-center">CATs Step placement test</h3>
    </div>
    <div class="modal-body md_bg">
        <div class="container-fluid" id="catsQuizEngine">
            <h1 class="text-center">Key : <?php echo $screen['questions'][0]['answerkey']; ?></h1>
           
            <div class="row mt100">
                <div class="col-sm-4 text-center">
                    <label class="option-circle">
                        <input type="radio" id="answer_<?php echo $screen['screenid']; ?>" name="answer_<?php echo $screen['screenid']; ?>" value="A">
                        <span class="checkmark a_ans_playback_<?php echo $screen['screenid']; ?>">A</span>
                    </label></div>   
                <div class="col-sm-4 text-center">
                    <label class="option-circle">
                        <input type="radio" id="answer_<?php echo $screen['screenid']; ?>" name="answer_<?php echo $screen['screenid']; ?>" value="B">
                        <span class="checkmark b_ans_playback_<?php echo $screen['screenid']; ?>">B</span>
                    </label></div>
                <div class="col-sm-4 text-center">
                    <label class="option-circle">
                        <input type="radio" id="answer_<?php echo $screen['screenid']; ?>" name="answer_<?php echo $screen['screenid']; ?>" value="C">
                        <span class="checkmark c_ans_playback_<?php echo $screen['screenid']; ?>">C</span>
                    </label></div>                          
            </div>

        </div>
    </div>
</div>
