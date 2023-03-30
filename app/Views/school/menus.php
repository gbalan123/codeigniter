<!-- NAVBAR
================================================== -->
<?php
  use App\Libraries\Acl_auth;
  use Config\Oauth;
  $this->acl_auth = new Acl_auth();
  $this->oauth = new \Config\Oauth(); 
  $this->session = \Config\Services::session();
  $this->zendesk_access = $this->oauth->catsurl('zendesk_access');
  $this->zendesk_domain_url = $this->oauth->catsurl('zendesk_domain_url');
?>

<div class="bg-white">
    <div class="container-fluid nopadding">
        <div class="navbar-header">
            <div class="logo"> 
                <a href="<?php echo ($this->acl_auth->logged_in()) ? site_url('school/dashboard') : site_url(); ?>" class="navbar-brand"><img src="<?php echo base_url() . '/public/images/logo_new.svg'; ?>" class="img-responsive" alt="CATs Logo" /></a> 
            </div>
        </div>
        <div class="langeage-box">
            <div class="dropdown language_select" id="langDropdown">
               <a class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                   <span class="l-icon"></span>
                   <?php
                    $this->lang = new \Config\MY_Lang();

                    if (is_array($languages) || is_object($languages))
                      {
                   foreach ($languages as $item):
                    
                       if ($item->code == $this->lang->lang()):
                           echo json_decode('"'.$item->name.'"');
                       endif;
                   endforeach;
                }
                   ?>
               </a>
               <div class="dropdown-menu  dropdown-menu-right" aria-labelledby="dropdownMenuButton">
               <?php
                   foreach ($languages as $item) { 
                   	 		?> <!-- CCC -131 - Condition changed to show only the basic languages by using content_status column in language -->
                       			<a class="dropdown-item" href="<?=site_url("lang/$item->code")?>"><?php echo  json_decode('"'.$item->name.'"'); ?></a> 
                            <?php } ?>
               </div>
           </div>
            <?php if ($this->acl_auth->logged_in()) { 		
                    if (strlen(ucfirst($this->session->get('user_firstname')." ".$this->session->get('user_lastname'))) > 5) {
                        $striped_user = mb_substr(ucfirst($this->session->get('user_firstname')." ".$this->session->get('user_lastname')), 0, 5) . '...';
					} else {
						$striped_user = mb_substr(ucfirst($this->session->get('user_firstname')." ".$this->session->get('user_lastname')),0,5,'UTF-8');
					}
                 ?>  
                <div class="dropdown user_logout" id="userDropdown">
                    <a class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-user fa-2x"></i><span class="user_name"><?php echo $striped_user; ?></span>
                    </a>
                    <div class="dropdown-menu  dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="<?php echo site_url('school/dashboard'); ?>"><span class="fa fa-dashboard " style="color: #fff;"></span>&nbsp;<?php echo lang('app.language_dashboard'); ?></a>
                        <?php if (null === $this->session->get('logged_tier1_userid') && $this->session->get('selected_tierid') == '') { ?>
                             <a class="dropdown-item" href="<?php echo site_url('school/profile'); ?>"><span class="fa fa-user " style="color: #fff;"></span>&nbsp;<?php echo lang('app.site_label_drop_profile'); ?></a>
                             <?php } if(isset($this->zendesk_access) && $this->zendesk_access == 1){?>
                            <?php  $user_session_id = (null !== $this->session->get('logged_tier1_userid') && $this->session->get('selected_tierid') != '') ? $this->session->get('logged_tier1_userid') : $this->session->get('user_id'); ?>
                                <input type="hidden" name="zen_user_id" id="zen_user_id" value="<?php echo $user_session_id; ?>">
                                <a class="dropdown-item zendesk" href="<?php echo @get_zend_desk_url($user_session_id);  ?>" target="_blank"><span class="fa fa-question-circle" style="color: #fff;"></span>&nbsp;<?php echo "Support" ?></a>
                            <?php   } ?>
                         
                        <a class="dropdown-item" id="logoutbtn" href="#"><span class="fa fa-sign-out" style="color: #fff;"></span>&nbsp;<?php echo lang('app.site_label_drop_logout'); ?></a>           
                    </div>
                </div>
            <?php } else { ?>
                <input name="" type="submit" class="loginbtn" value="<?php echo lang('app.login'); ?>"  data-toggle="modal" data-target="#loginModal" data-backdrop="static" data-keyboard="false">
            <?php } ?>
        </div>
		<?php if(null !== $this->session->get('logged_tier1_userid') && $this->session->get('selected_tierid') != ''){ ?>
		<div class="switch_accounts" style="margin: 25px 30px; display: block; float: right;">
			<h3 style="    float: left;line-height:46px;font-weight:500;margin:0 30px 0 10px;">
            <?php if(strlen($this->session->get('institute_name')) > 30){ ?>
                <a style="color:#252b2f;text-decoration: none;" href = "#" data-toggle="tooltip" data-placement="bottom" title = "<?php echo $this->session->get('institute_name');?>">
                <?php echo substr($this->session->get('institute_name'),0,30)."...";?> 
                </a>
            <?php }else{?>
                <?php echo $this->session->get('institute_name');?> 
            <?php }?>
            
            </h3>
			<button type="button" class="btn btn-sm btn-continue pull-right" id="tier_switch_account"><i class="fa fa-exchange " style="font-size: 19px;margin-right: 10px;vertical-align: middle;"></i>Switch Accounts</button>	
		</div>
		<?php }?>

        <div class="clear"></div>
    </div>

</div>
<script language="javascript">
function languagedropdown(gohere){
	
	myLocation = "<?php echo site_url(); ?>" + gohere;
	window.location = myLocation;
    
}
</script>
