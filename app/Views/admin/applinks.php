<?php include_once 'header.php'; ?>

<!-- /.row -->
<div class="row">

    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-plus fa-fw"></i><?= esc($admin_heading) ?><a
                    href="<?php echo site_url('admin/list_applinks'); ?>" class="pull-right"><i
                        class="fa fa-files-o fa-fw"></i><?php echo 'List apps'; ?></a>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?php if (isset($applinks) && !empty($applinks)): ?>
                            <?php echo form_open_multipart('admin/post_applink/' . $applinks[0]->id, array('class' => 'form bv-form', 'role' => 'form', 'id' => 'applinks_form', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>
                        <?php else: ?>
                            <?php echo form_open_multipart('admin/post_applink', array('class' => 'form bv-form', 'role' => 'form', 'id' => 'applinks_form', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>
                        <?php endif; ?>
                        <div class="form-group">
                            <label for="view_section"><?php echo lang('app.language_admin_applinks_heading_section'); ?><span>*</span></label> <input type="text" disabled="true"
                                                                                                                                                   class="form-control" name="view_section"
                                                                                                                                                   value="<?php echo set_value('view_section', isset($applinks) ? $applinks[0]->area : ''); ?>"
                                                                                                                                                   required>
                        </div>
                        <div class="form-group">
                            <label><?php echo lang('app.language_admin_applinks_heading_appurl'); ?><span>*</span></label><input class="form-control" type="text"
                                                                                                                    name="app_link"
                                                                                                                    value="<?php echo set_value('app_link', isset($applinks) ? $applinks[0]->app_link : ''); ?>"
                                                                                                                    placeholder="http://"  required>
                        </div>
                        <button type="submit" class="btn btn-primary"><?php echo lang('app.language_admin_submit'); ?></button>
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
<?php include 'footer.php'; ?>
