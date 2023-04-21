<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="refresh" content="7200;URL=<?php echo base_url('admin/logout');?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<link rel="icon" href="<?php echo base_url(); ?>public/images/favicon.ico">
<link rel="mask-icon"  href="<?php echo base_url(); ?>public/images/fav_mac.svg" >
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title><?= esc($admin_title) ?></title>

<!--  admin panel -->

<!-- Bootstrap Core CSS -->
<link
	href="<?php echo base_url(); ?>public/admin/bower_components/bootstrap/dist/css/bootstrap.min.css"
	rel="stylesheet">
<link
	href="<?php echo base_url(); ?>public/css/bootstrap-datetimepicker.css"
	rel="stylesheet">	
<!-- Bootstrap validator CSS -->
<link href="<?php echo base_url('public/js/bootstrapValidator/bootstrapValidator.min.css');?>" rel="stylesheet">

<!-- MetisMenu CSS -->
<link
	href="<?php echo base_url(); ?>public/admin/bower_components/metisMenu/dist/metisMenu.min.css"
	rel="stylesheet">
<!-- DataTables CSS -->
<link
	href="<?php echo base_url(); ?>public/admin/bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css"
	rel="stylesheet">

<!-- DataTables Responsive CSS -->
<link
	href="<?php echo base_url(); ?>public/admin/bower_components/datatables-responsive/css/dataTables.responsive.css"
	rel="stylesheet">

<!-- Timeline CSS -->
<link href="<?php echo base_url(); ?>public/admin/dist/css/timeline.css"
	rel="stylesheet">

<!-- Custom CSS -->
<link
	href="<?php echo base_url(); ?>public/admin/dist/css/sb-admin-2.css"
	rel="stylesheet">

<!-- Morris Charts CSS -->
<link
	href="<?php echo base_url(); ?>public/admin/bower_components/morrisjs/morris.css"
	rel="stylesheet">

<!-- Custom Fonts -->
<link
	href="<?php echo base_url(); ?>public/admin/bower_components/font-awesome/css/font-awesome.min.css"
	rel="stylesheet" type="text/css">
 <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/css/bootstrap-multiselect.css">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <link type="text/css" rel="stylesheet" href="<?php echo base_url('public/css/alertify.css') . '?' . time(); ?>">
        <!-- include alertify script -->
<script src="<?php echo base_url('public/js/alertify.js') . '?' . time(); ?>" type="text/javascript"></script> 
</head>
<body>
	<div id="wrapper">

	<?php include_once 'menus.php';  ?>
		<!-- Begin page content -->
		<div id="page-wrapper">
			<div class="row">


			<?php include_once 'messages.php';  ?>
				<div class="col-lg-12">
				<h1 class="page-header"><?= esc($admin_heading) ?></h1>
				</div>
			</div>