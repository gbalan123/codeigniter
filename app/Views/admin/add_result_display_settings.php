<style>
     p {
        color: red;
    }
</style>
<div class="row">
	<div class="col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading" style="overflow:hidden">
				<div class="pull-left">
                <?= esc($admin_heading) ?>
				</div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12">
                        <?php echo form_open('admin/add_result_display_settings', array('class' => 'form', 'role' => 'form', 'id' => 'result_display_settings')); ?>
                        <div class="form-group">
                            <label for="name">A(lower threshold) &lt; or =: <span>*</span></label> 
                            <input type="text" class="form-control numeric" name="lower_threshold" id="add_lower_threshold" value="<?php echo isset($settings['lower_threshold']) ? $settings['lower_threshold'] : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="name">B(passing threshold) &gt; or =: <span>*</span></label> 
                            <input type="text" class="form-control numeric" name="passing_threshold" id="add_passing_threshold" value="<?php echo isset($settings['passing_threshold']) ? $settings['passing_threshold'] : ''; ?>" required>
                        </div>
                        <legend>Base score levels</legend>
                        <?php $logit_values = isset($settings['logit_values']) ? unserialize($settings['logit_values']) : ''; ?>
                        <div class="col-xs-12 col-sm-6 col-lg-3">
                        <div class="form-group">
                            <label for="A1.1" class="">A1.1 <span>*</span></label>
                            <div class="">
                                <input type="text" class="form-control numeric" name="A1_1" placeholder="A1.1" value="<?php echo isset($logit_values['A1.1']) ? $logit_values['A1.1'] : ''; ?>" required>
                            </div>
                            <label for="A1.2" class="">A1.2 <span>*</span></label>
                            <div class="">
                                <input type="text" class="form-control numeric" name="A1_2" placeholder="A1.2" value="<?php echo isset($logit_values['A1.2']) ? $logit_values['A1.2']: ''; ?>" required>
                            </div>
                            <label for="A1.3" class="">A1.3 <span>*</span></label>
                            <div class="">
                                <input type="text" class="form-control numeric" name="A1_3" placeholder="A1.3" value="<?php echo isset($logit_values['A1.3']) ? $logit_values['A1.3'] : ''; ?>" required>
                            </div>
                        </div>
                                </div>
                            <div class="col-xs-12 col-sm-6 col-lg-3">
                        <div class="form-group">
                            <label for="A2.1" class="">A2.1 <span>*</span></label>
                            <div class="">
                                <input type="text" class="form-control numeric" name="A2_1" placeholder="A2.1" value="<?php echo isset($logit_values['A2.1']) ? $logit_values['A2.1'] : ''; ?>" required>
                            </div>
                            <label for="A2.2" class="">A2.2 <span>*</span></label>
                            <div class="">
                                <input type="text" class="form-control numeric" name="A2_2" placeholder="A2.2" value="<?php echo isset($logit_values['A2.2']) ? $logit_values['A2.2'] : ''; ?>" required>
                            </div>
                            <label for="A2.3" class="">A2.3 <span>*</span></label>
                            <div class="">
                                <input type="text" class="form-control numeric" name="A2_3" placeholder="A2.3" value="<?php echo isset($logit_values['A2.3']) ? $logit_values['A2.3'] : ''; ?>" required>
                            </div>
                        </div>
                                </div>
                        <div class="col-xs-12 col-sm-6 col-lg-3">
                        <div class="form-group">
                            <label for="B1.1" class="">B1.1 <span>*</span></label>
                            <div class="">
                                <input type="text" class="form-control numeric" name="B1_1" placeholder="B1.1" value="<?php echo isset($logit_values['B1.1']) ? $logit_values['B1.1'] : ''; ?>" required>
                            </div>
                            <label for="B1.2" class="">B1.2 <span>*</span></label>
                            <div class="">
                                <input type="text" class="form-control numeric" name="B1_2" placeholder="B1.2" value="<?php echo isset($logit_values['B1.2']) ? $logit_values['B1.2'] : ''; ?>" required>
                            </div>
                            <label for="B1.3" class="">B1.3 <span>*</span></label>
                            <div class="">
                                <input type="text" class="form-control numeric" name="B1_3" placeholder="B1.3" value="<?php echo isset($logit_values['B1.3']) ? $logit_values['B1.3'] : ''; ?>" required>
                            </div>
                        </div>
                            </div>
                        <div class="col-xs-12 col-sm-6 col-lg-3">
                        <div class="form-group">
                            <label for="B1.1" class="">B2.1 <span>*</span></label>
                            <div class="">
                                <input type="text" class="form-control numeric" name="B2_1" placeholder="B2.1" value="<?php echo isset($logit_values['B2.1']) ? $logit_values['B2.1'] : ''; ?>" required>
                            </div>
                            <label for="B1.2" class="">B2.2 <span>*</span></label>
                            <div class="">
                                <input type="text" class="form-control numeric" name="B2_2" placeholder="B2.2" value="<?php echo isset($logit_values['B2.2']) ? $logit_values['B2.2'] : ''; ?>" required>
                            </div>
                            <label for="B1.3" class="">B2.3 <span>*</span></label>
                            <div class="">
                                <input type="text" class="form-control numeric" name="B2_3" placeholder="B2.3" value="<?php echo isset($logit_values['B2.3']) ? $logit_values['B2.3'] : ''; ?>" required>
                            </div>
                        </div>
                            </div>							

                        <div class="form-group">
                            <label for="name"><?php echo lang('app.language_admin_result_display_reason'); ?> <span>*</span></label> 
                            <textarea class="form-control" name="message" id ="add_message" rows="2" cols="20" required></textarea>
                            <span id="remain">500</span> characters remaining
						</div>
                        <div class="form-group pull-right">
                            <button type="submit" id="submitBtn" class="btn btn-primary">Submit</button>
                        </div>
                        <?php form_close(); ?>
                    </div>
                </div>
			</div>
		</div>
	</div>
</div>

<script>
    $('#result_display_settings').submit(function (e) {
        e.preventDefault();
        $('#submitBtn').attr('disabled', true);
        $.ajax({
            type: "POST",
            url: $(this).attr('action'),
            data: $(this).serialize(),
            dataType: 'json',
            success: function (data)
            {
                $('#submitBtn').attr('disabled', false);
                clear_errors(data);
                if (data.success) {
                    location.reload();
                }else {
                    set_errors(data);
                }
            }
        });
        return false;
    });

    function clear_errors(data){
        if (typeof (data.errors) != "undefined" && data.errors !== null) {
            for (var k in data.errors) {
                $('#' + k).next('p').remove();
            }
        }
    }

    function set_errors(data){
        if (typeof (data.errors) != "undefined" && data.errors !== null) {
            for (var k in data.errors) {
                $(data.errors[k]).insertAfter($("#" + k));
            }
        }
    }

    $('.numeric').on('input', function (event) { 
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    var maxchars = 500;
    $('textarea').keyup(function () {
        var tlength = $(this).val().length;
        $(this).val($(this).val().substring(0, maxchars));
        var tlength = $(this).val().length;
        remain = maxchars - parseInt(tlength);
        $('#remain').text(remain);
    });
</script>