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

// 1.fixLastactivitydate + fixNextactivitydate + fixIsactivity
// 2.每天获取每一个患者的状态（得到基本数据）插入到rpt_patients表中
// 3.得到一天中所有患者的总汇总数据，插入到rpt_date_patients表中
class Rpt_patient_process extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::rpt;
        $row["when"] = 'daily';
        $row["title"] = '每天, 01:30 rpt_patient 数据报表汇总';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog () {
        return false;
    }

    // 是否记cronlog, 重载
    protected function needCronlog () {
        return true;
    }

    // 模板方法的实现, 重载
    public function doworkImp () {

        // $totime = strtotime('2015-04-09');
        $cronbegintime = XDateTime::now();

        $totime = strtotime(date('Y-m-d'));

        while ($totime < time()) {

            $todate = date("Y-m-d", $totime);

            // 往前翻一天
            $thetime = strtotime($todate) - 86400;
            $thedate = date("Y-m-d", $thetime);

            echo "\n======================\n";
            echo $todate;
            echo "\n======================\n";

            $rpt = Rpt_date_patientDao::getByThedate($thedate);

            if ($rpt instanceof Rpt_date_patient) {
                echo "\n repeate";
            } else {
                $this->doResetActivityDate();
                $this->doFixAllUserActivity($todate);
                $this->doRpt_patient($todate);
                $this->doRpt_date_patient($thedate);
            }

            $totime = $totime + 86400;
        }

        $unitofwork = BeanFinder::get("UnitOfWork");

        $unitofwork->commitAndInit();
    }

    // 有用药记录的
    private function doResetActivityDate () {
        $time = date("Y-m-d H:i:s");
        echo "\n{$time} ===doResetActivityDate===";
        $sql = " update patients set lastactivitydate='0000-00-00' , nextactivitydate = '0000-00-00' ";
        Dao::executeNoQuery($sql);
    }

    // fixLastactivitydate + fixNextactivitydate + fixIsactivity
    private function doFixAllUserActivity ($todate) {
        $time = date("Y-m-d H:i:s");
        echo "\n{$time} ===doFixAllUserLastactivitydate===";

        // 往前翻一天
        $totime = strtotime($todate);
        $thetime = $totime - 86400;
        $thedate = date("Y-m-d", $thetime);

        $unitofwork = BeanFinder::get("UnitOfWork");
        $bind = [];
        $bind[':todate'] = $todate;

        $sql = " select distinct a.id
            from patients a
            inner join pcards b on b.patientid=a.id
            where a.createtime < :todate and b.diseaseid = 1 and a.status=1 and a.subscribe_cnt>0 ";
        $ids = Dao::queryValues($sql, $bind);

        $cnt = count($ids);

        foreach ($ids as $i => $id) {
            $patient = Patient::getById($id);

            if ($i % 100 == 0) {
                $time = date("Y-m-d H:i:s");
                echo "\n{$time} {$i} / {$cnt} : {$id} : ";
            }
            echo ".";

            $patient->fixLastactivitydate($todate);
            $patient->fixNextactivitydate($todate);
            $patient->fixIsactivity($thedate); // 这里传的是前一天
                                               // echo " $thedate [ ";
                                               // echo
                                               // $patient->lastactivitydate;
                                               // echo " ";
                                               // echo
                                               // $patient->nextactivitydate;
                                               // echo " ] [ ";
                                               // echo $patient->isactivity;
                                               // echo " ] ";
            $unitofwork->commitAndInit();
            $unitofwork = BeanFinder::get("UnitOfWork");
        }
        $unitofwork->commitAndInit();
    }

    // 从业务系统中得到患者某天的状态，将数据插入到rpt_patients表中
    private function doRpt_patient ($todate) {
        $time = date("Y-m-d H:i:s");
        echo "\n{$time} ===doRpt_patient===";

        // 往前翻一天
        $totime = strtotime($todate);
        $begintime = $totime - 86400;
        $endtime = $begintime + 86400;

        $thedate = date("Y-m-d", $begintime);

        $unitofwork = BeanFinder::get("UnitOfWork");

        $bind = [];
        $bind[':todate'] = $todate;

        $sql = " select distinct a.id
            from patients a
            inner join pcards b on b.patientid=a.id
            where a.createtime < :todate and b.diseaseid = 1 ";

        $ids = Dao::queryValues($sql, $bind);

        $cnt = count($ids);

        $bind[':fromdate'] = $thedate;

        foreach ($ids as $i => $id) {
            $patient = Patient::getById($id);

            if ($i % 100 == 0) {
                $time = date("Y-m-d H:i:s");
                echo "\n{$time} {$i} / {$cnt} : {$id} : ";
            }
            echo ".";

            $row = array();
            $row["patientid"] = $patient->id;
            $row["doctorid"] = $patient->doctorid;
            $row["thedate"] = $thedate;
            $row["isbaodao"] = $patient->isBaodaoed();
            // medicinestr 已改成动态生成
            $row["medicinestr"] = $patient->getMedicinestr();
            $row["isactivity"] = $patient->isactivity;

            $row["isscan"] = $this->getIsscan($patient);
            $row["baodaodate"] = substr($patient->createtime, 0, 10);
            $row["patient_daycnt_lifecycle"] = $this->getLifecycleOfPatient($patient);
            $row["patient_status"] = $patient->status;

            $row["fbt_cnt"] = 0;

            $sql = " select count(*) from drugitems
                where left(createtime, 10)='{$thedate}' and patientid='{$id}'
                    and medicineid>0 and type=1 ";

            $row["drugitem_cnt"] = Dao::queryValue($sql);

            $row["drug_status"] = $this->getDrugstatusByPatientThedate($patient);

            // 以下数据统计的是当日数据
            $sql = " select count(*) from pipes
                where createtime >= :fromdate and createtime < :todate
                    and patientid='{$id}' and subdomain='wx' ";

            $row["pipe_cnt"] = Dao::queryValue($sql, $bind);

            $pipedataArr = $this->getPipeData($id, $bind);

            $wxpicmsg_cnt = 0;
            $wxtxtmsg_cnt = 0;
            $wxvoicemsg_cnt = 0;
            $answersheet_cnt = 0;
            $patientnote_cnt = 0;
            $paper_cnt = 0;
            $lessonuserref_hwk_cnt = 0;
            $lessonuserref_test_cnt = 0;
            $comment_share_cnt = 0;
            if (count($pipedataArr) > 0) {
                foreach ($pipedataArr as $item) {
                    if ($item["objtype"] == "WxPicMsg") {
                        $wxpicmsg_cnt = $item["cnt"];
                        continue;
                    }
                    if ($item["objtype"] == "WxTxtMsg") {
                        $wxtxtmsg_cnt = $item["cnt"];
                        continue;
                    }
                    if ($item["objtype"] == "WxVoiceMsg") {
                        $wxvoicemsg_cnt = $item["cnt"];
                        continue;
                    }
                    if ($item["objtype"] == "Paper" && $item["objcode"] == "scale") {
                        $answersheet_cnt = $item["cnt"];
                        continue;
                    }
                    if ($item["objtype"] == "PatientNote") {
                        $patientnote_cnt = $item["cnt"];
                        continue;
                    }
                    if ($item["objtype"] == "Paper") {
                        $paper_cnt = $item["cnt"];
                        continue;
                    }
                    if ($item["objtype"] == "LessonUserRef" && $item["objcode"] == "hwk") {
                        $lessonuserref_hwk_cnt = $item["cnt"];
                        continue;
                    }
                    if ($item["objtype"] == "LessonUserRef" && $item["objcode"] == "test") {
                        $lessonuserref_test_cnt = $item["cnt"];
                        continue;
                    }
                    if ($item["objtype"] == "Comment" && $item["objcode"] == "share") {
                        $comment_share_cnt = $item["cnt"];
                        continue;
                    }
                }
            }

            $row["wxpicmsg_cnt"] = $wxpicmsg_cnt;
            $row["wxtxtmsg_cnt"] = $wxtxtmsg_cnt;
            $row["wxvoicemsg_cnt"] = $wxvoicemsg_cnt;
            $row["answersheet_cnt"] = $answersheet_cnt;
            $row["patientnote_cnt"] = $patientnote_cnt;
            $row["paper_cnt"] = $paper_cnt;
            $row["lessonuserref_hwk_cnt"] = $lessonuserref_hwk_cnt;
            $row["lessonuserref_test_cnt"] = $lessonuserref_test_cnt;
            $row["comment_share_cnt"] = $comment_share_cnt;

            $sql1 = " select count(*)
                from lessonuserrefs
                where createtime >= :fromdate and createtime < :todate
                    and patientid='{$id}' and readtime != '0000-00-00 00:00:00' ";

            $row["lxgc_all"] = Dao::queryValue($sql1, $bind);

            $row["lxgc_test_cnt"] = Dao::queryValue($sql . " and objtype ='LessonUserRef' and objcode = 'test' ", $bind);
            $row["lxgc_hwk_cnt"] = Dao::queryValue($sql . " and objtype ='LessonUserRef' and objcode = 'hwk' ", $bind);
            $row["lastactivitydate"] = $patient->lastactivitydate;
            $row["nextactivitydate"] = $patient->nextactivitydate;
            $entity = Rpt_patient::createByBiz($row);
            $unitofwork->commitAndInit();
            $unitofwork = BeanFinder::get("UnitOfWork");
        }
        $unitofwork->commitAndInit();
    }

    // 取得患者是否扫码
    private function getIsscan (Patient $patient) {
        $isscan = 0;
        $sql = " SELECT a.*
                FROM wxusers a
                INNER JOIN users b ON a.userid=b.id
                WHERE a.wx_ref_code!='' AND a.ref_objtype='Doctor'
                AND b.patientid = :patientid ";

        $bind = [];
        $bind[':patientid'] = $patient->id;

        $wxuser = Dao::loadEntity("WxUser", $sql, $bind);

        if ($wxuser instanceof WxUser) {
            $isscan = 1;
        }

        return $isscan;
    }

    // 天数 = 取消关注日期—报到日期
    private function getLifecycleOfPatient (Patient $patient) {
        $sql = " SELECT
              (unix_timestamp(c.unsubscribe_time)-unix_timestamp(a.createtime))  as subtime
            FROM patients a
            INNER JOIN users b ON b.patientid = a.id
            INNER JOIN wxusers c ON c.userid = b.id
            WHERE a.id=:patientid ";

        $bind = [];
        $bind[':patientid'] = $patient->id;

        $subtime = Dao::queryValue($sql, $bind);

        if ($subtime >= 0) {
            // floor向下舍入最接近的整数，取关时间与报到时间间隔小于24小时，记 $patientdaycnt 为0
            $lifecycle = floor($subtime / 86400);
        } else {
            $lifecycle = - 1;
        }

        return $lifecycle;
    }

    // 得到某位患者执行脚本时的用药状态
    private function getDrugstatusByPatientThedate (Patient $patient) {
        // 用药状态,0:无填写记录，1：用药，2：不服药，3：停药
        $status = 0;

        $cond = " AND patientid = :patientid ";

        $bind = [];
        $bind[':patientid'] = $patient->id;

        $refs = Dao::getEntityListByCond("PatientMedicineRef", $cond, $bind);

        $cnt = count($refs);
        if ($cnt > 0) {
            foreach ($refs as $k => $ref) {
                if ($ref->medicineid > 0 && $ref->status > 0) {
                    $status = 1;
                    break;
                }
                if ($cnt == $k + 1 && $ref->medicineid == 0) {
                    $status = 3;
                    break;
                }
            }
            if ($status == 0 && $cnt == 1) {
                $status = 2;
            }
        }

        return $status;
    }

    // 得到某位患者一段时间内的pipe类型 及 对应的数量
    private function getPipeData ($patientid, $bind) {
        $sql = " SELECT objtype, objcode, count(*) as cnt
             FROM pipes
             where createtime >= :fromdate and createtime < :todate and patientid='{$patientid}'
             GROUP BY objtype, objcode ";

        return Dao::queryRows($sql, $bind);
    }

    // 最后一次疗效评估,当前是否服药=否
    private function doRpt_date_patient ($thedate) {
        $time = date("Y-m-d H:i:s");
        echo "\n{$time} ===doRpt_date_patient===";

        echo " [{$thedate}] ";

        $unitofwork = BeanFinder::get("UnitOfWork");
        $bind = [];
        $bind[':thedate'] = $thedate;

        // 测试和韩颖
        $ids = Doctor::getTestDoctorIdStr();
        $str = " from statdb.rpt_patients where isbaodao=1 and ( doctorid not in ({$ids}) and doctorid > 0 and doctorid < 10000 ) and thedate = :thedate ";
        // $str = " from rpt_patients where isbaodao=1 and thedate = :thedate ";

        $row = array();
        $row["thedate"] = $thedate;
        $row["allcnt"] = Dao::queryValue("select count(*) from rpt_patients where thedate = :thedate  ", $bind, 'statdb');
        $row["sumcnt0"] = Dao::queryValue("select count(*) $str  ", $bind, 'statdb');
        $row["yes_sumcnt0"] = Dao::queryValue(
                "select count(*)
                 $str
                 and exists( select *
                    from patientmedicinerefs
                    where patientid = statdb.rpt_patients.patientid and medicineid in (2,3) and status = 1)", $bind);
        $row["yes_zsd_cnt0"] = Dao::queryValue(
                "select count(*)
                $str
                and exists(select *
                    from patientmedicinerefs
                    where patientid = statdb.rpt_patients.patientid and medicineid = 2 and status = 1)", $bind);
        $row["yes_zzd_cnt0"] = Dao::queryValue(
                "select count(*)
                $str
                and exists(select *
                    from patientmedicinerefs
                    where patientid = statdb.rpt_patients.patientid and medicineid = 3 and status = 1)", $bind);

        $row["sumcnt1"] = Dao::queryValue("select count(*) $str AND isactivity=1 ", $bind);
        $row["yes_sumcnt1"] = Dao::queryValue(
                "select count(*)
                $str AND isactivity=1
                and exists(select *
                    from patientmedicinerefs
                    where patientid = statdb.rpt_patients.patientid and medicineid in (2,3) and status = 1)", $bind);
        $row["yes_zsd_cnt1"] = Dao::queryValue(
                "select count(*)
                $str AND isactivity=1
                and exists(select *
                    from patientmedicinerefs
                    where patientid = statdb.rpt_patients.patientid and medicineid = 2 and status = 1)", $bind);
        $row["yes_zzd_cnt1"] = Dao::queryValue(
                "select count(*)
                $str AND isactivity=1
                and exists(select *
                    from patientmedicinerefs
                    where patientid = statdb.rpt_patients.patientid and medicineid = 3 and status = 1)", $bind);

        $row["pipe_sumcnt"] = 0 + Dao::queryValue("select sum(pipe_cnt) $str  ", $bind);
        $row["wxpicmsg_sumcnt"] = 0 + Dao::queryValue("select sum(wxpicmsg_cnt) $str  ", $bind);
        $row["wxtxtmsg_sumcnt"] = 0 + Dao::queryValue("select sum(wxtxtmsg_cnt) $str  ", $bind);
        $row["answersheet_sumcnt"] = 0 + Dao::queryValue("select sum(answersheet_cnt) $str  ", $bind);
        $row["patientnote_sumcnt"] = 0 + Dao::queryValue("select sum(patientnote_cnt) $str  ", $bind);
        $row["fbt_sumcnt"] = 0 + Dao::queryValue("select sum(fbt_cnt) $str  ", $bind);
        $row["pipe_pcnt"] = Dao::queryValue("select count(*) $str AND pipe_cnt > 0  ", $bind);
        $row["wxpicmsg_pcnt"] = Dao::queryValue("select count(*) $str AND wxpicmsg_cnt > 0 ", $bind);
        $row["wxtxtmsg_pcnt"] = Dao::queryValue("select count(*) $str AND wxtxtmsg_cnt > 0 ", $bind);
        $row["answersheet_pcnt"] = Dao::queryValue("select count(*) $str AND answersheet_cnt > 0 ", $bind);
        $row["patientnote_pcnt"] = Dao::queryValue("select count(*) $str AND patientnote_cnt > 0 ", $bind);
        $row["fbt_pcnt"] = Dao::queryValue("select count(*) $str AND fbt_cnt > 0 ", $bind);

        $row["lxgc_sumall"] = 0 + Dao::queryValue("select sum(lxgc_all) $str  ", $bind);
        $row["lxgc_test_sumcnt"] = 0 + Dao::queryValue("select sum(lxgc_test_cnt) $str  ", $bind);
        $row["lxgc_hwk_sumcnt"] = 0 + Dao::queryValue("select sum(lxgc_hwk_cnt) $str  ", $bind);

        $row["lxgc_pall"] = 0 + Dao::queryValue("select count(*) $str AND lxgc_all > 0  ", $bind);
        $row["lxgc_test_pcnt"] = 0 + Dao::queryValue("select count(*) $str AND lxgc_test_cnt > 0  ", $bind);
        $row["lxgc_hwk_pcnt"] = 0 + Dao::queryValue("select count(*) $str AND lxgc_hwk_cnt > 0 ", $bind);

        Rpt_date_patient::createByBiz($row);

        $unitofwork->commitAndInit();
    }
}

// //////////////////////////////////////////////////////

$process = new Rpt_patient_process(__FILE__);
$process->dowork();
