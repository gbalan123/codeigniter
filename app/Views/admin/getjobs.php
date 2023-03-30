<?php include_once 'header.php';  ?>
<?php 
    if(isset($test_type)){
        $action_url = 'admin/savePracticetestJobs';
    }else{
        $action_url = 'admin/saveJobs';
    }
?>
<!-- /.row -->
<style>
    .fixed-panel {
      min-height: 130px;
      max-height: 130px;
      overflow-y: scroll;
    }
</style>
<div class="row">
	
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading"><i class="fa fa-plus fa-fw"></i><?= esc($admin_heading) ?>
                            <p class="pull-right"><strong>Current Server Time: <?php echo date('H:i'); ?></strong> </p>  
                        </div>
			<div class="panel-body">
                                
				<div class="row">
					<div class="col-xs-4">
					
                                        <?php  
                                    
                                       $times = array(      '0' => '00:00',
                                                            '1' => '01:00',
                                                            '2' => '02:00',
                                                            '3' => '03:00',
                                                            '4' => '04:00',
                                                            '5' => '05:00',
                                                            '6' => '06:00',
                                                            '7' => '07:00',
                                                            '8' => '08:00',
                                                            '9' => '09:00',
                                                           '10' => '10:00',
                                                           '11' => '11:00', 
                                                           '12' => '12:00',
                                                           '13' => '13:00',
                                                           '14' => '14:00',
                                                           '15' => '15:00', 
                                                           '16' => '16:00', 
                                                           '17' => '17:00', 
                                                           '18' => '18:00', 
                                                           '19' => '19:00', 
                                                           '20' => '20:00', 
                                                           '21' => '21:00', 
                                                           '22' => '22:00', 
                                                           '23' => '23:00' );
                                        ?>   

                                               <div class="form-group">
                                                   <label for="name"><i class="fa fa-link">&nbsp;</i><?php echo 'URL'; ?>:</label> 
                                                         <div class="input-group">
                                                             <span class="input-group-addon">https://</span>
                                                             <input type="text" class="form-control" name="url" id="url" value="<?php echo isset($url) ? $url : ''; ?>" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="off" required><span class="input-group-addon" ><?php echo @$suffix; ?></span>
                                                            
                                                         </div> 
                                                   <div class="text-center"><p id="preview"></p></div>
                                                </div>             
						<div class="form-group">
       							<label for="name"><?php echo lang('app.language_admin_first_run'); ?>:</label> 
                                                         <div class="input-group">
                                                        <select name="first_run" class="form-control">
                                                            <?php foreach($times as $timeK => $timeV): ?>
                                                                <option <?php echo (@substr($jobs[0],2,2) == $timeK) ? 'selected="selected"' : ''; ?>  value="<?php echo $timeK; ?>"><?php echo $timeV; ?></option>
                                                            <?php endforeach; ?>
                                                        </select> <span class="input-group-addon"><i class="fa fa-clock-o"></i>&nbsp;hours</span>
                                                        </div>
                                                </div>
                                                <div class="form-group">
       							<label for="name"><?php echo lang('app.language_admin_second_run'); ?>:</label> 
                                                         <div class="input-group">
                                                        <select name="second_run" class="form-control">
                                                            <?php foreach($times as $timeK => $timeV): ?>
                                                                <option  <?php echo (@substr($jobs[1],2,2) == $timeK) ? 'selected="selected"' : ''; ?>   value="<?php echo $timeK; ?>"><?php echo $timeV; ?></option>
                                                            <?php endforeach; ?>
                                                        </select> <span class="input-group-addon"><i class="fa fa-clock-o"></i>&nbsp;hours</span>
                                                        </div>
                                                </div>
                                       
                                                <div class="form-group">
       							<label for="name"><?php echo lang('app.language_admin_third_run'); ?>:</label> 
                                                        <div class="input-group">
                                                        <select name="third_run" class="form-control">
                                                            <?php foreach($times as $timeK => $timeV): ?>
                                                                <option  <?php echo (@substr($jobs[2],2,2) == $timeK) ? 'selected="selected"' : ''; ?>  value="<?php echo $timeK; ?>"><?php echo $timeV; ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        
                                                            <span class="input-group-addon"><i class="fa fa-clock-o"></i>&nbsp;hours</span>
                                                        </div>
                                                </div> 
                                                <div class="form-group">
                                                    <label for="name"><?php echo lang('app.language_admin_fourth_run'); ?>:</label> 
                                                      <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" <?php echo ($mail_to['status'] == '1') ? 'checked="checked"': ''; ?>  name="mailto_check" value="1"  ><input type="email" name="cron_mailto"  class="form-control" value="<?php echo $mail_to['cron_mailto']; ?>" required > 
                                                        </label>
                                                      </div>
                                                </div>
                                              
						<button type="submit" class="btn btn-primary"><?php echo lang('app.language_admin_submit'); ?></button>
							
					</div>
                                    <div class="col-xs-8">
                                           
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
                                              <button type="button" class="btn btn-primary pull-right" onclick="window.location.reload()"><?php echo "Refresh"; ?></button>
                                             
                                     
				</div>
				<!-- /.row (nested) -->
			</div>
			<!-- /.panel-body -->
		</div>
		<!-- /.panel -->
	</div>
	<!-- /.col-lg-12 -->
</div>
<!-- /.row -->


<?php include 'footer.php';  ?>
    <script>
    $(function(){
        
        
       $('#url').keyup(function(){
           if($(this).val()!=''){
               
             var href = "<a href='"+'https://'+$(this).val().trim().replace(/ /g,'')+'.catsstep.education'+"' target='_blank' >"+'https://'+$(this).val()+'.catsstep.education'+"</a>"  
             $('#preview').html(href);
           }else{
               $('#preview').html('');
           }
       }); 
    });
    </script>
