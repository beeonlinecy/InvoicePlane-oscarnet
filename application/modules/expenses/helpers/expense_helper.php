<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane Expense Module Helper
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2025 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

/**
 * Save expense items from form data
 */
function save_expense_items($expense_id, $items)
{
    if (empty($items)) {
        return;
    }

    $CI = &get_instance();
    
    // Load models
    $CI->load->model('expenses/mdl_expense_items');
    $CI->load->model('expenses/mdl_expense_item_amounts');

    // Get existing items
    $existing_items = $CI->db->where('expense_id', $expense_id)->get('ip_expense_items')->result();
    $existing_item_ids = [];
    foreach ($existing_items as $item) {
        $existing_item_ids[] = $item->item_id;
    }

    $processed_item_ids = [];

    foreach ($items as $item_data) {
        if (empty($item_data['item_name'])) {
            continue;
        }

        // Set defaults
        if (!isset($item_data['item_quantity']) || empty($item_data['item_quantity'])) {
            $item_data['item_quantity'] = 1;
        }
        
        if (!isset($item_data['item_price']) || empty($item_data['item_price'])) {
            $item_data['item_price'] = 0;
        }

        $item_data['expense_id'] = $expense_id;

        // Update or insert item
        if (isset($item_data['expense_item_id']) && !empty($item_data['expense_item_id'])) {
            // Update existing item
            $item_id = $item_data['expense_item_id'];
            unset($item_data['expense_item_id']);
            $CI->mdl_expense_items->save($item_id, $item_data);
            $processed_item_ids[] = $item_id;
        } else {
            // Insert new item
            $item_id = $CI->mdl_expense_items->save(null, $item_data);
            $processed_item_ids[] = $item_id;
        }

        // Calculate item amounts
        $CI->mdl_expense_items->calculate($item_id);
    }

    // Delete items that are no longer in the form
    foreach ($existing_item_ids as $existing_id) {
        if (!in_array($existing_id, $processed_item_ids)) {
            $CI->mdl_expense_items->delete($existing_id);
        }
    }

    // Recalculate expense totals
    $CI->load->model('expenses/mdl_expense_amounts');
    $CI->mdl_expense_amounts->calculate($expense_id);
}

/**
 * Save expense tax rates from form data
 */
function save_expense_tax_rates($expense_id, $tax_rates)
{
    if (empty($tax_rates)) {
        return;
    }

    $CI = &get_instance();
    
    // Load model
    $CI->load->model('expenses/mdl_expense_tax_rates');

    // Get existing tax rates
    $existing_taxes = $CI->db->where('expense_id', $expense_id)->get('ip_expense_tax_rates')->result();
    $existing_tax_ids = [];
    foreach ($existing_taxes as $tax) {
        $existing_tax_ids[] = $tax->expense_tax_rate_id;
    }

    $processed_tax_ids = [];

    foreach ($tax_rates as $tax_data) {
        if (empty($tax_data['tax_rate_id'])) {
            continue;
        }

        $tax_data['expense_id'] = $expense_id;
        
        // Set defaults for checkboxes
        if (!isset($tax_data['include_item_tax'])) {
            $tax_data['include_item_tax'] = 0;
        }
        
        if (!isset($tax_data['include_tax'])) {
            $tax_data['include_tax'] = 0;
        }

        // Get tax rate percent from ip_tax_rates
        $tax_rate_row = $CI->db->where('tax_rate_id', $tax_data['tax_rate_id'])->get('ip_tax_rates')->row();
        if ($tax_rate_row) {
            $tax_data['expense_tax_rate_percent'] = $tax_rate_row->tax_rate_percent;
        }

        // Update or insert tax rate
        if (isset($tax_data['expense_tax_rate_id']) && !empty($tax_data['expense_tax_rate_id'])) {
            // Update existing tax rate
            $tax_rate_id = $tax_data['expense_tax_rate_id'];
            unset($tax_data['expense_tax_rate_id']);
            $CI->mdl_expense_tax_rates->save($tax_rate_id, $tax_data);
            $processed_tax_ids[] = $tax_rate_id;
        } else {
            // Insert new tax rate
            $tax_rate_id = $CI->mdl_expense_tax_rates->save(null, $tax_data);
            $processed_tax_ids[] = $tax_rate_id;
        }
    }

    // Delete tax rates that are no longer in the form
    foreach ($existing_tax_ids as $existing_id) {
        if (!in_array($existing_id, $processed_tax_ids)) {
            $CI->mdl_expense_tax_rates->delete($existing_id);
        }
    }

    // Recalculate expense totals with taxes
    $CI->load->model('expenses/mdl_expense_amounts');
    $CI->mdl_expense_amounts->calculate($expense_id);
}

/**
 * Get expense summary for dashboard
 */
function get_expense_summary($period = 'month')
{
    $CI = &get_instance();
    $CI->load->model('expenses/mdl_expense_amounts');
    
    $total_expenses = $CI->mdl_expense_amounts->get_total_expenses($period);
    $total_paid = $CI->mdl_expense_amounts->get_total_paid($period);
    $total_balance = $CI->mdl_expense_amounts->get_total_balance($period);
    $status_totals = $CI->mdl_expense_amounts->get_status_totals($period);
    $category_totals = $CI->mdl_expense_amounts->get_category_totals($period);
    
    return [
        'total_expenses' => $total_expenses,
        'total_paid' => $total_paid,
        'total_balance' => $total_balance,
        'status_totals' => $status_totals,
        'category_totals' => $category_totals
    ];
}

/**
 * Format expense amount for display
 */
function format_expense_amount($amount, $currency = null)
{
    if (empty($currency)) {
        $currency = get_setting('currency_symbol', '$');
    }
    
    $decimal_point = get_setting('decimal_point', '.');
    $thousands_sep = get_setting('thousands_separator', ',');
    
    return $currency . number_format($amount, 2, $decimal_point, $thousands_sep);
}

/**
 * Get expense status label with color
 */
function get_expense_status_label($status_id, $statuses = null)
{
    if ($statuses === null) {
        $CI = &get_instance();
        $CI->load->model('expenses/mdl_expenses');
        $statuses = $CI->mdl_expenses->statuses();
    }
    
    if (!isset($statuses[$status_id])) {
        return ['label' => 'Unknown', 'class' => 'label-default'];
    }
    
    return $statuses[$status_id];
}

/**
 * Get expense category info
 */
function get_expense_category($category_id)
{
    $CI = &get_instance();
    return $CI->db->where('expense_category_id', $category_id)->get('ip_expense_categories')->row();
}

/**
 * Validate expense data
 */
function validate_expense_data($expense_data)
{
    $errors = [];
    
    // Required fields
    $required_fields = [
        'expense_category_id' => 'Category',
        'expense_date_created' => 'Expense Date',
        'user_id' => 'User'
    ];
    
    foreach ($required_fields as $field => $label) {
        if (empty($expense_data[$field])) {
            $errors[] = "$label is required";
        }
    }
    
    // Validate date format
    if (!empty($expense_data['expense_date_created']) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $expense_data['expense_date_created'])) {
        $errors[] = 'Invalid expense date format';
    }
    
    return $errors;
}

/**
 * Check if expense can be edited
 */
function can_edit_expense($expense, $user_id = null)
{
    if ($user_id === null) {
        $CI = &get_instance();
        $user_id = $CI->session->userdata('user_id');
    }
    
    // Only creator or admin can edit
    if ($expense->user_id != $user_id && !is_admin()) {
        return false;
    }
    
    // Cannot edit if expense is read-only
    if ($expense->expense_is_read_only) {
        return false;
    }
    
    return true;
}

/**
 * Get expense total with taxes
 */
function calculate_expense_total($expense_id)
{
    $CI = &get_instance();
    $CI->load->model('expenses/mdl_expense_amounts');
    
    $amounts = $CI->mdl_expense_amounts->where('expense_id', $expense_id)->get()->row();
    
    if (!$amounts) {
        return 0;
    }
    
    return $amounts->expense_total;
}

/**
 * Get recent expenses for widget
 */
function get_recent_expenses($limit = 5, $user_id = null)
{
    $CI = &get_instance();
    $CI->load->model('expenses/mdl_expenses');
    
    $CI->mdl_expenses->limit($limit);
    $CI->mdl_expenses->order_by('expense_date_created', 'DESC');
    
    if ($user_id) {
        $CI->mdl_expenses->by_user($user_id);
    }
    
    return $CI->mdl_expenses->get()->result();
}

/**
 * Get expense statistics for charts
 */
function get_expense_stats_for_chart($period = 'year', $category_id = null)
{
    $CI = &get_instance();
    $CI->load->model('expenses/mdl_expense_amounts');
    
    // Get monthly totals for the given period
    switch ($period) {
        case 'month':
            $query = "
                SELECT 
                    DATE(expense_date_created) as date,
                    SUM(expense_total) as total
                FROM ip_expenses 
                JOIN ip_expense_amounts ON ip_expense_amounts.expense_id = ip_expenses.expense_id
                WHERE MONTH(expense_date_created) = MONTH(NOW())
                AND YEAR(expense_date_created) = YEAR(NOW())
                " . ($category_id ? "AND expense_category_id = $category_id" : "") . "
                GROUP BY DATE(expense_date_created)
                ORDER BY DATE(expense_date_created)
            ";
            break;
            
        case 'year':
            $query = "
                SELECT 
                    MONTH(expense_date_created) as month,
                    SUM(expense_total) as total
                FROM ip_expenses 
                JOIN ip_expense_amounts ON ip_expense_amounts.expense_id = ip_expenses.expense_id
                WHERE YEAR(expense_date_created) = YEAR(NOW())
                " . ($category_id ? "AND expense_category_id = $category_id" : "") . "
                GROUP BY MONTH(expense_date_created)
                ORDER BY MONTH(expense_date_created)
            ";
            break;
            
        default:
            return [];
    }
    
    return $CI->db->query($query)->result();
}
