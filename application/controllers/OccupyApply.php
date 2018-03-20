<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OccupyApply extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
        session_start();
		$this->load->helper('url');
        $this->load->model('occupy_apply_model', 'oam');
	}

    //获得向我发起的场所占用申请列表
	public function index()
	{
        $data['user_name'] = $_SESSION['user_name'];
        $data['court_name'] = $_SESSION['court_name'];
        $data['department_name'] = $_SESSION['department_name'];
        $data['occupy_list'] = $this->oam->get_occupy_apply_list();
        $this->load->view('header', $data);
        $this->load->view('occupy_apply_view');
        $this->load->view('footer');
	}

}
