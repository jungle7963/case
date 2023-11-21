<?php
declare (strict_types=1);

namespace app\controller\admin;

use app\controller\admin\Base;
use app\controller\Index;
use app\traits\ControllerTrait;
use app\traits\DownloadTrait;
use http\Client;
use think\annotation\route\Group;
use think\annotation\Route;
use think\facade\Config;
use think\facade\Db;


/**
 * youtube数据获取
 * Class Youtube
 * @package app\controller\admin
 * @author  2066362155@qq.com
 * @Group("admin/youtube")
 */
class Youtube extends Base
{
    public static $service = 'app\service\YoutubeService';
    public static $AuthRuleService = 'app\service\AuthRuleService';
    public static $informationService = 'app\service\InformationService';
    public static $wordpressService = 'app\service\WordpressService';
    public static $YoutubeQueryService = 'app\service\YoutubeQueryService';
    public static $YoutubeChannelService = 'app\service\YoutubeChannelService';
    public static $validateName = 'Youtube';
    public static $validateScene = 'save';
    public static $enableField = ['status' => [0, 1]];

    use ControllerTrait;
    use DownloadTrait;

    public function beforeIndex()
    {
        //搜索参数
        $link      = input('link', '', 'trim');
        $title     = input('title', '', 'trim');
        $classify  = input('classify', '', 'trim');
        $status    = input('status', -1, 'intval');
        $where = true;

        if ($link) {
            $where .= " and link like '%" . $link . "%' ";
        }
        if ($title) {
            $where .= " and title like '%" . $title . "%' ";
        }
        if ($classify) {
            $where .= " and classify like '%" . $classify . "%' ";
        }
        if ($status != -1) {
            $where .= " and status = " . $status;
        }

        return [$where, []];
    }

    public function beforeSave($id)
    {
        //接收数据
        $data = [
            'link'       => input('link', '', 'trim'),
            'classify'   => input('classify', '', 'trim'),
            'content'    => input('content', '', 'trim'),
            'original'   => input('original', '', 'trim'),
            'lyric'      => input('lyric', '', 'trim'),
            'title'      => input('title', '', 'trim'),
            'information_id'     => input('information_id', '', 'int'),
        ];
        return $data;
    }

    /**
     * 删除
     * @Route("ytdel", method="POST")
     */
    public function ytdel()
    {
        $id = input('id', '0', 'int');
        if ($id == 0) {
            return json_error(10004);
        } else {
            $where = "1 and id = '$id'";
            $list = self::$service::getLists($where);
            $list = json_decode(json_encode($list),true);
            $vid = $list[0]['vid'];
            if (!empty($vid)) {
                unlink(__DIR__ . "/../../../public/download/$vid.mp4");
                unlink(__DIR__ . "/../../../public/download/$vid.mp3");
            }
            if (self::$service::del($id)) {
                return json_ok([], 10008);
            } else {
                return json_error(10007);
            }
        }
    }

    /**
     * id查询youtube视频
     * @Route("serach", method="POST")
     */
    public function serach()
    {
        $vid = input('vid','','trim');
        if ($vid === '') return json_error(10031,'视频ID不能为空！');

        $key = Config::get('video.youtubeApi_key');
        $queryList = file_get_contents("https://youtube.googleapis.com/youtube/v3/videos?part=snippet%2CcontentDetails%2Cstatistics&id={$vid}&key=$key" );
        $query = json_decode($queryList,true);
        if (!empty($query['error'])) return json_error(10031,'youtubeApi_key失效或错误');
        if (count($query['items']) === 0) return json_error(10031,'视频ID有误，查询失败！');

        foreach ($query['items'] as $item){
            $data[] = [
                'link'=>'https://www.youtube.com/embed/'.$item['id'],
                'channelId' => $item['snippet']['channelId'],
                'channelTitle' => $item['snippet']['channelTitle'],
                'title' => $item['snippet']['title'],
                'description' => $item['snippet']['description'],
                'img' => $item['snippet']['thumbnails']['high']['url'],
                'viewCount' =>$item['statistics']['viewCount'] ?? 0,
                'likeCount' =>$item['statistics']['likeCount'] ?? 0,
                'publishedAt'=>substr($item['snippet']['publishedAt'],0,10)
            ];
        }
        
        return json_ok($data);
    }

    /**
     * 条件查询youtube视频
     * @Route("query", method="POST")
     */
    public function query()
    {
        $count = input('count','', 'trim');
        $order = input('order','', 'trim');
        $q = input('q','', 'trim');
        $channelId = input('channelId','', 'trim');
        $regionCode = input('regionCode','', 'trim');
        $start_time = input('start_time','', 'trim');
        $end_time = input('end_time','', 'trim');
        $types = input('type','', 'trim');

        if ($types === ''){
            $type = '';
            $videoCaption = '';
            $videoDuration = '';
        }elseif (count($types) === 1){
            $type = $types[0];
            $videoCaption = '';
            $videoDuration = '';
        }elseif (count($types) === 2){
            $type = $types[0];
            $videoCaption = $types[1];
            $videoDuration = '';
        }else{
            $type = $types[0];
            $videoCaption = $types[1];
            $videoDuration = $types[2];
        }
        $genre = $type;

        if ($videoCaption === 'all') $videoCaption = '';
        if ($videoDuration === 'all') $videoDuration = '';
        if ($count === '') $count = '10';
        if (!is_numeric($count)) return json_error(10031,'查询条数请输入数字');
        if ($q != '') $q = '&q='.$q;
        if ($channelId != '') $channelId = '&channelId='.$channelId;
        if ($order != '') $order = '&order='.$order;
        if ($regionCode != '') $regionCode = '&regionCode='.$regionCode;
        if ($type != '') $type = '&type='.$type;
        if ($videoCaption != '') $videoCaption = '&videoCaption='.$videoCaption;
        if ($videoDuration != '') $videoDuration = '&videoDuration='.$videoDuration;
        if ($start_time != '') $start_time = '&publishedAfter='.$start_time.'T00%3A00%3A00Z';
        if ($end_time != '') $end_time = '&publishedBefore='.$end_time.'T00%3A00%3A00Z';
        $maxResults = '&maxResults='.$count;

        $key = Config::get('video.youtubeApi_key');
        $link = "https://youtube.googleapis.com/youtube/v3/search?part=snippet{$maxResults}{$q}{$channelId}{$regionCode}{$type}{$videoCaption}{$videoDuration}{$order}{$start_time}{$end_time}&key=$key";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $link);
        $query = json_decode(curl_exec($ch),true);
        curl_close($ch);

        if (!empty($query['error'])) return json_error(10031,'youtubeApi_key失效或错误');
        if (count($query['items']) === 0) return json_error(10031,'未查询到相关数据！');
        $country = $query['regionCode'];

        switch ($genre)
        {
            case '':
            case 'video':
                $youtube = new \Madcoda\Youtube\Youtube(array('key' => $key));
                foreach ($query['items'] as $value){
                    if (!isset($value['id']['videoId'])) continue;
                    $item = json_decode(json_encode($value['id']['videoId']),true);
                    $video = $youtube->getVideoInfo($item);
                    $video = json_decode(json_encode($video),true);

                    $data[] = [
                        'genre' => $genre,
                        'link'=>'https://www.youtube.com/embed/'.$value['id']['videoId'],
                        'country' => $country,
                        'channelId' => $video['snippet']['channelId'],
                        'channelTitle' => $video['snippet']['channelTitle'],
                        'title' => $video['snippet']['title'],
                        'img' => $video['snippet']['thumbnails']['high']['url'],
                        'viewCount'=>$video['statistics']['viewCount'] ?? 0,
                        'likeCount' =>$video['statistics']['likeCount'] ?? 0,
                        'publishedAt'=>substr($value['snippet']['publishedAt'],0,10)
                    ];
                }
                break;

            case 'channel':
                foreach ($query['items'] as $item){
                    $channelId = $item['id']['channelId'];
                    $queryList = file_get_contents("https://youtube.googleapis.com/youtube/v3/channels?part=snippet%2Cstatistics&id={$channelId}&key=$key");
                    $query = json_decode($queryList,true);

                    $data[] = [
                        'genre' => $genre,
                        'link'=>'https://www.youtube.com/channel/'.$item['id']['channelId'],
                        'country' => $country,
                        'viewCount' => $query['items'][0]['statistics']['viewCount'] ?? 0,
                        'subscriberCount' => $query['items'][0]['statistics']['subscriberCount'] ?? 0,
                        'videoCount' => $query['items'][0]['statistics']['videoCount'] ?? 0,
                        'channelId' => $item['id']['channelId'],
                        'channelTitle' => $item['snippet']['channelTitle'],
                        'img' => $item['snippet']['thumbnails']['high']['url'],
                        'publishedAt'=>substr($item['snippet']['publishedAt'],0,10),
                    ];
                }
                break;

            default:
                return json_error(10031,'未查询到相关数据！');
        }

        return json_ok($data);
    }

    //过滤emoji表情
    public function filterEmoji($str)
    {
        $str = preg_replace_callback('/./u',function (array $match) {
            return strlen($match[0]) >= 4 ? '' : $match[0];
        },$str);
        return $str;
    }

    /**
     * 语言获取字幕
     * @Route("caption", method="POST")
     */
    public function caption()
    {
        $url = input('url','', 'trim');
        $lang = input('lang','', 'trim');
        $link = $url.$lang;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $link);
        $content = curl_exec($ch);
        curl_close($ch);

        $html = preg_replace('#</text>#', "\r\n", $content);
        $html = preg_replace('#<text.*>#', "", $html);
        $html = preg_replace('#<\?xml.*\?><transcript>#', "", $html);
        $captions = preg_replace('#</transcript>#', "", $html);
        $search = ['&lt;','&gt;','&amp;','&quot;','&apos;','&#39;'];
        $replace = ['<','>','&','"',"'","'"];
        for ($i = 0; $i < count($search); $i++) {
            $captions = str_ireplace($search[$i], $replace[$i], $captions);
        }
        $captions = str_replace("\r\n","</br>",$captions);
        return $captions;
    }

    /**
     * 字幕
     * @Route("captions", method="POST")
     */
    public function captions($link)
    {
        if (empty($link)) $link = input('link','', 'trim');
        $u = str_replace("embed/",'watch?v=',$link);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $u);
        $return_content = curl_exec($ch);
        if (!$return_content) return;
        preg_match_all('#timedtext\?v=.*?(?=",)#', $return_content, $matches);
        if (empty($matches[0])) return;

        $lang = [];
        foreach ($matches[0] as $index => $match){
            $str = substr(strstr($match,'lang='),5);
            if (!strpos($str, "\u0026")){
                array_push($lang,$str);
            }
        }
        $lang = array_unique($lang);

        $url = 'https://www.youtube.com/api/'.$matches[0][0];
        $newUrl = str_replace("\u0026",'&',$url);
        $s = substr($newUrl,0,strpos($newUrl,'lang=')).'lang=';
        $newUrl = $s.$lang[0];

        curl_setopt($ch, CURLOPT_URL, $newUrl);
        $content = curl_exec($ch);
        curl_close($ch);

        $html = preg_replace('#</text>#', "\r\n", $content);
        $html = preg_replace('#<text.*>#', "", $html);
        $html = preg_replace('#<\?xml.*\?><transcript>#', "", $html);
        $captions = preg_replace('#</transcript>#', "", $html);
        $search = ['&lt;','&gt;','&amp;','&quot;','&apos;','&#39;'];
        $replace = ['<','>','&','"',"'","'"];
        for ($i = 0; $i < count($search); $i++) {
            $captions = str_ireplace($search[$i], $replace[$i], $captions);
        }
        $captions = str_replace("\r\n","</br>",$captions);
        return [$captions,$lang,$s];
    }

    /**
     * 保存youtube视频
     * @Route("saveQuery", method="POST")
     */
    public function saveQuery()
    {
        $link = input('link','', 'trim');
        $genre = input('genre','', 'trim');

        $where = "1 and link = '$link'";
        if ($genre === 'video' || $genre === '') $lists = self::$YoutubeQueryService::getLists($where);
        if ($genre === 'channel') $lists = self::$YoutubeChannelService::getLists($where);
        if (count($lists) === 0) $id = 0;
        else $id = $lists[0]['id'];

        if ($genre != 'channel'){
            $title = $this->filterEmoji(input('title','', 'trim'));
            list($description,$lang,$url) = $this->captions($link);
            $description = $this->filterEmoji($description);
        }
        $channelTitle = $this->filterEmoji(input('channelTitle','', 'trim'));

        switch ($genre)
        {
            case '':
            case 'video':
                $json = [
                    'link' => $link,
                    'viewCount' => input('viewCount',0, 'trim'),
                    'likeCount' => input('likeCount',0, 'trim'),
                    'country' => input('country','', 'trim'),
                    'publishedAt' => input('publishedAt','', 'trim'),
                    'img' => input('img','', 'trim'),
                    'channelId' => input('channelId','', 'trim'),
                    'channelTitle' => $channelTitle,
                    'title' => $title,
                    'description' => $description,
                    'langs' => json_encode($lang),
                    'lang' => $lang[0] ?? '',
                    'url' => $url,
                ];
                self::$YoutubeQueryService::save($json,$id);
                break;
            case 'channel':
                $json = [
                    'link' => $link,
                    'viewCount' => input('viewCount',0, 'trim'),
                    'subscriberCount' => input('subscriberCount',0, 'trim'),
                    'videoCount' => input('videoCount',0, 'trim'),
                    'country' => input('country','', 'trim'),
                    'publishedAt' => input('publishedAt','', 'trim'),
                    'img' => input('img','', 'trim'),
                    'channelId' => input('channelId','', 'trim'),
                    'channelTitle' => $channelTitle,
                ];
                self::$YoutubeChannelService::save($json,$id);
                break;
            default:
                return json_error(10005);
        }
        return json_ok([],10006);
    }

    /**
     * 批量保存youtube视频
     * @Route("saveQueryAll", method="POST")
     */
    public function saveQueryAll()
    {
        $all = input();
        array_pop($all);
        foreach ($all as $item){
            $link = $item['link'];
            $where = "1 and link = '$link'";
            $item['genre'] ?? $item['genre'] = 'video';
            if ($item['genre'] === 'video' || $item['genre'] === '') $lists = self::$YoutubeQueryService::getLists($where);
            if ($item['genre'] === 'channel') $lists = self::$YoutubeChannelService::getLists($where);
            if (count($lists) === 0) $id = 0;
            else $id = $lists[0]['id'];

            if ($item['genre'] != 'channel'){
                $title = $this->filterEmoji($item['title']);
                list($description,$lang,$url) = $this->captions($link);
                $description = $this->filterEmoji($description);
            }
            $channelTitle = $this->filterEmoji($item['channelTitle']);

            switch ($item['genre'])
            {
                case '':
                case 'video':
                    $json = [
                        'link' => $link,
                        'viewCount' => $item['viewCount'] ?? 0,
                        'likeCount' => $item['likeCount'] ?? 0,
                        'country' => $item['country'] ?? '',
                        'publishedAt' => $item['publishedAt'],
                        'img' => $item['img'],
                        'channelId' => $item['channelId'],
                        'channelTitle' => $channelTitle,
                        'title' => $title,
                        'description' => $description,
                        'langs' => json_encode($lang),
                        'lang' => $lang[0] ?? '',
                        'url' => $url,
                    ];
                    self::$YoutubeQueryService::save($json,$id);
                    break;
                case 'channel':
                    $json = [
                        'link' => $link,
                        'viewCount' => $item['viewCount'] ?? 0,
                        'subscriberCount' => $item['subscriberCount'] ?? 0,
                        'videoCount' => $item['videoCount'] ?? 0,
                        'country' => $item['country'],
                        'publishedAt' => $item['publishedAt'],
                        'img' => $item['img'],
                        'channelId' => $item['channelId'],
                        'channelTitle' => $channelTitle,
                    ];
                    self::$YoutubeChannelService::save($json,$id);
                    break;
                default:
                    return json_error(10005);
            }
        }
        return json_ok([],10006);
    }

    /**
     * 添加链接
     * @Route("saveLink", method="POST")
     */
    public function saveLink()
    {
        $link = input('link', '', 'trim');
        if ($link === '') return json_error(10026);
        $videourl = preg_match('#(?<=//)[a-zA-Z0-9-]+(?=/)|(?<=//\/)[^/\n]+|(?<=//)[^/\n]+#', $link, $matches);
        if (!$videourl) return json_error(10027);
        $url = $matches[0];
        if ($url === 'www.bilibili.com' || $url === 'www.youtube.com'){
            preg_match('#(?<=www.)[a-zA-Z0-9-]+(?=.com)#', $url, $mc);
            // }elseif ($url === 'v.kuaishou.com' || $url === 'v.douyin.com'){
            //     preg_match('#(?<=v.)[a-zA-Z0-9-]+(?=.com)#', $url, $mc);
        }else{
            return json_error(10027);
        }
        $classify = $mc[0];
        if ($classify == 'bilibili') {
            $success = preg_match('#(?<=video\/)[^?=/]+#', $link, $matches);
            if (!$success) return json_error(10027);
            $link = 'https://www.bilibili.com/video/'.$matches[0];
        }

        $where = "1 and link = '$link'";
        $lists = self::$service::getLists($where);
        if (count($lists) != 0) return json_error(10028,'链接已存在，请勿重复添加');

        $data = [
            'link'       => $link,
            'classify'   => $classify,
        ];
        $res = self::$service::save($data);
        if ($res == false) {
            return json_error(10028);
        } else {
            return json_ok(['id' => strval($res)], 10029);
        }
    }

    /**
     * 搜索发布到wordpress
     * @Route("publish", method="POST")
     */
    public function publish()
    {
        $where = "1 and id = 45";
        $lists = self::$AuthRuleService::getLists($where);
        $lists = json_decode(json_encode($lists),true);
        if ($lists[0]['release_status'] != 1) return json_error(10033);

        $where = "1 and id = 46";
        $lists = self::$AuthRuleService::getLists($where);
        $lists = json_decode(json_encode($lists),true);
        if ($lists[0]['release_status'] != 1) return json_error(10033);

        $content = input('description', '', 'trim');
        $link = input('link', '', 'trim');
        $l = input('langs', '', 'trim');
        $lang = '';
        $langs = '';
        if (!empty($l)) {
            $lang = $l[0];
            $langs = json_encode($l);
        }
        $where = "1 and link = '$link'";
        $lists = self::$YoutubeQueryService::getLists($where);
        if (count($lists) === 0) $id = 0;
        else $id = $lists[0]['id'];

        $f = $this->announce($content);
        if (!empty($f)) return json_error(10016,$f);
        $json = [
            'link' => input('link','', 'trim'),
            'viewCount' => input('viewCount',0, 'trim'),
            'likeCount' => input('likeCount',0, 'trim'),
            'country' => input('country','', 'trim'),
            'publishedAt' => input('publishedAt','', 'trim'),
            'img' => input('img','', 'trim'),
            'channelId' => input('channelId','', 'trim'),
            'channelTitle' => input('channelTitle','', 'trim'),
            'title' => input('title','', 'trim'),
            'description' => input('description','', 'trim'),
            'langs' => $langs,
            'lang' => $lang,
            'url' => input('url','', 'trim'),
        ];
        self::$YoutubeQueryService::save($json,$id);

        return json_ok([],10015);
    }

    /**
     * 下载发布到wordpress
     * @Route("release", method="POST")
     */
    public function release()
    {
        $where = "1 and id = 45";
        $lists = self::$AuthRuleService::getLists($where);
        $lists = json_decode(json_encode($lists),true);
        if ($lists[0]['release_status'] != 1) return json_error(10033);

        $where = "1 and id = 49";
        $lists = self::$AuthRuleService::getLists($where);
        $lists = json_decode(json_encode($lists),true);
        if ($lists[0]['release_status'] != 1) return json_error(10033);

        $content = input('content', '', 'trim');
        $f = $this->announce($content);
        if (!empty($f)) return json_error(10016,$f);
        $this->save();
        return json_ok([],10015);
    }

    /**
     * 获取文章类别
     * @Route("categories", method="POST")
     */
    public function categories() {
        $information_id  = input('information_id', '', 'int');
        $where = "1 and id = $information_id";
        $lists = self::$informationService::getLists($where);
        if (count($lists) === 0) return 'wordpress接口信息有误';

        $username = $lists[0]['username'];
        $password = $lists[0]['password'];
        $rest_api_url = $lists[0]['url'];
        $rest_api_url = rtrim($rest_api_url,'posts').'categories?per_page=100';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $rest_api_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($username . ':' . $password),
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = json_decode(curl_exec($ch),true);
        curl_close($ch);
        if (empty($result)) return json_error();

        foreach ($result as $item){
            $category = rtrim($item['link'],'/');
            $category = trim(strrchr($category, '/'),'/');
            $data['data'][] = ['id' => $item['id'], 'category' => $category];
        }
        $url = substr($rest_api_url,0,strpos($rest_api_url, '/'));
        preg_match('#(?<=//)[^/\n]+#', $rest_api_url, $matches);
        $data['url'] = $url .'//'. $matches[0];
        return json_ok($data,10000);
    }

    public function announce($content){
        $admin = new Admin($this->app);
        $user = $admin->getuser();
        $data = $this->accessProtected($user,'data');
        $u = $data['data']['username'];

        $title = input('title', '', 'trim');
        $slug = input('alias', '', 'trim');
        $category = input('category', '', 'int');
        $category == '' ?: $category = [$category];

        $information_id  = input('information_id', '', 'int');

        if ($content === '' || $title === '' || $information_id === '') return '标题、内容、wordpress接口不能为空';
        $where = "1 and id = $information_id";
        $lists = self::$informationService::getLists($where);
        if (count($lists) === 0) return 'wordpress接口信息有误';

        $username = $lists[0]['username'];
        $password = $lists[0]['password'];
        $rest_api_url = $lists[0]['url'];
        $data_string = json_encode([
            'title'    => $title,
            'content'  => $content,
            'status'   => 'publish',
            'slug' => $slug,
            'categories'     => $category
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $rest_api_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string),
            'Authorization: Basic ' . base64_encode($username . ':' . $password),
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = json_decode(curl_exec($ch),true);
        curl_close($ch);

        if ($result === null || empty($result['id'])) return '发布失败';

        $json = [
            "link" => input('link','', 'trim'),
            "title" => $title,
            "description" => $content,
            "url" => $rest_api_url,
            "username" => $u,
            "wordpress_url" => $result['link']
        ];
        self::$wordpressService::save($json);
    }

}
