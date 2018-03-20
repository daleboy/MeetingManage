<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Meeting_report_model extends CI_Model {

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
            $sql = "SELECT min(publish_time) AS start_year, max(publish_time) AS end_year FROM s_document WHERE court_fjm = ? AND document_type = 2 AND (is_audit = 0 OR audit_status = 1) AND delete_status = 1";
            $query = $this->db->query($sql, array($_SESSION['court_fjm']));
            $row = $query->row();
            $start_year = intval(date('Y', $row->start_year));
            $end_year = intval(date('Y', $row->end_year));
            $tree = array();
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
                    'name' => '无会议总结数据',
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
                    $report_count = $this->CountReport($p_id, $i);
                    $tree[] = array(
                        'id' => strval($p_id) . '-' . $i,
                        'pId' => $p_id,
                        'name' => $i . "月（{$report_count}）",
                        'isParent' => FALSE,
                        'reportNum' => $report_count
                    );
                }
            }
        }
        return $tree;
    }

    /**
     * 统计会议资料总数
     *
     * @param int $year 年份
     * @param int $month 月份
     * @return 会议总结资料总数
     */
    private function CountReport($year, $month)
    {
        $start_time = date('Y-m-d', strtotime($year . '-' . $month . '-01'));
        $end_time = date('Y-m-d', strtotime("$start_time +1 month -1 day"));
        //$sql = "SELECT COUNT(0) AS report_count FROM s_document WHERE publish_time BETWEEN ? AND ? AND court_fjm = ? AND document_type = ? AND (is_audit = ? OR audit_status = ?) AND delete_status = ?";
        $sql = "SELECT COUNT(0) AS report_count FROM s_document d, s_schedule s WHERE s.start_time BETWEEN ? AND ? AND d.court_fjm = ? AND d.document_type = ? AND (d.is_audit = ? OR d.audit_status = ?) AND d.delete_status = ? AND d.s_id = s.s_id";
        $query = $this->db->query($sql, array(strtotime($start_time . ' 00:00:00'), strtotime($end_time . ' 23:59:59'), $_SESSION['court_fjm'], 2, 0, 1, 1));
        $row = $query->row();
        return $row->report_count;
    }

    /**
     * 会议资料数量
     *
     * @param string $search_content 搜索内容
     * @return 会议资料数量
     */
    public function count_report_search($search_content)
    {
        $sql = "SELECT COUNT(0) AS search_count FROM s_document WHERE html_content LIKE ? AND court_fjm = ? AND document_type = ? AND (is_audit = ? OR audit_status = ?) AND delete_status = ?";
        $query = $this->db->query($sql, array('%' . $search_content . '%', $_SESSION['court_fjm'], 2, 0, 1, 1));
        $row = $query->row();
        return $row->search_count;
    }

    /**
     * 获取总结资料列表
     *
     * @param string $year_month 年-月
     * @param int $curr_page 当前页
     * @param int $per_page 每页显示数
     * @param string $search_content 搜索内容
     * @return 会议总结资料列表
     */
    public function get_report_list($year_month, $curr_page, $per_page, $search_content)
    {
        $offset = ($curr_page - 1) * $per_page;
        if (!is_int($per_page))
            $per_page = intval($per_page);
        $report_list = array();
        if (!empty($search_content))
        {
            $sql = "SELECT d.id, d.html_content, d.publish_username, d.publish_strtime, json_extract(s.content, '$[0].a') AS meeting_name FROM s_document d, s_system s WHERE d.html_content LIKE ? AND d.s_id = s.s_id AND d.court_fjm = ? AND d.document_type = ? AND (d.is_audit = ? OR d.audit_status = ?) AND d.delete_status = ? ORDER BY d.publish_time LIMIT ?, ?";
            $query = $this->db->query($sql, array('%' . $search_content . '%', $_SESSION['court_fjm'], 2, 0, 1, 1, $offset, $per_page));
        }
        else
        {
            $start_time = date('Y-m-d', strtotime($year_month . '-01'));
            $end_time = date('Y-m-d', strtotime("$start_time +1 month -1 day"));
            $sql = "SELECT d.id, d.html_content, d.publish_username, d.publish_strtime, json_extract(s.content, '$[0].a') AS meeting_name FROM s_document d, s_system s, s_schedule h WHERE d.s_id = s.s_id AND d.s_id = h.s_id AND h.start_time BETWEEN ? AND ? AND d.court_fjm = ? AND d.document_type = ? AND (d.is_audit = ? OR d.audit_status = ?) AND d.delete_status = ? ORDER BY d.publish_time LIMIT ?, ?";
            $query = $this->db->query($sql, array(strtotime($start_time . ' 00:00:00'), strtotime($end_time . ' 23:59:59'), $_SESSION['court_fjm'], 2, 0, 1, 1, $offset, $per_page));
        }
        foreach ($query->result() as $row)
        {
            if (substr($row->html_content, 0, 1) == '<')
                $subject = substr($row->html_content, 1);
            else
                $subject = '>' . $row->html_content;
            $subject = strstr(substr($subject, strpos($subject, '>') + 1), '<', TRUE);
            $report_list[] = array(
                'report_name' => $subject,
                'report_time' => $row->publish_strtime,
                'publish_user' => $row->publish_username,
                'meeting_name' => str_replace('"', '', $row->meeting_name),
                'control' => '<button class="button" onClick="View(\'' . $row->id . '\')">查看</button>'
            );
        }
        return $report_list;
    }

    /**
     * 获取总结资料内容
     *
     * @param int $id 会议总结资料id
     * @return 会议总结资料内容
     */
    public function get_report_detail($id)
    {
        $data['content'] = '';
        $data['files'] = array();
        $sql = "SELECT html_content FROM s_document WHERE id = ?";
        $query = $this->db->query($sql, array($id));
        $row = $query->row();
        if (isset($row))
        {
            $data['content'] = $row->html_content;
        }
        $sql = "SELECT file_path, id FROM s_files WHERE s_id = ?";
        $query = $this->db->query($sql, array($id));
        $result = $query->result_array();
        if (isset($result))
        {
            $data['files'] = $result;
        }
        return $data;
    }

}