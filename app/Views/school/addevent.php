<?php 
     $this->session = \Config\Services::session();
     $this->lang = new \Config\MY_Lang(); 
?>
<style>
    #plus{

        text-decoration:none;
        background:#000;
        color:#fff;
        display:inline-block;
        padding:1px;
    }
    #event_add_form p {
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

    ul.products{
        display:none;
        list-style: none;
        padding: 0 0 0 30px;
        margin: 0;
    }
    ul.products > li>label>input{
        margin-right: 5px;
    }
    .show_hide{
        width:15px;
        height:15px;
        display: inline-block;
        background-color: whitesmoke;
        border: 1px solid #cacaca;
        position:relative;
        top: 3px;
        margin-left: 5px;
    }
    .show_hide:after{
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
    .plus > input[type="radio"]:checked + .show_hide:after{
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
    .allocation{
        display: none;
    }
</style>

<?php if ($this->session->getFlashdata('message')) { ?> 
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <?php echo $this->session->getFlashdata('message'); ?>
    </div>
<?php } ?>
<?php if ($this->session->getFlashdata('error')) { ?> 
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <?php echo $this->session->getFlashdata('error'); ?>
    </div>
<?php } ?>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <em class="fa fa-plus fa-fw"></em><?php echo lang('app.language_school_event_add_test_title'); ?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="alert alert-info allocation"><?php echo lang('app.language_school_event_allocation_contact_admin'); ?></div>
                        <?php echo form_open_multipart('school/addevent', array('class' => 'form bv-form', 'role' => 'form', 'id' => 'event_add_form')); ?>
                        <label for="productGroup">
                            <?php echo lang('app.language_school_event_product_test_event'); ?> <span>*</span>
                        </label>
                        <div class="form-group" style="margin-left:20px;">
                            <?php
                            $eligible_name = array_map("current", $eligible_product_name);
                            $eligible_version = array_map("current", $eligible_version_products);
                            $css = "pointer-events: none;  color: #B8B8B8;";
                            ?>
                            <?php foreach ($product_by_groups as $group_name => $group_value): ?>
                                <div class="product_group">
                                    <label class="plus" style="<?php echo (isset($eligible_name) && in_array($group_name, $eligible_name) ? '' : $css); ?>">
                                        <input type="radio" name="product_group_name" value="<?php echo $group_name ?>"> <?php echo $group_name ?> <span class="show_hide" ></span>
                                    </label>

                                    <ul class="products" id="<?php echo $group_name ?>">
                                        <?php foreach ($group_value as $val => $val_name): ?>

                                            <li>
                                                <label>
                                                    <?php if ($group_name == "Primary") { ?>
                                                        <strong style ="<?php echo (isset($eligible_version) && in_array($val, $eligible_version) ? '' : $css); ?>"> <input type="checkbox" name="product_group_values[]" value= "<?php echo $val; ?>" <?php echo (isset($eligible_version) && in_array($val, $eligible_version) ? '' : 'disabled'); ?>></input><?php echo " " . $val_name; ?>
                                                            <input type="hidden" name="primary_tds_type" value="<?php echo isset($group_tds_type['primary']) ? $group_tds_type['primary'] : ''; ?>">
                                                        </strong>
                                                    <?php } ?>

                                                    <?php if ($group_name == "Core") { ?>
                                                        <strong style ="<?php echo (isset($eligible_version) && in_array($val, $eligible_version) ? '' : $css); ?>"><input type="checkbox" name="product_group_values[]" value= "<?php echo $val; ?>" <?php echo (isset($eligible_version) && in_array($val, $eligible_version) ? '' : 'disabled'); ?>></input><?php echo " " . $val_name; ?>
                                                            <input type="hidden" name="core_tds_type" value="<?php echo isset($group_tds_type['core']) ? $group_tds_type['core'] : ''; ?>">
                                                        </strong>
                                                    <?php } ?>  

                                                    <?php if ($group_name == "Higher") { ?>
                                                        <strong style ="<?php echo (isset($eligible_version) && in_array($val, $eligible_version) ? '' : $css); ?>"> <input type="checkbox" name="product_group_values[]" value= "<?php echo $val; ?>"<?php echo (isset($eligible_version) && in_array($val, $eligible_version) ? '' : 'disabled'); ?>></input><?php echo " " . $val_name; ?>
                                                            <input type="hidden" name="higher_tds_type" value="<?php echo isset($group_tds_type['higher']) ? $group_tds_type['higher'] : ''; ?>">
                                                        </strong>
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
                            <div class="form-group col-sm-4">
                                <label for="capacity"><?php echo lang('app.language_school_event_test_date'); ?> <span>*</span></label>
                                <div class='input-group date' id='datetimepicker1'>							    
                                    <input type="text" class="form-control" name="testdate" maxlength="10" id="testdate" value="<?php echo set_value('testdate'); ?>" />
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>						
                            <div class="form-group test_time col-sm-4">
                                <label for="capacity"><?php echo lang('app.language_school_event_start_time'); ?> <span>*</span></label>						
                                <div class='input-group date' id='datetimepicker3'>							    
                                    <input type="text" class="form-control" name="starttime" id="starttime" value="<?php echo set_value('starttime'); ?>"  required/>	
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                </div>
                            </div>						
                            <div class="form-group col-sm-4">
                                <label for="capacity"><?php echo lang('app.language_school_event_end_time'); ?></label>						
                                <div class='input-group date' id='datetimepicker4'>
                                    <input type="text" class="form-control" name="endtime1" id="endtime1" value="<?php echo set_value('endtime'); ?>" disabled="disable" />	
                                    <input type="hidden" class="form-control" name="endtime" id="endtime" value=""/>	
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
                            foreach ($venues as $skey => $slice) {

                                $slicevenue[$skey] = strlen($slice) > 50 ? substr($slice, 0, 50) . "..." : $slice;
                            }
                            echo form_dropdown('venue_id', $slicevenue, set_value('venue_id'), 'class="form-control" id="venue_id"');
                            ?>
                        </div>
                        <div class="form-group ">
                            <label for="capacity"><?php echo lang('app.language_school_event_capacity'); ?> <span>*</span></label>					
                            <input type="number" min="1" step="1" class="form-control capacity_adder" name="capacity" id="capacity" value="<?php echo set_value('capacity'); ?>" required/>						
                        </div>
                        <div class="form-group ">
                            <label for="notes"><?php echo lang('app.language_school_event_notes'); ?></label>							
                            <textarea class="form-control" name="notes" id="notes" rows="2" cols="10"></textarea>						
                        </div>						
                        <div class="modal-footer">
                            <div class="row">
                                <div class="col-sm-12 col-xs-12 text-right">
                                    <button type="submit" id="preview-btn" class="btn btn-sm btn-continue"><?php echo lang('app.language_school_submit'); ?></button>
                                    <button type="button" class="btn btn-sm btn-continue" data-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
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
        $("#testdate").keypress(function (event) {
            event.preventDefault();
        });
        $("#endtime").keypress(function (event) {
            event.preventDefault();
        });
        $("#starttime").keypress(function (event) {
            event.preventDefault();
        });

        // current date + 1hrs -> tds	
        var sevendays = moment().add(1, 'hours').format("MM/DD/YYYY"); 
        // current date + 1 year
        var max = moment().add(90, 'd').format("MM/DD/YYYY");

        $('#datetimepicker1').datetimepicker({
            format: 'L',
            defaultDate: sevendays,
        });
        $("#datetimepicker1").on("dp.change dp.show", function (e) {
            $('#event_add_form').bootstrapValidator('revalidateField', 'testdate');
            $('#event_add_form').bootstrapValidator('revalidateField', 'starttime');		
            $('#datetimepicker1').data("DateTimePicker").maxDate(max);
            $('#datetimepicker1').data("DateTimePicker").minDate(sevendays);
        });

        $('#datetimepicker3').datetimepicker({
            format: 'LT',
            format: 'HH:mm',
        });

        $("#datetimepicker3").on("dp.change dp.show", function (e) {
            $('#event_add_form').bootstrapValidator('revalidateField', 'starttime');
            $.ajax({
                url: '<?php echo site_url('school/js_endtime_set'); ?>',
                type: 'POST',
                dataType: "json",
                data: {starttime: ($('#starttime').val() != '') ? $('#starttime').val() : ''},
                success: function (result) {
                    $('#endtime, #endtime1').val(result.endtime);
                }
            });

        });



        //event add form validation
        $('#event_add_form').bootstrapValidator({
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
                                    'testdate': ($('#testdate').val() != '') ? $('#testdate').val() : '',
                                    'starttime': ($('#starttime').val() != '') ? $('#starttime').val() : '',
                                    'endtime': ($('#endtime').val() != '') ? $('#endtime').val() : '',
                                };
                            }
                        }
                    }
                },
                capacity: {
                    validators: {
                        notEmpty: {
                        },
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
                var $form = $(e.target);
                $( ".help-block" ).empty();
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
                                url: $('#event_add_form').attr('action'),
                                data: $('#event_add_form').serialize(),
                                dataType: 'json',
                                success: function (data)
                                {
                                    clear_errors_add(data);
                                    ;
                                    $('.loading_main').hide();
                                    if (data.success) {
                                        location.reload();
                                    } else {
                                        setadd_errors(data);
                                    }

                                }
                            });
                            return false;



                        }
                    }
                });
            }

        });

        $(".plus").on('click', function () {
            $(".products").css({"display": "none"});
            $("#event_add_form .products").find('input[type="checkbox"]').prop("checked", false);
            var product = $(this).children('input[type="radio"]').val();
            $("#" + product).css({"display": "block"});
        });
        //To show contact admin msg
        $("input[name = 'product_group_name']").click(function () {
            $('.allocation').hide();
            var obj = {};
            obj.value = $(this).val();
            $.post("<?php echo site_url('school/version_allocation_msg'); ?>", obj, function (data) {
                if (data.valid == 0) {
                    $('.allocation').show();
                }
            }, "json");
        });

        function clear_errors_add(data)
        {
            if (typeof (data.errors) != "undefined" && data.errors !== null) {
                for (var k in data.errors) {

                    $('#' + k).next('p').remove();

                }
            }
        }

        function setadd_errors(data)
        {
            if (typeof (data.errors) != "undefined" && data.errors !== null) {
                for (var k in data.errors) {
                    $(data.errors[k]).insertAfter($("#" + k)).css('color', '#a94442');
                }
            }
        }

    });
</script>
