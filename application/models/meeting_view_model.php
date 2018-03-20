<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Meeting_view_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    /**
     * 按年份、月份分类生成树结构
     *
     * @param int $p_id 年份
     * @return 该年所有月份合计数
     */
    public function show_tree($p_id)
    {
        //年份为0，从数据库中读有记录的年份
        if ($p_id == 0)
        {
            $sql = "SELECT min(s.start_time) AS start_year, max(s.end_time) AS end_year FROM s_schedule s, s_system t WHERE s.s_id = t.s_id AND s.court_fjm = ? AND t.system_type = 3";
            $query = $this->db->query($sql, array($_SESSION['court_fjm']));
            $row = $query->row();
            $start_year = intval(date('Y', $row->start_year));
            $end_year = intval(date('Y', $row->end_year));
            if ($start_year > 1970)
            {
                while ($start_year <= $end_year)
                {
                    $tree[] = array(
                        'id' => $start_year,
                        'pId' => 0,
                        'name' => strval($start_year) . '年会议列表',
                        'isParent' => TRUE
                    );
                    $start_year += 1;
                }
            }
            else
            {
                $tree[] = array(
                    'id' => 0,
                    'pId' => 0,
                    'name' => '无会议数据',
                    'isParent' => FALSE
                );
            }
        }
        else
        {
            if (strlen(strval($p_id)) == 4)
            {
                for ($i = 1; $i <= 12; $i++)
                {
                    $meeting_count = $this->CountMeeting($p_id, $i);
                    $tree[] = array(
                        'id' => strval($p_id) . '-' . $i,
                        'pId' => $p_id,
                        'name' => $i . "月（{$meeting_count}）",
                        'isParent' => FALSE,
                        'meetingNum' => $meeting_count
                    );
                }
            }
        }
        return $tree;
    }

    /**
     * 统计会议总数
     *
     * @param int $year 年份
     * @param int $month 月份
     * @return 会议总数
     */
    private function CountMeeting($year, $month)
    {
        $start_time = date('Y-m-d', strtotime($year . '-' . $month . '-01'));
        $end_time = date('Y-m-d', strtotime("$start_time +1 month -1 day"));
        $sql = "SELECT COUNT(s.id) AS meeting_count FROM s_schedule s, s_system t WHERE s.start_time >= ? AND s.end_time <= ? AND t.system_type = 3 AND s.s_id = t.s_id AND s.court_fjm = ?";
        $query = $this->db->query($sql, array(strtotime($start_time . ' 00:00:00'), strtotime($end_time . ' 23:59:59'), $_SESSION['court_fjm']));
        $row = $query->row();
        return $row->meeting_count;
    }

    /**
     * 获取会议列表
     *
     * @param string $year_month 年-月
     * @param int $curr_page 当前页
     * @param int $per_page 每页显示数
     * @return 会议列表
     */
    public function get_meeting_list($year_month, $curr_page, $per_page)
    {
        $offset = ($curr_page - 1) * $per_page;
        if (!is_int($per_page))
            $per_page = intval($per_page);
        $this->load->library('cgetroomdata');
        $this->load->library('cgetname');
        $this->load->library('ccheckaudit');
        $meeting_list = array();
        $start_time = date('Y-m-d', strtotime($year_month . '-01'));
        $end_time = date('Y-m-d', strtotime("$start_time +1 month -1 day"));
        
        $sql = "SELECT s.s_id, s.start_time, s.end_time, s.status, c.content, r.r_id FROM s_schedule s, s_system c, s_room r WHERE s.start_time >= ? AND s.end_time <= ? AND s.s_id = c.s_id AND s.s_id = r.s_id AND c.system_type = ? AND s.court_fjm = ? ORDER BY s.start_time DESC LIMIT ?, ?";
        $query = $this->db->query($sql, array(strtotime($start_time . ' 00:00:00'), strtotime($end_time . ' 23:59:59'), 3, $_SESSION['court_fjm'], $offset, $per_page));
        foreach ($query->result() as $row)
        {
            $content = json_decode($row->content);
            $room = $this->cgetroomdata->get_room_name_and_type($row->r_id);
            $room_court = $this->cgetname->get_courtname($room['ssdw']);
            
            if ($row->status == 0)
            {
                $status = '<span class="text-yellow">排期申请未提交</span>';
            }
            else
            {
                $approval = $this->ccheckaudit->CheckMeetingAndScheduleAudit($row->s_id);
                //$status = $approval['schedule']['status_chs'] . '、' . $approval['meeting']['status_chs'];
                $status = $approval['schedule']['status_chs'];
            }
            if (time() > $row->end_time)
            {
                if ($row->status == 2) // && $approval['meeting']['status'] == 1
                    $status = '<span class="text-gray">会议已结束</span>';
                else
                    $status = '<span class="text-gray">会议已结束</span>（' . $status . '）';
                    
            }

            $control = '<button class="button" onClick="View(\'' . $row->s_id . '\')">查看详情</button>';
            $meeting_list[] = array(
                'meeting_name' => $content[0]->a,
                'meeting_time' => date('Y年m月d日H时i分', $row->start_time) . ' 至 ' . date('Y年m月d日H时i分', $row->end_time),
                'address' => $room_court . $room['name'] . '（' . $room['address'] . '）',
                'status' => $status,
                'control' => $control
            );
        }
        return $meeting_list;
    }

}