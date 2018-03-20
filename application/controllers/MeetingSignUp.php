<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MeetingSignUp extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
        session_start();
		$this->load->helper('url');
        $this->load->model('meeting_sign_up_model', 'msum');
	}

	public function index()
	{
        //$data = $this->msum->get_dept_user($_SESSION['department_id']);
        $data['sign_up_count'] = $this->msum->init();
		$data['user_name'] = $_SESSION['user_name'];
        $data['court_name'] = $_SESSION['court_name'];
        $data['department_name'] = $_SESSION['department_name'];
		$this->load->view('header', $data);
		$this->load->view('meeting_sign_up_view');
        $this->load->view('footer');
	}

    /**
     * 获取部门及子部门树结构数据
     *
     * @param    int $pId    父部门id
     * @return    string    树结构
     */
    public function ShowDeptTree()
    {
        $pId = 0;
        if (array_key_exists('id', $_REQUEST))
        {
            $pId = $_REQUEST['id'];
        }
        $tree = $this->msum->show_dept_tree($pId);
        echo json_encode($tree);
    }

    /**
     * 获取部门人员信息
     *
     * @param    string $sId    会议申请id
     * @param    int $dept_id    部门id
     * @return   string    部门人员数据
     */
    public function GetDeptUser()
    {
        $dept_id = $this->input->post('deptId') == 0 ? $_SESSION['department_id'] : $this->input->post('deptId');
        $data = $this->msum->get_dept_user($this->input->post('sId'), $dept_id);
        echo json_encode($data);
    }

    /**
     * 获取可报名会议列表
     *
     * @param    int $curr    当前页
     * @param    int $perPage    每页显示数
     * @return   string    可报名会议列表
     */
    public function GetSignUpList()
    {
        $data['sign_up_list'] = $this->msum->get_sign_up_list($this->input->post('curr'), $this->input->post('perPage'));
        $data['curr_page'] = $this->input->post('curr');
        echo json_encode($data);
    }

    /**
     * 保存报名人员
     *
     * @param    string $sId    会议申请id
     * @param    string[] $choseUsers    选择的用户
     * @param    string[] $chosedUsers   上一次选择的用户
     * @param    string[] $delChosedUsers    删除上一次选择的用户
     * @return   string    保存成功与否
     */
    public function SaveSignUp()
    {
        $result = $this->msum->save_sign_up($this->input->post('sId'), $this->input->post('choseUsers'), $this->input->post('chosedUsers'), $this->input->post('delChosedUsers'));
        echo json_encode($result);
    }

}
