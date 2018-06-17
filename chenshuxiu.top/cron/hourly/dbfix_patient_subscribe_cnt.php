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

// 修正patients表字段 subscribe_cnt 和 wxuser_cnt
class dbfix_patient_subscribe_cnt extends CronBase
{

    private $cnt = 0;

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::dbfix;
        $row["when"] = 'hourly';
        $row["title"] = '每小时, 修正patients表字段 subscribe_cnt 和 wxuser_cnt';
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

        $sql = "select a.id, count(c.id) as wxuser_cnt, sum(c.subscribe) as subscribe_cnt
            from patients a
            left join users b on b.patientid = a.id
            left join wxusers c on c.userid = b.id
            group by a.id;";

        $patient_wxusercnt_subscribe_cnts = Dao::queryRows($sql, []);

        $rowcnt = count($patient_wxusercnt_subscribe_cnts);

        $i = 0;
        foreach ($patient_wxusercnt_subscribe_cnts as $a) {
            $i ++;

            $patient = Patient::getById($a['id']);

            if ($patient->subscribe_cnt != $a['subscribe_cnt'] + 0) {
                $old_subscribe_cnt = $patient->subscribe_cnt;
                $patient->subscribe_cnt = $a['subscribe_cnt'] + 0;

                echo $str = "\nsubscribe_cnt : [{$patient->id}] [{$patient->name}] {$old_subscribe_cnt} -> {$patient->subscribe_cnt}";

                $this->cnt ++;
                $this->cronlog_content .= $str;
            }

            if ($patient->wxuser_cnt != $a['wxuser_cnt'] + 0) {
                $old_wxuser_cnt = $patient->wxuser_cnt;
                $patient->wxuser_cnt = $a['wxuser_cnt'] + 0;

                echo $str = "\nwxuser_cnt : [{$patient->id}] [{$patient->name}] {$old_wxuser_cnt} -> {$patient->wxuser_cnt}";

                $this->cnt ++;
                $this->cronlog_content .= $str;
            }

            if ($i % 500 == 0) {
                $unitofwork->commitAndInit();
                echo "\n----------------------------------------------------[$i / $rowcnt]";
            }
        }

        $unitofwork->commitAndInit();

        $this->cronlog_brief = "cnt={$this->cnt}";

        return $this->cnt;
    }
}

// //////////////////////////////////////////////////////

$process = new dbfix_patient_subscribe_cnt(__FILE__);
$process->dowork();
