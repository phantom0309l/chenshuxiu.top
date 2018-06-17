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
class Lilly_patient_optask_basedrug extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'daily';
        $row["title"] = '每天, 08:45 生成sunflower项目患者的4周、12周、24周基础用药任务';
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

        $sql = "select id
            from patient_hezuos
            where status=1 and company='Lilly'
            and datediff(now(), createtime) in (28, 84, 168)";

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
            $diff = $patient_hezuo->getDayCntFromCreate();

            $patient = $patient_hezuo->patient;
            if (false == $patient instanceof Patient) {
                continue;
            }

            // 患者报到第 4*7天 生成 4周基础用药任务；
            // 患者报到第 12*7天 生成 12周基础用药任务；
            // 患者报到第 24*7天 生成 24周基础用药任务；
            $arr = [
                '28' => 'baseDrug_4_week',
                '84' => 'baseDrug_12_week',
                '168' => 'baseDrug_24_week'];

            if (isset($arr[$diff])) {
                $code = $arr[$diff];

                // 生成任务: 4周基础用药, 12周基础用药, 24周基础用药
                OpTaskService::createPatientOpTask($patient, "{$code}:", null, '', 1);
            }
        }

        $unitofwork->commitAndInit();
    }
}

// //////////////////////////////////////////////////////

$process = new Lilly_patient_optask_basedrug(__FILE__);
$process->dowork();
