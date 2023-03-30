
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>CATs Step Primary Results</title>
        <link href="<?php echo base_url(); ?>public/fonts/fonts.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="<?php echo base_url('public/css/primary_pdf_style.css'); ?>">
        <style>
            .score_txt{
                margin-top: 60px;
                font-size: 20px;                       
                font-weight: bold;
            }
          .more{
          margin: 60px 0 0 0;
          padding: 5px 0;
          text-align: center;
          width: 100%;
          background-color: #fff;
        }
		.logo {
            width: 210px;
        }
        </style>
    </head>
	<body>
	    <section>
            <!-- overall bg -->
    	    <div class="bg">
            	<img src="<?php echo base_url('public/images/nature_bg.png'); ?>">
        	</div>
        	
        	<div class="content_area">
	            <div> 
            	    <!-- Left side content -->
            	    <div class="left_content">
                    	<div class="logo">
	                        <img src="<?php echo base_url('public/images/cats_logo_pdf.png'); ?>">
                    	</div>
                    	<div class="profile_area">
	                        <div class="bg">
                            	<img src="<?php echo base_url('public/images/blue_bg.png'); ?>">
                        	</div>
                        	<table>
        						<tr>
        							<td style="padding-right:10px; vertical-align:top"><p>Level</p></td>
        							<td><p><span><?php echo $results['userDetails']['productname']; ?></p></span></td>
        						</tr>
        						<tr>
        							<td style="padding:0px 10px 5px 0px; vertical-align:top"><p>Name</p></td>
        							<td style="padding:0px 0px 5px 0px;"><p><span><?php echo  $results['userDetails']['name']; ?></span></p></td>
        						</tr>
        					</table>
                    	</div>
                    	<div class="exam_details">
                        	<?php
            				    //check if booking available for Under13 - WP-1167
                        	    if($results['userDetails']['start_date_time']  != NULL){
                        	       $date_of_exam = date('d F Y', strtotime($results['date_exam_event']));
                				}else{
                				    $date_of_exam = date('d F Y', strtotime('-1 day', strtotime($results['userDetails']['result_date'])));
                				}
            				?>
	                    	<p>Date of Exam: <span><?php echo $date_of_exam; ?></span></p>
							<p>Invigilation Status: <span><?php echo "Supervised"; ?></span></p>  

                                                        <div class="score_txt">
					<p><?php echo lang('app.language_pdf_certicate_assesment');?></p>
				</div>
                    	</div>
                	</div>
                	
                    <!-- right side content -->
                	<div class="right_content">
	                    <div class="balloon_area">
                        	<div class="balloon_top">
	                            <img src="<?php echo base_url('public/images/balloon_top.png'); ?>" width="100%">
                        	</div>
                        	<div class="balloon_bottom">
	                            <div class="qr_code_area">
                                	<p><?php echo $results['userDetails']['thirdparty_id']; ?></p>
									<?php $_url = explode("/", $results['qr_url']);
                   					$file_name = end($_url);
                   					$parts = explode('.',$file_name);  ?>
                                	<div class="qr_pic"> <img src="<?php echo @image_png($parts[0],$parts[1],'qrcodes'); ?>"></div>
                                	<p><a href="#"><?php echo $results['google_url']; ?></a></p>
                            	</div>	
                            	<div class="balloon_bottom_bg">
	                                <img src="<?php echo base_url('public/images/balloon_bottom.png'); ?>" width="100%">
                            	</div>
                        	</div>
                    	</div>
                    	<div class="score_area">
	                        <div>
                            	<img src="<?php echo base_url('public/images/wooden_board.png'); ?>" width="100%">
                            	<div class="score">
	                                <p>Overall Score</p>
                                	<h2><?php echo $results['percentage']; ?></h2>
                            	</div>
                        	</div>
                    	</div>
                	</div>
                	
                	<div class="clear"></div>
                	
                    <!-- cartoon area -->
                	<div class="comments_area">
                	
	                    <!-- 1st cartoon -->	
                    	<table>
	                        <tbody>
                            	<tr>
	                                <td class="cartoon">
                                    	<img src="<?php echo base_url('public/images/boy.png'); ?>">
                                	</td>
                                	<td class="cartoon_comments_area">
	                                    <table>
                                        	<tbody>
	                                            <tr>
                                                	<td class="comment_arrow">
	                                                    <img src="<?php echo base_url('public/images/left_arrow.png'); ?>" width="100%">
                                                	</td>
                                                	<td class="comment_container">
	                                                    <p><?php echo '<span>'.$results['leveltext']["first"] . ' ' . $results['userDetails']['level'] . ' ' . $results['leveltext']['first_1'] . '</span> ' . $results['leveltext']["second_1"]; ?></p>
                                                    	<div class="bg">
	                                                        <img src="<?php echo base_url('public/images/white_bg.png'); ?>">
                                                    	</div>
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
	                                                    <p><?php echo '<span>'.$results['leveltext']["first"] . ' ' . $results['userDetails']['level'] . ' ' . $results['leveltext']['first_2'] . '</span> ' . $results['leveltext']["second_2"]; ?></p>
                                                    	<div class="bg">
	                                                        <img src="<?php echo base_url('public/images/white_bg.png'); ?>">
                                                    	</div>
                                                	</td>	
                                                	<td class="comment_arrow">
	                                                    <img src="<?php echo base_url('public/images/right_arrow.png'); ?>" width="100%">
                                                	</td>
                                            	</tr>
                                        	</tbody>
                                    	</table>
                                	</td>
                                	<td class="cartoon">
	                                    <img src="<?php echo base_url('public/images/girl_1.png'); ?>">
                                	</td>
                            	</tr>
                        	</tbody>
                    	</table>
                    	
                    	<table>
	                        <tbody>
                            	<tr>
	                                <td class="cartoon">
                                    	<img src="<?php echo base_url('public/images/girl_2.png'); ?>">
                                	</td>
                                	<td class="cartoon_comments_area">
	                                    <table>
                                        	<tbody>
	                                            <tr>
                                                	<td class="comment_arrow">
	                                                    <img src="<?php echo base_url('public/images/left_arrow.png'); ?>" width="100%">
                                                	</td>
                                                	<td class="comment_container">
	                                                    <p><?php echo '<span>'.$results['leveltext']["first"] . ' ' . $results['userDetails']['level'] . ' ' . $results['leveltext']['first_3'] . '</span> ' . $results['leveltext']["second_3"]; ?></p>
                                                    	<div class="bg">
	                                                        <img src="<?php echo base_url('public/images/white_bg.png'); ?>">
                                                    	</div>
                                                	</td>
                                            	</tr>
                                        	</tbody>
                                    	</table>
                                	</td>
                            	</tr>
                        	</tbody>
                    	</table>
                    	<p class="more"><?php echo lang('app.language_pdf_certicate_find_out');?><?php echo lang('app.language_cats_step_website_link_primary');?></p>
                	</div>
                	
            	</div>
            	<div class="clear"></div>
        	</div>
    	</section>
	</body>

</html>