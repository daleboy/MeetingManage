<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MeetingView extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
        session_start();
		$this->load->helper('url');
        $this->load->model('meeting_view_model', 'mvm');
	}

	public function index()
	{
		$data['user_name'] = $_SESSION['user_name'];
        $data['court_name'] = $_SESSION['court_name'];
        $data['department_name'] = $_SESSION['department_name'];
		$this->load->view('header', $data);
		$this->load->view('meeting_view_view');
        $this->load->view('footer');
	}

    /**
     * 按年份、月份分类生成树结构
     *
     * @return 数结构数据json
     */
    public function ShowTree()
    {
        $pId = 0;
        if (array_key_exists('id', $_REQUEST))
        {
            $pId = $_REQUEST['id'];
        }
        $tree = $this->mvm->show_tree($pId);
        echo json_encode($tree);
    }

    /**
     * 获取总结资料列表
     *
     * @param string $yearMonth 年-月
     * @param int $curr 当前页
     * @param int $perPage 每页显示数
     * @return 会议总结资料列表
     */
    public function GetMeetingList()
    {
        $data['meeting_list'] = $this->mvm->get_meeting_list($this->input->post('yearMonth'), $this->input->post('curr'), $this->input->post('perPage'));
        $data['curr_page'] = $this->input->post('curr');
        echo json_encode($data);
    }

}