<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Meeting_safeguards_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function getSafeguards($sId)
    {
        $query = $this->db->select('s_schedule.start_time, s_schedule.end_time, s_schedule.court_fjm, s_system.content, s_room.r_id')
            ->from('s_schedule')
            ->join('s_system', 's_schedule.s_id = s_system.s_id', 'left')
            ->join('s_room', 's_schedule.s_id = s_room.s_id', 'left')
            ->where('s_schedule.s_id', $sId)
            ->get();
        $row = $query->row_array();
        $data = array();
        if (isset($row)) {
            //获得技术支持id及对应名称
            $technologyName = $this->getTechnologyTypeName($row['court_fjm']);
            list($systemJson) = array_values(json_decode($row['content']));
            //技术支持类型
            if (!empty($systemJson->g)) {
                $tTypeArr = explode(',', $systemJson->g);
                foreach ($tTypeArr as $tVal) {
                    $tValArr = explode('|', $tVal);
                    $data['technologyType'][] = $technologyName[$tValArr[0]] . '：' . (isset($tValArr[1]) ? '&emsp;' . $tValArr[1] : '&emsp;是');
                }
            }
            //承办部门、联系人、联系电话
            $data['meetingCbbm'] = '';
            $data['meetingLxr'] = '';
            $data['meetingLxdh'] = '';
            if (!empty($systemJson->h)) {
                $cbbmArr = explode('|', $systemJson->h);
                $data['meetingCbbm'] = $cbbmArr[0];
                $data['meetingLxr'] = $cbbmArr[1];
                $data['meetingLxdh'] = $cbbmArr[2];
            }
            //是否发言
            $data['whetherSpeak1'] = '最高法会议广西端是否需要发言：';
            $data['whetherSpeak2'] = '自治区级会议法院端是否需要发言：';
            $data['whetherSpeak3'] = '全区法院会议中、基层院是否发言：';
            if (!empty($systemJson->i)) {
                $whetherSpeakArr = explode(',', $systemJson->i);
                $data['whetherSpeak1'] .= in_array('1', $whetherSpeakArr) ? '&emsp;是' : '&emsp;否';
                $data['whetherSpeak2'] .= in_array('2', $whetherSpeakArr) ? '&emsp;是' : '&emsp;否';
                $data['whetherSpeak3'] .= in_array('3', $whetherSpeakArr) ? '&emsp;是' : '&emsp;否';
            }
            //发言单位
            $data['speakDept'] = empty($systemJson->j) ? '' : $systemJson->j;
            //参加会议范围
            $data['meetingRange'] = array();
            if (!empty($systemJson->k)) {
                $meetingRangeArr = explode(',', $systemJson->k);
                if (in_array('1', $meetingRangeArr)) {
                    $data['meetingRange'][] = '高院';
                }
                if (in_array('2', $meetingRangeArr)) {
                    $data['meetingRange'][] = '中院';
                }
                if (in_array('3', $meetingRangeArr)) {
                    $data['meetingRange'][] = '基层院';
                }
                if (in_array('4', $meetingRangeArr)) {
                    $data['meetingRange'][] = '其他';
                }
            }
            //会议名称
            $data['meetingName'] = $systemJson->a;
            //会议类型
            $meetingType = $this->getMeetingTypeName($row['court_fjm']);
            $data['meetingType'] = isset($meetingType[$systemJson->b]) ? $meetingType[$systemJson->b] : '';
            //会议时间
            $data['meetingTime'] = date('Y年m月d日H时i分', $row['start_time']) . ' 至 ' . date('Y年m月d日H时i分', $row['end_time']);
            //会议地点
            $this->load->library('CGetRoomData');
            $roomData = $this->cgetroomdata->get_room_name_and_type($row['r_id']);
            $data['meetingAddress'] = isset($roomData['name']) ? $roomData['name'] . '(' . $roomData['address'] . ')' : '';
            return $data;
        } else {
            return null;
        }
    }

    private function getTechnologyTypeName($fjm)
    {
        $query = $this->db->select('type_id, type_name')
            ->from('s_technology_type')
            ->where('fjm', $fjm)
            ->where('del_flag', 0)
            ->get();
        $result = $query->result_array();
        return array_combine(array_column($result, 'type_id'), array_column($result, 'type_name'));
    }

    private function getMeetingTypeName($fjm)
    {
        $query = $this->db->query("SELECT type_id, type_name FROM s_meeting_type WHERE (fjm = '{$fjm}' OR fjm IS NULL) AND del_flag = 0");
        $result = $query->result_array();
        return array_combine(array_column($result, 'type_id'), array_column($result, 'type_name'));
    }
}
