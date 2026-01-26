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
class Mdl_Expenses extends Response_Model
{
    public $table = 'ip_expenses';

    public $primary_key = 'ip_expenses.expense_id';

    public $date_modified_field = 'expense_date_modified';

    /**
     * @return array
     */
    public function statuses()
    {
        return [
            '1' => [
                'label' => trans('new'),
                'class' => 'new',
                'href'  => 'expenses/status/new',
            ],
            '2' => [
                'label' => trans('confirmed'),
                'class' => 'confirmed',
                'href'  => 'expenses/status/confirmed',
            ],
            '3' => [
                'label' => trans('paid'),
                'class' => 'paid',
                'href'  => 'expenses/status/paid',
            ],
        ];
    }

    public function default_select()
    {
        $this->db->select("
            SQL_CALC_FOUND_ROWS
            ip_expense_categories.*,
            ip_users.*,
            ip_expense_amounts.expense_amount_id,
            IFnull(ip_expense_amounts.expense_item_subtotal, '0.00') AS expense_item_subtotal,
            IFnull(ip_expense_amounts.expense_item_tax_total, '0.00') AS expense_item_tax_total,
            IFnull(ip_expense_amounts.expense_tax_total, '0.00') AS expense_tax_total,
            IFnull(ip_expense_amounts.expense_total, '0.00') AS expense_total,
            IFnull(ip_expense_amounts.expense_paid, '0.00') AS expense_paid,
            IFnull(ip_expense_amounts.expense_balance, '0.00') AS expense_balance,
            ip_expense_amounts.expense_sign AS expense_sign,
            (CASE WHEN ip_expenses.expense_status_id != 3 AND DATEDIFF(NOW(), expense_date_due) > 0 THEN 1 ELSE 0 END) is_overdue,
            DATEDIFF(NOW(), expense_date_due) AS days_overdue,
            ip_expenses.*", false);
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_expenses.expense_date_created DESC');
    }

    public function default_join()
    {
        $this->db->join('ip_expense_categories', 'ip_expense_categories.expense_category_id = ip_expenses.expense_category_id');
        $this->db->join('ip_users', 'ip_users.user_id = ip_expenses.user_id');
        $this->db->join('ip_expense_amounts', 'ip_expense_amounts.expense_id = ip_expenses.expense_id', 'left');
    }

    /**
     * @return array
     */
    public function validation_rules()
    {
        return [
            'expense_category_id' => [
                'field' => 'expense_category_id',
                'label' => trans('expense_category'),
                'rules' => 'required',
            ],
            'expense_date_created' => [
                'field' => 'expense_date_created',
                'label' => trans('expense_date'),
                'rules' => 'required',
            ],
            'expense_time_created' => [
                'rules' => 'required',
            ],
            'expense_currency_code' => [
                'field' => 'expense_currency_code',
                'label' => trans('currency'),
            ],
            'expense_rate' => [
                'field' => 'expense_rate',
                'label' => trans('exchange_rate'),
                'rules' => 'required',
            ],
            'user_id' => [
                'field' => 'user_id',
                'label' => trans('user'),
                'rules' => 'required',
            ],
        ];
    }

    /**
     * @return array
     */
    public function validation_rules_save_expense()
    {
        return [
            'expense_number' => [
                'field' => 'expense_number',
                'label' => trans('expense') . ' #',
                'rules' => 'is_unique[ip_expenses.expense_number' . (($this->id) ? '.expense_id.' . $this->id : '') . ']',
            ],
            'expense_date_created' => [
                'field' => 'expense_date_created',
                'label' => trans('date'),
                'rules' => 'required',
            ],
            'expense_date_due' => [
                'field' => 'expense_date_due',
                'label' => trans('due_date'),
                'rules' => 'required',
            ],
            'expense_time_created' => [
                'rules' => 'required',
            ],
            'expense_password' => [
                'field' => 'expense_password',
                'label' => trans('expense_password'),
            ],
        ];
    }

    /**
     * @param null $db_array
     * @param bool $include_expense_tax_rates
     *
     * @return int|null
     */
    public function create($db_array = null, $include_expense_tax_rates = true)
    {
        $expense_id = parent::save(null, $db_array);

        $exp = $this->where('ip_expenses.expense_id', $expense_id)->get()->row();

        // Create an expense amount record
        $db_array = [
            'expense_id' => $expense_id,
        ];

        $this->db->insert('ip_expense_amounts', $db_array);

        if ($include_expense_tax_rates) {
            // Create the default expense tax record if applicable
            if (get_setting('default_invoice_tax_rate')) {
                $db_array = array(
                    'expense_id' => $expense_id,
                    'tax_rate_id' => get_setting('default_invoice_tax_rate'),
                    'include_item_tax' => get_setting('default_include_item_tax', 0),
                    'include_tax' => get_setting('default_include_tax', 0),
                    'expense_tax_rate_amount' => 0
                );

                $this->db->insert('ip_expense_tax_rates', $db_array);
            }
        }

        return $expense_id;
    }

    /**
     * Copies expense items, tax rates, etc from source to target.
     *
     * @param int  $source_id
     * @param int  $target_id
     */
    public function copy_expense($source_id, $target_id)
    {
        $this->load->model('expenses/mdl_expense_items');
        $this->load->model('expenses/mdl_expense_tax_rates');

        // Copy the items
        $expense_items = $this->mdl_expense_items->where('expense_id', $source_id)->get()->result();

        foreach ($expense_items as $expense_item) {
            $db_array = [
                'expense_id'           => $target_id,
                'item_tax_rate_id'     => $expense_item->item_tax_rate_id,
                'item_name'            => $expense_item->item_name,
                'item_description'     => $expense_item->item_description,
                'item_quantity'        => $expense_item->item_quantity,
                'item_price'           => $expense_item->item_price,
                'item_discount_amount' => $expense_item->item_discount_amount,
                'item_discount_percent' => $expense_item->item_discount_percent,
                'item_order'           => $expense_item->item_order,
            ];

            $this->mdl_expense_items->save(null, $db_array);
        }

        // Copy the tax rates
        $expense_tax_rates = $this->mdl_expense_tax_rates->where('expense_id', $source_id)->get()->result();

        foreach ($expense_tax_rates as $expense_tax_rate) {
            $db_array = array(
                'expense_id' => $target_id,
                'tax_rate_id' => $expense_tax_rate->tax_rate_id,
                'include_item_tax' => $expense_tax_rate->include_item_tax,
                'include_tax' => $expense_tax_rate->include_tax,
                'expense_tax_rate_amount' => $expense_tax_rate->expense_tax_rate_amount,
                'expense_tax_rate_percent' => $expense_tax_rate->expense_tax_rate_percent
            );

            $this->mdl_expense_tax_rates->save(null, $db_array);
        }

        // Copy the custom fields
        $this->load->model('custom_fields/mdl_expense_custom');
        $custom_fields = $this->mdl_expense_custom->where('expense_id', $source_id)->get()->result();

        $form_data = [];
        foreach ($custom_fields as $field) {
            $form_data[$field->expense_custom_fieldid] = $field->expense_custom_fieldvalue;
        }
        $this->mdl_expense_custom->save_custom($target_id, $form_data);
    }

    /**
     * @return array
     */
    public function db_array()
    {
        $db_array = parent::db_array();

        // Set default status if not provided
        if (!isset($db_array['expense_status_id']) || empty($db_array['expense_status_id'])) {
            $db_array['expense_status_id'] = 1; // Default: new
        }

        // Check if expense number is needed
        $generate_expense_number = get_setting('expenses_generate_number_for_new');

        if ($db_array['expense_status_id'] === 1 && $generate_expense_number == 1) {
            $db_array['expense_number'] = $this->get_expense_number();
        } elseif ($db_array['expense_status_id'] != 1) {
            $db_array['expense_number'] = $this->get_expense_number();
        } else {
            $db_array['expense_number'] = '';
        }

        // Set default currency code
        if (empty($db_array['expense_currency_code'])) {
            $db_array['expense_currency_code'] = get_setting('expenses_default_currency_code', 'USD');
        }

        // Set default rate
        if (empty($db_array['expense_rate'])) {
            $db_array['expense_rate'] = '1.0000';
        }

        // Set due date
        $db_array['expense_date_due'] = $this->get_date_due($db_array['expense_date_created']);

        // Set default terms and notes
        $db_array['expense_terms'] = get_setting('expenses_default_terms', 'Оплата должна быть произведена в течение 30 дней');
        $db_array['expense_notes'] = get_setting('expenses_default_notes', 'Расход по модулю InvoicePlane');

        // Generate the unique url key
        $db_array['expense_url_key'] = $this->get_url_key();

        return $db_array;
    }

    /**
     * @param $expense_date_created
     *
     * @return string
     */
    public function get_date_due($expense_date_created)
    {
        $expense_date_due = new DateTime($expense_date_created);
        $expense_date_due->add(new DateInterval('P' . get_setting('expenses_due_after_days', 30) . 'D'));

        return $expense_date_due->format('Y-m-d');
    }

    /**
     * @return string
     */
    public function get_expense_number()
    {
        $prefix = get_setting('expenses_next_number_prefix', 'EXP');
        $next_number = get_setting('expenses_next_number', 1);

        return $prefix . str_pad($next_number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * @return string
     */
    public function get_url_key()
    {
        $this->load->helper('string');

        return random_string('alnum', 32);
    }

    /**
     * @param int $expense_id
     */
    public function delete($expense_id)
    {
        parent::delete($expense_id);

        $this->load->helper('orphan');
        delete_orphans();
    }

    // Filter methods for different expense statuses
    public function is_new()
    {
        $this->filter_where('expense_status_id', 1);

        return $this;
    }

    public function is_confirmed()
    {
        $this->filter_where('expense_status_id', 2);

        return $this;
    }

    public function is_paid()
    {
        $this->filter_where('expense_status_id', 3);

        return $this;
    }

    public function is_overdue()
    {
        $this->filter_having('is_overdue', 1);

        return $this;
    }

    public function by_category($category_id)
    {
        $this->filter_where('ip_expenses.expense_category_id', $category_id);

        return $this;
    }

    public function by_user($user_id)
    {
        $this->filter_where('ip_expenses.user_id', $user_id);

        return $this;
    }

    /**
     * @param int $expense_id
     */
    public function mark_confirmed($expense_id)
    {
        $expense = $this->get_by_id($expense_id);

        if ( ! empty($expense)) {
            if ($expense->expense_status_id == 1) {
                $expense_number = $expense->expense_number;

                $this->db->where('expense_id', $expense_id);
                $this->db->set('expense_status_id', 2);
                $this->db->set('expense_number', $expense_number);
                $this->db->update('ip_expenses');

                $this->update_expense_due_date($expense_id);
            }
        }
    }

    /**
     * @param int $expense_id
     */
    public function mark_paid($expense_id)
    {
        $expense = $this->get_by_id($expense_id);

        if ( ! empty($expense)) {
            $this->db->where('expense_id', $expense_id);
            $this->db->set('expense_status_id', 3);
            $this->db->update('ip_expenses');
        }
    }

    /**
     * Update the expense due date.
     *
     * @param int $expense_id
     */
    public function update_expense_due_date($expense_id)
    {
        $expense = $this->get_by_id($expense_id);

        if ( ! empty($expense) && $expense->is_read_only != 1) {
            $current_date = date_to_mysql(date(date_format_setting()));
            $this->db->where('expense_id', $expense_id);
            $this->db->set('expense_date_due', $this->get_date_due($current_date));
            $this->db->update('ip_expenses');
        }
    }
}
