<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane Expense Module
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2025 InvoicePlane.com
 * @license     https://invoiceplane.com license.txt
 * @link        https://invoiceplane.com
 */

#[AllowDynamicProperties]
class Mdl_Expense_Custom extends Response_Model
{
    public $table = 'ip_expense_custom';

    public $primary_key = 'ip_expense_custom.expense_custom_id';

    public function default_join()
    {
        $this->db->join('ip_custom_fields', 'ip_custom_fields.custom_field_id = ip_expense_custom.expense_custom_fieldid');
    }

    /**
     * Get custom fields for an expense
     */
    public function by_id($expense_id)
    {
        $this->db->where('ip_expense_custom.expense_id', $expense_id);
        return $this;
    }

    /**
     * Save custom field values
     */
    public function save_custom($expense_id, $form_data)
    {
        // Delete existing custom field values
        $this->db->where('expense_id', $expense_id);
        $this->db->delete('ip_expense_custom');

        // Insert new custom field values
        foreach ($form_data as $fieldid => $value) {
            $db_array = array(
                'expense_id' => $expense_id,
                'expense_custom_fieldid' => $fieldid,
                'expense_custom_fieldvalue' => $value
            );

            $this->db->insert('ip_expense_custom', $db_array);
        }
    }

    /**
     * Get custom fields by expense ID
     */
    public function get_by_expid($expense_id)
    {
        $this->db->where('ip_expense_custom.expense_id', $expense_id);
        return $this->get();
    }
}
