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
class Optask_remind_medicineBreak extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'daily';
        $row["title"] = '每天, 07:20 生成 "药物到期提醒" 任务';
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

        $sql = "select id from patients
            where id not in (select patientid from patient_hezuos where status=1)";

        $bind = [];

        $sql .= ' and medicine_break_date = :medicine_break_date ';
        $bind[':medicine_break_date'] = date('Y-m-d');

        $ids = Dao::queryValues($sql, $bind);
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

            $patient = Patient::getById($id);

            if($this->haveCreate5DaysAgo($patient)){
                continue;
            }

            // 生成任务: 药物到期提醒
            OpTaskService::createPatientOpTask($patient, 'remind:medicineBreak', null, '', 1);
        }

        $unitofwork->commitAndInit();
    }

    private function haveCreate5DaysAgo ($patient) {
        $optasktpl = OpTaskTplDao::getOneByUnicode("remind:medicineBreak");
        $cond = " and patientid = :patientid and optasktplid = :optasktplid and left(createtime, 10) = :createday ";

        $bind = [];
        $bind[':patientid'] = $patient->id;
        $bind[':optasktplid'] = $optasktpl->id;
        $bind[':createday'] = date('Y-m-d', strtotime('-5 day'));

        $optask = Dao::getEntityByCond('OpTask', $cond, $bind);
        return $optask instanceof OpTask;
    }
}

// //////////////////////////////////////////////////////

$process = new Optask_remind_medicineBreak(__FILE__);
$process->dowork();
