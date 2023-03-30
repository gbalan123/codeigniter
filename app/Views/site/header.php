
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="refresh" content="14400;URL=<?php echo site_url('site/logout');?>">
    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=no">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="title" content="<?php if(!empty($cmsvalue[0]->meta_title))echo $cmsvalue[0]->meta_title; ?>">
    <meta name="keywords" content="<?php if(!empty($cmsvalue[0]->meta_keywords)) echo $cmsvalue[0]->meta_keywords; ?>">
    <meta name="description" content="<?php if(!empty($cmsvalue[0]->meta_description)) echo $cmsvalue[0]->meta_description; ?>">
    <meta name="author" content="">
    <link rel="icon" href="<?php echo base_url(); ?>/public/images/favicon.ico">
    <link rel="mask-icon"  href="<?php echo base_url(); ?>/public/images/fav_mac.svg" >

    <title>CATs Step - Start Learning</title>
    
   <!--Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:100,200,300,400,500,600,700,800,900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Bubblegum+Sans&display=swap" rel="stylesheet">
  
	<!-- Bootstrap -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>/public/css/bootstrap.min.css">
	<!--  Site -->
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/public/css/bootstrap-multiselect.css">
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/public/css/style_updated_ui.css">
        <script src="https://apis.google.com/js/platform.js" async defer></script>
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <link type="text/css" rel="stylesheet" href="<?php echo base_url('public/css/alertify.css') . '?' . time(); ?>">
        <script src="<?php echo base_url('public/js/alertify.js') . '?' . time(); ?>" type="text/javascript"></script>
        <?php $this->request = \Config\Services::request();
            $this->data['lang_code'] = $this->request->getLocale() 
        ?>
<!-- Jquery -->
  </head>

<!-- WP-1115-Placement test export fails - added class notranslate  -->
<body class="notranslate">