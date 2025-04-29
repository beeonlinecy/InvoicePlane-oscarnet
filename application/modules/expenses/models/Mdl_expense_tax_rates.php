<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * expensePlane
 *
 * @author		expensePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 expensePlane.com
 * @license		https://expenseplane.com/license.txt
 * @link		https://expenseplane.com
 */

#[AllowDynamicProperties]
class Mdl_expense_Tax_Rates extends Response_Model
{
    public $table = 'ip_expense_tax_rates';
    public $primary_key = 'ip_expense_tax_rates.expense_tax_rate_id';

    public function default_select()
    {
        $this->db->select('ip_tax_rates.tax_rate_name AS expense_tax_rate_name');
        $this->db->select('ip_tax_rates.tax_rate_percent AS expense_tax_rate_percent');
        $this->db->select('ip_expense_tax_rates.*');
    }

    public function default_join()
    {
        $this->db->join('ip_tax_rates', 'ip_tax_rates.tax_rate_id = ip_expense_tax_rates.tax_rate_id');
    }

    /**
     * @param null $id
     * @param null $db_array
     * @return void
     */
    public function save($id = null, $db_array = null)
    {
        parent::save($id, $db_array);

        $this->load->model('expenses/mdl_expense_amounts');

        if (isset($db_array['expense_id'])) {
            $expense_id = $db_array['expense_id'];
        } else {
            $expense_id = $this->input->post('expense_id');
        }

        if ($expense_id) {
            $this->mdl_expense_amounts->calculate_expense_taxes($expense_id);
            $this->mdl_expense_amounts->calculate($expense_id);
        }

    }

    /**
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'expense_id' => array(
                'field' => 'expense_id',
                'label' => trans('expense'),
                'rules' => 'required'
            ),
            'tax_rate_id' => array(
                'field' => 'tax_rate_id',
                'label' => trans('tax_rate'),
                'rules' => 'required'
            ),
            'include_item_tax' => array(
                'field' => 'include_item_tax',
                'label' => trans('tax_rate_placement'),
                'rules' => 'required'
            ),
            'include_tax' => array(
                'field' => 'include_tax',
                'label' => trans('tax_rate_included'),
                'rules' => 'required'
            )            
        );
    }

}
