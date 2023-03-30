<style>
    #venue_form p {
        color : red;
    }
                .has-feedback label~.form-control-feedback {
        top: 19px!important;
    }
    .has-feedback .form-control {

    }
    .dropdown-menu
    {
        width: 100%; 
    }
    textarea {
        resize: none;
    }
</style>

<!-- /.row -->
<div class="row">
    <p class="lead"></p>
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-plus fa-fw"></i><?php echo $distributor_heading; ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12 clearfix">
                        <?php if (isset($venuedatas) && !empty($venuedatas)): ?>
                            <?php echo form_open_multipart('school/postvenue', array('class' => 'form bv-form', 'role' => 'form', 'id' => 'edit_venue_form', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>
                            <input type="hidden" class="edit_venue_id" name="venue_id"  id="venue_id" value="<?php echo $venuedatas[0]->id; ?>"/>
                        <?php else: ?>                                                  
                            <?php echo form_open_multipart('school/postvenue', array('class' => 'form bv-form', 'role' => 'form', 'id' => 'add_venue_form', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>
                            <input type="hidden" class="add_venue_id" name="venue_id" id="venue_id" value=""/>
                        <?php endif; ?>

                        <div class="row">	
                            <div class="form-group col-xs-12 clearfix" >
                                <label for="name"><?php echo lang('app.language_school_label_venue_name'); ?> <span>*</span></label>


                                <input type="text" style="width:817px" placeholder="<?php echo lang('app.language_school_label_venue_name'); ?>"
                                       class="form-control" name="venue_name"
                                       value="<?php echo set_value('venue_name', isset($venuedatas) ? $venuedatas[0]->venue_name : ''); ?>"
                                       required>

                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-xs-6 clearfix">
                                <label for="address_line1" ><?php echo lang('app.language_school_label_address1'); ?> <span>*</span></label> 

                                <input type="text" style="width:388px" placeholder="<?php echo lang('app.language_school_label_address1'); ?>"
                                       class="form-control" name="address_line1"
                                       value="<?php echo set_value('address_line1', isset($venuedatas) ? $venuedatas[0]->address_line1 : ''); ?>"
                                       required>

                            </div>
                            <div class="form-group col-xs-6 clearfix">
                                <label for="address_line2" ><?php echo lang('app.language_school_label_address2'); ?> </label>

                                <input type="text" style="width:388px" placeholder="<?php echo lang('app.language_school_label_address2'); ?>"
                                       class="form-control" name="address_line2"
                                       value="<?php echo set_value('address_line2', isset($venuedatas) ? $venuedatas[0]->address_line2 : ''); ?>">

                            </div>

                        </div>

                        <div class="row" >	
                            <div class="form-group col-xs-6 clearfix">
                                <label for="city"><?php echo lang('app.language_school_label_city'); ?> <span>*</span></label>

                                <input type="text" style="width:388px"
                                       class="form-control" name="city" placeholder="<?php echo lang('app.language_school_label_city'); ?>"
                                       value="<?php echo set_value('city', isset($venuedatas) ? $venuedatas[0]->city : ''); ?>"
                                       required>

                            </div>
                            <div class="form-group col-xs-3 clearfix">
                                <label for=country"><?php echo lang('app.language_school_label_country'); ?> <span>*</span></label>
                                <input type="text"
                                       class="form-control" name="country"
                                       value="<?php echo set_value('country', $venueCountry[0]->countryName); ?>"
                                       readonly	required>

                            </div>
                            <div class="form-group col-xs-3 clearfix" >
                                <label for="area_code"><?php echo lang('app.language_school_label_area_code'); ?> <span>*</span></label> 

                                <input type="text"
                                       class="form-control" name="area_code" placeholder="<?php echo lang('app.language_school_label_area_code'); ?>"
                                       value="<?php echo set_value('area_code', isset($venuedatas) ? $venuedatas[0]->area_code : ''); ?>"
                                       required>

                            </div>
                        </div> 

                        <fieldset>

                            <legend>Contact Details</legend>
                            <div class="row">
                                <div class="form-group col-xs-4 clearfix" >
                                    <label for="first_name"><?php echo lang('app.language_school_label_firstname'); ?> <span>*</span></label> <input type="text"
                                                                                                                                                 class="form-control" name="firstname"  placeholder="<?php echo lang('app.language_school_label_firstname'); ?>"
                                                                                                                                                 value="<?php echo set_value('firstname', isset($venuedatas) ? $venuedatas[0]->first_name : ''); ?>"
                                                                                                                                                 required>
                                                                                                                                                 <?php  //echo form_error('firstname'); ?>
                                </div>
                                <div class="form-group col-xs-4 clearfix">
                                    <label for="last_name"><?php echo lang('app.language_school_label_lastname'); ?> <span>*</span></label> <input type="text"
                                                                                                                                               class="form-control" name="lastname" placeholder="<?php echo lang('app.language_school_label_lastname'); ?>"
                                                                                                                                               value="<?php echo set_value('lastname', isset($venuedatas) ? $venuedatas[0]->last_name : ''); ?>"
                                                                                                                                               required>
                                                                                                                                               <?php  //echo form_error('lastname'); ?>
                                </div> 
                                <div class="form-group col-xs-4 clearfix">
                                    <label for="email"><?php echo lang('app.language_school_label_email'); ?> <span>*</span></label> 
                                    <input type="text" style="width:246px"
                                           class="form-control" name="email" placeholder="<?php echo lang('app.language_school_label_email'); ?>"
                                           value="<?php echo set_value('email', isset($venuedatas) ? $venuedatas[0]->email : ''); ?>"
                                           required>
                                </div> 
                            </div>
                            <div class="row">
                                <div class="form-group col-xs-4 clearfix">
                                    <label for="last_name"><?php echo lang('app.language_school_label_contact_no'); ?> <span>*</span></label> <input type="text"
                                                                                                                                                 class="form-control" name="contact_no" placeholder="<?php echo lang('app.language_school_label_contact_no'); ?>"
                                                                                                                                                 value="<?php echo set_value('contact_no', isset($venuedatas) ? $venuedatas[0]->contact_no : ''); ?>"
                                                                                                                                                 required>
                                                                                                                                                 <?php  //echo form_error('contact_no'); ?>
                                </div>
                                <div class="form-group col-xs-8 clearfix">
                                    <label><?php echo lang('app.language_school_label_location_URL'); ?> <span>*</span></label>
                                    <input class="form-control" style="width:531px"
                                           name="location_URL" 
                                           value="<?php echo set_value('location_URL', isset($venuedatas) ? $venuedatas[0]->location_URL : ''); ?>"
                                           required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-xs-12 clearfix">
                                    <label for="content"><?php echo lang('app.language_school_label_notes'); ?> </label>
                                    <textarea class="form-control" style="width:818px" name="notes" rows="2" cols="10"><?php echo set_value('notes', isset($venuedatas) ? $venuedatas[0]->notes : ''); ?></textarea>
                                </div>
                            </div>


                        </fieldset>
                        <div class="form-group text-right" style="clear:both;">
                            <?php if (isset($venuedatas) && !empty($venuedatas)): ?>
                                <button type="submit" id="edit_submitBtn"
                                        class="btn btn-sm btn-continue" ><?php echo lang('app.language_admin_institutions_submit_btn'); ?></button>
                                    <?php else: ?> 
                                <button type="submit" id="add_submitBtn" 
                                        class="btn btn-sm btn-continue" ><?php echo lang('app.language_admin_institutions_submit_btn'); ?></button>                                                 
                                    <?php endif; ?>

                            <button type="button" class="btn btn-sm btn-continue"  data-dismiss="modal">Cancel</button>
                        </div>	 
                        <?php form_close(); ?>
                        <img id="loading_in" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" />
                    </div>

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


