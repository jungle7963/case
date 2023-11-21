<?php
declare (strict_types=1);

namespace app\controller\admin;

use app\controller\admin\Base;
use app\traits\ControllerTrait;
use think\annotation\route\Group;
use think\annotation\Route;

/**
 * 文章分类管理
 * Class Information
 * @package app\controller\admin
 * @author  2066362155@qq.com
 * @Group("admin/information")
 */
class Information extends Base
{
    //服务，带命名空间
    public static $service = 'app\service\InformationService';
    //验证器名称
    public static $validateName = 'Information';
    //保存验证场景
    public static $validateScene = 'save';
    //状态变更允许字段,格式 字段名：允许值
    public static $enableField = ['status' => [0, 1]];

    use ControllerTrait;

    //查询条件前置处理
    public function beforeIndex()
    {
        //搜索参数
        $status     = input('status', -1, 'intval');
        $password   = input('password', '', 'trim');
        $username   = input('username', '', 'trim');
        $interface_name        = input('interface_name', '', 'trim');

        $where = true;
        if ($username) {
            $where .= " and username like '%" . $username . "%' ";
        }
        if ($password) {
            $where .= " and password like '%" . $password . "%' ";
        }
        if ($interface_name) {
            $where .= " and interface_name like '%" . $interface_name . "%' ";
        }
        if ($status != -1) {
            $where .= " and status = " . $status;
        }


        return [$where, []];
    }


    //保存前置处理
    public function beforeSave($id)
    {
        //接收数据
        $data = [
            'username'      => input('username', '', 'trim'),
            'password'      => input('password', '', 'trim'),
            'interface_name'      => input('interface_name', '', 'trim'),
            'url'           => input('url', '', 'trim'),
            'status'        => input('status', 0, 'int'),
        ];

        return $data;
    }
}
