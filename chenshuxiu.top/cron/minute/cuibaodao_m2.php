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
//对大于等于47小时30分钟 小于48小时的扫码关注但没报到的用户催报到
//这个脚本每10分钟执行一次
class CuiBaodao_m2
{
    public function getConfig () {
        $lefttime = time() - 2*86400;
        $righttime = time() - (2*86400-1800);
        $starttime = date("Y-m-d H:i:s", $lefttime);
        $endtime = date("Y-m-d H:i:s", $righttime);
        $config = array(
            "starttime" => $starttime,
            "endtime" => $endtime,
            "typestr" => "cuibaodao[m2]",
            "content" => "对大于等于47小时30分钟 小于48小时的扫码关注但没报到的用户催报到"
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
            $wx_uri = Config::getConfig("wx_uri");
            $baodao_url = "<a href=\"{$wx_uri}/baodao/baodao?openid={$wxuser->openid}\">『报到』</a>";
            $arr = array(
                '#baodao_url#' => $baodao_url,
            );

            $content = MsgTemplateService::getMsgContentByWxUserEname($wxuser, 'cuibaodao_m2', $arr);
            if( $doctor instanceof Doctor && $doctor->isHezuo("Lilly")){
                $content = MsgTemplateService::getMsgContentByWxUserEname($wxuser, 'lilly_cuibaodao_m2', $arr);
            }
        }
        return $content;
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][CuiBaodao_m2.php]=====");

$a = new CuiBaodao_m2();
$cuibaodaobase = new CuiBaodaoBase($a);
$cuibaodaobase->dowork();

Debug::trace("=====[cron][end][CuiBaodao_m2.php]=====");
Debug::flushXworklog();

echo "\n-----end----- " . XDateTime::now();
