<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CGetRoomData extends CI_Model {

    private $room_conn;

    public function __construct()
    {
        parent::__construct();
        $this->room_conn = $this->load->database('room', TRUE);
    }

    private function get_room_type_name($room_type)
    {
        if ($room_type == 1)
            return "法庭";
        else if ($room_type == 3)
            return "调解室"; 
        else if ($room_type == 4)
            return "听证室";
        else if ($room_type == 5)
            return "信访室";
        else if ($room_type == 6)
            return "接访室";
        else if ($room_type == 7)
            return "会议室";
        else if ($room_type == 10)
            return "提讯室";
        else
            return "";
    }

    public function get_room_name_and_type($r_id)
    {
        $sql = "SELECT name, type, ssdw, dd FROM paiqi_field WHERE id = ?";
        $query = $this->room_conn->query($sql, array($r_id));
        $res = $query->row();
        $result['name'] = $res->name;
        $result['type'] = $this->get_room_type_name($res->type);
        $result['ssdw'] = $res->ssdw;
        $result['address'] = $res->dd;
        return $result;
    }

    public function get_ensure_person($room_id, $type)
    {
        $ensure_person_email = array();
        $sql = "SELECT tableId FROM paiqi_bjuser WHERE field = ? AND type = ?";
        $query = $this->room_conn->query($sql, array($room_id, $type));
        foreach ($query->result() as $row)
        {
            $ensure_person_email[] = $row->tableId;
        }
        return $ensure_person_email;
    }

    public function get_paperless_room_id($r_id)
    {
        $query = $this->room_conn->select('json')
            ->from('paiqi_joggle')
            ->where('csid', $r_id)
            ->get();
        $row = $query->row();
        if (isset($row))
        {
            $room_id = json_decode($row->json);
            $room_id = $room_id->roomId;
            return intval($room_id);
        }
        else
        {
            return 0;
        }
    }

    /**
     * 判断是否无纸化会议室
     *
     * @param [type] $r_id
     * @return bool TRUE是，FALSE否
     */
    public function check_is_paperless_room($r_id)
    {
        $query = $this->room_conn->select('wzh')
            ->from('paiqi_field')
            ->where('id', $r_id)
            ->get();
        $row = $query->row();
        if (isset($row))
        {
            if ($row->wzh == 1)
                return TRUE;
            else
                return FALSE;
        }
        else
        {
            return FALSE;
        }
    } 
}