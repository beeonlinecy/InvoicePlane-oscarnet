<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * @param object $company
 * @return string
 */
function format_company($company)
{
    $company_title='';
    if(property_exists($company, 'company_title')){
        $company_title = $company->company_title === 'custom' ? '' : $company->company_title ?? '';
    }
    
    return ucfirst(trans($company_title)) . ' ' . $company->company_name; //. (empty($company->company_surname) ? '' : ' ' . $company->company_surname);
}
