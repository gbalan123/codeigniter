<?php $this->lang = new \Config\MY_Lang(); ?>
<style>
    #institute_form p {
        color : red;
    }
    .form-actions {
        margin: 0;
        background-color: transparent;
        text-align: center;
    }
    .has-feedback label~.form-control-feedback {
        top: 0px;
    }
    .dropdown-menu
    {
        width: 100%; 
    }
</style>
<!-- /.row -->
<div class="row">
    <p class="lead"></p>
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <em class="fa fa-plus fa-fw"></em><?= esc($admin_heading) ?> <a
                    href="<?php echo site_url('admin/institutions'); ?>" class="pull-right"><em
                        class="fa fa-tasks fa-fw"></em><?php echo lang('app.language_admin_institutions_heading'); ?></a>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12 clearfix">
                        <?php if (isset($institute) && !empty($institute)): 
                            
                            ?>
                            <?php echo form_open_multipart('admin/postinstitute/' . $institute['id'], array('class' => 'form bv-form', 'role' => 'form', 'id' => 'institute_form', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>
                            <input type="hidden" name="institute_id"  id="institute_id" value="<?php echo $institute['id']; ?>"/>
                        <?php else: ?>
                            <?php echo form_open_multipart('admin/postinstitute', array('class' => 'form bv-form', 'role' => 'form', 'id' => 'institute_form', 'data-bv-feedbackicons-valid' => 'glyphicon glyphicon-ok', 'data-bv-feedbackicons-invalid' => "glyphicon glyphicon-remove", 'data-bv-feedbackicons-validating' => "glyphicon glyphicon-refresh")); ?>
                            <input type="hidden" name="institute_id" id="institute_id" value=""/>
                        <?php endif; ?>
                        <fieldset>

                            <legend><?php echo lang('app.language_admin_institutions_organisation_details'); ?></legend>
                            <div class="row">	
                                <div class="form-group col-xs-6 clearfix" >
                                    <label for="tierId"><?php echo 'Tier'; ?> <span>*</span></label>
                                    <?php
                                    if (isset($institute) && $institute['tierId'] != '') { ?>
                                        <input type="text" class="form-control" placeholder="<?php echo "Tier ". set_value('tier', isset($institute) ? $institute['tierId'] : ''); ?>" readonly>
                                        <input type="hidden" name="tier" id="tier" value="<?php echo set_value('tier', isset($institute) ? $institute['tierId'] : ''); ?>">
                                    <?php } else { ?>
                                        <select class="form-control" name="tier" id="tier" onchange="get_section(this.value)" >
                                            <option value=""><?php echo 'Please select a tier'; ?></option>
                                            <?php foreach ($tiers as $tier) : 
                                                ?>
                                               <option <?php echo set_select('tier', isset($institute) ? $institute['tierId'] : '', (isset($institute) && $institute['tierId'] == $tier->id ? TRUE : FALSE)); ?> value="<?php echo $tier->id; ?>"><?php echo $tier->name; ?></option>
                                            <?php endforeach; ?>

                                         
                                        </select>
                                  
                                    <?php } ?>
                                </div>
                                <div class="form-group col-xs-6 clearfix" >
                                    <label for="external_id"><?php echo lang('app.language_admin_institutions_external_id'); ?> </label> 
                                    <input type="text"
                                           class="form-control" name="external_id"  id="external_id" placeholder="<?php echo lang('app.language_admin_institutions_external_id'); ?>"
                                           value="<?php echo set_value('external_id', isset($institute) ? $institute['external_id'] : ''); ?>"
                                           <?php echo (isset($institute) && isset($institute['organisation_type']) && $institute['organisation_type'] == '6' ) ? 'readonly' : ''; ?> >
                                          

                                </div>
                            </div>
                            
                            <div class="row">	
                                <div class="form-group col-xs-6 clearfix" >
                                    <label for="organization_name"><?php echo lang('app.language_admin_institutions_name'); ?> <span>*</span></label> 

                                    <input type="text"
                                           class="form-control" name="organization_name" id="organization_name" placeholder="<?php echo lang('app.language_admin_institutions_name'); ?>"
                                           value="<?php echo set_value('organization_name', isset($institute) ? $institute['organization_name'] : ''); ?>"
                                           <?php echo (isset($institute) && isset($institute['organisation_type']) && $institute['organisation_type'] == '6' ) ? 'readonly' : ''; ?> >
                                

                                </div>
                                <div class="form-group col-xs-6 clearfix" >
                                    <label for="department"><?php echo lang('app.language_admin_institutions_department'); ?> </label> 

                                    <input type="text"
                                           class="form-control" name="department" id="department" placeholder="<?php echo lang('app.language_admin_institutions_department'); ?>"
                                           value="<?php echo set_value('department', isset($institute) ? $institute['department'] : ''); ?>"
                                           >
                                   

                                </div>
                            </div>	
                            <div class="row">
                                <div class="form-group col-xs-4 clearfix">
                                    <label for="address_line1" ><?php echo lang('app.language_admin_institutions_address_line1'); ?> <span>*</span></label> 

                                    <input type="text" placeholder="<?php echo lang('app.language_admin_institutions_address_line1'); ?>"
                                           class="form-control" name="address_line1" id="address_line1"
                                           value="<?php echo set_value('address_line1', isset($institute) ? $institute['address_line1'] : ''); ?>"
                                           >
                                        

                                </div>
                                <div class="form-group col-xs-4 clearfix">
                                    <label for="address_line2" ><?php echo lang('app.language_admin_institutions_address_line2'); ?></label>

                                    <input type="text" placeholder="<?php echo lang('app.language_admin_institutions_address_line2'); ?>"
                                           class="form-control" name="address_line2" id="address_line2"
                                           value="<?php echo set_value('address_line2', isset($institute) ? $institute['address_line2'] : ''); ?>">

                                </div>
                                <div class="form-group col-xs-4 clearfix">
                                    <label for="address_line3" ><?php echo lang('app.language_admin_institutions_address_line3'); ?> </label>

                                    <input type="text" placeholder="<?php echo lang('app.language_admin_institutions_address_line3'); ?>"
                                           class="form-control" name="address_line3" id="address_line3"
                                           value="<?php echo set_value('address_line3', isset($institute) ? $institute['address_line3'] : ''); ?>">

                                </div>

                            </div>
                            <div class="row">
                                <div class="form-group col-xs-4 clearfix">
                                    <label for="postal_and_locality" ><?php echo lang('app.language_admin_institutions_postal_and_locality'); ?> <span>*</span></label>

                                    <input type="text" placeholder="<?php echo lang('app.language_admin_institutions_postal_and_locality'); ?>"
                                           class="form-control" name="postal_and_locality" id="postal_and_locality"
                                           value="<?php echo set_value('postal_and_locality', isset($institute) ? $institute['postal_and_locality'] : ''); ?>">

                                </div>
                                <div class="form-group col-xs-4 clearfix">
                                    <label for="country" ><?php echo lang('app.language_admin_institutions_country'); ?>  <span>*</span></label>
                                    <select class="form-control" name="country" id="country"  onchange="get_regions(this.value)">
                                        <option value=""><?php echo 'Please select'; ?></option>
                                        <?php foreach ($countries as $country): ?>
                                            <option <?php echo set_select('country', isset($institute) ? $institute['country'] : '', ( isset($institute) && $institute['country'] == $country->countryCode ? TRUE : FALSE)); ?> value="<?php echo $country->countryCode; ?>"><?php echo $country->countryName; ?></option>
                                        <?php endforeach; ?>
										<?php foreach ($otherCountries as $country): ?>
                                            <option <?php echo set_select('country', isset($institute) ? $institute['country'] : '', ( isset($institute) && $institute['country'] == $country->countryCode ? TRUE : FALSE)); ?> value="<?php echo $country->countryCode; ?>"><?php echo $country->countryName; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group col-xs-4 clearfix">
                                    <label for="region" ><?php echo 'Region'; ?> <span>*</span></label>
                                    <select class="form-control" name="region" id="region" >
                                        <option value=""><?php echo 'Please select'; ?></option>
                                        <?php if(!empty($regions)){
                                                foreach ($regions as $region): 
                                                    $institute['region'] = $institute['region'] == NULL ? 'Please Select' : $institute['region']; ?>
                                                    <option <?php echo set_select('region', isset($institute) ? $institute['region'] : '', ( isset($institute) && $institute['region'] == $region->regionCode ? TRUE : FALSE)); ?> value="<?php echo $region->regionCode; ?>"><?php echo $region->name; ?></option>
                                        <?php 
                                           endforeach; 
                                           }
                                        ?>
                                    </select>
                                </div>

                            </div>
							<div class="row">
                              	<div class="form-group col-xs-4 clearfix">
                                    <label for="timezone" ><?php echo lang('app.language_admin_institutions_timezone'); ?> <span>*</span></label>
                                    <select class="form-control" name="timezone" id="timezone" >
                                        <option value=""><?php echo lang('app.language_admin_please_select'); ?></option>
                                        <?php foreach ($timezones as $value => $label): ?>
                                        	<option <?php echo set_select('timezone', isset($institute) ? $institute['timezone'] : '', ( isset($institute) && $institute['timezone'] == $value ? TRUE : FALSE)); ?> value="<?php echo $value; ?>"><?php echo $label; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                        	</div>
                        </fieldset>
                        <fieldset id="tier3_section"  <?php echo (isset($institute) && isset($institute['tierId']) && $institute['tierId'] == '3' ) ? 'style="display:block;"' : 'style="display:none;"' ?>>
                   <!--WP-1060 starts-->
                   <legend>The institution is able to offer the following product groups (select at least one): <span style="color:red;">*</span></legend>
                            <div class="row">
                                <div class="form-group col-xs-12 clearfix">
                                    <?php
                                        $institute_courseTypes = isset($institute_courseType)? array_map('current',$institute_courseType):'';
                                        foreach ($product_group as $product) {
                                            // WP-1202 TDS option added in Product eligibility section in Add/Update institution
                                            ?>
                                        <div class="row">
                                            <div class="col-xs-3">
                                        		<label class="checkbox-inline" style="padding:2px 50px;">
    	                                            <input class="product_group" name="product_group[]" type="checkbox" value="<?php echo $product['id']; ?>" <?php echo (isset($institute_courseType) && in_array($product['id'], $institute_courseTypes) ? "checked" : ''); ?> />
                                                	<strong> <?php echo $product['name']; ?> </strong>
    	                                    	</label>
    	                                    </div>
                                    
                                    	</div> <?php 
                                        } ?> 
                                   <p id="product_group">
                                </div>
                              
                            </div>
                    <!--WP-1060 ends-->
                            <legend><?php echo 'The organisation will be linked to the following tier 1 and tier 2 organisation.'; ?></legend>
                            <div class="row">
                                <div class="form-group col-xs-6 clearfix">
                                    <label for="tier1"><?php echo 'Tier 1'; ?></label> 
                                    <select class="form-control" name="tier1" id="tier1" >
                                        <option value=""><?php echo 'Please select'; ?></option>
                                          <?php foreach ($tiers_1 as $tier): ?>
                                             <option <?php echo set_select('tier1', isset($tier_relations[1]) ? $tier_relations[1]->TierID : '', ( isset($tier_relations[1]) && $tier_relations[1]->InstituteID == $tier->id ? TRUE : FALSE)); ?> value="<?php echo $tier->id; ?>"><?php echo $tier->organization_name; ?></option>
                                         <?php endforeach; ?>
                                    </select>
                                </div> 
                                <div class="form-group col-xs-6 clearfix">
                                    <label for="tier2"><?php echo 'Tier 2'; ?></label>
                                    <select class="form-control" name="tier2" id="tier2" >
                                        <option value=""><?php echo 'Please select'; ?></option>
                                         <?php foreach ($tiers_2 as $tier): ?>
                                             <option <?php echo set_select('tier2', isset($tier_relations[2]) ? $tier_relations[2]->TierID : '', ( isset($tier_relations[2]) && $tier_relations[2]->InstituteID == $tier->id ? TRUE : FALSE)); ?> value="<?php echo $tier->id; ?>"><?php echo $tier->organization_name; ?></option>
                                         <?php endforeach; ?>
                                    </select>
                                </div> 
                            </div>
                            <div class="row">
                                <div class="form-group col-xs-6 clearfix">
                                    <label for="language" ><?php echo 'Organisation type'; ?>  <span>*</span></label>
                                    <select class="form-control" name="institution_type" id="institution_type" >
                                        <option value=""><?php echo 'Please select'; ?></option>
                                        <?php foreach ($institution_groups as $type): ?>
                                            <option  <?php echo set_select('institution_type', isset($institute) ? $institute['organisation_type'] : '', ( isset($institute) && $institute['organisation_type'] == $type->institutionGroupId ? TRUE : FALSE)); ?> value="<?php echo $type->institutionGroupId; ?>"><?php echo $type->englishTitle; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group col-xs-6 clearfix">
                                    <label for="language" ><?php echo 'Default language for learner documents'; ?>  <span>*</span></label>
                                    <select class="form-control" name="language" id="language" >
                                        <option value=""><?php echo 'Please select'; ?></option>
                                        <?php foreach ($languages as $language): ?>
                                            <option <?php echo set_select('language', isset($institute) ? $institute['access_detail_language'] : '', ( isset($institute) && $institute['access_detail_language'] == $language->language_id ? TRUE : FALSE)); ?> value="<?php echo $language->language_id; ?>"><?php echo json_decode('"'.$language->name.'"'); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </fieldset>

                        <div class="form-group form-actions pull-right" style="clear:both;">
                            <?php if (isset($institute['email'])): ?>
                                <button type="button" id="<?php echo isset($institute) ? intval($institute['id']) : ''; ?>" class="btn btn-lg btn-success sendMailBtn wpsc_button"  style="pointer-events:none;"><?php echo lang('language_admin_institutions_resend_email_btn'); ?></button>
                            <?php endif; ?>
                            <button type="submit" id="submitBtn" class="btn btn-lg btn-primary wpsc_button" style="pointer-events:none;"><?php echo lang('app.language_admin_institutions_submit_btn'); ?></button><img id="loading_in" style="display: none;" src="<?php echo base_url('public/images/loading.gif'); ?>" alt="..." />
                        </div>						
                        <?php form_close(); ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<script>

function getSectors(organisation_type) {
    if (organisation_type == '6' && organisation_type != '') {
        $('#external_id').attr('readonly', true);
        $('#external_id').val('');
        $('#organization_name').attr('readonly', true);
        $('#organization_name').val('');
        $('#access_to_webversion').attr('checked', false);
        $('#access_to_webversion').attr('disabled', true);
        $('#sectorsDisplay').show();
    } else {
        $('#sectors').attr('selected', false);
        $('#sectorsDisplay').hide();
        $('#external_id').attr('readonly', false);
        $('#organization_name').attr('readonly', false);
        $('#access_to_webversion').attr('disabled', false);
    }
}

$('.sectors-multi').multiselect({
    buttonWidth: '100%',
    buttonText: function (options, select) {
        if (options.length === 0) {
            return "<?php echo lang('lsetting_please_select'); ?>";
        } else if (options.length >= 1) {
            return options.length + " <?php echo ($this->lang->lang() == 'ms') ? 'pilihan' : 'selected'; ?>";
        }
    }
});

$('#institute_form').submit(function (e) {

    $('#submitBtn').attr('disabled', true);
    $( ".help-block" ).empty();
    $('#loading_in').show();
    $.ajax({
        type: "POST",
        url: $(this).attr('action'),
        data: $(this).serialize(),
        dataType: 'json',
        success: function (data)
        {
            clear_errors(data);
            $('#submitBtn').attr('disabled', false);
            $('#loading_in').hide();
            if (data.success) {
                location.reload();
            } else {
                set_errors(data);
            }

        }
    });
    return false;
});

function clear_errors(data)
{
    if (typeof (data.errors) != "undefined" && data.errors !== null) {
        for (var k in data.errors) {
            if (k == 'sectors') {
                $('.multiselect').next('p').remove();
            } else {
                $('#' + k).next('p').remove();
            }
        }
    }
}

function set_errors(data)
{
    if (typeof (data.errors) != "undefined" && data.errors !== null) {
        for (var k in data.errors) {
            if (k == 'sectors') {
                $(data.errors[k]).insertAfter($('.multiselect'));
            } else {
                $(data.errors[k]).insertAfter($("#" + k));
            }
        }
    }
}

//resend email click
$('.sendMailBtn').click(function (e) {
    e.preventDefault();
    var result = confirm("Are you sure?");
    if (result) {
        obj = {};
        obj.institution_id = $(this).attr('id');
        $('#' + $(this).attr('id')).attr('disabled', true);
        $('#loading_in').show();
        $.ajax({
            type: "POST",
            url: "<?php echo site_url('admin/resend_institute_account_email'); ?>",
            data: obj,
            dataType: 'json',
            success: function (data)
            {
                $('#' + $(this).attr('id')).attr('disabled', false);
                $('#loading_in').hide();
                if (data.success) {
                    location.reload();
                } else {

                }

            }
        });

    }
    return false;
});

function get_section(val) {
    if (val == '3') {
        $('#tier3_section').show();
    } else {
        $('#tier3_section').hide();
    }

}

function get_regions(country_code){
    if(country_code != ''){
        $('#loading_c').show();
        
        $.ajax({
            type: "GET",
            url: "<?php echo site_url('admin/get_regions'); ?>"+'/'+country_code,
            dataType: 'json',
            success: function (data)
            {
                $('#loading_c').hide();
                if (data.success) {
                   $('#region').html(data.html);
                }
                if(data.available){
                   $('#region').attr('disabled',false);
                }else{
                   $('#region').attr('disabled',true);
                }

            }
        });
    }else{
        $('#region').attr('disabled',true);
        $('#region').html("<option value=''>Please select</option>"); 
    }
}
$(document).ready(function() {
    $('.product_group').on("click",function(){
        var product_val = $(this).val();
        if ($(this).prop('checked') == true){ 
            $(".tds_"+product_val).removeAttr('disabled');
        }
        if ($(this).prop('checked') == false){ 
            $(".tds_"+product_val).attr("disabled", "disabled");
            $(".tds_"+product_val).prop('checked', false);
        }
    });
});
</script>