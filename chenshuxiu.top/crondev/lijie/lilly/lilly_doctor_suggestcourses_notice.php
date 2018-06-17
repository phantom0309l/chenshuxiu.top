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

class Lilly_doctor_suggestcourses_notice
{
    public function dopush () {
        $sql = " select id from doctor_hezuos where status=1 and is_suggest_courses=0 ";
        $ids = Dao::queryValues($sql);
        $i = 0;

        foreach ($ids as $id) {
            $doctor_hezuo = Doctor_hezuo::getById($id);

            $this->sendmsg($doctor_hezuo);
            echo "\n====id[{$id}]===" . XDateTime::now();
        }
    }

    public function sendmsg ($doctor_hezuo) {
        //给礼来接口推送提醒消息
        $date = date("Y-m-d");

        $content = "{first: '行为训练课程介绍，请为家长勾选行为训练课程',keywords: ['{$date}', '1'],remark: '感谢您的辛勤工作！'}";

        Debug::trace("{$content}");
        Debug::trace("{$doctor_hezuo->doctor_code}");
        $lillyservice = new LillyService();
        $send_status = $lillyservice->sendTemplate(2, $doctor_hezuo->doctor_code, $content);
        echo "\n\n-----发送消息返回状态：--{$send_status}--- ";
    }

}

// //////////////////////////////////////////////////////

$process = new Lilly_doctor_suggestcourses_notice(__FILE__);
$process->dopush();
Debug::flushXworklog();
