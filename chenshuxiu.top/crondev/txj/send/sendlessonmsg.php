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

$unitofwork = BeanFinder::get("UnitOfWork");

function sendmsg ($patientid, $userid, $content) {
    // 得到模板内容
    $patient = Patient::getById($patientid);
    $wxuser = $patient->getMasterWxUser();
    $doctor = $patient->doctor;
    if (! empty($patient) && ! empty($wxuser) && ! empty($doctor)) {
        $doctorname = $doctor->name;
        $doctorid = $doctor->id;
        $str = "医生助理";
        $openid = $wxuser->openid;

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
        $tplid = WxTemplate::getTemplateid($wxuser, "adminNotice");
        WechatMsg::send_template($userid, $patientid, $openid, $tplid, $content, $url = "http://wx.fangcunyisheng.com/lesson/lxgc?openid={$openid}", 1,
                $doctorid, $objtype = 'LessonMsg');
    }
}

$cond = " AND status=1 and subscribe_cnt>0 ";
$patients = Dao::getEntityListByCond("Patient", $cond);
$content = "很多家长反馈不知道如何观察疗效，监控药物副作用，为此我们准备了相关学习知识，点击【家长须知】-【疗效观察】查看

几点说明：
1. 其中的内容会应家长需求不断完善
2. 系统会每周提醒家长学习
3. 此外，我们每周会给家长们推送一篇文章，主要内容是治疗过程中家长普遍遇到的一些问题

我们期望能协助您在家更好地管理孩子。
";
foreach ($patients as $a) {
    $patientid = $a->id;
    $users = $a->getUsers();
    foreach ($users as $user) {
        sendmsg($patientid, $user->id, $content);
        echo "\n";
        echo "====[{$patientid}]===";
    }

}

$unitofwork->commitAndInit();
