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

class Mgtgroup_lilly_init
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $now = date("Y-m-d H:i:s", time());
        $sql = "select id from patient_hezuos";
        $ids = Dao::queryValues($sql);
        $i = 0;
        foreach ($ids as $id) {
            $patient_hezuo = Patient_hezuo::getById($id);
            if($patient_hezuo instanceof Patient_hezuo){
                $patient = $patient_hezuo->patient;
                $mgtgrouptpl = MgtGroupTplDao::getByEname("lilly");

                //先判断该患者是否已有mgtgroup，已有跳过
                $mgtGroup = MgtGroupDao::getByPatientMgtGroupTpl($patient, $mgtgrouptpl);
                if($mgtGroup instanceof MgtGroup){
                    continue;
                }

                $status = $patient_hezuo->status;

                //生成mgtgroup
                $row = array();
                $row["wxuserid"] = $patient->createuser->createwxuserid;
                $row["userid"] = $patient->createuserid;
                $row["patientid"] = $patient->id;

                $row["mgtgrouptplid"] = $mgtgrouptpl->id;
                $row["objtype"] = get_class($patient_hezuo);
                $row["objid"] = $patient_hezuo->id;
                $row["startdate"] = $patient_hezuo->startdate;
                $row["enddate"] = $patient_hezuo->enddate;
                $row["status"] = $status;
                $cnt = MgtGroupDao::getCntByPatient($patient);
                $row["pos"] = $cnt + 1;
                MgtGroup::createByBiz($row);

                //设置所属管理组
                if($status == 1){
                    $patient->mgtgrouptplid = $mgtgrouptpl->id;
                }

            }

            $i ++;
            if ($i % 50 == 0) {
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
        }

        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Mgtgroup_lilly_init.php]=====");

$process = new Mgtgroup_lilly_init();
$process->dowork();

Debug::trace("=====[cron][end][Mgtgroup_lilly_init.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
