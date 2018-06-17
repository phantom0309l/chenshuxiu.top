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

class Set_patient_drug_status extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::dbfix;
        $row["when"] = 'daily';
        $row["title"] = '每天, 23:40 更新患者的用药状态';
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

        $sql = " select id from patients where diseaseid=1 and status=1 ";
        $ids = Dao::queryValues($sql);
        $i = 0;
        foreach ($ids as $id) {
            echo "\n\nid[{$id}]";
            $i ++;
            if ($i >= 1000) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }

            $patient = Patient::getById($id);

            if($patient->isDruging()){
                $patient->drug_status=1;
                echo "\n更新服药状态--服药";
                continue;
            }

            if($patient->isNoDruging()){
                $patient->drug_status=2;
                echo "\n更新服药状态--不服药";
                continue;
            }

            if($patient->isStopDruging()){
                $patient->drug_status=3;
                echo "\n更新服药状态--停药";
                continue;
            }

        }

        $unitofwork->commitAndInit();
    }

}

// //////////////////////////////////////////////////////

$process = new Set_patient_drug_status(__FILE__);
$process->dowork();
