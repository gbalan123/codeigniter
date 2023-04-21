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
                    <span id="show_error"></span>
                    <div class="col-xs-12">
                        <?php echo form_open('admin/add_primary_placement_configs', array('class' => 'form', 'role' => 'form', 'id' => 'add_primary_placement_configs')); ?>
                            <?php @$logit_values = unserialize($settings['logit_values']); ?>
                            <div class="col-xs-12 col-sm-4">
                                <div class="form-group">
                                    <label for="A0.1" class="">Primary Step Into 1 <?php echo ($products_primary[0]->active == 0 ) ? '' : '<span>*</span>';?></label>
                                    <div class="">
                                        <input type="number" class="form-control" name="A0_1" placeholder="A0.1" value="<?php echo @$logit_values['A0_1']; ?>" <?php echo ($products_primary[0]->active == 0 ) ? 'disabled' : '';?> <?php echo ($products_primary[0]->active == 0 ) ? '' : 'required';?>>
                                    </div>
                                    <label for="A0.2" class="">Primary Step Into 2 <?php echo ($products_primary[1]->active == 0 ) ? '' : '<span>*</span>';?></label>
                                    <div class="">
                                        <input type="number" class="form-control" name="A0_2" placeholder="A0.2" value="<?php echo @$logit_values['A0_2']; ?>" <?php echo ($products_primary[1]->active == 0 ) ? 'disabled' : '';?> <?php echo ($products_primary[1]->active == 0 ) ? '' : 'required';?>>
                                    </div>
                                    <label for="A0.3" class="">Primary Step Into 3 <?php echo ($products_primary[2]->active == 0 ) ? '' : '<span>*</span>';?></label>
                                    <div class="">
                                        <input type="number" class="form-control" name="A0_3" placeholder="A0.3" value="<?php echo @$logit_values['A0_3']; ?>" <?php echo ($products_primary[2]->active == 0 ) ? 'disabled' : '';?> <?php echo ($products_primary[2]->active == 0 ) ? '' : 'required';?>>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-4">
                                <div class="form-group">
                                    <label for="A1.1" class="">Primary Step Forward 1 <?php echo ($products_primary[3]->active == 0 ) ? '' : '<span>*</span>';?></label>
                                    <div class="">
                                        <input type="number" class="form-control" name="A1_1" placeholder="A1.1" value="<?php echo @$logit_values['A1_1']; ?>" <?php echo ($products_primary[3]->active == 0 ) ? 'disabled' : '';?> <?php echo ($products_primary[3]->active == 0 ) ? '' : 'required';?> >
                                    </div>
                                    <label for="A1.2" class="">Primary Step Forward 2 <?php echo ($products_primary[4]->active == 0 ) ? '' : '<span>*</span>';?></label>
                                    <div class="">
                                        <input type="number" class="form-control" name="A1_2" placeholder="A1.2" value="<?php echo @$logit_values['A1_2']; ?>" <?php echo ($products_primary[4]->active == 0 ) ? 'disabled' : '';?> <?php echo ($products_primary[4]->active == 0 ) ? '' : 'required';?>>
                                    </div>
                                    <label for="A1.3" class="">Primary Step Forward 3 <?php echo ($products_primary[5]->active == 0 ) ? '' : '<span>*</span>';?></label>
                                    <div class="">
                                        <input type="number" class="form-control" name="A1_3" placeholder="A1.3" value="<?php echo @$logit_values['A1_3']; ?>" <?php echo ($products_primary[5]->active == 0 ) ? 'disabled' : '';?> <?php echo ($products_primary[5]->active == 0 ) ? '' : 'required';?>>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-4">
                                <div class="form-group">
                                    <label for="A2.1" class="">Primary Step Up 1 <?php echo ($products_primary[6]->active == 0 ) ? '' : '<span>*</span>';?></label>
                                    <div class="">
                                        <input type="number" class="form-control" name="A2_1" placeholder="A2.1" value="<?php echo @$logit_values['A2_1']; ?>" <?php echo ($products_primary[6]->active == 0 ) ? 'disabled' : '';?> <?php echo ($products_primary[6]->active == 0 ) ? '' : 'required';?>>
                                    </div>
                                    <label for="A2.2" class="">Primary Step Up 2 <?php echo ($products_primary[7]->active == 0 ) ? '' : '<span>*</span>';?></label>
                                    <div class="">
                                        <input type="number" class="form-control" name="A2_2" placeholder="A2.2" value="<?php echo @$logit_values['A2_2']; ?>" <?php echo ($products_primary[7]->active == 0 ) ? 'disabled' : '';?> <?php echo ($products_primary[7]->active == 0 ) ? '' : 'required';?>>
                                    </div>
                                    <label for="A2.3" class="">Primary Step Up 3 <?php echo ($products_primary[8]->active == 0 ) ? '' : '<span>*</span>';?></label>
                                    <div class="">
                                        <input type="number" class="form-control" name="A2_3" placeholder="A2.3" value="<?php echo @$logit_values['A2_3']; ?>" <?php echo ($products_primary[8]->active == 0 ) ? 'disabled' : '';?> <?php echo ($products_primary[8]->active == 0 ) ? '' : 'required';?>>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="name"><?php echo lang('app.language_admin_result_display_reason'); ?> <span>*</span></label> 
                                <textarea class="form-control" name="message" id ="add_message" rows="2" cols="20" required></textarea>
                                <span id="remain">500</span> characters remaining
                            </div>
                            
                            <div class="form-group col-xs-12">
                                <button type="submit" class="btn btn-primary pull-right">Submit</button>
                            </div>
                        <?php form_close(); ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
     $('#add_primary_placement_configs').submit(function (e) {
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
                $('#show_error').html('');
                if (data.success) {
                    location.reload();
                }else {
                    $('#show_error').html('<div class="alert alert-danger" role="alert">'+data.errors+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
                }
            }
        });
        return false;
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