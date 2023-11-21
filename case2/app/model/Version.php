<?php
declare (strict_types=1);

namespace app\model;

use think\Model;
use think\model\concern\SoftDelete;
use app\traits\ModelTrait;

/**
 * 管理员
 * Class Version
 * @package app\model
 * @author  2066362155@qq.com
 */
class Version extends Model
{
    //据输出显示的属性
    public static $showField = ['id', 'version'];

    //查询类型转换, 与Model 中的type类型转化功能相同，只是新增字符串类型
    protected $selectType = [
        'id'       => 'string',
    ];

    use ModelTrait;
}



