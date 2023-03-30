<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title></title>
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
						img {
							-ms-interpolation-mode: bicubic;
						}
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
<!-- //Header Outer Table -->
<table bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" style="padding: 0 10px 0; font-family: Helvetica, Arial, sans-serif; font-size: 14px;" width="100%">
	<tbody>
		<tr>
			<td align="center" valign="top"><!-- //Content Table -->
			<table border="0" cellpadding="0" cellspacing="0" class="container" style="max-width:600px!important; width:100%;">
				<tbody>  
					<tr>    
						<td align="left" style="padding: 10px 0;"><img alt="logo" src="https://test.catsstep.education/public/images/logo_new.png" style="width: 245px; height: 60px;"/></td>
					</tr>
					<tr>
						<td style="border-top: 1px solid #ff6600;"> </td>
					</tr>
					<tr>
						<td style="padding-bottom: 10px;color:#252b2f;">
						<p>Dear <?php echo $user_name;?>,</p>
						<p><?php echo lang('app.language_site_email_contact_page_content1'); ?></p>
						<p><?php echo lang('app.language_site_email_contact_page_content2'); ?></p>
						<p style="color: #3066ac; font-size: 17px; margin-top:40px; margin-bottom:0px;"><?php echo lang('app.language_site_email_contact_page_regards'); ?></p>
						<p style="color: #3066ac; font-size: 17px; margin-top:5px; "><?php echo lang('app.language_admin_email_learner_forget_password_team'); ?></p>
						</td>
					</tr>
					<tr>
						<td style="width: 100%; padding: 15px 10px; background: #3066ac; color: #fff; font-size: 12px;"><?php echo lang('app.language_admin_email_footer'); ?></td>
					</tr>
				</tbody>
			</table>
			<!-- Content Table// --></td>
		</tr>
	</tbody>
</table>
</html>