<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane Expense Module
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2025 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

#[AllowDynamicProperties]
class Mdl_Expense_Item_Amounts extends Response_Model
{
    public $table = 'ip_expense_item_amounts';

    public $primary_key = 'ip_expense_item_amounts.item_amount_id';

    public function default_join()
    {
        $this->db->join('ip_expense_items', 'ip_expense_items.item_id = ip_expense_item_amounts.item_id');
    }
}
