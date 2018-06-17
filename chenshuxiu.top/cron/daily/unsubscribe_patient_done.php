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

class Unsubscribe_patient_done extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::dbfix;
        $row["when"] = 'daily';
        $row["title"] = '每天, 06:30 每天 查询前天报到时间超过24h, 且报到后24h之内取消关注的患者, 直接记录为疑似无效患者';
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

        // 要搜索的报到起始，结束时间
        $fromdate = date("Y-m-d", time() - 3 * 86400);
        $todate = date("Y-m-d", time() - 2 * 86400);

        $patientidsarr = $this->getPassedPatientidsArr($fromdate, $todate);

        $patientdaycnt = 0;

        foreach ($patientidsarr as $k => $v) {
            $id = $v["id"];

            echo "\n\n-----begin----- " . $id;

            $patient = Patient::getById($id);

            if ($patient instanceof Patient) {

                // 报到至取关时间
                $patient_lifecycle = $v["patient_lifecycle"];
                // 取关时间
                $unsubscribe_time = $v["unsubscribe_time"];
                // 关注时间
                $subscribe_time = $v["subscribe_time"];
                if ($patient_lifecycle >= 0) {
                    $patientdaycnt = floor($patient_lifecycle / 86400);
                } else {
                    $patientdaycnt = - 1;
                }

                // 报到不到48小时且为取关患者
                if ($patientdaycnt >= 0 && $patientdaycnt <= 1 && $unsubscribe_time > $subscribe_time) {
                    $patient->doubt_type = 1;
                }
            }

            $unitofwork->commitAndInit();
            $unitofwork = BeanFinder::get("UnitOfWork");
        }
        $unitofwork->commitAndInit();
    }

    private function getPassedPatientidsArr ($fromdate, $todate) {
        $sql = " SELECT a.id as id,
              (unix_timestamp(c.unsubscribe_time)-unix_timestamp(a.createtime))  as patient_lifecycle,
              c.unsubscribe_time as unsubscribe_time,
              c.subscribe_time as subscribe_time
            FROM patients a
            INNER JOIN users b ON b.patientid = a.id
            INNER JOIN wxusers c ON c.userid = b.id
            WHERE a.status=1 AND c.wxshopid=1
                AND (b.id < 10000 OR b.id > 20000 )
                AND a.createtime > :fromdate AND a.createtime <= :todate
            GROUP BY a.id ";

        $bind = [];
        $bind[':fromdate'] = $fromdate;
        $bind[':todate'] = $todate;

        return Dao::queryRows($sql, $bind);
    }
}

// //////////////////////////////////////////////////////

$process = new Unsubscribe_patient_done(__FILE__);
$process->dowork();
