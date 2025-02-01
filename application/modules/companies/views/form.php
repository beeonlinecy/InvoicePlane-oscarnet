<?php
$cv = $this->controller->view_data['custom_values'];
?>

<script type="text/javascript">
    $(function () {
        $("#company_country").select2({
            placeholder: "<?php _trans('country'); ?>",
            allowClear: true
        });

        <?php $this->layout->load_view('companies/js/script_select_company_title.js'); ?>
    });
</script>

<form method="post">
    <input type="hidden" name="<?php echo $this->config->item('csrf_token_name'); ?>"
           value="<?php echo $this->security->get_csrf_hash() ?>">

    <div id="headerbar">
        <h1 class="headerbar-title"><?php _trans('company_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>
    <div id="content">
        <?php $this->layout->load_view('layout/alerts'); ?>
        <input class="hidden" name="is_update" type="hidden"
            <?php if ($this->mdl_companies->form_value('is_update')) {
                echo 'value="1"';
            } else {
                echo 'value="0"';
            } ?>
        >
        <div class="row">
            <div class="col-xs-12 col-sm-6">

                <div class="panel panel-default">
                    <div class="panel-heading form-inline clearfix">
                        <?php _trans('personal_information'); ?>
                        <div class="pull-right">
                            <label for="company_active" class="control-label">
                                <?php _trans('active_company'); ?>
                                <input id="company_active" name="company_active" type="checkbox" value="1"
                                    <?php if ($this->mdl_companies->form_value('company_active') == 1
                                        || ! is_numeric($this->mdl_companies->form_value('company_active'))
                                    ) {
                                        echo 'checked="checked"';
                                    } ?>>
                            </label>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="company_name">
                                <?php _trans('company_name'); ?>
                            </label>
                            <input id="company_name" name="company_name" type="text" class="form-control"
                                   autofocus
                                   value="<?php echo $this->mdl_companies->form_value('company_name', true); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="company_surname">
                                <?php _trans('company_surname_optional'); ?>
                            </label>
                            <input id="company_surname" name="company_surname" type="text" class="form-control"
                                   value="<?php echo $this->mdl_companies->form_value('company_surname', true); ?>">
                        </div>
                        <div class="form-group no-margin">
                            <label for="company_language">
                                <?php _trans('language'); ?>
                            </label>
                            <select name="company_language" id="company_language" class="form-control simple-select">
                                <option value="system">
                                    <?php _trans('use_system_language') ?>
                                </option>
                                <?php foreach ($languages as $language) {
                                    $company_lang = $this->mdl_companies->form_value('company_language');
                                    ?>
                                    <option value="<?php echo $language; ?>"
                                        <?php check_select($company_lang, $language) ?>>
                                        <?php echo ucfirst($language); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="panel panel-default">

                    <div class="panel-heading">
                        <?php _trans('address'); ?>
                    </div>

                    <div class="panel-body">
                        <div class="form-group">
                            <label for="company_address_1"><?php _trans('street_address'); ?></label>

                            <div class="controls">
                                <input type="text" name="company_address_1" id="company_address_1" class="form-control"
                                       value="<?php echo $this->mdl_companies->form_value('company_address_1', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="company_address_2"><?php _trans('street_address_2'); ?></label>

                            <div class="controls">
                                <input type="text" name="company_address_2" id="company_address_2" class="form-control"
                                       value="<?php echo $this->mdl_companies->form_value('company_address_2', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="company_city"><?php _trans('city'); ?></label>

                            <div class="controls">
                                <input type="text" name="company_city" id="company_city" class="form-control"
                                       value="<?php echo $this->mdl_companies->form_value('company_city', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="company_state"><?php _trans('state'); ?></label>

                            <div class="controls">
                                <input type="text" name="company_state" id="company_state" class="form-control"
                                       value="<?php echo $this->mdl_companies->form_value('company_state', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="company_zip"><?php _trans('zip_code'); ?></label>

                            <div class="controls">
                                <input type="text" name="company_zip" id="company_zip" class="form-control"
                                       value="<?php echo $this->mdl_companies->form_value('company_zip', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="company_country"><?php _trans('country'); ?></label>

                            <div class="controls">
                                <select name="company_country" id="company_country" class="form-control">
                                    <option value=""><?php _trans('none'); ?></option>
                                    <?php foreach ($countries as $cldr => $country) { ?>
                                        <option value="<?php echo $cldr; ?>"
                                            <?php check_select($selected_country, $cldr); ?>
                                        ><?php echo $country ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <!-- Custom Fields -->
                        <?php foreach ($custom_fields as $custom_field): ?>
                            <?php if ($custom_field->custom_field_location != 1) {
                                continue;
                            } ?>
                            <?php print_field($this->mdl_companies, $custom_field, $cv); ?>
                        <?php endforeach; ?>
                    </div>

                </div>

            </div>
            <div class="col-xs-12 col-sm-6">

                <div class="panel panel-default">

                    <div class="panel-heading">
                        <?php _trans('contact_information'); ?>
                    </div>

                    <div class="panel-body">
                        <div class="form-group">
                            <label for="company_phone"><?php _trans('phone_number'); ?></label>

                            <div class="controls">
                                <input type="text" name="company_phone" id="company_phone" class="form-control"
                                       value="<?php echo $this->mdl_companies->form_value('company_phone', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="company_fax"><?php _trans('fax_number'); ?></label>

                            <div class="controls">
                                <input type="text" name="company_fax" id="company_fax" class="form-control"
                                       value="<?php echo $this->mdl_companies->form_value('company_fax', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="company_mobile"><?php _trans('mobile_number'); ?></label>

                            <div class="controls">
                                <input type="text" name="company_mobile" id="company_mobile" class="form-control"
                                       value="<?php echo $this->mdl_companies->form_value('company_mobile', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="company_email"><?php _trans('email_address'); ?></label>

                            <div class="controls">
                                <input type="text" name="company_email" id="company_email" class="form-control"
                                       value="<?php echo $this->mdl_companies->form_value('company_email', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="company_web"><?php _trans('web_address'); ?></label>

                            <div class="controls">
                                <input type="text" name="company_web" id="company_web" class="form-control"
                                       value="<?php echo $this->mdl_companies->form_value('company_web', true); ?>">
                            </div>
                        </div>

                        <!-- Custom fields -->
                        <?php foreach ($custom_fields as $custom_field): ?>
                            <?php if ($custom_field->custom_field_location != 2) {
                                continue;
                            } ?>
                            <?php print_field($this->mdl_companies, $custom_field, $cv); ?>
                        <?php endforeach; ?>
                    </div>

                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-6">

                <div class="panel panel-default">

                    <div class="panel-heading">
                        <?php _trans('personal_information'); ?>
                    </div>

                    <div class="panel-body">
                        <div class="form-group">
                            <label for="company_gender"><?php _trans('gender'); ?></label>
                            <div class="controls">
                                <select name="company_gender" id="company_gender"
                                        class="form-control simple-select" data-minimum-results-for-search="Infinity">
                                    <?php
                                    $genders = [
                                        trans('gender_male'),
                                        trans('gender_female'),
                                        trans('gender_other'),
                                    ];
foreach ($genders as $key => $val) { ?>
                                        <option
                                            value=" <?php echo $key; ?>" <?php check_select($key, $this->mdl_companies->form_value('company_gender')) ?>>
                                            <?php echo $val; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php $company_title    = $this->mdl_companies->form_value('company_title'); ?>
                            <?php $is_custom_title = null === ClientTitleEnum::tryFrom($company_title) ?>
                            <label for="company_title"><?php _trans('company_title'); ?></label>
                            <select name="company_title" id="company_title" class="form-control simple-select">
                                <?php foreach ($company_title_choices as $company_title_choice) : ?>
                                    <option
                                        value="<?php echo $company_title_choice; ?>"
                                        <?php echo $company_title === $company_title_choice ? 'selected' : '' ?>
                                        <?php echo $is_custom_title && $company_title_choice === ClientTitleEnum::CUSTOM
        ? 'selected'
        : ''
                                    ?>
                                    >
                                        <?php echo ucfirst(trans($company_title_choice)); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <input
                                id="company_title_custom"
                                name="company_title_custom"
                                type="text"
                                class="form-control <?php echo $company_title === ClientTitleEnum::CUSTOM || $is_custom_title ? '' : 'hidden' ?>"
                                placeholder=<?php echo trans('custom_title') ?>
                                value="<?php echo $this->mdl_companies->form_value('company_title', true); ?>"
                            />
                        </div>
                        <div class="form-group has-feedback">
                            <label for="company_birthdate"><?php _trans('birthdate'); ?></label>
                            <?php
                            $bdate = $this->mdl_companies->form_value('company_birthdate');
if ($bdate && $bdate != '0000-00-00') {
    $bdate = date_from_mysql($bdate);
} else {
    $bdate = '';
}
?>
                            <div class="input-group">
                                <input type="text" name="company_birthdate" id="company_birthdate"
                                       class="form-control datepicker"
                                       value="<?php _htmlsc($bdate); ?>">
                                <span class="input-group-addon">
                                <i class="fa fa-calendar fa-fw"></i>
                            </span>
                            </div>
                        </div>

                        <?php if ($this->mdl_settings->setting('sumex') == '1'): ?>

                            <div class="form-group">
                                <label for="company_avs"><?php _trans('sumex_ssn'); ?></label>
                                <?php $avs = $this->mdl_companies->form_value('company_avs'); ?>
                                <div class="controls">
                                    <input type="text" name="company_avs" id="company_avs" class="form-control"
                                           value="<?php echo htmlspecialchars(format_avs($avs), ENT_COMPAT); ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="company_insurednumber"><?php _trans('sumex_insurednumber'); ?></label>
                                <?php $insuredNumber = $this->mdl_companies->form_value('company_insurednumber'); ?>
                                <div class="controls">
                                    <input type="text" name="company_insurednumber" id="company_insurednumber"
                                           class="form-control"
                                           value="<?php echo htmlentities($insuredNumber, ENT_COMPAT); ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="company_veka"><?php _trans('sumex_veka'); ?></label>
                                <?php $veka = $this->mdl_companies->form_value('company_veka'); ?>
                                <div class="controls">
                                    <input type="text" name="company_veka" id="company_veka" class="form-control"
                                           value="<?php echo htmlentities($veka, ENT_COMPAT); ?>">
                                </div>
                            </div>

                        <?php endif; ?>

                        <!-- Custom fields -->
                        <?php foreach ($custom_fields as $custom_field): ?>
                            <?php if ($custom_field->custom_field_location != 3) {
                                continue;
                            } ?>
                            <?php print_field($this->mdl_companies, $custom_field, $cv); ?>
                        <?php endforeach; ?>
                    </div>

                </div>

            </div>
            <div class="col-xs-12 col-sm-6">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php _trans('tax_information'); ?>
                    </div>

                    <div class="panel-body">
                        <div class="form-group">
                            <label for="company_vat_id"><?php _trans('vat_id'); ?></label>

                            <div class="controls">
                                <input type="text" name="company_vat_id" id="company_vat_id" class="form-control"
                                       value="<?php echo $this->mdl_companies->form_value('company_vat_id', true); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="company_tax_code"><?php _trans('tax_code'); ?></label>

                            <div class="controls">
                                <input type="text" name="company_tax_code" id="company_tax_code" class="form-control"
                                       value="<?php echo $this->mdl_companies->form_value('company_tax_code', true); ?>">
                            </div>
                        </div>

                        <!-- Custom fields -->
                        <?php foreach ($custom_fields as $custom_field): ?>
                            <?php if ($custom_field->custom_field_location != 4) {
                                continue;
                            } ?>
                            <?php print_field($this->mdl_companies, $custom_field, $cv); ?>
                        <?php endforeach; ?>
                    </div>

                </div>

            </div>
        </div>
        <?php if ($custom_fields): ?>
            <div class="row">
                <div class="col-xs-12 col-md-6">

                    <div class="panel panel-default">

                        <div class="panel-heading">
                            <?php _trans('custom_fields'); ?>
                        </div>

                        <div class="panel-body">
                            <?php foreach ($custom_fields as $custom_field): ?>
                                <?php if ($custom_field->custom_field_location != 0) {
                                    continue;
                                }
                                print_field($this->mdl_companies, $custom_field, $cv);
                                ?>
                            <?php endforeach; ?>
                        </div>

                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</form>
