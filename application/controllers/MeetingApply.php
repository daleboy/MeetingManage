<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MeetingApply extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
        session_start();
		$this->load->helper('url');
	}

	public function index($s_id = 0)
	{
		$data['user_name'] = $_SESSION['user_name'];
        $data['court_name'] = $_SESSION['court_name'];
        $data['department_name'] = $_SESSION['department_name'];
        $data['s_id'] = $s_id;
		$this->load->view('header', $data);
		$this->load->view('meeting_apply_view'); //视图层调用排期系统页面
        $this->load->view('footer');
	}

}
