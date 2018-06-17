<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "3048M");
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class ZsglrjProcess extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::dbfix;
        $row["when"] = 'daily';
        $row["title"] = '每天, 22:30, 和海南处方系统同步数据';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog () {
        return false;
    }

    // 是否记cronlog, 重载
    protected function needCronlog () {
        return true;
    }

    protected function doworkImp () {
        $sql = "select distinct a.id
            from prescriptions a
            inner join patients c on c.id = a.patientid
            inner join users d on d.patientid = c.id
            inner join prescriptionitems x on x.prescriptionid = a.id
            where a.chufang_cfbh = '' and ( d.id < 10000 or d.id > 20000 )
            order by a.id asc limit 300";

        $prescriptionIds = Dao::queryValues($sql);
        foreach ($prescriptionIds as $prescriptionId) {

            echo "\n=====================================";
            echo "\nprescriptionId = {$prescriptionId}";
            echo "\n=====================================";

            // 开处方流程
            $zsglrjUtil = new ZsglrjUtil($prescriptionId);
            $zsglrjUtil->doWork();
        }
    }
}

$process = new ZsglrjProcess(__FILE__);
$process->dowork();

