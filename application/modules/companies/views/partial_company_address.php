<?php $this->load->helper('country'); ?>

<span class="company-address-street-line">
    <?php echo($company->company_address_1 ? htmlsc($company->company_address_1) . '<br>' : ''); ?>
</span>
<span class="company-address-street-line">
    <?php echo($company->company_address_2 ? htmlsc($company->company_address_2) . '<br>' : ''); ?>
</span>
<span class="company-adress-town-line">
    <?php echo($company->company_city ? htmlsc($company->company_city) . ' ' : ''); ?>
    <?php echo($company->company_state ? htmlsc($company->company_state) . ' ' : ''); ?>
    <?php echo($company->company_zip ? htmlsc($company->company_zip) : ''); ?>
</span>
<span class="company-adress-country-line">
    <?php echo($company->company_country ? '<br>' . get_country_name(trans('cldr'), $company->company_country) : ''); ?>
</span>
