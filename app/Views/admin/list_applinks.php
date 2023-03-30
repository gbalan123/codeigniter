<?php include_once 'header.php'; ?>


<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-tasks fa-fw"></i><?= esc($admin_heading) ?>
                <!--<a  href="<?php echo site_url('admin/cms'); ?>" class="pull-right"><i class="fa fa-plus fa-fw"></i><?php echo lang('language_admin_add_page'); ?></a>-->
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="table-responsive table-bordered">
                    <table class="table">
                        <thead>
                            <tr>
                                <th><?php echo lang('app.language_admin_applinks_heading_section'); ?></th>
                                <th><?php echo 'Platform'; ?></th>
                                <th><?php echo lang('app.language_admin_applinks_heading_appurl'); ?></th>

                                <th><?php echo lang('app.language_admin_action'); ?></th>
                            </tr>
                        </thead>
                        <?php if (!empty($results)): ?>
                            <tbody>

                                <?php foreach ($results as $result): ?>	
                                    <tr>

                                        <td><?php echo $result->area; ?></td>
                                        <td><?php echo $result->platform; ?></td>
                                        <td><?php echo $result->app_link; ?></td>

                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-primary" onclick="window.location = '<?php echo site_url('admin/applinks/' . $result->id); ?>'"><?php echo lang('app.language_admin_edit'); ?></button>
                                            </div>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>


                            </tbody>
                        <?php endif; ?>
                    </table>

                        <?php if ($pager) :?>
                        <?php $check = $pager->setPath($_SERVER['PHP_SELF']);?>
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
