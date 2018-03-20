<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MeetingReport extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
        session_start();
		$this->load->helper('url');
        $this->load->model('meeting_report_model', 'mrm');
	}

	public function index()
	{
		$data['user_name'] = $_SESSION['user_name'];
        $data['court_name'] = $_SESSION['court_name'];
        $data['department_name'] = $_SESSION['department_name'];
		$this->load->view('header', $data);
		$this->load->view('meeting_report_view');
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
        $tree = $this->mrm->show_tree($pId);
        echo json_encode($tree);
    }

    /**
     * 获取总结资料列表
     *
     * @param string $yearMonth 年-月
     * @param int $curr 当前页
     * @param int $perPage 每页显示数
     * @param string $searchContent 搜索内容
     * @return 会议总结资料列表
     */
    public function GetReportList()
    {
        $data['report_list'] = $this->mrm->get_report_list($this->input->post('yearMonth'), $this->input->post('curr'), $this->input->post('perPage'), trim($this->input->post('searchContent')));
        $data['curr_page'] = $this->input->post('curr');
        echo json_encode($data);
    }

    /**
     * 获取总结资料内容
     *
     * @param int $id 会议总结资料id
     * @return 会议总结资料内容
     */
    public function GetReportDetail()
    {
        $data = $this->mrm->get_report_detail($this->input->post('id'));
        echo json_encode($data);
    }

    /**
     * 会议资料数量
     *
     * @param string $search_content 搜索内容
     * @return 会议资料数量
     */
    public function CountReportSearch()
    {
        $data = $this->mrm->count_report_search(trim($this->input->post('searchContent')));
        echo json_encode($data);
    }

}