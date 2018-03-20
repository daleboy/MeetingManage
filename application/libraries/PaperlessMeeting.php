<?php
class PaperlessMeeting
{
    private $_url;

    public function __construct()
    {
        $this->_url = 'http://147.1.19.129:2029/'; //用户/密码：lenovo/12345678
        //$this->_url = 'http://147.1.7.91:2029/';
    }

    private function PostData($cmd, $postData)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_url . $cmd);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 3000);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * 添加新会议
     *
     * @param [type] $meetingTitle 会议标题
     * @return 如果失败，返回具体错误信息。如果成功，返回：meetingInfoOK id=新建的会议ID
     */
    public function NewMeeting($meetingTitle)
    {
        $postData = "action=add&id=0&clonemeetingid=0&title={$meetingTitle}&chairman=&assist=&service=";
        $result = $this->PostData('MeetingInfo', $postData);
        return intval(substr($result, strpos($result, 'id=') + 3));
    }

    /**
     * 删除会议
     *
     * @param [type] $meetingId 要删除的会议ID
     * @return 如果失败，返回具体错误信息。 如果成功，返回：meetingInfoOK
     */
    public function DelMeeting($meetingId)
    {
        $postDataArr = array(
            'action' => 'del',
            'id' => $meetingId
        );
        $result = $this->PostData('meetingInfo', http_build_query($postDataArr));
        return $result;
    }

    /**
     * 添加会议安排
     *
     * @param [type] $meetingId 上一步添加的无纸化会议id
     * @param [type] $roomId 无纸化会议的场所id
     * @param [type] $startTime 开始时间
     * @param [type] $endTime 结束时间
     * @return 如果失败，返回具体错误信息。如果成功，返回：MeetingPlanOK id=新增的会议安排id
     */
    public function NewMeetingSchedule($meetingId, $roomId, $startTime, $endTime)
    {
        $postData = "action=add&id=0&meetingid={$meetingId}&meetingroomid={$roomId}&starttime={$startTime}&endtime={$endTime}";
        $result = $this->PostData('MeetingPlan', $postData);
        return $result;
    }

    /**
     * 修改会议安排
     *
     * @param [type] $meetingId 会议ID
     * @param [type] $roomId 会议室ID
     * @param [type] $startTime 开始时间
     * @param [type] $endTime 结束时间
     * @param integer $id 要修改的会议安排ID（可不填）
     * @return 如果失败，返回具体错误信息。 如果成功，返回：MeetingPlanOK
     */
    public function ModifyMeetingSchedule($meetingId, $roomId, $startTime, $endTime, $id = 0)
    {
        $postData = "action=edit&id=0&meetingid={$meetingId}&meetingroomid={$roomId}&starttime={$startTime}&endtime={$endTime}";
        $result = $this->PostData('MeetingPlan', $postData);
        return $result;
    }

    /**
     * 删除会议安排
     *
     * @param [type] $id 要删除的会议安排ID
     * @return void
     */
    public function DelMeetingSchedule($id)
    {
        $postDataArr = array(
            'action' => 'del',
            'id' => $id
        );
        $result = $this->PostData('MeetingPlan', http_build_query($postDataArr));
        return $result;
    }

    /**
     * 添加与会者
     *
     * @param [type] $meetingId 会议ID
     * @param [type] $email 用户在OA系统中的id
     * @param [type] $name 姓名
     * @param [type] $pwd 密码(为用户输入密码的MD5，比如：本系统对原始密码“abc123”取MD5后的值为“e99a18c428cb38d5f260853678922e03”)
     * @param [type] $department 单位
     * @param string $status 职位
     * @param string $limit 权限代码
     * @return 如果失败，返回具体错误信息。 如果成功，返回：doPeopleInfoOK id=新增的用户id
     */
    public function AddParticipants($meetingId, $email, $name, $pwd, $department, $status = '', $limit = '1,2,3,4')
    {
        $postData = "action=add&id=0&meetingid={$meetingId}&user={$name}&pass={$pwd}&department={$department}&status={$status}&limit={$limit}&userid_oa={$email}";
        $result = $this->PostData('PeopleInfo', $postData);
        return $result;
    }

    /**
     * 编辑与会者
     *
     * @param [type] $meetingId 会议ID
     * @param [type] $email 用户在OA系统中的id
     * @param [type] $name 姓名
     * @param [type] $pwd 密码
     * @param [type] $department 单位
     * @param string $status 职位
     * @param integer $seatNum 座位号
     * @param string $limit 权限代码
     * @return 如果失败，返回具体错误信息。 如果成功，返回：doPeopleInfoOK
     */
    public function ModifyParticipants($meetingId, $email, $name, $pwd, $department, $status = '', $seatNum = 0, $limit = '1,2,3,4')
    {
        $postData = "action=edit&userid_oa={$email}&meetingid={$meetingId}&user={$name}&pass={$pwd}&department={$department}&status={$status}&seatnum={$seatNum}&limit={$limit}";
        $result = $this->PostData('PeopleInfo', $postData);
        return $result;
    }

    /**
     * 删除与会者
     *
     * @param [type] $meetingId 会议ID
     * @param [type] $email 用户在OA系统中的id
     * @return 如果失败，返回具体错误信息。 如果成功，返回：doPeopleInfoOK
     */
    public function DelParticipants($meetingId, $email)
    {
        $postData = "action=del&meetingid={$meetingId}&userid_oa={$email}";
        $result = $this->PostData('PeopleInfo', $postData);
        return $result;
    }

    /**
     * 删除所有与会者
     *
     * @param [type] $meetingId 会议id
     * @return 如果失败，返回具体错误信息。 如果成功，返回：doPeopleInfoOK
     */
    public function DelAllParticipants($meetingId)
    {
        $postDataArr = array(
            'action' => 'clear',
            'meetingid' => $meetingId
        );
        $result = $this->PostData('PeopleInfo', http_build_query($postDataArr));
        return $result;
    }

    /**
     * 获取与会者列表
     *
     * @param [type] $meetingId 会议ID
     * @return 如果失败，返回具体错误信息。如果成功，返回类似如下数据：getPeoplesListOk
     * data:
     * 与会者数据1
     * 与会者数据2
     * 与会者数据3
     * ……
     * 与会者数据n
     * 
     * 其中每一条“与会者数据”格式如下：
     * 与会者ID|||姓名|||密码|||单位|||职位|||座位号|||权限代码|||用户在OA系统中的id
     * 
     * 其中“权限代码”由数字代表
     * （1-查看投票结果、2-中控、3-发起同步演示、4-主持功能、5-免签到进入会议）
     * 比如某与会者有 中控、发起同步演示、主持 三项权限，则其权限代码为“2,3,4”
     */
    public function GetParticipantsList($meetingId)
    {
        $postDataArr = array(
            'meetingid' => $meetingId
        );
        $result = $this->PostData('getPeoplesListForMange', http_build_query($postDataArr));
        return $result;
    }

    public function UploadUrlFile($userName, $fileUrl, $filePath, $meetingId)
    {
        $postData = "creator={$userName}&limitids=&url={$fileUrl}&file=Meeting\{$filePath}&meetingid={$meetingId}";
        $result = $this->PostData('UploadUrlFile', $postData);
        return $result;
    }

    public function GetMeetingPlan()
    {
        $postDataArr = array(
            'action' => 'getlist'
        );
        $result = $this->PostData('MeetingPlan', http_build_query($postDataArr));
        return $result;
    }

    public function GetMeetingRoomList()
    {
        $postDataArr = array(
            'action' => 'loadlist'
        );
        $result = $this->PostData('MeetingRoom', http_build_query($postDataArr));
        return $result;
    }
}