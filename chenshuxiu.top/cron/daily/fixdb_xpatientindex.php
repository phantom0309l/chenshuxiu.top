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

class Fixdb_xpatientindex extends CronBase
{

    public $min_patientid = 0;

    // 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::dbfix;
        $row["when"] = 'daily';
        $row["title"] = '每天, 23:00 全量修正所有的xpatientindex ';
        return $row;
    }

    // 重载
    protected function needFlushXworklog () {
        return false;
    }

    // 重载
    protected function needCronlog () {
        return true;
    }

    // 重载
    public function doworkImp () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select id
            from patients
            where id > :min_patientid
            order by id limit 10000";

        $bind = [];
        $bind[':min_patientid'] = $this->min_patientid;

        $ids = Dao::queryValues($sql, $bind);

        $i = 0;
        $k = 0;
        foreach ($ids as $id) {
            $i ++;
            $patient = Patient::getById($id);

            // 更新
            XPatientIndex::updateAllXPatientIndex($patient);

            if ($i % 100 == 0) {
                $k += 100;
                echo $k . "/" . count($ids) . "\n {$id} ";
                $unitofwork->commitAndInit();
            } else {
                echo ".";
            }
        }

        echo "\n";

        $unitofwork->commitAndInit();
    }
}

// //////////////////////////////////////////////////////

$min_patientid = $argv[1];

if (empty($min_patientid)) {
    echo "\nplease input min_patientid, 格式: 123 \n\n";
    exit();
}

$process = new Fixdb_xpatientindex(__FILE__);
$process->min_patientid = $min_patientid;
$process->dowork($min_patientid);
