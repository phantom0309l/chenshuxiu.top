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

print_r($argv);
// 外部传入的 wxvoicemsgid
$wxvoicemsgid = $argv[1];

echo "\n-----" . $wxvoicemsgid . "-----" . XDateTime::now();

class Fetch_wxvoicemsg
{

    public function dowork ($wxvoicemsgid) {

        $unitofwork = BeanFinder::get("UnitOfWork");

        // 拿到 $wxvoicemsg 实体
        $wxvoicemsg = WxVoiceMsg::getById($wxvoicemsgid);

        if(false ==  $wxvoicemsg instanceof WxVoiceMsg){
            echo "\n-----无效的 wxvoiceid-----" . XDateTime::now();
            return;
        }

        $downloadurl = $wxvoicemsg->downloadurl;
        // 截取到 media_id
        $media_id = substr($downloadurl, -64);
        $wxuser = $wxvoicemsg->wxuser;

        if(false == $wxuser instanceof WxUser){
            echo "\n-----wxuserid 为0-----" . XDateTime::now();
            return;
        }
        $wxshop = $wxuser->wxshop;
        $access_token = $wxshop->getAccessToken();

        $url = "https://api.weixin.qq.com/cgi-bin/media/get?access_token=" . $access_token . "&media_id=" . $media_id;

        $voice = Voice::createByFetch($url, $wxuser->id);
        $wxvoicemsg->voiceid = $voice->id;

        $unitofwork->commitAndInit();

        echo "\n-----  ok  -----" . XDateTime::now();

    }
}

echo "\n\n-----begin----- " . XDateTime::now();

$process = new Fetch_wxvoicemsg();
$process->dowork($wxvoicemsgid);

Debug::trace("=====[cron][end][fetch_wxvoicemsg.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
