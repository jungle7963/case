<?php
declare (strict_types=1);

namespace app\validate;

use think\Validate;

/**
 * 文章分类
 * Class Content
 * @package app\validate
 * @author  2066362155@qq.com
 * @date 2019-11-27
 */
class Information extends Validate
{
    //验证规则
    protected $rule = [
        'username'   => ['require'],
        'password'   => ['require'],
        'url'        => ['require'],
    ];

    //提示信息
    protected $message = [
        'username'   => '用户名必填',
        'password'   => '密码必填',
        'url'        => 'wordpress接口必填',
    ];
    //验证场景
    protected $scene = [
        'save'   => ['username', 'password', 'url'],
    ];

}
