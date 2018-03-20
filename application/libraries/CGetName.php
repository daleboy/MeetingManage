<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CGetName extends CI_Model {

    private $portal;

    public function __construct()
    {
        parent::__construct();
        $this->portal = $this->load->database('portal', TRUE);
    }

    //得到用户名称
    public function get_username($userid)
    {
        $sql = "SELECT XM FROM org_user WHERE YOUXIANG = '{$userid}' ";
        $query = $this->portal->query($sql);
        $row = $query->row_array();
        return $row['XM'];
    }

    public function get_user_mobile($userId)
    {
        $sql = "SELECT SJHM FROM org_user WHERE YOUXIANG = '{$userId}'";
        $query = $this->portal->query($sql);
        $row = $query->row();
        if (isset($row))
        {
            if (!is_null($row->SJHM) && $row->SJHM != 'NULL')
                return $row->SJHM;
            else
                return 0;
        }
        else
        {
            return 0;
        }
    }

    public function get_user_email_name_and_mobile($userId)
    {
        $data['name'] = '';
        $data['mobile'] = '';
        $data['email'] = '';
        $sql = "SELECT XM, SJHM, YOUXIANG FROM org_user WHERE userId = ?";
        $query = $this->portal->query($sql, array($userId));
        $row = $query->row();
        if (isset($row))
        {
            $data['name'] = $row->XM;
            if (!is_null($row->SJHM))
                $data['mobile'] = $row->SJHM;
            $data['email'] = $row->YOUXIANG;
        }
        return $data;
    }

    //得到法院名称，可输入法院代码或分级码
    public function get_courtname($courtid)
    {
        if (strstr($courtid, "K")) 
        {
            $sql = "SELECT MC FROM org_fyxx WHERE FJM = '{$courtid}' ";
        }
        else
        {
            $sql = "SELECT MC FROM org_fyxx WHERE DM = {$courtid} ";
        }
        $query = $this->portal->query($sql);
        $row = $query->row_array();
        return $row['MC'];
    }

    public function get_nw_user_id_arr($email_array)
    {
        $sql = "SELECT nwid, dlm FROM org_user_rybs WHERE dlm IN ?";
        $query = $this->portal->query($sql, array($email_array));
        foreach ($query->result() as $row)
        {
            $nw_id_array[$row->dlm] = $row->nwid;
        }
        return $nw_id_array;
    }

    public function get_nwid_name_mobile_arr($emailOrId, $type = 'email')
    {
        if ($type == 'email')
            $sql = "SELECT r.nwid, r.xm, u.YOUXIANG, u.SJHM FROM org_user_rybs r, org_user u WHERE r.rybs = u.RYBS AND u.YOUXIANG = ?";
        else
            $sql = "SELECT r.nwid, r.xm, u.YOUXIANG, u.SJHM FROM org_user_rybs r, org_user u WHERE r.rybs = u.RYBS AND u.userId = ?";
        $query = $this->portal->query($sql, array($emailOrId));
        $row = $query->row();
        if (isset($row->xm)) {
            $xm = $row->xm;
        }
        else {
            $xm = '';
        }
        if (isset($row->nwid)) {
            $nwid = $row->nwid;
        }
        else {
            $nwid = '';
        }
        if (isset($row->SJHM)) {
            $mobile = $row->SJHM;
        }
        else {
            $mobile = '';
        }
        $nw_id_array = array(
            'name' => $xm,
            'nwid' => $nwid,
            'mobile' => $mobile,
            'email' => $row->YOUXIANG
        );
        return $nw_id_array;
    }

    public function get_all_dept_name_and_id($dept_id, $dept_id_str = "", $dept_name_str = "")
    {
        $sql = "SELECT orgId, MC, parentOrgId FROM org_orginfo WHERE orgId = ?";
        $query = $this->portal->query($sql, array($dept_id));
        if ($row = $query->row()) {
            $dept_id_str .= $row->orgId.",";
            $dept_name_str .= $row->MC."/";
            $parent_id = $row->parentOrgId;
        }
        else{
            $dept_id_str = "";
            $dept_name_str = "";
            $parent_id = 0;            
        }
        if ($parent_id != 0) {
            return $this->get_all_dept_name_and_id($parent_id, $dept_id_str, $dept_name_str);
        }
        else{
            if ($dept_id_str != "") {
                $dept_id_str = substr($dept_id_str, 0, -1);
                $dept_name_str = substr($dept_name_str, 0, -1);
            }
            $str_arr = array(
                    'dept_id_str' => $dept_id_str,
                    'dept_name_str' => $dept_name_str
                );
            return $str_arr;
        }
        
    }

    public function get_user_dept_and_court($user_id)
    {
        $sql = "SELECT 
                    o.orgId, o.MC AS dept_name, o.FY, f.MC AS court_name, u.XM, l.password
                FROM
                    org_user u,
                    org_orginfo o,
                    org_fyxx f,
                    org_login l
                WHERE
                    u.YOUXIANG = ?
                        AND u.orgId = o.orgId
                        AND u.FY = f.FJM
                        AND u.YOUXIANG = l.youxiang";
        $query = $this->portal->query($sql, array($user_id));
        $row = $query->row();
        if (isset($row))
        {
            $user = array(
                'name' => $row->XM,
                'password' => $row->password,
                'dept_id' => $row->orgId,
                'dept_name' => $row->dept_name,
                'court_fjm' => $row->FY,
                'court_name' => $row->court_name
            );
        }
        else
        {
            $user = 0;
        }
        return $user;
    }

    public function get_user_password($email)
    {
        $query = $this->portal->select('password')
            ->from('org_login')
            ->where('youxiang', $email)
            ->get();
        $row = $query->row();
        if (isset($row))
        {
            return $row->password;
        }
        else
        {
            return '';
        }
    }

}