<?php
declare (strict_types=1);

namespace app\repository;

use app\traits\RepositoryTrait;

/**
 * 文章栏目
 * Class WordpressRepository
 * @package app\repository
 * @author  2066362155@qq.com
 */
class WordpressRepository
{
//模型，带命名空间
    public static $model = 'app\model\Wordpress';
    //模型主键
    public static $pk = 'id';

    use RepositoryTrait;
}
