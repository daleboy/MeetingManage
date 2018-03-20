<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
        session_start();
		$this->load->model('welcome_model');
	}

	public function index()
	{
		$this->load->library('phpcas');
        $this->phpcas->client(CAS_VERSION_2_0, '147.1.6.16', 8080, '/cas');
        $this->phpcas->setNoCasServerValidation();
        $this->phpcas->forceAuthentication();
        
        if (isset($_REQUEST['logout'])) 
        {
            $_SESSION = array();
            session_destroy();
            $logoutService = base_url();
            $this->phpcas->logoutWithRedirectService("http://147.1.6.16:8080/cas/logout?service=$logoutService");
        }
        //获得CAS登录用户名
        $user_id = $this->phpcas->getUser();
        //$user_id = 'gyzzt@gxfy.com';
		$data = $this->welcome_model->reg_session($user_id);

        if (sizeof($data) > 0)
        {
            //会议审核数
            $this->load->model('meeting_audit_model');
            $meeting_audit = $this->meeting_audit_model->init();
            $data['meeting_audit'] = $meeting_audit['my_audit_count'];
            //会议报名数
            $this->load->model('meeting_sign_up_model');
            $data['sign_up'] = $this->meeting_sign_up_model->init();
            //$data['sign_up'] = $sign_up['sign_up_count'];
            //待参加会议数
            $this->load->model('my_join_meeting_model');
            $join_meeting = $this->my_join_meeting_model->init();
            $data['my_join_meeting_count'] = $join_meeting['my_join_meeting_count'];
            //场所占用申请
            $this->load->model('occupy_apply_model');
            $occupy_apply = $this->occupy_apply_model->get_occupy_apply_list();
            $data['occupy_apply_count'] = sizeof($occupy_apply);
            //会议总结资料待审核数
            $this->load->model('meeting_report_audit_model');
            $report_pending = $this->meeting_report_audit_model->init();
            $data['report_audit_count'] = $report_pending['audit_count'];

            $this->load->view('header', $data);
            $this->load->view('welcome_message');
            $this->load->view('footer');
        }
        else
        {
            $this->load->view('warning');
        }
	}

}
