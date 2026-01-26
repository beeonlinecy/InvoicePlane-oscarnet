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
class Mdl_expense_sumex extends Response_Model
{
    public $table = 'ip_expense_sumex';
    public $primary_key = 'ip_expense_sumex.sumex_id';

    public function default_select()
    {
        $this->db->select('ip_expense_sumex.*');
    }

    /**
     * @param null $id
     * @param null $db_array
     * @return void
     */
    public function save($id = null, $db_array = null)
    {
        $id = $this->where('sumex_expense', $id)->get()->row()->sumex_id;
        parent::save($id, $db_array);
    }

    /**
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'sumex_expense' => array(
                'field' => 'sumex_expense',
                'label' => trans('expense'),
                'rules' => 'required'
            ),
            'sumex_reason' => array(
                'field' => 'sumex_reason',
                'label' => trans('reason'),
                'rules' => 'required|greater_than_equal_to[0]|less_than_equal_to[5]'
            ),
            'sumex_diagnosis' => array(
                'field' => 'sumex_diagnosis',
                'label' => trans('diagnosis')
            ),
            'sumex_observations' => array(
                'field' => 'sumex_observations',
                'label' => trans('sumex_observations')
            ),
            'sumex_treatmentstart' => array(
                'field' => 'sumex_treatmentstart',
                'label' => trans('start'),
                'rules' => 'required'
            ),
            'sumex_treatmentend' => array(
                'field' => 'sumex_treatmentend',
                'label' => trans('end'),
                'rules' => 'required'
            ),
            'sumex_casedate' => array(
                'field' => 'sumex_casedate',
                'label' => trans('case_date'),
                'rules' => 'required'
            ),
            'sumex_casenumber' => array(
                'field' => 'sumex_casenumber',
                'label' => trans('case_number')
            )
        );
    }

}
