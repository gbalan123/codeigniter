<?php
if (!function_exists('set_select_box_1029')) {
    function set_select_box_1029($missing_answer,$screenid = false) {
    //$pattern = "/\[(.*?)\]/si";
        $replaceString = array('[' => '', ']' => '');
        $pattern = "/\[(.*?)\]/si";

        preg_match_all($pattern, $missing_answer, $matches);
        //$explodeArr = explode('/',$matches[0]);
        $replaceString = array('[' => '', ']' => '');
        $plselect = array(' ' => '');

        $attr = array('id' => 'answer_'  . $screenid, 'autocomplete' =>"off");

        foreach ($matches[0] as $key => $value) {

            $explodeArr[$matches[0][$key]] = explode('/', str_replace(array_keys($replaceString), $replaceString, trim($matches[0][$key])));
            $form_options[$matches[0][$key]] = '<span class="form-group" style="color:black;">' . form_dropdown('answer_' . $screenid, array_merge($plselect, array_combine(array_map('trim', $explodeArr[$matches[0][$key]]), array_map('trim', $explodeArr[$matches[0][$key]]))), '', $attr) . "</span>";
        }

        return $form_options;
    }
}
?>
<div class="modal-content">
    <div class="modal-header modal_head_bg">
        <button type="button" class="close md_close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h3 class="modal-title rubric text-center">CATs Step placement test</h3>
    </div>
    <div class="modal-body md_bg">
        <div class="container-fluid" id="catsQuizEngine">
            <h1 class="text-center">Key : <?php echo $screen['questions'][0]['answerkey']; ?></h1>
            <div class="row mt50">
                <div class="col-sm-12">
                    <div class="sys_qus_block text-center">
                        <?php
                        $question['missinganswer'] = $screen['questions'][0]['missinganswer'];
                        $screenid = $screen['screenid']
                        ?>
                        <ol>
                            <li style="list-style: none;"><p class="question" style="text-align: left;"><?php echo str_replace(array_keys(set_select_box_1029($question['missinganswer'])), set_select_box_1029($question['missinganswer'], $screenid), $question['missinganswer']); ?></p></li>
                        </ol>
                    </div>
                </div>
            </div> 
        </div>
    </div>
</div>
