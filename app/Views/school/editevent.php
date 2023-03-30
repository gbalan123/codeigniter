<?php 
     $this->session = \Config\Services::session();
     $this->lang = new \Config\MY_Lang(); 
?>
<style>
    #event_edit_form p {
        color : red;
    }
    .form-actions {
        margin: 0;
        background-color: transparent;
        text-align: center;
    }
    .has-feedback label~.form-control-feedback {
        top: 0px;
    }
    .has-feedback .form-control {

    }
    #event_add_form .selectContainer .form-control-feedback {
        /* Adjust feedback icon position */
        right: -15px;
    }
    ul.products_edit{
        display:none;
        list-style: none;
        padding: 0 0 0 30px;
        margin: 0;
    }
    .plus_edit > input[type="radio"]:checked + ul.products_edit{
        display:block;
    }

    ul.products_edit > li>label>input{
        margin-right: 5px;
    }
    .show_hide_edit{
        width:15px;
        height:15px;
        display: inline-block;
        background-color: whitesmoke;
        border: 1px solid #cacaca;
        position:relative;
        top: 3px;
        margin-left: 5px;
    }
    .show_hide_edit:after{
        position: absolute;
        color: #424242;
        content: '+';
        line-height: 12px;
        text-align: center;
        width: 100%;
        height: 100%;
        font-weight: bold;
        font-size: 14px;
    }
    .plus_edit > input[type="radio"]:checked + .show_hide_edit:after{
        content: '-';
    }
    .test_field{
        width:91%;
    }
    .test_field input[type="text"]{
        padding-right:10px;
    }
    .test_field > div{
        padding-right:0;
    }
    .capacity_adder{
        padding-right:0 !important;
    }
     textarea {
   resize: none;
}
</style>
<?php if ($this->session->setFlashdata('error')) { ?> 
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <?php echo $this->session->setFlashdata('error'); ?>
    </div>
<?php } ?>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-plus fa-fw"></i><?php echo lang('app.language_school_event_update_test_title'); ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">
                        <?php echo form_open_multipart('school/editevent', array('class' => 'form bv-form', 'role' => 'form', 'id' => 'event_edit_form')); ?>
                        <label for="productGroup">
                            <?php echo lang('app.language_school_event_product_test_event'); ?> <span>*</span>
                        </label>
                        <div class="" style="margin-left:20px;">
                            <?php
                            $edit_product_name = array_map("current", $product_group_name);
                            $eligible_version = array_map("current", $eligible_version_products);
                            $css_edit = "pointer-events: none;  color: #B8B8B8;";
                            ?>
                            <?php foreach ($product_by_groups as $group_name => $group_value): ?>
                                <div class="product_group">
                                    <label class="plus_edit" style="<?php echo (isset($edit_product_name) && in_array($group_name, $edit_product_name) ? '' : $css_edit); ?>">
                                        <input type="radio" name="product_group_name" value="<?php echo $group_name ?>" <?php echo (isset($edit_product_name) && in_array($group_name, $edit_product_name) ? 'checked' : ''); ?>> <?php echo $group_name ?> <span class="show_hide_edit"></span>
                                    </label>

                                    <ul class="products_edit" id="<?php echo $group_name ?>_edit">
                                        <?php foreach ($group_value as $val => $val_name): ?>

                                            <li>
                                                <label>
                                                    <?php if ($group_name == "Primary") { ?>
                                                        <input type="checkbox" name="product_group_values[]" value= "<?php echo $val; ?>"<?php echo (isset($product_ids) && in_array($val, $product_ids)) ? "checked Disabled" : ''; ?> <?php echo (isset($eligible_version) && in_array($val, $eligible_version) ? '' : 'disabled'); ?>><?php echo $val_name; ?></input>
                                                        <?php if (isset($product_ids) && in_array($val, $product_ids)) { ?>
                                                            <input name="product_group_values[]" type="hidden" value="<?php echo $val; ?>"/>
                                                        <?php } ?>
                                                    <?php } ?>

                                                    <?php if ($group_name == "Core") { ?>
                                                        <input type="checkbox" name="product_group_values[]" value= "<?php echo $val; ?>"<?php echo (isset($product_ids) && in_array($val, $product_ids)) ? "checked Disabled" : ''; ?> <?php echo (isset($eligible_version) && in_array($val, $eligible_version) ? '' : 'disabled'); ?>><?php echo $val_name; ?></input>
                                                        <?php if (isset($product_ids) && in_array($val, $product_ids)) { ?>
                                                            <input name="product_group_values[]" type="hidden" value="<?php echo $val; ?>"/>
                                                        <?php } ?>
                                                    <?php } ?>  

                                                    <?php if ($group_name == "Higher") { ?>
                                                        <input type="checkbox" name="product_group_values[]" value= "<?php echo $val; ?>"<?php echo (isset($product_ids) && in_array($val, $product_ids)) ? "checked Disabled" : ''; ?> <?php echo (isset($eligible_version) && in_array($val, $eligible_version) ? '' : 'disabled'); ?>><?php echo $val_name; ?></input>
                                                        <?php if (isset($product_ids) && in_array($val, $product_ids)) { ?>
                                                            <input name="product_group_values[]" type="hidden" value="<?php echo $val; ?>"/>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </label>

                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endforeach; ?>

                            <p id="product_group_values">
                        </div>	
                        <div class="row test_field">
                        <div class="col-sm-4">
                            <label for="capacity"><?php echo lang('app.language_school_event_test_date'); ?> <span>*</span></label>						
                            <div class='input-group date' id='datetimepicker1edit'>
                                <input type="text" class="form-control" name="testdate" id="testdate_eidt" maxlength="10" value="<?php echo set_value('testdate'); ?>" />
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                            <p id="testdate"></p>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="capacity" ><?php echo lang('app.language_school_event_start_time'); ?> <span>*</span></label>						
                            <div class='input-group date' id='datetimepicker3edit'>
                                <?php
                                $starttime_edit = $results['start_time'];
                                ?>
                                <input type="text" class="form-control" name="starttime" id="starttime_edit" value="<?php echo $starttime_edit; ?>" required/>

                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                        </div>						
                        <div class="form-group col-sm-4">
                            <label for="capacity" ><?php echo lang('app.language_school_event_end_time'); ?></label>						
                            <div class='input-group date' id='datetimepicker4edit'>
                                <?php
                                $endtime_edit = $results['end_time'];
                                ?>
                                <input type="text" class="form-control" name="endtime1" id="endtime1edit" value="<?php echo set_value('endtime'); ?>" disabled="disable" placeholder="<?php echo $endtime_edit; ?>" required/>	
                                <input type="hidden" class="form-control" name="endtime" id="endtime_edit" value="<?php echo $endtime_edit; ?>"/>	
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                        </div>	
                        </div>
                        <div class="form-group">
                            <label for="distributor"><?php echo lang('app.language_school_venue'); ?> <span>*</span></label>
                           
                            <?php
                            $slicevenue = array();
                            foreach($venues as $skey => $slice)
                            {
                                
                                $slicevenue[$skey] = strlen($slice) > 50 ? substr($slice,0,50)."..." : $slice;
                                
                            }
                            echo form_dropdown('venue_id', $slicevenue, set_value('venue_id', isset($results['venue_id']) ? $results['venue_id'] : ''), 'class="form-control" id="venue_id"');
                            ?>
                        </div>
                        <div class="">
                            <div id='fixed_capacity'>
                            <label for="capacity"><?php echo lang('app.language_school_event_capacity'); ?> <span>*</span></label>					
                            <input type="number" min="1" step="1" class="form-control capacity_adder" name="capacity" id="capacity" value="<?php echo set_value('capacity', isset($results['fixed_capacity']) ? $results['fixed_capacity'] : ''); ?>"  required/>
                            <p id="capacity"></p>
                            </div>
                        </div>
                        <input type="hidden" class="form-control" id="eventid" name="eventid" value="<?php if (!empty($results['id'])) echo $results['id']; ?>"  />
                        <div class="form-group ">
                            <label for="notes"><?php echo lang('app.language_school_event_notes'); ?></label>							
                            <!--<input type="text" class="form-control" name="notes" id="notes"  value="<?php //echo set_value('notes', isset($results['notes']) ? $results['notes'] : ''); ?>" />-->
                            <textarea class="form-control" name="notes" id="notes" rows="2" cols="10" ><?php echo set_value('notes', isset($results['notes']) ? $results['notes'] : ''); ?></textarea>							
                        </div>	
                        <button type="button" class="btn btn-sm btn-continue pull-right" data-dismiss="modal">Cancel</button>
                        <?php if($event_over == 0){ ?>
                        <button type="submit" id="submitBtn" class="btn btn-sm btn-continue pull-right"><?php echo lang('app.language_school_submit'); ?></button>
                        <?php } ?>
                        
						<?php form_close(); ?>

                    </div>

                </div>
                <!-- /.row (nested) -->
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->


<script type="text/javascript">
    $(function () {
        $("#testdate_eidt").keypress(function (event) {
            event.preventDefault();
        });
        $("#endtime_edit").keypress(function (event) {
            event.preventDefault();
        });
        $("#starttime_edit").keypress(function (event) {
            event.preventDefault();
        });

        // current date + 1hrs ->tds	
        var sevendays = moment().add(1, 'hours').format("MM/DD/YYYY"); 
        // current date + 1 year
        var max = moment().add(90, 'd').format("MM/DD/YYYY");


        var dateString = moment.unix("<?php echo $results['date']; ?>").format("MM/DD/YYYY");
        var start_time = moment.unix("<?php echo $results['date']; ?>").format("MM/DD/YYYY <?php echo $results['start_time']; ?>");
        var end_time = moment.unix("<?php echo $results['date']; ?>").format("MM/DD/YYYY <?php echo $results['end_time']; ?>");
        $('#datetimepicker1edit').datetimepicker({
            format: 'L',
            defaultDate: dateString,
        });

        $("#datetimepicker1edit").on("dp.change dp.show", function (e) {
            $('#event_edit_form').bootstrapValidator('revalidateField', 'testdate');
            $('#event_add_form').bootstrapValidator('revalidateField', 'starttime');	 
            $('#datetimepicker1edit').data("DateTimePicker").maxDate(max);
            $('#datetimepicker1edit').data("DateTimePicker").minDate(sevendays);
        });

        $('#datetimepicker3edit').datetimepicker({
            //format: 'LT',
            format: 'HH:mm',
            defaultDate: start_time,
        });

        $("#datetimepicker3edit").on("dp.change dp.show", function (e) {
            $('#event_edit_form').bootstrapValidator('revalidateField', 'starttime');
            $.ajax({
                url: '<?php echo site_url('school/js_endtime_set'); ?>',
                type: 'POST',
                dataType: "json",
                data: {starttime: ($('#starttime_edit').val() != '') ? $('#starttime_edit').val() : ''},
                success: function (result) {
                    $('#endtime_edit, #endtime1edit').val(result.endtime);
                }
            });

        });
        
        //event add form validation
        $('#event_edit_form').bootstrapValidator({
            locale: "<?php echo $this->lang->lang(); ?>",
            // List of fields and their validation rules
            fields: {
                'product_group_name': {
                    validators: {
                        choice: {
                            min: 1,
                            message: '<?php echo lang('app.language_school_event_please_choose_event_group'); ?>'
                        }
                    }
                },
                'product_group_values[]': {
                    validators: {
                        choice: {
                            min: 1,
                            message: '<?php echo lang('app.language_school_event_please_choose_event'); ?>'
                        }
                    }
                },
                venue_id: {
                    validators: {
                        notEmpty: {
                            message: '<?php echo lang('app.language_school_event_please_choose_venue'); ?>'
                        }
                    }
                },
                testdate: {
                    validators: {
                        notEmpty: {
                        }
                    }
                },
                starttime: {
                    validators: {
                        notEmpty: {
                        },
                        remote: {
                            url: '<?php echo site_url('school/js_end_time_lesser'); ?>',
                            type: 'POST',
                            data: function (validator, $field, value) {
                                // Return an object
                                return {
                                    'testdate': ($('#testdate_eidt').val() != '') ? $('#testdate_eidt').val() : '',
                                    'starttime': ($('#starttime_edit').val() != '') ? $('#starttime_edit').val() : '',
                                    'endtime': ($('#endtime_edit').val() != '') ? $('#endtime_edit').val() : '',
                                };
                            }
                        }
                    }
                },
                notes: {
                    validators: {
                        stringLength: {
                            max: 500,
                        }
                    }
                }

            },
            onSuccess: function (e, data) {
                e.preventDefault();
                $('#preview-btn').attr("disabled", "disabled");
                $( ".help-block" ).empty();
                var $form = $(e.target);
                $.ajax({
                    url: '<?php echo site_url('school/js_starttime_check'); ?>',
                    type: 'POST',
                    dataType: "json",
                    data: $form.serialize(),
                    success: function (result) {
                        if (result.valid == false) {
                             alertify.alert('<?php echo lang('app.language_school_event_cannot_be_added'); ?>');
                        } else {
                            $.ajax({
                                type: "POST",
                                url: $('#event_edit_form').attr('action'),
                                data: $('#event_edit_form').serialize(),
                                dataType: 'json',
                                success: function (data)
                                { clear_errors_edit(data);
                                    $('.loading_main').hide();
                                    if (data.success) {
                                        location.reload();
                                    }else{
                                          setedit_errors(data);
                                }

                                }
                            });
                            return false;
                        }
                    }
                });
            }

        });
        
        function clear_errors_edit(data)
                    {
                        if (typeof (data.errors) != "undefined" && data.errors !== null) {
                            for (var k in data.errors) {

                                $('#' + k).next('p').remove();

                            }
                        }
                    }
        
        function setedit_errors(data)
                    {
                        if (typeof (data.errors) != "undefined" && data.errors !== null) {
                            for (var k in data.errors) {
                                $(data.errors[k]).insertAfter($("#" + k)).css('color', '#a94442');
                            }
                        }
                    }

        var selectedoption = $('#event_edit_form .plus_edit > input[name="product_group_name"]:checked ').val();
        $("#" + selectedoption + "_edit").css({"display": "block"});
        $(".plus_edit").on('click', function () {
            $(".products_edit").css({"display": "none"});
            var product = $(this).children('input[type="radio"]').val();
            $("#" + product + "_edit").css({"display": "block"});
        });
    });
</script>


