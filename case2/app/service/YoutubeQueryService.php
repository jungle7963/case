<?php
declare (strict_types=1);
namespace app\service;

use app\traits\ServiceTrait;

/**
 * 文章
 * Class YoutubeQueryService
 * @package app\service
 * @author  2066362155@qq.com
 */
class YoutubeQueryService
{
    //仓库，带命名空间
    public static $repository = 'app\repository\YoutubeQueryRepository';

    use ServiceTrait;

}
