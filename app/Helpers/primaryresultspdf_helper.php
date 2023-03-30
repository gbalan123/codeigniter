<?php
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 
function generatePrimaryResultsZipPDF($userDetails = FALSE,$id = FALSE){
    $efsfile = new \Config\Efsfilepath();
    $efsfilepath = $efsfile->get_Efs_path();
    $bulk_download_path = $efsfilepath->efs_uploads_bulk_dwn;
    $efs_custom_log_path = $efsfilepath->efs_custom_log;
    define("LOG_FILE_PDF", $efs_custom_log_path . "pdf_log.txt");
    if($userDetails){
        ob_start();
        ?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>CATs Step Primary Results - Pdf</title>
		<link href="<?php echo base_url(); ?>/public/fonts/fonts.css" rel="stylesheet" type="text/css">
        <style>
		@page {
          margin: 10px !important;
        }
        html {
            height: 100% !important;
        }
        
        * {
            box-sizing: border-box !important;
        }
        
        p {
            margin: 0px !important;
			padding:0px !important;
			line-height:14px !important;
        }
        
        .clear {
            clear: both !important;
        }
        
        body {
            margin: 0 auto !important;
            padding: 0 !important;
            box-sizing: border-box !important;
            font: 14px Comic Sans Regular,Comic Sans Bold;
        }
        
        body>section {
            width: 100% !important;
            height: 100% !important;
            margin: 0 auto !important;
            overflow: hidden !important;
        }
        
        .content_area {
            padding: 30px !important;
            Width:100% !important;
        }
        
        .left_content {
            width: 60% !important;
            display:block !important;
            float:left !important;
        }
        
        .right_content {
            width: 40% !important;
            display:block !important;
            float:right !important;
        }
        
        .logo {
            width: 210px !important;
        }
        
        .logo>img {
            width: 100% !important;
        }
        
        .profile_area {
            padding:18px 30px !important;
            border-radius: 7px !important;
            margin-top: 30px !important;
            background: #067cc1 !important;
        }
        
        .profile_area p {
            color: #8ed3fb !important;
            font-size: 24px !important;
            font-weight: 200 !important;
            line-height: 24px !important;	
            font: 20px  Comic Sans Regular,Comic Sans Bold !important;	
        }
        
        .profile_area p:last-child {
            /*margin-top: 10px;*/
        }
        
        .profile_area p span {
           
            /* font-weight: 600 !important; */
            color: #ffffff !important;
            font: 20px  Comic Sans Regular,Comic Sans Bold  !important;
            font-weight: bold !important;
        }
        
        .exam_details {
            font-size: 18px !important;
            color: #404040 !important;
            margin-top: 60px !important;
            width: 100% !important;
            clear:left !important;
            
        }
        
        .exam_details p {
            padding: 5px 10px !important;
           
        }
        
        .exam_details p span {
            /* font-weight: 600 !important; */
             /* font-weight: bold; !important; */
            font: 18px  Comic Sans Regular,Comic Sans Bold  !important;
            font-weight: bold !important;
        }
        
        .balloon_area {
            padding-right: 17% !important;
            margin-top: -30px !important;
        }
        
        .balloon_area .balloon_top img {
            display: block !important;
        }
        
        .balloon_bottom {
            height: 160px !important;
            margin: 0px 13% !important;
        }
    
        .qr_code_area {
            padding: 5px 10px 10px 10px !important;
             /* background: url(<?php echo base_url(); ?>/public/images/balloon_bottom.png) no-repeat top center; */
              background: url("<?php echo base_url(); ?>/public/images/balloon_bottom.png") no-repeat top center !important; 
             /* background: url(/public/images/balloon_bottom.png) no-repeat top center;*/
            height: 157px !important;
        }
    
        .qr_code_area>p {
            text-align: center !important;
        }
    
        .qr_code_area>p:first-child {
            margin-bottom: 12px !important;
            font-size: 11px !important;
        }
    
        .qr_code_area>p:last-child {
            line-height: 8px !important;
        }
        
        .qr_code_area a {
            font-size: 9px !important;
            color: #1c97df !important;
            text-decoration:none !important;
        }
        
        .qr_code_area .qr_pic {
            height: 75px !important;
            margin: 5px auto !important;
            text-align:center !important;
        }
        
        .qr_code_area .qr_pic img {
            text-align:center !important;
        }
        
        .score_area {
            padding: 75px 25px 0 !important;
        }
        
        .score_area>div {
            text-align: center !important;
        }
        
        .score_area .score {
            width: 244px !important;
            height: 308px !important;
            padding-top: 65px !important;
            /* background: url(<?php // echo base_url(); ?>/public/images/wooden_board.png); */
            background: url("<?php echo base_url(); ?>/public/images/wooden_board.png") !important;
            background-repeat:no-repeat !important;
        }
    
         
        .score_area .score p {
            font-size: 26px !important;            
        }
        
        
        .score_area .score h2 {
            font-size: 46px !important;
            margin: 0 !important;
            line-height: 52px !important;
        }
         /* noted */
        .comments_area {
            position:relative !important;
            top:-140px !important;
            padding: 0px 10px !important;
        }
        
        .comments_area .cartoon {
            width: 65px !important;
        }
    
    
        /* testing  
        .comments_area .cartoon img {
            width: 100% !important;
        } */
    
        .cartoon_img {
            width: 100% !important;
        } 
         
        .cartoon_comments_area table {
            width: 100% !important;
        }
         
        .comment_arrow {
            width: 20px !important;
        }
        
        .comment_container {
            border-radius: 5px !important;
            overflow:hidden !important;
        }
        
        .comment_container_shadow {
            height: 10px !important;
        }
        
         .cartoon_comments_area td {
            padding: 0 !important;
        }
        
        table {
            border-spacing: 0 !important;
        }
        
        .comment_container p {
            font: 8px Comic Sans Regular,Comic Sans Bold !important;
            color: #666666 !important;
            font-size: 10px !important;
            /* line-height: 10px !important; */
            line-height: 1 !important;
            background: #ffffff !important;
            padding: 8px !important;
        }
        
        .comment_container p span {
            color: #000 !important;
            font: 12px Comic Sans Regular,Comic Sans Bold !important;
            font-weight: 600;
        }
        
        .comments_area>table:not(:first-child) {
            margin-top: -20px !important;
        }
    
       
        .score_txt{
            font-size: 20px !important;
            margin: 100px 0px 00px 70px !important;   
            /* float: right ;*/
            font-weight: bold;
        }
    
        
    
        .more{
            margin: 20px 0 0 20px !important; 
            padding: 5px 0 !important;
            text-align: center !important;
            width: 100% !important;                     
            background-color: #fff !important;
        }
    </style>
    </head>
	<body>
	    <section style="background:url('<?php echo base_url(); ?>/public/images/nature_bg.png') no-repeat top center;background-position: center;background-repeat: no-repeat;background-size: cover;">
		<div class="content_area">
			<div class="left_content">
				<div class="logo">
					<img src="<?php echo base_url(); ?>/public/images/cats_logo_pdf.png">
				</div>
				<div class="profile_area">
					<table>
						<tr>
							<td style="font-family : Comic Sans regular; padding-right:10px; vertical-align:top"><p>Level</p></td>
							<td><p><span><?php echo  $userDetails[0]->values->productname; ?></p></span></td>
						</tr>
						<tr>
							<td style="font-family : Comic Sans regular; padding:0px 10px 5px 0px; vertical-align:top"><p>Name</p></td>
							<td style="font-family: DejaVu Sans, sans-serif;padding:0px 0px 5px 0px;"><p><span><?php echo  $userDetails[0]->values->p_user_name; ?></span></p></td>
						</tr>
					</table>
				</div>
				<div class="exam_details">
    				<?php
    				    //check if booking available for Under13 - WP-1167
        				if($userDetails[0]->values->test_date != NULL){
        				    $date_of_exam = date('d F Y', strtotime($userDetails[0]->values->test_date));
        				}else{
        				    $date_of_exam = date('d F Y', strtotime('-1 day', strtotime($userDetails[0]->values->result_date)));
        				}
    				?>
					<p>Date of Exam: <span><?php echo $date_of_exam; ?></span></p>
					<p>Invigilation Status: <span><?php echo "Supervised"; ?></span></p>
				</div>
                            <div class="score_txt">
					<p><?php echo lang('app.language_pdf_certicate_assesment');?></p>
				</div>
			</div>
			<div class="right_content">
				<div class="balloon_area">
					<div class="balloon_top">
						<img src="<?php echo base_url(); ?>/public/images/balloon_top.png" width="100%">
					</div>
					<div class="balloon_bottom">
						<div class="qr_code_area">
							<p><?php echo  $userDetails[0]->values->thirdparty_id; ?></p>
                                                        <?php if($userDetails[0]->qr_url != "" && $userDetails[0]->google_url != ""){
                                                            $_url = explode("/", $userDetails[0]->qr_url);
                                                            $file_name = end($_url);
                                                            $parts = explode('.',$file_name);
                                                            
                                                            ?>
                                                            <div class="qr_pic"> <img style="width:75px" src="<?php echo @image_png($parts[0],$parts[1],'qrcodes'); ?>"></div>
                                                            <p><a href="<?php echo $userDetails[0]->google_url; ?>" target="_blank"><?php echo $userDetails[0]->google_url; ?></a></p>
                                                        <?php } ?>
						</div>
					</div>
				</div>
				<div class="score_area">
					<div class="score">
						<p>Overall Score</p>
						<h2><?php echo  $userDetails[0]->percentage; ?></h2>
					</div>
				</div>
			</div>
			<div class="clear"></div>
		</div>		
		<div class="comments_area">
			<table>
				<tbody>
					<tr>
						<td>
							<img style="width: 60px; height : 70px;" src="<?php echo base_url(); ?>/public/images/boy.png">
						</td>
						<td class="cartoon_comments_area">
							<table>
								<tbody>
									<tr>
										<td class="comment_arrow">
											<img src="<?php echo base_url(); ?>/public/images/left_arrow.png" width="100%">
										</td>
										<td class="comment_container">
											<p> <span style="line-height: 100%"><?php echo $userDetails[0]->leveltext["first"] . ' ' . $userDetails[0]->level . ' ' . $userDetails[0]->leveltext["first_1"]; ?></span> <?php echo $userDetails[0]->leveltext["second_1"]; ?>
											</p>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			<table>
				<tbody>
					<tr>
						<td class="cartoon_comments_area">
							<table>
								<tbody>
									<tr>
										<td class="comment_container">
											<p><span style=" font-family : Comic Sans Bold;line-height: 100%"><?php echo $userDetails[0]->leveltext["first"] . ' ' . $userDetails[0]->level . ' ' . $userDetails[0]->leveltext["first_2"]; ?></span> <?php echo $userDetails[0]->leveltext["second_2"]; ?>
											</p>
										</td>
										<td class="comment_arrow">
											<img src="<?php echo base_url(); ?>/public/images/right_arrow.png" width="100%">
										</td>
									</tr>
								</tbody>
							</table>
						</td>
						<td class="cartoon">
							<img class="cartoon_img" style="width: inherit;" src="<?php echo base_url(); ?>/public/images/girl_1.png">
						</td>
					</tr>
				</tbody>
			</table>
			<table>
				<tbody>
					<tr>
						<td class="cartoon">
							<img class="cartoon_img" style="width: inherit;" src="<?php echo base_url(); ?>/public/images/girl_2.png">
						</td>
						<td class="cartoon_comments_area">
							<table>
								<tbody>
									<tr>
										<td class="comment_arrow">
											<img src="<?php echo base_url(); ?>/public/images/left_arrow.png" width="100%">
										</td>
										<td class="comment_container">
											<p><span  style="line-height: 100%"><?php echo $userDetails[0]->leveltext["first"] . ' ' . $userDetails[0]->level . ' ' . $userDetails[0]->leveltext["first_3"]; ?></span> <?php echo $userDetails[0]->leveltext["second_3"]; ?>
											</p>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
                    <table width="100%">
				<tbody>
					<tr><td ><p class="more"><?php echo lang('app.language_pdf_certicate_find_out');?><?php echo lang('app.language_cats_step_website_link_primary_pdf');?></p></td></tr>
				</tbody>
			</table>
	   </div>
		</section>
	</body>

	</html>  
	
    <?php
        $pdf_content = ob_get_clean();
        $options = new Options();
        $options->set('isRemoteEnabled', TRUE);
        $options->set('isHtml5ParserEnabled', TRUE);
        $dompdf = new Dompdf($options);
        //echo $pdf_content; die;

        $dompdf->loadHtml($pdf_content);
        
        $dompdf->set_option('isRemoteEnabled', TRUE);
        $dompdf->set_option('isHtml5ParserEnabled', TRUE);  
        
        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->getOptions()->setIsFontSubsettingEnabled(true);
        
        // Render the HTML as PDF
        $dompdf->render();
        
        $name = $userDetails[0]->values->thirdparty_id;
        
        //Creating folder using taskid
        $dir = $bulk_download_path.$userDetails[0]->task_id;
        $dir1 = $bulk_download_path.$userDetails[0]->task_id.'/'.$userDetails[0]->file_name;
        $oldmask = umask(0);
        if (!is_dir($dir)){
            if (!mkdir($dir, 0777, TRUE)) {
                error_log(date('[Y-m-d H:i e]') . ' Primary folder fails '. $dir . PHP_EOL, 3, LOG_FILE_PDF);
            }else{
                error_log(date('[Y-m-d H:i e]') .'Primary folder created ' . $dir . PHP_EOL, 3, LOG_FILE_PDF);
                if (!mkdir($dir1, 0777, TRUE)) {
                    error_log(date('[Y-m-d H:i e]') . ' Primary folder fails '. $dir1 .PHP_EOL, 3, LOG_FILE_PDF);
                }else{
                    error_log(date('[Y-m-d H:i e]') .'Primary sub folder created ' . $dir1 . PHP_EOL, 3, LOG_FILE_PDF);
                }
            }
        }
        umask($oldmask);
        
        $path = $bulk_download_path.$userDetails[0]->task_id.'/'.$userDetails[0]->file_name.'/';
        //writing files inside folder using dompdf
        if ( !write_file($path . $name . '.pdf', $dompdf->output())){
            error_log(date('[Y-m-d H:i e]') .'Primary File not written' . $path . $name . '.pdf' . PHP_EOL, 3, LOG_FILE_PDF);
        }else{
            error_log(date('[Y-m-d H:i e]') .'Primary File written ' . $path . $name . '.pdf' . PHP_EOL, 3, LOG_FILE_PDF);
        }
    }
}

function generatePrimaryTdsResultsZipPDF($userDetails = FALSE,$id = FALSE){
    $efsfile = new \Config\Efsfilepath();
    $efsfilepath = $efsfile->get_Efs_path();
    $bulk_download_path = $efsfilepath->efs_uploads_bulk_dwn;
    $efs_custom_log_path = $efsfilepath->efs_custom_log;
    define("LOG_FILE_PDF", $efs_custom_log_path . "pdf_log.txt");
    if($userDetails){
        ob_start();
        ?>
<!DOCTYPE html>
<html>
    <head>
      
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>CATs Step Primary Results - Pdf</title>
		<!-- <link href="<?php // echo base_url(); ?>/public/fonts/fonts.css" rel="stylesheet" type="text/css"> -->
		<link href="<?php echo site_url('public/fonts/fonts.css') ?>"  rel="stylesheet" type="text/css">

        
        <style>
		@page {
          margin: 10px !important;
        }
        html {
            height: 100% !important;
        }
        
        * {
            box-sizing: border-box !important;
        }
        
        p {
            margin: 0px !important; 
			padding:0px !important;
			line-height:14px !important;
        }
        
        .clear {
            clear: both !important;
        }
        
        body {
            margin: 0 auto !important;
            padding: 0 !important;
            box-sizing: border-box !important;
			font: 14px Comic Sans Regular,Comic Sans Bold;
        }
        
        body>section {
            width: 100% !important;
            height: 100% !important;
            margin: 0 auto !important;
            overflow: hidden !important;
        }
        
        .content_area {
            padding: 30px !important;
			Width:100% !important;
        }
        
        .left_content {
            width: 60% !important;
			display:block !important;
			float:left !important;
        }
        
        .right_content {
            width: 40% !important;
			display:block !important;
			float:right !important;
        }
        
        .logo {
            width: 210px !important;
        }
        
        .logo>img {
            width: 100% !important;
        }
        
        .profile_area {
			padding:18px 30px !important;
			border-radius: 7px !important;
			margin-top: 30px !important;
			background: #067cc1 !important;
        }
        
        .profile_area p {
            color: #8ed3fb !important;
            font-size: 24px !important;
            font-weight: 200 !important;
		    line-height: 24px !important;	
		    font: 20px  Comic Sans Regular,Comic Sans Bold !important;	
        }
        
        .profile_area p:last-child {
            /*margin-top: 10px;*/
        }
        
        .profile_area p span {
           
            /* font-weight: 600 !important; */
            color: #ffffff !important;
            font: 20px  Comic Sans Regular,Comic Sans Bold  !important;
            font-weight: bold !important;
        }
        
        .exam_details {
            font-size: 18px !important;
            color: #404040 !important;
            margin-top: 60px !important;
			width: 100% !important;
			clear:left !important;
        }
        
        .exam_details p {
            padding: 5px 10px !important;
        }
        
        .exam_details p span {
            /* font-weight: 600 !important; */
             /* font-weight: bold; !important; */
            font: 18px  Comic Sans Regular,Comic Sans Bold  !important;
            font-weight: bold !important;
        }
        
        .balloon_area {
            padding-right: 17% !important;
            margin-top: -30px !important;
        }
        
        .balloon_area .balloon_top img {
            display: block !important;
        }
        
        .balloon_bottom {
            height: 160px !important;
            margin: 0px 13% !important;
        }

        .qr_code_area {
            padding: 5px 10px 10px 10px !important;
			  background: url("<?php echo base_url(); ?>/public/images/balloon_bottom.png") no-repeat top center !important; 
			height: 157px !important;
        }

        .qr_code_area>p {
            text-align: center !important;
        }

        .qr_code_area>p:first-child {
            margin-bottom: 12px !important;
            font-size: 11px !important;
        }

        .qr_code_area>p:last-child {
            line-height: 8px !important;
        }
        
        .qr_code_area a {
            font-size: 9px !important;
            color: #1c97df !important;
			text-decoration:none !important;
        }
        
        .qr_code_area .qr_pic {
            height: 75px !important;
			margin: 5px auto !important;
			text-align:center !important;
        }
        
        .qr_code_area .qr_pic img {
			text-align:center !important;
        }
        
        .score_area {
            padding: 75px 25px 0 !important;
        }
        
        .score_area>div {
            text-align: center !important;
        }
        
        .score_area .score {
            width: 244px !important;
			height: 308px !important;
			padding-top: 65px !important;
			background: url("<?php echo base_url(); ?>/public/images/wooden_board.png") !important;
			background-repeat:no-repeat !important;
        }

         
        .score_area .score p {
            font-size: 26px !important;            
        }
        
        
        .score_area .score h2 {
            font-size: 46px !important;
            margin: 0 !important;
            line-height: 52px !important;
        }

        .comments_area {
			position:relative !important;
			top:-140px !important;
			padding: 0px 10px !important;
        }

        
        .comments_area .cartoon {
            width: 65px !important;
        }

        .cartoon_img {
            width: 100% !important;
        } 
         
        .cartoon_comments_area table {
            width: 100% !important;
        }
         
        .comment_arrow {
            width: 20px !important;
        }
        
        .comment_container {
            border-radius: 5px !important;
            overflow:hidden !important;
        }
        
        .comment_container_shadow {
            height: 10px !important;
        }


        /* 2 */
        
         .cartoon_comments_area td {
            padding: 0 !important;
        }
        
        table {
            border-spacing: 0 !important;
        }
        
        .comment_container p {
            font: 8px Comic Sans Regular,Comic Sans Bold !important;
            color: #666666 !important;
            font-size: 10px !important;
            /* line-height: 10px !important; */
            line-height: 1 !important;
			background: #ffffff !important;
			padding: 8px !important;
        }
        
        .comment_container p span {
            color: #000 !important;
            font: 12px Comic Sans Regular,Comic Sans Bold !important;
            font-weight: 600;
        }
      
        .comments_area>table:not(:first-child) {
            margin-top: -20px !important;
        }

       
        .score_txt{
            font-size: 20px !important;
            margin: 100px 0px 00px 70px !important;   
            /* float: right ;*/
            font-weight: bold;
        }
    
        */

        .more{
            margin: 20px 0 0 20px !important; 
            padding: 5px 0 !important;
            text-align: center !important;
            width: 100% !important;                     
            background-color: #fff !important;
        }

   
    </style>
    </head>
	<body>
	    <section style="background:url('<?php echo base_url(); ?>/public/images/nature_bg.png') no-repeat top center;background-position: center;background-repeat: no-repeat;background-size: cover;">
		<div class="content_area">
			<div class="left_content">
				<div class="logo">
					<img src="<?php echo base_url(); ?>/public/images/cats_logo_pdf.png">
				</div>
				<div class="profile_area">
					<table>
						<tr>
							<td style="padding-right:10px; vertical-align:top"><p>Level</p></td>
							<td><p><span><?php echo  $userDetails[0]->values->productname; ?></p></span></td>
						</tr>
						<tr>
							<td style="padding:0px 10px 5px 0px; vertical-align:top"><p>Name</p></td>
							<td style="padding:0px 0px 5px 0px;"><p><span><?php echo  $userDetails[0]->values->p_user_name; ?></span></p></td>
						</tr>
					</table>
				</div>
				<div class="exam_details">
    				<?php
    				    //check if booking available for Under13 - WP-1167
                        if($userDetails[0]->values->start_date_time != NULL){
                            $date_of_exam = date('d F Y',strtotime($userDetails[0]->date_of_exam)); 
                        }else{
                            $date_of_exam = date('d F Y', strtotime($userDetails[0]->values->tds_result_date));
                        }
    				?>
					<p>Date of Exam: <span><?php echo $date_of_exam; ?></span></p>
					<p>Invigilation Status: <span><?php echo "Supervised"; ?></span></p>
				</div>
                            <div class="score_txt">
					<p><?php echo lang('app.language_pdf_certicate_assesment');?></p>
				</div>
			</div>
			<div class="right_content">
				<div class="balloon_area">
					<div class="balloon_top">
						<img src="<?php echo base_url(); ?>/public/images/balloon_top.png" width="100%">
					</div>
					<div class="balloon_bottom">
						<div class="qr_code_area">
							<p><?php echo  $userDetails[0]->values->tds_candidate_id; ?></p>
                                                        <?php if($userDetails[0]->qr_url != "" && $userDetails[0]->google_url != ""){
                                                            $_url = explode("/", $userDetails[0]->qr_url);
                                                            $file_name = end($_url);
                                                            $parts = explode('.',$file_name);
                                                            ?>
                                                            <div class="qr_pic"> <img style="width:75px" src="<?php echo @image_png($parts[0],$parts[1],'qrcodes'); ?>"></div>
                                                            <p><a href="<?php echo $userDetails[0]->google_url; ?>" target="_blank"><?php echo $userDetails[0]->google_url; ?></a></p>
                                                        <?php } ?>
						</div>
					</div>
				</div>
				<div class="score_area">
					<div class="score">
						<p>Overall Score</p>
						<h2><?php echo  $userDetails[0]->percentage; ?></h2>
					</div>
				</div>
			</div>
			<div class="clear"></div>
		</div>		
		<div class="comments_area">
			<table>
				<tbody>
					<tr>
						<td >
						     <img  style="width: 60px; height : 70px;" src="<?php echo base_url(); ?>/public/images/boy.png">  
						</td>
						<td class="cartoon_comments_area">
							<table>
								<tbody>
									<tr>
										<td class="comment_arrow">
											<img src="<?php echo base_url(); ?>/public/images/left_arrow.png" width="100%">
										</td>
										<td class="comment_container">
											<p> <span  style="line-height: 100%"><?php echo $userDetails[0]->leveltext["first"] . ' ' . $userDetails[0]->level . ' ' . $userDetails[0]->leveltext["first_1"]; ?></span> <?php echo $userDetails[0]->leveltext["second_1"]; ?>
											</p>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			<table>
				<tbody>
					<tr>
						<td class="cartoon_comments_area">
							<table>
								<tbody>
									<tr>
										<td class="comment_container">
											<p><span style=" font-family : Comic Sans Bold;"><?php echo $userDetails[0]->leveltext["first"] . ' ' . $userDetails[0]->level . ' ' . $userDetails[0]->leveltext["first_2"]; ?></span> <?php echo $userDetails[0]->leveltext["second_2"]; ?>
											</p>
										</td>
										<td class="comment_arrow">
											<img src="<?php echo base_url(); ?>/public/images/right_arrow.png" width="100%">
										</td>
									</tr>
								</tbody>
							</table>
						</td>
						<td class="cartoon">
							<img class="cartoon_img" style="width: inherit;"  src="<?php echo base_url(); ?>/public/images/girl_1.png">
						</td>
					</tr>
				</tbody>
			</table>
			<table>
				<tbody>
					<tr>
						<td class="cartoon">
							<img class="cartoon_img" style="width: inherit;" src="<?php echo base_url(); ?>/public/images/girl_2.png">
						</td>
						<td class="cartoon_comments_area">
							<table>
								<tbody>
									<tr>
										<td class="comment_arrow">
											<img src="<?php echo base_url(); ?>/public/images/left_arrow.png" width="100%">
										</td>
										<td class="comment_container">
											<p><span style="line-height: 100%"><?php echo $userDetails[0]->leveltext["first"] . ' ' . $userDetails[0]->level . ' ' . $userDetails[0]->leveltext["first_3"]; ?></span> <?php echo $userDetails[0]->leveltext["second_3"]; ?>
											</p>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
                    <table width="100%">
				<tbody>
					<tr><td ><p class="more"><?php echo lang('app.language_pdf_certicate_find_out');?><?php echo lang('app.language_cats_step_website_link_primary_pdf');?></p></td></tr>
				</tbody>
			</table>
	   </div>
		</section>
	</body>

	</html>  
	
    <?php
        $pdf_content = ob_get_clean();
        $options = new Options();
        $options->set('isRemoteEnabled', TRUE);
        $options->set('isHtml5ParserEnabled', TRUE);
        $dompdf = new Dompdf($options);
        //echo $pdf_content; die;

        $dompdf->loadHtml($pdf_content);
        
        $dompdf->set_option('isRemoteEnabled', TRUE);
        $dompdf->set_option('isHtml5ParserEnabled', TRUE);  
        
        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->getOptions()->setIsFontSubsettingEnabled(true);
        
        // Render the HTML as PDF
        $dompdf->render();
        
        $name = $userDetails[0]->values->tds_candidate_id;
        
        //Creating folder using taskid
        $dir = $bulk_download_path.$userDetails[0]->task_id;
        $dir1 = $bulk_download_path.$userDetails[0]->task_id.'/'.$userDetails[0]->file_name;
        $oldmask = umask(0);
        if (!is_dir($dir)){
            if (!mkdir($dir, 0777, TRUE)) {
                error_log(date('[Y-m-d H:i e]') . ' Primary folder fails '. $dir . PHP_EOL, 3, LOG_FILE_PDF);
            }else{
                error_log(date('[Y-m-d H:i e]') .'Primary folder created ' . $dir . PHP_EOL, 3, LOG_FILE_PDF);
                if (!mkdir($dir1, 0777, TRUE)) {
                    error_log(date('[Y-m-d H:i e]') . ' Primary folder fails '. $dir1 .PHP_EOL, 3, LOG_FILE_PDF);
                }else{
                    error_log(date('[Y-m-d H:i e]') .'Primary sub folder created ' . $dir1 . PHP_EOL, 3, LOG_FILE_PDF);
                }
            }
        }
        umask($oldmask);
        
        $path = $bulk_download_path.$userDetails[0]->task_id.'/'.$userDetails[0]->file_name.'/';
        //writing files inside folder using dompdf
        if ( !write_file($path . $name . '.pdf', $dompdf->output())){
            error_log(date('[Y-m-d H:i e]') .'Primary File not written' . $path . $name . '.pdf' . PHP_EOL, 3, LOG_FILE_PDF);
        }else{
            error_log(date('[Y-m-d H:i e]') .'Primary File written ' . $path . $name . '.pdf' . PHP_EOL, 3, LOG_FILE_PDF);
        }
    }
}
