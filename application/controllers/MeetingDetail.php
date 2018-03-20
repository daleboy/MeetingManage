<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MeetingDetail extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
        session_start();
		$this->load->helper('url');
	}

	public function index($s_id)
	{
        $data['user_name'] = $_SESSION['user_name'];
        $data['court_name'] = $_SESSION['court_name'];
        $data['department_name'] = $_SESSION['department_name'];
        $href['s_id'] = $s_id;
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

}
