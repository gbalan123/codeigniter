<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Cron logs</title>
		<style type="text/css">/* /\/\/\/\/\/\/\/\/ CLIENT-SPECIFIC STYLES /\/\/\/\/\/\/\/\/ */
			#outlook a {
				padding: 0;
			} /* Force Outlook to provide a "view in browser" message */
			.ReadMsgBody {
				width: 100%;
			}
			.ExternalClass {
				width: 100%;
			} /* Force Hotmail to display emails at full width */
			.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {
				line-height: 100%;
			} /* Force Hotmail to display normal line spacing */
			body, table, td, p, a, li, blockquote {
				-webkit-text-size-adjust: 100%;
				-ms-text-size-adjust: 100%;
			} /* Prevent WebKit and Windows mobile changing default text sizes */
			table, td {
				mso-table-lspace: 0pt;
				mso-table-rspace: 0pt;
			} /* Remove spacing between tables in Outlook 2007 and up */
			p{
				margin-bottom: 20px;
			}
				/* Allow smoother rendering of resized image in Internet Explorer */
			/* /\/\/\/\/\/\/\/\/ RESET STYLES /\/\/\/\/\/\/\/\/ */
			body {
				margin: 0;
				padding: 0;
			}
			img {
				border: 0;
				height: auto;
				line-height: 100%;
				outline: none;
				max-width: 600px;
				text-decoration: none;
				-ms-interpolation-mode: bicubic;
			}

			@media screen and (min-width: 601px) {
				.container {
					width: 600px!important;
				}
			}

			@media screen and (max-width: 525px) {
				/* /\/\/\/\/\/\/ CLIENT-SPECIFIC MOBILE STYLES /\/\/\/\/\/\/ */
				body, table, td, p, a, li, blockquote {
					-webkit-text-size-adjust: none !important;
				} /* Prevent Webkit platforms from changing default text sizes */
				body {
					width: 100% !important;
					min-width: 100% !important;
				} /* Prevent iOS Mail from adding padding to the body */
				table {
					width: 100% !important;
					max-width: 100% !important;
					text-align: left !important;
				}
			}
		</style>
	</head>
<body bottommargin="0" leftmargin="0" offset="0" rightmargin="0" style="font-size: 14px; font-family: Arial, sans-serif; color: #444444;" topmargin="0"><!-- //Header Outer Table -->
	<table bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" style="padding: 0 10px 0; font-family: Helvetica, Arial, sans-serif; font-size: 14px;" width="100%">
		<tbody>
			<tr>
				<td align="center" valign="top"><!-- //Content Table -->
				<table border="0" cellpadding="0" cellspacing="0" class="container" style="max-width:600px!important; width:100%;">
					<tbody>
						<tr>
							<td align="left" style="width: 100%; padding: 10px 0;"><img alt="logo" src="<?php echo base_url('/public/images/logo_new.svg'); ?>" style="max-width: 400px; width: 100%;" /></td>
						</tr>
						<tr>
							<td style="height:20px; background: #1db9df; width: 100%;">&nbsp;</td>
						</tr>
						<tr>
							<td style="height:20px; background: #067dc1; width: 100%;">&nbsp;</td>
						</tr>
						<tr>
							<td style="padding-bottom: 10px; padding-top:20px;">
							<p>Dear Admin,</p>
								<div class="panel panel-success">
								<div class="panel-heading">
									<h3 class="panel-title"><?php echo lang('app.language_admin_successful_run'); ?>:</h3>
								</div>
								<div class="panel-body fixed-panel">
									<?php if(!empty($success_logs)): ?>
										<table class = "table">
										<tr>
											<th>Date</th>
											<th>Time</th>
											<th>Attempt</th>
											<th>Message </th>
										</tr>


										<?php foreach($success_logs as $log): ?>
											<?php if($log->status == 1): ?>
											<tr>
												<td><?php echo date('d-m-Y',$log->date_run); ?></td>
												<td><?php echo $log->time_run; ?></td>
												<td><?php echo $log->attempt; ?></td>
												<td><?php echo $log->message; ?></td>
											</tr>
												<?php endif; ?>
										<?php endforeach; ?>
									</table>
									<?php endif; ?>
								</div>
							</div>

							<div class="panel panel-danger">
								<div class="panel-heading">
									<h3 class="panel-title"><?php echo lang('app.language_admin_failure_run'); ?>:</h3>
								</div>
								<div class="panel-body fixed-panel">
										<?php if(!empty($failure_logs)): ?>
										<table class = "table">
										<tr>
											<th>Date</th>
											<th>Time</th>
											<th>Attempt</th>
											<th>Message </th>
										</tr>


										<?php foreach($failure_logs as $log): ?>
											<?php if($log->status == 0): ?>
												<tr>
													<td><?php echo date('d-m-Y',$log->date_run); ?></td>
													<td><?php echo $log->time_run; ?></td>
													<td><?php echo $log->attempt; ?></td>
													<td><?php echo $log->message; ?></td>
												</tr>
											<?php endif; ?>
										<?php endforeach; ?>
									</table>
									<?php endif; ?>
								</div>

							</div>  
							<p style="color: #1dbadf; font-size: 17px; margin-top:40px; margin-bottom:0px;">Best regards,</p>
							<p style="color: #1dbadf; font-size: 17px; margin-top:5px; ">CATs Step Team</p>
						</tr>
						<tr>
							<td style="width: 100%; padding: 15px 10px; border-top: solid 5px #117dc1; background: #000000; color: #b8b8b8; font-size: 12px;">&copy; CATs Step <?php echo date('Y')?></td>
						</tr>
					</tbody>
				</table>
	            </td>
			</tr>
		</tbody>
	</table>
</body>
</html>
