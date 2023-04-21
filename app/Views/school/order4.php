<div class="bg-lightgrey"> 
    <div class="container">
            <?php include_once 'messages.php'; ?>
                <h1 class="user_name"><?php echo lang('app.language_dashboard_welcome') . ', ' . ucfirst($this->session->get('user_firstname')." ".$this->session->get('user_lastname')); ?></h1>
                <div class="terms_condtion nav_dashboard nextstep">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#order_success">Order Success</a></li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div id="order_success" class="tab-pane fade in active terms_tab">
                            <div class="row">
                                <div class="col-sm-12 col-s-12">
                                    <!-- content display below -->
                                    <p><?php echo lang('app.language_school_order_success_msg1'); ?> </p>
                                    <p><?php echo lang('app.language_school_order_success_msg2'); ?> </p>			
                                    <?php if ($this->session->get('school_orderid')) { ?>
                                                <!--<a href="<?php echo site_url('school/tokenlist') . '/' . $this->session->get('school_orderid'); ?>" class="btn btn-lg btn-warning" >Download test tokens</a>-->
                                    <?php } ?> 
                                    <!-- content display above-->
                                    <div class="col-sm-12">
                                        <p class="pull-left p20"><a href="<?php echo site_url('school/dashboard'); ?>"><span class="fa fa-long-arrow-left"></span> <?php echo lang('app.language_search_events_back_to_dash'); ?></a></p>
                                    </div>
                                </div>
                            </div>
                        </div>		
                    </div>
                </div> 
    </div>
</div>