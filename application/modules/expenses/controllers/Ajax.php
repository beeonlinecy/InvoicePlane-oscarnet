<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

#[AllowDynamicProperties]
class Ajax extends Admin_Controller
{

    public $ajax_controller = true;

    public function save()
    {
        $this->load->model('invoices/mdl_items');
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('units/mdl_units');
        $this->load->model('invoices/mdl_expense_sumex');

        $expense_id = $this->security->xss_clean($this->input->post('expense_id', true));

        $this->mdl_invoices->set_id($expense_id);

        if ($this->mdl_invoices->run_validation('validation_rules_save_invoice')) {
            $items = json_decode($this->input->post('items'));

            foreach ($items as $item) {
                // Check if an item has either a quantity + price or name or description
                if (!empty($item->item_name)) {
                    $item->item_quantity = ($item->item_quantity ? standardize_amount($item->item_quantity) : floatval(0));
                    $item->item_price = ($item->item_price ? standardize_amount($item->item_price) : floatval(0));
                    $item->item_discount_amount = ($item->item_discount_amount) ? standardize_amount($item->item_discount_amount) : null;
                    $item->item_product_id = ($item->item_product_id ? $item->item_product_id : null);
                    if (property_exists($item, 'item_date')) {
                        $item->item_date = ($item->item_date ? date_to_mysql($item->item_date) : null);
                    }
                    $item->item_product_unit_id = ($item->item_product_unit_id ? $item->item_product_unit_id : null);
                    $item->item_product_unit = $this->mdl_units->get_name($item->item_product_unit_id, $item->item_quantity);
                    $item_id = ($item->item_id) ?: null;
                    unset($item->item_id);

                    if (!$item->item_task_id) {
                        unset($item->item_task_id);
                    } else {
                        $this->load->model('tasks/mdl_tasks');
                        $this->mdl_tasks->update_status(4, $item->item_task_id);
                    }

                    $this->mdl_items->save($item_id, $item);
                } elseif (empty($item->item_name) && (!empty($item->item_quantity) || !empty($item->item_price))) {
                    // Throw an error message and use the form validation for that
                    $this->load->library('form_validation');
                    $this->form_validation->set_rules('item_name', trans('item'), 'required');
                    $this->form_validation->run();

                    $response = [
                        'success' => 0,
                        'validation_errors' => [
                            'item_name' => form_error('item_name', '', ''),
                        ],
                    ];

                    echo json_encode($response);
                    exit;
                }
            }

            $expense_status = $this->input->post('expense_status_id');

            if ($this->input->post('expense_discount_amount') === '') {
                $expense_discount_amount = floatval(0);
            } else {
                $expense_discount_amount = $this->input->post('expense_discount_amount');
            }

            if ($this->input->post('expense_discount_percent') === '') {
                $expense_discount_percent = floatval(0);
            } else {
                $expense_discount_percent = $this->input->post('expense_discount_percent');
            }

            // Generate new invoice number if needed
            $expense_number = $this->input->post('expense_number');

            if (empty($expense_number) && $expense_status != 1) {
                $expense_group_id = $this->mdl_expenses->get_expense_group_id($expense_id);
                $expense_number = $this->mdl_expenses->get_expense_number($expense_group_id);
            }

            $db_array = [
                'expense_number' => $expense_number,
                'expense_terms' => $this->security->xss_clean($this->input->post('expense_terms')),
                'expense_date_created' => date_to_mysql($this->input->post('expense_date_created')),
                'expense_date_due' => date_to_mysql($this->input->post('expense_date_due')),
                'expense_password' => $this->security->xss_clean($this->input->post('expense_password')),
                'expense_status_id' => $expense_status,
                'payment_method' => $this->security->xss_clean($this->input->post('payment_method')),
                'expense_discount_amount' => standardize_amount($expense_discount_amount),
                'expense_discount_percent' => standardize_amount($expense_discount_percent),
            ];

            // check if status changed to sent, the feature is enabled and settings is set to sent
            if ($this->config->item('disable_read_only') === false) {
                if ($expense_status == get_setting('read_only_toggle')) {
                    $db_array['is_read_only'] = 1;
                }
            }

            $this->mdl_invoices->save($expense_id, $db_array);
            $sumexInvoice = $this->mdl_invoices->where('sumex_invoice', $expense_id)->get()->num_rows();

            if ($sumexInvoice >= 1) {
                $sumex_array = [
                    'sumex_invoice' => $expense_id,
                    'sumex_reason' => $this->input->post('expense_sumex_reason'),
                    'sumex_diagnosis' => $this->input->post('expense_sumex_diagnosis'),
                    'sumex_treatmentstart' => date_to_mysql($this->input->post('expense_sumex_treatmentstart')),
                    'sumex_treatmentend' => date_to_mysql($this->input->post('expense_sumex_treatmentend')),
                    'sumex_casedate' => date_to_mysql($this->input->post('expense_sumex_casedate')),
                    'sumex_casenumber' => $this->input->post('expense_sumex_casenumber'),
                    'sumex_observations' => $this->input->post('expense_sumex_observations'),
                ];
                $this->mdl_expense_sumex->save($expense_id, $sumex_array);
            }

            // Recalculate for discounts
            $this->load->model('invoices/mdl_expense_amounts');
            $this->mdl_expense_amounts->calculate($expense_id);

            $response = [
                'success' => 1,
            ];
        } else {

            log_message('error', '980: I wasnt able to run the validation validation_rules_save_invoice');

            $this->load->helper('json_error');
            $response = [
                'success' => 0,
                'validation_errors' => json_errors(),
            ];
        }

        // Save all custom fields
        if ($this->input->post('custom')) {
            $db_array = [];

            $values = [];
            foreach ($this->input->post('custom') as $custom) {
                if (preg_match("/^(.*)\[\]$/i", $custom['name'], $matches)) {
                    $values[$matches[1]][] = $custom['value'];
                } else {
                    $values[$custom['name']] = $custom['value'];
                }
            }

            foreach ($values as $key => $value) {
                preg_match("/^custom\[(.*?)\](?:\[\]|)$/", $key, $matches);
                if ($matches) {
                    $db_array[$matches[1]] = $value;
                }
            }


            $this->load->model('custom_fields/mdl_expense_custom');
            $result = $this->mdl_expense_custom->save_custom($expense_id, $db_array);
            if ($result !== true) {
                $response = [
                    'success' => 0,
                    'validation_errors' => $result,
                ];

                echo json_encode($response);
                exit;
            }
        }

        echo json_encode($response);
    }

    public function save_expense_tax_rate()
    {
        $this->load->model('invoices/mdl_expense_tax_rates');

        if ($this->mdl_expense_tax_rates->run_validation()) {
            $this->mdl_expense_tax_rates->save();

            $response = [
                'success' => 1,
            ];
        } else {
            $response = [
                'success' => 0,
                'validation_errors' => $this->mdl_expense_tax_rates->validation_errors,
            ];
        }

        echo json_encode($response);
    }

    public function create()
    {
        $this->load->model('expenses/mdl_expenses');

        if ($this->mdl_expenses->run_validation()) {
            $expense_id = $this->mdl_expenses->create();

            $response = [
                'success' => 1,
                'expense_id' => $expense_id,
            ];
        } else {
            $this->load->helper('json_error');
            $response = [
                'success' => 0,
                'validation_errors' => json_errors(),
            ];
        }

        echo json_encode($response);
    }

    public function create_recurring()
    {
        $this->load->model('invoices/mdl_invoices_recurring');

        if ($this->mdl_invoices_recurring->run_validation()) {
            $this->mdl_invoices_recurring->save();

            $response = [
                'success' => 1,
            ];
        } else {
            $this->load->helper('json_error');
            $response = [
                'success' => 0,
                'validation_errors' => json_errors(),
            ];
        }

        echo json_encode($response);
    }

    public function get_item()
    {
        $this->load->model('invoices/mdl_items');

        $item = $this->mdl_items->get_by_id($this->security->xss_clean($this->input->post('item_id', true)));

        echo json_encode($item);
    }

    public function modal_create_expense()
    {
        $this->load->module('layout');
        //$this->load->model('expense_groups/mdl_expense_groups');
        $this->load->model('tax_rates/mdl_tax_rates');
        $this->load->model('companies/mdl_companies');

        $data = [
            //'expense_groups' => $this->mdl_expense_groups->get()->result(),
            'tax_rates' => $this->mdl_tax_rates->get()->result(),
            'company' => $this->mdl_companies->get_by_id($this->input->post('company_id')),
            'companies' => $this->mdl_companies->get_latest(),
        ];

        $this->layout->load_view('expenses/modal_create_expense', $data);
    }

    public function modal_create_recurring()
    {
        $this->load->module('layout');

        $this->load->model('mdl_invoices_recurring');

        $data = [
            'expense_id' => $this->security->xss_clean($this->input->post('expense_id')),
            'recur_frequencies' => $this->mdl_invoices_recurring->recur_frequencies,
        ];

        $this->layout->load_view('invoices/modal_create_recurring', $data);
    }

    public function get_recur_start_date()
    {
        $expense_date = $this->input->post('expense_date');
        $recur_frequency = $this->input->post('recur_frequency');

        echo increment_user_date($expense_date, $recur_frequency);
    }

    public function modal_change_client()
    {
        $this->load->module('layout');
        $this->load->model('clients/mdl_clients');

        $data = [
            'client_id' => $this->security->xss_clean($this->input->post('client_id')),
            'expense_id' => $this->security->xss_clean($this->input->post('expense_id')),
            'clients' => $this->mdl_clients->get_latest(),
        ];

        $this->layout->load_view('invoices/modal_change_client', $data);
    }

    public function change_client()
    {
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('clients/mdl_clients');

        // Get the client ID
        $client_id = $this->security->xss_clean($this->input->post('client_id'));
        $client = $this->mdl_clients->where('ip_clients.client_id', $client_id)->get()->row();

        if (!empty($client)) {
            $expense_id = $this->security->xss_clean($this->input->post('expense_id'));

            $db_array = [
                'client_id' => $client_id,
            ];
            $this->db->where('expense_id', $expense_id);
            $this->db->update('ip_invoices', $db_array);

            $response = [
                'success' => 1,
                'expense_id' => $this->security->xss_clean($expense_id),
            ];
        } else {
            $this->load->helper('json_error');
            $response = [
                'success' => 0,
                'validation_errors' => json_errors(),
            ];
        }

        echo json_encode($response);
    }

    public function modal_copy_invoice()
    {
        $this->load->module('layout');

        $this->load->model('invoices/mdl_invoices');
        $this->load->model('expense_groups/mdl_expense_groups');
        $this->load->model('tax_rates/mdl_tax_rates');

        $data = [
            'expense_groups' => $this->mdl_expense_groups->get()->result(),
            'tax_rates' => $this->mdl_tax_rates->get()->result(),
            'expense_id' => $this->security->xss_clean($this->input->post('expense_id')),
            'invoice' => $this->mdl_invoices->where('ip_invoices.expense_id', $this->security->xss_clean($this->input->post('expense_id')))
                ->get()
                ->row(),
        ];

        $this->layout->load_view('invoices/modal_copy_invoice', $data);
    }

    public function copy_invoice()
    {
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('invoices/mdl_items');
        $this->load->model('invoices/mdl_expense_tax_rates');

        if ($this->mdl_invoices->run_validation()) {
            $target_id = $this->mdl_invoices->save();
            $source_id = $this->security->xss_clean($this->input->post('expense_id'));

            $this->mdl_invoices->copy_invoice($source_id, $target_id);

            $response = [
                'success' => 1,
                'expense_id' => $target_id,
            ];
        } else {
            $this->load->helper('json_error');
            $response = [
                'success' => 0,
                'validation_errors' => json_errors(),
            ];
        }

        echo json_encode($response);
    }

    public function modal_create_credit()
    {
        $this->load->module('layout');

        $this->load->model('invoices/mdl_invoices');
        $this->load->model('expense_groups/mdl_expense_groups');
        $this->load->model('tax_rates/mdl_tax_rates');

        $data = [
            'expense_groups' => $this->mdl_expense_groups->get()->result(),
            'tax_rates' => $this->mdl_tax_rates->get()->result(),
            'expense_id' => $this->security->xss_clean($this->input->post('expense_id')),
            'invoice' => $this->mdl_invoices->where('ip_invoices.expense_id', $this->security->xss_clean($this->input->post('expense_id')))
                ->get()
                ->row(),
        ];

        $this->layout->load_view('invoices/modal_create_credit', $data);
    }

    public function create_credit()
    {
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('invoices/mdl_items');
        $this->load->model('invoices/mdl_expense_tax_rates');

        if ($this->mdl_invoices->run_validation()) {
            $target_id = $this->mdl_invoices->save();
            $source_id = $this->security->xss_clean($this->input->post('expense_id'));

            $this->mdl_invoices->copy_credit_invoice($source_id, $target_id);

            // Set source invoice to read-only
            if ($this->config->item('disable_read_only') == false) {
                $this->mdl_invoices->where('expense_id', $source_id);
                $this->mdl_invoices->update('ip_invoices', ['is_read_only' => '1']);
            }

            // Set target invoice to credit invoice
            $this->mdl_invoices->where('expense_id', $target_id);
            $this->mdl_invoices->update('ip_expenses', ['creditexpense_parent_id' => $source_id]);

            $this->mdl_invoices->where('expense_id', $target_id);
            $this->mdl_invoices->update('ip_expense_amounts', ['expense_sign' => '-1']);

            $response = [
                'success' => 1,
                'expense_id' => $target_id,
            ];
        } else {
            $this->load->helper('json_error');
            $response = [
                'success' => 0,
                'validation_errors' => json_errors(),
            ];
        }

        echo json_encode($response);
    }

    /**
     * @param $expense_id
     */
    public function delete_item($expense_id)
    {
        $success = 0;
        $item_id = $this->security->xss_clean($this->input->post('item_id'));
        $this->load->model('mdl_invoices');

        // Only continue if the invoice exists or no item id was provided
        if ($this->mdl_invoices->get_by_id($expense_id) || empty($item_id)) {

            // Delete invoice item
            $this->load->model('mdl_items');
            $item = $this->mdl_items->delete($item_id);

            // Check if deletion was successful
            if ($item) {

                $success = 1;

                // Mark task as complete from invoiced
                if (isset($item->item_task_id) && $item->item_task_id) {
                    $this->load->model('tasks/mdl_tasks');
                    $this->mdl_tasks->update_status(3, $item->item_task_id);
                }
            }

        }

        // Return the response
        echo json_encode([
            'success' => $success
        ]);
    }

}
