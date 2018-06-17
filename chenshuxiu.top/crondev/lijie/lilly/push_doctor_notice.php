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

class Push_doctor_notice
{
    public function dopush ($doctor_hezuo_name) {

        $unitofwork = BeanFinder::get("UnitOfWork");

        //第一个合作患者入组时间为起始时间，每隔两周向礼来接口推送提醒消息；
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

        $this->sendmsg($doctor_hezuo);
        echo "\n====[{$doctor_hezuo->name}]========执行成功！！！" . XDateTime::now();

        $unitofwork->commitAndInit();
    }

    public function sendmsg ($doctor_hezuo) {
        //给礼来接口推送提醒消息
        $date = date("Y-m-d");
        $cnt = Patient_hezuoDao::getCntByCompanyDoctorid("Lilly", $doctor_hezuo->doctorid);

        if(0 != $cnt){
            $cntstr = $cnt."位";
        }else {
            $cntstr = "无报到患者";
        }

        $content = "{first: '您的患者报到情况如下：',keywords: ['{$date}', '{$cntstr}'],remark: '您可以进入患者管理查看详情，感谢您的辛勤工作！'}";

        Debug::trace("{$content}");
        Debug::trace("{$doctor_hezuo->doctor_code}");
        $lillyservice = new LillyService();
        $send_status = $lillyservice->sendTemplate(2, $doctor_hezuo->doctor_code, $content);
        echo "\n\n-----发送消息返回状态：--{$send_status}--- ";
    }

}

// //////////////////////////////////////////////////////

$process = new Push_doctor_notice(__FILE__);
$process->dopush($doctor_hezuo_name);
Debug::flushXworklog();
