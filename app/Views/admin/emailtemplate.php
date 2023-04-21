<?php include_once 'header.php'; ?>

<!-- /.row -->
<div class="row">
    <p class="lead">
        </p>
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <em class="fa fa-plus fa-fw"></em><?= esc($admin_heading) ?> <a
                    href="<?php echo site_url('admin/listemailtemplates'); ?>" class="pull-right"><em
                        class="fa fa-files-o fa-fw"></em><?php echo lang('app.language_admin_list_templates'); ?></a>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?php if (isset($templatedatas) && !empty($templatedatas)): ?>
                            <?php echo form_open_multipart('admin/posttemplate/' . $templatedatas[0]->id, array('class' => 'form bv-form', 'role' => 'form', 'id' => 'template_form', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>
                        <?php else: ?>
                            <?php echo form_open_multipart('admin/posttemplate', array('class' => 'form bv-form', 'role' => 'form', 'id' => 'template_form', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="language"><?php echo lang('app.language_admin_language'); ?> <span>*</span></label> <select
                            class="form-control" name="language_code" required  <?php echo (isset($templatedatas) && isset($templatedatas[0]->language_code)) ? "disabled='true'" : ''; ?>>
                                    <?php foreach ($languages as $language): ?>
                                    <option value="<?php echo $language->code; ?>"
                                            <?php echo (isset($templatedatas) && $language->code == $templatedatas[0]->language_code) ? 'selected' : ''; ?>>
                                        <?php echo json_decode('"'.$language->name.'"'); ?></option>
                                <?php endforeach; ?>	
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="category_id"><?php echo lang('app.language_admin_mail_category'); ?> <span>*</span></label> <select
                                class="form-control" name="category_id" required <?php echo (isset($templatedatas) && isset($templatedatas[0]->category_id)) ? "disabled='true'" : ''; ?>>
                                <option value=""><?php echo lang('app.language_admin_label_mail_select_category'); ?></option>
                                    <?php foreach ($templatenames as $templatename): ?>
                                    <option value="<?php echo $templatename->id; ?>"
                                    <?php echo set_select('category_id', $templatename->id); ?>
                                            <?php echo (isset($templatedatas) && $templatename->id == $templatedatas[0]->category_id) ? 'selected' : ''; ?>>
                                        <?php echo $templatename->category_name; ?></option>
                                <?php endforeach; ?>	
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="display_name"><?php echo lang('app.language_admin_label_mail_display_name'); ?>  <span>*</span></label><input type="text"
                                                                                                                                      class="form-control" name="display_name"
                                                                                                                                      value="<?php echo set_value('display_name', isset($templatedatas) ? $templatedatas[0]->display_name : ''); ?>"
                                                                                                                                      required>
                        </div>
                        <div class="form-group">
                            <label for="username"><?php echo lang('app.language_admin_label_mail_from_email'); ?>  <span>*</span></label><input type="text"
                                                                                                                                      class="form-control" name="from_email"
                                                                                                                                      value="<?php echo set_value('from_email', isset($templatedatas) ? $templatedatas[0]->from_email : ''); ?>"
                                                                                                                                      required>
                        </div>

                        <div class="form-group">
                            <label for="username"><?php echo lang('app.language_admin_label_mail_subject'); ?>  <span>*</span></label><input type="text"
                                                                                                                                         class="form-control" name="subject"
                                                                                                                                         value="<?php echo set_value('subject', isset($templatedatas) ? $templatedatas[0]->subject : ''); ?>"
                                                                                                                                         required>
                        </div>
                        <div class="form-group">
                            <label for="content"><?php echo lang('app.language_admin_label_mail_content'); ?> <span>*</span></label> 
                            <textarea class="form-control" name="content" id="content" 
                                      rows="10" cols="10" required>
                                <?php echo set_value('content', isset($templatedatas) ? $templatedatas[0]->content : ''); ?></textarea>
                        </div>
              

                        <button type="submit" class="btn btn-primary"><?php echo lang('app.language_admin_submit'); ?></button>
                        <?php form_close(); ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
<script
src='<?php echo base_url(); ?>public/js/ckeditor/ckeditor.js'></script>
<script>
    if ($('#content').val() != '') {


        CKEDITOR.config.protectedSource.push(/<\?[\s\S]*?\?>/g);
        var editor = CKEDITOR.replace('content',
                {
                    language: "",
                    height: 900,
                    allowedContent: true

                });



    }

</script>