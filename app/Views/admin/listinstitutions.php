<?php include_once 'header.php';
$this->request = \Config\Services::request();
?>
<style>
    hr{
        border-top: 0px solid #eee;
    }
    .clearable{
        background: #fff url(https://i.stack.imgur.com/mJotv.gif) no-repeat right -10px center;
        border: 1px solid #999;
        padding: 3px 18px 3px 4px;     /* Use the same right padding (18) in jQ! */
        border-radius: 3px;
        transition: background 0.4s;
    }
    .clearable.x  { background-position: right 5px center; } /* (jQ) Show icon */
    .clearable.onX{ cursor: pointer; }              /* (jQ) hover cursor style */
    .clearable::-ms-clear {display: none; width:0; height:0;} /* Remove IE default X */
    
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading" style="padding-bottom:25px;">
                <i class="fa fa-building fa-fw"></i><?= esc($admin_heading) ?>
                <form class="form-inline" action="<?php echo site_url('admin/institutions'); ?>" style="margin-top:20px;" id="searchForm" >
                    <div class="form-group" style="width:50%;">

                        <input maxlength="50" style="width:100%;" type="text" placeholder="<?php echo lang('app.language_admin_institutions_instituition_enter_search_term'); ?>" name="search" class="form-control clearable search" id="search" value="<?php echo @$search_item; ?>">
                    </div>
                    <button type="submit" class="btn btn-default" ><?php echo lang('app.language_admin_institutions_search_btn'); ?></button>
                    <button type="button" id="clearBtn" class="btn btn-default" ><?php echo 'Clear'; ?></button>

                    <div class="btn-group pull-right" style="margin-top:3px;">
                        <button type="button" class="btn btn-success wpsc_button"  style="pointer-events:none;" data-toggle="modal"  data-backdrop="static" data-keyboard="false"  data-target="#addupdateModal" id="addBtn">
                            <i class="fa fa-plus fa-fw"></i>
                            <?php echo lang('app.language_admin_institutions_add_btn'); ?></button>
                        <button type="button"

                                <?php echo (empty($results)) ? 'disabled' : '' ?>
                                class="btn btn-primary wpsc_button"  style="pointer-events:none;"   data-toggle="modal" data-backdrop="static" data-keyboard="false"  data-target="#updateaddModal" id="editBtn">
                            <i class="fa fa-edit fa-fw"></i>



                            <?php echo lang('app.language_admin_institutions_viewedit_btn'); ?></button>


                    </div>

                    <div class="clearfix"></div>
                </form>

            </div>

            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="table-responsive table-bordered table-striped">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>&nbsp;</th>
                                <th><?php echo lang('app.language_admin_institutions_name'); ?></th>
                                <th><?php echo lang('app.language_admin_institutions_id'); ?></th>
                                <th>
                                    <?php if (!empty($results) && count($results) > 1) { ?>
                                    <?php echo anchor(current_url() . "?order=" . (($this->request->getVar('order') == 'DESC') ? 'ASC' : 'DESC'), lang('app.language_admin_institutions_type') . (($this->request->getVar('order') == 'DESC') ? '&nbsp;<span class="glyphicon glyphicon-arrow-up"></span>' : '&nbsp;<span class="glyphicon glyphicon-arrow-down"></span>')); ?>
                                    <?php } else { ?>
                                        <?php echo lang('app.language_admin_institutions_type'); ?> 
                                    <?php } ?>
                                </th>

                                


                                <th><?php echo lang('app.language_admin_institutions_location'); ?></th>
                                <th><?php echo 'Region'; ?></th>
                                <th><?php echo 'Country'; ?></th>
                                <th><?php echo ''; ?></th>

                            </tr>
                        </thead>




                        <?php
                        if (!empty($results)):
                            $i = 0;
                            ?>

                            <tbody>

                                <?php foreach ($results as $result): 

                                // echo '<pre>'; print_r($result); echo '</pre>';
                                    
                                    ?>	
                                    <tr>
                                        <td><input type="radio" name="institutions_id"  value="<?php echo $result->id; ?>" <?php echo ($i == 0) ? 'checked="checked"' : ''; ?>  /></td>
                                        <td><?php echo $result->organization_name; ?></td>
                                        <td><?php echo ($result->external_id != '') ? $result->external_id : ''; ?></td>
                                        <td><?php echo ($result->englishTitle != '' && $result->organisation_type !='') ? $result->englishTitle : $result->TierName; ?></td>
                                        <td><?php echo $result->postal_and_locality; ?></td>
                                        <td><?php echo $result->regionName; ?></td>
                                        <td><?php echo $result->countryName; ?></td>
										<?php if($result->usercount > 0) { ?>
                                        <td><a href="<?php echo site_url('admin/list_tierusers/'.$result->id); ?>" ><?php echo 'Manage users' ?></a></td>
										<?php } else { ?>
                                        <td><a href="<?php echo site_url('admin/list_tierusers/'.$result->id); ?>" target="_top"><?php echo 'Add User'; ?></a></td>
										<?php } ?>
                                    </tr>
                                    <?php
                                    $i++;
                                endforeach;
                                ?>


                            </tbody>
                        <?php else: ?>
                            <tbody>
                            <td colspan="7" style="text-align:center;"><?php echo lang('app.language_admin_no_institutiions_available_msg'); ?></td>
                            </tbody>
                        <?php endif; ?>
                    </table>
                </div>
               
                <div class="text-center">
                    <?php if ($pagination) :?>
                    <?= $pagination->links('pagination_admin_institutions') ?>
                    <?php endif ?> 
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
                <img class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" />
                <div class="modal-header" style="border-bottom: 0px solid #e5e5e5;">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"
                        style="font-weight: bold; text-align: center;">

                        <?php //echo lang('language_distributor_add_venue');  ?></h4>
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
                <img class="loading_main" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" />
                <div class="modal-header" style="border-bottom: 0px solid #e5e5e5;">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"
                        style="font-weight: bold; text-align: center;">

                        <?php //echo lang('language_distributor_add_venue');  ?></h4>
                </div>
                <div class="modal-body" >

                </div>
                <div class="modal-footer" style="border-top: 0px solid #e5e5e5;"></div>
            </div>
        </div>
    </div>

</div>





<?php include 'footer.php'; ?>

<script>
    $(window).load(function(){
        $('.wpsc_button').css('pointer-events','all');
        $('.wpsc_button').removeClass('disabled');
    });
    $(function () {
        var institutions_id;

        institutions_id = $("input[name='institutions_id']:checked").val();

        $('input[type=radio][name=institutions_id]').change(function () {
            if (this.value != '') {
                institutions_id = this.value;
            } else {
                institutions_id = $("input[name='institutions_id']:checked").val();
            }
            return institutions_id;
        });

        //add
        $('#addBtn').click(function (e) {
            //e.preventDefault();
            $('.loading_main').show();
            $('#updateaddModal').modal('hide'); 
            $("#addupdateModal").on("shown.bs.modal", function (e) {
                $(this).find(".modal-body").load("<?php echo site_url('admin/institution'); ?>", function () {
                    $('.loading_main').hide();
                    $('.wpsc_button').css('pointer-events','all');
                    $('.wpsc_button').removeClass('disabled');
                    return false;
                });
            });

        });


        //edit
        $('#editBtn').click(function (e) {
            e.preventDefault();
            $('.loading_main').show();
            $('#addupdateModal').modal('hide'); 
            $("#updateaddModal").on("show.bs.modal", function () {
                $(this).find(".modal-body").load("<?php echo site_url('admin/institution'); ?>" + '/' + institutions_id, function () {
                    $('.loading_main').hide();
                    $('.wpsc_button').css('pointer-events','all');
                    $('.wpsc_button').removeClass('disabled');
                    return false;
                });
            });


        });
        
      
        $(document).on('hidden.bs.modal', function (e) {
            var target = $(e.target);
            target.removeData('bs.modal')
            .find(".modal-body").html('');
        });
 


        // CLEARABLE INPUT
        function tog(v) {
            return v ? 'addClass' : 'removeClass';
        }
        $(document).on('input', '.clearable', function () {
            $(this)[tog(this.value)]('x');
        }).on('mousemove', '.x', function (e) {
            $(this)[tog(this.offsetWidth - 18 < e.clientX - this.getBoundingClientRect().left)]('onX');
        }).on('touchstart click', '.onX', function (ev) {
            ev.preventDefault();
            $(this).removeClass('x onX').val('').change();
            window.location = "<?php echo site_url('admin/institutions'); ?>";
            //$(".results tbody tr").attr('visible','true').css({'display':'block'});
        });

        if ($('#search').val() != '') {
            $('#search').addClass('x');
        } else {
            $('#search').removeClass('x');
        }
        
        $('#clearBtn').on('click', function(){
             window.location = "<?php echo site_url('admin/institutions'); ?>";
        });

    });



</script>