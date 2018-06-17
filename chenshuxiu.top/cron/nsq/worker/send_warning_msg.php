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

// static $access_token =
// 'KSSHYKK0Sqq4qNATw7eVmJ8gHwtRo6QZ2VDoVN28Z82l3E6pgL-JFXAa8uHvCHk93k6dItFbSftDs62Jbu4Kfcv_0sESflGYgVldgvF5ai3GiyxWi2F6LA8tNXahWno9IMSdABACGM';
function send_warning_msg ($data) {
    $wxshopid = 8;
    $template_id = 'cwn3DV-Y-poPEHQrEVTOe_lKFii6h_Y448qDrb9vkKg';

    $openids = [
        'taoxiaojin' => 'oMQqrwH0ZwQNV4LswjIfy853EpP4',  // 10004 乔涛金
        'shijianping' => 'oMQqrwExmUkQdVevWeQEzDZRw5qM',  // 10007 史建平
        'chenshigang' => 'oMQqrwLDjWSVaOiCo5rFG519N3Mk',  // 10022 陈士岗
        'fanghanwen' => 'oMQqrwEsvGcGA2eHE4nzu_Tt14tk',  // 10013 方汉文
        'lijie' => 'oMQqrwEfd1joL9bNK2klsIvNBOHA',  // 10014 李杰
        'likunting' => 'oMQqrwO_15upHpEzOUfbJJxdMNOM', // 10052 李琨亭
        'yangshujie' => 'oMQqrwEAkNCidzNNecVIQFEv_dEM', //10104 杨舒杰
        'sunshu' => 'oMQqrwBQOJ82MIJlcgREjR28b45Q', // 10116 孙术
        'hanlei' => 'oMQqrwOGDi9gcw3gqdFF51V2lsu4']; // 10139 韩磊

    $i = 0;
    while ($i < 2) {
        try {
            $wxshop = WxShop::getById($wxshopid);
            break;
        } catch (Exception $e) {
            $i ++;
            // BeanFinder::clearBean("UnitOfWork");
            BeanFinder::clearBean("DbExecuter");
            $dbExecuter = BeanFinder::get('DbExecuter');
            $dbExecuter->reConnection();
            // Debug::error('getAccessToken fail ' . $i . ' row:' . $workload .
            // "\n"
            // . $e->getTraceAsString());
            sleep(1);
        }
    }
    $title = $data['env'] . ' ID ' . $data['unitofworkid'];
    $brief = $data['brief'];
    $pattern = '/\\[\\d{2}m(.*?)\\[m/i';
    $brief = preg_replace($pattern, '$1', $brief);

    $first = array(
        "value" => $title,
        "color" => "");
    $keywords = array();
    if (strpos($brief, 'FATAL_ERROR') !== false) {
        $logLevel = "ERR";
        $keywords[] = array(
            'value' => '错误',
            'color' => '#ff0000');
    } else {
        $logLevel = "WAR";
        $keywords[] = array(
            "value" => '警告',
            "color" => "#aa0808");
    }
    $keywords[] = array(
        "value" => $brief,
        "color" => "#333");
    $remark = '';
    $body = WxTemplateService::createTemplateContent($first, $keywords, $remark);

    $openid = '';
    list ($env, $user) = explode('-', $data['env']);
    $envvv = $env == '[线上' ? 'prod' : 'dev';
    $date = date('Ymd', $data['time']);
    $url = sprintf('http://opp.fangcunhulian.cn/log/index?logid=%s&env=%s&user=%s&date=%s', $data['unitofworkid'], $envvv, $user, $date);
    if ($user) {
        $openid = $openids[$user];
    }
    if ($env == '[线上' || ! $openid) {
        foreach ($openids as $openid) {
            $wxuser = WxUserDao::getByOpenid($openid);
            if ($wxuser->isOpsOpen()) {
                $errcode = WxApi::kefuTplMsg($wxshop, $openid, $template_id, $url, $body);
            }
        }
    } else {
        $errcode = WxApi::kefuTplMsg($wxshop, $openid, $template_id, $url, $body);
    }

    try {
        xerrlog($data['unitofworkid'], $logLevel, $brief);
    } catch (Exception $ex) {}

    return true;
}

function send_dingding_warning_msg ($data) {
    $mobiles = [
        'chenshigang' => '13227791329',  // 10022 陈士岗
        'taoxiaojin' => '18701503285',  // 10004 乔涛金
        'shijianping' => '18611820612',  // 10007 史建平
        'lijie' => '18311374180',  // 10014 李杰
        'fanghanwen' => '17762745826',  // 10013 方汉文
        'likunting' => '18665504922' // 10052 李琨亭
];
    $brief = $data['brief'];
    $pattern = '/\\[\\d{2}m(.*?)\\[m/i';
    $brief = preg_replace($pattern, '$1', $brief);
    $isAtAll = false;

    $date = date('Ymd', $data['time']);
    $datetime = date('Y-m-d H:i:s', $data['time']);
    list ($env, $user) = explode('-', $data['env']);
    $envvv = $env == '[线上' ? 'prod' : 'dev';

    $atMobiles = [];
    if ($user) {
        $atMobiles[] = $mobiles[$user];
    }
    if ($env == '[线上' || ! $atMobiles) {
        $isAtAll = true;
    }

    if (strpos($brief, 'FATAL_ERROR') !== false) {
        $level = '错误';
    } else {
        $level = '警告';
    }
    $url = sprintf('http://tool.fangcunhulian.cn/?logid=%s&env=%s&user=%s&date=%s', $data['unitofworkid'], $envvv, $user, $date);
    $text = "#### {$data['env']}\n";
    $text .= "##### 类型: {$level}\n";
    $text .= "##### ID：{$data['unitofworkid']} \n";
    $text .= "> {$brief}\n";
    $text .= " ";
    $text .= "##### {$datetime} [详情]({$url})\n";

    $content = [
        'msgtype' => 'markdown',
        'markdown' => [
            'title' => '报警通知',
            'text' => $text],
        'at' => [
            'atMobiles' => $atMobiles,
            'isAtAll' => $isAtAll]];
    file_put_contents('/tmp/dingding', print_r($content, true), FILE_APPEND);
    $ddurl = 'https://oapi.dingtalk.com/robot/send?access_token=5b989eb4b95d16a598dca455bb4863066403dc7442d75f6bdd0343b822da7672';
    $data = json_encode($content);
    file_put_contents('/tmp/dingding', $data, FILE_APPEND);
    $headers = [
        'Content-Type: application/json;charset=utf-8'];
    $ret = FUtil::curlPost($ddurl, $data, 5, $headers);
}

// 错误日志入库
function xerrlog ($xunitofworkid, $logLevel, $content) {
    if (Debug::$xunitofwork_create_close) {
        return false;
    }

    if (false == Config::getConfig("xworkdbOpen", false)) {
        return false;
    }

    $randno = XUnitOfWork::getTablenoByXunitofworkid($xunitofworkid);

    $row = array();
    $row["randno"] = $randno;
    $row["xunitofworkid"] = $xunitofworkid;
    $row["level"] = $logLevel;
    $row["content"] = $content;

    $dbconf = [];
    $dbconf['database'] = 'xworkdb';
    $entity = Xerrlog::createByBiz($row, $dbconf);

    $dbExe = BeanFinder::get("DbExecuter", 'xworkdb');
    $sqls = $entity->getInsertCommand();
    foreach ($sqls as $a) {
        $sql = $a['sql'];
        $param = $a['param'];
        $dbExe->executeNoQuery($sql, $param);
    }

    return true;
}

if ($argc < 2) {
    echo "usage: php " . basename(__FILE__) . " jsonData\n";
    exit(1);
}

$jsonData = $argv[1];
$data = json_decode($jsonData, true);
if (! $data) {
    echo "data($jsonData) is not a valid json\n";
    exit(2);
}
send_warning_msg($data);
//send_dingding_warning_msg($data);

//Debug::flushXworklog();
