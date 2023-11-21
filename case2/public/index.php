<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
namespace think;

//引入composer
require __DIR__ . '/../vendor/autoload.php';

//通过Ioc容器将HTTP类实例出来
// 执行HTTP应用并响应
$http = (new App())->http;

//执行HTTP类中的run类方法 并返回一个response对象
$response = $http->run();

//执行response对象中的send类方法  该方法是处理并输出http状态码以及页面内容
$response->send();

//执行response对象中的end方法
$http->end($response);

