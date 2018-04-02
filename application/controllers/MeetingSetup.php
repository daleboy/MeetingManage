<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MeetingSetup extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        $this->load->helper('url');
        $this->load->model('meeting_setup_model', 'msm');
    }

    public function index()
    {
        $data['user_name'] = $_SESSION['user_name'];
        $data['court_name'] = $_SESSION['court_name'];
        $data['department_name'] = $_SESSION['department_name'];
        $this->load->library('ccheckauthority');
        $this->load->view('header', $data);
        if ($this->ccheckauthority->check_authority($_SESSION['schedule_user_function'], 'set_system')) {
            $this->load->view('meeting_setup_view');
        } else {
            $this->load->view('not_access');
        }
        $this->load->view('footer');
    }

    /* 会议类型设置开始 */
    public function GetMeetingType()
    {
        $data = $this->msm->GetMeetingType();
        echo json_encode($data);
    }

    public function AddMeetingType()
    {
        $result = $this->msm->AddMeetingType($this->input->post('typeName'));
        echo json_encode($result);
    }

    public function ModifyMeetingType()
    {
        $result = $this->msm->ModifyMeetingType($this->input->post('typeName'), $this->input->post('typeId'));
        echo json_encode($result);
    }

    public function DelMeetingType()
    {
        $result = $this->msm->DelMeetingType($this->input->post('typeId'));
        echo json_encode($result);
    }

    /* 技术保障类型设置开始 */
    public function GetTechnologyType()
    {
        $data = $this->msm->GetTechnologyType();
        echo json_encode($data);
    }

    public function AddTechnologyType()
    {
        $result = $this->msm->AddTechnologyType($this->input->post('typeName'), $this->input->post('style'));
        echo json_encode($result);
    }

    public function ModifyTechnologyType()
    {
        $result = $this->msm->ModifyTechnologyType($this->input->post('typeName'), $this->input->post('typeId'), $this->input->post('style'));
        echo json_encode($result);
    }

    public function DelTechnologyType()
    {
        $result = $this->msm->DelTechnologyType($this->input->post('typeId'));
        echo json_encode($result);
    }
}
