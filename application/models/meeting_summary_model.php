<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Meeting_summary_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //个人会议首次提交
    //参数1文本内容，参数2会务id，参数3文件数组，参数4是否需要审核，参数5审核状态
    public function insert_document($htmlcontent, $s_id, $filejson, $is_audit=0, $audit_status=1)
    {
        $sql = "INSERT INTO s_document (s_id, html_content, publish_user_id, publish_username, court_fjm, court_name, dept_id, dept_name, publish_time, publish_strtime, document_type, is_audit, audit_status) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $publish_time = time();
        $publish_strtime = date("Y-m-d H:i:s",$publish_time);
        $this->db->trans_start();
        $query = $this->db->query($sql,array($s_id,$htmlcontent, $_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['court_fjm'], $_SESSION['court_name'], $_SESSION['department_id'], $_SESSION['department_name'], $publish_time, $publish_strtime, 2, $is_audit, $audit_status));
        $insert_id = $this->db->insert_id();
        foreach ($filejson as $key => $value) {
            $sql = "UPDATE s_files SET s_id = ?, owner = ? WHERE id = ?";
            $query = $this->db->query($sql, array($insert_id, $_SESSION['user_id'], $value['file_id']));
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $res = 0;
        }
        else
        {
            $res = $insert_id;
        }
        return $res;
    }

/*    //会议总结查询排期会议审批状态与是否需要审批
    public function select_schedule($s_id)
    {
        $sql = "SELECT audit, status FROM s_schedule WHERE s_id = ?";
        $query = $this->db->query($sql,array($s_id));
        return $query->row_array();
    } */

    //会议总结插入流程表
    //参数1用户id，参数2审核人id，参数3会务id
    public function insert_flow($user_id, $audit_man_id, $s_id)
    {
        $sql = "INSERT INTO s_flow (flow_num, pre_man, next_man, reach_time, flow_status, s_id, is_meeting_approval) VALUES(?,?,?,?,?,?,?)";
        $reach_time = time();
        $this->db->trans_start();
        $query = $this->db->query($sql,array(1, $user_id, $audit_man_id, $reach_time, 2, $s_id, 2));
        $query_res = $this->db->insert_id();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $res = 0;
        }
        else
        {
            $res = $query_res;
        }
        return $res;
    }

    //个人会议二次提交
    //参数1文本内容，参数2文档id，参数3文件数组，参数4审核状态
    public function update_document($htmlcontent, $note_id, $filejson, $audit=1)
    {
        if ($audit == 1) {
            $is_audit = 0;
        }
        else{
            $audit = 2;
            $is_audit = $audit;
        }
        $sql = "UPDATE s_document SET html_content = ?, audit_status = ?, is_audit = ? WHERE id = ?";
        $this->db->trans_start();
        $query = $this->db->query($sql,array($htmlcontent, $audit, $is_audit, $note_id));
        foreach ($filejson as $key => $value) {
            $sql = "UPDATE s_files SET s_id = ?, owner = ? WHERE id = ?";
            $query = $this->db->query($sql, array($note_id, $_SESSION['user_id'], $value['file_id']));
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $res = 0;
        }
        else
        {
            $res = $note_id;
        }
        return $res;
    }

    //个人会议最近发布读取
    public function select_self_note()
    {
        $sql = "SELECT html_content, publish_strtime, id FROM s_document WHERE publish_user_id = ? AND document_type = ? AND delete_status = ? ORDER BY publish_time DESC LIMIT 4";
        $query = $this->db->query($sql,array($_SESSION['user_id'], 1, 1));
        return $query->result_array();
    }

    //个人会议最近发布查看更多读取
    //参数1页码
    public function select_self_more_note($page)
    {
        $count = 20;//每页十行
        $page = ((int)$page-1)*$count;
        $sql = "SELECT
                    d.id,
                    a.s_id,
                    c.title,
                    e.flow_status,
                    c.start_time,
                    c.end_time,
                    a.apply_id,
                    d.publish_strtime
                FROM
                    s_schedule AS a
                LEFT JOIN (
                    SELECT
                        s_id,
                        system_type
                    FROM
                        s_system
                ) AS b ON a.s_id = b.s_id
                LEFT JOIN s_title AS c ON a.s_id = c.s_id
                LEFT JOIN (SELECT * FROM s_flow WHERE is_meeting_approval = 2) AS e ON a.s_id = e.s_id
                LEFT JOIN (
                    SELECT
                        *
                    FROM
                        s_document
                    WHERE
                        s_id IS NOT NULL
                    AND delete_status = ?
                ) AS d ON a.s_id = d.s_id
                WHERE a.status = ?
                AND
                b.system_type = ?
                AND
                a.apply_id = ?
                AND
                c.end_time < unix_timestamp()
                ORDER BY
                id ASC,
                start_time DESC
                LIMIT ?,?";
        $query = $this->db->query($sql,array(1, 2, 3, $_SESSION['user_id'], $page, $count));
        return $query->result_array();
    }

    //获取数据总数
    public function select_count()
    {
        $sql = "SELECT COUNT(0) AS total 
                FROM
                    s_schedule AS a
                LEFT JOIN (
                    SELECT
                        s_id,
                        system_type
                    FROM
                        s_system
                ) AS b ON a.s_id = b.s_id
                LEFT JOIN s_title AS c ON a.s_id = c.s_id
                LEFT JOIN (
                    SELECT
                        *
                    FROM
                        s_document
                    WHERE
                        s_id IS NOT NULL
                    AND delete_status = ?
                ) AS d ON a.s_id = d.s_id
                WHERE a.status = ?
                AND
                b.system_type = ?
                AND
                a.apply_id = ?
                AND
                c.start_time < unix_timestamp()";
        $query = $this->db->query($sql,array(1, 2, 3, $_SESSION['user_id']));
        return $query->row_array();
    }

    //读取某条会议的内容
    //参数1会务id
    public function select_html_content($sid)
    {
        $sql = "SELECT html_content FROM s_document WHERE id = ?";
        $query = $this->db->query($sql,array($sid));
        return $query->row_array();
    }

    //读取某条会议的标题
    //参数1会务id
    public function select_title($sid)
    {
        $sql = "SELECT b.title FROM s_document AS a LEFT JOIN (SELECT s_id, title FROM s_title) AS b ON a.s_id = b.s_id WHERE a.id = ?";
        $query = $this->db->query($sql,array($sid));
        return $query->row_array();
    }

    //读取某条会议总结的附件
    //参数1会务id
    public function select_filejson($sid)
    {
        $sql = "SELECT id AS file_id, file_path AS name FROM s_files WHERE s_id = ?";
        $query = $this->db->query($sql,array($sid));
        $data = $query->result_array();
        foreach ($data as $key => $value) {
            $data[$key]['name'] = substr($value['name'], strripos($value['name'],"/")+1, strlen($value['name']));
        }
        return $data;
    }
}