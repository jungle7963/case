<?php
declare (strict_types=1);

namespace app\model;

use think\Model;
use app\traits\ModelTrait;

/**
 * 文章
 * Class YoutubeChannel
 * @package app\model
 * @author  2066362155@qq.com
 */
class YoutubeChannel extends Model
{
    //时间字段显示格式
    protected $dateFormat = 'Y-m-d H:i:s';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    //只读字段，不允许被更改
    protected $readonly = [];
    //数据输出隐藏的属性
    protected $hidden = [];

    //据输出显示的属性
    public static $showField = ['id', 'link', 'viewCount', 'subscriberCount', 'videoCount', 'channelId', 'channelTitle', 'country', 'publishedAt', 'img', 'create_time'];

    //查询类型转换, 与Model 中的type类型转化功能相同，只是新增字符串类型
    protected $selectType = [
        'id'        => 'string',
        'subscriberCount'        => 'string',
        'viewCount'        => 'string',
        'videoCount'        => 'string',
    ];

    use ModelTrait;
}
