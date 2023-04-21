<?php include_once 'header.php'; ?>

<!-- /.row -->
<div class="row">
    <div class="col-lg-8">
        <div class="panel panel-default">

            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12">
                        <?php echo form_open_multipart('admin/primary_question_upload', array('class' => 'form', 'role' => 'form', 'method' => 'post', 'id' => 'question_bank_form', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>


                        <div class="form-group">
                            <label for="name"><?php echo lang('app.language_admin_question_linear_bank_field'); ?>:</label> 
                            <input type="file" class="form-control input-lg" name="question_linear_bank" id="question_linear_bank"  required />
                        </div>
                        <button name="linear_submit" type="submit" class="btn btn-primary"><?php echo lang('app.language_admin_upload'); ?></button>
                        <?php echo form_close(); ?>
                    </div>

                </div>
                <div class="row ">					
                    <div class="col-sm-12">						 
                        <div class="form-group  new-previw">
                            <a id="linearpreview_link" href="#" target="_blank" class="btn btn-primary">Preview</a>
                            <?php if (!empty($linear_details)) { ?> 		
                                <label class="radio-inline" for="radios-0"><input type="radio" name="linearpreview" class="linearpreview" value="from_database" checked>From the database</label>
                            <?php } ?>

                            <span>
                                <label class="radio-inline" for="radios-1"><input type="radio" name="linearpreview" class="linearpreview"id="linearpreview_above" value="from_above">From the above file</label>&nbsp;&nbsp;<img alt="loading" class="loading" style="display:none;" src="<?php echo base_url('public/images/loading.gif'); ?>">
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.row -->
<?php include 'footer.php'; ?>
<script type="text/javascript">
    $(document).ready(function () {
        /*Core bank preview */
        $('#linearpreview_link').click(function () {
            $('.loading').show();
            if ($('input[name=linearpreview]:checked').val()) {
                if ($('input[name=linearpreview]:checked').val() == 'from_database') {
                    $('.loading').hide();
                    addr = "<?php echo site_url('admin/download_content_json/linear'); ?>";
                    $('#linearpreview_link').attr('target', '_blank');
                    $("#linearpreview_link").attr("href", addr);
                    $('#linearpreview_link').removeAttr('disabled');
                } else if ($('input[name=linearpreview]:checked').val() == 'from_above') {
                    addr = "#";
                    $("#linearpreview_link").attr("href", addr);
                    $('#linearpreview_link').removeAttr('target');

                    var has_selected_file = $('#question_linear_bank').filter(function () {
                        return $.trim(this.value) != ''
                    }).length > 0;
                    if (has_selected_file) {
                        console.log('has file');
                        var data = new FormData($('#question_bank_form')[0]);
                        $.ajax({
                            type: "POST",
                            url: "<?php echo site_url('admin/fileUpload/linear'); ?>",
                            data: data,
                            mimeType: "multipart/form-data",
                            contentType: false,
                            cache: false,
                            processData: false,
                            success: function (data) {
                                var obj = JSON.parse(data);
                                $('.loading').hide();
                                if (obj.status == 'success') {

                                    addr = "<?php echo site_url('admin/preview_content_json/linear'); ?>";
                                    $('#linearpreview_link').attr('target', '_blank');
                                    $("#linearpreview_link").attr("href", addr);
                                    window.open('<?php echo site_url('admin/preview_content_json/linear'); ?>', '_blank');
                                } else {
                                    addr = "#";
                                    $("#linearpreview_link").attr("href", addr);
                                    $('#linearpreview_link').removeAttr('target');
                                    alert('test.json file is not available');
                                }
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                                console.log('failure');
                                console.log(data);
                            }
                        });
                    } else {
                        $('.loading').hide();
                        addr = "#";
                        $("#linearpreview_link").attr("href", addr);
                        $('#linearpreview_link').removeAttr('target');
                        alert('Upload a valid file in linear bank upload section');
                        console.log('no file');
                    }
                }
            }
        });

        //product form validation
        $('#question_bank_form').bootstrapValidator({
            locale: "en",
            // List of fields and their validation rules
            fields: {
                question_bank: {
                    validators: {
                        file: {
                            extension: 'zip',
                            contentType: 'application/zip'

                        }
                    }
                }

            }

        });


    });
</script>

