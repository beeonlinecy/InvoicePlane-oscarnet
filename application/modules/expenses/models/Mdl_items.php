<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * expensePlane
 *
 * @author      expensePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 expensePlane.com
 * @license     https://expenseplane.com/license.txt
 * @link        https://expenseplane.com
 */

#[AllowDynamicProperties]
class Mdl_Items extends Response_Model
{

    public $table = 'ip_expense_items';

    public $primary_key = 'ip_expense_items.item_id';

    public $date_created_field = 'item_date_added';

    public function default_select()
    {
        $this->db->select('ip_expense_item_amounts.*, ip_products.*, ip_expense_items.*,
            item_tax_rates.tax_rate_percent AS item_tax_rate_percent,
            item_tax_rates.tax_rate_name AS item_tax_rate_name');
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_expense_items.item_order');
    }

    public function default_join()
    {
        $this->db->join('ip_expense_item_amounts', 'ip_expense_item_amounts.item_id = ip_expense_items.item_id', 'left');
        $this->db->join('ip_tax_rates AS item_tax_rates', 'item_tax_rates.tax_rate_id = ip_expense_items.item_tax_rate_id', 'left');
        $this->db->join('ip_products', 'ip_products.product_id = ip_expense_items.item_product_id', 'left');
    }

    /**
     * @return array
     */
    public function validation_rules()
    {
        return [
            'expense_id' => [
                'field' => 'expense_id',
                'label' => trans('expense'),
                'rules' => 'required',
            ],
            'item_sku' => [
                'field' => 'item_sku',
                'label' => trans('item_sku'),
                'rules' => 'required|unique',
            ],
            'item_name' => [
                'field' => 'item_name',
                'label' => trans('item_name'),
                'rules' => 'required',
            ],
            'item_description' => [
                'field' => 'item_description',
                'label' => trans('description'),
            ],
            'item_quantity' => [
                'field' => 'item_quantity',
                'label' => trans('quantity'),
                'rules' => 'required',
            ],
            'item_price' => [
                'field' => 'item_price',
                'label' => trans('price'),
                'rules' => 'required',
            ],
            'item_tax_rate_id' => [
                'field' => 'item_tax_rate_id',
                'label' => trans('item_tax_rate'),
            ],
            'item_product_id' => [
                'field' => 'item_product_id',
                'label' => trans('original_product'),
            ],
            'item_date' => [
                'field' => 'item_date',
                'label' => trans('item_date'),
            ],
            'item_is_recurring' => [
                'field' => 'item_is_recurring',
                'label' => trans('recurring'),
            ],
        ];
    }

    /**
     * @param null $id
     * @param null $db_array
     *
     * @return int|null
     */
    public function save($id = null, $db_array = null)
    {
        $id = parent::save($id, $db_array);

        $this->load->model('expenses/mdl_item_amounts');
        $this->mdl_item_amounts->calculate($id);

        $this->load->model('expenses/mdl_expense_amounts');

        if (is_object($db_array) && isset($db_array->expense_id)) {
            $this->mdl_expense_amounts->calculate($db_array->expense_id);
        } elseif (is_array($db_array) && isset($db_array['expense_id'])) {
            $this->mdl_expense_amounts->calculate($db_array['expense_id']);
        }

        return $id;
    }

    /**
     * @param int $item_id
     *
     * @return bool
     */
    public function delete($item_id)
    {
        // Get item:
        // the expense id is needed to recalculate expense amounts
        // and the task id to update status if the item refers a task
        $query = $this->db->get_where($this->table, ['item_id' => $item_id]);

        if ($query->num_rows() == 0) {
            return false;
        }

        $row = $query->row();
        $expense_id = $row->expense_id;

        // Delete the item
        parent::delete($item_id);

        // Delete the item amounts
        $this->db->where('item_id', $item_id);
        $this->db->delete('ip_expense_item_amounts');

        // Recalculate expense amounts
        $this->load->model('expenses/mdl_expense_amounts');
        $this->mdl_expense_amounts->calculate($expense_id);

        return true;
    }
}
