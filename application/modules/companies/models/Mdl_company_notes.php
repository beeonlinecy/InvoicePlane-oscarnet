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
class Mdl_Company_Notes extends Response_Model
{
    public $table = 'ip_company_notes';
    public $primary_key = 'ip_company_notes.company_note_id';

    public function default_order_by()
    {
        $this->db->order_by('ip_company_notes.company_note_date DESC');
    }

    public function validation_rules()
    {
        return array(
            'company_id' => array(
                'field' => 'company_id',
                'label' => trans('company'),
                'rules' => 'required'
            ),
            'company_note' => array(
                'field' => 'company_note',
                'label' => trans('note'),
                'rules' => 'required'
            )
        );
    }

    public function db_array()
    {
        $db_array = parent::db_array();

        $db_array['company_note_date'] = date('Y-m-d');

        return $db_array;
    }

    /**
     * @param int $id
     */
    public function delete($id)
    {
        parent::delete($id);
        // For Ajax Check if deletion was successful
        return true;
    }

}
