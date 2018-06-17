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

class Mgtgroup_pku6_init
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $now = date("Y-m-d H:i:s", time());
        $sql = "select id from patients where mgtplanid >0";
        $ids = Dao::queryValues($sql);
        $i = 0;
        foreach ($ids as $id) {
            $patient = Patient::getById($id);
            if($patient instanceof Patient){
                $doctorid = $patient->doctorid;
                $doctorid_arr = array(1, 2, 3, 537);
                if (false == in_array($doctorid, $doctorid_arr)) {
                    //已经不是六院的医生，不生成mgtgroup
                    echo "\npatientid[{$id}]已经不是六院的医生不生成mgtgroup\n";
                    continue;
                }

                $mgtgrouptpl = MgtGroupTplDao::getByEname("pkuh6");

                //先判断该患者是否已有mgtgroup，已有跳过
                $mgtGroup = MgtGroupDao::getByPatientMgtGroupTpl($patient, $mgtgrouptpl);
                if($mgtGroup instanceof MgtGroup){
                    continue;
                }

                //生成mgtgroup
                $row = array();
                $row["wxuserid"] = $patient->createuser->createwxuserid;
                $row["userid"] = $patient->createuserid;
                $row["patientid"] = $patient->id;
                $row["mgtgrouptplid"] = $mgtgrouptpl->id;
                $row["startdate"] = date("Y-m-d", strtotime($patient->createtime));
                $row["status"] = 1;
                $cnt = MgtGroupDao::getCntByPatient($patient);
                $row["pos"] = $cnt + 1;
                MgtGroup::createByBiz($row);

                //加入管理组
                $patient->mgtgrouptplid = $mgtgrouptpl->id;
                echo "\n====[{$i}][{$id}]==============\n";

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
Debug::trace("=====[cron][beg][Mgtgroup_pku6_init.php]=====");

$process = new Mgtgroup_pku6_init();
$process->dowork();

Debug::trace("=====[cron][end][Mgtgroup_pku6_init.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();