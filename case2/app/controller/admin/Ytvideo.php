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
 * Class Ytvideo
 * @package app\controller\admin
 * @author  2066362155@qq.com
 * @Group("admin/ytvideo")
 */
class Ytvideo extends Base
{

    public static $service = 'app\service\YoutubeQueryService';
    public static $AuthRuleService = 'app\service\AuthRuleService';
    public static $validateName = 'Youtube';
    public static $validateScene = 'save';
    public static $enableField = ['status' => [0, 1]];

    use ControllerTrait;

    public function beforeIndex()
    {
        //搜索参数
        $link      = input('link', '', 'trim');
        $country     = input('country', '', 'trim');
        $channelId     = input('channelId', '', 'trim');
        $start_time = input('start_time', '', 'strtotime');
        $end_time   = input('end_time', '', 'strtotime');

        $where = true;

        if ($link) {
            $where .= " and link like '%" . $link . "%' ";
        }
        if ($country) {
            $where .= " and country like '%" . $country . "%' ";
        }
        if ($channelId) {
            $where .= " and channelId like '%" . $channelId . "%' ";
        }
        if ($start_time) {
            $where .= " and create_time >= " . $start_time . " ";
        }
        if ($end_time) {
            $where .= " and create_time <= " . $end_time . " ";
        }

        return [$where, []];
    }

    public function beforeSave($id)
    {
        //接收数据
        $data = [
            'title'   => input('title', '', 'trim'),
            'description'     => input('description', '', 'trim'),
            'information_id'     => input('information_id', '', 'int'),
            'lang'     => input('lang', '', 'trim'),
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

        $where = "1 and id = 47";
        $lists = self::$AuthRuleService::getLists($where);
        $lists = json_decode(json_encode($lists),true);
        if ($lists[0]['release_status'] != 1) return json_error(10033);

        $description = input('description', '', 'trim');
        $youtube = new Youtube($this->app);
        $f = $youtube->announce($description);
        if (!empty($f)) return json_error(10016,$f);
        $this->save();
        return json_ok([],10015);
    }
}
