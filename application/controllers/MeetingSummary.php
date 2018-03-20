<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MeetingSummary extends CI_Controller {
	
    public $encryption_key;
	public function __construct()
	{
		parent::__construct();
        session_start();
		$this->load->helper('url');
        $this->load->model('meeting_summary_model', 'msm');
        $encryption_key = $this->config->item('encryption_key');
        $this->load->library("cencrypt");
	}

    //会议总结列表页
	public function index()
	{
		$data['user_name'] = $_SESSION['user_name'];
        $data['court_name'] = $_SESSION['court_name'];
        $data['department_name'] = $_SESSION['department_name'];
		$this->load->view('header', $data);
		$this->load->view('meeting_summary_list_view',$data);
        $this->load->view('footer');
	}

    //编辑会议总结页
    public function edit_index()
    {
        $document_id = $this->input->get('id');
        if (!empty($document_id)) {
            $data['document_id'] = $this->cencrypt->cl_decrypt($document_id);
        }
        else{
            $data['document_id'] = "0";
        }
        $data['s_id'] = $this->input->get('s_id');
        $data['user_name'] = $_SESSION['user_name'];
        $data['court_name'] = $_SESSION['court_name'];
        $data['department_name'] = $_SESSION['department_name'];
        $this->load->view('header', $data);
        $this->load->view('meeting_summary_view',$data);
        $this->load->view('footer');
    }

    //展示会议总结页
    public function show_index()
    {
        $document_id = $this->input->get('id');
        if (!empty($document_id)) {
            $data['document_id'] = $this->cencrypt->cl_decrypt($document_id);
        }
        else{
            $data['document_id'] = "0";
        }
        $html_content = $this->msm->select_html_content($data['document_id']);
        $select_title = $this->msm->select_title($data['document_id']);
        $data['htmlcontent'] = $html_content['html_content'];
        $data['title'] = $select_title['title'];
        $data['user_name'] = $_SESSION['user_name'];
        $data['court_name'] = $_SESSION['court_name'];
        $data['department_name'] = $_SESSION['department_name'];
        $this->load->view('header', $data);
        $this->load->view('meeting_summary_show_view',$data);
        $this->load->view('footer');
    }

    //会议总结提交
    public function submit()
    {
        $note_id = $this->input->post('note_id');
        if ($note_id === "0") 
        {
            if ($this->input->post('audit_man') != "no") 
            {
                $audit = $this->msm->insert_flow($_SESSION['user_id'], $this->input->post('audit_man'), $this->input->post('s_id'));
                if ($audit != 0) 
                {
                    $data['result'] = $this->msm->insert_document($this->input->post('htmlcontent'), $this->input->post('s_id'), $this->input->post('filejson'), $audit, 2);
                }
                else
                {
                    $data['result'] = 0;
                }
                
            }
            else
            {
                $data['result'] = $this->msm->insert_document($this->input->post('htmlcontent'), $this->input->post('s_id'), $this->input->post('filejson'), 0, 1);
            }
        }
        else{
            if (!is_numeric($note_id)) {
                $note_id = $this->cencrypt->cl_decrypt($note_id);
            }
            if($this->input->post('audit_man') != "no"){
                $audit = $this->msm->insert_flow($_SESSION['user_id'], $this->input->post('audit_man'), $this->input->post('s_id'));
            }
            else{
                $audit = "";
            }
            $data['result'] = $this->msm->update_document($this->input->post('htmlcontent'),$note_id, $this->input->post('filejson'),$audit);
        }
        if ($data['result'] != 0) {
            $data['result'] = $this->cencrypt->cl_encrypt($data['result']);
        }
        echo json_encode($data);
    }

    //会议总结最近发布读取
    public function showlist()
    {
        if ($this->input->post("more") == 0) {
            $result = $this->msm->select_self_note();
        }
        else{
            $result = $this->msm->select_self_more_note($this->input->post("page"));
        }
        $data = array();
        $data['more'] = "no";
        $data['list'] = array();
        foreach ($result as $key => $value) 
        {   $value['start_time'] = date("Y年m月d日",$value['start_time']);
/*            $value['yuanid'] = $value['id'];*/
            $value['summary_status'] = (empty($value['publish_strtime']))?0:1;
            $value['publish_strtime'] = empty($value['publish_strtime'])?"":$value['publish_strtime'];
            $value['id'] = $this->cencrypt->cl_encrypt($value['id']);
            $data['list'][] = $value;
            if ($this->input->post("more") == 0 && $key == 2) {
                $data['more'] = "yes";
                break;
            }
        }
        $data['user_id'] = $_SESSION['user_id'];
        $data['count'] = $this->select_count();
        echo json_encode($data);
    }

    //获取会议总结数量
    public function select_count()
    {
        $result = $this->msm->select_count();
        return $result['total'];
    }

    //读取某条会议的内容
    public function show()
    {
        if (is_numeric($this->input->post("id"))) {
            $sid = $this->input->post("id");
        }
        else{
            $sid = $this->cencrypt->cl_decrypt($this->input->post("id"));
        }
        $data = $this->msm->select_html_content($sid); 
        $data['filejson'] = $this->msm->select_filejson($sid);
        echo json_encode($data);       
    }

}