<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Meeting_sign_up_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function init()
    {
        $sql = "SELECT 
                    COUNT(0) AS sign_up_count
                FROM
                    s_dept_number d,
                    s_flow f,
                    s_schedule s
                WHERE
                    d.dept_id = ? AND s.s_id = d.s_id
                        AND s.s_id = f.s_id
                        AND s.start_time > ?
                        AND s.status = 2
                        AND f.flow_status = 1
                        AND f.is_meeting_approval = 1
                        AND f.id = (SELECT 
                            MAX(id)
                        FROM
                            s_flow
                        WHERE
                            s_id = f.s_id AND is_meeting_approval = 1)";
        $query = $this->db->query($sql, array($_SESSION['department_id'], time()));
        $row = $query->row();
        $sign_up_count = $row->sign_up_count;
        return $sign_up_count;
    }

    /**
     * 获取部门及子部门树结构数据
     *
     * @param    int $pId    父部门id
     * @return   array    树结构
     */
    public function show_dept_tree($p_id)
    {
        if ($p_id == 0)
        {
            $p_id = $_SESSION['department_id'];
            $tree[] = array(
                'id' => $p_id,
                'name' => $_SESSION['department_name'],
                'pId' => 0,
                'isParent' => $this->check_son_dept($p_id)
            );
        }
        else
        {
            $portal = $this->load->database('portal', TRUE);
            $sql = "SELECT orgId, parentOrgId, MC FROM org_orginfo WHERE parentOrgId = ? AND YX = 1 ORDER BY PXH";
            $query = $portal->query($sql, array($p_id));
            foreach ($query->result() as $row)
            {
                $tree[] = array(
                    'id' => $row->orgId,
                    'pId' => $row->parentOrgId,
                    'name' => $row->MC,
                    'isParent' => $this->check_son_dept($row->orgId)
                );
            }
        }
        return $tree;
    }

    /**
     * 检查是否有子部门
     *
     * @param    int $p_id    父部门id
     * @return   bool    有、无
     */
    private function check_son_dept($p_id)
    {
        $portal = $this->load->database('portal', TRUE);
        $sql = "SELECT COUNT(0) AS have_son FROM org_orginfo WHERE parentOrgId = ?";
        $query = $portal->query($sql, array($p_id));
        $row = $query->row();
        if ($row->have_son > 0)
            return TRUE;
        else
            return FALSE;
    }

    /**
     * 获取部门人员信息
     *
     * @param    string $sId    会议申请id
     * @param    int $dept_id    部门id
     * @return   string    部门人员数据
     */
    public function get_dept_user($s_id, $dept_id)
    {
        $data['user_list'] = array();
        $portal = $this->load->database('portal', TRUE);
        $sql = "SELECT u.XM, u.YOUXIANG, i.MC FROM org_user u, org_orginfo i WHERE u.orgId = ? AND u.orgId = i.orgId ORDER BY u.PXH";
        $query = $portal->query($sql, array($dept_id));
        foreach ($query->result() as $row)
        {
            $data['user_list'][] = array(
                'user_id' => $row->YOUXIANG,
                'user_name' => $row->XM,
                'sign_up_status' => $this->check_user_sign_up($s_id, $row->YOUXIANG)
            );
            $data['dept_name'] = $row->MC;
        }
        //报名已报名数
        $sign_up_num = $this->get_dept_sign_up_num($s_id);
        foreach ($sign_up_num as $sign)
        {
            if ($sign['dept_id'] == $dept_id)
            {
                $data['space'] = $sign['space'];
                $data['sign_up_num'] = $sign['sign_up_num'];
            }
        }
        
        return $data;
    }

    /**
     * 检查此会议该用户是否已报名或被指定
     *
     * @param    string $s_id    会议申请id
     * @param    string $user_id    用户id
     * @return   array    用户报名或指定情况
     */
    private function check_user_sign_up($s_id, $user_id)
    {
        //查该用户当前会议是否已报名或被指定
        $sql = "SELECT is_appoint FROM s_person WHERE s_id = ? AND user_id = ? AND valid = 1";
        $query = $this->db->query($sql, array($s_id, $user_id));
        $row = $query->row();
        if (isset($row))
        {
            $data['have_log'] = 1;
            $data['is_appoint'] = $row->is_appoint;
        }
        else
        {
            $data['have_log'] = 0;
        }
        //查该用户其他排期是否被占用
        $sql = "SELECT start_time, end_time FROM s_schedule WHERE s_id = ?";
        $query = $this->db->query($sql, array($s_id));
        $row = $query->row();
        if (isset($row))
        {
            $start_time = $row->start_time;
            $end_time = $row->end_time;
            $sql = "SELECT
                        COUNT(0) AS isrepeat
                    FROM
                        s_person
                    WHERE
                        user_id = ?
                    AND valid = 1 
                    AND (
                        ? BETWEEN occupancy_start_time	AND occupancy_end_time
                        OR ? BETWEEN occupancy_start_time AND occupancy_end_time
                        OR occupancy_start_time BETWEEN ? AND ?
                        OR occupancy_end_time BETWEEN ? AND ?
                    )
                    AND s_id <> ?";
            $query = $this->db->query($sql, array($user_id, $start_time, $end_time, $start_time, $end_time, $start_time, $end_time, $s_id));
            $row = $query->row();
            if ($row->isrepeat > 0)
            {
                $data['other_schedule'] = 1;
            }
            else
            {
                $data['other_schedule'] = 0;
            }
        }
        else
        {
            $data['other_schedule'] = 0;
        }
        return $data;
    }
 
    /**
     * 获取可报名会议列表
     *
     * @param    int $curr    当前页
     * @param    int $perPage    每页显示数
     * @return   string    可报名会议列表
     */
    public function get_sign_up_list($curr_page, $per_page)
    {
        $offset = ($curr_page - 1) * $per_page;
        if (!is_int($per_page))
            $per_page = intval($per_page);
        $this->load->library('cgetroomdata');
        $this->load->library('cgetname');
        $sign_up_list = array();
        $sql = "SELECT 
                    s.s_id,
                    s.start_time,
                    s.end_time,
                    d.dept_id,
                    d.dept_name,
                    d.number,
                    c.content,
                    r.r_id
                FROM
                    s_dept_number d,
                    s_flow f,
                    s_schedule s,
                    s_system c,
                    s_room r
                WHERE
                    d.dept_id = ? AND s.s_id = d.s_id
                        AND s.s_id = f.s_id
                        AND s.s_id = c.s_id
                        AND s.s_id = r.s_id
                        AND s.start_time > ?
                        AND s.status = 2
                        AND f.flow_status = 1
                        AND f.is_meeting_approval = 1
                        AND f.id = (SELECT 
                            MAX(id)
                        FROM
                            s_flow
                        WHERE
                            s_id = f.s_id AND is_meeting_approval = 1) 
                ORDER BY s.start_time LIMIT ?, ?";
        $query = $this->db->query($sql, array($_SESSION['department_id'], time(), $offset, $per_page));
        foreach ($query->result() as $row)
        {
            $content = json_decode($row->content);
            $room = $this->cgetroomdata->get_room_name_and_type($row->r_id);
            $room_court = $this->cgetname->get_courtname($room['ssdw']);
            
            $sign_up_list[] = array(
                'meeting_name' => $content[0]->a,
                'meeting_time' => date('Y年m月d日H时i分', $row->start_time) . ' 至 ' . date('Y年m月d日H时i分', $row->end_time),
                'meeting_start_time' => $row->start_time,
                'meeting_end_time' => $row->end_time,
                'address' => $room_court . $room['name'] . '（' . $room['address'] . '）',
                'sign_up_number' => $this->get_dept_sign_up_num($row->s_id),
                'control' => '<button class="button" onClick="View(\'' . $row->s_id . '\')">详情</button><button class="button" onClick="SignUp(\'' . $row->s_id . '\', 0, true)">报名</button>'
            );
        }
        return $sign_up_list;
    }

    /**
     * 获取部门报名数
     *
     * @param    string $s_id    会议申请id
     * @return   array    部门及子部门报名情况
     */
    private function get_dept_sign_up_num($s_id)
    {
        $son_dept = array($_SESSION['department_id']);
        $sign_up = array();
        $portal = $this->load->database('portal', TRUE);
        //获取是否有子部门
        $sql = "SELECT orgId FROM org_orginfo WHERE parentOrgId = ?";
        $query = $portal->query($sql, array($_SESSION['department_id']));
        foreach ($query->result() as $row)
        {
            $son_dept[] = $row->orgId;
        }
        $sql = "SELECT dept_id, dept_name, number FROM s_dept_number WHERE s_id = ?";
        $query = $this->db->query($sql, array($s_id));
        foreach ($query->result() as $row)
        {
            if (in_array($row->dept_id, $son_dept))
            {
                $sign_up[] = array(
                    'dept_id' => $row->dept_id,
                    'dept_name' => $row->dept_name,
                    'space' => $row->number,
                    'sign_up_num' => $this->get_sign_up_num($s_id, $row->dept_id)
                );
            }
        }
        return $sign_up;
    }

    /**
     * 获取报名数
     *
     * @param    string $s_id    会议申请id
     * @param    int $dept_id    部门id
     * @return   int    报名数
     */
    private function get_sign_up_num($s_id, $dept_id)
    {
        $sql = "SELECT COUNT(0) AS sign_up_num FROM s_person WHERE s_id = ? AND dept_id = ? AND is_appoint = 0 AND valid = 1";
        $query = $this->db->query($sql, array($s_id, $dept_id));
        $row = $query->row();
        return $row->sign_up_num;
    }

    /**
     * 保存报名人员
     *
     * @param    string $s_id    会议申请id
     * @param    string[] $chose_users    选择的用户
     * @param    string[] $chosed_users   上一次选择的用户
     * @param    string[] $del_users    删除上一次选择的用户
     * @return   int    0保存失败、1成功
     */
    public function save_sign_up($s_id, $chose_users, $chosed_users, $del_users)
    {
        $this->load->library('paperlessmeeting');

        $sql = "SELECT s.start_time, s.end_time, m.paperless_meetingid FROM s_schedule s, s_system m WHERE s.s_id = ? AND s.s_id = m.s_id";
        $query = $this->db->query($sql, array($s_id));
        $row = $query->row();
        $start_time = $row->start_time;
        $end_time = $row->end_time;
        $paperless_meetingid = $row->paperless_meetingid; //无纸化会议的会议id

        $this->db->trans_begin();
        if (sizeof($del_users) > 0)
        {
            $sql = "DELETE FROM s_person WHERE s_id = ? AND user_id IN ? AND valid = 1 AND is_appoint = 0 AND sign_in = 0";
            $this->db->query($sql, array($s_id, $del_users));
            if ($paperless_meetingid > 0) //如果该会议在无纸化会议系统中有记录
            {
                try
                {
                    foreach ($del_users as $d)
                    {
                        $this->paperlessmeeting->DelParticipants($paperless_meetingid, $d);
                    }
                }
                catch (Exception $ex)
                {
                    //throw new Exception("删除报名人员失败", 1);
                }
            }
        }
        
        $sql = "INSERT INTO s_person (s_id, user_id, user_name, fjm, court_name, dept_id, dept_name, occupancy_start_time, occupancy_end_time, is_appoint) VALUES ";
        $this->load->library('cgetname');
        $addUserNum = 0;
        $addUsers = array();
        if (is_null($chosed_users))
        {
            $chosed_users = array();
        }
        foreach ($chose_users as $user)
        {
            //选择了新用户
            if (!in_array($user, $chosed_users))
            {
                $c_sql = "SELECT COUNT(0) AS is_sign_up FROM s_person WHERE s_id = ? AND user_id = ?";
                $c_query = $this->db->query($c_sql, array($s_id, $user));
                $c_row = $c_query->row();
                if ($c_row->is_sign_up <= 0)
                {
                    $u = $this->cgetname->get_user_dept_and_court($user);
                    $sql .= "('{$s_id}', '{$user}', '{$u['name']}', '{$u['court_fjm']}', '{$u['court_name']}', '{$u['dept_id']}', '{$u['dept_name']}', {$start_time}, {$end_time}, 0),";
                    $addUserNum++;
                    array_push($addUsers, $user);

                    if ($paperless_meetingid > 0) //如果该会议在无纸化会议系统中有记录
                    {
                        try
                        {
                            $this->paperlessmeeting->AddParticipants($paperless_meetingid, $user, $u['name'], $u['password'], $u['dept_name']);
                        }
                        catch (Exception $ex)
                        {
                            //throw new Exception("添加报名人员失败", 1);
                        }
                    }
                }
            }
        }
        $create_code_result = 0;
        if ($addUserNum > 0)
        {
            $sql = substr($sql, 0, -1);
            $this->db->query($sql);
        }
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return 0;
        }
        else
        {
            $this->db->trans_commit();
            //生成二维码
            ini_set("soap.wsdl_cache_enabled", "0");
            $client = new SoapClient("http://147.1.4.90:892/CreateMeetingQRCode.asmx?wsdl");
            $params = array(
                'sId' => $s_id,
                'userEmail' => $addUsers
            );
            $create_code_result = $client->CreateQRCode($params);
            return 1;
        }
    }

}