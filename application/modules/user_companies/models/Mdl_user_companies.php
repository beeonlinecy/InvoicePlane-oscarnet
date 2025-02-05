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
class Mdl_User_Companies extends MY_Model
{
    public $table = 'ip_user_companies';
    public $primary_key = 'ip_user_companies.user_company_id';

    public function default_select()
    {
        $this->db->select('ip_user_companies.*, ip_users.user_name, ip_companies.company_name, ip_companies.company_surname');
    }

    public function default_join()
    {
        $this->db->join('ip_users', 'ip_users.user_id = ip_user_companies.user_id');
        $this->db->join('ip_companies', 'ip_companies.company_id = ip_user_companies.company_id');
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_companies.company_name', 'ACS');
    }

    /**
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'user_id' => array(
                'field' => 'user_id',
                'label' => trans('user'),
                'rules' => 'required'
            ),
            'company_id' => array(
                'field' => 'company_id',
                'label' => trans('company'),
                'rules' => 'required'
            ),
        );
    }

    /**
     * @param $user_id
     * @return $this
     */
    public function assigned_to($user_id)
    {
        $this->filter_where('ip_user_companies.user_id', $user_id);
        return $this;
    }

    /**
    *
    * @param array $users_id
    */
    public function set_all_companies_user($users_id)
    {
        $this->load->model('companies/mdl_companies');

        for ($x = 0; $x < count($users_id); $x++) {
            $companies = $this->mdl_companies->get_not_assigned_to_user($users_id[$x]);

            for ($i = 0; $i < count($companies); $i++) {
                $user_company = array(
                    'user_id' => $users_id[$x],
                    'company_id' => $companies[$i]->company_id
                );

                $this->db->insert('ip_user_companies', $user_company);
            }
        }
    }

    public function get_users_all_companies()
    {
        $this->load->model('users/mdl_users');
        $users = $this->mdl_users->where('user_all_clients', 1)->get()->result();

        $new_users = array();

        for ($i = 0; $i < count($users); $i++) {
            array_push($new_users, $users[$i]->user_id);
        }

        $this->set_all_companies_user($new_users);
    }
}
