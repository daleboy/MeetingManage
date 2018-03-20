<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class My_meeting_apply_model extends CI_Model {

    private $dept_data;
    private $department;
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    //初始化页面数据
    public function init()
    {
        $sql = "SELECT COUNT(0) AS my_list_count FROM s_schedule s, s_system c, s_room r WHERE s.apply_id = ? AND s.s_id = c.s_id AND s.s_id = r.s_id AND c.system_type = ?";
        $query = $this->db->query($sql, array($_SESSION['user_id'], 3));
        $row = $query->row();
        $data['my_list_count'] = $row->my_list_count;
        return $data;
    }

    /**
     * 获取我的会议申请列表
     *
     * @param    int $curr    当前页
     * @param    int $perPage    每页显示数
     * @return    string    列表数据
     */
    public function get_my_list($curr_page, $per_page)
    {
        $offset = ($curr_page - 1) * $per_page;
        if (!is_int($per_page))
            $per_page = intval($per_page);
        $this->load->library('cgetroomdata');
        $this->load->library('cgetname');
        $this->load->library('ccheckaudit');
        $meeting_list = array();
        $sql = "SELECT s.id, s.s_id, s.start_time, s.end_time, s.status, c.content, r.r_id FROM s_schedule s, s_system c, s_room r WHERE s.apply_id = ? AND s.s_id = c.s_id AND s.s_id = r.s_id AND c.system_type = ? ORDER BY s.start_time DESC LIMIT ?, ?";
        $query = $this->db->query($sql, array($_SESSION['user_id'], 3, $offset, $per_page));
        foreach ($query->result() as $row)
        {
            $content = json_decode($row->content);
            $room = $this->cgetroomdata->get_room_name_and_type($row->r_id);
            $room_court = $this->cgetname->get_courtname($room['ssdw']);
            $control = '<button class="button" onClick="View(\'' . $row->s_id . '\')">详情</button>';

            if ($row->status == 0)
            {
                $status = '<span class="text-yellow">排期申请未提交</span>';
                $control .= '<a href="' . base_url() . 'index.php/MeetingApply/index/' . $row->s_id . '" class="button">提交</a>';
                $control .= '<button class="button" onClick="DelMyApply(\'' . $row->s_id . '\', \'' . $content[0]->a . '\')">删除</button>';
            }
            else
            {
                $approval = $this->ccheckaudit->CheckMeetingAndScheduleAudit($row->s_id);
                //$status = $approval['schedule']['status_chs'] . '、' . $approval['meeting']['status_chs'];
                $status = $approval['schedule']['status_chs'];
                //排期会议都审核通过 删 && $approval['meeting']['status'] == 1
                if ($row->status == 2)
                {
                    if (time() < ($row->start_time - 3600)) //开会前一小时
                    {
                        $control .= '<a href="' . base_url() . 'index.php/MeetingApply/index/' . $row->s_id . '" target="_blank" class="button">修改</a>';
                        $control .= '<button class="button" onClick="Remind(\'' . $row->s_id . '\')">提醒</button>';
                        $control .= '<button class="button" onClick="SendNotify(\'' . $row->s_id . '\')">发公告</button>';
                    }
                    
                    if (time() < $row->end_time && time() > ($row->start_time - 14400)) //会议开始前4小时和会中
                    {
                        $control .= '<a href="' . base_url() . 'index.php/MyMeetingApply/SignIn/' . $row->s_id . '" target="_blank" class="button">签到</a>';
                    }
                    else if (time() > $row->end_time) //会议结束后查看签到情况
                    {
                        $control .= '<a href="' . base_url() . 'index.php/MyMeetingApply/LedShowSingIn/' . $row->s_id . '" target="_blank" class="button">查看签到情况</a>';
                    }
                }
                //排期会议都不通过且会议未开始 删 $row->status == 3 && $approval['meeting']['status'] == 3
                if (time() < $row->start_time)
                {
                    $control .= '<button class="button" onClick="DelMyApply(\'' . $row->s_id . '\', \'' . $content[0]->a . '\')">删除</button>';
                }
                //排期会议申请中 删 || $approval['meeting']['status'] == 2
                if ($row->status == 1)
                {
                    if (time() < ($row->start_time - 3600))
                    {
                        $control .= '<a href="' . base_url() . 'index.php/MeetingApply/index/' . $row->s_id . '" target="_blank" class="button">修改</a>';
                    }
                }
            }
            if (time() > $row->end_time)
            {
                if ($row->status == 2) //删 && $approval['meeting']['status'] == 1
                {
                    $status = '<span class="text-gray">会议已结束</span>';
                    $control .= '<button class="button" onClick="UploadSignInForm(\'' . $row->s_id . '\')">上传签到表</button>';
                }
                else
                    $status = '<span class="text-gray">会议已结束</span>（' . $status . '）';
            }

            $join_dept = $this->GetMeetingDept($row->s_id);
            $join_dept = $join_dept == '' ? '' : '指定部门：' . $join_dept;
            $join_person = $this->GetMeetingPerson($row->s_id);
            $join_person = $join_person == '' ? '' : '指定人员：' . $join_person;
            
            $meeting_list[] = array(
                'meeting_name' => $content[0]->a,
                'meeting_time' => date('Y年m月d日H时i分', $row->start_time) . ' 至 ' . date('Y年m月d日H时i分', $row->end_time),
                'address' => $room_court . $room['name'] . '（' . $room['address'] . '）',
                'join_person' => $join_person,
                'join_dept' => $join_dept,
                'status' => $status,
                'control' => $control
            );
        }
        return $meeting_list;
    }

    /**
     * 获取参会人员
     * 
     * @param    string $s_id    会议申请生成的id
     * @return   string    以逗号分隔的用户姓名
     */
    private function GetMeetingPerson($s_id, $is_appoint = 1)
    {
        $name_str = '';
        $sql = "SELECT user_name FROM s_person WHERE s_id = ? AND valid = 1 AND is_appoint = ?";
        $query = $this->db->query($sql, array($s_id, $is_appoint));
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

    /**
     * 获取参会部门
     * 
     * @param    string $s_id    会议申请生成的id
     * @return   string    以逗号分隔的部门名
     */
    private function GetMeetingDept($s_id)
    {
        $dept_str = '';
        $sql = "SELECT dept_id, dept_name FROM s_dept_number WHERE s_id = ?";
        $query = $this->db->query($sql, array($s_id));
        foreach ($query->result() as $row)
        {
            $sign_up_sql = "SELECT user_name FROM s_person WHERE s_id = ? AND dept_id = ? AND valid = 1 AND is_appoint = 0";
            $sign_up_query = $this->db->query($sign_up_sql, array($s_id, $row->dept_id));
            $sign_up_user = '';
            foreach ($sign_up_query->result() as $sign_up_row)
            {
                $sign_up_user .= $sign_up_row->user_name . '，';
            }
            if ($sign_up_user != '')
            {
                $sign_up_user = substr($sign_up_user, 0, -3);
            }
            else
            {
                $sign_up_user = '无报名';
            }
            $dept_str .= $row->dept_name . '(' . $sign_up_user . ')，';
        }
        if ($dept_str != '')
        {
            $dept_str = substr($dept_str, 0, -3);
        }
        return $dept_str;
    }

    /**
     * 删除我的会议申请
     *
     * @param    string $sId    会议申请生成的id
     * @return   int    1成功、0失败
     */
    public function del_my_apply($s_id)
    {
        $query = $this->db->select('paperless_meetingid')
            ->from('s_system')
            ->where('s_id', $s_id)
            ->get();
        $row = $query->row();
        $paperlessMeetingId = $row->paperless_meetingid;
        $sql = "DELETE 
                    s_schedule, s_person, s_room, s_system, s_title, s_flow, s_dept_number 
                FROM s_schedule 
                LEFT JOIN s_person ON s_person.s_id = s_schedule.s_id 
                LEFT JOIN s_room ON s_room.s_id = s_schedule.s_id 
                LEFT JOIN s_system ON s_system.s_id = s_schedule.s_id 
                LEFT JOIN s_title ON s_title.s_id = s_schedule.s_id
                LEFT JOIN s_flow ON s_flow.s_id = s_schedule.s_id 
                LEFT JOIN s_files ON s_files.s_id = s_schedule.s_id
                LEFT JOIN s_dept_number ON s_dept_number.s_id = s_schedule.s_id
                WHERE s_schedule.s_id = ?";
        if ($this->db->query($sql, array($s_id)))
        {
            if ($paperlessMeetingId > 0)
            {
                $this->load->library('paperlessmeeting');
                $delPaperlessMeeting = $this->paperlessmeeting->DelMeeting($paperlessMeetingId);
            }
            $result = 1;
        }
        else
        {
            $result = 0;
        }
        return $result;
    }

    /**
     * 获取会议提醒信息
     *
     * @param    string $sId    会议申请生成的id
     * @return   string    会议提醒数据
     */
    public function get_meeting_remind($s_id)
    {
        $this->load->library('cgetroomdata');
        $this->load->library('cgetname');
        $data['tea'] = array();
        $data['technology'] = array();
        $data['person'] = array();
        $data['dept'] = array();
        $data['sign_up'] = array();
        $data['remind_history'] = array();
        //获取保障人员信息
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
        //获取参会人员信息
        $sql = "SELECT user_id, user_name FROM s_person WHERE s_id = ? AND valid = 1 AND is_appoint = 1";
        $query = $this->db->query($sql, array($s_id));
        foreach ($query->result() as $row)
        {
            $data['person'][] = array(
                'email' => $row->user_id,
                'name' => $row->user_name,
                'mobile' => $this->cgetname->get_user_mobile($row->user_id)
            );
        }
        //获取部门报名人员信息
        $sql = "SELECT dept_id, dept_name, number FROM s_dept_number WHERE s_id = ?";
        $query = $this->db->query($sql, array($s_id));
        foreach ($query->result() as $row)
        {
            $data['dept'][] = array(
                'id' => $row->dept_id,
                'name' => $row->dept_name,
                'num' => $row->number
            );
            $d_sql = "SELECT user_id, user_name FROM s_person WHERE s_id = ? AND dept_id = ? AND valid = 1 AND is_appoint = 0";
            $d_query = $this->db->query($d_sql, array($s_id, $row->dept_id));
            //已报名人数
            $sign_up_num = $d_query->num_rows();
            if ($sign_up_num > 0)
            {
                foreach ($d_query->result() as $d_row)
                {
                    $data['sign_up'][$row->dept_id][] = array(
                        'email' => $d_row->user_id,
                        'name' => $d_row->user_name,
                        'mobile' => $this->cgetname->get_user_mobile($d_row->user_id)
                    );
                }
            }
        }
        //获取消息提醒历史记录
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

    /**
     * 发送提醒
     * 
     * @param    string $s_id    会议申请生成的id
     * @param    string $email    邮箱
     * @param    string[] $nw_id_arr    内网id数组
     * @param    string[] $name    接收人姓名数组
     * @param    string[] $mobile    接收人手机数组
     * @param    int $send_email    是否发送邮件提醒
     * @param    int $send_message    是否发送消息提醒
     * @param    int $send_sms    是否发送短信提醒
     * @param    string $send_time    发送时间
     * @param    string $send_content    发送内容
     * @return   int    0失败、1成功
     */
    public function send_remind($s_id, $email, $nw_id_arr, $name, $mobile, $send_email, $send_message, $send_sms, $send_time, $send_content)
    {
        $this->load->library('cremind');
        $send_email_result = TRUE;
        $send_message_result = TRUE;
        $send_sms_result = 1;
        $this->db->trans_begin();
        //发送邮件提醒
        if ($send_email == 1)
        {
            if (sizeof($nw_id_arr) > 0)
            {
                $send_email_result = $this->cremind->SendEmail($nw_id_arr, '会议提醒', $send_content, $send_time);
            }
        }
        //发送消息提醒
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
        //发送短信提醒
        if ($send_sms == 1)
        {
            if (sizeof($mobile) > 0)
            {
                $send_sms_result = $this->cremind->SendSms($mobile, $send_content, $send_time);
            }
        }
        //记录发送历史
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
    }

    /**
     * 签到页面初始化数据
     *
     * @param    string $s_id    会议申请生成的id
     * @return   array    签到页面数据
     */
    public function sign_in_init($s_id)
    {
        //需签到总人数
        $sql = "SELECT COUNT(0) AS sign_in_count FROM s_person WHERE s_id = ? AND valid = 1";
        $query = $this->db->query($sql, array($s_id));
        $row = $query->row();
        $data['sign_in_count'] = $row->sign_in_count;
        //会议名称
        $sql = "SELECT json_extract(content, '$[0].a') AS meeting_name FROM s_system WHERE s_id = ?";
        $query = $this->db->query($sql, array($s_id));
        $row = $query->row();
        $data['meeting_name'] = str_replace('"', '', $row->meeting_name);
        //签到人员信息
        $sign_list = array();
        $sql = "SELECT fjm, court_name, dept_id, dept_name, user_id, user_name, sign_in FROM s_person WHERE s_id = ? AND valid = 1 ORDER BY fjm";
        $query = $this->db->query($sql, array($s_id));
        foreach ($query->result() as $row)
        {
            $sign_list[$row->fjm]['court_name'] = $row->court_name;
            $sign_list[$row->fjm]['dept'][$row->dept_id]['dept_name'] = $row->dept_name; 
            $sign_list[$row->fjm]['dept'][$row->dept_id]['person'][] = array(
                'user_id' => $row->user_id,
                'user_name' => $row->user_name,
                'sign_in' => $row->sign_in
            );
        }
        $data['sign_list'] = $sign_list;
        $data['s_id'] = $s_id;
        return $data;
    }

    /**
     * 点击名字签到
     *
     * @param    string $s_id    会议申请id
     * @param    string $user_id    点击的人id
     * @return   int    1成功、0失败
     */
    public function click_sign_in($s_id, $user_id)
    {
        $sql = "UPDATE s_person SET sign_in = 1 WHERE s_id = ? AND user_id = ?";
        if ($this->db->query($sql, array($s_id, $user_id)))
            return 1;
        else
            return 0;
    }

    /**
     * 获取会议公告数据
     *
     * @param    string $s_id    会议申请id
     * @return   array    公告数据
     */
    public function get_meeting_notify($s_id)
    {
        $data['meeting_title'] = "";
        $data['notify_title'] = array();
        $sql = "SELECT title FROM s_title WHERE s_id = ?";
        $query = $this->db->query($sql, array($s_id));
        $row = $query->row();
        if (!empty($row)) {
            $data['meeting_title'] = $row->title;
            $data['start_time'] = date("Y-m-d",time());
        }
        $sql = "SELECT id, file_path FROM s_files WHERE s_id = ?";
        $query = $this->db->query($sql, array($s_id));
        $result = $query->result_array();
        foreach ($result as $key => $value) {
            $data['notify_title'][$key] = $value['file_path'];
            $data['file_id'][$key] = $value['id'];
        }
        return $data;
    }

    /**
     * 获取会议签到表
     *
     * @param    string $s_id    会议申请id
     * @return   array    签到表地址
     */
    public function get_meeting_sign($s_id)
    {
        $data = array();
        $sql = "SELECT id, file_path FROM s_files WHERE s_id = ?";
        $query = $this->db->query($sql, array($s_id));
        $result = $query->result_array();
        foreach ($result as $key => $value) {
            if (strstr($value['file_path'],"签到表s")) 
            {
                $data['notify_title'][$key] = $value['file_path'];
                $data['file_id'][$key] = $value['id'];
            }
        }
        return $data;
    }

    /**
     * 获取本院部门
     *
     * @return    string    部门列表
     */
    public function get_self_court_dept()
    {
        $this->dept_data = $this->get_all_dept_array();
        $sql = "SELECT DEPT_ID FROM department WHERE FY = ? AND (LB = 1 OR LB = 3 OR LB =5)";
        $query = $this->department->query($sql, array($_SESSION['court_fjm']));
        $row = $query->row_array();
        $data = array();
        if (!empty($row)) {
            $sql = "SELECT DEPT_ID, DEPT_NAME, DEPT_PARENT, LB FROM department WHERE DEPT_PARENT = ?";
            $query = $this->department->query($sql, array($row['DEPT_ID']));
            $result = $query->result_array();
            foreach ($result as $key => $value) 
            {
                $data[$key]['dept_id'] = $value['DEPT_ID'];
                $data[$key]['dept_name'] = $this->get_dept_name($value['DEPT_PARENT'], $value['DEPT_NAME']);
            }
        }
        $this->dept_data = array();
        return $data;
    }

    /**
     * 获取所有法院部门
     *
     * @return    string    法院部门列表
     */
    public function get_all_court_dept()
    {
        $this->dept_data = $this->get_all_dept_array();
        $sql = "SELECT DEPT_ID, DEPT_NAME, DEPT_PARENT, LB FROM department WHERE LB = 0 ORDER BY DEPT_PARENT ASC";
        $query = $this->department->query($sql);
        $result = $query->result_array();
        foreach ($result as $key => $value) 
        {
            $data[$key]['dept_id'] = $value['DEPT_ID'];
            $data[$key]['dept_name'] = $this->get_dept_name($value['DEPT_PARENT'], $value['DEPT_NAME']);
        }
        $this->dept_data = array();
        return $data;
    }

    /**
     * 获取法院部门全称
     *
     * @param    int $dept_id    部门id
     * @param    string $dept_name    部门名
     * @return   string    部门全称
     */
    public function get_dept_name($dept_id, $dept_name)
    {
        if ($dept_id != 0) 
        {
            $dept_data = $this->dept_data;
            $dept_name = $dept_data[$dept_id]['dept_name']."->".$dept_name;
            $dept_parent = $dept_data[$dept_id]['dept_parent'];
            $dept_data = "";
            return $this->get_dept_name($dept_parent, $dept_name);
        }
        else{
            //$this->dept_data = array();
            return $dept_name;
        }
    }

    /**
     * 获取所有法院部门数组
     *
     * @return    array    法院部门数组
     */
    public function get_all_dept_array()
    {
        $this->department = $this->load->database('department', TRUE);
        $sql = "SELECT DEPT_ID, DEPT_NAME, DEPT_PARENT, LB FROM department ";
        $query = $this->department->query($sql);
        $result = $query->result_array();
        foreach ($result as $key => $value) 
        {
            $data[$value['DEPT_ID']]['dept_name'] = $value['DEPT_NAME'];
            $data[$value['DEPT_ID']]['lb'] = $value['LB'];
            $data[$value['DEPT_ID']]['dept_parent'] = $value['DEPT_PARENT'];
        }
        return $data;
    }

    /**
     * 发公告
     *
     * @param    string $notify_title    公告标题
     * @param    int $dept_id    部门id
     * @param    string $user_id    用户id
     * @param    string $start_time    开始时间
     * @param    string $end_time    结束时间
     * @param    string $file_dir    附件路径
     * @param    string $file_name    附件名
     * @param    string $content    公告正文
     * @param    string $nw_id    发公告人id
     * @param    int $department_id    发公告人部门id
     * @return   int    0失败、1成功
     */
    public function notify_go($notify_title, $dept_id, $user_id, $start_time, $end_time, $file_dir, $file_name, $content, $nw_id, $department_id)
    {
        $notify_status = 0;
        $notify_user_status = 1;
        $notify_dept_status = 1;
        $portal = $this->load->database('portal', TRUE);
        $notify = $this->load->database('notify', TRUE);
        $department = $this->load->database('department', TRUE);
        $sql = "SELECT DEPT_ID,DEPT_NAME FROM department WHERE DEPT_ID = ?";
        $query = $department->query($sql, array($department_id));
        $dept_row = $query->row_array();
        if (!empty($file_dir)) {
            if (strpos($file_dir,",")){
                $file_dir_arr = explode(",", $file_dir);
                array_pop($file_dir_arr);
                foreach ($file_dir_arr as $key => $value) {
                    $file_dir_arr[$key] = "schedule/".substr($value, 0 , strrpos($value, "/")) ;
                }
                $file_dir = implode(',',$file_dir_arr);
            }
        }
        if (!empty($dept_row)) {
            //插入通知公告表
            $sql = "INSERT INTO notify_new (FROM_ID, FROM_DEPNAME, DEPT_LONG_ID, SUBJECT, CONTENT, SEND_TIME, BEGIN_DATE, END_DATE, ATTACHMENT_ID, ATTACHMENT_NAME, PRINT, TOP, FORMAT, PUBLISH, ANDA, TABLE_NAME) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $this->db->trans_begin();
            $now_time = date("Y-m-d H:i:s",time());
/*            $sql = "INSERT INTO notify_new (FROM_ID, FROM_DEPNAME, DEPT_LONG_ID, SUBJECT, CONTENT, SEND_TIME, BEGIN_DATE, END_DATE, ATTACHMENT_ID, ATTACHMENT_NAME, PRINT, TOP, FORMAT, PUBLISH, ANDA, TABLE_NAME) VALUES('{$nw_id}', '{$dept_row['DEPT_NAME']}', '{$dept_row['DEPT_ID']}', '{$notify_title}', '{$content}', '{$now_time}', '{$start_time}', '{$end_time}', '{$file_dir}', '{$file_name}', 1, 0, 0, 1, 0, 'NOTIFY')";*/
            /*$notify->query($sql);*/
            $notify_status = $notify->query($sql, array($nw_id, $dept_row['DEPT_NAME'], $dept_row['DEPT_ID'], $notify_title, $content, $now_time, $start_time, $end_time, $file_dir, $file_name, 1, 0, 0, 1, 0, 'NOTIFY'));
            $notify_id = $notify->insert_id();
            //选择了查看用户
            if (!empty($user_id)) {
                if (strpos($user_id,",")) {
                    $user_id_arr = explode(",", $user_id);
                    $user_status = "";
                    foreach ($user_id_arr as $key => $value) {
                        $sql = "SELECT nwid FROM org_user_rybs WHERE dlm = ?";
                        $query = $portal->query($sql, array($value));
                        $row = $query->row_array();
                        $user_status .= "(".$notify_id.", '".$row['nwid']."'),";
                    }
                    $user_status = substr($user_status, 0, -1);
                }
                else{
                    $sql = "SELECT nwid FROM org_user_rybs WHERE dlm = ?";
                    $query = $portal->query($sql, array($user_id));
                    $row = $query->row_array();
                    $user_status = "(".$notify_id.", '".$row['nwid']."') ";
                }

                if (!empty($user_status)) {
                    $sql = "INSERT INTO notify_user_id (NOTIFY_ID, USER_ID) VALUES".$user_status;
                    $notify_user_status = $notify->query($sql);
                }
            }
            //选择了查看部门
            if (!empty($dept_id)) 
            {
                //$dept_id = substr($dept_id, 0, -1);
                $dept_id_arr = explode(",", $dept_id);
                $dept_status = "";
                foreach ($dept_id_arr as $key => $value) {
                    $dept_status .= "(".$notify_id.", '".$value."'),";
                }
                $dept_status = substr($dept_status, 0,-1);
                if (!empty($dept_status)) {
                    $sql = "INSERT INTO notify_to_id (NOTIFY_ID, TO_ID) VALUES".$dept_status;
                    $notify_dept_status = $notify->query($sql);
                }
            }
            
            if ($this->db->trans_status() === FALSE || $notify_status == 0 || $notify_user_status == 0 || $notify_dept_status == 0)
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
        else{
            return 0;
        }
        
    }

    /**
     * 读签到二维码
     *
     * @param    string $code    二维码字符串
     * @param    string $sId    会议申请id
     * @param    string $user_id    用户id
     * @return    string    签到是否成功
     */
    public function read_code($flag, $s_id, $user_id)
    {
        $data['status'] = 0;
        if ($flag == 'S') //指定参会人或报名人的二维码
        {
            $sql = "SELECT user_name, court_name, dept_name, occupancy_start_time, occupancy_end_time, sign_in FROM s_person WHERE s_id = ? AND user_id = ? AND valid = 1";
            $query = $this->db->query($sql, array($s_id, $user_id));
            $row = $query->row();
            if (isset($row))
            {
                if (time() > $row->occupancy_end_time)
                {
                    $data['result'] = "{$row->court_name} {$row->dept_name} {$row->user_name}。会议已结束，不能再签到！";
                }
                elseif (time() < ($row->occupancy_start_time - 14400))
                {
                    $data['result'] = "{$row->court_name} {$row->dept_name} {$row->user_name}。还没到会议签到时间！";
                }
                else
                {
                    if ($row->sign_in == 1)
                    {
                        $data['status'] = 2;
                        $data['result'] = "{$row->court_name} {$row->dept_name} {$row->user_name}。已签到！";
                    }
                    else
                    {
                        if ($this->click_sign_in($s_id, $user_id) == 1)
                        {
                            $data['status'] = 1;
                            $data['result'] = "{$row->court_name} {$row->dept_name} {$row->user_name}。签到成功！";
                            $data['userId'] = $user_id;
                            $data['refresh'] = 0;
                        }
                        else
                        {
                            $data['status'] = -1;
                            $data['result'] = "{$row->court_name} {$row->dept_name} {$row->user_name}。签到失败！";
                        }
                    }
                }
            }
            else
            {
                $data['result'] = '该二维码不合法或不是本次参会人员！';
            }
        }
        else //个人二维码
        {
            $sql = "SELECT 
                        s.start_time,
                        s.end_time,
                        s.is_open_court
                    FROM
                        s_schedule s,
                        s_system m
                    WHERE
                        s.s_id = ?
                            AND s.s_id = m.s_id
                            AND m.system_type = 3";
            $query = $this->db->query($sql, array($s_id));
            $row = $query->row();
            if (isset($row))
            {
                $start_time = $row->start_time;
                $end_time = $row->end_time;
                if (time() > $end_time)
                {
                    $data['result'] = "会议已结束，不能再签到！";
                }
                elseif (time() < ($start_time - 14400))
                {
                    $data['result'] = "还没到会议签到时间！";
                }
                else
                {
                    //$meeting_type = $row->is_open_court;
                    $sql = "SELECT sign_in FROM s_person WHERE s_id = ? AND user_id = ?";
                    $query = $this->db->query($sql, array($s_id, $user_id));
                    if ($query->num_rows() > 0) //参会人员表有记录，肯定是指定参会或报名人用个人二维码签到；或无需报名会议已签过到的人
                    {
                        $row = $query->row();
                        if ($row->sign_in == 1)
                        {
                            $data['status'] = 2;
                            $data['result'] = "{$row->court_name} {$row->dept_name} {$row->user_name}。已签到！";
                        }
                        else
                        {
                            $this->load->library('cgetname');
                            $user_info = $this->cgetname->get_user_dept_and_court($user_id);
                            if ($user_info != 0)
                            {
                                if ($this->click_sign_in($s_id, $user_id) == 1)
                                {
                                    $data['status'] = 1;
                                    $data['result'] = "{$user_info['court_name']} {$user_info['dept_name']} {$user_info['name']}。签到成功！";
                                    $data['userId'] = $user_id;
                                    $data['refresh'] = 0;
                                }
                                else
                                {
                                    $data['status'] = -1;
                                    $data['result'] = "{$user_info['court_name']} {$user_info['dept_name']} {$user_info['name']}。签到失败！";
                                }
                            }
                            else
                            {
                                $data['status'] = -1;
                                $data['result'] = "二维码信息不正确！";
                            }
                        }
                    }
                    else //参会人员表无记录，指定人参会的会议但其他人来签到了；或无需报名的人来签到了
                    {
                        $this->load->library('cgetname');
                        $user_info = $this->cgetname->get_user_dept_and_court($user_id);
                        if ($user_info != 0)
                        {
                            $sql = "INSERT INTO s_person (s_id, user_id, user_name, fjm, court_name, dept_id, dept_name, occupancy_start_time, occupancy_end_time, is_appoint, sign_in) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                            if ($this->db->query($sql, array($s_id, $user_id, $user_info['name'], $user_info['court_fjm'], $user_info['court_name'], $user_info['dept_id'], $user_info['dept_name'], $start_time, $end_time, 2, 1)))
                            {
                                $data['status'] = 1;
                                $data['result'] = "{$user_info['court_name']} {$user_info['dept_name']} {$user_info['name']}。签到成功！";
                                $data['userId'] = $user_id;
                                $data['refresh'] = 1;
                            }
                            else
                            {
                                $data['status'] = -1;
                                $data['result'] = "{$user_info['court_name']} {$user_info['dept_name']} {$user_info['name']}。签到失败！";
                            }
                        }
                        else
                        {
                            $data['status'] = -1;
                            $data['result'] = "二维码信息不正确！";
                        }
                    }
                }
            }
        }
        return $data;
    }

    /**
     * 大屏展示签到数据
     *
     * @param    string $s_id    会议申请id
     * @return   展示页面
     */
    public function led_sign_in($s_id)
    {
        $sql = "SELECT COUNT(0) AS sign_in_count FROM s_person WHERE s_id = ? AND valid = 1";
        $query = $this->db->query($sql, array($s_id));
        $row = $query->row();
        $data['sign_in_count'] = $row->sign_in_count;
        $sql = "SELECT json_extract(content, '$[0].a') AS meeting_name FROM s_system WHERE s_id = ?";
        $query = $this->db->query($sql, array($s_id));
        $row = $query->row();
        $data['meeting_name'] = str_replace('"', '', $row->meeting_name);
        $sign_in = array();
        $not_sign_in = array();
        $sql = "SELECT fjm, court_name, dept_id, dept_name, user_id, user_name, sign_in FROM s_person WHERE s_id = ? AND valid = 1 ORDER BY fjm";
        $query = $this->db->query($sql, array($s_id));
        foreach ($query->result() as $row)
        {
            if ($row->sign_in == 1)
            {
                $sign_in[$row->fjm]['court_name'] = $row->court_name;
                $sign_in[$row->fjm]['dept'][$row->dept_id]['dept_name'] = $row->dept_name; 
                $sign_in[$row->fjm]['dept'][$row->dept_id]['person'][] = array(
                    'user_id' => $row->user_id,
                    'user_name' => $row->user_name
                );
            }
            else
            {
                $not_sign_in[$row->fjm]['court_name'] = $row->court_name;
                $not_sign_in[$row->fjm]['dept'][$row->dept_id]['dept_name'] = $row->dept_name; 
                $not_sign_in[$row->fjm]['dept'][$row->dept_id]['person'][] = array(
                    'user_id' => $row->user_id,
                    'user_name' => $row->user_name
                );
            }
        }
        $data['sign_in'] = $sign_in;
        $data['not_sign_in'] = $not_sign_in;
        $data['s_id'] = $s_id;
        return $data;
    }

}