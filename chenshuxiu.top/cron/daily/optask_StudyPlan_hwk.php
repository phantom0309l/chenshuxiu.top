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
class Optask_StudyPlan_hwk extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::optask;
        $row["when"] = 'daily';
        $row["title"] = '每天, 03:01 生成分组课程任务';
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

        $time = time();
        $enddate = date("Y-m-d", $time);
        $sql = "select id from studyplans where enddate = :enddate and objcode = 'hwk' and enddate > '2017-04-02'";
        $bind = [];
        $bind[":enddate"] = $enddate;
        $ids = Dao::queryValues($sql, $bind);
        $i = 0;
        foreach ($ids as $id) {
            $i ++;
            if ($i >= 50) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }

            $studyplan = StudyPlan::getById($id);
            $done_cnt = $studyplan->done_cnt;
            if ($done_cnt > 0) {
                // 生成任务: 分组作业 (实体唯一 StudyPlan)
                //OpTaskService::tryCreateOpTaskByObj($wxuser = null, $studyplan->patient, $doctor = null, 'hwk:StudyPlan', $studyplan, '', 1);
            }
        }

        $unitofwork->commitAndInit();
    }
}

// //////////////////////////////////////////////////////

$process = new Optask_StudyPlan_hwk(__FILE__);
$process->dowork();
