<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class My_join_meeting_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function init()
    {
        $sql = "SELECT COUNT(0) AS join_meeting_count 
                FROM 
                    s_schedule s, 
                    s_person p, 
                    s_system m
                WHERE 
                    s.s_id = p.s_id AND s.s_id = m.s_id 
                        AND s.status = 2 
                        AND m.system_type = 3 
                        AND p.user_id = ? 
                        AND p.occupancy_start_time > ?";
        $query = $this->db->query($sql, array($_SESSION['user_id'], time()));
        $row = $query->row();
        $data['my_join_meeting_count'] = $row->join_meeting_count;
        return $data;
    }

    /**
     * 获取所有可参加会议数量
     *
     * @return    int    可参加会议数量合计
     */
    public function all_can_join_meeting_count()
    {
        $sql = "SELECT DISTINCT
                    (s.s_id),
                    s.start_time,
                    s.end_time,
                    s.is_open_court,
                    JSON_EXTRACT(m.content, '$[0].a') AS meeting_name,
                    r.r_id
                FROM
                    s_schedule s,
                    s_person p,
                    s_system m,
                    s_room r,
                    s_flow f
                WHERE
                    s.s_id = m.s_id AND s.s_id = r.s_id
                        AND s.s_id = f.s_id
                        AND s.status = 2
                        AND f.is_meeting_approval = 1
                        AND f.flow_status = 1
                        AND m.system_type = 3
                        AND ((s.is_open_court = 1
                            AND s.select_room_court_fjm = ?)
                            OR (p.s_id = s.s_id
                            AND p.user_id = ?))
                        AND s.start_time > ?";
        $query = $this->db->query($sql, array($_SESSION['court_fjm'], $_SESSION['user_id'], time()));
        $data['all_can_join_meeting_count'] = $query->num_rows();
        return $data;
    }

    /**
     * 获取本人要参加的会议列表
     * 
     * @param    int $curr_page    当前页
     * @param    int $per_page    每页显示数
     * @return    array    本人要参加会议数据
     */
    public function get_my_join_meeting_list($curr_page, $per_page)
    {
        $offset = ($curr_page - 1) * $per_page;
        if (!is_int($per_page))
            $per_page = intval($per_page);
        $this->load->library('cgetroomdata');
        $this->load->library('cgetname');
        $join_list = array();
        $sql = "SELECT DISTINCT
                    (s.s_id),
                    s.start_time,
                    s.end_time,
                    s.is_open_court,
                    JSON_EXTRACT(m.content, '$[0].a') AS meeting_name,
                    r.r_id
                FROM
                    s_schedule s,
                    s_person p,
                    s_system m,
                    s_room r,
                    s_flow f
                WHERE
                    s.s_id = m.s_id AND s.s_id = r.s_id
                        AND s.s_id = f.s_id
                        AND s.status = 2
                        AND f.is_meeting_approval = 1
                        AND f.flow_status = 1
                        AND m.system_type = 3
                        AND ((s.is_open_court = 1
                            AND s.select_room_court_fjm = ?)
                            OR (p.s_id = s.s_id
                            AND p.user_id = ?))
                        AND s.start_time > ?
                ORDER BY s.start_time LIMIT ?, ?";
        $query = $this->db->query($sql, array($_SESSION['court_fjm'], $_SESSION['user_id'], time(), $offset, $per_page));
        foreach ($query->result() as $row)
        {
            $room = $this->cgetroomdata->get_room_name_and_type($row->r_id);
            $room_court = $this->cgetname->get_courtname($room['ssdw']);
            $join_list[] = array(
                'meeting_name' => str_replace('"', '', $row->meeting_name),
                'meeting_time' => date('Y年m月d日H时i分', $row->start_time) . ' 至 ' . date('Y年m月d日H时i分', $row->end_time),
                'address' => $room_court . $room['name'] . '（' . $room['address'] . '）',
                'status' => $row->start_time - time(),
                'control' => '<button class="button" onClick="Detail(\'' . $row->s_id . '\')">详情</button>',
                'is_open' => $row->is_open_court
            );
        }
        return $join_list;
    }

    /*private function GetMeetingPerson($s_id)
    {
        $name_str = '';
        $sql = "SELECT user_name FROM s_person WHERE s_id = ? AND valid = ?";
        $query = $this->db->query($sql, array($s_id, 1));
        foreach ($query->result() as $row)
        {
            $name_str .= $row->user_name . '，';
        }
        if ($name_str != '')
        {
            $name_str = substr($name_str, 0, -3);
        }
        return $name_str;
    }

    public function del_my_apply($s_id)
    {
        $sql = "DELETE 
                    s_schedule, s_person, s_room, s_system, s_title, s_flow 
                FROM s_schedule 
                LEFT JOIN s_person ON s_person.s_id = s_schedule.s_id 
                LEFT JOIN s_room ON s_room.s_id = s_schedule.s_id 
                LEFT JOIN s_system ON s_system.s_id = s_schedule.s_id 
                LEFT JOIN s_title ON s_title.s_id = s_schedule.s_id
                LEFT JOIN s_flow ON s_flow.s_id = s_schedule.s_id 
                LEFT JOIN s_files ON s_files.s_id = s_schedule.s_id
                WHERE s_schedule.s_id = ?";
        if ($this->db->query($sql, array($s_id)))
        {
            $result = 1;
        }
        else
        {
            $result = 0;
        }
        return $result;
    }

    public function get_meeting_remind($s_id)
    {
        $this->load->library('cgetroomdata');
        $this->load->library('cgetname');
        $data['tea'] = array();
        $data['technology'] = array();
        $data['person'] = array();
        $data['remind_history'] = array();
        $sql = "SELECT json_extract(s.content, '$[0].d') AS ensure, r.r_id FROM s_system s, s_room r WHERE s.s_id = ? AND s.s_id = r.s_id";
        $query = $this->db->query($sql, array($s_id));
        $row = $query->row();
        if (isset($row))
        {
            $ensure = $row->ensure;
            if (!empty($ensure))
            {
                $ensure = str_replace('"', '', $ensure);
                $ensure_arr = explode(',', $ensure);
                foreach ($ensure_arr as $e)
                {
                    if ($e == '茶水')
                    {
                        $tea = $this->cgetroomdata->get_ensure_person($row->r_id, 2);
                        if (!empty($tea))
                        {
                            foreach ($tea as $t)
                            {
                                $tea_info = $this->cgetname->get_user_email_name_and_mobile($t);
                                $data['tea'][] = array(
                                    'email' => $tea_info['email'],
                                    'name' => $tea_info['name'],
                                    'mobile' => $tea_info['mobile']
                                );
                            }
                        }
                    }
                    if ($e == '技术支持')
                    {
                        $technology = $this->cgetroomdata->get_ensure_person($row->r_id, 1);
                        if (!empty($technology))
                        {
                            foreach ($technology as $tech)
                            {
                                $technology_info = $this->cgetname->get_user_email_name_and_mobile($tech);
                                $data['technology'][] = array(
                                    'email' => $technology_info['email'],
                                    'name' => $technology_info['name'],
                                    'mobile' => $technology_info['mobile']
                                );
                            }
                        }
                    }
                }
            }
        }
        $sql = "SELECT user_id, user_name FROM s_person WHERE s_id = ?";
        $query = $this->db->query($sql, array($s_id));
        foreach ($query->result() as $row)
        {
            $data['person'][] = array(
                'email' => $row->user_id,
                'name' => $row->user_name,
                'mobile' => $this->cgetname->get_user_mobile($row->user_id)
            );
        }
        $sql = "SELECT send_timestamp, send_time, sms, email, message, content FROM s_remind_history WHERE s_id = ? GROUP BY send_timestamp ORDER BY send_timestamp ASC";
        $query = $this->db->query($sql, array($s_id));
        foreach ($query->result() as $row)
        {
            $remind_type = '';
            if ($row->email == 1)
                $remind_type .= '邮件、';
            if ($row->message == 1)
                $remind_type .= '短消息、';
            if ($row->sms == 1)
                $remind_type .= '短信、';
            $remind_type = substr($remind_type, 0, -3);
            $data['remind_history'][] = "提醒时间：{$row->send_time}；<br />&nbsp;&nbsp;&nbsp;&nbsp;提醒方式：[{$remind_type}]；<br />&nbsp;&nbsp;&nbsp;&nbsp;提醒内容：{$row->content}";
        }
        return $data;
    }

    public function send_remind($s_id, $email, $nw_id_arr, $name, $mobile, $send_email, $send_message, $send_sms, $send_time, $send_content)
    {
        $this->load->library('cremind');
        $send_email_result = TRUE;
        $send_message_result = TRUE;
        $send_sms_result = 1;
        $this->db->trans_begin();
        if ($send_email == 1)
        {
            if (sizeof($nw_id_arr) > 0)
            {
                $send_email_result = $this->cremind->SendEmail($nw_id_arr, '会议提醒', $send_content, $send_time);
            }
        }
        if ($send_message == 1)
        {
            if (sizeof($nw_id_arr) > 0)
            {
                $sql = "SELECT id FROM s_schedule WHERE s_id = ?";
                $query = $this->db->query($sql, array($s_id));
                $row = $query->row();
                $send_message_result = $this->cremind->SendMessage($nw_id_arr, $send_content, $row->id, $send_time);
            }
        }
        if ($send_sms == 1)
        {
            if (sizeof($mobile) > 0)
            {
                $send_sms_result = $this->cremind->SendSms($mobile, $send_content, $send_time);
            }
        }
        $sql = "INSERT INTO s_remind_history (s_id, send_timestamp, send_time, sms, email, message, send_user_email, send_user_nw_id, send_user_name, mobile, content) VALUES ";
        foreach ($email as $user)
        {
            $sql .= "('{$s_id}', " . strtotime($send_time) . ", '{$send_time}', {$send_sms}, {$send_email}, {$send_message}, '{$user}', '{$nw_id_arr[$user]}', '{$name[$user]}', '{$mobile[$user]}', '{$send_content}'),";
        }
        if (sizeof($email) > 0)
        {
            $sql = substr($sql, 0, -1);
            $this->db->query($sql);
            if ($this->db->trans_status() === FALSE || $send_email_result === FALSE || $send_message_result === FALSE || $send_sms_result == 0)
            {
                $this->db->trans_rollback();
                return 0;
            }
            else
            {
                $this->db->trans_commit();
                return 1;
            }
        }
        else
        {
            return -1;
        }
    }*/

}