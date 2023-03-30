<?php include_once 'header.php';  

?>

<!-- /.row -->
<style>
    p {
        color: red;
    }
    .error {
        color: red;
    }
    hr {
        border-top: 0px solid #eee;
    }
    .fixed-panel {
        min-height: 130px;
        max-height: 400px;
        overflow-y: scroll;
    }
    .table {
    width: 100%;
    max-width: 100%;
    margin-bottom: 0px !important;
}
 input, select, textarea {
   
    padding: 6px 12px;
    border: 1px solid #cccccc;
    border-radius: 4px;
   font-size: 14px;
    color: #555555;
}
.btn-primary {
    width: 75px;

}


</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading" style="padding: bottom 10px;px;">
              
            <h3><?php echo lang('app.language_admin_email_setting');?></h3>
                </div>
                <div class="clearfix"></div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="table-responsive table-bordered table-striped">
                    <!-- <form method="post" id="sender_form" action="<?php //echo site_url('admin/email_senders_update'); ?>"> -->
                        <table class="table" style="width: 100%;">
                            <thead>
                                <tr>
                                <th style="width: 30%;border-top: 1px solid #ddd;border-left: 1px solid #ddd;"><?php echo lang('app.language_admin_email_category');?></th>
                                    <th style="width: 40%;border-top: 1px solid #ddd;"><?php echo lang('app.language_admin_email_sender');?></th>
                                    <th style="width: 30%;border-top: 1px solid #ddd;border-right: 1px solid #ddd; padding-left:50px;"><?php echo lang('app.language_admin_email_action');?></th>
                                </tr>
                            </thead>
                                <tbody>
                                    <?php foreach($email_categorys as $category){?>
                                    <tr>
                                        <td><?php echo $category['category_name'];?></td>
                                        <td> <?php $sender_id = @get_sender_id_by_category($category['id']);?>
                                            <select class="form-control" id="sender_value_<?php echo $category['id']?>" name="sender_value_<?php echo $category['id']?>">
                                                <option value=""><?php echo "Please Select";?></option>
                                                <?php foreach($senders as $sender){?>
                                                    <option value="<?php echo $sender['id']; ?>" <?php echo ($sender_id['category_id'] == $category['id'] &&  $sender_id['sender_id']== $sender['id']) ? 'selected' : '' ?>><?php echo $sender['display_name'];?></option>
                                                <?php }?>
                                            </select>
                                        </td>
                                        <td style="padding-left:40px;">
                                        <button id="submitBtn_<?php echo $category['id']?>"  cat_id="<?php echo $category['id']?>" type="submit" class="btn btn-primary submit_email">Submit</button> 
                                        </td>  
                                    </tr>
                                    <?php }?>
                                </tbody>
                        </table>
                     <!-- </form> -->
                </div>

                <!--  second table-responsive -->
                <div class="text-right">
                
                </div>
            </div>
            
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-6-->
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading" style="padding: bottom 10px;px;">
            <h3><?php echo lang('app.language_admin_email_sender_list');?></h3>
                <div class="clearfix"></div>
            </div>
            
            <!-- /.panel-heading -->
            <div class="panel-body fixed-panel">
                <div class="table-responsive table-bordered table-striped">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width: 22%;border-top: 1px solid #ddd;border-left: 1px solid #ddd;"><?php echo lang('app.language_admin_email_category');?></th>
                                <th style="width: 18%;border-top: 1px solid #ddd;"><?php echo lang('app.language_admin_email_sender');?></th>
                            </tr>
                        </thead>
                        <?php if (!empty($senders_list)):?>
                        <tbody>
                            <?php foreach($senders_list as $list){ ?>
                                <tr>
                                    <td><?php echo $list['category_name'] ;?></td>
                                    <td><?php echo $list['display_name'] ;?> </td>  
                                </tr>
                            <?php } ?>
                        </tbody>
                        <?php else: ?>
                            <tbody>
                                <td colspan="7" style="text-align:center;"><b><?php echo "No Email Senders List Available"; ?></b></td>
                            </tbody>
                        <?php endif; ?>   
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-6-->
</div>

<div class="container">
    <div id="addupdateModal" class="modal fade" role="dialog" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content" >
                <img class="loading_main" style="display: none;" src="" />
                <div class="modal-header" style="border-bottom: 0px solid #e5e5e5;">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"
                        style="font-weight: bold; text-align: center;">
                </div>
                <div class="modal-body" >

                </div>
                <div class="modal-footer" style="border-top: 0px solid #e5e5e5;"></div>
            </div>
        </div>
    </div>

</div>

<div class="container">
    <div id="updateaddModal" class="modal fade" role="dialog" >
        <div class="modal-dialog modal-lg">
            <div class="modal-content" >
                <img class="loading_main" style="display: none;" src="" />
                <div class="modal-header" style="border-bottom: 0px solid #e5e5e5;">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"
                        style="font-weight: bold; text-align: center;">
                </div>
                <div class="modal-body" >

                </div>
                <div class="modal-footer" style="border-top: 0px solid #e5e5e5;"></div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php';  ?>
<script>
$(".submit_email").click(function(e) {
    e.preventDefault();
    var category_id = $(this).attr('cat_id');
    var submitButtonId = "#submitBtn_"+ category_id;
    $(submitButtonId).attr('disabled', true);
    $('#loading_in').show();
    var sender_id = "#sender_value_" + category_id;
    $.ajax({
        url: '<?php echo site_url('admin/email_senders_update'); ?>',
        type: 'POST',
        dataType: "json",
        data: {category_id: category_id, sender_id: $(sender_id).val()},
        success: function (data) {
            clear_errors(data);
            $(submitButtonId).attr('disabled', false);
            $('#loading_in').hide();
            if (data.success) {
                location.reload();
            }else {
                set_errors(data);
            }
        }
    });
})

function clear_errors(data){
	if (typeof (data.errors) != "undefined" && data.errors !== null) {
		for (var k in data.errors) {
            $('#' + k).next('p').remove();
		}
	}
}

function set_errors(data){
	if (typeof (data.errors) != "undefined" && data.errors !== null) {
		for (var k in data.errors) {
            $(data.errors[k]).insertAfter($("#" + k));
		}
	}
}
</script>

