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

class Dbfix_doctors_first_patient_date extends CronBase
{
    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::dbfix;
        $row["when"] = 'daily';
        $row["title"] = '每天, 23:50 数据修补doctors中first_patient_date';
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

        $sql = "update doctors a
                inner join (
                    select min(a.createtime) as min_time,a.doctorid
                    from patients a
                    inner join users b on b.patientid = a.id
                    where a.status=1 and (b.id<10000 or b.id>20000) and a.is_test=0
                    group by a.doctorid
                ) tt on tt.doctorid = a.id
                set a.first_patient_date = left(tt.min_time, 10)
                where a.first_patient_date = '0000-00-00'";

        Dao::executeNoQuery($sql);

        $unitofwork->commitAndInit();

        $time = date("Y-m-d H:i:s");
        echo "\n ===== {$time} end =====\n";
    }
}

$process = new Dbfix_doctors_first_patient_date(__FILE__);
$process->dowork();
