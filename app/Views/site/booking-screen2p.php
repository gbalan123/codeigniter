<?php 
include_once 'header-booking.php';
use Config\Oauth;
$this->oauth = new Oauth(); 
?>

<?php 
    $practice_test_token = $practice_test_url = $final_test_token = $final_test_url = "";
    $practice_test_launch_btn = $final_test_launch_btn = "disabled";
    //WP-1202 Fetch the Practice Test test Launch details 
	if(isset($products[0]['practice_test']) && count($products[0]['practice_test']) > 0){
	    $practice_tests = $products[0]['practice_test'];
	    if(isset($practice_tests['launch_urls']) && count($practice_tests['launch_urls']) > 0){
	        $practice_test_url = $practice_tests['launch_urls']['practice_test1'];
	    }
	    if(isset($practice_tests['launch_btns']) && count($practice_tests['launch_btns']) > 0){
	        $practice_test_launch_btn = ($practice_tests['launch_btns']['practice_test1'] === 'enable') ? "" : "disabled";
	    }
	    if(isset($practice_tests['launch_tokens']) && count($practice_tests['launch_tokens']) > 0){
	        $practice_test_token = $practice_tests['launch_tokens']['practice_test1'];
	    }
	}
	//WP-1202 Fetch the Final Test test Launch details 
	if(isset($products[0]['final_test']) && count($products[0]['final_test']) > 0){
	    $final_tests = $products[0]['final_test'];
	    if(isset($final_tests['launch_urls']) && count($final_tests['launch_urls']) > 0){
	        $final_test_url = $final_tests['launch_urls']['final_test1'];
	    }
	    if(isset($final_tests['launch_btns']) && count($final_tests['launch_btns']) > 0){
	        $final_test_launch_btn = ($final_tests['launch_btns']['final_test1'] === 'enable') ? "" : "disabled";
	    }
	    if(isset($final_tests['launch_tokens']) && count($final_tests['launch_tokens']) > 0){
	        $final_test_token = $final_tests['launch_tokens']['final_test1'];
	    }
	}
	//$products[0]['final_test_section'] ="show";
	
?>
<style>
    .booking_screen_btns {
        padding: 4px 10px;
        display: block;
        margin: auto !important;
        float: none !important;
        font-size: 17px;
    }
    .final_msg .alert{
        margin: 10px 0 0;
        margin-bottom: 10px;
    }
</style>
<div class="bg-lightgrey">
    <div class="container">
    
        <div class="primary_dashboard">
            <div class="get_started">
                <div class="row">
                    <div class="col-sm-12">
                            <?php include "booking-tabs.php"; ?>
                        <?php if ((isset($products[0]['final_test_section'])) && ($products[0]['final_test_section'] == 'show')) { ?>
                        <div class="terms_condtion nav_dashboard final_test">
                            <ul class="nav nav-tabs">
                                <?php if ((isset($products[0]['final_test_section'])) && ($products[0]['final_test_section'] == 'show')) { ?>
                                    <li class="<?php echo  'active'; ?>"><a data-toggle="tab" href="#final_tab"><?php echo lang('app.language_site_booking_screen_2p_sec3_title'); ?> </a></li>
                                <?php } ?>

                            </ul>
                            <div class="tab-content">
                                    <div id="final_tab"  class="tab-pane final_tab fade in active">
                                        <?php if (null !== $this->session->getFlashdata('final_error')) { ?>
                                            <div class="final_error_msg">
                                                <div class="alert alert-danger alert-dismissible" role="alert">
                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                    <?php echo $this->session->getFlashdata('final_error'); ?>
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <div class="row">
                                            <div class="col-sm-12">
                                                <p><?php echo lang('app.language_site_booking_screen_2p_sec3_txt'); ?></p>
                                            </div>
                                            <div class="col-sm-12 text-center mt30">
                                                <input type="button" value="<?php echo lang('app.language_site_booking_screen_2p_final_test_btn'); ?>" onclick="window.open('<?php echo $final_test_url; ?>' <?php echo ($final_test_token == '') ? "" : ",'_self'"; ?>)" class="btn btn-sm btn-continue btn-continue-child booking_screen_btns" <?php echo ($final_test_url == "") ? "disabled" : $final_test_launch_btn; ?> />
                                                <input type="hidden" id="final_test_token" value= <?php echo base64_encode($final_test_token); ?>>

                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>        
                        <div class="main_tab_content">
                            <div class="tab-content">							

                                <div id="learning_tab" class="tab-pane start_learning fade in active">

                                    <div class="final_msg">
                                            <?php
                                        $tds_return_msg = $this->session->get('tds_return_msg');
                                        if(isset($tds_return_msg) && $tds_return_msg == true){
                                            include_once 'messages.php';
                                        $this->session->remove('tds_return_msg');
                                    }
                                    ?>
                                    </div>
                                    <div class="nav_dashboard">
                                        <div class="overflow_x">
                                        <ul class="nav nav-tabs">
                                                <li class="<?php echo ($this->session->getFlashdata('practice_error') || $this->session->getFlashdata('final_error') ) ? '' : 'active'; ?>"><a data-toggle="tab" href="#Preparation_tab"><?php echo lang('app.language_site_booking_screen_2p_sec1_title'); ?></a></li>
                                                <li class="<?php echo ($this->session->getFlashdata('practice_error')) ? 'active' : ''; ?>"><a data-toggle="tab" href="#practice_tab"><?php echo lang('app.language_site_booking_screen_2p_sec2_title'); ?></a></li>
                                        </ul>
                                        </div>
                                        <div class="start_learning_content institution_content">
                                            <div class="tab-content" id="catsQuizEngine">
                                                <div id="Preparation_tab"  class="tab-pane preparation_practice fade <?php echo ($this->session->getFlashdata('practice_error') || $this->session->getFlashdata('final_error') ) ? '' : 'in active'; ?>">
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                                <p><?php echo lang('app.language_site_booking_screen_2p_sec1_txt1'); ?></p>
                                                                <p><?php echo lang('app.language_site_booking_screen_2p_sec1_txt2'); ?></p>
                                                        </div>
                                                        <div class="col-sm-12 text-center mt30">

                                                                 <?php   
                                         //Webclient changes to display 'Opencourse' button according to the base url --START
                                                     /*$base_url_array = array("https://test.cats.education/", "https://uat.cats.education/", "http://172.17.0.217/cmedt/", "https://lt.cats.education/"); 
                                                     $base_url = base_url();

                                                     if (in_array($base_url, $base_url_array)){
                                                         if (isset($_SESSION['user_id'])){ ?>
                                                                                <input type="hidden" name="datasso" id="datasso" value="<?php echo @$encryptedToken; ?>"/>
                                                                                        <button type="button" id="primarycourseBtnWc" class="btn-main" style="padding: 4px 10px;margin: auto 32%;float: left;"><?php echo lang('wc_language_dashboard_open_course1'); ?></button><?php 
                                                            }
                                                     }*/ 
                                         //WP-807 Webclient changes to display 'Opencourse' in primry based on "Allow access to the web version of course" check option in admin. 15-02-2018--START
                                        if((isset($show_open_course)) && ($show_open_course == 'show')) {
                                            if(isset($buttonEnable) && $buttonEnable === 0) { ?>
                                                <button type="button" class="btn btn-sm btn-continue btn-continue-child booking_screen_btns" style="background-color:#ccc" disabled><?php echo lang('app.wc_language_dashboard_open_course1'); ?></button> <?php  
                                            } else { if (isset($is_mobile)) {

                                                $page_apps_id =  ($is_mobile['device_os'] == "IOS") ? $list_apps[5]['id'] : $list_apps[4]['id']; ?>
                                                <button type="button" class="primary-mobile-link btn btn-sm btn-continue btn-continue-child booking_screen_btns" data-toggle="modal" data-target="#mobile-playstore-link" data-backdrop="static" data-keyboard="false" data-id="<?php echo $page_apps_id; ?>" id="<?php echo $page_apps_id; ?>"><?php echo lang('app.wc_language_dashboard_open_course1'); ?></button>
                                            <?php } else { ?>
                                                <button type="button" id="primarycourseBtnWc" class="btn btn-sm btn-continue btn-continue-child booking_screen_btns"><?php echo lang('app.wc_language_dashboard_open_course1'); ?></button> <?php 
                                            }
                                        } }

                                          
                                        ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="practice_tab"  class="tab-pane practice_test fade <?php echo ($this->session->getFlashdata('practice_error')) ? 'active in' : ''; ?>">
                                                        <?php if(null !== $this->session->getFlashdata('practice_error')){ ?>
                                                    <div class="practice_error_msg">
                                                        <div class="alert alert-danger alert-dismissible" role="alert">
                                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                </button>
                                                                <?php echo $this->session->getFlashdata('practice_error'); ?>
                                                        </div>
                                                    </div>
                                                        <?php } ?>

                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                                <p><?php echo lang('app.language_site_booking_screen_2p_sec2_txt'); ?></p>
                                                        </div>
                                                        <div class="col-sm-12 text-center mt30">
                                                            <input type="button" value="<?php echo lang('app.language_site_booking_screen_2p_practice_test_btn'); ?>" onclick="window.open('<?php echo $practice_test_url; ?>' <?php echo ($practice_test_token == '') ? "" : ",'_self'"; ?>)" class="btn btn-sm btn-continue btn-continue-child text-right booking_screen_btns" <?php echo ($practice_test_url == "") ? "disabled" : $practice_test_launch_btn; ?> />
                                                            <input type="hidden" id="practice_test_token" value= <?php echo base64_encode($practice_test_token); ?>>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>						
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal for view mobile app Link -->
<div class="container">
    <div id="primary-playstore-link-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-xs">
            <div class="modal-content">
                <div class="modal-header" style="border-bottom: 0px solid #e5e5e5;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" style="font-weight: bold; text-align: center;">&nbsp;<img class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" />
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="modal-footer" style="border-top: 0px solid #e5e5e5;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer-booking.php'; ?>
<script>
//Webclient changes to login when click Opencourse button for Priamry user -- START
$(document).on("click", "#primarycourseBtnWc", function () {
   // var redirectWC = window.open("<?php // echo $this->config->item('primarywebclient_url'); ?>login.html", "_self");


    $.ajax({
        // url: '<?php  // echo $this->config->item('primarywebclient_url'); ?>api/ssologin.php?callback=?',
        // url: '<?php //  echo $this->oauth->primarywebclient_url; ?>api/ssologin.php?callback=?',
        url: '<?php echo $this->oauth->catsurl('primarywebclient_url'); ?>api/ssologin.php?callback=?',       
        type: 'GET',
        data: {data: JSON.stringify({WCLTK: '<?php echo @$encryptedToken; ?>', WCLTT: 'WEBCLIENT'})},
        dataType: 'jsonp',
        crossDomain: true,
        jsonpCallback: "logResults",
        success: function (result) {


            //var obj = $.parseJSON(result);
            var obj = result;
            localStorage.setItem('sso', 'enable');
            if (obj.code) {
                //redirectWC.location;
            	// window.location = "<?php // echo $this->oauth->primarywebclient_url; ?>login.html";
            	window.location = "<?php echo $this->oauth->catsurl('primarywebclient_url'); ?>login.html";
            }
        }
    });
});
//Webclient changes to login --END

//WP-1202 AJAX call to set latest Practice test token in PHP session
$(document).on("click", ".practice_test_token", function () {
	$.ajax({
        url: '<?php echo site_url('site/set_tdstoken_sessions'); ?>',
        type: 'POST',
        dataType: "JSON",
        data: {	latest_tds_token: $("#practice_test_token").val() },
        success: function (result) {
			console.log('Primary practice test token is set');
        }
    });
});
//WP-1202 AJAX call to set latest Final test token in PHP session
$(document).on("click", ".final_test_token", function () {
	$.ajax({
        url: '<?php echo site_url('site/set_tdstoken_sessions'); ?>',
        type: 'POST',
        dataType: "JSON",
        data: {	latest_tds_token: $("#final_test_token").val() },
        success: function (result) {
			console.log('Primary final test token is set');
        }
    });
});

//WC-15 - Divert learners to app version for non-desktop access
$('.primary-mobile-link').click(function(ev) {
    ev.preventDefault();
    var fetch_id = $(this).attr('id');
    $.get("<?php echo site_url('site/play_store_link'); ?>" + '/' + fetch_id, function(html) {
        $('#primary-playstore-link-modal .modal-body').html(html);
        $('#primary-playstore-link-modal').modal('show', {
            backdrop: 'static'
        });
    });
});

</script>


<script>
            $(document).ready(function() {
    tabScroll();
    tabScrollPrimary();
   
    });
            $(window).resize(function(){
    tabScroll();
    tabScrollPrimary();
    
    });
    
//width for main tab 
        function tabScroll(){          
           var listWidth = 0;                  
              $('.primary_dashboard .main_tab .nav.nav-tabs li').each(function(){
                  listWidth += $(this).outerWidth();
              })

        if($(window).width() < 768){
              $('.primary_dashboard .main_tab .nav-tabs').width(listWidth + 30) ;
              if(!$('.primary_dashboard .main_tab .overflow_x ul li:first-child').hasClass('active')){
              scrollLefts();
            }
        }
        else{
             $('.main_tab .nav-tabs').width('auto')  ;
        }
       }
        function scrollLefts(){
             $('.primary_dashboard .main_tab .overflow_x').scrollLeft(0);
             
             $('.primary_dashboard .main_tab .overflow_x').scrollLeft($('.primary_dashboard .main_tab .overflow_x ul li.active').siblings()[0].clientWidth + 100);
        }
      
//width for primary dashboard child

        function tabScrollPrimary(){          
           var listWidth = 0;                  
              $('.primary_dashboard .main_tab_content .nav.nav-tabs li').each(function(){
                  listWidth += $(this).outerWidth();
              })

        if($(window).width() < 768){
              $('.primary_dashboard .main_tab_content .nav-tabs').width(listWidth + 30) ;           
        }
        else{
             $('.primary_dashboard .main_tab_content .nav-tabs').width('auto')  ;
        }
       }
            

</script>
