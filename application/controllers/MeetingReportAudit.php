<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MeetingReportAudit extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
        session_start();
		$this->load->helper('url');
        $this->load->model('meeting_report_audit_model', 'mram');
	}

	public function index()
	{
        $data = $this->mram->init();
		$data['user_name'] = $_SESSION['user_name'];
        $data['court_name'] = $_SESSION['court_name'];
        $data['department_name'] = $_SESSION['department_name'];
		$this->load->view('header', $data);
		$this->load->view('meeting_report_audit_view');
        $this->load->view('footer');
	}

    /**
     * 获取审核列表
     *
     * @param    int $curr    当前页
     * @param    int $perPage    每页显示数
     * @return   string    列表数据json
     */
    public function GetAuditList()
    {
        $data['audit_list'] = $this->mram->get_audit_list($this->input->post('curr'), $this->input->post('perPage'));
        $data['curr_page'] = $this->input->post('curr');
        echo json_encode($data);
    }

    /**
     * 获取总结资料详情
     *
     * @param int $id 会议总结资料id
     * @return 会议总结资料内容
     */
    public function GetAuditDetail()
    {
        $data = $this->mram->get_audit_detail($this->input->post('id'));
        echo json_encode($data);
    }

    /**
     * 审核是否同意
     *
     * @param int $id 会议总结资料id
     * @param int $status 审核是否同意
     * @param string $suggestion 审核意见
     * @return 审核提交是否成功
     */
    public function audit()
    {
        $result = $this->mram->audit($this->input->post('id'), $this->input->post('status'), $this->input->post('suggestion'));
        echo json_encode($result);
    }

}
