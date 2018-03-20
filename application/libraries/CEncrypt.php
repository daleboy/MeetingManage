<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CEncrypt extends CI_Model {

    private $portal;

    public function __construct()
    {
        parent::__construct();
    }

    //得到陈理加密方法
    public function cl_encrypt($test)
    {
/*        $test = (int)$test +50;
        $test = $test*3;*/
        $test = chr($test);
        $test = base64_encode($test);
        $test = urlencode($test);
        return $test;
    }

    //得到陈理解密方法
    public function cl_decrypt($test)
    {
        $test = urldecode($test);
        $test = base64_decode($test);
        $test = (int)ord($test);
/*        $test = $test/3;
        $test = $test-50;*/
        return $test;
    }

}