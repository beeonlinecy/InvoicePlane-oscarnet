<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane Expense Module
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2025 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

#[AllowDynamicProperties]
class Ajax extends Admin_Controller
{
    /**
     * Ajax constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get expense categories
     */
    public function get_expense_categories()
    {
        $this->db->where('is_active', 1);
        $categories = $this->db->get('ip_expense_categories')->result();

        header('Content-Type: application/json');
        echo json_encode($categories);
    }

    /**
     * Get expense data
     */
    public function get_expense($expense_id)
    {
        $this->load->model('expenses/mdl_expense_amounts');

        $expense = $this->db->where('expense_id', $expense_id)->get('ip_expenses')->row();
        
        if (!$expense) {
            echo json_encode(['error' => 'Expense not found']);
            return;
        }

        $expense_amounts = $this->mdl_expense_amounts->where('expense_id', $expense_id)->get()->row();

        $data = [
            'expense' => $expense,
            'amounts' => $expense_amounts
        ];

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * Calculate expense totals
     */
    public function calculate_expense($expense_id)
    {
        $this->load->model('expenses/mdl_expense_amounts');
        $this->load->model('expenses/mdl_expense_items');

        $this->mdl_expense_amounts->calculate($expense_id);

        $amounts = $this->mdl_expense_amounts->where('expense_id', $expense_id)->get()->row();
        $items = $this->mdl_expense_items->with_amounts($expense_id);

        $data = [
            'amounts' => $amounts,
            'items' => $items
        ];

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * Get expense statistics for dashboard
     */
    public function get_expense_statistics($period = 'month')
    {
        $this->load->model('expenses/mdl_expense_amounts');

        $total_expenses = $this->mdl_expense_amounts->get_total_expenses($period);
        $total_paid = $this->mdl_expense_amounts->get_total_paid($period);
        $total_balance = $this->mdl_expense_amounts->get_total_balance($period);
        $category_totals = $this->mdl_expense_amounts->get_category_totals($period);
        $status_totals = $this->mdl_expense_amounts->get_status_totals($period);

        $data = [
            'total_expenses' => $total_expenses,
            'total_paid' => $total_paid,
            'total_balance' => $total_balance,
            'category_totals' => $category_totals,
            'status_totals' => $status_totals
        ];

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * Search expenses
     */
    public function search_expenses()
    {
        $query = $this->input->get('query');
        $limit = $this->input->get('limit', 10);

        if (empty($query)) {
            echo json_encode([]);
            return;
        }

        $this->db->like('expense_number', $query);
        $this->db->or_like('expense_notes', $query);
        $this->db->or_like('item_name', $query);
        $this->db->limit($limit);

        $this->db->join('ip_expenses', 'ip_expenses.expense_id = ip_expense_items.expense_id');
        $results = $this->db->get('ip_expense_items')->result();

        header('Content-Type: application/json');
        echo json_encode($results);
    }

    /**
     * Validate expense number
     */
    public function validate_expense_number()
    {
        $expense_number = $this->input->post('expense_number');
        $expense_id = $this->input->post('expense_id');

        if (empty($expense_number)) {
            echo json_encode(['valid' => true]);
            return;
        }

        $this->db->where('expense_number', $expense_number);
        if (!empty($expense_id)) {
            $this->db->where('expense_id !=', $expense_id);
        }

        $count = $this->db->count_all_results('ip_expenses');

        echo json_encode(['valid' => ($count == 0)]);
    }

    /**
     * Get expense amounts by period
     */
    public function get_expense_amounts_by_period($period = 'month')
    {
        $this->load->model('expenses/mdl_expense_amounts');

        $amounts = $this->mdl_expense_amounts->get_total_expenses($period);

        header('Content-Type: application/json');
        echo json_encode(['amount' => $amounts]);
    }

    /**
     * Bulk update expense status
     */
    public function bulk_update_status()
    {
        $expense_ids = $this->input->post('expense_ids');
        $new_status = $this->input->post('new_status');

        if (empty($expense_ids) || empty($new_status)) {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
            return;
        }

        $this->load->model('expenses/mdl_expenses');
        $this->load->model('expenses/mdl_expense_amounts');

        $results = [];
        foreach ($expense_ids as $expense_id) {
            $this->db->where('expense_id', $expense_id);
            $this->db->set('expense_status_id', $new_status);
            $result = $this->db->update('ip_expenses');

            $results[$expense_id] = $result;

            // Recalculate amounts
            $this->mdl_expense_amounts->calculate($expense_id);
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'results' => $results]);
    }

    /**
     * Get recent expenses for dashboard
     */
    public function get_recent_expenses($limit = 10)
    {
        $this->db->limit($limit);
        $this->db->order_by('expense_date_created', 'DESC');
        $this->db->select('ip_expenses.*, ip_expense_categories.expense_category_name');
        $this->db->join('ip_expense_categories', 'ip_expense_categories.expense_category_id = ip_expenses.expense_category_id');
        $expenses = $this->db->get('ip_expenses')->result();

        header('Content-Type: application/json');
        echo json_encode($expenses);
    }

    /**
     * Get expense payments
     */
    public function get_expense_payments($expense_id)
    {
        $this->db->where('expense_id', $expense_id);
        $this->db->order_by('payment_date', 'DESC');
        $payments = $this->db->get('ip_expense_payments')->result();

        header('Content-Type: application/json');
        echo json_encode($payments);
    }

    /**
     * Get expense items with amounts
     */
    public function get_expense_items($expense_id)
    {
        $this->load->model('expenses/mdl_expense_items');
        
        $items = $this->mdl_expense_items->with_amounts($expense_id);

        header('Content-Type: application/json');
        echo json_encode($items);
    }
}
