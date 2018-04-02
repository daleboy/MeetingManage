<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Meeting_detail_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function getScheduleIdById($id)
    {
        $query = $this->db->select('s_id')
            ->from('s_schedule')
            ->where('id', $id)
            ->get();
        $row = $query->row_array();
        return $row['s_id'];
    }
}
