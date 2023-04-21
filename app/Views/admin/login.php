
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <link rel="icon" href="<?php echo base_url(); ?>/public/images/favicon.ico">
    <link rel="mask-icon"  href="<?php echo base_url(); ?>/public/images/fav_mac.svg" >

    <title><?= esc($admin_title) ?></title>

    <!-- Bootstrap Core CSS -->
    <link href="<?php echo base_url(); ?>public/admin/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="<?php echo base_url(); ?>public/admin/bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?php echo base_url(); ?>public/admin/dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="<?php echo base_url(); ?>public/admin/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

</head>

<body>

    <div class="container">
        <div class="row">
        <?php include_once 'messages.php';?>
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= esc($admin_heading) ?></h3>
                        <p class="lead"></p>
                          <?php if (isset($validation)): ?>
                            <p><?= $validation->getError('username') ?></p>
                            <p><?= $validation->getError('password') ?></p>
                            <?php endif; ?>
                        <p></p>
                    </div>
                    <div class="panel-body">
                        <?php echo form_open('admin/login',array('class'=>'form','role'=>'form')); ?>
                            <legend style="font-size: 14px;">
                                <div class="form-group">
                                    <input class="form-control" placeholder="<?php echo lang('app.language_admin_username'); ?>" name="username" type="text" autofocus>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder=<?php echo lang('app.language_admin_password'); ?> name="password" type="password" value="">
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="remember" type="checkbox" value="TRUE"><?php echo lang('app.language_admin_remember_me'); ?>
                                    </label>
                                </div>
                                <!-- Change this to a button or input when using this as a form -->
                               <button type="submit" class="btn btn-lg btn-primary btn-block"><?php echo lang('app.language_admin_submit'); ?></button>
                            </legend>
                       <?php form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="<?php echo base_url(); ?>public/admin/bower_components/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="<?php echo base_url(); ?>public/admin/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="<?php echo base_url(); ?>public/admin/bower_components/metisMenu/dist/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="<?php echo base_url(); ?>public/admin/dist/js/sb-admin-2.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
				$('.custommessages').delay(4000).fadeTo(1000,0.01).slideUp(500,function() {});
		});
	</script>
</body>

</html>



