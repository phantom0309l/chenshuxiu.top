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
//对当天[6,18)扫码关注但没报到的用户催报到
//触发条件：2小时后还没报到，且没发送过催报到消息的
//这个脚本每10分钟执行一次,有效执行时间是[8,20)点,非这个时间点直接返回
class CuiBaodao_m1
{
    public function getConfig () {
        $today_begin = date("Y-m-d", time());
        $starttime = $today_begin . " 06:00:00";
        $time = time()- 2*3600;
        $endtime = date("Y-m-d H:i:s", $time);
        $config = array(
            "starttime" => $starttime,
            "endtime" => $endtime,
            "typestr" => "cuibaodao[m1]",
            "content" => "对当天[6,18)扫码关注但没报到的用户催报到"
        );
        return $config;
    }

    public function filter($wxuser, $typestr=""){
        if( false == $wxuser instanceof WxUser ){
            return true;
        }
        //通过comment判断今天是不是已经催过了
        $comments = CommentDao::getListByObjtypeObjidTypestr("WxUser", $wxuser->id, $typestr);
        if( count($comments) > 0 ){
            return true;
        }else{
            return false;
        }
    }

    public function getSendContent($wxuser){
        $content = "";
        if( $wxuser instanceof WxUser ){
            $doctor = $wxuser->doctor;
            $content = MsgTemplateService::getMsgContentByWxUserEname($wxuser, 'cuibaodao_m1');
            if( $doctor instanceof Doctor && $doctor->isHezuo("Lilly") ){
                $content = MsgTemplateService::getMsgContentByWxUserEname($wxuser, 'lilly_cuibaodao_m1');
            }
        }
        return $content;
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][CuiBaodao_m1.php]=====");

$now = time();
$today_begin = date("Y-m-d", $now);
$time = strtotime($today_begin);
$lefttime = $time + 8*3600;
$righttime = $time + 20*3600 + 10*60;
if( $now < $lefttime || $now >= $righttime ){
    return;
}
$a = new CuiBaodao_m1();
$cuibaodaobase = new CuiBaodaoBase($a);
$cuibaodaobase->dowork();

Debug::trace("=====[cron][end][CuiBaodao_m1.php]=====");
Debug::flushXworklog();

echo "\n-----end----- " . XDateTime::now();
