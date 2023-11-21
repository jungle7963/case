<?php
declare (strict_types=1);

namespace app\controller\admin;

use app\controller\admin\Base;
use app\controller\Index;
use app\traits\ControllerTrait;
use http\Client;
use think\annotation\route\Group;
use think\annotation\Route;



/**
 * youtube数据获取
 * Class Wordpress
 * @package app\controller\admin
 * @author  2066362155@qq.com
 * @Group("admin/wordpress")
 */
class Wordpress extends Base
{

    public static $service = 'app\service\WordpressService';
    public static $AuthRuleService = 'app\service\AuthRuleService';

    use ControllerTrait;

    public function beforeIndex()
    {
        $admin = new Admin($this->app);
        $user = $admin->getuser();
        $y = new Youtube($this->app);
        $data = $y->accessProtected($user,'data');
        $where = true;

        //搜索参数
        $link      = input('link', '', 'trim');
        $title     = input('title', '', 'trim');
        $wordpress_url  = input('wordpress_url', '', 'trim');
        $start_time = input('start_time', '', 'strtotime');
        $end_time   = input('end_time', '', 'strtotime');
        $username    = input('username', '', 'trim');
        if ($data['data']['group_id'] != 1)  $username = $data['data']['username'];

        if ($link) {
            $where .= " and link like '%" . $link . "%' ";
        }
        if ($title) {
            $where .= " and title like '%" . $title . "%' ";
        }
        if ($wordpress_url) {
            $where .= " and wordpress_url like '%" . $wordpress_url . "%' ";
        }
        if ($start_time) {
            $where .= " and create_time >= " . $start_time . " ";
        }
        if ($end_time) {
            $where .= " and create_time <= " . $end_time . " ";
        }
        if ($username) {
            $where .= " and username = '" . $username . "'";
        }

        return [$where, []];
    }

    public function beforeSave($id)
    {
        //接收数据
        $data = [
            'link'    => input('link', '', 'trim'),
            'title'   => input('title', '', 'trim'),
            'description'   => input('title', '', 'trim'),
            'url'     => input('url', '', 'trim'),
        ];
        return $data;
    }

    /**
     * 发布到wordpress
     * @Route("release", method="POST")
     */
    public function release()
    {
        $where = "1 and id = 45";
        $lists = self::$AuthRuleService::getLists($where);
        $lists = json_decode(json_encode($lists),true);
        if ($lists[0]['release_status'] != 1) return json_error(10033);

        $where = "1 and id = 51";
        $lists = self::$AuthRuleService::getLists($where);
        $lists = json_decode(json_encode($lists),true);
        if ($lists[0]['release_status'] != 1) return json_error(10033);

        $description = input('description', '', 'trim');
        $youtube = new Youtube($this->app);
        $f = $youtube->announce($description);
        if (!empty($f)) return json_error(10016,$f);
        return json_ok([],10015);
    }
}
