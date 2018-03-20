<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CCheckAuthority {
    
    public function __construct()
    {
        
    }
    
    //$user_function:用户权限拥有的所有功能； $need_function:需要的功能
    public function check_authority($user_function, $need_function)
    {
        if (strpos($user_function, $need_function.',') !== false)
            return TRUE;
        else
            return FALSE;
    }
}