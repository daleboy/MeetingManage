<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CRemind extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    // <summary>
    // 发送邮件
    // </summary>
    // <param name="fromId">发件人id</param>
    // <param name="toId">收件人id</param>
    // <param name="subject">主题</param>
    // <param name="content">内容</param>
    // <returns>true或false</returns>
    public function SendEmail($toIdArr, $subject, $content, $sendTime, $fromId = "paiqi@gxfy.com")
    {
        $fromId = substr($fromId, 0, strpos($fromId, '@'));
        $email = $this->load->database('email', TRUE);
        $email->query("set character set 'gbk'");
        $sql = "INSERT INTO email (FROM_ID, TO_ID, TO_ID2, SUBJECT, CONTENT, SEND_TIME, READ_FLAG, SEND_FLAG, DELETE_FLAG, IMPORTANT) VALUES ";
        foreach ($toIdArr as $toId)
        {
            $sql .= "('{$fromId}', '{$toId}', '{$toId},', '" . iconv('UTF-8', 'GBK', $subject) . "', '" . iconv('UTF-8', 'GBK', $content) . "', '{$sendTime}', 0, 1, 0, 1),";
        }
        $sql = substr($sql, 0, -1);
        if ($email->query($sql))
            return TRUE;
        else
            return FALSE;
    }

    // <summary>
    // 发送短消息
    // </summary>
    // <param name="fromId">发消息人id</param>
    // <param name="toId">收消息人id</param>
    // <param name="content">内容</param>
    // <param name="eventId">各系统的id字段</param>
    // <returns>true或false</returns>
    public function SendMessage($toIdArr, $content, $eventId, $sendTime, $fromId = "paiqi@gxfy.com")
    {
        $fromId = substr($fromId, 0, strpos($fromId, '@'));
        $message = $this->load->database('sms', TRUE);
        $message->query("set character set 'gbk'");
        $sql = "INSERT INTO sms (FROM_ID, TO_ID, SMS_TYPE, CONTENT, SEND_TIME, REMIND_FLAG, NOTIFY_ID) VALUES ";
        foreach ($toIdArr as $toId)
        {
            $sql .= "('{$fromId}', '{$toId}', '3', '" . iconv('UTF-8', 'GBK', $content) . "', '{$sendTime}', '1', {$eventId}),";
        }
        $sql = substr($sql, 0, -1);
        if ($message->query($sql))
            return TRUE;
        else
            return FALSE;
    }

    // <summary>
    // 发短信
    // </summary>
    // <param name="fromId">发短信人id</param>
    // <param name="mobile">收短信人手机号码</param>
    // <param name="content">内容</param>
    // <returns>插入sms2后返回的id</returns>
    public function SendSms($mobileArr, $content, $sendTime, $task = 0, $fromId = "paiqi@gxfy.com")
    {
        $sms = $this->load->database('sms', TRUE);
        $sms->query("set character set 'gbk'");
        $sql = "INSERT INTO sms2 (FROM_ID, PHONE, CONTENT, SEND_TIME, TASK) VALUES ";
        foreach ($mobileArr as $mobile)
        {
            if (!empty($mobile) && $mobile != '0')
            {
                $sql .= "('{$fromId}', '{$mobile}', '" . iconv('UTF-8', 'GBK', $content) . "', '{$sendTime}', '{$task}'),";
            }
        }
        $sql = substr($sql, 0, -1);
        if ($sms->query($sql))
            return $sms->insert_id();
        else
            return 0;
    }

    // <summary>
    // 发通知公告
    // </summary>
    // <param name="fromId">发通知人id</param>
    // <param name="content">内容</param>
    // <returns>插入sms2后返回的id</returns>
    public function SendNotify($s_id, $user_id, $dept_id_str, $dept_name_str)
    {
        $notify = $this->load->database('email', TRUE);
        $this->load->database();
        $notify->query("set character set 'gbk'");
        $sql = "SELECT title FROM s_title WHERE s_id = ?";
        $result = array();
        $result['notify'] = "";
        $query = $this->db->query($sql,array($s_id));
        if($row = $query->row()){
            $this->db->trans_begin();
            $sql = "SELECT system_id FROM s_system WHERE s_id = ?";
            $query = $this->db->query($sql,array($s_id));
            $istitle = $query->row();
            if (empty($istitle->system_id)) 
            {
                $sql = "SELECT file_path FROM s_files WHERE s_id = ? AND owner IS NULL";
                $query = $this->db->query($sql,array($s_id));
                $attachment_id = "";
                $attachment_name = "";
                foreach ($query->result_array() as $key => $value) {
                    $file = substr(iconv("utf-8", "gbk", $value['file_path']), 0, strrpos(iconv("utf-8", "gbk", $value['file_path']), "/"));
                    $attachment_id .= "Schedule/".$file.",";
                    $attachment_name .= preg_replace('/.*\//','',iconv("utf-8", "gbk", $value['file_path']))."*";
                }
                $sql = "INSERT INTO NOTIFY_NEW(FROM_ID,FROM_DEPNAME,DEPT_LONG_ID,SUBJECT,SEND_TIME,ATTACHMENT_ID,ATTACHMENT_NAME,PRINT,PUBLISH) values (?,?,?,?,?,?,?,?,?)";
                $send_time = date("Y-m-d H:i:s",time());
                $dept_name_str = iconv("utf-8", "gbk", $dept_name_str);
                $row->title = iconv("utf-8", "gbk", $row->title);
                $is_notify = $notify->query($sql,array($user_id, $dept_name_str, $dept_id_str, $row->title, $send_time, $attachment_id, $attachment_name, 1, 0));
                $notify_id = $notify->insert_id();
                $sql = "UPDATE s_system SET system_id = ? WHERE s_id = ?";
                $is_system = $this->db->query($sql,array($notify_id, $s_id));  
            }
            else
            {
                $notify_id = $istitle->system_id;
                $sql = "UPDATE NOTIFY_NEW SET SEND_TIME = ? WHERE NOTIFY_ID = ?";
                $send_time = date("Y-m-d H:i:s",time());
                $is_notify = $notify->query($sql,array($send_time, $notify_id));
                $is_system = 1;
            }
            if ($this->db->trans_status() === TRUE) {
                if($is_notify && $is_system){
                    $this->db->trans_commit();
                    $result['result'] = 1;
                    $result['notify'] = $notify_id;
                    return $result;//发布通知成功
                }
                elseif(!$is_notify){
                    $this->db->trans_rollback();
                    $result['result'] = 2;
                    return $result;//插入通知表失败
                }
                else{
                    $this->db->trans_rollback();
                    $result['result'] = 3;
                    return $result;//更新排期系统表标示失败
                }
            }
        }
        else{
            $result['result'] = 4;
            return $result;//s_id无效
        }
    }
}