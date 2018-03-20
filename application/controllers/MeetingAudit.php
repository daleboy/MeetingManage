<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MeetingAudit extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
        session_start();
		$this->load->helper('url');
        $this->load->model('meeting_audit_model', 'mam');
	}

	public function index()
	{
        $data = $this->mam->init();
		$data['user_name'] = $_SESSION['user_name'];
        $data['court_name'] = $_SESSION['court_name'];
        $data['department_name'] = $_SESSION['department_name'];
		$this->load->view('header', $data);
		$this->load->view('meeting_audit_view');
        $this->load->view('footer');
	}

    /**
	 * 获取待审核列表
	 *
	 * @param	int	$curr	当前页
	 * @param	string	$perPage	每页显示数
	 * @return	string	列表json数据
	 */
    public function GetAuditList()
    {
        $data['audit_list'] = $this->mam->get_audit_list($this->input->post('curr'), $this->input->post('perPage'));
        $data['curr_page'] = $this->input->post('curr');
        echo json_encode($data);
    }

    /**
	 * 审核
	 *
	 * @param	string $sId    会议申请生成的id
	 * @param	int	$status	   1同意、0不同意
	 * @return	string	提交是否成功json数据
	 */
    public function Audit()
    {
        $result = $this->mam->audit($this->input->post('sId'), $this->input->post('status'), $this->input->post('suggestion'));
        echo json_encode($result);
    }

    /**
     * 转审
     *
     * @param    string $sId    会议申请生成的id
     * @param    string $pUserId    转交给接收者用户id
     * @param    string $suggestion    转审意见
     * @return   string    提交是否成功json数据
     */
    public function PassOn()
    {
        $result = $this->mam->pass_on($this->input->post('sId'), $this->input->post('pUserId'), $this->input->post('suggestion'));
        echo json_encode($result);
    }

}
