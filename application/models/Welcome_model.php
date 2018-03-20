<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function reg_session($user_id)
    {
        $portal = $this->load->database('portal', TRUE);
        $query = $portal->query("SELECT org_fyxx.MC AS fy_mc, org_fyxx.FJM AS fy_fjm, org_user_rybs.xm AS user_name, org_user_rybs.rybs, org_user_rybs.nwid, org_user.FZZW_CODE FROM org_fyxx, org_user_rybs, org_user WHERE org_user_rybs.dlm = '{$user_id}' AND org_user_rybs.fy = org_fyxx.FJM AND org_user_rybs.dlm = org_user.YOUXIANG");
        $row = $query->row();
        if (isset($row))
        {
            //登录用户所属法院分级码
            $_SESSION['court_fjm'] = $row->fy_fjm;
            //登录用户所属法院名称
            $_SESSION['court_name'] = $row->fy_mc;
            //登录用户id
            $_SESSION['user_id'] = $user_id;
            //登录用户姓名
            $_SESSION['user_name'] = $row->user_name;
            $_SESSION['user_rybs'] = $row->rybs;
            $_SESSION['user_nwid'] = $row->nwid;
            //判断是否内勤
            if ($row->FZZW_CODE == 'ZDY_NQ')
                $data['user_nq'] = TRUE;
            else
                $data['user_nq'] = FALSE;
            //部门ID
            $department_query = $portal->query("SELECT orgId, SJHM FROM org_user WHERE YOUXIANG='{$user_id}'");
            $department_row = $department_query->row();
            $_SESSION['department_id'] = $department_row->orgId;
            $_SESSION['user_mobile'] = $department_row->SJHM;
            $department_name_query = $portal->query("SELECT MC FROM org_orginfo WHERE orgId='{$_SESSION['department_id']}'");
            $department_name_row = $department_name_query->row();
            $_SESSION['department_name'] = $department_name_row->MC;
            $data['department_name'] = $department_name_row->MC;
            //将用户权限加入session
            $user_function = '';
            $authority_query = $this->db->query("SELECT function_id FROM s_role_function WHERE role_id IN (SELECT role_id FROM s_role_user WHERE user_id='{$user_id}')");
            foreach ($authority_query->result() as $authority)
            {
                $user_function .= $authority->function_id.',';
            }
            $_SESSION['schedule_user_function'] = $user_function;
            
            //返回用于页面显示的数据
            $data['user_name'] = $row->user_name;
            $data['court_name'] = $row->fy_mc;
        }
        else
        {
            $data = array();
        }
        
        return $data;
    }

}