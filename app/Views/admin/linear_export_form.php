<?php include_once 'header.php'; ?>

<!-- /.row -->
<div class="row">

    <div class="col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <em class="fa fa-download fa-fw"></em><?= esc($admin_heading) ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12">
                        <?php echo form_open_multipart('admin/linear_export', array('class' => 'form bv-form form-inline', 'role' => 'form', 'id' => 'product_form', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>
                        <div class = "form-group">
                            <label class = "sr-only" for = "sdate"><?php echo lang('app.language_admin_start_date'); ?></label>
                            <div class='input-group date' id='datetimepicker1'>							    
                                <input type="text" class="form-control" name="start_date" id="start_date" placeholder="<?php echo lang('app.language_admin_start_date'); ?>" value="<?php echo set_value('start_date'); ?>" />
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>

                        <div class = "form-group">
                            <label class = "sr-only" for = "edate"><?php echo lang('app.language_admin_end_date'); ?></label>
                            <div class='input-group date' id='datetimepicker2'>							    
                                <input type="text" class="form-control" name="end_date" placeholder="<?php echo lang('app.language_admin_end_date'); ?>" id="end_date" value="<?php echo set_value('end_date'); ?>" />
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class = "checkbox-inline">
                                <input type = "radio" id = "export_style[]" checked="checked"  name = "export_style[]" value = "report"> <?php echo lang('app.language_admin_adp_export_report'); ?>
                            </label>
                        </div>
                       
                        <button type="submit"  class="btn btn-primary"><span class="glyphicon glyphicon-download" >&nbsp;</span><?php echo lang('app.language_admin_export_to_excel'); ?></button>
                            <?php form_close(); ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


<?php include 'footer.php'; ?>
<script type="text/javascript">
    $(document).ready(function () {
        $("#start_date").keypress(function (event) {
            event.preventDefault();
        });
        $("#end_date").keypress(function (event) {
            event.preventDefault();
        });
        // current date 	
        var currentdate = moment();
        // max date
        var max = moment().add(365, 'd').format("MM/DD/YYYY");
        $('#datetimepicker1').datetimepicker({
            format: 'DD/MM/YYYY'
        });
        $('#datetimepicker2').datetimepicker({
            format: 'DD/MM/YYYY',
            
            maxDate: currentdate
        });
        
    });





</script>

