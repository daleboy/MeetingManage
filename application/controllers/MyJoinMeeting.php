<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MyJoinMeeting extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
        session_start();
		$this->load->helper('url');
        $this->load->model('my_join_meeting_model', 'mjmm');
	}

	public function index()
	{
        //$data = $this->mjmm->init();
        $data = $this->mjmm->all_can_join_meeting_count();
		$data['user_name'] = $_SESSION['user_name'];
        $data['court_name'] = $_SESSION['court_name'];
        $data['department_name'] = $_SESSION['department_name'];
		$this->load->view('header', $data);
		$this->load->view('my_join_meeting_view');
        $this->load->view('footer');
	}

    /**
     * 获取参加会议列表
     *
     * @param    int $curr    当前页
     * @param    int $perPage    每页显示数
     * @return   string    列表数据
     */
    public function GetJoinList()
    {
        $data['join_list'] = $this->mjmm->get_my_join_meeting_list($this->input->post('curr'), $this->input->post('perPage'));
        $data['curr_page'] = $this->input->post('curr');
        echo json_encode($data);
    }

}
