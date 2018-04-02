<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MeetingDetail extends CI_Controller {
    
    public function __construct()
	{
		parent::__construct();
        session_start();
        if (!isset($_SESSION['user_name'])) {
            $this->casLogin();
        }
        $this->load->helper('url');
        $this->load->model('meeting_detail_model', 'mdm');
	}

	public function index($s_id)
	{
        $data['user_name'] = $_SESSION['user_name'];
        $data['court_name'] = $_SESSION['court_name'];
        $data['department_name'] = $_SESSION['department_name'];
        $href['s_id'] = preg_match('/^\d*$/', $s_id) ? $this->mdm->getScheduleIdById($s_id) : $s_id;
        $this->load->view('header', $data);
        $this->load->view('meeting_detail_view', $href);
        $this->load->view('footer');
	}

    public function JumpToNotify($notify_id)
    {
        $data['user_name'] = $_SESSION['user_name'];
        $data['court_name'] = $_SESSION['court_name'];
        $data['department_name'] = $_SESSION['department_name'];
        $href['notify_id'] = $notify_id;
        $this->load->view('header', $data);
        $this->load->view('send_notify_view', $href);
        $this->load->view('footer');
    }

    private function casLogin()
    {
        $this->load->library('PhpCAS');
        $this->phpcas->client(CAS_VERSION_2_0, '147.1.6.16', 8080, '/cas');
        $this->phpcas->setNoCasServerValidation();
        if ($this->phpcas->checkAuthentication()) {
            $userEmail = $this->phpcas->getUser();
            $this->load->library('CGetName');
            $userInfo = $this->cgetname->get_user_dept_and_court($userEmail);
            $_SESSION['user_name'] = $userInfo['name'];
            $_SESSION['court_name'] = $userInfo['court_name'];
            $_SESSION['department_name'] = $userInfo['dept_name'];
        } else {
            $this->phpcas->forceAuthentication();
        }
    }

}
