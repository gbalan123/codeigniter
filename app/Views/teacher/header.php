<?php //include 'expires.php';?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="refresh" content="7200;URL=<?php echo site_url('teacher/logout');?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="title" content="<?php if(!empty($cmsvalue[0]->meta_title))echo $cmsvalue[0]->meta_title; ?>">
    <meta name="keywords" content="<?php if(!empty($cmsvalue[0]->meta_keywords)) echo $cmsvalue[0]->meta_keywords; ?>">
    <meta name="description" content="<?php if(!empty($cmsvalue[0]->meta_description)) echo $cmsvalue[0]->meta_description; ?>">
    <meta name="author" content="">
    <link rel="icon" href="<?php echo base_url(); ?>/public/images/favicon.ico">
    <link rel="mask-icon"  href="<?php echo base_url(); ?>/public/images/fav_mac.svg" >

    <title>CATs Step - Start Learning</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:100,200,300,400,500,600,700,800,900&display=swap" rel="stylesheet">
	<!-- Bootstrap -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>/public/css/bootstrap.css">
	<!-- TDS-366 bootstrap icon cdn -->
  <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css'>    
  <!--  Site -->
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/public/css/style_updated_ui.css">
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/public/css/bootstrap-multiselect.css">
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/public/css/daterangepicker.css">
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
          <!-- include alertify.css -->
        <link type="text/css" rel="stylesheet" href="<?php echo base_url('public/css/alertify.css') . '?' . time(); ?>">
        <!-- include alertify script -->
        <script src="<?php echo base_url('public/js/alertify.js') . '?' . time(); ?>" type="text/javascript"></script>
       
<!-- Jquery -->

  </head>


<body class="light-grey">