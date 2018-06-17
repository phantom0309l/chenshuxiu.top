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

class Lilly_doctor_survey extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'daily';
        $row["title"] = '每天, 09:05 发送合作医生满意度调查问卷';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog () {
        return true;
    }

    // 是否记cronlog, 重载
    protected function needCronlog () {
        return true;
    }

    // 模板方法的实现, 重载
    public function doworkImp () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = " select id
            from doctor_hezuos
            where status=1 and company='Lilly' and SUBSTRING(BIN(can_send_msg), -2, 1)=1
            and (datediff(now(), createtime)=56 or datediff(now(), createtime)=140) ";

        $ids = Dao::queryValues($sql);
        $i = 0;
        foreach ($ids as $id) {
            $i ++;
            if ($i >= 50) {
                $i = 0;
                echo "\n\n-----commit----- " . XDateTime::now();
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
            echo "\n====id[{$id}]===" . XDateTime::now();

            $doctor_hezuo = Doctor_hezuo::getById($id);
            $diff = $doctor_hezuo->getDayCntFromCreate();

            //患者报到第56天和140天发送满意度调查问卷；
            if($diff == 56){
                $url = "http://survey.decipherinc.com/survey/selfserve/53b/170531";
                $this->sendmsg($doctor_hezuo, "第一次", $url);
            }

            if($diff == 140){
                $url = "http://survey.decipherinc.com/survey/selfserve/53b/170532";
                $this->sendmsg($doctor_hezuo, "第二次", $url);
            }

        }

        $unitofwork->commitAndInit();
    }

    private function sendmsg ($doctor_hezuo, $numberStr, $url) {
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
$process->dowork();
