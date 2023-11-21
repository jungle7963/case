<?php

declare (strict_types=1);
namespace app\service;

use app\traits\ServiceTrait;

/**
 * 文章栏目
 * Class InformationService
 * @package app\service
 * @author  2066362155@qq.com
 */

class InformationService
{
    //仓库，带命名空间
    public static $repository = 'app\repository\InformationRepository';

    use ServiceTrait;
}
