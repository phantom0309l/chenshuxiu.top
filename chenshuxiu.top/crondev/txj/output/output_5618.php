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

class Output_5618
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select a.id from patient_hezuos a
                    inner join patients b on b.id = a.patientid
                    where a.startdate < '2018-01-24' and b.is_test = 0 and b.doctorid not in (10,11)";

        $ids = Dao::queryValues($sql);
        $i = 0;
        $data = array();
        foreach ($ids as $id) {
            $i ++;
            if ($i >= 40) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
            echo "[{$id}]\n";
            $patient_hezuo = Patient_hezuo::getById($id);
            if( $patient_hezuo instanceof Patient_hezuo ){
                $patient = $patient_hezuo->patient;
                $temp = array();

                //patientid
                $temp[] = $patient->id;

                //patient_name
                $temp[] =$patient->name;

                //患者身上关注的微信个数
                $temp[] = $this->getWxUserCnt($patient);

                //入组日期
                $temp[] = $patient_hezuo->startdate;

                //当前出/入项目状态——（在项目中/已出项目）
                $temp[] = $patient_hezuo->getStatusStr();

                //患者呼入次数
                $temp[] = $this->getPatientCallInCnt($patient);

                //坐席呼出次数
                $temp[] = $this->getAuditorCallOutCnt($patient);


                //患者发文本消息的次数
                $txtmsg_cnt = $this->getPatientSendTxtMsgCnt($patient);
                $temp[] = $txtmsg_cnt;

                //患者发语音消息的次数
                $voicemsg_cnt = $this->getPatientSendVoiceMsgCnt($patient);
                $temp[] = $voicemsg_cnt;

                //患者发图片消息的次数
                $picmsg_cnt = $this->getPatientSendPicMsgCnt($patient);
                $temp[] = $picmsg_cnt;

                //患者发消息总次数
                $temp[] = $txtmsg_cnt + $voicemsg_cnt + $picmsg_cnt;

                //AE上报次数
                $temp[] = $this->getAEcnt($patient);

                //AEPC上报次数
                $temp[] = $this->getAEPCcnt($patient);

                //行为训练参加的课程数
                $temp[] = $this->getJoinPgroupCnt($patient);

                //系统提醒评估的次数
                $temp[] = $this->getSysNoticeCnt($patient);

                //系统提醒评估日期+7天有过量表记录的次数——（系统提醒评估日期+7天内，只要患者完成了任意一个量表，认为当次完成了评估）
                $temp[] = $this->getScaleThingsDoneCnt($patient);

                //患教文章推送次数
                $temp[] = $this->getPushArticleCnt($patient);

                //Survey推送次数
                $temp[] = $this->getSurveyCnt($patient);

                $data[] = $temp;
            }
        }
        $headarr = array(
            "patientid",
            "patient_name",
            "患者身上关注的微信个数",
            "入组日期",
            "当前出/入项目状态",
            "患者呼入次数",
            "坐席呼出次数",
            "患者发文本消息的次数",
            "患者发语音消息的次数",
            "患者发图片消息的次数",
            "患者发消息总次数",
            "AE上报次数",
            "AEPC上报次数",
            "行为训练参加的课程数",
            "系统提醒评估的次数",
            "系统提醒评估日期+7天有过量表记录的次数",
            "患教文章推送次数",
            "Survey推送次数",
        );
        ExcelUtil::createForCron($data, $headarr, "/home/taoxiaojin/scale/output_5618.xlsx");
        $unitofwork->commitAndInit();
    }

    private function getWxUserCnt($patient){
        $wxusers = WxUserDao::getListByPatient($patient);
        return count($wxusers);
    }

    private function getPatientCallInCnt($patient){
        $sql = "select count(*) as cnt from cdrmeetings where cdr_call_type=1 and patientid = :patientid";
        $bind = [];
        $bind[":patientid"] = $patient->id;
        $cnt = Dao::queryValue($sql, $bind);
        return $cnt;

    }

    private function getAuditorCallOutCnt($patient){
        $sql = "select count(*) as cnt from cdrmeetings where cdr_call_type=3 and patientid = :patientid";
        $bind = [];
        $bind[":patientid"] = $patient->id;
        $cnt = Dao::queryValue($sql, $bind);
        return $cnt;

    }

    private function getPatientSendTxtMsgCnt($patient){
        $sql = "select count(id) as cnt from wxtxtmsgs where patientid = :patientid";
        $bind = [];
        $bind[":patientid"] = $patient->id;
        $cnt = Dao::queryValue($sql, $bind);
        return $cnt;
    }

    private function getPatientSendVoiceMsgCnt($patient){
        $sql = "select count(id) as cnt from wxvoicemsgs where patientid = :patientid";
        $bind = [];
        $bind[":patientid"] = $patient->id;
        $cnt = Dao::queryValue($sql, $bind);
        return $cnt;
    }

    private function getPatientSendPicMsgCnt($patient){
        $sql = "select count(id) as cnt from wxpicmsgs where patientid = :patientid";
        $bind = [];
        $bind[":patientid"] = $patient->id;
        $cnt = Dao::queryValue($sql, $bind);
        return $cnt;
    }

    private function getAEcnt($patient){
        $cond = " and papertplid in (275143816)";
        $bind = [];

        $cond .= ' and patientid=:patientid ';
        $bind[':patientid'] = $patient->id;

        $sql = "select count(*) from papers where 1=1 {$cond}";
        return Dao::queryValue($sql, $bind) + 0;
    }

    private function getAEPCcnt($patient){
        $cond = " and papertplid in (312586776)";
        $bind = [];

        $cond .= ' and patientid=:patientid ';
        $bind[':patientid'] = $patient->id;

        $sql = "select count(*) from papers where 1=1 {$cond}";
        return Dao::queryValue($sql, $bind) + 0;
    }

    private function getJoinPgroupCnt($patient){
        $patientPgrouprefs = PatientPgroupRefDao::getListByPatientid($patient->id);
        return count($patientPgrouprefs);
    }

    private function getSysNoticeCnt($patient){
        $sql = "select
                count(*) as cnt
                from pushmsgs
                where patientid = :patientid and content like '%用药和孩子情况是治疗中的重要参考依据。为了解孩子用药和变化情况，请及时更新用药记录并完成症状评估。点击『详情』完成更新！%'";
        $bind = [];
        $bind[":patientid"] = $patient->id;
        $cnt = Dao::queryValue($sql, $bind);
        return $cnt;
    }

    private function getScaleThingsDoneCnt($patient){
        $sql = "select
                createtime
                from pushmsgs
                where patientid = :patientid and content like '%用药和孩子情况是治疗中的重要参考依据。为了解孩子用药和变化情况，请及时更新用药记录并完成症状评估。点击『详情』完成更新！%'";
        $bind = [];
        $bind[":patientid"] = $patient->id;
        $createtime_arr = Dao::queryValues($sql, $bind);
        $aaa = count($createtime_arr);
        $cnt = 0;
        foreach($createtime_arr as $createtime){
            $starttime = $createtime;
            $endtime = date("Y-m-d H:i:s", strtotime($createtime)+7*86400);
            $scale_cnt = $this->getScaleCntImp($patient, $starttime, $endtime);
            if($scale_cnt > 0){
                $cnt++;
            }
        }
        return $cnt;
    }

    private function getScaleCntImp($patient, $starttime, $endtime){
        $sql = "select
                    count(*)
                from papers where patientid = :patientid and papertplid not in (275143816, 312586776, 275209326)
                and createtime >= :starttime and createtime <= :endtime ";
        $bind = [];
        $bind[":patientid"] = $patient->id;
        $bind[":starttime"] = $starttime;
        $bind[":endtime"] = $endtime;
        $cnt = Dao::queryValue($sql, $bind) + 0;
        return $cnt;
    }

    private function getPushArticleCnt($patient){
        $sql = "select
                count(*) as cnt
                from pushmsgs
                where patientid = :patientid and content like '%多动症文章第%'";
        $bind = [];
        $bind[":patientid"] = $patient->id;
        $cnt = Dao::queryValue($sql, $bind);
        return $cnt;
    }

    private function getSurveyCnt($patient){
        $sql = "select
                count(*) as cnt
                from pushmsgs
                where patientid = :patientid and content like '%关爱中心服务调查%'";
        $bind = [];
        $bind[":patientid"] = $patient->id;
        $cnt = Dao::queryValue($sql, $bind);
        return $cnt;
    }



}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Output_5618.php]=====");

$process = new Output_5618();
$process->dowork();

Debug::trace("=====[cron][end][Output_5618.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
