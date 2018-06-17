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

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][fill_base_msg.php]=====");

$ids = Dao::queryValues("select id from patients where sex=0 and birthday='0000-00-00' and status=1 and subscribe_cnt>0 ");
// $ids = Dao::queryValues("select id from patients where id=27");

foreach ($ids as $id) {
    $unitofwork = BeanFinder::get("UnitOfWork");
    $patient = Patient::getById($id);
    $user = $patient->getMasterUser();
    // 该服务应暂只有方寸儿童管理服务平台
    $wxuser = $user->getMasterWxUser();
    $doctor = $patient->doctor;
    echo "\n{$id}\n";
    if (! empty($patient) && ! empty($wxuser) && ! empty($doctor)) {
        $doctorname = $doctor->name;
        $doctorid = $doctor->id;
        $str = "医生助理";
        $openid = $wxuser->openid;

        $content = "{$patient->name}家长，请完善孩子的性别及年龄信息，以便{$doctorname}医生更准确的评估孩子的情况，给予指导建议。";

        $first = array(
            "value" => "",
            "color" => "");
        $keywords = array(
            array(
                "value" => $doctorname . $str,
                "color" => "#aaa"),
            array(
                "value" => $content,
                "color" => "#ff6600"));
        $content = WxTemplateService::createTemplateContent($first, $keywords);
        $appendarr = array(
            "doctorid" => $doctorid,
            "objtype" => "FillBaseMsg");
        $url = "http://wx.fangcunyisheng.com/notice/fill_base_msg?openid={$openid}";

        PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content, $url, $appendarr);
    }
    $unitofwork->commitAndInit();
}

Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
