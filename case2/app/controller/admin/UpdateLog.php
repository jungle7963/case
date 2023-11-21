<?php
declare (strict_types=1);

namespace app\controller\admin;

use app\controller\admin\Base;
use app\traits\ControllerTrait;
use think\annotation\route\Group;
use think\annotation\Route;
use think\facade\Config;
use think\facade\Db;

/**
 * 管理员登陆日志
 * Class Admin
 * @package app\controller\admin
 * @author  2066362155@qq.com
 * @Group("admin/updatelog")
 */
class UpdateLog extends Base
{
    //服务，带命名空间
    public static $service = 'app\service\UpdateLogService';
    //验证器名称
    public static $validateName = 'Log';
    //保存验证场景
    public static $validateScene = '';
    //状态变更允许字段,格式 字段名：允许值
    public static $enableField = [];

    use ControllerTrait;

    //查询后置处理
    public function afterIndex($data)
    {
        $data = json_decode(json_encode($data),true);
        if (!empty($data)) {
            if ($data[0]['version'] != Config::get('database.connections.mysql.version')){
                $data[0]['btn_version'] = '请更新数据库版本';
            }else{
                $data[0]['btn_version'] = '已是最新版本';
            }
        }else{
            $u = new Admin($this->app);
            $user = $u->getuser();
            $y = new Youtube($this->app);
            $d = $y->accessProtected($user,'data');
            $username = $d['data']['username'];
            $roles = $d['data']['group'];
            $time = time();
            $s = [
                'update_content' => Config::get('database.connections.mysql.version'),
                'username' => $username,
                'roles' => $roles,
                'version' => Config::get('database.connections.mysql.version'),
                'create_time' => $time
            ];
            Db::name('update_log')->insert($s);
        }
        return $data;
    }
}
