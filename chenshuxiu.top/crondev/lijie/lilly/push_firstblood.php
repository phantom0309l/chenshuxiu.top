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
$doctor_hezuo_name = $argv[1];
$patient_name = $argv[2];

class Push_firstblood
{
    public function dopush ($doctor_hezuo_name, $patient_name) {

        $unitofwork = BeanFinder::get("UnitOfWork");

        //给合作医生推送“一血”模版；
        $cond = " and name='{$doctor_hezuo_name}' ";
        $doctor_hezuo = Dao::getEntityByCond("Doctor_hezuo", $cond);

        if (false == $doctor_hezuo instanceof Doctor_hezuo) {
            echo "\n====[{$doctor_hezuo_name}]===不存在==" . XDateTime::now();
            return;
        }

        if ($doctor_hezuo->status != 1) {
            echo "\n====该医生未开通===" . XDateTime::now();
            return;
        }

        $this->sendmsg($doctor_hezuo, $patient_name);
        echo "\n====[{$doctor_hezuo->name}]========发送成功！！！" . XDateTime::now();

        $unitofwork->commitAndInit();
    }

    public function sendmsg ($doctor_hezuo,$patient_name) {
        //给礼来接口推送提醒消息
        $date = date("Y-m-d");
        $content = "{first: '您好，您有一位患者',keywords: ['{$patient_name}', '{$date}'],remark: '点此查看'}";
        $lillyservice = new LillyService();
        $send_status = $lillyservice->sendTemplate(1, $doctor_hezuo->doctor_code, $content);
        echo "\n\n-----发送消息返回状态：--{$send_status}--- ";
    }

}

// //////////////////////////////////////////////////////

$process = new Push_firstblood(__FILE__);
$process->dopush($doctor_hezuo_name, $patient_name);
Debug::flushXworklog();
