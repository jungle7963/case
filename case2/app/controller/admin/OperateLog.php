<?php
declare (strict_types=1);

namespace app\controller\admin;

use app\controller\admin\Base;
use app\traits\ControllerTrait;
use think\annotation\route\Group;
use think\annotation\Route;

/**
 * 管理员登陆日志
 * Class Admin
 * @package app\controller\admin
 * @author  2066362155@qq.com
 * @Group("admin/operatelog")
 */
class OperateLog extends Base
{
    //服务，带命名空间
    public static $service = 'app\service\OperateLogService';
    //验证器名称
    public static $validateName = 'Log';
    //保存验证场景
    public static $validateScene = '';
    //状态变更允许字段,格式 字段名：允许值
    public static $enableField = [];

    use ControllerTrait;


    //查询条件前置处理
    public function beforeIndex()
    {
        //搜索参数
        $username   = input('username', '', 'trim');
        $roles   = input('roles', '', 'trim');
        $start_time = input('start_time', '', 'strtotime');
        $end_time   = input('end_time', '', 'strtotime');

        $where = true;
        if ($username) {
            $where .= " and username like '%" . $username . "%' ";
        }
        if ($roles) {
            $where .= " and roles like '%" . $roles . "%' ";
        }
        if ($start_time) {
            $where .= " and create_time >= " . $start_time . " ";
        }
        if ($end_time) {
            $where .= " and create_time <= " . $end_time . " ";
        }

        return [$where, []];
    }
}
