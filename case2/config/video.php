<?php
// +----------------------------------------------------------------------
// | 视频设置
// +----------------------------------------------------------------------
use think\facade\Env;

return [
    'DOWNLOAD_FOLDER' => "download/",
    'DOWNLOAD_MAX_LENGTH' => 0,
    'LOG' => false,
    'MAX_RESULTS' => 10,
    'API_KEY' => '',
    'youtubeApi_key'     => Env::get('video.youtubeApi_key'),
];
