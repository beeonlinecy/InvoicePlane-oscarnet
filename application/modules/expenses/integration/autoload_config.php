<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * Autoload configuration for InvoicePlane Expense Module
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2025 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

// Добавить 'expense' в массив helper в application/config/autoload.php

/*
| -------------------------------------------------------------------
|  Auto-Loader
| -------------------------------------------------------------------
| This file defines the packages and file maps to autoload.
|
*/

$autoload['helper'] = array(
    'url',
    'form',
    'html',
    'invoice',
    'client',
    'currency',
    'invoice_pdf',
    'pdf',
    'date',
    'string',
    'pagination',
    'array',
    'custom_values',
    'expense'  // Добавить эту строку
);
