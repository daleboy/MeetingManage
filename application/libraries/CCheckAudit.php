<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CCheckAudit extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function CheckMeetingAndScheduleAudit($s_id, $check_schedule = TRUE, $check_meeting = TRUE)
    {
        //场所审核情况
        if ($check_schedule)
        {
            $data['schedule']['auditer'] = '';
            $data['schedule']['audit_time'] = '';
            $data['schedule']['suggestion'] = '';
            $sql = "SELECT status FROM s_schedule WHERE s_id = ?";
            $query = $this->db->query($sql, array($s_id));
            $row = $query->row();
            switch ($row->status) {
                case 0:
                    $data['schedule']['status'] = 0;
                    $data['schedule']['status_chs'] = '<span class="text-yellow">场所申请未提交</span>';
                    break;
                case 1:
                    $data['schedule']['status'] = 1;
                    $data['schedule']['status_chs'] = '<span class="text-sub">场所申请审批中</span>';
                    break;
                case 2:
                    $data['schedule']['status'] = 2;
                    $data['schedule']['status_chs'] = '<span class="text-green">场所审批通过</span>';
                    $approval = $this->GetApproval($s_id, 0);
                    $data['schedule']['auditer'] = $approval['auditer'];
                    $data['schedule']['audit_time'] = $approval['audit_time'];
                    $data['schedule']['suggestion'] = $approval['suggestion'];
                    break;
                case 3:
                    $data['schedule']['status'] = 3;
                    $data['schedule']['status_chs'] = '<span class="text-dot">场所审批不通过</span>';
                    $approval = $this->GetApproval($s_id, 0);
                    $data['schedule']['auditer'] = $approval['auditer'];
                    $data['schedule']['audit_time'] = $approval['audit_time'];
                    $data['schedule']['suggestion'] = $approval['suggestion'];
                    break;
                default:
                    $data['schedule']['status'] = -1;
                    $data['schedule']['status_chs'] = '';
                    break;
            }
        }
        
        //会议审核情况
        if ($check_meeting)
        {
            $data['meeting']['auditer'] = '';
            $data['meeting']['audit_time'] = '';
            $data['meeting']['suggestion'] = '';
            $sql = "SELECT next_man, reach_time, approval_text, flow_status FROM s_flow WHERE s_id = ? AND is_meeting_approval = 1 ORDER BY id DESC LIMIT 1";
            $query = $this->db->query($sql, array($s_id));
            $row = $query->row();
            if (isset($row))
            {
                switch ($row->flow_status) {
                    case 1:
                        $data['meeting']['status'] = 1;
                        $data['meeting']['status_chs'] = '<span class="text-green">会议审批通过</span>';
                        $data['meeting']['auditer'] = $row->next_man;
                        $data['meeting']['audit_time'] = $row->reach_time;
                        $data['meeting']['suggestion'] = $row->approval_text;
                        break;
                    case 2:
                        $data['meeting']['status'] = 2;
                        $data['meeting']['status_chs'] = '<span class="text-sub">会议申请审批中</span>';
                        $data['meeting']['auditer'] = $row->next_man;
                        $data['meeting']['audit_time'] = $row->reach_time;
                        $data['meeting']['suggestion'] = $row->approval_text;
                        break;
                    case 3:
                        $data['meeting']['status'] = 3;
                        $data['meeting']['status_chs'] = '<span class="text-dot">会议审批不通过</span>';
                        $data['meeting']['auditer'] = $row->next_man;
                        $data['meeting']['audit_time'] = $row->reach_time;
                        $data['meeting']['suggestion'] = $row->approval_text;
                        break;
                    default:
                        $data['meeting']['status'] = -1;
                        $data['meeting']['status_chs'] = '';
                        break;
                }
            }
            else
            {
                $data['meeting']['status'] = -1;
                $data['meeting']['status_chs'] = '<span class="text-red">会议审核流程异常</span>';
            }
        }
        return $data;
    }

    private function GetApproval($s_id, $type)
    {
        $sql = "SELECT next_man, reach_time, approval_text FROM s_flow WHERE s_id = ? AND is_meeting_approval = ? ORDER BY id DESC LIMIT 1";
        $query = $this->db->query($sql, array($s_id, $type));
        $row = $query->row();
        if (isset($row))
        {
            $data['auditer'] = $row->next_man;
            $data['audit_time'] = $row->reach_time;
            $data['suggestion'] = $row->approval_text;
        }
        else
        {
            $data['auditer'] = '';
            $data['audit_time'] = '';
            $data['suggestion'] = '';
        }
        return $data;
    }
}