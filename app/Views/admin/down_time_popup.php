<style>
    .time_error {
        color: red;
    }
</style>
<?php
if(isset($downtime_data) && !empty($downtime_data)) {
    $downtime_zone_values = @get_downtime_zone_from_utc($downtime_data['timezone'], $downtime_data['start_date_time'], $downtime_data['end_date_time']);
    $start_date = date('m/d/Y', strtotime($downtime_zone_values['downtime_start_date']));
    $start_time = date('H:i', strtotime($downtime_zone_values['downtime_start_time']));
    $end_date = date('m/d/Y', strtotime($downtime_zone_values['downtime_end_date']));
    $end_time = date('H:i', strtotime($downtime_zone_values['downtime_end_time']));
} else {
    $start_date = '';
    $start_time = '';
    $end_date = '';
    $end_time = '';
}
?>
<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading"><i class="fa fa-plus fa-fw"></i><?= esc($admin_heading) ?></div>
			<div class="panel-body">
            <span id="downtime_exist"></span>
            <?php echo form_open('admin/save_downtime', array('id' => 'add_down_time_form')); ?>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label><?php echo lang('app.language_admin_institutions_timezone');?></label> <span class="error">*</span>
                            <select class="form-control" name="timezone" id="timezone" >
                                <option value=""><?php echo lang('app.language_admin_please_select'); ?></option>
                                <?php foreach ($timezones as $value => $label): ?>
                                    <option <?php echo set_select('timezone', isset($downtime_data) ? $downtime_data['timezone'] : '', ( isset($downtime_data) && $downtime_data['timezone'] == $value ? TRUE : FALSE)); ?> value="<?php echo $value; ?>"><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
				<div class="row">
					<div class="col-xs-6">
                        <div class="form-group">
                            <label for="name"><?php echo lang('app.language_admin_downtime_start_date'); ?></label> <span class="error">*</span>
                            <div class='input-group date' id='datetimepicker1'>							    
                                <input type="text" class="form-control" name="start_date" maxlength="10" id="start_date" value="<?php echo $start_date;?>" />
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                            <?php if (isset($validation)): ?>
                            <?= $validation->listErrors('start_date') ?>
                            <?php endif; ?>
                        </div>
                    </div>   
                    <div class="col-xs-6">          
                    <div class="form-group">
                            <label for="name"><?php echo lang('app.language_admin_downtime_end_date'); ?></label> <span class="error">*</span>
                            <div class='input-group date' id='datetimepicker2'>							    
                                <input type="text" class="form-control" name="end_date" maxlength="10" id="end_date" value="<?php echo $end_date;?>" />
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
         
                            <?php if (isset($validation)): ?>
                            <?= $validation->listErrors('end_date') ?>
                            <?php endif; ?>
                        </div>
					</div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="name"><?php echo lang('app.language_admin_downtime_start_time'); ?></label> <span class="error">*</span>
                            <div class='input-group date' id='datetimepicker3'>							    
                                <input type="text" class="form-control" name="start_time" id="start_time" value="<?php echo $start_time;?>"  />	
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                        </div>
                        <span id="starttime_error" class="time_error"></span> 
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label for="name"><?php echo lang('app.language_admin_downtime_end_time'); ?></label> <span class="error">*</span>
                            <div class='input-group date' id='datetimepicker4'>							    
                                <input type="text" class="form-control" name="end_time" id="end_time" value="<?php echo $end_time;?>"  />	
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                        </div>
                        <span id="endtime_error" class="time_error"></span>
                    </div>
                </div>
               
                <input type="hidden" name="id" value="<?php if(!empty($downtime_data)){ echo $downtime_data['id']; }else{ echo '';} ?>" />
                <div class="form-group form-actions pull-right" style="clear: both;">
                    <button type="submit" id="submitBtn" class="btn btn-sm btn-primary wpsc_button" style="pointer-events: none;"><?php echo lang('app.language_admin_institutions_submit_btn'); ?></button>
                </div>	 
				<?php form_close(); ?>
				<!-- /.row (nested) -->
			</div>
			<!-- /.panel-body -->
		</div>
		<!-- /.panel -->
	</div>
	<!-- /.col-lg-12 -->
</div>
<script>
    $(function () {
        // current date + 1hrs -> tds	
        var sevendays = moment().add(1, 'hours').format("MM/DD/YYYY"); 
        // current date + 1 year
        var max = moment().add(90, 'd').format("MM/DD/YYYY");

        $('#datetimepicker1').datetimepicker({
            format: 'L',
            defaultDate: sevendays,
        });
        $("#datetimepicker1").on("dp.change dp.show", function (e) {
            $('#datetimepicker1').data("DateTimePicker").maxDate(max);
            $('#datetimepicker1').data("DateTimePicker").minDate(sevendays);
        });

        $('#datetimepicker2').datetimepicker({
            format: 'L',
            defaultDate: sevendays,
        });

        $("#datetimepicker2").on("dp.change dp.show", function (e) {
            var start_date = $("#start_date").val();
            $('#datetimepicker2').data("DateTimePicker").minDate(start_date);
        });

        var start_time = $('#start_time').val();
        if(start_time != '') {
            $('#datetimepicker3').datetimepicker({
                format: 'HH:mm',
            });
        } else {
            $('#datetimepicker3').datetimepicker({
                format: 'LT',
                format: 'HH:mm',
                defaultDate: moment(),
            });
        }
        var end_time = $('#end_time').val();

        if(end_time != '') {
            $('#datetimepicker4').datetimepicker({
                format: 'HH:mm',
            });
        } else {
            $('#datetimepicker4').datetimepicker({
                format: 'LT',
                format: 'HH:mm',
                defaultDate: moment()
            });
        }        

        $('#datetimepicker1').on('dp.change', function(e) {
            $('#datetimepicker2').data("DateTimePicker").minDate(e.date);
        });

        $('#datetimepicker4').on('dp.change', function(e) {
            start_time_check();
        });

        $('#datetimepicker3').on('dp.change', function(e) {
            start_time_check_based_on_timezone();
        });

        $('#timezone').change(function(){
            disable_date_time_fields();
            start_time_check_based_on_timezone();
        });

        $('#datetimepicker2').on('dp.change', function(e) {
            start_time_check();
        });

        function start_time_check() {
            $('#starttime_error').text('');
            $('#endtime_error').text('');
            $('.downtime_exist').hide();
            $( ".help-block" ).empty();
            $( "p" ).empty();
            var start_date = $("#start_date").val();
            var end_date = $("#end_date").val();
            if(start_date == end_date) {
                var start_time = $("#start_time").val();
                var end_time = $("#end_time").val();
                if(end_time == start_time) {
                    $('#starttime_error').text('Start time and end time should not be equal');
                    $('#endtime_error').text('Start time and end time should not be equal');
                    $('#submitBtn').attr('disabled', true);
                }else if(end_time <= start_time) {
                    $('#endtime_error').text('End time should be greater than start time');
                    $('#submitBtn').attr('disabled', true);
                } else {
                    $('#endtime_error').text('');
                    $('#submitBtn').attr('disabled', false);
                    $('#starttime_error').text('');
                }
            } else {
                $('#endtime_error').text('');
                $('#submitBtn').attr('disabled', false);
                $('#starttime_error').text('');
            }
        }
    });

    function start_time_check_based_on_timezone() {

        var start_date = $("#start_date").val();
        var start_time = $("#start_time").val();
        var timezone = $("#timezone").val();

        $('.downtime_exist').hide();
        $("#starttime_error").empty();
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('/admin/check_current_time_based_on_timezone');?>",
            data: {
                start_date: start_date,
                start_time: start_time,
                timezone: timezone
            },
            dataType: 'json',
            success: function (data)
            {
                $('#endtime_error, .help-block').text('');
                $( "p" ).empty();
                if (data.success) {
                    $('#submitBtn').attr('disabled', true);
                    $('#starttime_error').text(data.msg);
                }else {
                    $('#starttime_error').text('');
                    $('#submitBtn').attr('disabled', false);
                }
            }
        });
    }
    
    $(window).load(function(){
        $('.wpsc_button').css('pointer-events','all');
        $('.wpsc_button').removeClass('disabled');
    });

    $('#add_down_time_form').submit(function (e) {
        e.preventDefault();   
        $('#submitBtn').attr('disabled', true);
        $('.downtime_exist').hide();
        $(".help-block, #starttime_error").empty();
        $.ajax({
            type: "POST",
            url: $(this).attr('action'),
            data: $(this).serialize(),
            dataType: 'json',
            success: function (data)
            {
                clear_errors(data);
                $('#submitBtn').attr('disabled', false);
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
                if(k == 'starttime_error' && $("#starttime_error").text() != '') {
                    
                } else {
                    $(data.errors[k]).insertAfter($("#" + k));
                }
            }
        }
    }

    function disable_date_time_fields() {
        var timezone = $("#timezone").val();
        if(timezone == '') {
            $("#start_date").attr('disabled', true);
            $("#end_date").attr('disabled', true);
            $("#start_time").attr('disabled', true);
            $("#end_time").attr('disabled', true);
            $("#submitBtn").attr('disabled', true);
        } else {
            $("#start_date").attr('disabled', false);
            $("#end_date").attr('disabled', false);
            $("#start_time").attr('disabled', false);
            $("#end_time").attr('disabled', false);
            $("#submitBtn").attr('disabled', false); 
        }
        
    }

    $(document).ready(function(){
        disable_date_time_fields();
    });
</script>