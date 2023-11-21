<?php

namespace app\controller;

use app\BaseController;
use app\service\UserService;
use think\annotation\route\Group;
use think\annotation\Route;
use think\cache\driver\Redis;
use think\facade\Config;

/**
 * Class Index
 * @package app\controller
 * @author  2066362155@qq.com
 * @Group("/")
 */
class Index extends BaseController
{
    /**
     * @Route("index", method="GET")
     */
    public function index()
    {

    }

    /**
     * @param  string $name 数据名称
     * @return mixed
     * @Route("hello", method="POST")
     */
    public function hello()
    {
        $file = request()->file();

        print_r($file);

    }
}
