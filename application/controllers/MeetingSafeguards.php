<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MeetingSafeguards extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('meeting_safeguards_model', 'msm');
    }

    public function technologySafeguards($sId)
    {
        $data = $this->msm->getSafeguards($sId);
        $this->load->view('meeting_safeguards_view', $data);
    }

}
