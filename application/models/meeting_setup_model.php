<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Meeting_setup_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    /* 会议类型设置开始 */
    public function GetMeetingType()
    {
        $query = $this->db->select('type_id, type_name, fjm')
            ->where("del_flag = 0 AND (fjm = '{$_SESSION['court_fjm']}' OR fjm is null)")
            ->get('s_meeting_type');
        return $query->result_array();
    }

    public function AddMeetingType($type_name)
    {
        if ($this->db->insert('s_meeting_type', array('type_name' => $type_name, 'fjm' => $_SESSION['court_fjm'])))
            return 1;
        else
            return 0;
    }

    public function ModifyMeetingType($type_name, $type_id)
    {
        if ($this->db->where('type_id', $type_id)->update('s_meeting_type', array('type_name' => $type_name)))
            return 1;
        else
            return 0;
    }

    public function DelMeetingType($type_id)
    {
        if ($this->db->where('type_id', $type_id)->update('s_meeting_type', array('del_flag' => 1)))
            return 1;
        else
            return 0;
    }

    /* 技术保障类型设置开始 */
    public function GetTechnologyType()
    {
        $query = $this->db->select('type_id, type_name, fjm')
            ->where("del_flag = 0 AND (fjm = '{$_SESSION['court_fjm']}' OR fjm is null)")
            ->get('s_technology_type');
        return $query->result_array();
    }

    public function AddTechnologyType($type_name)
    {
        if ($this->db->insert('s_technology_type', array('type_name' => $type_name, 'fjm' => $_SESSION['court_fjm'])))
            return 1;
        else
            return 0;
    }

    public function ModifyTechnologyType($type_name, $type_id)
    {
        if ($this->db->where('type_id', $type_id)->update('s_technology_type', array('type_name' => $type_name)))
            return 1;
        else
            return 0;
    }

    public function DelTechnologyType($type_id)
    {
        if ($this->db->where('type_id', $type_id)->update('s_technology_type', array('del_flag' => 1)))
            return 1;
        else
            return 0;
    }
}
