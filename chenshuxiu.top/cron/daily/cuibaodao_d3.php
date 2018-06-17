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
//每天晚上18:10发送脚本
//发送给7天及以上的扫码关注但没报到的用户催报到
//每周发一次，一直按周发下去
class CuiBaodao_d3
{
    public function getConfig () {
        $starttime = "2015-04-01";
        $endtime = date("Y-m-d",strtotime("-6 day"));
        $config = array(
            "starttime" => $starttime,
            "endtime" => $endtime,
            "typestr" => "cuibaodao[d3]",
            "content" => "发送给7天及以上的扫码关注但没报到的用户催报到"
        );
        return $config;
    }

    public function filter($wxuser, $typestr=""){
        if( false == $wxuser instanceof WxUser ){
            return true;
        }
        $createtime = $wxuser->createtime;
        $createtime_begin = date("Y-m-d", strtotime($createtime));
        $today_begin = date("Y-m-d", time());

        $day_diff = XDateTime::getDateDiff($today_begin, $createtime_begin);
        if( $day_diff%7 == 0 ){
            return false;
        }else{
            return true;
        }
    }

    public function getSendContent($wxuser){
        $content = "";
        if( $wxuser instanceof WxUser ){
            $doctor = $wxuser->doctor;
            $wx_uri = Config::getConfig("wx_uri");
            $baodao_url = "<a href=\"{$wx_uri}/baodao/baodao?openid={$wxuser->openid}\">『报到』</a>";
            $arr = array(
                '#baodao_url#' => $baodao_url,
            );

            $content = MsgTemplateService::getMsgContentByWxUserEname($wxuser, 'cuibaodao_d3', $arr);
            if( $doctor instanceof Doctor && $doctor->isHezuo("Lilly")){
                $content = MsgTemplateService::getMsgContentByWxUserEname($wxuser, 'lilly_cuibaodao_d3', $arr);
            }
        }
        return $content;
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][CuiBaodao_d3.php]=====");

$a = new CuiBaodao_d3();
$cuibaodaobase = new CuiBaodaoBase($a);
$cuibaodaobase->dowork();

Debug::trace("=====[cron][end][CuiBaodao_d3.php]=====");
Debug::flushXworklog();

echo "\n-----end----- " . XDateTime::now();
