<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Meeting_audit_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //原会议审核、改会议室审核
    /*public function init()
    {
    $sql = "SELECT
    COUNT(0) AS my_audit_count
    FROM
    s_flow f
    WHERE
    next_man = ?
    AND flow_status = 2
    AND is_meeting_approval = 1
    AND id = (SELECT
    MAX(id)
    FROM
    s_flow
    WHERE
    s_id = f.s_id AND is_meeting_approval = 1)";
    $query = $this->db->query($sql, array($_SESSION['user_id']));
    $row = $query->row();
    $data['my_audit_count'] = $row->my_audit_count;
    return $data;
    }

    public function get_audit_list($curr_page, $per_page)
    {
    $offset = ($curr_page - 1) * $per_page;
    if (!is_int($per_page))
    $per_page = intval($per_page);
    $this->load->library('cgetroomdata');
    $this->load->library('cgetname');
    $this->load->library('ccheckaudit');
    $audit_list = '';
    $sql = "SELECT
    f.id, s.s_id, s.start_time, s.end_time, c.content, r.r_id
    FROM
    s_schedule s,
    s_system c,
    s_room r,
    s_flow f
    WHERE
    s.s_id = c.s_id AND s.s_id = r.s_id
    AND s.s_id = f.s_id
    AND f.next_man = ?
    AND f.flow_status = 2
    AND f.is_meeting_approval = 1
    AND f.id = (SELECT
    MAX(id)
    FROM
    s_flow
    WHERE
    s_id = f.s_id AND is_meeting_approval = 1)
    ORDER BY s.start_time DESC LIMIT ?, ?";
    $query = $this->db->query($sql, array($_SESSION['user_id'], $offset, $per_page));
    foreach ($query->result() as $row)
    {
    $content = json_decode($row->content);
    $room = $this->cgetroomdata->get_room_name_and_type($row->r_id);
    $room_court = $this->cgetname->get_courtname($room['ssdw']);
    $control = '<button class="button" onClick="View(\'' . $row->s_id . '\')">详情</button>';
    $control .= '<button class="button" onClick="Audit(\'' . $row->id . '\')">审核</button>';

    $audit_list[] = array(
    'meeting_name' => $content[0]->a,
    'meeting_time' => date('Y年m月d日H时i分', $row->start_time) . ' 至 ' . date('Y年m月d日H时i分', $row->end_time),
    'address' => $room_court . $room['name'] . '（' . $room['address'] . '）',
    'join_person' => $this->GetMeetingPerson($row->s_id),
    'control' => $control
    );
    }
    return $audit_list;
    }*/

    //初始化页面数据
    public function init()
    {
        $sql = "SELECT COUNT(0) AS my_audit_count
                FROM
                    s_schedule AS a
                LEFT JOIN s_system AS b ON b.s_id = a.s_id AND system_type = 3
                LEFT JOIN (
                    SELECT
                        h.next_man,
                        h.s_id
                    FROM
                        s_flow h
                    WHERE
                        h.id = (
                            SELECT
                              max(id)
                            FROM
                              s_flow
                            WHERE
                              is_meeting_approval = 0
                            AND
                              s_id = h.s_id
                        )
                    ) AS c ON c.s_id = a.s_id
                LEFT JOIN s_room AS d ON d.s_id = a.s_id
                WHERE court_fjm = ?
                    AND c.next_man = ?
                    AND a.status = 1
                    AND a.start_time > ?
                    AND long_range_court = 0 ";
        $query = $this->db->query($sql, array($_SESSION['court_fjm'], $_SESSION['user_id'], time()));
        $row = $query->row();
        $data['my_audit_count'] = $row->my_audit_count;
        return $data;
    }

    /**
     * 获取待审核列表
     *
     * @param    int    $curr    当前页
     * @param    string    $perPage    每页显示数
     * @return    string    列表json数据
     */
    public function get_audit_list($curr_page, $per_page)
    {
        $offset = ($curr_page - 1) * $per_page;
        if (!is_int($per_page)) {
            $per_page = intval($per_page);
        }

        $this->load->library('cgetroomdata');
        $this->load->library('cgetname');
        $this->load->library('ccheckaudit');
        $audit_list = array();
        $sql = "SELECT
                    a.s_id,
                    start_time,
                    end_time,
                    r_id,
                    apply_time,
                    b.content
                FROM
                    s_schedule AS a
                        LEFT JOIN
                    s_system AS b ON b.s_id = a.s_id AND system_type = 3
                        LEFT JOIN
                    (SELECT
                        h.next_man, h.s_id
                    FROM
                        s_flow h
                    WHERE
                        h.id = (SELECT
                                MAX(id)
                            FROM
                                s_flow
                            WHERE
                                is_meeting_approval = 0
                                    AND s_id = h.s_id)) AS c ON c.s_id = a.s_id
                        LEFT JOIN
                    s_room AS d ON d.s_id = a.s_id
                WHERE
                    c.next_man = ?
                        AND long_range_court = 0
                        AND a.status = 1
                        AND a.start_time > ?
                ORDER BY a.start_time DESC LIMIT ?, ?";
        $query = $this->db->query($sql, array($_SESSION['user_id'], time(), $offset, $per_page));
        foreach ($query->result() as $row) {
            $content = json_decode($row->content);
            $room = $this->cgetroomdata->get_room_name_and_type($row->r_id);
            $room_court = $this->cgetname->get_courtname($room['ssdw']);
            $control = '<button class="button" onClick="View(\'' . $row->s_id . '\')">详情</button>';
            $control .= '<button class="button" onClick="Audit(\'' . $row->s_id . '\')">审核</button>';
            $audit_list[] = array(
                'meeting_name' => $content[0]->a,
                'meeting_time' => date('Y年m月d日H时i分', $row->start_time) . ' 至 ' . date('Y年m月d日H时i分', $row->end_time),
                'apply_time' => date('Y年m月d日H时i分', strtotime($row->apply_time)),
                'address' => $room_court . $room['name'] . '（' . $room['address'] . '）',
                'join_person' => $this->GetMeetingPerson($row->s_id),
                'control' => $control,
            );
        }
        return $audit_list;
    }

    /**
     * 获取参会人员
     *
     * @param    string $s_id    会议申请生成的id
     * @return   string    以逗号分隔的用户姓名
     */
    private function GetMeetingPerson($s_id)
    {
        $name_str = '';
        $sql = "SELECT user_name FROM s_person WHERE s_id = ? AND valid = 1";
        $query = $this->db->query($sql, array($s_id));
        foreach ($query->result() as $row) {
            $name_str .= $row->user_name . '，';
        }
        if ($name_str != '') {
            $name_str = substr($name_str, 0, -3);
        }
        return $name_str;
    }

    /**
     * 审核
     *
     * @param    string $sId    会议申请生成的id
     * @param    int    $status       1同意、0不同意
     * @return    int    0提交失败、1提交成功
     */
    public function audit($s_id, $status, $suggestion)
    {
        $this->db->trans_begin();
        //修改主表状态
        $sql = "UPDATE s_schedule SET status = ? WHERE s_id = ?";
        $this->db->query($sql, array($status, $s_id));
        //更新审批流程表
        $sql = "UPDATE s_flow
                    SET flow_status = 1, approval_text = ?
                WHERE
                    s_id = ?
                AND id IN (
                    SELECT
                        l.id
                    FROM (
                        SELECT
                            max(id) id
                        FROM
                            s_flow
                        WHERE
                            s_id = ?
                        AND
                            is_meeting_approval = 0
                    ) l
                )";
        $this->db->query($sql, array($suggestion, $s_id, $s_id));
        //会议报名提醒
        if ($status == 2) {
            $sql = "SELECT s.id, s.start_time, s.end_time, r.r_id, m.paperless_meetingid, m.content, m.remind_content, m.technology_remind_content, m.tea_remind_content FROM s_schedule s, s_system m, s_room r WHERE s.s_id = m.s_id AND s.s_id = r.s_id AND s.s_id = ?";
            $query = $this->db->query($sql, array($s_id));
            $row = $query->row();
            $meeting_starttime = date('Y-m-d H:i:s', $row->start_time);
            $meeting_endtime = date('Y-m-d H:i:s', $row->end_time);
            $content = json_decode($row->content);
            //会议标题
            $meeting_title = $content[0]->a;
            //保障人员类型
            $content_d = explode(',', $content[0]->d);
            //消息提醒类型
            $content_e = explode(',', $content[0]->e);
            //提醒内容
            $remind_content = $row->remind_content;
            $technology_remind = $row->technology_remind_content;
            $tea_remind = $row->tea_remind_content;
            //场所id
            $room_id = $row->r_id;
            //主表id
            $main_id = $row->id;
            $this->load->library('cgetname');
            $this->load->library('cgetroomdata');
            //如果选择了消息提醒
            if (sizeof($content_e) > 0 && !empty($content_e[0])) {
                $portal = $this->load->database('portal', true);

                $appoint = array(); //指定参会人员
                $dept_user = array(); //部门内勤

                //获取指定参会人员内网id和手机号
                $query = $this->db->select('user_id')
                    ->where(array('s_id' => $s_id, 'is_appoint' => 1))
                    ->get('s_person');
                foreach ($query->result() as $row) {
                    $appoint[$row->user_id] = $this->cgetname->get_nwid_name_mobile_arr($row->user_id);
                }

                //获取指定参会部门的内勤人员内网id和手机号
                $query = $this->db->select('dept_id')
                    ->where('s_id', $s_id)
                    ->get('s_dept_number');
                foreach ($query->result() as $row) {
                    $nq_query = $portal->select('YOUXIANG, SJHM')
                        ->where(array('orgId' => $row->dept_id, 'FZZW_CODE' => 'ZDY_NQ', 'YX' => 1))
                        ->get('org_user');
                    foreach ($nq_query->result() as $nq_row) {
                        if (!isset($appoint[$nq_row->YOUXIANG])) {
                            $dept_user[$nq_row->YOUXIANG] = $this->cgetname->get_nwid_name_mobile_arr($nq_row->YOUXIANG);
                        }
                    }
                }
                //存在指定参会人员，发提醒
                if (sizeof($appoint) > 0) {
                    $this->send_remind($main_id, $s_id, $appoint, '您有一条会议排期提醒！', $remind_content, $content_e);
                }
                //存在指定部门，发提醒
                if (sizeof($dept_user) > 0) {
                    $this->send_remind($main_id, $s_id, $dept_user, '您有一条会议报名提醒！', $remind_content, $content_e);
                }
                //勾选了茶水保障提醒
                if (in_array('茶水', $content_d)) {
                    $tea_person = $this->cgetroomdata->get_ensure_person($room_id, 2);
                    $tea = array();
                    foreach ($tea_person as $tp) {
                        $temp_tp = $this->cgetname->get_nwid_name_mobile_arr($tp, 'userId');
                        $tea[$temp_tp['email']] = array(
                            'nwid' => $temp_tp['nwid'],
                            'mobile' => $temp_tp['mobile'],
                            'name' => $temp_tp['name'],
                        );
                    }
                    if (sizeof($tea) > 0) {
                        $this->send_remind($main_id, $s_id, $tea, '您有一条会议保障提醒！', $tea_remind, $content_e);
                    }
                }
                //勾选了技术支持
                if (in_array('技术支持', $content_d)) {
                    $technology_person = $this->cgetroomdata->get_ensure_person($room_id, 1);
                    $technology = array();
                    foreach ($technology_person as $tp) {
                        $temp_tp = $this->cgetname->get_nwid_name_mobile_arr($tp, 'userId');
                        $technology[$temp_tp['email']] = array(
                            'nwid' => $temp_tp['nwid'],
                            'mobile' => $temp_tp['mobile'],
                            'name' => $temp_tp['name'],
                        );
                    }
                    if (sizeof($technology) > 0) {
                        $this->send_remind($main_id, $s_id, $technology, '您有一条会议保障提醒！', $technology_remind . "(<a href=\"http://147.1.4.90/MeetingManage/index.php/MeetingSafeguards/technologySafeguards/{$s_id}\" target=\"_blank\">点击查看会议保障通知单</a>)", $content_e);
                    }
                }
            }

            //添加无纸化会议
            //先判断是不是无纸化会议室，是的话才推送数据
            if ($this->cgetroomdata->check_is_paperless_room($room_id)) {
                //获取用户
                $paperlessUser = array();
                $query = $this->db->select('user_id, user_name, dept_name')
                    ->from('s_person')
                    ->where('s_id', $s_id)
                    ->get();
                foreach ($query->result() as $row) {
                    $paperlessUser[] = array(
                        'email' => $row->user_id,
                        'name' => $row->user_name,
                        'password' => $this->cgetname->get_user_password($row->user_id),
                        'department' => $row->dept_name,
                    );
                }
                //获取文件
                $files = array();
                $query = $this->db->select('file_path')
                    ->from('s_files')
                    ->where('s_id', $s_id)
                    ->where('owner is null')
                    ->get();
                foreach ($query->result() as $row) {
                    $filePath = $row->file_path;
                    $fileUrl = $this->config->item('file_path') . $filePath;
                    $filePath = str_replace('/', '\\', $filePath);
                    $files[] = array(
                        'filePath' => $filePath,
                        'fileUrl' => $fileUrl,
                    );
                }
                $this->AddPaperlessMeeting($s_id, $room_id, $meeting_title, $meeting_starttime, $meeting_endtime, $paperlessUser, $files);
            }
        }
        if ($status == 3) {
            //场所表资源释放
            $sql = "UPDATE s_room SET valid = 0 WHERE s_id = ?";
            $this->db->query($sql, $s_id);
            //人员表资源释放
            $sql = "UPDATE s_person SET valid = 0 WHERE s_id = ?";
            $this->db->query($sql, $s_id);
            //释放系列案号资源
            $sql = "DELETE FROM s_title WHERE s_id = ?";
            $this->db->query($sql, $s_id);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return 0;
        } else {
            $this->db->trans_commit();
            return 1;
        }
    }

    /**
     * 添加无纸化会议
     *
     * @param [type] $s_id
     * @param [type] $select_room_id
     * @param [type] $meeting_title
     * @param [type] $meeting_starttime
     * @param [type] $meeting_endtime
     * @param [type] $paperlessUser
     * @return void
     */
    private function AddPaperlessMeeting($s_id, $select_room_id, $meeting_title, $meeting_starttime, $meeting_endtime, $paperlessUser, $files)
    {
        //获得无纸化会议场所id
        $this->load->library('cgetroomdata');
        $paperlessRoomId = $this->cgetroomdata->get_paperless_room_id($select_room_id);
        if ($paperlessRoomId > 0) {
            $this->load->library('paperlessmeeting');
            try
            {
                //无纸化会议添加会议
                $paperlessNewMeetingId = $this->paperlessmeeting->NewMeeting($meeting_title);
                if ($paperlessNewMeetingId > 0) {
                    //无纸化会议添加会议安排
                    $paperlessResult = $this->paperlessmeeting->NewMeetingSchedule($paperlessNewMeetingId, $paperlessRoomId, $meeting_starttime, $meeting_endtime);
                    if (!$paperlessResult) {
                        echo '添加无纸化会议安排失败';
                        exit(1);
                    } else {
                        if (sizeof($paperlessUser) > 0) {
                            try
                            {
                                foreach ($paperlessUser as $u) {
                                    $this->paperlessmeeting->AddParticipants($paperlessNewMeetingId, $u['email'], $u['name'], $u['password'], $u['department']);
                                }
                            } catch (Exception $ex) {
                                throw new Exception("添加无纸化会议用户失败", 1);
                                exit(1);
                            }
                        }
                        if (sizeof($files) > 0) {
                            try
                            {
                                foreach ($files as $f) {
                                    $this->paperlessmeeting->UploadUrlFile($_SESSION['user_name'], $f['fileUrl'], $f['filePath'], $paperlessNewMeetingId);
                                }
                            } catch (Exception $ex) {
                                throw new Exception("添加无纸化会议文件失败", 1);
                                exit(1);
                            }
                        }
                        //更新system表中无纸化会议id
                        $this->db->where('s_id', $s_id)
                            ->update('s_system', array('paperless_meetingid' => $paperlessNewMeetingId));
                    }
                } else {
                    echo '添加无纸化会议失败';
                    exit(1);
                }
            } catch (Exception $ex) {
                throw new Exception("无纸化会议数据生成失败", 1);
                exit(1);
            }
        } else {
            echo '获取无纸化会议室失败';
            exit(1);
        }
    }

    /**
     * 发送提醒
     *
     * @param    $main_id    会议申请生成的自增id
     * @param    $s_id    会议申请生成的id
     * @param    $receive_person_array    接收提醒的用户数组
     * @param    $remind_subject    提醒主题
     * @param    $remind_content    提醒内容
     * @param    $remind_type_array    提醒类型数组
     */
    private function send_remind($main_id, $s_id, $receive_person_array, $remind_subject, $remind_content, $remind_type_array)
    {
        $this->load->library('cremind');
        $receive_nwids = array();
        $receive_mobiles = array();
        $message = 0;
        $sms = 0;
        $email = 0;

        //遍历接收人数组，分别获得内网id数组，手机号码数组
        foreach ($receive_person_array as $r) {
            $receive_nwids[] = $r['nwid'];
            if (!empty($r)) {
                $receive_mobiles[] = $r['mobile'];
            }
        }

        //根据消息类型分别发送消息
        if (in_array('网页消息', $remind_type_array)) {
            $message = 1;
            $this->cremind->SendMessage($receive_nwids, $remind_content, $main_id, date('Y-m-d H:i:s', time()));
        }
        if (in_array('手机短信', $remind_type_array)) {
            $sms = 1;
            $this->cremind->SendSms($receive_mobiles, $remind_content, date('Y-m-d H:i:s', time()));
        }
        if (in_array('内网邮箱', $remind_type_array)) {
            $email = 1;
            $this->cremind->SendEmail($receive_nwids, $remind_subject, $remind_content, date('Y-m-d H:i:s', time()));
        }

        //插入消息历史表
        $h = array();
        foreach ($receive_person_array as $key => $r) {
            $h[] = array(
                's_id' => $s_id,
                'send_timestamp' => time(),
                'send_time' => date('Y-m-d H:i:s', time()),
                'sms' => $sms,
                'email' => $email,
                'message' => $message,
                'send_user_email' => $key,
                'send_user_nw_id' => $r['nwid'],
                'send_user_name' => $r['name'],
                'mobile' => $r['mobile'],
                'content' => $remind_content,
            );
        }
        $this->db->insert_batch('s_remind_history', $h);
    }

    /**
     * 转审
     *
     * @param    string $s_id    会议申请生成的id
     * @param    string $user_id    转交给接收者用户id
     * @param    string $suggestion    转审意见
     * @return   int    0提交失败、1提交成功
     */
    public function pass_on($s_id, $user_id, $suggestion)
    {
        $this->db->trans_begin();
        $sql = "SELECT id, flow_num, pre_man, next_man, reach_time, approval_text, flow_status, s_id FROM s_flow WHERE s_id = ? AND is_meeting_approval = 0 ORDER BY reach_time DESC LIMIT 1";
        $query = $this->db->query($sql, array($s_id));
        $row = $query->row();
        $flow_num = $row->flow_num + 1;
        $sql = "UPDATE s_flow SET approval_text = ? WHERE id = ?";
        $this->db->query($sql, array($suggestion, $row->id));
        $sql = "INSERT INTO s_flow (flow_num, pre_man, next_man, reach_time, flow_status, s_id) VALUES (?, ?, ?, ?, ?, ?)";
        $this->db->query($sql, array($flow_num, $row->next_man, $user_id, time(), 2, $row->s_id));
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return 0;
        } else {
            $this->db->trans_commit();
            return 1;
        }
    }

    //原会议审核
    /*public function audit($id, $status, $suggestion)
{
$sql = "SELECT s.id, s.status, m.content, f.s_id FROM s_schedule s, s_system m, s_flow f WHERE s.s_id = m.s_id AND s.s_id = f.s_id AND f.id = ?";
$query = $this->db->query($sql, array($id));
$row = $query->row();
$schedule_status = $row->status;
$content_e = json_decode($row->content);
$content_e = explode(',', $content_e[0]->e);
$s_id = $row->s_id;
$main_id = $row->id;
$this->db->trans_begin();
$sql = "UPDATE s_flow SET approval_text = ?, flow_status = ? WHERE id = ?";
$this->db->query($sql, array($suggestion, $status, $id));
if ($schedule_status == 2)
{
if (sizeof($content_e) > 0)
{
$portal = $this->load->database('portal', TRUE);
$this->load->library('cgetname');
$this->load->library('cremind');
$appoint = array();
$dept_user = array();
$sms = 0;
$message = 0;
$email = 0;
$appoint_sql = "SELECT user_id FROM s_person WHERE s_id = ? AND is_appoint = 1";
$appoint_query = $this->db->query($appoint_sql, array($s_id));
foreach ($appoint_query->result() as $appoint_row)
{
$appoint[$appoint_row->user_id] = $this->cgetname->get_nwid_name_mobile_arr($appoint_row->user_id);
}
$signup_dept_sql = "SELECT dept_id FROM s_dept_number WHERE s_id = ?";
$signup_dept_query = $this->db->query($signup_dept_sql, array($s_id));
foreach ($signup_dept_query->result() as $signup_dept_row)
{
$dept_user_sql = "SELECT YOUXIANG, SJHM FROM org_user WHERE orgId = ? AND YX = 1";
$dept_user_query = $portal->query($dept_user_sql, array($signup_dept_row->dept_id));
foreach ($dept_user_query->result() as $dept_user_row)
{
if (!isset($appoint[$dept_user_row->YOUXIANG]))
{
$dept_user[$dept_user_row->YOUXIANG] = $this->cgetname->get_nwid_name_mobile_arr($dept_user_row->YOUXIANG);
}
}
}
$history_sql1 = 'INSERT INTO s_remind_history (s_id, send_timestamp, send_time, sms, email, message, send_user_email, send_user_nw_id, send_user_name, mobile, content) VALUES ';
if (sizeof($appoint) > 0)
{
foreach ($appoint as $key => $val)
{
$send_users[] = $val['nwid'];
if (!empty($val))
{
$send_mobile[] = $val['mobile'];
}
}
$appoint_subject = '会议提醒';
$appoint_content = '您有一条会议排期提醒！';
if (in_array('网页消息', $content_e))
{
$message = 1;
$this->cremind->SendMessage($send_users, $appoint_content, $main_id, time());
}
if (in_array('手机短信', $content_e))
{
$sms = 1;
$this->cremind->SendSms($send_mobile, $appoint_content, time());
}
if (in_array('内网邮箱', $content_e))
{
$email = 1;
$this->cremind->SendEmail($send_users, $appoint_subject, $appoint_content, time());
}
foreach ($appoint as $key => $val)
{
$history_sql1 .= "('{$s_id}', " . time() . ", '" . date('Y-m-d H:i:s', time()) . "', {$sms}, {$email}, {$message}, '{$key}', '{$val['nwid']}', '{$val['name']}', '{$val['mobile']}', '{$appoint_content}'),";
}
$history_sql1 = substr($history_sql1, 0, -1);
$this->db->query($history_sql1);
}
$history_sql2 = 'INSERT INTO s_remind_history (s_id, send_timestamp, send_time, sms, email, message, send_user_email, send_user_nw_id, send_user_name, mobile, content) VALUES ';
if (sizeof($dept_user) > 0)
{
foreach ($dept_user as $key => $val)
{
$send_dept_users[] = $val['nwid'];
if (!empty($val))
{
$send_dept_mobile[] = $val['mobile'];
}
}
$dept_subject = '会议报名提醒';
$dept_content = '您有一条会议报名提醒！';
if (in_array('网页消息', $content_e))
{
$message = 1;
$this->cremind->SendMessage($send_dept_users, $dept_content, $main_id, time());
}
if (in_array('手机短信', $content_e))
{
$sms = 1;
$this->cremind->SendSms($send_dept_mobile, $dept_content, time());
}
if (in_array('内网邮箱', $content_e))
{
$email = 1;
$this->cremind->SendEmail($send_dept_users, $dept_subject, $dept_content, time());
}
foreach ($dept_user as $key => $val)
{
$history_sql2 .= "('{$s_id}', " . time() . ", '" . date('Y-m-d H:i:s', time()) . "', {$sms}, {$email}, {$message}, '{$key}', '{$val['nwid']}', '{$val['name']}', '{$val['mobile']}', '{$appoint_content}'),";
}
$history_sql2 = substr($history_sql2, 0, -1);
$this->db->query($history_sql2);
}
}
}
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
}*/

}
