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
$pos = $argv[2];

class Lilly_doctor_survey
{
    public function dopush ($doctor_hezuo_name, $pos) {

        $unitofwork = BeanFinder::get("UnitOfWork");

        //发送合作医生满意度调查问卷；
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

        if($pos ==1){
            //2月链接
            $url = "http://survey.decipherinc.com/survey/selfserve/53b/170531";
            $this->sendmsg($doctor_hezuo, "第一次", $url);
        }else {
            //5月链接
            $url = "http://survey.decipherinc.com/survey/selfserve/53b/170532";
            $this->sendmsg($doctor_hezuo, "第二次", $url);
        }

        echo "\n====[{$doctor_hezuo->name}]========执行完成！！！" . XDateTime::now();

        $unitofwork->commitAndInit();
    }

    public function sendmsg ($doctor_hezuo, $numberStr, $url) {
        //给礼来接口推送提醒消息
        $date = date("Y-m-d");
        $str = "在我们所做的服务和您满意的服务之间，差的只是您的意见，请点击『详情』告诉我们，您希望的服务的样子！";

        $content = "{first: '向日葵关爱行动服务调查',keywords: ['{$numberStr}', '{$str}', '{$date}'],remark: ''}";
        $lillyservice = new LillyService();
        $send_status = $lillyservice->sendTemplate(3, $doctor_hezuo->doctor_code, $content, $url);
        echo "\n\n-----发送消息返回状态：--{$send_status}--- ";
    }

}

// //////////////////////////////////////////////////////

$process = new Lilly_doctor_survey(__FILE__);
$process->dopush($doctor_hezuo_name, $pos);
Debug::flushXworklog();
