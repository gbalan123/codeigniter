<?php include_once 'header.php';  ?>
<!-- /.row -->
<style>
   .fixed-panel {
   min-height: 130px;
   max-height: 130px;
   overflow-y: scroll;
   }
   #results_tds .date_picker .form-group{
   margin-bottom : 5px;
   }
   #results_tds .date_picker p{
   font-size: 12px;
   font-weight: bold;
   margin: 0;
   color: #3A2FE1;
   }
</style>
<div class="row">
   <div class="col-md-12">
      <div class="panel panel-default">
         <div class="panel-heading"><em class="fa fa-envelope-o"></em><?php echo lang('app.language_admin_email_log_date_total'); ?> : <?php echo $success; ?> emails.</div>
         <div class="panel-body">
            <div class="row">
               <div class="col-md-12">
                     <form id="results_tds" action="<?php echo site_url('admin/get_mail_count'); ?>">
                        <div class="date_picker">
                           <div class="row mt20">
                              <div class="col-sm-4 col-xs-12">
                                 <div class="form-group">
                                    <label>Start Date</label>
                                    <div class="input-group">
                                       <input type="text" id='strd' class="form-control input-sm" name="strd" value="<?php if(!empty($mail_startdate)) echo $mail_startdate; ?>" />
                                       <label for='strd' class="input-group-addon">
                                       <span style="color:#3c1dd4;"class="glyphicon glyphicon-calendar"></span>
                                       </label>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-sm-4 col-xs-12">
                                 <div class="form-group">
                                    <label>End Date</label>
                                    <div class="input-group">
                                       <input type="text" id='endd' class="form-control input-sm" name="endd" value="<?php if(!empty($mail_enddate)) echo $mail_enddate; ?>"  />
                                       <label for='endd' class="input-group-addon">
                                       <span style="color:#3c1dd4;"  class="glyphicon glyphicon-calendar"></span>
                                       </label>
                                    </div>
                                 </div>
                                 <span class="time_error"style="color:#a94442;"></span>
                              </div>
                              <div class="col-sm-2 col-xs-12">
                                 <div class="form-group"style="margin-right:15px;margin-top:25px;">
                                    <div class="input-group">
                                       <button type="submit" class="btn btn-sm btn-primary wpsc_button" id="tds_results">Submit</button>	
                                    </div>
                                 </div>
                              </div>
                              <div class="clearfix"></div>
                           </div>
                        </div>
                     </form>
                     <!-- /.table-responsive -->
                  <div class="col- 12 pull-right" style="margin-bottom:4px;">
                     <?php if(!empty($results)){?>
                     <button type="button" class="btn btn-success wpsc_button" id="export_mail">
                     <em class="fa fa-download fa-fw"></em><?php echo lang('app.language_admin_email_log_date_csv'); ?></button>
                     <?php }else{ ?>
                     <button type="button" class="btn btn-success wpsc_button" id="export_mail"disabled>
                     <em class="fa fa-download fa-fw"></em><?php echo lang('app.language_admin_email_log_date_csv'); ?></button>
                     <?php } ?>
                  </div>
               </div>
      
                  <div class="panel-body">
                     <div class="table-responsive table-bordered table-striped">
                        <table class="table">
                           <thead>
                              <tr>
                                 <th><?php echo lang('app.language_admin_email_log_date_purpose'); ?></th>
                                 <th><?php echo lang('app.language_admin_email_log_date_from'); ?></th>
                                 <th><?php echo lang('app.language_admin_email_log_date_to'); ?></th>
                                 <th><?php echo lang('app.language_admin_email_log_date_time'); ?></th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php if(!empty($results)):?>
                              <?php foreach ($results as $result): ?>	
                              <tr>
                                 <td><?php echo $result['purpose']; ?></td>
                                 <td><?php echo $result['from_address']; ?></td>
                                 <td><?php echo $result['to_address']; ?></td>
                                 <td><?php echo date("d-m-Y H:i:s", strtotime($result['datetime'])); ?></td>
                                 <?php endforeach;?>
                           </tbody>
                           <?php else: ?>
                           <tr>
                              <td colspan="5">
                                 <div class="alert alert-danger fade in">
                                    <a href="#" class="close" data-dismiss="alert">&times;</a>
                                    <?php echo lang('app.language_admin_email_log_date_selected'); ?>
                                 </div>
                              </td>
                           </tr>
                           <?php endif; ?>
                        </table>
                     </div>
                     <div class="text-left">
                     <?php if (($pager)) :?>
                     <?php $pager->setPath($_SERVER['PHP_SELF']);?>
                     <?= $pager->links() ?>
                     <?php endif ?> 
                     </div>
                  </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php include 'footer.php';  ?>