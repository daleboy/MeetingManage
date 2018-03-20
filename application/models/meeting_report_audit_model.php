<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class meeting_report_audit_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function init()
    {
        $sql = "SELECT 
                    COUNT(0) AS audit
                FROM
                    s_document d,
                    s_flow f,
                    s_system s
                WHERE
                    d.document_type = 2 AND d.is_audit > 0
                        AND d.audit_status = 2
                        AND d.delete_status = 1
                        AND d.s_id = f.s_id
                        AND f.id = (SELECT max(id) FROM s_flow WHERE s_id = f.s_id)
                        AND f.flow_status = 2
                        AND f.next_man = ?
                        AND f.is_meeting_approval = 2
                        AND d.s_id = s.s_id";
        $query = $this->db->query($sql, array($_SESSION['user_id']));
        $row = $query->row();
        $data['audit_count'] = $row->audit;
        return $data;
    }

    /**
     * 获取审核列表
     *
     * @param int $curr_page 当前页
     * @param int $per_page 每页显示数
     * @return 列表数据
     */
    public function get_audit_list($curr_page, $per_page)
    {
        $offset = ($curr_page - 1) * $per_page;
        if (!is_int($per_page))
            $per_page = intval($per_page);
        $audit_list = array();
        $sql = "SELECT 
                    d.id,
                    d.html_content,
                    d.publish_username,
                    d.publish_strtime,
                    json_extract(s.content, '$[0].a') as meeting_name
                FROM
                    s_document d,
                    s_flow f,
                    s_system s
                WHERE
                    d.document_type = 2 AND d.is_audit > 0
                        AND d.audit_status = 2
                        AND d.delete_status = 1
                        AND d.s_id = f.s_id
                        AND f.id = (SELECT max(id) FROM s_flow WHERE s_id = f.s_id)
                        AND f.flow_status = 2
                        AND f.next_man = ?
                        AND f.is_meeting_approval = 2
                        AND d.s_id = s.s_id
                ORDER BY publish_time 
                LIMIT ?, ?";
        $query = $this->db->query($sql, array($_SESSION['user_id'], $offset, $per_page));
        foreach ($query->result() as $row)
        {
            if (substr($row->html_content, 0, 1) == '<')
                $subject = substr($row->html_content, 1);
            else
                $subject = '>' . $row->html_content;
            $subject = strstr(substr($subject, strpos($subject, '>') + 1), '<', TRUE);
            $audit_list[] = array(
                'subject' => $subject,
                'publisher' => $row->publish_username,
                'meeting_name' => str_replace('"', '', $row->meeting_name),
                'publish_time' => $row->publish_strtime,
                'control' => '<button class="button" onClick="Audit(\'' . $row->id . '\')">审核</button>'
            );
        }
        return $audit_list;
    }

    /**
     * 获取总结资料详情
     *
     * @param int $id 会议总结资料id
     * @return 会议总结资料内容
     */
    public function get_audit_detail($id)
    {
        $data['content'] = '';
        $sql = "SELECT html_content FROM s_document WHERE id = ?";
        $query = $this->db->query($sql, array($id));
        $row = $query->row();
        if (isset($row))
        {
            $data['content'] = $row->html_content;
        }
        return $data;
    }

    /**
     * 审核是否同意
     *
     * @param int $id 总结资料id
     * @param int $status 同意或不同意
     * @param string $suggestion 审批意见
     * @return 0提交失败、1提交成功
     */
    public function audit($id, $status, $suggestion)
    {
        $this->db->trans_begin();
        $sql = "UPDATE s_document SET audit_status = ? WHERE id = ?";
        $this->db->query($sql, array($status, $id));
        $sql = "UPDATE s_flow SET approval_text = ?, flow_status = ? WHERE next_man = ? AND s_id = ? AND is_meeting_approval = 2";
        $this->db->query($sql, array($suggestion, 1, $_SESSION['user_id'], $id));
        if ($this->db->trans_status() === FALSE)
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

}