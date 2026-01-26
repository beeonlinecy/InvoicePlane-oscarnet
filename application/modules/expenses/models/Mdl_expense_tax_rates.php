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
class Mdl_Expense_Tax_Rates extends Response_Model
{
    public $table = 'ip_expense_tax_rates';

    public $primary_key = 'ip_expense_tax_rates.expense_tax_rate_id';

    public function default_join()
    {
        $this->db->join('ip_tax_rates', 'ip_tax_rates.tax_rate_id = ip_expense_tax_rates.tax_rate_id');
    }

    public function validation_rules()
    {
        return [
            'expense_id' => [
                'field' => 'expense_id',
                'label' => trans('expense'),
                'rules' => 'required',
            ],
            'tax_rate_id' => [
                'field' => 'tax_rate_id',
                'label' => trans('tax_rate'),
                'rules' => 'required',
            ],
            'include_item_tax' => [
                'field' => 'include_item_tax',
                'label' => trans('include_item_tax'),
            ],
            'include_tax' => [
                'field' => 'include_tax',
                'label' => trans('include_tax'),
            ],
        ];
    }

    public function db_array()
    {
        $db_array = parent::db_array();

        // Get the tax rate percentage
        if (!empty($db_array['tax_rate_id'])) {
            $this->db->where('tax_rate_id', $db_array['tax_rate_id']);
            $tax_rate = $this->db->get('ip_tax_rates')->row();

            if ($tax_rate) {
                $db_array['expense_tax_rate_percent'] = $tax_rate->tax_rate_percent;
            }
        }

        return $db_array;
    }

    public function save($id = null, $db_array = null)
    {
        $result = parent::save($id, $db_array);

        // Recalculate expense amounts after saving tax rate
        if ($db_array && !empty($db_array['expense_id'])) {
            $this->load->model('expenses/mdl_expense_amounts');
            $this->mdl_expense_amounts->calculate($db_array['expense_id']);
        }

        return $result;
    }

    public function delete($expense_tax_rate_id)
    {
        // Get the expense_id before deleting
        $expense_tax_rate = $this->get_by_id($expense_tax_rate_id);
        
        if ($expense_tax_rate) {
            $expense_id = $expense_tax_rate->expense_id;
            
            parent::delete($expense_tax_rate_id);
            
            // Recalculate the expense amounts
            $this->load->model('expenses/mdl_expense_amounts');
            $this->mdl_expense_amounts->calculate($expense_id);
        }
    }
}
