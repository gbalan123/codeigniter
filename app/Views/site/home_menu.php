<?php 
use App\Libraries\Acl_auth;
use Config\MY_Lang;
$this->session = \Config\Services::session();
$this->acl_auth = new Acl_auth();
$this->lang = new \Config\MY_Lang();
$learnertype = $this->session->get('learnertype');
$url_element = $this->request->uri->getSegments();
$array_pages = ['cats_stepcheck', 'cats_steps', 'cats_solution', 'cats_stepcheck_employers', 'cats_stepcheck_education', 'cats_stepcheck_goverment', 'cats_stepcheck_format','about_us'];
?>
<!-- NAVBAR
================================================== -->

<?php if( (count($url_element) == 1) || ( isset($url_element[3]) && in_array($url_element[3],$array_pages)) ) { ?>
        <header class="fixed_header">
  <?php }?>
        <div class="bg-white">
    <div class="container-fluid">
        <div class="row">
        <div class="col-md-6 col-sm-4 col-xs-4">
            <div class="navbar-header">
            <div class="logo"> 
                <a href="<?php echo ($this->acl_auth->logged_in()) ? site_url('site/dashboard') : base_url(); ?>" class="navbar-brand"><img src="<?php echo base_url() . 'public/images/logo_new.svg'; ?>" class="img-responsive" alt="CATs Logo" /></a> 
            </div>
        </div>
        </div>
        <div class="col-md-6 col-sm-8 col-xs-8">
            <div class="langeage-box lang_custom">
            <div class="dropdown language_select" id="langDropdown">
                <a class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="l-icon">
                        <?php
                        foreach ($languages as $item):
                            if ($item->code == $this->lang->lang()):
                                echo json_decode('"' . $item->name . '"');
                            endif;
                        endforeach;
                        ?>
                    </span>

                </a>
                <div class="dropdown-menu  dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                    <?php foreach ($languages as $item) { ?>
                        <!-- CCC -131 - Condition changed to show only the basic languages by using content_status column in language-->
                       <a class="dropdown-item" href="<?=site_url('lang/'.$item->code)?>"><?php echo json_decode('"'.$item->name.'"'); ?></a>
                   <?php } ?>
                </div>
            </div>
            
        </div>
        </div>
        
        

    </div><div class="clear"></div>
       
    </div>

</div>
<?php if( (count($url_element) == 1) || ( isset($url_element[3]) && in_array($url_element[3],$array_pages)) ) { ?>
     </header>
<?php }?>



<script language="javascript">
    function languagedropdown(gohere) {
        myLocation = "<?php echo base_url(); ?>" + gohere;
        window.location = myLocation;
    }
</script>
