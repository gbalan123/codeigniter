<?php  $this->config = new \Config\MY_Lang();   ?>
<style>
.tier {
    min-height: 400px;
}
</style>
<?php 
if(isset($institute_search_item) && $institute_search_item != ''){
	$show_institutes = 'block';
	$enable_btn = 'checked';
}else{
	$show_institutes = 'none';
	$enable_btn = '';
}

if(null !== $this->session->get('tier_selected_option') && $this->session->get('tier_selected_option') == 'administer'){
	$show_institutes = 'block';
	$enable_btn = 'checked';
}else{
	$show_institutes = 'none';
	$enable_btn = '';
}

?>
<div class="bg-lightgrey">
    <div class="container">
		<div class="mt20">
			<?php include_once 'messages.php'; ?>
		</div>
		<div class="tier institution_page">
			<h1 class="user_name"><?php echo lang('app.language_tier2_dashboard_welcome'); ?> - <?php echo ucfirst($this->session->get('user_firstname')." ".$this->session->get('user_lastname')); ?></h1>
			<div class="institution_tab nav_dashboard" style="overflow:hidden;">
				<!-- Nav tabs -->
				<ul class="nav nav-tabs" role="tablist">
					<li id="tab_ord" role="presentation" class="active"><a href="#tier2_option" id="tab_tier2" aria-controls="home" role="tab" data-toggle="tab">Choose your option</a></li>
				</ul>
				<div class="institution_content">
				<div role="tabpanel" class="tab-pane  active" id="tier2_option">
					<div class="row" style="margin:0px;">
						<div class="pull-left radio_btn">
							<input type="radio" name="tier2_option" id="view_report" value="report" data-bv-field="product_type">
							<label for="view_report" style="font-weight: normal; margin-left: 10px; font-size: 15px;">View tier 2 reports</label>
						</div>
					</div>
					<div class="row" style="margin:0px;">
						<div class="pull-left radio_btn">
							<input type="radio" name="tier2_option" id="administer_institute" value="administer" <?php echo $enable_btn; ?> >
							<label for="administer_institute" style="font-weight: normal; margin-left: 10px; font-size: 15px;">Administer institutions</label>
						</div>
					</div>
					<div id="institute_list" class="mt30" style="overflow: hidden;display: block; display:<?php echo $show_institutes; ?>">
					<form class="form-inline" action="<?php echo site_url('tier2/dashboard'); ?>" id="searchForm_tier">
						<div class="form-group">
							<input maxlength="50" type="text" placeholder="Enter search term" name="institute_search" class="form-control clearable search" id="institute_search" value="<?php echo @$institute_search_item; ?>">
						</div>
						<button type="submit" class="btn btn-success">Search</button>
						<button type="button" id="institute_clearBtn" class="btn btn-default">Clear</button>
						<button type="button" disabled class="btn btn-sm btn-continue pull-right" data-toggle="modal" data-backdrop="static" data-keyboard="false" id="continue_btn_tier3">Continue</button>	
					</form>
					<div class="col-sm-12">
						<div class="table-responsive view-tokens mt40">
							<table class="table table-bordered institution_table">
								<thead>
								<tr>
								<th></th>
								<th>Name</th>
								<th>Address</th>
								</tr>
								</thead>
								<tbody>
									<?php
									if(isset($institute_data['institute_users']) && count($institute_data['institute_users']) > 0){?>
									<?php foreach ($institute_data['institute_users'] as $institute) {
										$institute_address_line2 = ($institute->address_line2) ? ', '.$institute->address_line2 : "";
										$institute_address_line3 = ($institute->address_line3) ? ', '.$institute->address_line3 : "";
										$region_name = ($institute->region_name) ? ', '.$institute->region_name : "";
										?>
										<tr>
											<td align="center"> <input type="radio" class="tier3_user" name="tier_id" value="<?php echo $institute->institutionTierId; ?>"></td>
											<td><?php echo $institute->organization_name; ?></td>
											<td><?php echo $institute->address_line1.$institute_address_line2.$institute_address_line3.' - '. $institute->postal_and_locality.$region_name; ?></td>
										</tr>
									<?php } ?>
									<?php }else{ ?>
										<td colspan="3">
											<div class="alert alert-danger fade in">
												<a style="text-decoration:none" href="#" class="close" data-dismiss="alert">&times;</a>
												No results were produced for the search term entered.
											</div>
										</td>
									<?php }?>
								</tbody>
							</table>
						</div>
						<div class="institution_pagination">
							<nav class="text-right">
								<?php if ($institute_data['pagination']) :?>
								<?= $institute_data['pagination']->links('pagination_tier2_institution_list') ?>
								<?php endif ?> 
							</nav>
						</div>
					</div>
					</div>
					<div class="pull-right mt30">
						<button type="button" disabled class="btn btn-sm btn-continue" data-toggle="modal" data-backdrop="static" data-keyboard="false" id="view_tier_report">Continue</button>					
					</div>	
				</div>
				</div>
			</div>	
		</div>
	</div>
</div>
<?php if(isset($this->zendesk_access) && $this->zendesk_access == 1){  ?>
	 <!--Start of cats66 Zendesk Widget script WP-1393 -->
	<script type="text/javascript">
		window.zESettings = {
			webWidget: {
				authenticate: {
					jwtFn: function(callback) {
						callback('<?php echo @get_web_widget_token($this->session->get('user_id'));?>');
					}
				}
			}
		};
	</script>
	<script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=7be50752-0a2f-49d7-95b8-873e359217de"> </script>
	<script type="text/javascript">
  		zE('webWidget', 'helpCenter:setSuggestions', { search: 'dashboard' });
	</script>
<?php }  ?>
