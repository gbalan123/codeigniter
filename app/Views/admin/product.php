<?php include_once 'header.php';  
$this->lang = new \Config\MY_Lang();
?>

<style>
.modal .modal-body {
	max-height: 420px;
	overflow-y: auto;
}

.modal.modal-wide .modal-dialog {
	width: 90%;
}

.modal-wide .modal-body {
	overflow-y: auto;
}

.glyphicon-refresh-animate {
	-animation: spin .7s infinite linear;
	-webkit-animation: spin2 .7s infinite linear;
}

@
-webkit-keyframes spin2 {from { -webkit-transform:rotate(0deg);
	
}

to {
	-webkit-transform: rotate(360deg);
}

}
@
keyframes spin {from { transform:scale(1)rotate(0deg);
	
}

to {
	transform: scale(1) rotate(360deg);
}

}
.header-fixed {
	width: 100%
}

.header-fixed>thead,.header-fixed>tbody,.header-fixed>thead>tr,.header-fixed>tbody>tr,.header-fixed>thead>tr>th,.header-fixed>tbody>tr>td
	{
	display: block;
}

.header-fixed>tbody>tr:after,.header-fixed>thead>tr:after {
	content: ' ';
	display: block;
	visibility: hidden;
	clear: both;
}

.header-fixed>tbody {
	overflow-y: auto;
	height: 200px;
}

.header-fixed>tbody>tr>td,.header-fixed>thead>tr>th {
	width: 13%;
	float: left;
}
.table thead th  {
   text-align: center;   
}
</style>
<!-- /.row -->
<div class="row">
	<p class="lead">
	</p>
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<em class="fa fa-plus fa-fw"></em><?= esc($admin_heading) ?><a href="<?php echo site_url('admin/listproducts'); ?>" class="pull-right"><em class="fa fa-tasks fa-fw"></em><?php echo lang('app.language_admin_list_products'); ?></a>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-lg-6">
					<?php echo form_open_multipart('admin/previewproduct',array('class'=>'form bv-form','role'=>'form','id'=>'product_form','data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>
						<div class="form-group">
							<label for="name"><?php echo lang('app.language_admin_product_upload_label'); ?>
								<span>*</span> </label> <input type="file"
								class="form-control input-lg" name="products_csv"
								id="products_csv" required />
						</div>
						<button type="submit" id="preview-btn" class="btn btn-primary">
							<span class="glyphicon glyphicon-refresh" id="loading-preview"></span>
						<?php echo lang('app.language_admin_preview'); ?></button>
					<?php form_close(); ?>
                         &nbsp;&nbsp;&nbsp;<span class="fa fa-download">&nbsp;</span></i><a class="text-right" href="<?php echo site_url('admin/download_csv/products'); ?>" download>Sample products CSV</a>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
<!-- /.row -->
<!-- Modal -->
<div id="myModal" class="modal fade modal-wide" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">
				<?php echo lang('app.language_admin_product_upload_modal_dialog_header_label'); ?></h4>
			</div>
			<div class="modal-body" id="uploaded_csv_table_client">
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary pull-left"
					id="csv_upload_btn">
					<span class="glyphicon glyphicon-upload" ></span>
					<?php echo lang('app.language_admin_upload'); ?>
				</button>
				&nbsp;&nbsp;<img alt="loading" class="loading" style="display:none;" src="<?php echo base_url('public/images/loading.gif'); ?>">
			</div>
		</div>

	</div>
</div>

<?php include 'footer.php';  ?>
<script type="text/javascript">
$(document).ready(function() {

//product form validation
$('#product_form').bootstrapValidator({
		locale : "<?php echo $this->lang->lang(); ?>",
		 // List of fields and their validation rules
       fields: {

           products_csv: {
               validators: {
                   file: {
                       extension: 'csv',
                       contentType: 'application/vnd.ms-excel'
                      
                   }
               }
           }
          
       }
	
	 }).on('success.form.bv', function(e) {

			e.preventDefault();
			 var $form = $(e.target);
			 var formData = new FormData($(this)[0]);
			 $('#loading-preview').addClass('glyphicon-refresh-animate');	
			    $.ajax({
			        url: $form.attr('action'),
			        type: 'POST',
			        dataType : 'json',
			        data: formData,
			        success: function (data) {
			        	
			        	$('#loading-preview').removeClass('glyphicon-refresh-animate');	
			        	
	
				        completeFn(data);
						
				        $('#csv_upload_btn').click(function(){
							 
							 complete_after_previewFn(data);
							 return false;	
						}); 
			        	
			        },
			        cache: false,
			        contentType: false,
			        processData: false
			    });

			
			
			
		
	});	
});



function completeFn(results)
{
	
	if(results !==null && results.length!== 0)
	{
	rows = results.length;
	var headersize = 6;
        var e_names,e_ids, e_groups,e_levels,e_progressions, e_audiences = 0; 
	var extra_columns		  = [];
	var product_ids 		  = [];
	var product_names 		  = [];
	var product_levels 		  = [];
        
	var product_groups 		  = [ "A1", "A2", "B1", "B2" ];
	var product_progressions  = [];
	
	
	var ptable = "<table class='table table-bordered'><thead><tr><th><?php echo lang("app.language_admin_product_id"); ?></th><th><?php echo lang("app.language_admin_product_name"); ?></th><th><?php echo lang("app.language_admin_product_level"); ?></th><th><?php echo lang("app.language_admin_product_progression"); ?></th><th><?php echo lang("app.language_admin_product_pgroup"); ?></th><th><?php echo lang("app.language_admin_product_audience"); ?></th><th><?php echo lang("app.language_admin_product_error_description"); ?></th></tr></thead><tbody>"
	var ptag   = "<p class='text-center' style='color:red;'><?php echo lang('app.language_admin_distributor_empty_csv_failure'); ?></p>";
	console.log(rows);
	if(rows == 1){
		$('#uploaded_csv_table_client').append(ptag);
	}
		for(var j=1;j<rows;j++){
				if(results != ""){
                                      
                                        
					var product_errors = [];
					var product_id 	 		= (String(results[j].id)=='null') ? '' : $.trim(String(results[j].id));
					var product_name 		= (String(results[j].name)=='null') ? '' : $.trim(String(results[j].name));
					var product_level 		= (String(results[j].level)=='null') ? '' : $.trim(String(results[j].level)).toUpperCase();
					var product_progression         = (String(results[j].progression)=='null') ? '' : $.trim(String(results[j].progression));
					var product_group 		= (String(results[j].pgroup)=='null') ? '' : $.trim(String(results[j].pgroup)).toUpperCase();
					var product_audience            = (String(results[j].audience)=='null') ? '' : $.trim(String(results[j].audience));
					var rowcount 			= String(results[j].count);

										
					//conditions	
					
					//more columns added
					if(rowcount > 6){
						product_errors.push('<span style="color:red;">&nbsp;&nbsp;<?php echo lang("app.language_admin_product_csv_extra_column_error"); ?><span>');
					}

					//minimum columns added
					if( rowcount < 6)
					{
						product_errors.push('<span style="color:red;">&nbsp;&nbsp;<?php echo lang("app.language_admin_product_csv_minimum_column_error"); ?><span>');
						
					}
					
					//product id
					if(product_id == ''  )
					{
                                                e_ids = 1;
						product_errors.push('<span style="color:red;">&nbsp;&nbsp;<?php echo lang("app.language_admin_product_csv_id_column_empty_error"); ?><span>');
					}
					if(isNaN(product_id))
					{
						product_errors.push('<span style="color:red;">&nbsp;&nbsp;<?php echo lang("app.language_admin_product_csv_id_column_numeric_error"); ?><span>');
					}
					if(/^[a-zA-Z0-9]+$/.test(product_id) == false && e_ids != 1) {
						
						product_errors.push('<span style="color:red;">&nbsp;&nbsp;<?php echo lang("app.language_admin_product_csv_id_column_splchar_error"); ?><span>');
					}
					var idxists = $.inArray( product_id, product_ids);
					product_ids.push(product_id);
					if(idxists != -1 && idxists > -1 && product_id != '' && e_ids != 1){
						product_errors.push('<span style="color:red;">&nbsp;&nbsp;<?php echo lang("app.language_admin_product_csv_id_column_unique_error"); ?></span>');
					}
					
					//product names
					if(product_name == '' )
					{
                                                e_names = 1;
						product_errors.push('<span style="color:red;">&nbsp;&nbsp;<?php echo lang("app.language_admin_product_csv_name_column_empty_error"); ?><span>');
					}
					if(product_name.length > 50 && e_names!=1){
						product_errors.push('<span style="color:red;">&nbsp;&nbsp;<?php echo lang("app.language_admin_product_csv_name_column_length_error"); ?><span>');
					}
					var nameexists = $.inArray( product_name, product_names);
					product_names.push(product_name);
					if(nameexists != -1 && nameexists > -1 && product_name != '' && e_names!=1){
						product_errors.push('<span style="color:red;">&nbsp;&nbsp;<?php echo lang("app.language_admin_product_csv_name_column_unique_error"); ?></span>');
					}

					//product levels
					if(product_level == '' )
					{
                                                e_levels = 1;
						product_errors.push('<span style="color:red;">&nbsp;&nbsp;<?php echo lang("app.language_admin_product_csv_level_column_empty_error"); ?><span>');
					}
					if(product_level.length > 4 && e_levels!=1){
						product_errors.push('<span style="color:red;">&nbsp;&nbsp;<?php echo lang("app.language_admin_product_csv_level_column_length_error"); ?><span>');
					}
					var levelexists = $.inArray( product_level, product_levels);
					product_levels.push(product_level);
					if(levelexists != -1 && levelexists > -1 && product_level != '' && e_levels!=1){
						product_errors.push('<span style="color:red;">&nbsp;&nbsp;<?php echo lang("app.language_admin_product_csv_level_column_unique_error"); ?></span>');
					}
                 
					//product groups condition
					if(product_group == '' )
					{
                                                e_groups = 1; 
						product_errors.push('<span style="color:red;">&nbsp;&nbsp;<?php echo lang("app.language_admin_product_csv_group_column_empty_error"); ?><span>');
                                                
					}
					if(product_group.length > 2 && e_groups!= 1){
						product_errors.push('<span style="color:red;">&nbsp;&nbsp;<?php echo lang("app.language_admin_product_csv_group_column_length_error"); ?><span>');
					}
					if(/^[a-zA-Z0-9]+$/.test(product_group) == false  && e_groups!= 1) {
						product_errors.push('<span style="color:red;">&nbsp;&nbsp;<?php echo lang("app.language_admin_product_csv_group_column_splchar_error"); ?><span>');
					}
                                        
					var groupexists = $.inArray( product_group, product_groups);
					if(groupexists != -1 && groupexists > -1 && product_group != ''){
						
					}else if(e_groups!= 1){
						product_errors.push('<span style="color:red;">&nbsp;&nbsp;<?php echo lang("app.language_admin_product_csv_group_column_unique_error"); ?></span>');
					}

					
                                         
					// product progression conditions
					
					if(product_progression == '' )
					{
                                                e_progressions = 1;
						product_errors.push('<span style="color:red;">&nbsp;&nbsp;<?php echo lang("app.language_admin_product_csv_progression_column_empty_error"); ?><span>');
                                               
					}
					if(isNaN(product_progression) && e_progressions!=1){
						product_errors.push('<span style="color:red;">&nbsp;&nbsp;<?php echo lang("app.language_admin_product_csv_progression_column_numeric_error"); ?><span>');
					}
					if(/^[a-zA-Z0-9]+$/.test(product_progression) == false && e_progressions!=1) {
						
						product_errors.push('<span style="color:red;">&nbsp;&nbsp;<?php echo lang("app.language_admin_product_csv_progression_column_splchar_error"); ?><span>');
					}
					
					var progresionexists = $.inArray( product_progression, product_progressions);
					product_progressions.push(product_progression);
					if(progresionexists != -1 && progresionexists > -1 && product_progression != '' && e_progressions!=1){
						product_errors.push('<span style="color:red;">&nbsp;&nbsp;<?php echo lang("app.language_admin_product_csv_progression_column_unique_error"); ?></span>');
					}

					
					

					//product audience conditions
					if(product_audience == '')
					{
                                                e_audiences = 1;
						product_errors.push('<span style="color:red;">&nbsp;&nbsp;<?php echo lang("app.language_admin_product_csv_audience_column_empty_error"); ?><span>');
					}
					if(product_audience.length > 50 && e_audiences!=1)
					{
						product_errors.push('<span style="color:red;">&nbsp;&nbsp;<?php echo lang("app.language_admin_product_csv_audience_column_length_error"); ?><span>');
					}
					
					ptable += '<tr id="row_'+j+'"><td>' + product_id + '</td><td>' + product_name + '</td><td>' + product_level + '</td><td>' + product_progression + '</td><td>' + product_group + '</td><td>' + product_audience + '</td><td id="error_'+j+'">'+ product_errors+'</td></tr>';

					
					
				}
		
		}
	ptable += "</tbody></table>"
	
	
	
	

	$('#uploaded_csv_table_client').append(ptable);
	
	
	
	$('#myModal').modal({
	    backdrop: 'static',
	    keyboard: false
	    
	   
	}).on('hidden.bs.modal',function(){

		 	window.location = "<?php echo site_url('admin/product'); ?>";
		
	});

	}else{
		
		
		window.location = "<?php echo site_url('admin/csvcontentempty'); ?>";
		
	}
	
}


//process the insertion
function complete_after_previewFn(results)
{
	
	var rows = results.length;
	var extra_columns		  = [];
	var product_ids 		  = [];
	var product_names 		  = [];
	var product_levels 		  = [];
	var product_groups 		  = [ "A1", "A2", "B1", "B2" ];
	var product_progressions  = [];
	
	var psendData = new Array();
	for(var a=1;a<rows;a++){
		var product_errors = [];
		var product_id 	 		= (String(results[a].id)=='null') ? '' : $.trim(String(results[a].id));
		var product_name 		= (String(results[a].name)=='null') ? '' : $.trim(String(results[a].name));
		var product_level 		= (String(results[a].level)=='null') ? '' : $.trim(String(results[a].level)).toUpperCase();
		var product_progression         = (String(results[a].progression)=='null') ? '' : $.trim(String(results[a].progression));
		var product_group 		= (String(results[a].pgroup)=='null') ? '' : $.trim(String(results[a].pgroup)).toUpperCase();
		var product_audience            = (String(results[a].audience)=='null') ? '' : $.trim(String(results[a].audience));
		var rowcount 			= String(results[a].count);


		//validations 
		//product id
		if(product_id == '' )
		{
			product_errors.push('Product id should not be empty');
		}
		if(isNaN(product_id))
		{
			product_errors.push('Product id must be a number');
		}
		if(/^[a-zA-Z0-9]+$/.test(product_id) == false) {
			
			product_errors.push('Product id has special chars');
		}
		var idxists = $.inArray( product_id, product_ids);
		product_ids.push(product_id);
		if(idxists != -1 && idxists > -1 && product_id != ''){
			product_errors.push('Product id must be unique');
		}
		
		//product names
		if(product_name == '' )
		{
			product_errors.push('Product name should not be empty');
		}
		if(product_name.length > 50){
			product_errors.push('Product name value should not be above 50');
		}
		var nameexists = $.inArray( product_name, product_names);
		product_names.push(product_name);
		if(nameexists != -1 && nameexists > -1 && product_name != ''){
			product_errors.push('Product name must be unique');
		}

		//product levels
		if(product_level == '' )
		{
			product_errors.push('Product level should not be empty');
		}
		if(product_level.length > 4){
			product_errors.push('Level should be below 4');
		}
		var levelexists = $.inArray( product_level, product_levels);
		product_levels.push(product_level);
		if(levelexists != -1 && levelexists > -1 && product_level != ''){
			product_errors.push('Product level must be unique');
		}
                
		//product groups condition
		if(product_group == '' )
		{
			product_errors.push('Product group should not be empty');
		}
		if(product_group.length > 2){
			product_errors.push('Group should be below 2');
		}
		if(/^[a-zA-Z0-9]+$/.test(product_group) == false) {
			
			product_errors.push('Product group has special chars');
		}
		var groupexists = $.inArray( product_group, product_groups);
		if(groupexists != -1 && groupexists > -1 && product_group != ''){
			
		}else{
			
			product_errors.push('Invalid Product group (This should be Al,A2 or B1)');
		}

		

		// product progression conditions
		if(product_progression == '')
		{
			product_errors.push('Product progression should not be empty');
		}
		if(isNaN(product_progression)){
			product_errors.push('Progression must be a number');
		}
		if(/^[a-zA-Z0-9]+$/.test(product_progression) == false) {
			
			product_errors.push('Product progression has special chars');
		}
		var progresionexists = $.inArray( product_progression, product_progressions);
		product_progressions.push(product_progression);
		if(progresionexists != -1 && progresionexists > -1 && product_progression != ''){
			product_errors.push('Product progression must be unique');
		}

		//product audience conditions
		if(product_audience == '')
		{
			product_errors.push('Product audience should not be empty');
		}
		if(product_audience.length > 50)
		{
			product_errors.push('Product audience should not be above 50 char');
		}

		
		if(results[a].count === 6 && product_errors.length == 0 && results[a].id != '' && results[a].name !='' && results[a].level != '' && results[a].progression != '' && results[a].pgroup != '' && results[a].audience != '' ){
			
			psendData.push({
		        id				: results[a].id,
		        name                            : results[a].name,
		        level                           : results[a].level.toUpperCase(),
		        progression                     : results[a].progression,
		        pgroup                          : results[a].pgroup.toUpperCase(),
		        audience                        : results[a].audience
		        
		    });
		}
	}
	
	obj = {};
	obj.data =  JSON.stringify(psendData);
 	
        $('#csv_upload_btn').attr('disabled','disabled');
 	
        $('.loading').show();	
        
	 $.ajax({
		type : 'POST',
		url  : "<?php echo site_url('admin/postproduct'); ?>",
		data : obj,
		dataType: "json",
	    success: function(data){
                $('#csv_upload_btn').removeAttr('disabled');
                $('.loading').hide();	
	    	if (data.success == 0){
	    		window.location = "<?php echo site_url('admin/product'); ?>";
		    }else{
		    	window.location = "<?php echo site_url('admin/listproducts'); ?>";
			}
		    
		},
	    failure: function(errMsg) {
	        alert(errMsg);
	        return false;
	    }
	  });
	
}
function get_moodle_courses()
{
                            var moodle_courses = []; 
			    $.ajax({
			        url: '<?php echo site_url('admin/get_all_moodle_courses'); ?>',
			        type: 'POST',
			        dataType : 'json',
                                async: false,
			        success: function (data) {
			        		
                                        for(var a=0; a < data.length; a++){
                                            moodle_courses.push(data[a].course_shortname);
                                        }
                                        return moodle_courses;
			        }
			    }); 
     
      return moodle_courses;
}

</script>
