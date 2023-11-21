<?php
declare (strict_types=1);

namespace app\controller\admin;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

/**
 * 音频转写
 * Class NLSFileTrans
 * @package app\controller\admin
 */
class NLSFileTrans
{
    // 请求参数
    private const KEY_APP_KEY = "appkey";
    private const KEY_FILE_LINK = "file_link";
    private const KEY_VERSION = "version";
    private const KEY_ENABLE_WORDS = "enable_words";
    private const KEY_Enable_Sample_Rate_Adaptive = "enable_sample_rate_adaptive";
    // 响应参数
    private const KEY_TASK_ID = "TaskId";
    private const KEY_STATUS_TEXT = "StatusText";
    private const KEY_RESULT = "Result";
    // 状态值
    private const STATUS_SUCCESS = "SUCCESS";
    private const STATUS_RUNNING = "RUNNING";
    private const STATUS_QUEUEING = "QUEUEING";
    function submitFileTransRequest($appKey, $fileLink) {
        // 获取task JSON字符串，包含appkey和file_link参数等。
        // 新接入请使用4.0版本，已接入（默认2.0）如需维持现状，请注释掉该参数设置。
        // 设置是否输出词信息，默认为false，开启时需要设置version为4.0。
        //self::KEY_VERSION => "4.0",
        $taskArr = array(self::KEY_APP_KEY => $appKey, self::KEY_FILE_LINK => $fileLink,  self::KEY_VERSION => "4.0",self::KEY_ENABLE_WORDS => false, self::KEY_Enable_Sample_Rate_Adaptive => true,);
        $task = json_encode($taskArr);
        try {
            // 提交请求，返回服务端的响应。
            $submitTaskResponse = AlibabaCloud::nlsFiletrans()
                ->v20180817()
                ->submitTask()
                ->withTask($task)
                ->request();

            // 获取录音文件识别请求任务的ID，以供识别结果查询使用。
            $taskId = NULL;

            $statusText = $submitTaskResponse[self::KEY_STATUS_TEXT];

            if (strcmp(self::STATUS_SUCCESS, $statusText) == 0) {
                $taskId = $submitTaskResponse[self::KEY_TASK_ID];
            }

            return $taskId;
        } catch (ClientException $exception) {
            // 获取错误消息
            print_r($exception->getErrorMessage());
        } catch (ServerException $exception) {
            // 获取错误消息
            print_r($exception->getErrorMessage());
        }
    }
    function getFileTransResult($taskId) {
        $result = NULL;
        while (TRUE) {
            try {
                $getResultResponse = AlibabaCloud::nlsFiletrans()
                    ->v20180817()
                    ->getTaskResult()
                    ->withTaskId($taskId)
                    ->request();
//                print "识别查询结果: " . $getResultResponse . "\n";
                $statusText = $getResultResponse[self::KEY_STATUS_TEXT];
                if (strcmp(self::STATUS_RUNNING, $statusText) == 0 || strcmp(self::STATUS_QUEUEING, $statusText) == 0) {
                    // 继续轮询
                    sleep(10);
                }
                else {
                    if (strcmp(self::STATUS_SUCCESS, $statusText) == 0) {
                        $result = $getResultResponse;
                    }
                    // 退出轮询
                    break;
                }
            } catch (ClientException $exception) {
                // 获取错误消息
                print_r($exception->getErrorMessage());
            } catch (ServerException $exception) {
                // 获取错误消息
                print_r($exception->getErrorMessage());
            }
        }
        return $result;
    }

}
