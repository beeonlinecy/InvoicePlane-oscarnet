<?php

if ( ! defined('BASEPATH')) {
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
class Mdl_Companies extends Response_Model
{
    public $table = 'ip_companies';

    public $primary_key = 'ip_companies.company_id';

    public $date_created_field = 'company_date_created';

    public $date_modified_field = 'company_date_modified';

    public function default_select()
    {
        $this->db->select(
            'SQL_CALC_FOUND_ROWS ' . $this->table . '.*, ' .
            $this->table . '.company_name',
            false
        );
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_companies.company_name');
    }

    public function validation_rules()
    {
        return [
            'company_title' => [
                'field' => 'company_title',
                'label' => trans('company_title'),
            ],
            'company_name' => [
                'field' => 'company_name',
                'label' => trans('company_name'),
                'rules' => 'required',
            ],
            'company_surname' => [
                'field' => 'company_surname',
                'label' => trans('company_surname'),
            ],
            'company_active' => [
                'field' => 'company_active',
            ],
            'company_language' => [
                'field' => 'company_language',
                'label' => trans('language'),
                'rules' => 'trim',
            ],
            'company_address_1' => [
                'field' => 'company_address_1',
            ],
            'company_address_2' => [
                'field' => 'company_address_2',
            ],
            'company_city' => [
                'field' => 'company_city',
            ],
            'company_state' => [
                'field' => 'company_state',
            ],
            'company_zip' => [
                'field' => 'company_zip',
            ],
            'company_country' => [
                'field' => 'company_country',
                'rules' => 'trim',
            ],
            'company_phone' => [
                'field' => 'company_phone',
            ],
            'company_fax' => [
                'field' => 'company_fax',
            ],
            'company_mobile' => [
                'field' => 'company_mobile',
            ],
            'company_email' => [
                'field' => 'company_email',
            ],
            'company_web' => [
                'field' => 'company_web',
            ],
            'company_vat_id' => [
                'field' => 'company_vat_id',
            ],
            'company_tax_code' => [
                'field' => 'company_tax_code',
            ],
            // SUMEX
            'company_birthdate' => [
                'field' => 'company_birthdate',
                'rules' => 'callback_convert_date',
            ],
            'company_gender' => [
                'field' => 'company_gender',
            ],
            'company_avs' => [
                'field' => 'company_avs',
                'label' => trans('sumex_ssn'),
                'rules' => 'callback_fix_avs',
            ],
            'company_insurednumber' => [
                'field' => 'company_insurednumber',
                'label' => trans('sumex_insurednumber'),
            ],
            'company_veka' => [
                'field' => 'company_veka',
                'label' => trans('sumex_veka'),
            ],
        ];
    }

    /**
     * @param int $amount
     *
     * @return mixed
     */
    public function get_latest($amount = 10)
    {
        return $this->mdl_companies
            //->where('client_active', 1)
            ->order_by('company_id', 'DESC')
            ->limit($amount)
            ->get()
            ->result();
    }

    /**
     * @return string
     */
    public function fix_avs($input)
    {
        if ($input != '') {
            if (preg_match('/(\d{3})\.(\d{4})\.(\d{4})\.(\d{2})/', $input, $matches)) {
                return $matches[1] . $matches[2] . $matches[3] . $matches[4];
            }
            if (preg_match('/^\d{13}$/', $input)) {
                return $input;
            }
        }

        return '';
    }

    public function convert_date($input)
    {
        $this->load->helper('date_helper');

        if ($input == '') {
            return '';
        }

        return date_to_mysql($input);
    }

    public function db_array()
    {
        $db_array = parent::db_array();

        if ( ! isset($db_array['company_active'])) {
            $db_array['company_active'] = 0;
        }

        return $db_array;
    }

    /**
     * @param int $id
     */
    public function delete($id)
    {
        parent::delete($id);

        $this->load->helper('orphan');
        delete_orphans();
    }

    /**
     * Returns company_id of existing company.
     *
     * @param $company_name
     *
     * @return int|null
     */
    public function company_lookup($company_name)
    {
        $company = $this->mdl_companies->where('company_name', $company_name)->get();

        if ($company->num_rows()) {
            $company_id = $company->row()->company_id;
        } else {
            $db_array = [
                'company_name' => $company_name,
            ];

            $company_id = parent::save(null, $db_array);
        }

        return $company_id;
    }

    public function with_total()
    {
        $this->filter_select('IFnull((SELECT SUM(invoice_total) FROM ip_invoice_amounts WHERE invoice_id IN (SELECT invoice_id FROM ip_companies WHERE ip_companies.company_id = ip_companies.company_id)), 0) AS client_invoice_total', false);

        return $this;
    }

    public function with_total_paid()
    {
        $this->filter_select('IFnull((SELECT SUM(invoice_paid) FROM ip_invoice_amounts WHERE invoice_id IN (SELECT invoice_id FROM ip_companies WHERE ip_companies.company_id = ip_companies.company_id)), 0) AS client_invoice_paid', false);

        return $this;
    }

    public function with_total_balance()
    {
        $this->filter_select('IFnull((SELECT SUM(expense_balance) FROM ip_expense_amounts WHERE expense_id IN (SELECT expense_id FROM ip_companies WHERE ip_companies.company_id = ip_companies.company_id)), 0) AS company_expense_balance', false);

        return $this;
    }
 
    /*
    public function is_inactive()
    {
        $this->filter_where('client_active', 0);

        return $this;
    }
    */
    /**
     * @param $user_id
     *
     * @return $this
     */
    public function get_not_assigned_to_user($user_id)
    {
        $this->load->model('user_companies/mdl_user_companies');
        $companies = $this->mdl_user_companies->select('ip_user_companies.company_id')
            ->assigned_to($user_id)->get()->result();

        $assigned_companies = [];
        foreach ($companies as $client) {
            $assigned_companies[] = $client->company_id;
        }

        if (count($assigned_companies) > 0) {
            $this->where_not_in('ip_companies.company_id', $assigned_companies);
        }

        //$this->is_active();

        return $this->get()->result();
    }

    public function is_active()
    {
        //$this->filter_where('client_active', 1);

        return $this;
    }
}
