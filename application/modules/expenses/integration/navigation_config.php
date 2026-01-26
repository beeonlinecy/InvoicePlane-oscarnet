<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * Navigation configuration for InvoicePlane Expense Module
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2025 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

// Добавить эту конфигурацию в application/config/navigation.php

$nav['expenses'] = [
    'title' => trans('expenses'),
    'class' => 'fa fa-money',
    'href'  => site_url('expenses'),
    'position' => 50
];
