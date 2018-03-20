<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SelfNote extends CI_Controller {
	
    public $encryption_key;
	public function __construct()
	{
		parent::__construct();
        session_start();
		$this->load->helper('url');
        $this->load->model('self_note_model', 'snm');
        $encryption_key = $this->config->item('encryption_key');
        $this->load->library("cencrypt");
	}
	//将用户信息返回个人笔记
	public function index()
	{
        $document_id = $this->input->get('id');
        if (!empty($document_id)) {
            $data['document_id'] = $this->cencrypt->cl_decrypt($document_id);
        }
        else{
            $data['document_id'] = "0";
        }
		$data['user_name'] = $_SESSION['user_name'];
        $data['court_name'] = $_SESSION['court_name'];
        $data['department_name'] = $_SESSION['department_name'];
		$this->load->view('header', $data);
		$this->load->view('self_note_view',$data);
        $this->load->view('footer');
	}

    //个人笔记提交
    public function submit()
    {
        $note_id = $this->input->post('note_id');
        if ($note_id == "0") {
            $data['result'] = $this->snm->insert_document($this->input->post('htmlcontent'),$this->input->post('filejson'));
        }
        else{
            if (!is_numeric($note_id)) {
                $note_id = $this->cencrypt->cl_decrypt($note_id);
            }
            $data['result'] = $this->snm->update_document($this->input->post('htmlcontent'),$note_id,$this->input->post('filejson'));
        }
        if ($data['result'] != 0) {
            $data['result'] = $this->cencrypt->cl_encrypt($data['result']);
        }
        echo json_encode($data);
    }

    //个人笔记最近发布读取
    public function showlist()
    {
        if ($this->input->post("more") == 0) {
            $result = $this->snm->select_self_note();
        }
        else{
            $result = $this->snm->select_self_more_note($this->input->post("page"));
        }
        $data = array();
        $data['more'] = "no";
        $data['list'] = array();
        foreach ($result as $key => $value) 
        {
            if (substr($value['html_content'], 0, 1) == '<') 
            {
                $subject = substr($value['html_content'], 1);
            }
            else{
                $subject = ">".$value['html_content'];
            }
            $value['html_content'] = strstr(substr($subject, strpos($subject, '>') + 1), '<', TRUE);
            $value['id'] = $this->cencrypt->cl_encrypt($value['id']);
            $data['list'][] = $value;
            if ($this->input->post("more") == 0 && $key == 2) {
                $data['more'] = "yes";
                break;
            }
        }
        $data['count'] = $this->select_count();
        echo json_encode($data);
    }
    //获取个人笔记总数
    public function select_count()
    {
        $result = $this->snm->select_count();
        return $result['total'];
    }

    //读取某条笔记的内容
    public function show()
    {
        if (is_numeric($this->input->post("id"))) {
            $sid = $this->input->post("id");
        }
        else{
            $sid = $this->cencrypt->cl_decrypt($this->input->post("id"));
        }
        $data = $this->snm->select_html_content($sid); 
        $data['filejson'] = $this->snm->select_filejson($sid); 
        echo json_encode($data);       
    }
    //删上传文件
    public function deletefile()
    {
        $data = $this->snm->deletefile($this->input->post("file_id")); 
        echo json_encode($data);     
    }
    //删除个人笔记
    public function deleteDocument()
    {
    	$id = $this->cencrypt->cl_decrypt($this->input->post("id"));
        $data = $this->snm->deleteDocument($id); 
        echo json_encode($data);    	
    }

}