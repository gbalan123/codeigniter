<!doctype html>
<html dir="ltr" lang="en-US" class="no-js">
<head>
    <meta charset="utf-8">
    <title>CATs Step</title>
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="author" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="shortcut icon" href="<?php echo base_url('/public/images/favicon.ico'); ?>" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>/public/css/bootstrap.css">
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('/public/css/style_updated_ui.css'); ?>" />
</head>
<body class="error_page">
    <header class="fixed_header">
        <div class="bg-white">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6 col-sm-4 col-xs-4">
                        <div class="navbar-header">
                            <div class="logo new_page"> 
                                <a href="#" class="navbar-brand "><img src="<?php echo base_url('/public/images/logo_new.svg'); ?>" class="img-responsive" alt="CATs Logo"></a> 
                            </div>
                        </div>
                    </div>
                </div><div class="clear"></div>  
            </div>
        </div>
    </header>
    <section class="not_found_page">
        <div class="container-fluid h-100">
            <div class="row h-100 align-items-center">
                <div class="col-12">
                    <h2>ERROR 404</h2>
                    <img src="<?php echo base_url('/public/images/error_img.png'); ?>" alt="error_img" class="img-fluid">
                    <p>The page you are looking for might have been removed, had its name changed or is temporarily unavailable.</p>
                    <a href="<?php echo site_url();?>" class="home_btn btn btn-primary">Home</a>
                </div>
            </div>
        </div>
	</section>
	<footer class="footer-section"></footer>    
</body>
</html>
