<?php

ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);
function download_cdrmeeting_airvoice($requestData) {
    // 获取录音存放路径
    $dataBaseDir = Config::getConfig('meeting_airvoice_path');

//     $requestData['cdr_main_unique_id'] = "10.10.60.130-1484201375.96760";
//     $requestData['recordurl'] = "http://api.clink.cn/20170112/3004631-20170112140935-18311413782-18709231977-record-10.10.60.130-1484201375.96760.mp3";
    if ($requestData) {
        $unitOfWork = BeanFinder::get('UnitOfWork');
        $cdr_main_unique_id = $requestData['cdr_main_unique_id'];
        $cdrmeeting = CdrMeetingDao::getByCdr_Main_Unique_Id($cdr_main_unique_id);
        if (! $cdrmeeting) {
            Debug::error('cdrmeeting is not exists cdr_main_unique_id:' . $cdr_main_unique_id);
            return;
        }
        $recordurl = $requestData['recordurl'];
        if (!$recordurl) {
            return 'recordurl id null';
        }
        $pos = strrpos($recordurl, '/');
        $date = substr($recordurl, $pos - 8, 8);
        $arr = explode("?", $recordurl);
        $fileName = substr($arr[0], $pos + 1); // 文件名
        $dataDir = $dataBaseDir . '/' . $date; // 存储目录
        if (! is_dir($dataDir)) {
            mkdir($dataDir, 0777, true);
            chmod($dataDir, 0777);
        }

        $tmpname = "/tmp/{$fileName}";

        $i = 0;
        while (true) {
            $cmd = "wget -t 3 -T 10 '$recordurl' -O  $tmpname";
//            $cmd = "wget -t 3 -T 10 '$recordurl' -O  {$dataDir}/{$fileName} ";
            Debug::trace($cmd);
            exec($cmd, $output, $return);
            if ($return == 0) {
                $cdrmeeting->filename = 'meeting/' . $date . '/' . $fileName;
                $cdrmeeting->downloadstatus = 1; // 已下载
                //todo 提交到fcstatic
                $size = filesize($tmpname);
                $result = UploadService::uploadMeeting($date . '/' . $fileName, $tmpname, $size, $dataBaseDir);
                @unlink($tmpname);
                if (!$result) {
                    Debug::warn('天润融通 '.$cdrmeeting->cdr_customer_number.' 电话录音转存到存储服务失败');
                    break;
                }
                break;
            } else {
                if ($i > 2) {
                    Debug::warn('天润融通 '.$cdrmeeting->cdr_customer_number.' 下载电话录音超出重试次数' . ($i + 1) . '次');
                    $cdrmeeting->downloadstatus = 2; // 下载失败，放弃
                    break;
                }
                sleep(10);
            }
            $i ++;
        }

        $unitOfWork->commitAndRelease();
        Debug::flushXworklog();
    }
    return 'ok';
}

if ($argc < 2) {
    echo "usage: php " . basename(__FILE__) . " jsonData\n";
    exit(1);
}

$jsonData = $argv[1];
$data = json_decode($jsonData, true);
if (!$data) {
    echo "data($jsonData) is not a valid json\n";
    exit(2);
}

download_cdrmeeting_airvoice($data);

Debug::flushXworklog();

