<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Occupy_apply_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    //获得向我发起的场所占用申请列表
    public function get_occupy_apply_list()
    {
        $list = array();
        $this->load->library('CGetRoomData');
        $query = $this->db->select('o.id, o.apply_user_name, o.room_id, o.occupy_reason, s.apply_name, s.start_time, s.end_time, t.title')
            ->from('s_occupy_apply o')
            ->join('s_schedule s', 'o.now_schedule_id = s.id', 'left')
            ->join('s_title t', 'o.now_schedule_id = s.id AND t.s_id = s.s_id', 'left')
            ->join('s_system m', 'm.s_id = s.s_id', 'left')
            ->where('m.system_type', 3)
            ->where('o.occupy_user_email', $_SESSION['user_id'])
            ->where('o.status', 0)
            ->where('s.start_time >', time())
            ->get();
        foreach ($query->result() as $row)
        {
            $room_data = $this->cgetroomdata->get_room_name_and_type($row->room_id);
            $list[] = array(
                'id' => $row->id,
                'apply_user' => $row->apply_user_name,
                'subject' => $row->title,
                'now_use_time' => date('Y年m月d日H时i分', $row->start_time) . ' 至 ' . date('Y年m月d日H时i分', $row->end_time),
                'room_name' => $room_data['name'] . '（' . $room_data['address'] . '）',
                'occupy_reason' => $row->occupy_reason
            );
        }
        return $list;
    }
}