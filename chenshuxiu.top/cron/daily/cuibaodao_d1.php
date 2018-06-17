<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// Debug::$debug = 'Dev';
// 每天早上8:00发送脚本
// 对昨天18点(包括)到今天6点(不包括)的扫码关注但没报到的用户催报到
class CuiBaodao_d1
{

    public function getConfig () {
        $ytime = time() - 86400;
        $yesterday_begin = date("Y-m-d", $ytime);
        $today_begin = date("Y-m-d", time());
        $starttime = $yesterday_begin . " 18:00:00";
        $endtime = $today_begin . " 06:00:00";
        $config = array(
            "starttime" => $starttime,
            "endtime" => $endtime,
            "typestr" => "cuibaodao[d1]",
            "content" => "对昨天晚上六点到今天早上六点区间段的用户催报到");
        return $config;
    }

    public function getSendContent ($wxuser) {
        $content = "";
        if ($wxuser instanceof WxUser) {
            $doctor = $wxuser->doctor;
            $content = MsgTemplateService::getMsgContentByWxUserEname($wxuser, 'cuibaodao_d1');
            if( $doctor instanceof Doctor && $doctor->isHezuo("Lilly") ){
                $content = MsgTemplateService::getMsgContentByWxUserEname($wxuser, 'lilly_cuibaodao_d1');
            }
        }
        return $content;
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][CuiBaodao_d1.php]=====");

$a = new CuiBaodao_d1();
$cuibaodaobase = new CuiBaodaoBase($a);
$cuibaodaobase->dowork();

Debug::trace("=====[cron][end][CuiBaodao_d1.php]=====");
Debug::flushXworklog();

echo "\n-----end----- " . XDateTime::now();
