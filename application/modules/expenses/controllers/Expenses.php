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
class Expenses extends Admin_Controller
{

    /**
     * expenses constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_expenses');
    }

    public function index()
    {
        // Display all expenses by default
        redirect('expenses/status/all');
    }

    /**
     * @param string $status
     * @param int $page
     */
    public function status($status = 'all', $page = 0)
    {
        // Determine which group of expenses to load
        switch ($status) {
            case 'draft':
                $this->mdl_expenses->is_draft();
                break;
            case 'sent':
                $this->mdl_expenses->is_sent();
                break;
            case 'viewed':
                $this->mdl_expenses->is_viewed();
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
                'expenses' => $expenses,
                'status' => $status,
                'filter_display' => true,
                'filter_placeholder' => trans('filter_expenses'),
                'filter_method' => 'filter_expenses',
                'expense_statuses' => $this->mdl_expenses->statuses(),
            ]
        );

        $this->layout->buffer('content', 'expenses/index');
        $this->layout->render();
    }

    public function archive()
    {
        $expense_array = [];

        if (isset($_POST['expense_number'])) {
            $expenseNumber = $_POST['expense_number'];
            $expense_array = glob(UPLOADS_ARCHIVE_FOLDER . '*' . '_' . $expenseNumber . '.pdf');
            $this->layout->set(
                [
                    'expenses_archive' => $expense_array,
                ]);
            $this->layout->buffer('content', 'expenses/archive');
            $this->layout->render();

        } else {
            foreach (glob(UPLOADS_ARCHIVE_FOLDER . '*.pdf') as $file) {
                array_push($expense_array, $file);
            }

            rsort($expense_array);
            $this->layout->set(
                [
                    'expenses_archive' => $expense_array,
                ]);
            $this->layout->buffer('content', 'expenses/archive');
            $this->layout->render();
        }
    }

public function download($expense)
{
    $safeBaseDir = realpath(UPLOADS_ARCHIVE_FOLDER);

    $fileName = basename($expense); // Strip directory traversal sequences
    $filePath = realpath($safeBaseDir . DIRECTORY_SEPARATOR . $fileName);

    if ($filePath === false || strpos($filePath, $safeBaseDir) !== 0) {
        log_message('error', "Invalid file access attempt: $fileName");
        show_404();
        return;
    }

    if (!file_exists($filePath)) {
        log_message('error', "While downloading: File not found: $filePath");
        show_404();
        return;
    }

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
}

    /**
     * @param $expense_id
     */
    public function view($expense_id)
    {
        $this->load->model(
            [
                'mdl_items',
                'tax_rates/mdl_tax_rates',
                'payment_methods/mdl_payment_methods',
                'mdl_expense_tax_rates',
                'custom_fields/mdl_custom_fields',
            ]
        );

        $this->load->helper("custom_values");
        $this->load->helper("company");
        $this->load->model('units/mdl_units');
        $this->load->module('payments');

        $this->load->model('custom_values/mdl_custom_values');
        $this->load->model('custom_fields/mdl_expense_custom');

        $this->db->reset_query();

        /*$expense_custom = $this->mdl_expense_custom->where('expense_id', $expense_id)->get();

        if ($expense_custom->num_rows()) {
            $expense_custom = $expense_custom->row();

            unset($expense_custom->expense_id, $expense_custom->expense_custom_id);

            foreach ($expense_custom as $key => $val) {
                $this->mdl_expenses->set_form_value('custom[' . $key . ']', $val);
            }
        }*/

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
                    // TODO: Hackish, may need a better optimization
                    $this->mdl_expenses->set_form_value(
                        'custom[' . $cfield->custom_field_id . ']',
                        $fvalue->expense_custom_fieldvalue
                    );
                    break;
                }
            }
        }

        // Check whether there are payment custom fields
        $payment_cf = $this->mdl_custom_fields->by_table('ip_payment_custom')->get();
        $payment_cf_exist = ($payment_cf->num_rows() > 0) ? "yes" : "no";

        $this->layout->set(
            [
                'expense' => $expense,
                'items' => $this->mdl_items->where('expense_id', $expense_id)->get()->result(),
                'expense_id' => $expense_id,
                'tax_rates' => $this->mdl_tax_rates->get()->result(),
                'expense_tax_rates' => $this->mdl_expense_tax_rates->where('expense_id', $expense_id)->get()->result(),
                'units' => $this->mdl_units->get()->result(),
                'payment_methods' => $this->mdl_payment_methods->get()->result(),
                'custom_fields' => $custom_fields,
                'custom_values' => $custom_values,
                'custom_js_vars' => [
                    'currency_symbol' => get_setting('currency_symbol'),
                    'currency_symbol_placement' => get_setting('currency_symbol_placement'),
                    'decimal_point' => get_setting('decimal_point'),
                ],
                'expense_statuses' => $this->mdl_expenses->statuses(),
                'payment_cf_exist' => $payment_cf_exist,
            ]
        );

        //if ($expense->sumex_id != null) {
        //    $this->layout->buffer(
        //        [
        //            ['modal_delete_expense', 'expenses/modal_delete_expense'],
        //            ['modal_add_expense_tax', 'expenses/modal_add_expense_tax'],
        //            ['modal_add_payment', 'payments/modal_add_payment'],
        //            ['content', 'expenses/view_sumex'],
        //        ]
        //    );
        //} else {
            $this->layout->buffer(
                [
                    ['modal_delete_expense', 'expenses/modal_delete_expense'],
                    ['modal_add_expense_tax', 'expenses/modal_add_expense_tax'],
                    ['modal_add_payment', 'payments/modal_add_payment'],
                    ['content', 'expenses/view'],
                ]
            );
        //}

        $this->layout->render();
    }

    /**
     * @param $expense_id
     */
    public function delete($expense_id)
    {
        // Get the status of the expense
        $expense = $this->mdl_expenses->get_by_id($expense_id);
        $expense_status = $expense->expense_status_id;

        if ($expense_status == 1 || $this->config->item('enable_expense_deletion') === true) {
            // If expense refers to tasks, mark those tasks back to 'Complete'
            $this->load->model('tasks/mdl_tasks');
            $tasks = $this->mdl_tasks->update_on_expense_delete($expense_id);

            // Delete the expense
            $this->mdl_expenses->delete($expense_id);
        } else {
            // Add alert that expenses can't be deleted
            $this->session->set_flashdata('alert_error', trans('expense_deletion_forbidden'));
        }

        // Redirect to expense index
        redirect('expenses/index');
    }

    /**
     * @param $expense_id
     * @param bool $stream
     * @param null $expense_template
     */
    public function generate_pdf($expense_id, $stream = true, $expense_template = null)
    {
        $this->load->helper('pdf');

        if (get_setting('mark_expenses_sent_pdf') == 1) {
            $this->mdl_expenses->generate_expense_number_if_applicable($expense_id);
            $this->mdl_expenses->mark_sent($expense_id);
        }

        generate_expense_pdf($expense_id, $stream, $expense_template, null);
    }

    /**
     * @param $expense_id
     */
    public function generate_zugferd_xml($expense_id)
    {
        $this->load->model('expenses/mdl_items');
        $this->load->library('ZugferdXml', [
            'expense' => $this->mdl_expenses->get_by_id($expense_id),
            'items' => $this->mdl_items->where('expense_id', $expense_id)->get()->result(),
        ]);

        $this->output->set_content_type('text/xml');
        $this->output->set_output($this->zugferdxml->xml());
    }

    public function generate_sumex_pdf($expense_id)
    {
        $this->load->helper('pdf');

        generate_expense_sumex($expense_id);
    }

    public function generate_sumex_copy($expense_id)
    {


        $this->load->model('expenses/mdl_items');
        $this->load->library('Sumex', [
            'expense' => $this->mdl_expenses->get_by_id($expense_id),
            'items' => $this->mdl_items->where('expense_id', $expense_id)->get()->result(),
            'options' => [
                'copy' => "1",
                'storno' => "0",
            ],
        ]);

        $this->output->set_content_type('application/pdf');
        $this->output->set_output($this->sumex->pdf());
    }

    /**
     * @param $expense_id
     * @param $expense_tax_rate_id
     */
    public function delete_expense_tax($expense_id, $expense_tax_rate_id)
    {
        $this->load->model('mdl_expense_tax_rates');
        $this->mdl_expense_tax_rates->delete($expense_tax_rate_id);

        $this->load->model('mdl_expense_amounts');
        $this->mdl_expense_amounts->calculate($expense_id);

        redirect('expenses/view/' . $expense_id);
    }

    public function recalculate_all_expenses()
    {
        $this->db->select('expense_id');
        $expense_ids = $this->db->get('ip_expenses')->result();

        $this->load->model('mdl_expense_amounts');

        foreach ($expense_ids as $expense_id) {
            $this->mdl_expense_amounts->calculate($expense_id->expense_id);
        }
    }

}
