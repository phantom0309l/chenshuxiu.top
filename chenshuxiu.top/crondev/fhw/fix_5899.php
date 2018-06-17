<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
require_once(ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel.php");
require_once(ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php");

mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);
//Debug::$debug = 'Dev';

class Fix_5899
{
    public function work () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $today = date('Y-m-d');
        $sql = "select a.id
                from optasks a
                inner join opnodes b on b.id = a.opnodeid
                inner join optasktpls c on c.id = a.optasktplid
                left join optaskcrons d on d.optaskid = a.id
                where b.code = 'root' and a.send_status = 0 and c.is_auto_send = 1 and a.plantime >= '{$today}' and a.status in (0,2) and d.id is null ";
        $ids = Dao::queryValues($sql); // 458089196
        $cnt = count($ids);

        foreach ($ids as $i => $id) {
            $optask = OpTask::getById($id);
            $plantime = $optask->plantime;

            $optasktplcron = OpTaskTplCronDao::getByOptasktplidStep($optask->optasktplid, 1);

            if (false == $optasktplcron instanceof OpTaskTplCron) {
                continue;
            }

            // 计算定时事件的执行时间
            if ($plantime > $today) {
                $plan_exe_time = $plantime;
            } else {
                $plan_exe_time = date('Y-m-d', time() + 3600 * 24);
            }

            $row = [];
            $row["optaskid"] = $optask->id;
            $row["optasktplcronid"] = $optasktplcron->id;
            $row["plan_exe_time"] = $plan_exe_time;
            $row["status"] = 0;
            $optaskcron = OpTaskCron::createByBiz($row);

            if ($i % 100 == 0) {
                echo "{$i}/{$cnt}\n";

                $unitofwork->commitAndInit();
            } else {
                echo ".";
            }
        }

        $unitofwork->commitAndInit();
    }
}

$test = new Fix_5899();
$test->work();
