<?php
use App\Libraries\Acl_auth;
use Config\MY_Lang;
$this->lang = new \Config\MY_Lang(); 
$this->acl_auth = new Acl_auth();

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=9,10,edge" >
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="<?php echo base_url('/public/images/favicon.ico'); ?>">
        <link rel="mask-icon"  href="<?php echo base_url(); ?>/public/images/fav_mac.svg" >
        <title>CATs Step - Start Learning</title>
        <!-- Bootstrap core CSS -->
        <link type="text/css" href="<?php echo base_url('/public/css/bootstrap.css') . '?' . time(); ?>" rel="stylesheet">
        <!-- Bootstrap validator CSS -->
        <link type="text/css" href="<?php echo base_url('/public/js/bootstrapValidator/bootstrapValidator.min.css') . '?' . time(); ?>" rel="stylesheet">
        <!-- Custom styles for this template -->
        <link type="text/css" href="<?php echo base_url('/public/css/style_updated_ui.css') . '?' . time(); ?>" rel="stylesheet">
	   <!-- Font -->   	
        <!-- include alertify.css -->
        <link type="text/css" rel="stylesheet" href="<?php echo base_url('public/css/alertify.css') . '?' . time(); ?>">
        <!--Google Fonts -->
  		<link href="https://fonts.googleapis.com/css?family=Montserrat:100,200,300,400,500,600,700,800,900&display=swap" rel="stylesheet">
 		 <link href="https://fonts.googleapis.com/css?family=Bubblegum+Sans&display=swap" rel="stylesheet">
        <link type="text/css" href="<?php echo base_url('public/css/realia/realia.css'); ?>" rel="stylesheet">
        <!-- include alertify script -->
        <script src="<?php echo base_url('public/js/alertify.js') . '?' . time(); ?>" type="text/javascript"></script>
        <script src="<?php echo base_url('public/js/howler.min.js').'?'.  time();?>"></script>
        <script>
            var timeleft = 4;
            function timedCount() {
                $('.countdowntimer_global').show();
                if(timeleft <= 0){
                    $('.next-btn').click();
                    timeleft = 4;
                    $('.countdowntimer_global').hide();
                    clearTimeout(timer);
                    return false;
                }
                sec_text = "<?php echo lang('app.language_linear_timer_seconds'); ?>";                                    
                timeleft = timeleft - 1;
                if(timeleft <= 1){
                    sec_text = "<?php echo lang('app.language_linear_timer_second'); ?>";
                }
                $('#countdowntimer').text(timeleft+' '+sec_text);
                var timer = setTimeout(function(){ timedCount() }, 1000);
                
            }  
        </script>
        <style>
            .help-block{
                color : red;
                font-size: 12px;
            }
            #buttonGroupForm .btn-group .form-control-feedback {
                top: 0;
                right: -30px;
            }


            .dinosaurs { counter-reset: item }
            .dinosaurs .radio,.dinosaurs .checkbox{
                margin-left: 40px;
            }
            .dinosaurs li { display: block }
            .dinosaurs li:before { 
                content: counter(item) ". ";
                counter-increment: item;
                width: 2em;
                display: inline-block;
                float : left;
            }

        </style>
        <style>
            .button {
                position: relative;
                background-color: #4CAF50;
                border: none;
                font-size: 28px;
                color: #FFFFFF;
                padding: 20px;
                width: 200px;
                text-align: center;
                -webkit-transition-duration: 0.4s; /* Safari */
                transition-duration: 0.4s;
                text-decoration: none;
                overflow: hidden;
                cursor: pointer;
            }

            .button:after {
                content: "";
                background: #90EE90;
                display: block;
                position: absolute;
                padding-top: 300%;
                padding-left: 350%;
                margin-left: -20px!important;
                margin-top: -120%;
                opacity: 0;
                transition: all 0.8s
            }

            .button:active:after {
                padding: 0;
                margin: 0;
                opacity: 1;
                transition: 0s
            }
            /* Disable certain interactions on touch devices */
            body { -webkit-touch-callout: none; -webkit-text-size-adjust: none; -webkit-user-select: none; -webkit-highlight: none; -webkit-tap-highlight-color: rgba(0,0,0,0); }
        </style>
    </head>
    <!-- NAVBAR
    ================================================== -->
    <body class="notranslate"><!-- WP-1115-Placement test export fails - added class notranslate  -->
        <div class="logo_alone">
        <div class="bg-white">
            <div class="container-fluid nopadding">
                <div class="row">
                    <div class="col-md-6 col-sm-4 col-xs-4">                            
                        <div class="navbar-header">
                            <div class="logo"> <a href="<?php echo ($this->acl_auth->logged_in()) ? site_url('site/dashboard') : base_url(); ?>" class="navbar-brand"><img src="<?php echo base_url() . '/public/images/logo_new.svg'; ?>" 
                                                                                                                                                                          class="img-responsive" alt="CATs Logo" /></a> </div>
                        </div>
                    </div>
                <?php if(isset($languages)&& !empty($languages)): ?>
                <div class="col-md-6 col-sm-8 col-xs-8">
                        <div class="langeage-box">
                             <div class="dropdown language_select" id="langDropdown">
                                <a class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="l-icon"></span>
                                    <?php
                                        foreach ($languages as $item):
                                            if ($item->code == $this->lang->lang()):
                                            echo json_decode('"'.$item->name.'"');
                                            endif;
                                        endforeach;
                                    ?>
                                </a>
                                <div class="dropdown-menu  dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                           <?php 
                           foreach ($languages as $item) {
                                                ?> <!-- CCC -131 - Condition changed to show only the basic languages by using content_status column in language--> 
                                                <a class="dropdown-item" href="<?=site_url("lang/$item->code")?>"><?php echo  json_decode('"'.$item->name.'"'); ?></a> 
                                                    <?php   } ?>
                       </div>
                            </div>
                            <?php if ($this->acl_auth->logged_in()) { ?>
                            <div class="langeage-box-logout">
                               <input class="loginbtn" type="submit" id="logoutbtn" value="<?php echo lang('app.site_label_drop_logout'); ?>" />
                            </div>
                            <?php } ?>
                        </div>
                 </div>
                <?php endif; ?>
                 </div>                               
            </div>
        </div>
        
        </div>
        <script language="javascript">
        function languagedropdown(gohere) {
            myLocation = "<?php echo base_url(); ?>/" + gohere;
            window.location = myLocation;
        }
        </script>
