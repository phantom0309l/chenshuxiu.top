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

class Dbfix_optasks_diseaseid extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::dbfix;
        $row["when"] = 'daily';
        $row["title"] = '每天, 07:01 数据修补optasks中diseaseid';
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

    // 模板方法的实现, 重载
    public function doworkImp () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $time = date("Y-m-d H:i:s");
        echo "\n ===== {$time} begin =====\n";

        $sql = 'SELECT op.id as optaskid, pc.diseaseid
                FROM optasks op
                INNER JOIN pcards pc ON ( op.patientid=pc.patientid AND op.doctorid=pc.doctorid )
                WHERE op.diseaseid <> pc.diseaseid';

        $rows = Dao::queryRows($sql);

        $cnt = count($rows);

        echo "\ncnt=$cnt\n";

        foreach ($rows as $row) {
            $optaskid = $row['optaskid'];
            $diseaseid = $row['diseaseid'];

            $optask = OpTask::getById($optaskid);
            if ($diseaseid > 0) {
                $optask->diseaseid = $diseaseid;
                $this->cronlog_content .= "{$optaskid}\n";
            } else {
                Debug::warn("[optaskid][{$optaskid}]->diseaseid fix fail.");
            }

            echo ".";
        }

        $this->cronlog_brief = $cnt;

        $unitofwork->commitAndInit();

        $time = date("Y-m-d H:i:s");
        echo "\n ===== {$time} end =====\n";
    }
}

$process = new Dbfix_optasks_diseaseid(__FILE__);
$process->dowork();
