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

class Rpt_ppgroup_process extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::rpt;
        $row["when"] = 'daily';
        $row["title"] = '每天, 01:15 rpt_ppgroup 数据报表汇总';
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

        $cronbegintime = XDateTime::now();
        $ltdate = date("Y-m-d", time());
        $thedate = date("Y-m-d", time() - 86400);

        $a = Rpt_date_patientpgroupDao::getOneByThedate($thedate);
        if (false == $a instanceof Rpt_date_patientpgroup) {
            $pcnt = $this->getPcnt($ltdate);
            $pinpgroupcnt = $this->getPInpgroupcnt($ltdate);
            $ppgroupcnt = $this->getPpgroupcnt($ltdate);
            $overduecnt = $this->getOverduecnt($ltdate);
            $addcnt = $this->getAddcnt($thedate);

            $unitofwork = BeanFinder::get("UnitOfWork");
            $row = array();
            $row["thedate"] = $thedate;
            $row["pcnt"] = $pcnt;
            $row["ppgroupcnt"] = $ppgroupcnt;
            $row["pinpgroupcnt"] = $pinpgroupcnt;
            $row["needfollowcnt"] = 0;
            $row["overduecnt"] = $overduecnt;
            $row["addcnt"] = $addcnt;
            Rpt_date_patientpgroup::createByBiz($row);
            $unitofwork->commitAndInit();
        }
    }

    private function getPcnt ($ltdate) {
        $testpatientids = $this->getTestpatientids();
        return PatientDao::getPatientCnt(
                " and a.status=1 and a.subscribe_cnt>0 and b.diseaseid=1 and a.createtime < '{$ltdate}' and a.id not in ($testpatientids)");
    }

    private function getPInpgroupcnt ($ltdate) {
        $testpatientids = $this->getTestpatientids();
        return PatientPgroupRefDao::getPatientCnt(" and status=1 and diseaseid=1 and createtime < '{$ltdate}' and patientid not in ({$testpatientids})");
    }

    private function getPpgroupcnt ($ltdate) {
        $testpatientids = $this->getTestpatientids();
        return PatientPgroupRefDao::getPatientCnt(
                " and (status=1 or status=2) and diseaseid=1 and createtime < '{$ltdate}' and patientid not in ({$testpatientids})");
    }

    private function getOverduecnt ($ltdate) {
        $testpatientids = $this->getTestpatientids();
        return PatientPgroupRefDao::getPatientCntOverdue(
                "and a.status = 1 and a.diseaseid=1 and a.createtime < '{$ltdate}' and a.patientid not in ({$testpatientids})");
    }

    private function getAddcnt ($thedate) {
        $testpatientids = $this->getTestpatientids();
        return PatientPgroupRefDao::getPatientCnt(" and status =1 and startdate='{$thedate}' and diseaseid=1 and patientid not in ({$testpatientids})");
    }

    private function getTestpatientids () {
        $patientids_company = PatientDao::getIdsOfCompany();
        return implode(",", $patientids_company);
    }
}

// //////////////////////////////////////////////////////

$process = new Rpt_ppgroup_process(__FILE__);
$process->dowork();
