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
class Mdl_Expense_Amounts extends CI_Model
{
    /**
     * IP_EXPENSE_AMOUNTS
     * expense_amount_id
     * expense_id
     * expense_item_subtotal    SUM(item_subtotal)
     * expense_item_tax_total   SUM(item_tax_total)
     * expense_tax_total
     * expense_total            expense_item_subtotal + expense_item_tax_total + expense_tax_total
     * expense_paid
     * expense_balance          expense_total - expense_paid
     *
     * IP_EXPENSE_ITEM_AMOUNTS
     * item_amount_id
     * item_id
     * item_tax_rate_id
     * item_subtotal            item_quantity * item_price
     * item_tax_total           item_subtotal * tax_rate_percent
     * item_total               item_subtotal + item_tax_total
     * item_discount
     *
     * @param int $expense_id
     */
    public function calculate($expense_id)
    {
        // Get the basic totals
        $query = $this->db->query("
        SELECT  SUM(item_subtotal) AS expense_item_subtotal,
                SUM(item_tax_total) AS expense_item_tax_total,
                SUM(item_discount) AS expense_item_discount
        FROM ip_expense_item_amounts
        WHERE item_id IN (
            SELECT item_id FROM ip_expense_items WHERE expense_id = " . $this->db->escape($expense_id) . "
            )
        ");

        $expense_amounts = $query->row();

        $expense_item_subtotal = $expense_amounts->expense_item_subtotal - $expense_amounts->expense_item_discount;
        $expense_subtotal = $expense_item_subtotal + $expense_amounts->expense_item_tax_total;
        $expense_total = $this->calculate_discount($expense_id, $expense_subtotal);

        // Get the amount already paid
        $query = $this->db->query("
          SELECT SUM(payment_amount) AS expense_paid
          FROM ip_expense_payments
          WHERE expense_id = " . $this->db->escape($expense_id)
        );

        $expense_paid = $query->row()->expense_paid ? floatval($query->row()->expense_paid) : 0;

        // Create the database array and insert or update
        $db_array = array(
            'expense_id' => $expense_id,
            'expense_item_subtotal' => $expense_item_subtotal,
            'expense_item_tax_total' => $expense_amounts->expense_item_tax_total,
            'expense_total' => $expense_total,
            'expense_paid' => $expense_paid,
            'expense_balance' => $expense_total - $expense_paid
        );

        $this->db->where('expense_id', $expense_id);

        if ($this->db->get('ip_expense_amounts')->num_rows()) {
            // The record already exists; update it
            $this->db->where('expense_id', $expense_id);
            $this->db->update('ip_expense_amounts', $db_array);
        } else {
            // The record does not yet exist; insert it
            $this->db->insert('ip_expense_amounts', $db_array);
        }

        // Calculate the expense taxes
        $this->calculate_expense_taxes($expense_id);

        // Check if expense should be marked as paid
        $this->load->model('expenses/mdl_expenses');
        $expense = $this->mdl_expenses->get_by_id($expense_id);

        // Set to paid if balance is zero and setting is enabled
        if ($expense->expense_balance == 0 && $expense->expense_total != 0) {
            if (get_setting('expenses_mark_as_paid_on_payment', 1) == 1) {
                $this->db->where('expense_id', $expense_id);
                $this->db->set('expense_status_id', 3);
                $this->db->update('ip_expenses');
            }
        }
    }

    /**
     * @param int $expense_id
     * @param float $expense_total
     * @return float
     */
    public function calculate_discount($expense_id, $expense_total)
    {
        // For expense module, we don't have expense discount fields like invoices
        // But we can extend this in future if needed
        return $expense_total;
    }

    /**
     * @param int $expense_id
     */
    public function calculate_expense_taxes($expense_id)
    {
        // First check to see if there are any expense taxes applied
        $this->load->model('expenses/mdl_expense_tax_rates');
        $expense_tax_rates = $this->mdl_expense_tax_rates->where('expense_id', $expense_id)->get()->result();

        if ($expense_tax_rates) {
            // There are expense taxes applied
            // Get the current expense amount record
            $expense_amount = $this->db->where('expense_id', $expense_id)->get('ip_expense_amounts')->row();

            // Loop through the expense taxes and update the amount for each of the applied expense taxes
            foreach ($expense_tax_rates as $expense_tax_rate) {
                if ($expense_tax_rate->include_item_tax) {
                    // The expense tax rate should include the applied item tax
                    $expense_tax_rate_amount = ($expense_amount->expense_item_subtotal + $expense_amount->expense_item_tax_total) * ($expense_tax_rate->expense_tax_rate_percent / 100);
                } else {
                    // The expense tax rate should not include the applied item tax
                    if($expense_tax_rate->include_tax){
                        // But expense tax is included in total
                        $expense_tax_rate_amount = $expense_amount->expense_item_subtotal / ($expense_tax_rate->expense_tax_rate_percent + 100) * $expense_tax_rate->expense_tax_rate_percent;
                    } else {
                        $expense_tax_rate_amount = $expense_amount->expense_item_subtotal * ($expense_tax_rate->expense_tax_rate_percent / 100);
                    }
                }

                // Update the expense tax rate record
                $db_array = array(
                    'expense_tax_rate_amount' => $expense_tax_rate_amount
                );
                $this->db->where('expense_tax_rate_id', $expense_tax_rate->expense_tax_rate_id);
                $this->db->update('ip_expense_tax_rates', $db_array);
            }

            // Update the expense amount record with the total expense tax amount
            $this->db->query("
              UPDATE ip_expense_amounts
              SET expense_tax_total = (
                SELECT SUM(expense_tax_rate_amount)
                FROM ip_expense_tax_rates
                WHERE expense_id = " . $this->db->escape($expense_id) . ")
              WHERE expense_id = " . $this->db->escape($expense_id));

            // Get the updated expense amount record
            $expense_amount = $this->db->where('expense_id', $expense_id)->get('ip_expense_amounts')->row();

            // Recalculate the expense total and balance
            if(!$expense_tax_rate->include_tax){
                //If tax isn't inclusive keep default calculation
                $expense_total = $expense_amount->expense_item_subtotal + $expense_amount->expense_item_tax_total + $expense_amount->expense_tax_total;
            } else {
                $expense_total = $expense_amount->expense_item_subtotal + $expense_amount->expense_item_tax_total;
            }
            $expense_total = $this->calculate_discount($expense_id, $expense_total);
            $expense_balance = $expense_total - $expense_amount->expense_paid;

            // Update the expense amount record
            $db_array = array(
                'expense_total' => $expense_total,
                'expense_balance' => $expense_balance
            );

            $this->db->where('expense_id', $expense_id);
            $this->db->update('ip_expense_amounts', $db_array);
        } else {
            // No expense taxes applied

            $db_array = array(
                'expense_tax_total' => '0.00'
            );

            $this->db->where('expense_id', $expense_id);
            $this->db->update('ip_expense_amounts', $db_array);
        }
    }

    /**
     * @param string|null $period
     * @return mixed
     */
    public function get_total_expenses($period = null)
    {
        switch ($period) {
            case 'month':
                return $this->db->query("
                    SELECT SUM(expense_total) AS total_expenses
                    FROM ip_expense_amounts
                    WHERE expense_id IN
                    (SELECT expense_id FROM ip_expenses
                    WHERE MONTH(expense_date_created) = MONTH(NOW())
                    AND YEAR(expense_date_created) = YEAR(NOW()))")->row()->total_expenses;
            case 'last_month':
                return $this->db->query("
                    SELECT SUM(expense_total) AS total_expenses
                    FROM ip_expense_amounts
                    WHERE expense_id IN
                    (SELECT expense_id FROM ip_expenses
                    WHERE MONTH(expense_date_created) = MONTH(NOW() - INTERVAL 1 MONTH)
                    AND YEAR(expense_date_created) = YEAR(NOW() - INTERVAL 1 MONTH))")->row()->total_expenses;
            case 'year':
                return $this->db->query("
                    SELECT SUM(expense_total) AS total_expenses
                    FROM ip_expense_amounts
                    WHERE expense_id IN
                    (SELECT expense_id FROM ip_expenses WHERE YEAR(expense_date_created) = YEAR(NOW()))")->row()->total_expenses;
            case 'last_year':
                return $this->db->query("
                    SELECT SUM(expense_total) AS total_expenses
                    FROM ip_expense_amounts
                    WHERE expense_id IN
                    (SELECT expense_id FROM ip_expenses
                    WHERE YEAR(expense_date_created) = YEAR(NOW() - INTERVAL 1 YEAR))")->row()->total_expenses;
            default:
                return $this->db->query("SELECT SUM(expense_total) AS total_expenses FROM ip_expense_amounts")->row()->total_expenses;
        }
    }

    /**
     * @param string|null $period
     * @return mixed
     */
    public function get_total_paid($period = null)
    {
        switch ($period) {
            case 'month':
                return $this->db->query("
                    SELECT SUM(expense_paid) AS total_paid
                    FROM ip_expense_amounts
                    WHERE expense_id IN
                    (SELECT expense_id FROM ip_expenses
                    WHERE MONTH(expense_date_created) = MONTH(NOW())
                    AND YEAR(expense_date_created) = YEAR(NOW()))")->row()->total_paid;
            case 'last_month':
                return $this->db->query("
                    SELECT SUM(expense_paid) AS total_paid
                    FROM ip_expense_amounts
                    WHERE expense_id IN
                    (SELECT expense_id FROM ip_expenses
                    WHERE MONTH(expense_date_created) = MONTH(NOW() - INTERVAL 1 MONTH)
                    AND YEAR(expense_date_created) = YEAR(NOW() - INTERVAL 1 MONTH))")->row()->total_paid;
            case 'year':
                return $this->db->query("
                    SELECT SUM(expense_paid) AS total_paid
                    FROM ip_expense_amounts
                    WHERE expense_id IN
                    (SELECT expense_id FROM ip_expenses WHERE YEAR(expense_date_created) = YEAR(NOW()))")->row()->total_paid;
            case 'last_year':
                return $this->db->query("
                    SELECT SUM(expense_paid) AS total_paid
                    FROM ip_expense_amounts
                    WHERE expense_id IN
                    (SELECT expense_id FROM ip_expenses
                    WHERE YEAR(expense_date_created) = YEAR(NOW() - INTERVAL 1 YEAR))")->row()->total_paid;
            default:
                return $this->db->query("SELECT SUM(expense_paid) AS total_paid FROM ip_expense_amounts")->row()->total_paid;
        }
    }

    /**
     * @param string|null $period
     * @return mixed
     */
    public function get_total_balance($period = null)
    {
        switch ($period) {
            case 'month':
                return $this->db->query("
                    SELECT SUM(expense_balance) AS total_balance
                    FROM ip_expense_amounts
                    WHERE expense_id IN
                    (SELECT expense_id FROM ip_expenses
                    WHERE MONTH(expense_date_created) = MONTH(NOW())
                    AND YEAR(expense_date_created) = YEAR(NOW()))")->row()->total_balance;
            case 'last_month':
                return $this->db->query("
                    SELECT SUM(expense_balance) AS total_balance
                    FROM ip_expense_amounts
                    WHERE expense_id IN
                    (SELECT expense_id FROM ip_expenses
                    WHERE MONTH(expense_date_created) = MONTH(NOW() - INTERVAL 1 MONTH)
                    AND YEAR(expense_date_created) = YEAR(NOW() - INTERVAL 1 MONTH))")->row()->total_balance;
            case 'year':
                return $this->db->query("
                    SELECT SUM(expense_balance) AS total_balance
                    FROM ip_expense_amounts
                    WHERE expense_id IN
                    (SELECT expense_id FROM ip_expenses WHERE YEAR(expense_date_created) = YEAR(NOW()))")->row()->total_balance;
            case 'last_year':
                return $this->db->query("
                    SELECT SUM(expense_balance) AS total_balance
                    FROM ip_expense_amounts
                    WHERE expense_id IN
                    (SELECT expense_id FROM ip_expenses
                    WHERE YEAR(expense_date_created) = YEAR(NOW() - INTERVAL 1 YEAR))")->row()->total_balance;
            default:
                return $this->db->query("SELECT SUM(expense_balance) AS total_balance FROM ip_expense_amounts")->row()->total_balance;
        }
    }

    /**
     * @param string $period
     * @return array
     */
    public function get_status_totals($period = '')
    {
        switch ($period) {
            case 'this-month':
            case '':
                $results = $this->db->query("
                    SELECT ip_expenses.expense_status_id, 
                    SUM(ip_expense_amounts.expense_total) AS sum_total, 
                    COUNT(*) AS num_total
                    FROM ip_expense_amounts
                    JOIN ip_expenses ON ip_expenses.expense_id = ip_expense_amounts.expense_id
                        AND MONTH(ip_expenses.expense_date_created) = MONTH(NOW())
                        AND YEAR(ip_expenses.expense_date_created) = YEAR(NOW())
                    GROUP BY ip_expenses.expense_status_id")->result_array();
                break;
            case 'last-month':
                $results = $this->db->query("
                    SELECT ip_expenses.expense_status_id, 
                    SUM(ip_expense_amounts.expense_total) AS sum_total, 
                    COUNT(*) AS num_total
                    FROM ip_expense_amounts
                    JOIN ip_expenses ON ip_expenses.expense_id = ip_expense_amounts.expense_id
                        AND MONTH(ip_expenses.expense_date_created) = MONTH(NOW() - INTERVAL 1 MONTH)
                        AND YEAR(ip_expenses.expense_date_created) = YEAR(NOW())
                    GROUP BY ip_expenses.expense_status_id")->result_array();
                break;
            case 'this-quarter':
                $results = $this->db->query("
                    SELECT ip_expenses.expense_status_id, 
                    SUM(ip_expense_amounts.expense_total) AS sum_total, 
                    COUNT(*) AS num_total
                    FROM ip_expense_amounts
                    JOIN ip_expenses ON ip_expenses.expense_id = ip_expense_amounts.expense_id
                        AND QUARTER(ip_expenses.expense_date_created) = QUARTER(NOW())
                        AND YEAR(ip_expenses.expense_date_created) = YEAR(NOW())
                    GROUP BY ip_expenses.expense_status_id")->result_array();
                break;
            case 'this-year':
                $results = $this->db->query("
                    SELECT ip_expenses.expense_status_id, 
                    SUM(ip_expense_amounts.expense_total) AS sum_total, 
                    COUNT(*) AS num_total
                    FROM ip_expense_amounts
                    JOIN ip_expenses ON ip_expenses.expense_id = ip_expense_amounts.expense_id
                        AND YEAR(ip_expenses.expense_date_created) = YEAR(NOW())
                    GROUP BY ip_expenses.expense_status_id")->result_array();
                break;
            case 'last-year':
                $results = $this->db->query("
                    SELECT ip_expenses.expense_status_id, 
                    SUM(ip_expense_amounts.expense_total) AS sum_total, 
                    COUNT(*) AS num_total
                    FROM ip_expense_amounts
                    JOIN ip_expenses ON ip_expenses.expense_id = ip_expense_amounts.expense_id
                        AND YEAR(ip_expenses.expense_date_created) = YEAR(NOW() - INTERVAL 1 YEAR)
                    GROUP BY ip_expenses.expense_status_id")->result_array();
                break;
        }

        $return = array();

        foreach ($this->mdl_expenses->statuses() as $key => $status) {
            $return[$key] = array(
                'expense_status_id' => $key,
                'class' => $status['class'],
                'label' => $status['label'],
                'href' => $status['href'],
                'sum_total' => 0,
                'num_total' => 0
            );
        }

        foreach ($results as $result) {
            $return[$result['expense_status_id']] = array_merge($return[$result['expense_status_id']], $result);
        }

        return $return;
    }

    /**
     * @param string $period
     * @return array
     */
    public function get_category_totals($period = '')
    {
        switch ($period) {
            case 'this-month':
            case '':
                $results = $this->db->query("
                    SELECT ip_expense_categories.expense_category_id,
                    ip_expense_categories.expense_category_name,
                    SUM(ip_expense_amounts.expense_total) AS sum_total, 
                    COUNT(*) AS num_total
                    FROM ip_expense_amounts
                    JOIN ip_expenses ON ip_expenses.expense_id = ip_expense_amounts.expense_id
                    JOIN ip_expense_categories ON ip_expense_categories.expense_category_id = ip_expenses.expense_category_id
                        AND MONTH(ip_expenses.expense_date_created) = MONTH(NOW())
                        AND YEAR(ip_expenses.expense_date_created) = YEAR(NOW())
                    GROUP BY ip_expense_categories.expense_category_id
                    ORDER BY sum_total DESC")->result_array();
                break;
            case 'this-year':
                $results = $this->db->query("
                    SELECT ip_expense_categories.expense_category_id,
                    ip_expense_categories.expense_category_name,
                    SUM(ip_expense_amounts.expense_total) AS sum_total, 
                    COUNT(*) AS num_total
                    FROM ip_expense_amounts
                    JOIN ip_expenses ON ip_expenses.expense_id = ip_expense_amounts.expense_id
                    JOIN ip_expense_categories ON ip_expense_categories.expense_category_id = ip_expenses.expense_category_id
                        AND YEAR(ip_expenses.expense_date_created) = YEAR(NOW())
                    GROUP BY ip_expense_categories.expense_category_id
                    ORDER BY sum_total DESC")->result_array();
                break;
            default:
                $results = $this->db->query("
                    SELECT ip_expense_categories.expense_category_id,
                    ip_expense_categories.expense_category_name,
                    SUM(ip_expense_amounts.expense_total) AS sum_total, 
                    COUNT(*) AS num_total
                    FROM ip_expense_amounts
                    JOIN ip_expenses ON ip_expenses.expense_id = ip_expense_amounts.expense_id
                    JOIN ip_expense_categories ON ip_expense_categories.expense_category_id = ip_expenses.expense_category_id
                    GROUP BY ip_expense_categories.expense_category_id
                    ORDER BY sum_total DESC")->result_array();
                break;
        }

        return $results;
    }
}
