<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_Expense_Categories extends Response_Model
{
    public $table = 'ip_expense_categories';
    public $primary_key = 'ip_expense_categories.expense_category_id';

    public function default_select()
    {
        $this->db->select('DISTINCT ip_expense_categories.*', false);
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_expense_categories.expense_category_name');
    }

    public function validation_rules()
    {
        return array(
            'expense_category_name' => array(
                'field' => 'expense_category_name',
                'label' => lang('expense_category_name'),
                'rules' => 'required'
            )
        );
    }

    public function get_categories()
    {
        return $this->db->get('ip_expense_categories')->result();
    }
}
