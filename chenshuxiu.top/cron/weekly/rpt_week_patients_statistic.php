<?php
/**
 * Created by PhpStorm.
 * User: lijie
 * Date: 16-7-21
 * Time: 上午11:24
 */
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

class Rpt_week_patients_statistic
{

    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $date=date('Y-m-d', time());

            $last_monday = $this->last_monday($date);
            $last_sunday = $this->last_sunday($date);

            $row = Rpt_week_patient::getRptRowByDate($last_monday, $last_sunday);
            $last_monday = $row['mondaydate'];

            echo "\n=--------------------------".$last_monday;

            $cond = " and mondaydate = :mondaydate ";

            $bind = [];
            $bind[':mondaydate'] = $last_monday;

            $rpt_week_patient = Rpt_week_patientDao::getEntityByCond("Rpt_week_patient", $cond, $bind, 'statdb');

            if(false == $rpt_week_patient instanceof Rpt_week_patient) {
                Rpt_week_patient::createByBiz($row);
            }

        $unitofwork->commitAndInit();
    }

    private function last_monday($datetamp = ""){
        $w=date('w',strtotime($datetamp));

        $now_start=date('Y-m-d',strtotime("$datetamp -".($w ? $w - 1 : 6).' days'));

        $last_start=date('Y-m-d',strtotime("$now_start - 7 days"));

        return $last_start;
    }

    private function last_sunday($datetamp = ""){
        $w=date('w',strtotime($datetamp));

        $now_start=date('Y-m-d',strtotime("$datetamp -".($w ? $w - 1 : 6).' days'));

        $last_start = date('Y-m-d',strtotime("$now_start - 1 days"));

        return $last_start;
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Rpt_week_patients_statistic.php]=====");

$process = new Rpt_week_patients_statistic();
$process->dowork();

Debug::trace("=====[cron][end][Rpt_week_patients_statistic.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
