<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Uploadfile extends CI_Controller {
	
	private $file_path;
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
        $this->load->model('upload_model', 'um');
        $this->file_path = $this->config->item('file_path');
	}
    //上传函数公共
    public function upload()
    {

        ini_set("soap.wsdl_cache_enabled", "0");
        $client = new SoapClient("http://147.1.6.30:890/ScheduleWebService.asmx?wsdl");
        $file = $_FILES['file'];
        if (!empty($_FILES)) {
            $tempFile = $file['tmp_name'][0];
            if (empty($tempFile)) 
            {
                echo json_encode(0);
            }
            else{
                $attachment_name[] = $file['name'][0];
                $handle = fopen($tempFile, "r");
                $file_byte = fread($handle, filesize($tempFile));
                $attachment[] = $file_byte;
                fclose($handle);
                $s_id = $_POST['s_id'];
                $params = array(
                    'sId' => $s_id,
                    'attachmentName' => $attachment_name,
                    'attachment' => $attachment
                );
                $result = $client->UploadFiles($params);
                $data = $this->um->getfile($result->UploadFilesResult);
                echo json_encode($data);
            }
        }  

    }
    //上传会议签到表
    public function upload_sign()
    {
        ini_set("soap.wsdl_cache_enabled", "0");
        $client = new SoapClient("http://147.1.6.30:890/ScheduleWebService.asmx?wsdl");
        $file = $_FILES['file'];
        if (!empty($_FILES)) {
            $tempFile = $file['tmp_name'][0];
            //$attachment_name[] = $file['name'][0];
            $times = $this->um->get_sign_times($_POST['s_id']);
            $attachment_name[] = "会议签到表s".$times.date("YmdHis",time()).substr($file['name'][0], strrpos($file['name'][0], "."), strlen($file['name'][0]));
            $handle = fopen($tempFile, "r");
            $file_byte = fread($handle, filesize($tempFile));
            $attachment[] = $file_byte;
            fclose($handle);
            $s_id = $_POST['s_id'];
            $params = array(
                'sId' => $s_id,
                'attachmentName' => $attachment_name,
                'attachment' => $attachment
            );
            $result = $client->UploadFiles($params);
            $data = $this->um->getfile($result->UploadFilesResult);
            echo json_encode($data);
        }  

    }
    //删除文件
    public function deletefile()
    {
        $data = $this->um->deletefile($this->input->post('file_id'));
        echo json_encode($data);
    }

    //下载
    public function download()
    {
		$data = $this->um->download($this->input->post('id'));
		$filedir = $this->file_path.$data['file_path']; //文件名
		echo $filedir;
    }

/*    public function download()
    {
		$data = $this->um->download($this->input->get('file_id'));
		$filedir = $this->file_path.$data['file_path']; //文件名
		$filename = substr($data['file_path'], strripos($data['file_path'], "/")+1, strlen($data['file_path']));
		$date=date("Ymd-H:i:m");
		Header( "Content-type:  application/octet-stream "); 
		Header( "Accept-Ranges:  bytes "); 
		Header( "Accept-Length: " .filesize($filedir));
		$filename = iconv("utf-8", "gbk", $filename);
		header( "Content-Disposition:  attachment;  filename=".$filename); 
		echo file_get_contents($filedir);
		readfile($filedir); 
    }*/

}