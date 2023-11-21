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
 * @Group("admin/channel")
 */
class Channel extends Base
{

    public static $service = 'app\service\YoutubeChannelService';
    public static $validateName = 'Youtube';
    public static $validateScene = 'save';
    public static $enableField = ['status' => [0, 1]];

    use ControllerTrait;

    public function beforeIndex()
    {
        //搜索参数
        $channelId = input('channelId', '', 'trim');
        $country = input('country', '', 'trim');
        $start_time = input('start_time', '', 'strtotime');
        $end_time = input('end_time', '', 'strtotime');

        $where = true;

        if ($channelId) {
            $where .= " and channelId like '%" . $channelId . "%' ";
        }
        if ($country) {
            $where .= " and country like '%" . $country . "%' ";
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
