<?php
declare (strict_types=1);
namespace app\repository;

use app\traits\RepositoryTrait;

/**
 * 文章
 * Class YoutubeChannelRepository
 * @package app\repository
 * @author  2066362155@qq.com
 */
class YoutubeChannelRepository
{
    //模型，带命名空间
    public static $model = 'app\model\YoutubeChannel';
    //模型主键
    public static $pk = 'id';

    use RepositoryTrait;

}
