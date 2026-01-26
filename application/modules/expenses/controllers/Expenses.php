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
class Expenses extends Admin_Controller
{
    /**
     * Expenses constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('expenses/mdl_expenses');
    }

    public function index()
    {
        // Display all expenses by default
        redirect('expenses/status/all');
    }

    /**
     * @param string $status
     * @param int    $page
     */
    public function status($status = 'all', $page = 0)
    {
        // Determine which group of expenses to load
        switch ($status) {
            case 'new':
                $this->mdl_expenses->is_new();
                break;
            case 'confirmed':
                $this->mdl_expenses->is_confirmed();
                break;
            case 'paid':
                $this->mdl_expenses->is_paid();
                break;
            case 'overdue':
                $this->mdl_expenses->is_overdue();
                break;
        }

        $this->mdl_expenses->paginate(site_url('expenses/status/' . $status), $page);
        $expenses = $this->mdl_expenses->result();

        $this->layout->set(
            [
                'expenses'           => $expenses,
                'status'             => $status,
                'filter_display'     => true,
                'filter_placeholder' => trans('filter_expenses'),
                'filter_method'      => 'filter_expenses',
                'expense_statuses'   => $this->mdl_expenses->statuses(),
            ]
        );

        $this->layout->buffer('content', 'expenses/index');
        $this->layout->render();
    }

    public function category($category_id, $page = 0)
    {
        $this->mdl_expenses->by_category($category_id);
        $this->mdl_expenses->paginate(site_url('expenses/category/' . $category_id), $page);
        $expenses = $this->mdl_expenses->result();

        // Get category info
        $this->db->where('expense_category_id', $category_id);
        $category = $this->db->get('ip_expense_categories')->row();

        $this->layout->set(
            [
                'expenses'           => $expenses,
                'category_id'        => $category_id,
                'category'           => $category,
                'filter_display'     => true,
                'filter_placeholder' => trans('filter_expenses'),
                'filter_method'      => 'filter_expenses',
                'expense_statuses'   => $this->mdl_expenses->statuses(),
            ]
        );

        $this->layout->buffer('content', 'expenses/index');
        $this->layout->render();
    }

    public function create()
    {
        $this->load->model('expenses/mdl_expense_categories');
        $this->load->model('users/mdl_users');
        $this->load->model('expenses/mdl_expense_items');
        $this->load->model('tax_rates/mdl_tax_rates');
        $this->load->model('expenses/mdl_expense_tax_rates');
        $this->load->model('custom_fields/mdl_custom_fields');

        // Handle form submission
        if ($this->input->post('btn_cancel')) {
            redirect('expenses/index');
        }

        if ($this->input->post('btn_submit')) {
            $this->load->helper('custom_values');

            if ($this->mdl_expenses->run_validation()) {
                $expense_id = $this->mdl_expenses->save();

                // Save expense items
                $this->load->helper('expense');
                $items = $this->input->post('items');
                $has_items = false;
                if (!empty($items)) {
                    foreach ($items as $item) {
                        if (!empty($item['item_name'])) {
                            $has_items = true;
                            break;
                        }
                    }
                }

                if ($has_items) {
                    save_expense_items($expense_id, $items);
                } else {
                    // If quick total entered and no items, save it directly
                    $quick_total = $this->input->post('expense_total');
                    if (!empty($quick_total) && $quick_total > 0) {
                        $this->db->where('expense_id', $expense_id);
                        $this->db->update('ip_expense_amounts', [
                            'expense_item_subtotal' => $quick_total,
                            'expense_total' => $quick_total,
                            'expense_balance' => $quick_total
                        ]);
                    }
                }

                // Save expense tax rates and recalculate totals with tax
                $tax_rates_data = $this->input->post('tax_rates');
                if (!empty($tax_rates_data)) {
                    save_expense_tax_rates($expense_id, $tax_rates_data);
                    
                    // Recalculate with taxes
                    $this->load->model('expenses/mdl_expense_amounts');
                    $this->mdl_expense_amounts->calculate_expense_taxes($expense_id);
                }

                // Save custom fields
                $this->load->model('custom_fields/mdl_expense_custom');
                $custom_fields = $this->input->post('custom');
                if (!empty($custom_fields)) {
                    $this->mdl_expense_custom->save_custom($expense_id, $custom_fields);
                }

                redirect('expenses/view/' . $expense_id);
            }
        }

        // Load form data
        $expense_categories = $this->mdl_expense_categories->where('is_active', 1)->get()->result();
        $users = $this->mdl_users->get()->result();
        $tax_rates = $this->mdl_tax_rates->get()->result();

        $custom_fields = $this->mdl_custom_fields->by_table('ip_expense_custom')->get()->result();
        $custom_values = [];
        foreach ($custom_fields as $custom_field) {
            if (in_array($custom_field->custom_field_type, $this->mdl_custom_values->custom_value_fields())) {
                $values = $this->mdl_custom_values->get_by_fid($custom_field->custom_field_id)->result();
                $custom_values[$custom_field->custom_field_id] = $values;
            }
        }

        $this->layout->set(
            [
                'expense_categories'  => $expense_categories,
                'users'              => $users,
                'tax_rates'          => $tax_rates,
                'custom_fields'      => $custom_fields,
                'custom_values'      => $custom_values,
                'expense_statuses'   => $this->mdl_expenses->statuses(),
                'expense_id'         => null,
            ]
        );

        $this->layout->buffer('content', 'expenses/modal_create_expense');
        $this->layout->render();
    }

    public function edit($expense_id)
    {
        $this->load->model('expenses/mdl_expense_categories');
        $this->load->model('users/mdl_users');
        $this->load->model('expenses/mdl_expense_items');
        $this->load->model('tax_rates/mdl_tax_rates');
        $this->load->model('expenses/mdl_expense_tax_rates');
        $this->load->model('custom_fields/mdl_custom_fields');
        $this->load->model('custom_fields/mdl_expense_custom');

        // Handle form submission
        if ($this->input->post('btn_cancel')) {
            redirect('expenses/index');
        }

        if ($this->input->post('btn_submit')) {
            if ($this->mdl_expenses->run_validation()) {
                $this->mdl_expenses->save($expense_id);

                // Save expense items
                $this->load->helper('expense');
                save_expense_items($expense_id, $this->input->post('items'));

                // Save expense tax rates
                $this->load->helper('expense');
                save_expense_tax_rates($expense_id, $this->input->post('tax_rates'));

                // Save custom fields
                $custom_fields = $this->input->post('custom');
                if (!empty($custom_fields)) {
                    $this->mdl_expense_custom->save_custom($expense_id, $custom_fields);
                }

                redirect('expenses/view/' . $expense_id);
            }
        }

        // Load expense data
        $expense = $this->mdl_expenses->get_by_id($expense_id);

        if (!$expense) {
            show_404();
        }

        // Set form values
        $this->mdl_expenses->set_form_value('expense_number', $expense->expense_number);
        $this->mdl_expenses->set_form_value('expense_category_id', $expense->expense_category_id);
        $this->mdl_expenses->set_form_value('expense_date_created', $expense->expense_date_created);
        $this->mdl_expenses->set_form_value('expense_date_due', $expense->expense_date_due);
        $this->mdl_expenses->set_form_value('expense_currency_code', $expense->expense_currency_code);
        $this->mdl_expenses->set_form_value('expense_rate', $expense->expense_rate);
        $this->mdl_expenses->set_form_value('expense_status_id', $expense->expense_status_id);
        $this->mdl_expenses->set_form_value('expense_notes', $expense->expense_notes);
        $this->mdl_expenses->set_form_value('expense_terms', $expense->expense_terms);
        $this->mdl_expenses->set_form_value('user_id', $expense->user_id);

        // Load categories and other data
        $expense_categories = $this->mdl_expense_categories->where('is_active', 1)->get()->result();
        $users = $this->mdl_users->get()->result();
        $tax_rates = $this->mdl_tax_rates->get()->result();
        $custom_fields = $this->mdl_custom_fields->by_table('ip_expense_custom')->get()->result();

        $custom_values = [];
        foreach ($custom_fields as $custom_field) {
            if (in_array($custom_field->custom_field_type, $this->mdl_custom_values->custom_value_fields())) {
                $values = $this->mdl_custom_values->get_by_fid($custom_field->custom_field_id)->result();
                $custom_values[$custom_field->custom_field_id] = $values;
            }
        }

        $this->layout->set(
            [
                'expense_categories'  => $expense_categories,
                'users'              => $users,
                'tax_rates'          => $tax_rates,
                'custom_fields'      => $custom_fields,
                'custom_values'      => $custom_values,
                'expense_statuses'   => $this->mdl_expenses->statuses(),
                'expense_id'         => $expense_id,
                'expense'            => $expense,
            ]
        );

        $this->layout->buffer('content', 'expenses/modal_edit_expense');
        $this->layout->render();
    }

    public function view($expense_id)
    {
        $this->load->model(
            [
                'expenses/mdl_expense_items',
                'tax_rates/mdl_tax_rates',
                'expenses/mdl_expense_tax_rates',
                'custom_fields/mdl_custom_fields',
            ]
        );

        $this->load->helper('custom_values');
        $this->load->model('custom_values/mdl_custom_values');
        $this->load->model('custom_fields/mdl_expense_custom');
        $this->load->model('expenses/mdl_expense_amounts');

        $fields = $this->mdl_expense_custom->by_id($expense_id)->get()->result();
        $expense = $this->mdl_expenses->get_by_id($expense_id);

        if (!$expense) {
            show_404();
        }

        $custom_fields = $this->mdl_custom_fields->by_table('ip_expense_custom')->get()->result();
        $custom_values = [];
        foreach ($custom_fields as $custom_field) {
            if (in_array($custom_field->custom_field_type, $this->mdl_custom_values->custom_value_fields())) {
                $values = $this->mdl_custom_values->get_by_fid($custom_field->custom_field_id)->result();
                $custom_values[$custom_field->custom_field_id] = $values;
            }
        }

        foreach ($custom_fields as $cfield) {
            foreach ($fields as $fvalue) {
                if ($fvalue->expense_custom_fieldid == $cfield->custom_field_id) {
                    $this->mdl_expenses->set_form_value(
                        'custom[' . $cfield->custom_field_id . ']',
                        $fvalue->expense_custom_fieldvalue
                    );
                    break;
                }
            }
        }

        $this->layout->set(
            [
                'expense'           => $expense,
                'items'             => $this->mdl_expense_items->where('expense_id', $expense_id)->get()->result(),
                'expense_id'        => $expense_id,
                'tax_rates'         => $this->mdl_tax_rates->get()->result(),
                'expense_tax_rates' => $this->mdl_expense_tax_rates->where('expense_id', $expense_id)->get()->result(),
                'custom_fields'     => $custom_fields,
                'custom_values'     => $custom_values,
                'custom_js_vars'    => [
                    'currency_symbol'           => get_setting('currency_symbol'),
                    'currency_symbol_placement' => get_setting('currency_symbol_placement'),
                    'decimal_point'             => get_setting('decimal_point'),
                ],
                'expense_statuses' => $this->mdl_expenses->statuses(),
            ]
        );

        $this->layout->buffer('content', 'expenses/view');
        $this->layout->render();
    }

    public function delete($expense_id)
    {
        $this->mdl_expenses->delete($expense_id);

        // Redirect to expense index
        redirect('expenses/index');
    }

    public function mark_confirmed($expense_id)
    {
        $this->mdl_expenses->mark_confirmed($expense_id);
        redirect('expenses/view/' . $expense_id);
    }

    public function mark_paid($expense_id)
    {
        $this->mdl_expenses->mark_paid($expense_id);
        redirect('expenses/view/' . $expense_id);
    }

    public function delete_expense_tax($expense_id, $expense_tax_rate_id)
    {
        $this->load->model('expenses/mdl_expense_tax_rates');
        $this->mdl_expense_tax_rates->delete($expense_tax_rate_id);

        $this->load->model('expenses/mdl_expense_amounts');
        $this->mdl_expense_amounts->calculate($expense_id);

        redirect('expenses/view/' . $expense_id);
    }

    public function recalculate_all_expenses()
    {
        $this->db->select('expense_id');
        $expense_ids = $this->db->get('ip_expenses')->result();

        $this->load->model('expenses/mdl_expense_amounts');

        foreach ($expense_ids as $expense_id) {
            $this->mdl_expense_amounts->calculate($expense_id->expense_id);
        }
    }

    public function settings()
    {
        $this->load->model('tax_rates/mdl_tax_rates');

        if ($this->input->post('btn_save')) {
            $settings = [
                'expenses_next_number_prefix',
                'expenses_next_number',
                'expenses_due_after_days',
                'expenses_default_currency_code',
                'expenses_default_item_tax_rate',
                'expenses_default_expense_tax_rate',
                'expenses_default_terms',
                'expenses_default_notes'
            ];

            foreach ($settings as $setting) {
                $value = $this->input->post($setting);
                if ($value !== null) {
                    $this->mdl_settings->save($setting, $value);
                }
            }

            // Checkbox settings
            $this->mdl_settings->save('expenses_generate_number_for_new', $this->input->post('expenses_generate_number_for_new') ? 1 : 0);
            $this->mdl_settings->save('expenses_mark_as_paid_on_payment', $this->input->post('expenses_mark_as_paid_on_payment') ? 1 : 0);

            $this->session->set_flashdata('alert_success', trans('settings_saved'));
            redirect('expenses/settings');
        }

        $this->layout->set([
            'tax_rates' => $this->mdl_tax_rates->get()->result()
        ]);

        $this->layout->buffer('content', 'expenses/settings');
        $this->layout->render();
    }
}
