<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// Debug::$debug = 'Dev';

class update_6285
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $optaskcheckids = $this->getTextOpTaskCheckids();

        foreach ($optaskcheckids as $optaskcheckid) {
            var_dump($optaskcheckid);
            $optaskcheck = OpTaskCheck::getById($optaskcheckid);
            $auditor = $optaskcheck->auditor;

            $startTime = $optaskcheck->thedate;
            $endTime = $optaskcheck->createtime;
            $weekStartTime = $this->getWeekStartTime($startTime);

            $optaskid = $this->getOptaskIdByAuditorAndTimeSolt($auditor, $startTime, $endTime, $weekStartTime);

            $optaskcheck->optask_id=$optaskid;
        }

        $unitofwork->commitAndInit();
    }

    private function getTextOpTaskCheckids () {
        $select = 'SELECT a.id FROM optaskchecks a 
                    LEFT JOIN optasks b ON a.optask_id=b.id
                   WHERE b.id IS NULL';

        return Dao::queryValues($select);
    }

    // 随机获取auditor 的optask
    private function getOptaskIdByAuditorAndTimeSolt(Auditor $auditor, $startTime, $endTime, $weekStartTime) {
        $sql = "SELECT a.id FROM optasks a force index (idx_donetime)
                  LEFT JOIN users b on a.userid = b.id
                    WHERE a.auditorid = :auditorid
                      AND a.donetime BETWEEN :startTime AND :endTime
                      AND a.status=1
                      AND a.patientid NOT IN (
                        SELECT a.patientid FROM optasks AS a
                          LEFT JOIN optaskchecks AS b ON b.optask_id = a.id
                        WHERE b.auditor_id = :auditorid
                          AND b.thedate BETWEEN :weekStartTime AND :endTime
                      )
                      AND (b.id<10000 OR b.id>20000)
                  GROUP BY a.patientid
                  ORDER BY rand()  
                  limit 1";

        $bind = [];
        $bind[':auditorid'] = $auditor->id;
        $bind[':startTime'] = $startTime;
        $bind[':endTime'] = $endTime;
        $bind[':weekStartTime'] = $weekStartTime;

        $optaskId = Dao::queryValue($sql, $bind);
        return $optaskId;
    }

    // 周起始时间
    private function getWeekStartTime($date) {
        $w = date('w', strtotime($date));
        $time = strtotime("$date -" . ($w ? $w - 1 : 6) . ' days');
        return date('Y-m-d', $time); //获取周开始日期，如果$w是0，则表示周日，减去 6 天
    }


}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Update_6285.php]=====");

$update_6285 = new update_6285();
$update_6285->dowork();

Debug::trace("=====[cron][end][Update_6285.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
