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

class Lilly_patient_autoout extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'daily';
        $row["title"] = '每天, 08:55 患者入组超过7*26天时，记录结束时间，推送项目结束通知；';
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

        $sql = "select id from patient_hezuos where status=1 and company='Lilly' and datediff(now(), createtime)>=7*26";

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

            $patient_hezuo = Patient_hezuo::getById($id);

            $patient = $patient_hezuo->patient;
            if(false == $patient instanceof Patient){
                continue;
            }

            if($patient_hezuo->needAutoOut()){
                $patient_hezuo->goOut(2);
            }

        }

        $unitofwork->commitAndInit();
    }

}

// //////////////////////////////////////////////////////

$process = new Lilly_patient_autoout(__FILE__);
$process->dowork();
