<?php
/**
 * User: Administrator
 */
namespace Cloud\Controller;
use Think\Controller;

class CloudshopController extends Controller {

    /**
     * 析构流函数
     */
    public function  __construct() {
        parent::__construct();
    }

    /**
     * 一元云购主页
     */
    public function index(){

        echo "一元云购主页";

    }



}
