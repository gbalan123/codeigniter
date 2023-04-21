<?php
use App\Models\Site\Bookingmodel;

$this->bookingmodel = new Bookingmodel();
?>
<div class="panel panel-default">
    <div class="panel-body">

        <?php
        $html_final = $html = $select_level = $not_eligible = $higher_final = '';
        $isCatsPrimaryAvailable = false;
        $isCatsCoreAvailable = false;
        foreach ($u13_products as $details):
            if ($details->audience == 'Primary' && $details->active == '1') {
                $select_level .= ' <option value="' . $details->id . '">' . $details->name . '</option> ';
            }
        endforeach;

        foreach ($u13_learners as $result):
            if(isset($result['final_test'])){
                if ($result['final_test'] != '' && $result['thirdparty_id'] != '') {
                    if ($result["product"] == 'cats_primary') {
                        $isCatsPrimaryAvailable = true;
						if(in_array(1,$institute_courseType)){
                        $next_level = '<td>
                                            <select class="form-control" id="cats_level" name="cats_level[]">
                                                <option value="">Please select</option>
                                                ' . $select_level . '
                                            </select>
    										<input type="hidden" name="learner_id[]" value="'.$result['id'].'">
                                        </td>';
						$eligibility = true;				
						}else{
							$not_eligible .= '<tr>
                               <td>' . $result['name'] . '</td>
                               <td>' . $result['username'] . '</td>
                            </tr>';
							$eligibility = false;
						}
                    } else {
                        $isCatsCoreAvailable = true;
						if(in_array(2,$institute_courseType)){
							if($result['product_id'] <= 9){
                                $delivery_type = $this->bookingmodel->get_delivery_type_by_thirdparty_id($result['thirdparty_id']);
                                if($delivery_type[0]->tds_option == 'catstds'){
                                    $next = @get_next_core_level($result['level'],False, False, $result['thirdparty_id']); 
                                }else{
                                    $next = @get_next_core_level($result['level'], $result['section_one'], $result['section_two'], $result['thirdparty_id']);  
                                } 
                                }else{
                                    $next = @get_next_higher_level($result['product_id']);
                                }
							if($next['product_id'] > 9 ){
								if(in_array(3,$institute_courseType)){
                                    if($next['product_id'] != 13 ){
                                        $next_level = '<td>' . $next['level'] .'
									    <input type="hidden" name="cats_level[]" value="'.$next['product_id'].'">
									    <input type="hidden" name="learner_id[]" value="'.$result['id'].'">
									    </td>';
									    $eligibility = true;
                                    }else{
                                        $higher_final .= '<tr>
                                        <td>' . $result['name'] . '</td>
                                        <td>' . $result['username'] . '</td>
                                        </tr>';
                                        $eligibility = false;
                                    }
									
								}else{
									$not_eligible .= '<tr>
									   <td>' . $result['name'] . '</td>
									   <td>' . $result['username'] . '</td>
									</tr>';
									$eligibility = false;
								}
							}else{
							$next_level = '<td>' . $next['level'] .'
							<input type="hidden" name="cats_level[]" value="'.$next['product_id'].'">
							<input type="hidden" name="learner_id[]" value="'.$result['id'].'">
							</td>';
							$eligibility = true;
							}
						}else{
							$not_eligible .= '<tr>
                               <td>' . $result['name'] . '</td>
                               <td>' . $result['username'] . '</td>
                            </tr>';
							$eligibility = false;
						}
                    }
                    
                    if($eligibility){
                           $html_final .= '<tr>
                                <td>' . $result['name'] . '</td>
                                <td>' . $result['username'] . '</td>
                                ' . $next_level . '
                            </tr>'; 
					}	
                }
            }
             else {
                $html .= '<tr>
                               <td>' . $result['name'] . '</td>
                               <td>' . $result['username'] . '</td>
                          </tr>';
            }
        endforeach;
        ?>

        <form action="<?php echo site_url('school/post_nextlevel'); ?>" class="form bv-form" role="form" id="u13_nextlevel_form"
	data-bv-feedbackicons-valid="glyphicon glyphicon-ok" data-bv-feedbackicons-invalid="glyphicon glyphicon-remove"
	data-bv-feedbackicons-validating="glyphicon glyphicon-refresh" enctype="multipart/form-data"
	method="post" accept-charset="utf-8">
        <p><?php echo lang('app.language_school_u13learner_next_level_infrm'); ?></p>
        <div class="table-responsive">
            <table  class="teacher-table table">
                <thead>
                <th><?php echo lang('app.language_school_teacher_label_name'); ?></th>
                <th><?php echo lang('app.language_tbl_label_username'); ?></th>
                <th><?php echo lang('app.language_school_u13learner_next_cats_level'); ?></th>
                </thead>
                <tbody>
                    <?php echo $html_final; ?>
                </tbody>
            </table>
        </div>

        <p><?php echo lang('app.language_school_u13learner_not_next_level'); ?></p>
        <div class="table-responsive">
            <table  class="teacher-table table">
                <thead>
                <th><?php echo lang('app.language_school_teacher_label_name'); ?></th>
                <th><?php echo lang('app.language_tbl_label_username'); ?></th>
                </thead>
                <tbody>
                    <?php echo $html; ?>
                </tbody>
            </table>
        </div>
		
		<p><?php echo 'The following learners could not be added because the institution eligibility is not available for the recommended course:' ?></p>
        <div class="table-responsive">
            <table  class="teacher-table table">
                <thead>
                <th><?php echo lang('app.language_school_teacher_label_name'); ?></th>
                <th><?php echo lang('app.language_tbl_label_username'); ?></th>
                </thead>
                <tbody>
                    <?php echo $not_eligible; ?>
                </tbody>
            </table>
        </div>
                
                <p><?php echo 'The following learners achieved the highest CATs Step level:' ?></p>
        <div class="table-responsive">
            <table  class="teacher-table table">
                <thead>
                <th><?php echo lang('app.language_school_teacher_label_name'); ?></th>
                <th><?php echo lang('app.language_tbl_label_username'); ?></th>
                </thead>
                <tbody>
                    <?php echo $higher_final; ?>
                </tbody>
            </table>
        </div>
		
        <div class="row">
            <div class="form-group col-xs-12 clearfix">
                <p style="width: 90%; text-align: justify; display: inline;"><?php echo lang('app.language_school_u13learner_policy') ?></p>
                <input type="checkbox" id="cats_data_protection_policy" class="" name="cats_data_protection_policy">
            </div>
        </div>
        <div class="form-group form-actions" style="clear:both;">
            <button id=""  data-dismiss="modal" class="btn btn-sm btn-continue">Cancel</button>
            <input type="hidden" name="submit_type"  id="submit_type" value=""/>
            <?php if($html_final){
                 $buttonEnabled = '';
                    $cursorValue = 'pointer';
                 }else{
                    $buttonEnabled = 'disabled';
                    $cursorValue = 'not-allowed';
                 }
            ?>
            <button  type="submit" style="pointer-events: all;cursor:<?php echo $cursorValue; ?>" id="submitBtn" <?php echo $buttonEnabled; ?> class="btn btn-sm btn-continue">Continue</button>
            <img alt="" id="loading_in" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>">
             
        </div>
        </form>
	</div>
</div>
<script>
$('#u13_nextlevel_form').submit(function (e) {
    $( ".help-block" ).empty();
	e.preventDefault();
	$('#submitBtn').attr('disabled', true);
	$('#loading_in').show();
	$.ajax({
		type: "POST",
		url: $(this).attr('action'),
		data: $(this).serialize(),
		dataType: 'json',
		success: function (data)
		{
			clear_errors(data);
			$('#loading_in').hide();
			if (data.success) {
				location.reload();
			} else {
				$('#submitBtn').attr('disabled', false);
				set_errors(data);
			}

		}
	});
	return false;
});

function clear_errors(data)
{
	if (typeof (data.errors) != "undefined" && data.errors !== null) {
		for (var k in data.errors) {

			$('#' + k).next('p').remove();

		}
	}
}

function set_errors(data)
{
	console.log(data);
	if (typeof (data.errors) != "undefined" && data.errors !== null) {
		for (var k in data.errors) {

			$(data.errors[k]).insertAfter($("#" + k)).css('color', 'red');

		}
	}
}
</script>