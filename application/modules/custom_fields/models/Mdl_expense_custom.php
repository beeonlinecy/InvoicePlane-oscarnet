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
class Mdl_expense_Custom extends Validator
{
    public static $positions = array(
        'custom_fields',
        'properties'
    );
    public $table = 'ip_expense_custom';
    public $primary_key = 'ip_expense_custom.expense_custom_id';

    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS ip_expense_custom.*, ip_custom_fields.*', false);
    }

    public function default_join()
    {
        $this->db->join('ip_custom_fields', 'ip_expense_custom.expense_custom_fieldid = ip_custom_fields.custom_field_id');
    }

    public function default_order_by()
    {
        $this->db->order_by('custom_field_table ASC, custom_field_order ASC, custom_field_label ASC');
    }

    public function save_custom($expense_id, $db_array)
    {
        $result = $this->validate($db_array);
        if ($result === true) {
            $form_data = isset($this->_formdata) ? $this->_formdata : null;

            if (is_null($form_data)) {
                return true;
            }

            $expense_custom_id = null;

            foreach ($form_data as $key => $value) {
                $db_array = array(
                    'expense_id' => $expense_id,
                    'expense_custom_fieldid' => $key,
                    'expense_custom_fieldvalue' => $value
                );
                $expense_custom = $this->where('expense_id', $expense_id)->where('expense_custom_fieldid', $key)->get();

                if ($expense_custom->num_rows()) {
                    $expense_custom_id = $expense_custom->row()->expense_custom_id;
                }

                parent::save($expense_custom_id, $db_array);
            }

            return true;
        }

        return $result;
    }

    public function by_id($expense_id)
    {
        $this->db->where('ip_expense_custom.expense_id', $expense_id);
        return $this;
    }

}
