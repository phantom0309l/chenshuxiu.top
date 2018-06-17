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

class Lilly_doctor_notice extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'daily';
        $row["title"] = '每天, 08:50 , 向礼来接口推送每两周给医生的提醒消息';
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

        //第一个合作患者入组时间为起始时间，每隔两周向礼来接口推送提醒消息；
        $sql = " select id from doctor_hezuos
        where status=1 and doctorid!=0 and SUBSTRING( BIN(can_send_msg), -1, 1)=1
        and first_patient_date!='0000-00-00' and datediff(now(), first_patient_date)%14=0
        and left(now(), 10)!=first_patient_date ";
        $ids = Dao::queryValues($sql, []);
        $i = 0;
        foreach ($ids as $id) {
            $i ++;
            if ($i >= 5) {
                $i = 0;
                echo "\n\n-----commit----- " . XDateTime::now();
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
            $doctor_hezuo = Doctor_hezuo::getById($id);

            $this->sendmsg($doctor_hezuo);
            echo "\n====id[{$id}]===" . XDateTime::now();
        }

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
        $lillyservice = new LillyService();
        $send_status = $lillyservice->sendTemplate(2, $doctor_hezuo->doctor_code, $content);
        echo "\n\n-----发送消息返回状态：--{$send_status}--- ";
    }

}

// //////////////////////////////////////////////////////

$process = new Lilly_doctor_notice(__FILE__);
$process->dowork();
