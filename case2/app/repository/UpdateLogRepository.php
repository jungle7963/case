<?php
declare (strict_types=1);
namespace app\repository;

use app\traits\RepositoryTrait;

/**
 * 管理员登陆日志
 * Class UpdateLogRepository
 * @package app\repository
 * @author  2066362155@qq.com
 */
class UpdateLogRepository
{
    //模型，带命名空间
    public static $model = 'app\model\UpdateLog';
    //模型主键
    public static $pk = 'id';
    //name字段名称
    public static $name = 'uid';

    use RepositoryTrait;

}
