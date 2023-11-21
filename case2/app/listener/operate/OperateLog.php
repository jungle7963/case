<?php
declare (strict_types=1);

namespace app\listener\operate;

use app\controller\admin\Admin;
use app\controller\admin\Base;
use app\controller\admin\Youtube;
use app\service\AdminService;
use app\service\LoginLogService;
use think\facade\Db;

/**
 * 用户登录事件
 * Class OperateLog
 * @package app\listener\operate
 * @author 2066362155@qq.com
 */
class OperateLog extends Base
{
    /**
     * 事件监听处理
     *
     * @return mixed
     */
    public function handle($event)
    {
        $admin = new Admin($this->app);
        $user = $admin->getuser();
        $y = new Youtube($this->app);
        $data = $y->accessProtected($user,'data');
        $username = $data['data']['username'];
        $roles = $data['data']['group'];
        list($operate_content) = $event;
        $time = time();
        Db::name('operate_log')->insert([
            'operate_content' => $operate_content,
            'username' => $username,
            'roles' => $roles,
            'create_time' => $time
        ]);
    }

}
