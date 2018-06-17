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

function download_meeting_airvoice($requestData) {
    $dataBaseDir = Config::getConfig('meeting_airvoice_path');
    if ($requestData) {
        $unitOfWork = BeanFinder::get('UnitOfWork');
        $callsid = $requestData['callsid'];
        $meeting = MeetingDao::getByCallSid($callsid);
        if (! $meeting) {
            Debug::error('meeting is not exists callsid:' . $callsid);
            return;
        }
        $recordurl = $requestData['recordurl'];
        $pos = strrpos($recordurl, '/');
        $date = substr($recordurl, $pos - 8, 8);
        $fileName = substr($recordurl, $pos + 1); // 文件名
        $dataDir = $dataBaseDir . '/' . $date; // 存储目录
        if (! is_dir($dataDir)) {
            mkdir($dataDir, 0777, true);
            chmod($dataDir, 0777);
        }
        $i = 0;
        while (true) {
            $cmd = "wget -t 3 -T 10  -P $dataDir $recordurl";
            Debug::trace($cmd);
            exec($cmd, $output, $return);
            if ($return == 0) {
                $meeting->filename = 'meeting/' . $date . '/' . $fileName;
                $meeting->downloadstatus = 1; // 已下载
                break;
            } else {
                if ($i > 2) {
                    Debug::warn(__METHOD__ . ' 下载电话录音超出重试次数' . ($i + 1) . '次');
                    $meeting->downloadstatus = 2; // 下载失败，放弃
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

download_meeting_airvoice($data);

Debug::flushXworklog();
