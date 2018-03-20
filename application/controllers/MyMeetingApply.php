<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MyMeetingApply extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
        session_start();
		$this->load->helper('url');
        $this->load->model('my_meeting_apply_model', 'mmam');
	}

	public function index()
	{
        $data = $this->mmam->init();
		$data['user_name'] = $_SESSION['user_name'];
        $data['court_name'] = $_SESSION['court_name'];
        $data['department_name'] = $_SESSION['department_name'];
		$this->load->view('header', $data);
		$this->load->view('my_meeting_apply_view');
        $this->load->view('footer');
	}

    /**
     * 获取我的会议申请列表
     *
     * @param    int $curr    当前页
     * @param    int $perPage    每页显示数
     * @return    string    列表json数据
     */
    public function GetMyList()
    {
        $data['meeting_list'] = $this->mmam->get_my_list($this->input->post('curr'), $this->input->post('perPage'));
        $data['curr_page'] = $this->input->post('curr');
        echo json_encode($data);
    }

    /**
     * 删除我的会议申请
     *
     * @param    string $sId    会议申请生成的id
     * @return   string    删除成功json数据
     */
    public function DeleteMyApply()
    {
        $result = $this->mmam->del_my_apply(trim($this->input->post('sId')));
        echo json_encode($result);
    }

    /**
     * 获取会议通知公告
     *
     * @param    string $sId    会议申请生成的id
     * @return   string    通知公告数据
     */
    public function GetMeetingNotify()
    {
        $data = $this->mmam->get_meeting_notify(trim($this->input->post('sId')));
        echo json_encode($data);
    }

    /**
     * 获取会议提醒信息
     *
     * @param    string $sId    会议申请生成的id
     * @return   string    会议提醒数据
     */
    public function GetMeetingRemind()
    {
        $data = $this->mmam->get_meeting_remind(trim($this->input->post('sId')));
        echo json_encode($data);
    }

    /**
     * 获取签到数据
     *
     * @param    string $sId    会议申请生成的id
     * @return   string    已签到、未签到数据
     */
    public function GetSign()
    {
        $data = $this->mmam->get_meeting_sign(trim($this->input->post('sId')));
        echo json_encode($data);
    }

    /**
     * 发送通知公告
     *
     * @param    string $notify_title    公告标题
     * @param    int $dept_id    部门id
     * @param    string $user_id    用户内网id
     * @param    string $start_time    开始时间
     * @param    string $end_time    结束时间
     * @param    string $file_dir    文件路径
     * @param    string $file_name    文件名
     * @param    string $content    公告正文
     * @param    string $user_nwid    发布人内网id
     * @param    int $department_id    发布人部门id
     * @return   string    发布成功失败
     */
    public function NotifyGo()
    {
        $data = $this->mmam->notify_go($this->input->post('notify_title'), $this->input->post('dept_id'), $this->input->post('user_id'), $this->input->post('start_time'), $this->input->post('end_time'), $this->input->post('file_dir'), $this->input->post('file_name'), $this->input->post('content'), $_SESSION['user_nwid'], $_SESSION['department_id']);
        echo json_encode($data);        
    }

    /**
     * 发送提醒
     * 
     * @param    string $s_id    会议申请生成的id
     * @param    string $email    邮箱
     * @param    string[] $nw_id_arr    内网id数组
     * @param    string[] $name    接收人姓名数组
     * @param    string[] $mobile    接收人手机数组
     * @param    int $send_email    是否发送邮件提醒
     * @param    int $send_message    是否发送消息提醒
     * @param    int $send_sms    是否发送短信提醒
     * @param    string $send_time    发送时间
     * @param    string $send_content    发送内容
     * @return   string    失败、成功json
     */
    public function SendRemind()
    {
        //分解参会人员信息
        $temp = $this->input->post('meetingPerson');
        if (!empty($temp) && sizeof($temp) > 0)
        {
            foreach ($temp as $t)
            {
                $email[] = $t['email'];
                $name[$t['email']] = $t['name'];
                $mobile[$t['email']] = $t['mobile'];
            }
        }
        //分解报名人员信息
        $temp = $this->input->post('signUpPerson');
        if (!empty($temp) && sizeof($temp) > 0)
        {
            foreach ($temp as $t)
            {
                foreach ($t as $tu)
                {
                    $email[] = $tu['email'];
                    $name[$tu['email']] = $tu['name'];
                    $mobile[$tu['email']] = $tu['mobile'];
                }
            }
        }
        //分解茶水保障人员信息
        $temp = $this->input->post('tea');
        if (!empty($temp) && sizeof($temp) > 0)
        {
            foreach ($temp as $t)
            {
                $email[] = $t['email'];
                $name[$t['email']] = $t['name'];
                $mobile[$t['email']] = $t['mobile'];
            }
        }
        //分解技术支持人员信息
        $temp = $this->input->post('technology');
        if (!empty($temp) && sizeof($temp) > 0)
        {
            foreach ($temp as $t)
            {
                $email[] = $t['email'];
                $name[$t['email']] = $t['name'];
                $mobile[$t['email']] = $t['mobile'];
            }
        }
        //获取内网id
        if (sizeof($email) > 0)
        {
            $this->load->library('cgetname');
            $nw_id = $this->cgetname->get_nw_user_id_arr($email);
        }
        $result = $this->mmam->send_remind($this->input->post('sId'), $email, $nw_id, $name, $mobile, $this->input->post('sendEmail'), $this->input->post('sendMessage'), $this->input->post('sendSms'), $this->input->post('remindTime'), $this->input->post('remindContent'));
        echo json_encode($result);
    }

    /**
     * 会议签到
     * 
     * @param    string $s_id    会议申请id
     * @return   会议签到页面
     */
    public function SignIn($s_id)
    {
        $data = $this->mmam->sign_in_init($s_id);
        $data['user_name'] = $_SESSION['user_name'];
        $data['court_name'] = $_SESSION['court_name'];
        $data['department_name'] = $_SESSION['department_name'];
		$this->load->view('header', $data);
		$this->load->view('sign_in_view');
        $this->load->view('footer');
    }

    /**
     * 点击名字签到
     *
     * @param    string $s_id    会议申请id
     * @param    string $user_id    点击的人id
     * @return   string    1成功、0失败json
     */
    public function ClickSignIn()
    {
        $result = $this->mmam->click_sign_in(trim($this->input->post('sId')), $this->input->post('userId'));
        echo json_encode($result);
    }

    /**
     * 获取本院部门
     *
     * @return    string    部门列表
     */
    public function GetSelfCourtDept()
    {
        $result = $this->mmam->get_self_court_dept();
        echo json_encode($result);
    }

    /**
     * 获取所有法院部门
     *
     * @return    string    法院部门列表
     */
    public function GetALLCourtDept()
    {
        $result = $this->mmam->get_all_court_dept();
        echo json_encode($result);
    }

    public function Submit($id)
    {
        $meeting['id'] = $id;
        $data['user_name'] = $_SESSION['user_name'];
        $data['court_name'] = $_SESSION['court_name'];
        $data['department_name'] = $_SESSION['department_name'];
		$this->load->view('header', $data);
		$this->load->view('meeting_submit_view', $meeting);
        $this->load->view('footer');
    }

    /**
     * 读签到二维码
     *
     * @param    string $code    二维码字符串
     * @param    string $sId    会议申请id
     * @return    string    签到是否成功
     */
    public function ReadCode()
    {
        //指定参会人员或报名人员二维码格式：S|sId|email。无需报名人员二维码格式：N|email
        $code = trim($this->input->post('code'));
        $code = explode('|', $code);
        if ($code[0] == 'S')
        {
            if (trim($this->input->post('sId')) != $code[1])
            {
                $data['status'] = -2;
                $data['result'] = '该二维码不是本次会议签到二维码！';
            }
            else
            {
                $data = $this->mmam->read_code($code[0], $code[1], $code[2]);
            }
        }
        else if ($code[0] == 'N')
        {
            $data = $this->mmam->read_code($code[0], trim($this->input->post('sId')), $code[1]);
        }
        else
        {
            $data['status'] = 0;
            $data['result'] = '该二维码不合法！';
        }
        echo json_encode($data);
    }

    /**
     * 大屏展示签到情况
     *
     * @param    string $s_id    会议申请id
     * @return   展示页面
     */
    public function LedShowSingIn($s_id)
    {
        $data = $this->mmam->led_sign_in($s_id);
        $data['user_name'] = $_SESSION['user_name'];
        $data['court_name'] = $_SESSION['court_name'];
        $data['department_name'] = $_SESSION['department_name'];
		$this->load->view('header', $data);
		$this->load->view('led_sign_in_view');
        $this->load->view('footer');
    }

    public function Notify_Go()
    {
        $this->load->library('cremind');
        $this->load->library('cgetname');
        $data = $this->cgetname->get_all_dept_name_and_id($_SESSION['department_id']);
        $result = $this->cremind->SendNotify($this->input->post("sId"), $_SESSION['user_nwid'], $data['dept_id_str'], $data['dept_name_str']);
        echo json_encode($result);
    }

}
