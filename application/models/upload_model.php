<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Upload_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //获取文档文件路径，名称
    //参数1文档id
    public function getfile($file_id)
    {
        $sql = "SELECT file_path FROM s_files WHERE id = ?";
        $query = $this->db->query($sql, array($file_id));
        $row = $query->row_array();
        if (!empty($row)) {
            $data = array(
                    "result" => 1,
                    "filename" => preg_replace('/.*\//','',$row['file_path']),
                    "file_dir" => $row['file_path'],
                    'file_id' => $file_id
                );
        }
        else{
            $data['result'] = 0;
        }
        return $data;
    }

    //删除文件
    //参数1文档id
    public function deletefile($file_id)
    {
        $sql = "DELETE FROM s_files WHERE id = ?";
        if ($this->db->query($sql, array($file_id))) {
            $result = 1;
        }
        else{
            $result = 0;
        }
        return $result;
    }

    //获取签到时间
    //参数1排期id
    public function get_sign_times($s_id)
    {
        $sql = "SELECT count(0) AS times FROM s_files WHERE  s_id = ?";
        $query = $this->db->query($sql, array($s_id));
        $row = $query->row_array();
        return $row['times'];
    }

    //下载：获取下载文档id
    //参数1文档id
    public function download($file_id)
    {
        $sql = "SELECT file_path FROM s_files WHERE id = ?";
        $query = $this->db->query($sql, array($file_id));
        return $query->row_array();
    }

}