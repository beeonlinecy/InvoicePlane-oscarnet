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
class Mdl_Expense_Items extends Response_Model
{
    public $table = 'ip_expense_items';

    public $primary_key = 'ip_expense_items.item_id';

    public function default_order_by()
    {
        $this->db->order_by('ip_expense_items.item_order', 'asc');
    }

    public function validation_rules()
    {
        return [
            'expense_id' => [
                'field' => 'expense_id',
                'label' => trans('expense'),
                'rules' => 'required',
            ],
            'item_name' => [
                'field' => 'item_name',
                'label' => trans('item'),
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
        ];
    }

    public function db_array()
    {
        $db_array = parent::db_array();

        if ( ! isset($db_array['item_quantity'])) {
            $db_array['item_quantity'] = 1;
        }

        if ( ! isset($db_array['item_order'])) {
            $db_array['item_order'] = $this->get_next_item_order($db_array['expense_id']);
        }

        return $db_array;
    }

    public function get_next_item_order($expense_id)
    {
        $query = $this->db->query("SELECT MAX(item_order) as max_order FROM ip_expense_items WHERE expense_id = " . $this->db->escape($expense_id));
        $result = $query->row();

        return ($result->max_order + 1);
    }

    public function calculate($expense_item_id)
    {
        $this->load->model('expenses/mdl_expense_item_amounts');

        $expense_item = $this->get_by_id($expense_item_id);

        if (empty($expense_item)) {
            return;
        }

        // Calculate item subtotal
        $item_subtotal = $expense_item->item_quantity * $expense_item->item_price;

        // Calculate discount
        $item_discount = 0;
        if (!empty($expense_item->item_discount_amount)) {
            $item_discount += $expense_item->item_discount_amount;
        }
        if (!empty($expense_item->item_discount_percent)) {
            $item_discount += ($item_subtotal * $expense_item->item_discount_percent / 100);
        }

        // Calculate item tax
        $item_tax = 0;
        if ($expense_item->item_tax_rate_id) {
            $this->db->where('tax_rate_id', $expense_item->item_tax_rate_id);
            $tax_rate = $this->db->get('ip_tax_rates')->row();

            if ($tax_rate) {
                $tax_rate_percent = $tax_rate->tax_rate_percent;
                $item_tax = ($item_subtotal - $item_discount) * $tax_rate_percent / 100;
            }
        }

        // Calculate item total
        $item_total = ($item_subtotal - $item_discount) + $item_tax;

        // Update or insert item amount record
        $item_amount_data = array(
            'item_id' => $expense_item_id,
            'item_subtotal' => $item_subtotal,
            'item_tax_total' => $item_tax,
            'item_total' => $item_total,
            'item_discount' => $item_discount
        );

        $this->db->where('item_id', $expense_item_id);

        if ($this->db->get('ip_expense_item_amounts')->num_rows()) {
            $this->db->update('ip_expense_item_amounts', $item_amount_data);
        } else {
            $this->db->insert('ip_expense_item_amounts', $item_amount_data);
        }

        // Recalculate the expense totals
        $this->load->model('expenses/mdl_expense_amounts');
        $this->mdl_expense_amounts->calculate($expense_item->expense_id);
    }

    public function delete($expense_item_id)
    {
        // Get the expense_id before deleting
        $expense_item = $this->get_by_id($expense_item_id);
        
        if ($expense_item) {
            $expense_id = $expense_item->expense_id;
            
            // Delete the item and its amount record
            $this->db->where('item_id', $expense_item_id);
            $this->db->delete('ip_expense_item_amounts');
            
            parent::delete($expense_item_id);
            
            // Recalculate the expense totals
            $this->load->model('expenses/mdl_expense_amounts');
            $this->mdl_expense_amounts->calculate($expense_id);
        }
    }

    /**
     * Get items for an expense with amounts
     */
    public function with_amounts($expense_id)
    {
        $this->db->select('ip_expense_items.*, ip_expense_item_amounts.*');
        $this->db->join('ip_expense_item_amounts', 'ip_expense_item_amounts.item_id = ip_expense_items.item_id', 'left');
        $this->db->where('expense_id', $expense_id);
        
        return $this->get()->result();
    }
}
