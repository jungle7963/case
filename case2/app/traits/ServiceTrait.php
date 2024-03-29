<?php
declare (strict_types=1);

namespace app\traits;

use \think\facade\Snowflake;
use \think\helper\Arr;
use \think\helper\Str;

/**
 * 逻辑服务公共方法
 * Trait ServiceTrait
 * @package app\traits
 * @author  2066362155@qq.com
 */
trait ServiceTrait
{
    /**
     * 通过ID获取信息
     * @param $id
     * @param $field 获取字段
     * @param $where 附加条件
     * @return mixed
     */
    public static function getInfoById($id, $field = [], $where = true)
    {
        return self::$repository::getInfoById($id, $field, $where);
    }

    /**
     * 通过name获取信息
     * @param $name
     * @param $field 获取字段
     * @param $where 附加条件
     * @return mixed
     */
    public static function getInfoByName($name, $field = [], $where = true)
    {
        return self::$repository::getInfoByName($name, $field, $where);
    }

    /**
     * 获取列表,分頁
     * @param bool $where 查询条件
     * @param array $myorder 排序
     * @param int $page 页码
     * @param int $psize 分页大小
     * @param array $field 获取字段
     * @return mixed
     */
    public static function getLists($where = true, $myorder = ['id' => 'desc'], $page = 1, $psize = 20, $field = [])
    {
        return self::$repository::getLists($where, $myorder, $page, $psize, $field);
    }

    /**
     * 获取全部列表
     * @param bool $where 查询条件
     * @param array $myorder 排序
     * @param array $field 获取字段
     * @return mixed
     */
    public static function getListsAll($where = true, $myorder = ['id' => 'desc'], $field = [])
    {
        return self::$repository::getListsAll($where, $myorder, $field);
    }

    /**
     * 查询数量
     * @param bool $where 查询条件
     * @return mixed
     */
    public static function getTotal($where = true)
    {
        return self::$repository::getTotal($where);
    }

    /**
     * 保存
     * @param $data 数据
     * @param $id 主键id
     * @return bool|int
     */
    public static function save($data, $id = 0)
    {
        $m = substr(self::$repository,15);
        $table = str_ireplace('Repository','',$m);
        if ($id) {
            $result = self::$repository::edit($id, $data);
            $operate = "修改了 ".$table." 表中id为 ".json_encode($id)." 的数据";
        } else {
            if (!isset($data['id']) || !$data['id']) {
                $data['id'] = Snowflake::generate();
            }
            $id     = $data['id'];
            $result = self::$repository::add($data);
            $operate = "添加了 ".$table." 中id为 ".json_encode($id)." 的数据 ";
        }
        if ($result) {
            if (array_keys($data) != ["login_time"] && array_keys($data) != ["uid","username","roles","login_time","login_ip","id"]){
                event('OperateLog', [$operate]);
            }
            return $id;
        } else {
            return false;
        }
    }

    /**
     * 删除
     * @param int/array $id
     * @return int
     */
    public static function del($id)
    {
        if (self::$repository::del($id)){
            $m = substr(self::$repository,15);
            $table = str_ireplace('Repository','',$m);
            $operate = "删除了 ".$table." 中id为 ".json_encode($id)." 的数据 ";
            event('OperateLog', [$operate]);
        }
        return self::$repository::del($id);
    }
}