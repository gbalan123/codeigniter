<!-- NAVBAR
================================================== -->

<?php  
use App\Libraries\Acl_auth;
use Config\Oauth;
$this->session = \Config\Services::session();
$this->acl_auth = new Acl_auth();
$this->lang = new \Config\MY_Lang();   
$this->oauth = new \Config\Oauth(); 
$this->zendesk_access = $this->oauth->catsurl('zendesk_access');
$this->zendesk_domain_url = $this->oauth->catsurl('zendesk_domain_url');
?>
<div class="bg-white">
    <div class="container-fluid nopadding">
        <div class="navbar-header">
            <div class="logo"> 
                <a href="<?php echo ($this->acl_auth->logged_in()) ? site_url('tier2/dashboard') : site_url(); ?>" class="navbar-brand"><img src="<?php echo base_url() . '/public/images/logo_new.svg'; ?>" class="img-responsive" alt="CATs Logo" /></a> 
            </div>
        </div>
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
                   <?php foreach ($languages as $item) { ?>
                       <a class="dropdown-item" href="<?=site_url('lang/'.$item->code)?>"><?php echo json_decode('"'.$item->name.'"'); ?></a>
                   <?php } ?>
               </div>
            </div>
            <?php if ($this->acl_auth->logged_in()) { 		
                    if (strlen(ucfirst($this->session->get('user_firstname'))) > 5) {
                        $striped_user = substr(ucfirst($this->session->get('user_firstname')), 0, 5) . '...';
					} else {
						$striped_user = mb_substr(ucfirst($this->session->get('user_firstname')),0,5,'UTF-8');
					}
                 ?>  
                <div class="dropdown user_logout" id="userDropdown">
                    <a class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-user fa-2x"></i> <?php echo $striped_user; ?>
                    </a>
                    <div class="dropdown-menu  dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="<?php echo site_url('tier2/dashboard'); ?>"><span class="fa fa-dashboard " style="color: #fff;"></span>&nbsp;<?php echo lang('app.language_dashboard'); ?></a>
						<a class="dropdown-item" href="<?php echo site_url('tier2/profile'); ?>"><span class="fa fa-user " style="color: #fff;"></span>&nbsp;<?php echo lang('app.site_label_drop_profile'); ?></a>
                        <?php if(isset($this->zendesk_access) && $this->zendesk_access == 1){ ?>
                            <input type="hidden" name="zen_user_id" id="zen_user_id" value="<?php echo $this->session->get('user_id'); ?>">
                            <a class="dropdown-item zendesk" href="<?php echo @get_zend_desk_url($this->session->get('user_id'));  ?>" target="_blank"><span class="fa fa-question-circle" style="color: #fff;"></span>&nbsp;<?php echo "Support" ?></a>
                        <?php } ?>
                        <a class="dropdown-item" id="logoutbtn" href="#"><span class="fa fa-sign-out" style="color: #fff;"></span>&nbsp;<?php echo lang('app.site_label_drop_logout'); ?></a>
                    </div>
                </div>
            <?php } else { ?>
                <input name="" type="submit" class="loginbtn" value="<?php echo lang('app.login'); ?>"  data-toggle="modal" data-target="#loginModal" data-backdrop="static" data-keyboard="false">
            <?php } ?>
        </div>

        <div class="clear"></div>
    </div>

</div>
<script language="javascript">
function languagedropdown(gohere){
	
	myLocation = "<?php echo base_url(); ?>" + gohere;
	window.location = myLocation;
    
}
</script>

