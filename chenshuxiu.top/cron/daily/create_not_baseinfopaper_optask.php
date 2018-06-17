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

// fhw address
// 取消该脚本
class Create_not_baseinfopaper_optask extends CronBase
{
    protected function getRowForCronTab() {
        $row = array();
        $row["type"] = CronTab::optask;
        $row["when"] = 'daily';
        $row["title"] = '每天, 10:13, 把昨天报到但未填写【入组基本信息】量表的肿瘤患者，生成【基本信息填写】任务';
        return $row;
    }

    protected function needFlushXworklog() {
        return true;
    }

    protected function needCronlog() {
        return $this->cronlog_brief > 0;
    }

    protected function doworkImp() {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $patientids = $this->getPatientIds();

//         $optaskids = [459621178];

        $brief = 0;
        $logcontent = '';

        foreach ($patientids as $id) {
            $patient = Patient::getById($id);

            // 生成任务: 基本信息填写 (患者唯一)
            OpTaskService::tryCreateOpTaskByPatient($patient, 'BaseInfo:collection', null, '', 1);

            $brief ++;
            $logcontent .= $patient->id . " ";

            if ($brief % 100 == 0) {
                $unitofwork->commitAndInit();
            }
        }

        $this->cronlog_brief = $brief;
        $this->cronlog_content = $logcontent;

        echo "{$this->cronlog_brief} {$this->cronlog_content} \n";

        $unitofwork->commitAndInit();
    }

    private function getPatientIds () {
        $today = date('Y-m-d');
        $two_day_before = date('Y-m-d', time() - 3600 * 24 * 2);

        $sql = "select distinct a.id
                from patients a
                inner join wxusers w on w.patientid = a.id
                left join (
                    select b.*
                    from optasks b
                    inner join optasktpls c on c.id = b.optasktplid
                    where c.objtype = 'Paper' and c.code = 'BaseInfo' and c.subcode = 'collection'
                ) t on t.patientid = a.id
                where a.diseaseid in (8,15,19,21) and a.createtime > '{$two_day_before}' and a.createtime < '{$today}' and t.id is null ";
        $ids = Dao::queryValues($sql);

        return $ids;
    }

}

$test = new Create_not_baseinfopaper_optask(__FILE__);
$test->dowork();
