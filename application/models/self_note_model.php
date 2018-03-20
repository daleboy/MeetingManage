<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Self_note_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //个人笔记首次提交
    //参数1文本内容，参数2文件数组
    public function insert_document($htmlcontent,$filejson)
    {
        $sql = "INSERT INTO s_document (html_content, publish_user_id, publish_username, court_fjm, court_name, dept_id, dept_name, publish_time, publish_strtime) VALUES(?,?,?,?,?,?,?,?,?)";
        $publish_time = time();
        $publish_strtime = date("Y-m-d H:i:s",$publish_time);
        $this->db->trans_start();
        $query = $this->db->query($sql,array($htmlcontent, $_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['court_fjm'], $_SESSION['court_name'], $_SESSION['department_id'], $_SESSION['department_name'], $publish_time, $publish_strtime));
        $insert_id = $this->db->insert_id();
        if (!empty($filejson)) 
        {
	        foreach ($filejson as $key => $value) {
	            $update_sql = "UPDATE s_files SET s_id = ?, owner = ? WHERE id = ?";
	            $query = $this->db->query($update_sql,array($insert_id, $_SESSION['user_id'], $value['file_id']));
	        }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $res = 0;
        }
        else
        {
            $res = $insert_id;
        }
        return $res;
    }

    //个人笔记二次提交
    //参数1文本内容，参数2文档id，参数3文件数组
    public function update_document($htmlcontent, $note_id,$filejson)
    {
        $sql = "UPDATE s_document SET html_content = ? WHERE id = ?";
        $this->db->trans_start();
        $query = $this->db->query($sql,array($htmlcontent, $note_id));
        foreach ($filejson as $key => $value) {
            $update_sql = "UPDATE s_files SET s_id = ?, owner = ? WHERE id = ?";
            $query = $this->db->query($update_sql,array($note_id, $_SESSION['user_id'], $value['file_id']));
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $res = 0;
        }
        else
        {
            $res = $note_id;
        }
        return $res;
    }

    //个人笔记最近发布读取
    public function select_self_note()
    {
        $sql = "SELECT html_content, publish_strtime, id FROM s_document WHERE publish_user_id = ? AND document_type = ? AND delete_status = ? ORDER BY publish_time DESC LIMIT 4";
        $query = $this->db->query($sql,array($_SESSION['user_id'], 1, 1));
        return $query->result_array();
    }

    //个人笔记最近发布查看更多读取
    //参数1页码
    public function select_self_more_note($page)
    {
        $count = 20;//每页十行
        $page = ((int)$page-1)*$count;
        $sql = "SELECT html_content, publish_strtime, id FROM s_document WHERE publish_user_id = ? AND document_type = ? AND delete_status = ? ORDER BY publish_time DESC LIMIT ?,?";
        $query = $this->db->query($sql,array($_SESSION['user_id'], 1, 1, $page, $count));
        return $query->result_array();
    }

    //获取数据总数
    public function select_count()
    {
        $sql = "SELECT COUNT(0) AS total FROM s_document WHERE publish_user_id = ? AND document_type = ? AND delete_status = ?";
        $query = $this->db->query($sql,array($_SESSION['user_id'], 1, 1));
        return $query->row_array();
    }

    //读取某条笔记的内容
    //参数1会务id
    public function select_html_content($sid)
    {
        $sql = "SELECT html_content FROM s_document WHERE id = ?";
        $query = $this->db->query($sql,array($sid));
        return $query->row_array();
    }

    //读取某条笔记的附件
     //参数1会务id
    public function select_filejson($sid)
    {
        $sql = "SELECT id AS file_id, file_path AS name FROM s_files WHERE s_id = ?";
        $query = $this->db->query($sql,array($sid));
        $data = $query->result_array();
        foreach ($data as $key => $value) {
            $data[$key]['name'] = substr($value['name'], strripos($value['name'],"/")+1, strlen($value['name']));
        }
        return $data;
    }
    //删除上传文件
    //参数1文件id
    public function deletefile($fid)
    {
        $sql = "DELETE FROM s_files WHERE id = ?";
        $query = $this->db->query($sql,array($fid));
        return $query;
    }
    //删除会议总结文档
    //参数1文档id
    public function deleteDocument($document_id)
    {
        $sql = "DELETE FROM s_document WHERE id = ?";
        $query = $this->db->query($sql,array($document_id));
        return $query;
    }
}