<?php include_once 'header.php';
$this->request = \Config\Services::request();
?>


<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-tasks fa-fw"></i><?= esc($admin_heading) ?>
                <a  href="<?php echo site_url('admin/template'); ?>" class="pull-right"><i class="fa fa-plus fa-fw"></i><?php echo lang('app.language_admin_add_mail_template'); ?></a>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="table-responsive table-bordered">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>
                                    <?php  
										echo anchor(current_url()."?order=" .(($this->request->getVar('order') == 'DESC') ? 'ASC' : 'DESC').'&val=categ', lang('app.language_admin_label_mail_category'). ((($this->request->getVar('order') == 'DESC') && ($this->request->getVar('val') == 'categ'))? '&nbsp;<span class="glyphicon glyphicon-arrow-up"></span>' : ((( ($this->request->getVar('order') == 'ASC') && ($this->request->getVar('val') == 'categ') )) ? '&nbsp;<span class="glyphicon glyphicon-arrow-down"></span>' : '')) ); 	
									?>
								</th>
                                <th><?php echo lang('app.language_admin_language'); ?></th>
                                <th><?php echo lang('app.language_admin_label_mail_subject'); ?></th>
                                <th><?php echo lang('app.language_admin_label_mail_display_name'); ?></th>
                                <th><?php echo lang('app.language_admin_label_mail_from_email'); ?></th>
                                <th><?php echo lang('app.language_admin_action'); ?></th>
                            </tr>
                        </thead>
                        <?php if (!empty($results)): ?>
                            <tbody>

                                <?php foreach ($results as $result): ?>	
                                    <tr>

                                        <td><?php echo $result->category_name; ?></td>
                                        <td><?php echo json_decode('"'.$result->LANG_NAME.'"'); ?></td>
                                        <td><?php echo $result->subject; ?></td>
                                        <td><?php echo $result->display_name; ?></td>
                                        <td><?php echo $result->from_email; ?></td>

                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-primary" onclick="window.location = '<?php echo site_url('admin/template/' . $result->id); ?>'"><?php echo lang('app.language_admin_edit'); ?></button>
                                                <button  type="button" class="btn btn-danger delete_emailtemplate" id="mail_<?php echo $result->id; ?>"><?php echo lang('app.language_admin_delete'); ?></button>
                                            </div>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>


                            </tbody>
                        <?php endif; ?>
                    </table>
                    <?php if (($pager)) :?>
                    <?php $pager->setPath($_SERVER['PHP_SELF']);?>
                    <?= $pager->links() ?>
                    <?php endif ?> 
                  
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-6-->
</div>




<?php include 'footer.php'; ?>
<script>
    $(function () {
        $('.delete_emailtemplate').click(function () {
            if (confirm("<?php echo lang('app.language_admin_are_you_sure'); ?>")) {
                var mail_str = $(this).attr('id');
                var mail_id = mail_str.replace("mail_", "");
                window.location = "<?php echo site_url('admin/deletemailtemplate'); ?>" + '/' + mail_id;
            }
            return false;
        });


    });
</script>