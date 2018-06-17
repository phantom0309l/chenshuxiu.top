<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// Debug::$debug = 'Dev';

class Fix_voice
{
    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $wxuser = WxUser::getById(566365956);
        $wxshop = WxShop::getById($wxuser->wxshopid);
        $access_token = $wxshop->getAccessToken();
        $media_id = 'CZIHskWYsI6ri0Ye2BF4tgVNnQNkpUK-hITRD-BotCbWSDnOc3f38DzOkXWNMwAE';
        $downloadurl = "https://api.weixin.qq.com/cgi-bin/media/get?access_token={$access_token}&media_id={$media_id}";

        $wxvoicemsg = WxVoiceMsg::getById(568625446);
        $wxvoicemsg->downloadurl = $downloadurl;

        $voice = Voice::createByFetch($wxvoicemsg->downloadurl, $wxvoicemsg->wxuserid);
        $wxvoicemsg->voiceid = $voice->id;
        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Fix_voice.php]=====");

$process = new Fix_voice();
$process->dowork();

Debug::trace("=====[cron][end][Fix_voice.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
