<?php include_once 'header.php';  ?>


            <!-- /.row -->
            <div class="row">
      
                 <div class="col-lg-3 col-md-6">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <em class="fa fa-book fa-5x"></em>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php echo $product_count; ?></div>
                                    <div><?php echo lang('app.language_admin_products'); ?></div>
                                </div>
                            </div>
                        </div>
                        <a href="<?php echo site_url('admin/listproducts');?>">
                            <div class="panel-footer">
                                <span class="pull-left"><?php echo lang('app.language_admin_view_details'); ?></span>
                                <span class="pull-right"><em class="fa fa-arrow-circle-right"></em></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
        
                
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-warning">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <em class="fa fa-building fa-5x"></em>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php echo @$institute_count; ?></div>
                                    <div><?php echo lang('app.language_admin_institutions_heading'); ?></div>
                                </div>
                            </div>
                        </div>
                        <a href="<?php echo site_url('admin/institutions');?>">
                            <div class="panel-footer">
                                <span class="pull-left"><?php echo lang('app.language_admin_menu_institution_setup'); ?></span>
                                <span class="pull-right"><em class="fa fa-arrow-circle-right"></em></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
              
            </div>
           
            <!-- /.row -->
        

<?php include 'footer.php';  ?>
