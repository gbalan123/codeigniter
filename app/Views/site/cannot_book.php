<?php include_once 'header-booking.php'; ?>
<div class="bg-lightgrey">
    <div class="container">
        <div class="terms_condtion nav_dashboard">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#terms_and_condtion"><?php echo 'Your Next Step'; ?></a></li>

            </ul>
            <div class="tab-content">
                <div id="terms_and_condtion" class="tab-pane fade in active terms_tab">
                    <div class="row">
                        <div class="col-sm-12 col-s-12">
                            <!-- content display below -->
                            <p><?php echo lang('app.language_site_learner_cannot_book');?></p>
                            <div class="col-sm-12">
								<p class="p20"><a href="<?php echo site_url('site/dashboard'); ?>" ><span class="fa fa-long-arrow-left"></span> Go to dashboard</a></p>
							</div>
                            <!-- content display above-->
                        </div>
                    </div>
                </div>
            </div>
        </div>        
</div>
</div>

<?php include 'footer-booking.php'; ?>



