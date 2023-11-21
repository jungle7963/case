<?php
declare (strict_types=1);

namespace app\traits;

use app\controller\admin\NLSFileTrans;
use app\model\Download;
use app\service\ImageHashService;
use mysql_xdevapi\Warning;
use think\cache\driver\Redis;
use think\Exception;
use think\facade\Env;
use think\facade\Snowflake;
use think\annotation\Route;

use Symfony\Component\Process\ExecutableFinder;
use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

use think\Request;
use think\Response;

use OSS\OssClient;
use OSS\Core\OssException;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

use FFMpeg\FFMpeg;
use FFMpeg\Format\Audio\Mp3;

use think\facade\Config;

/**
 * 文件上传公共方法
 * Trait DownloadTrait
 * @package app\traits
 */
trait DownloadTrait
{
    /**
     * 音视频下载
     * @Route("download", method="POST")
     */
    public function download()
    {
        //视频下载
        $s = $this->mp4Download();
        $data = $this->accessProtected($s,'data');
        if ($data['status'] != 1) return json_error(10018);
        //视频转音频
        $this->mp3Transcription();
        $data = $this->accessProtected($s,'data');
        if ($data['status'] != 1) return json_error(10020);
        //上传OSS
        $this->ossUpload();
        $data = $this->accessProtected($s,'data');
        if ($data['status'] != 1) return json_error(10022);
        //语音识别
        $this->mp3Translate();
        $data = $this->accessProtected($s,'data');
        if ($data['status'] != 1) return json_error(10023);

        return json_ok([], 10013);
    }

    public function accessProtected($obj, $prop){
        $reflection = new \ReflectionClass($obj);
        $property = $reflection->getProperty($prop);
        $property->setAccessible(true);
        return $property->getValue($obj);
    }

    /**
     * 进度
     * @Route("getprogress", method="POST")
     */
    public function getprogress()
    {
        $redis = new Redis(Config::get('cache.stores.redis'));
        return (['progress' => $redis->get('progress'),'speed' => $redis->get('speed')]);
    }

    /**
     * 下载视频
     * @Route("mp4Download", method="POST")
     */
    public function mp4Download()
    {
        $link = input('link');
        $classify = input('classify');
        $start_time = time();
        $redis = new Redis(Config::get('cache.stores.redis'));
        $redis->set('progress', 0);

        switch ($classify) {
            case 'bilibili':
            case 'youtube':
                if ($classify === 'bilibili') {
                    $success = preg_match('#(?<=video\/)[^?=/]+#', $link, $matches);;
                    if (!$success) return json_error(10014);
                    $where = "1 and link = '$link'";
                    $lists = self::$service::getLists($where);
                    $lists = json_decode(json_encode($lists),true);
                    $id = $lists[0]['vid'];
                } else {
                    $success = preg_match('#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#', $link, $matches);
                    if (!$success) return json_error(10014);
                    $id = $matches[0];
                }

                if (!empty($id)){
                    $format = 'mp4';
                    $exists = file_exists(\think\facade\Config::get('video.DOWNLOAD_FOLDER') . $id . "." . $format);
                    if ($exists) return json_error(10025);
                }

                $ytdlp = (new ExecutableFinder())->find('yt-dlp', Config::get('ffprobe.yt-dlp'));
                $options = Options::create()
                    ->output('%(id)s.%(ext)s')
                    ->downloadPath(\think\facade\Config::get('video.DOWNLOAD_FOLDER'))
                    ->url($link);
                if($classify === 'youtube') {
                    $options = $options->format('18');//22->1280x720; 18->640x360
                }

                try {
                    $dl = new YoutubeDl();
                    if ($ytdlp !== null) $dl->setBinPath($ytdlp);

                    $dl->onProgress(static function ($progressTarget, $percentage, $size, $speed, $eta, $totalTime): void {
                        $redis = new Redis(Config::get('cache.stores.redis'));
                        if ($redis->get('progress') === '100'){
                            $redis->set('progress', 100);
                        }else{
                            $redis->set('progress', substr($percentage,0,-1));
                            $redis->set('speed', $speed);
                        }
                    });

                    $video = $dl->download($options)->getVideos();
                    if (empty($video)) return json_error(10014);
                    $video = $video[0];
                    if ($video->getError() !== null) return json_error(10014);
                } catch (Exception $e) {
                    var_dump($e);
                    return json_error(10014, $e->getMessage());
                }
                $redis->set('speed', '');
                $vid = $video->getId();
                $title = $video->getTitle();
                break;

            case 'kuaishou':
            case 'douyin':
                preg_match('#(?<=com/)[a-zA-Z0-9-]+(?=)|(?<=com/\/)[^\n]+|(?<=com/)[^\n]+#', $link, $matches);
                $vid = $matches[0];
                $format = 'mp4';
                $exists = file_exists(\think\facade\Config::get('video.DOWNLOAD_FOLDER') . $vid . "." . $format);
                if ($exists) return json_error(10025);

                require_once __DIR__.'/../Video.php';
                $api = new \Video();
                $ret = $api->exp($link);
                $link = $ret['data']['url'];
                $title = $ret['data']['title'];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_URL, $link);
                curl_setopt($ch,CURLOPT_TIMEOUT,180);

                curl_setopt($ch, CURLOPT_NOPROGRESS, false);
                curl_setopt($ch, CURLOPT_PROGRESSFUNCTION,
                    function($resource,$download_size, $downloaded, $upload_size, $uploaded){
                        if($download_size > 0){
                            $progress = number_format($downloaded / $download_size  * 100,1);
                            $redis = new Redis(Config::get('cache.stores.redis'));
                            $redis->set('progress', $progress);
                        }
                    });

                ob_start();
                curl_exec($ch);
                if(curl_errno($ch)) return;
                $return_content = ob_get_contents();
                ob_end_clean();
                $info = curl_getinfo($ch);
                if ($info['http_code'] != 200) return json_error(10014);
                $fp = @fopen( __DIR__ . "/../../public/download/$vid.mp4", "a");
                fwrite($fp, $return_content);
                curl_close($ch);
                break;

            default:
                return json_error(10014);
                break;
        }
        $end_time = time();
        $mp4_download_time = $end_time - $start_time;
        $id = input('id', '0', 'int');
        $byte = filesize(__DIR__ . "/../../public/download/$vid.mp4");
        $size = json_encode(round($byte / (1024 * 1024), 2));
        $url = 'http://'.$_SERVER['HTTP_HOST'].'/download/'.$vid.'.mp4';
        $getID3 = new \getID3();
        $file = $getID3->analyze(__DIR__ . "/../../public/download/$vid.mp4");
        $video_length = $file['playtime_string'] ?? '--';
        $json = [
            "title" => $title,
            'vid'   => $vid,
            'url'   => $url,
            'size'  => $size,
            'video_length'  => $video_length,
            'mp4_download_time' => $mp4_download_time
        ];
        self::$service::save($json,$id);
        return json_ok([], 10017);
    }

    /**
     * 视频转音频
     * @Route("mp3Transcription", method="POST")
     */
    public function mp3Transcription()
    {
        $id = input('id');
        $start_time = time();
        $where = "1 and id = $id";
        $lists = self::$service::getLists($where);
        $vid = $lists[0]['vid'];

        $exists = file_exists(\think\facade\Config::get('video.DOWNLOAD_FOLDER') . $vid . ".mp4");
        if (!$exists) return json_error(10020,'请先下载视频');

        $exists = file_exists(\think\facade\Config::get('video.DOWNLOAD_FOLDER') . $vid . ".mp3");
        if ($exists) return json_error(10032);

        //TODO ffprobe需要额外配置文件, 下载、转译需要用的第三方文件统一配置第三方路径
        $ffmpeg = FFMpeg::create(["ffmpeg.binaries" => Config::get('ffprobe.ffmpeg'),'ffprobe.binaries' => Config::get('ffprobe.ffprobe')]);
        $video = $ffmpeg->open(__DIR__ . "/../../public/download/$vid.mp4");
        $audio_format = new Mp3();
        $video->save($audio_format, __DIR__ . "/../../public/download/$vid.mp3");

        $end_time = time();
        $mp3_transcription_time = $end_time - $start_time;

        $json = [
            'mp3_transcription_time' => $mp3_transcription_time
        ];
        self::$service::save($json,$id);

        return json_ok([], 10019);
    }

    /**
     * 上传至oss
     * @Route("ossUpload", method="POST")
     */
    public function ossUpload()
    {
        $id = input('id');
        $start_time = time();
        $where = "1 and id = $id";
        $lists = self::$service::getLists($where);
        $idformat = $lists[0]['vid'].'.mp3';
        $exists = file_exists(\think\facade\Config::get('video.DOWNLOAD_FOLDER') . $idformat);
        if (!$exists) return json_error(10022,'音频不存在');
        $accessKeyId = Env::get('video.accessKeyId', '');
        $accessKeySecret = Env::get('video.accessKeySecret', '');
        // yourEndpoint填写Bucket所在地域对应的Endpoint。Endpoint填写如https://oss-cn-hangzhou.aliyuncs.com。
        $endpoint = "oss-cn-shanghai.aliyuncs.com";
        // 填写Bucket名称，例如examplebucket。
        $bucket = Env::get('video.bucket', '');
        // 填写Object完整路径，例如exampledir/exampleobject.txt。Object完整路径中不能包含Bucket名称。
        $object = "video/$idformat";
        // <yourLocalFile>由本地文件路径加文件名包括后缀组成，例如/users/local/myfile.txt。
        // 填写本地文件的完整路径，默认从示例程序所属项目对应本地路径中上传文件。
        $filePath = __DIR__ . "/../../public/download/$idformat";

        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);

            $ossClient->uploadFile($bucket, $object, $filePath);

        }catch(\Exception $e){
            var_dump($e); //TODO 异常处理
        }
        $end_time = time();
        $oss_upload_time = $end_time - $start_time;

        $json = [
            'oss_upload_time' => $oss_upload_time
        ];
        self::$service::save($json,$id);

        return json_ok([], 10021);

    }

    /**
     * 语音识别
     * @Route("mp3Translate", method="POST")
     */
    public function mp3Translate()
    {
        $id = input('id');
        $start_time = time();
        $where = "1 and id = $id";
        $lists = self::$service::getLists($where);
        if ($lists[0]['oss_upload_time'] === null) return json_error(10023,'音频未上传到OSS');
        $idformat = $lists[0]['vid'].'.mp3';
        $accessKeyId = Env::get('video.accessKeyId', '');
        $accessKeySecret = Env::get('video.accessKeySecret', '');
        $original = '';
        $content = '';
        $lyric = '';

        $appKey = "xpniwqn2gICK9H39";

        $fileLink = "https://vvres.oss-cn-shanghai.aliyuncs.com/video/$idformat";

        //第一步：设置一个全局客户端。
        //使用阿里云RAM账号的AccessKey ID和AccessKey Secret进行鉴权。
        AlibabaCloud::accessKeyClient($accessKeyId, $accessKeySecret)
            ->regionId("cn-shanghai")
            ->asGlobalClient();

        $fileTrans = new NLSFileTrans();
        //第二步：提交录音文件识别请求，获取任务ID，用于后续的识别结果轮询。
        $taskId = $fileTrans->submitFileTransRequest($appKey, $fileLink);

        if ($taskId != null) {
            //第三步：根据任务ID轮询识别结果。
            $result = $fileTrans->getFileTransResult($taskId);
            if ($result != NULL) {

                $Result = $result['Result'];
                $Sentences = $Result['Sentences'];
                for ($i = 0; $i < count($Sentences); $i++) {
                    $content .= $Sentences[$i]['Text'];
                }

                for ($i = 0; $i < count($Sentences); $i++) {
                    $original .= "[" . $Sentences[$i]['BeginTime'] . "]" . "[" . $Sentences[$i]['EndTime'] . "]" . $Sentences[$i]['Text'];
                }

                for ($i = 0; $i < count($Sentences); $i++) {
                    $begintime = intval(ceil($Sentences[$i]['BeginTime'] / 1000));
                    $endtime = intval(ceil($Sentences[$i]['EndTime'] / 1000));
                    if ($begintime > 3600) {
                        $hours = intval($begintime / 3600);
                        $betime = $hours . ":" . gmstrftime('%M:%S', $begintime);
                    } else {
                        $betime = gmstrftime('%H:%M:%S', $begintime);
                    }
                    if ($endtime > 3600) {
                        $hours = intval($endtime / 3600);
                        $entime = $hours . ":" . gmstrftime('%M:%S', $endtime);
                    } else {
                        $entime = gmstrftime('%H:%M:%S', $endtime);
                    }
                    $lyric .= "[" . $betime . "]" . "[" . $entime . "]" . $Sentences[$i]['Text'] . "\r\n";
                }

            } else {
                return json_error(10023,'语音识别失败，请稍后重试！');
            }

        } else {
            return json_error(10023,'语音识别失败，请稍后重试！');
        }

        $end_time = time();
        $mp3_translate_time = $end_time - $start_time;
        $json = [
            "content" => $content,
            'original' => $original,
            'lyric' => $lyric,
            'text_length' => strlen($content),
            'mp3_translate_time' => $mp3_translate_time,
        ];
        self::$service::save($json,$id);

        return json_ok([], 10024);
    }

}

