<?php
use think\facade\Env;

return [
    'ffprobe'    => Env::get('video.ffprobe'),
    'ffmpeg'     => Env::get('video.ffmpeg'),
    'yt-dlp'     => Env::get('video.yt-dlp'),
];
