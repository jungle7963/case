<?php
declare (strict_types=1);

namespace app\controller\admin;

use app\controller\admin\Base;
use app\model\Version;
use app\service\AuthGroupService;
use app\service\AuthRuleService;
use app\traits\ControllerTrait;
use think\annotation\route\Group;
use think\annotation\Route;
use app\util\TreeUtil;
use think\facade\Config;
use think\facade\Db;
use think\facade\Env;

/**
 * 管理员管理
 * Class Admin
 * @package app\controller\admin
 * @author  2066362155@qq.com
 * @Group("admin/admin")
 */
class Admin extends Base
{
    //服务，带命名空间
    public static $service = 'app\service\AdminService';
    public static $wordpressService = 'app\service\WordpressService';

    //验证器名称
    public static $validateName = 'Admin';
    //保存验证场景
    public static $validateScene = 'save';
    //状态变更允许字段,格式 字段名：允许值
    public static $enableField = ['is_enabled' => [0, 1],'is_release' => [0, 1]];

    use ControllerTrait;

    /**
     * 获取数据库版本
     * @Route("getversion", method="POST")
     */
    public function getVersion()
    {
         $version = Version::select();
         $version = json_decode(json_encode($version),true);
         if (empty($version)) {
             $v = new Version();
             $v->version = Config::get('database.connections.mysql.version');
             $v->save();

             $user = $this->getuser();
             $y = new Youtube($this->app);
             $data = $y->accessProtected($user,'data');
             $username = $data['data']['username'];
             $roles = $data['data']['group'];
             $time = time();
             Db::name('update_log')->insert([
                 'update_content' => Config::get('database.connections.mysql.version'),
                 'username' => $username,
                 'roles' => $roles,
                 'version' => Config::get('database.connections.mysql.version'),
                 'create_time' => $time
             ]);

             $version = Version::select();
             $version = json_decode(json_encode($version),true);
         }
         if ($version[0]['version'] != Config::get('database.connections.mysql.version')) {
             return json_ok([],10034);
         }
         return json_ok();
    }

    /**
     * 修改数据库版本
     * @Route("editVersion", method="POST")
     */
    public function editVersion()
    {
        $versions = Version::select();
        $versions = json_decode(json_encode($versions),true);
        $id = $versions[0]['id'];
        $oldVersion = $versions[0]['version'];
        $newVersion = Config::get('database.connections.mysql.version');
        $version = Version::find($id);
        $version->version = $newVersion;
        $s = $version->save();
        if ($s){
            $user = $this->getuser();
            $y = new Youtube($this->app);
            $data = $y->accessProtected($user,'data');
            $username = $data['data']['username'];
            $roles = $data['data']['group'];
            $update_content = $oldVersion. ' => ' .$newVersion;
            $time = time();
            Db::name('update_log')->insert([
                'update_content' => $update_content,
                'username' => $username,
                'roles' => $roles,
                'version' => $newVersion,
                'create_time' => $time
            ]);
            return json_ok([],10035);
        }
        return json_error(10036);
    }

    /**
     * 获取用户数、文章数
     * @Route("getcount", method="POST")
     */
    public function getCount()
    {
        $admin = self::$service::getListsAll();
        $admin = json_decode(json_encode($admin),true);
        $usernames = array_column($admin,'username');

        $lists = self::$wordpressService::getListsAll();
        $lists = json_decode(json_encode($lists),true);
        if (empty($lists)) {
            $result['usernameCount'] = count($admin);
            return json_ok($result);
        }
        foreach ($lists as &$list) {
            $list['create_time'] = substr($list['create_time'], 0, 10);
        }
        unset($list);
        $username = input('username', '', 'trim');
        if ($username == '') $username = $usernames[0];
        $create_times = array_unique(array_column($lists,'create_time'));
        asort($create_times);
        $times = array_values($create_times);

        $user = $this->getuser();
        $y = new Youtube($this->app);
        $data = $y->accessProtected($user,'data');
        if ($data['data']['group_id'] != 1) $username = $data['data']['username'];

        $sum = [];
        $sum1 = [];
        foreach ($times as $index => $time){
            $sum[$index] = 0;
            $sum1[$index] = 0;
            foreach ($lists as $list){
                if ($list['create_time'] == $time) $sum1[$index]++;
                if ($list['username'] == $username && $list['create_time'] == $time) $sum[$index]++;
            }
        }

        if ($data['data']['group_id'] != 1) {
            $result['times'] = array_values($create_times);
            $result['releaseCount'] = array_values($sum);
            $result['articleCount'] = array_sum($sum);
            return json_ok($result);
        }

        $result['times'] = array_values($create_times);
        $result['releaseCount'] = array_values($sum);
        $result['releaseCounts'] = array_values($sum1);
        $result['usernames'] = $usernames;
        $result['username'] = $username;
        $result['usernameCount'] = count($admin);
        $result['articleCount'] = count($lists);
        return json_ok($result);
    }

    /**
     * 获取登录用户信息,菜单支持三级
     * @Route("getuser", method="POST")
     */
    public function getuser()
    {
        $user['username']    = $this->user->username;
        $user['email']       = $this->user->email;
        $user['realname']    = $this->user->realname;
        $user['phone']       = $this->user->phone;
        $user['img']         = $this->user->img;
        $user['group_id']    = $this->user->group_id;
        $user['is_release']    = $this->user->is_release;
        $user['create_time'] = $this->user->create_time;
        $user['login_time']  = $this->user->login_time ? date('Y-m-d H:i:s', $this->user->login_time) : '';
        $group               = AuthGroupService::getInfoById($this->user->group_id);
        $user['group']       = $group['title'];

        $rules = AuthRuleService::getAuthByGroupId($this->user->group_id);
        $rules = TreeUtil::listToTreeMulti($rules, 0, 'id', 'pid', 'children');

        $routers = [];

        foreach ($rules as $v) {
            $temp = $this->getdata($v);
            foreach ($v['children'] as $vo) {
                $temp2=$this->getdata($vo);
                foreach ($vo['children'] as $vv){
                    $temp2['children'][]=$this->getdata($vv);
                }
                $temp['children'][] = $temp2;
            }
            $routers[] = $temp;
        }
        $user['access'] = $routers;

        return json_ok($user);
    }

    protected function getdata($data)
    {
        $temp              = [];
        $temp['path']      = $data['path'];
        $temp['component'] = $data['component'];
        $temp['name']      = $data['name'];
        if ($data['hidden'] > -1) {
            $temp['hidden'] = (boolean)$data['hidden'];
        }
        if ($data['always_show'] > -1) {
            $temp['alwaysShow'] = (boolean)$data['always_show'];
        }
        if ($data['redirect']) {
            $temp['redirect'] = $data['redirect'];
        }
        $temp['meta']['title'] = $data['title'];
        $temp['meta']['icon']  = $data['icon'];
        if ($data['no_cache'] > -1) {
            $temp['meta']['noCache'] = (boolean)$data['no_cache'];
        }

        return $temp;
    }

    //查询条件前置处理
    public function beforeIndex()
    {
        //搜索参数
        $is_enabled = input('is_enabled', -1, 'intval');
        $username   = input('username', '', 'trim');
        $phone      = input('phone', '', 'trim');
        $realname   = input('realname', '', 'trim');
        $start_time = input('start_time', '', 'strtotime');
        $end_time   = input('end_time', '', 'strtotime');

        $where = true;
        if ($username) {
            $where .= " and username like '%" . $username . "%' ";
        }
        if ($phone) {
            $where .= " and phone like '%" . $phone . "%' ";
        }
        if ($realname) {
            $where .= " and realname like '%" . $realname . "%' ";
        }
        if ($start_time) {
            $where .= " and login_time >= " . $start_time . " ";
        }
        if ($end_time) {
            $where .= " and login_time <= " . $end_time . " ";
        }
        if ($is_enabled != -1) {
            $where .= " and is_enabled = " . $is_enabled;
        }

        return [$where, []];
    }


    //保存前置处理
    public function beforeSave($id)
    {

		$id = input('id', '0', 'int');
        //接收数据
        $data = [
            'id'         => $id,
            'group_id'   => input('group_id', '', 'trim'),
            'username'   => input('username', '', 'trim'),
            'realname'   => input('realname', '', 'trim'),
            'img'        => input('img', '', 'trim'),
            'phone'      => input('phone', '', 'trim'),
            'email'      => input('email', '', 'trim'),
            'password'   => input('password', '', 'trim'),
            'is_enabled' => input('is_enabled', 0, 'int'),
            'is_release' => input('is_release', 0, 'int'),
        ];

        if ($id == 0) {
            $data['reg_ip'] = request()->ip();
        }
        if ($data['password']) {
            $data['password'] = encrypt_pass($data['password']);
        } else {
            unset($data['password']);
        }

        return $data;
    }

    /**
     * 修改
     * @Route("modify", method="POST")
     */
    public function modify()
    {
        //接收数据
        $data = [
            'realname'   => input('realname', '', 'trim'),
            'img'        => input('img', '', 'trim'),
            'phone'      => input('phone', '', 'trim'),
            'email'      => input('email', '', 'trim'),
            'password'   => input('password', '', 'trim'),
        ];

        if ($data['password']) {
            $data['password'] = encrypt_pass($data['password']);
        } else {
            unset($data['password']);
        }
        $validate = validate(self::$validateName);
        $result = $validate->scene('modify')->check($data);
        if (!$result) {
            $error = $validate->getError();
            return json_error($error);
        }

        $res = self::$service::save($data, $this->user->id);
        if ($res == false) {
            return json_error(10005);
        } else {
            return json_ok(['id' => strval($res)], 10006);
        }
    }

    /**
     * 获取配置
     * @Route("getConfig", method="POST")
     */
    public function getConfig()
    {
        $data['youtubeApi_key'] = Env::get('video.youtubeApi_key', '');
        $data['ffmpeg'] = Env::get('video.ffmpeg', '');
        $data['ffprobe'] = Env::get('video.ffprobe', '');
        $data['yt_dlp'] = Env::get('video.yt-dlp', '');
        $data['accessKeyId'] = Env::get('video.accessKeyId', '');
        $data['accessKeySecret'] = Env::get('video.accessKeySecret', '');
        $data['bucket'] = Env::get('video.bucket', '');
        return json_ok($data,10000);
    }

    /**
     * 修改配置
     * @Route("editConfig", method="POST")
     */
    public function editConfig()
    {
        Env::offsetSet('video.youtubeApi_key',input('youtubeApi_key','','trim'));
        Env::offsetSet('video.ffmpeg',input('ffmpeg','','trim'));
        Env::offsetSet('video.ffprobe',input('ffprobe','','trim'));
        Env::offsetSet('video.yt-dlp',input('yt_dlp','','trim'));
        Env::offsetSet('video.accessKeyId',input('accessKeyId','','trim'));
        Env::offsetSet('video.accessKeySecret',input('accessKeySecret','','trim'));
        Env::offsetSet('video.bucket',input('bucket','','trim'));

        $envPath = root_path() . DIRECTORY_SEPARATOR . '.env';
        $envinidata=Env::get();
        $inicontent=self::arr_trinsform_ini($envinidata);
        $fp = fopen($envPath, "w") or die("Couldn't open $envPath");
        fputs($fp,$inicontent);
        fclose($fp);
        return json_ok([],10006);
    }

    public function arr_trinsform_ini(array $a, array $parent = array()){
        $out = ''.PHP_EOL;
        $keysindent=[];
        foreach ($a as $k => $v) {
            if (is_array($v)) {
                $sec = array_merge((array) $parent, (array) $k);
                $out .= '[' . join('.', $sec) . ']' . PHP_EOL;
                $out .= arr_trinsform_ini($v, $sec);
            }
            else {
                $key=explode('_',$k);
                if(count($key)>1 && !in_array($key[0],$keysindent)){
                    $keysindent[]=$key[0];
                    $out .=PHP_EOL.PHP_EOL;
                    $out .="[$key[0]]".PHP_EOL;
                    unset($key[0]);
                    $out .= implode('_',$key)." = $v" . PHP_EOL;
                    //unset($key);
                }elseif (count($key)>1 && in_array($key[0],$keysindent)){
                    unset($key[0]);
                    $out .= implode('_',$key)." = $v" . PHP_EOL;
                }else{
                    $out .= "$k = $v" . PHP_EOL;
                }
            }
        }
        return $out;
    }
}
