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

class ScaleNotice
{

    protected static $wcnt = -1;
    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select distinct a.id
                from patients a
                inner join pcards b on b.patientid = a.id
                where a.status = 1 and a.subscribe_cnt > 0 and b.status = 1 and b.diseaseid = 1";
        $ids = Dao::queryValues($sql);
        $i = 0;

        foreach ($ids as $id) {
            $i ++;
            if ($i >= 5) {
                $i = 0;
                echo "\n\n-----commit----- " . XDateTime::now();
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
            $patient = Patient::getById($id);
            if ($patient instanceof Patient) {
                if (false == $this->canSendMsg($patient)) {
                    continue;
                }

                $this->closedUndoPatientTask($patient);
                $this->createPatientTasks($patient);
                $this->sendmsg($patient);
                //生成optask任务
                $wcnt = self::$wcnt;
                echo "\n====wcnt[{$wcnt}]===";
                if( $wcnt == 1 || $wcnt == 4 ){
                    OpTaskService::createPatientOpTask($patient, 'baseScale:');
                }
                echo "\n====id[{$id}]===" . XDateTime::now();
            }
        }

        $unitofwork->commitAndInit();
    }

    private function isDoneDrugSheet($patient, $fromcreatetime){
        $leftdate = date("Y-m-d", strtotime($fromcreatetime));
        $rightdate = date("Y-m-d", time());
        $drugsheets = DrugSheetDao::getListByPatientid($patient->id, " and thedate >= '{$leftdate}' and thedate < '{$rightdate}'");
    }

    private function closedUndoPatientTask($patient){
        $cond = " and status=0 and patientid = :patientid";
        $bind = [];
        $bind[":patientid"] = $patient->id;
        $patienttasks = Dao::getEntityListByCond("PatientTask", $cond, $bind);
        foreach ($patienttasks as $patienttask) {
            $patienttask->closedBySys();
        }
    }

    private function createPatientTasks($patient){
        $wxuser = $patient->createuser->masterWxUser;
        $doctor = $patient->getMasterDoctor();
        $diseaseid = $patient->diseaseid;

        if($doctor->isQiYuanLi() && $doctor instanceof Doctor){
            $papertpls = PaperTpl::getList_scale_QiYuanLi();
        }else{
            $papertpls = PaperTpl::getList_scale();
        }

        $row = array();
        foreach($papertpls as $papertpl){
            $row['wxuserid']=$wxuser->id;
            $row['userid']=$wxuser->userid;
            $row['patientid']=$patient->id;
            $row['doctorid']=$doctor->id;
            $row['diseaseid']=$diseaseid;
            $row['typestr']="PaperTpl";
            $row['tpl_objtype']="PaperTpl";
            $row['tpl_objid']=$papertpl->id;
            $row['objtype']="Paper";
            $row['objid']=0;
            $row['content']="患者填写评估量表";
            $row['status']=0;

            PatientTask::createByBiz($row);
        }
    }
    private function canSendMsg ($patient) {
        if (false == $patient->isUnderControl()) {
            return false;
        }
        // 距离报到1、2、4、8、12、16....周的患者发消息
        $today = date("Y-m-d H:i:s", time());
        $createtime = $patient->createtime;
        $diff = XDateTime::getDateDiff($createtime, $today);

        if ($diff % 7 != 0) {
            return false;
        }

        self::$wcnt = $w = $diff / 7;

        // 不到一周返回
        if ($w < 1) {
            return false;
        }

        // 1周和2周
        if ($w < 3) {
            return true;
        }

        // 4，8，12....周
        if ($w % 4 == 0) {
            return true;
        } else {
            return false;
        }
    }

    private function sendmsg ($patient) {
        $user = $patient->createuser;
        $wxuser = $user->createwxuser;
        if ($wxuser instanceof WxUser && 1 == $wxuser->wxshopid && 1 == $wxuser->subscribe) {
            $doctor_name = $patient->doctor->name;
            $str = "{$doctor_name}医生助理";
            $content = $this->getSendContent($patient);
            $first = array(
                "value" => "",
                "color" => "#ff6600");
            $keywords = array(
                array(
                    "value" => $str,
                    "color" => "#aaa"),
                array(
                    "value" => $content,
                    "color" => "#ff6600"));
            $content = WxTemplateService::createTemplateContent($first, $keywords);

            $openid = $wxuser->openid;
            $wx_uri = Config::getConfig("wx_uri");
            $url = $wx_uri . "/paper/index?openid={$openid}";

            PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content, $url);
        }
    }

    private function getSendContent ($patient) {
        $createtime = $patient->createtime;
        $diff = XDateTime::getDateDiff($createtime, date("Y-m-d H:i:s", time()));
        $patient_name = $patient->name;
        $doctor = $patient->doctor;
        $doctor_name = "";
        if ($doctor instanceof Doctor) {
            $doctor_name = $doctor->name;
        }
        $str = "{$patient_name}家长您好，今天是您开始ADHD诊后管理平台的第{$diff}天，为了更好的了解孩子的进步，{$doctor_name}医生助理需要督促您完成本次评估，点击本消息左下角的详情去完成评估任务吧！";
        return $str;
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][ScaleNotice.php]=====");

$process = new ScaleNotice();
$process->dowork();

Debug::trace("=====[cron][end][ScaleNotice.php]=====");
//Debug::flushXworklog(); // 不记日志
echo "\n-----end----- " . XDateTime::now();
