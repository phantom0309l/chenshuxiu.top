<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");

mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class Create_Optask_Check extends CronBase
{

    protected function getRowForCronTab() {
        $row = array();
        $row["type"] = CronTab::optask;
        $row["when"] = 'daily';
        $row["title"] = '每天, 20:30, 随机取运营人员的 n 条不同患者的关闭的不同类型的任务';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog() {
        return true;
    }

    // 是否记cronlog, 重载
    protected function needCronlog() {
        return true;
    }

    // 模板方法的实现, 重载
    public function doworkImp() {
        // 配置需要评审的运行组 ename
        // 可配置运营组 成员每天需要抓取的 optask 个数
        $config = OpTaskCheckTpl::getOpTaskCheckTplAndAuditorGroupMap();

        $unitofwork = BeanFinder::get("UnitOfWork");

        $startTime = date('Y-m-d');
        $endTime = date('Y-m-d H:i:s');

        var_dump($startTime);
        var_dump($endTime);
        foreach ($config as $item) {
            $auditorGroup = AuditorGroupDao::getByTypeAndEname('base', $item['AuditorGroupEname']);
            $optaskCheckTpl = OpTaskCheckTplDao::getByEname($item['opTaskCheckTplEname']);
            $limit = OpTaskCheckTpl::getLimitByEname($item['AuditorGroupEname']);

            if ($auditorGroup instanceof AuditorGroup == false) {
                Debug::warn("ename为{$item['AuditorGroupEname']} 的AuditorGroup 不存在，或其 type 不为 base");
                continue;
            }

            if ($optaskCheckTpl instanceof OpTaskCheckTpl == false) {
                Debug::warn("ename为{$item['opTaskCheckTplEname']} OpTaskCheckTpl 不存在");
                continue;
            }

            // 获取auditorgroup 下的 auditorids
            $auditorids = AuditorGroupRefDao::getAuditorIdsByAuditorGroupId($auditorGroup->id);

            $weekStartTime = $this->getWeekStartTime($startTime);

            foreach ($auditorids as $auditorid) {
                $auditor = Auditor::getById($auditorid);
                if ($auditor instanceof Auditor == false) {
                    Debug::warn("无 auditor:{$auditorid} 的运营人员");
                    continue;
                }

                $optaskids = $this->getOptaskIdsByAuditorAndTimeSolt($auditor, $limit, $startTime, $endTime, $weekStartTime);

                if ($limit > count($optaskids) && !empty($optaskids)) {
                    $num = $limit - count($optaskids);
                    $replenishOpTaskIds = $this->getOptaskIdsByAuditorAndTimeSolt($auditor, $num, $startTime, $endTime, $weekStartTime ,false, $optaskids);
                    $optaskids = array_merge($optaskids, $replenishOpTaskIds);
                }

                $this->insertOpTaskCheckByOpTaskIds($optaskids, $optaskCheckTpl, $auditor);

                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");

                if(date('w', strtotime($startTime)) == 0){
                    $sundayLimit = $this->getLimitForOpTaskCheck($auditor, $limit, $weekStartTime);
                    $optaskids = $this->getOptaskIdsByAuditorAndTimeSolt($auditor, $sundayLimit, $weekStartTime, $endTime, $weekStartTime, false);
                    $this->insertOpTaskCheckByOpTaskIds($optaskids, $optaskCheckTpl, $auditor);
                    $unitofwork->commitAndInit();
                    $unitofwork = BeanFinder::get("UnitOfWork");
                }
            }
        }
        $unitofwork->commitAndInit();
    }

    // 随机获取auditor 的optask
    private function getOptaskIdsByAuditorAndTimeSolt(Auditor $auditor, $limit = 4, $startTime, $endTime, $weekStartTime, $isGroup=true, $choiseOpTaskIds = []) {
        $sql = "SELECT f1.id FROM (
                  SELECT a.id,a.optasktplid FROM optasks a force index (idx_donetime)
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
                      ";

        if (!empty($choiseOpTaskIds)) {
            $choisePatientIds = $this->getPatientidsByOpTaskids($choiseOpTaskIds);
            $choisePatientIdsStr = implode(",", $choisePatientIds);
            $sql .= "AND a.patientid NOT IN ({$choisePatientIdsStr}) ";
        }

        $sql .= " GROUP BY a.patientid
                    ORDER BY rand()
                  ) f1 ";

        if ($isGroup) {
            $sql .= " GROUP BY f1.optasktplid ";
        }
       
        $sql .= " limit {$limit}";

        $bind = [];
        $bind[':auditorid'] = $auditor->id;
        $bind[':startTime'] = $startTime;
        $bind[':endTime'] = $endTime;
        $bind[':weekStartTime'] = $weekStartTime;

        $optaskIds = Dao::queryValues($sql, $bind);
        return $optaskIds;
    }

    // 周起始时间
    private function getWeekStartTime($date) {
        $w = date('w', strtotime($date));
        $time = strtotime("$date -" . ($w ? $w - 1 : 6) . ' days');
        return date('Y-m-d', $time); //获取周开始日期，如果$w是0，则表示周日，减去 6 天
    }

    // 获取周日需要填充的 optaskcheck数量
    private function getLimitForOpTaskCheck(Auditor $auditor, $limit, $weekStartTime) {
        $gotOpTaskCheckCnt = $this->getOptaskCntOfTimeSlotByAuditor($auditor, $weekStartTime);
        $limitForOpTaskCheck = $limit * 7 - $gotOpTaskCheckCnt;
        $limitForOpTaskCheck = $limitForOpTaskCheck<=0 ? 0: $limitForOpTaskCheck;

        return $limitForOpTaskCheck;
    }

    // getPatientidsByOpTaskids
    private function getPatientidsByOpTaskids(Array $ids) {
        if (empty($ids)) {
            return [];
        }
        $optaskidsStr = implode(",", $ids);
        $sql = "SELECT patientid FROM optasks WHERE id IN ({$optaskidsStr})";
        return Dao::queryValues($sql);
    }

    // 获取指定时间段内已经抓取的 optask 数量
    // 周日逻辑中使用
    private function getOptaskCntOfTimeSlotByAuditor(Auditor $auditor, $startTime) {
        $sql = 'SELECT COUNT(*) FROM optaskchecks WHERE auditor_id=:auditorid AND thedate BETWEEN :startTime AND :endTime';
        $bind = [];
        $bind[':auditorid'] = $auditor->id;
        $bind[':startTime'] = $startTime;
        $bind[':endTime'] = date('Y-m-d H:i:s');

        // 时间段内已经生成的 optaskcheck 数量
        $createdCnt = Dao::queryValue($sql, $bind);
        return $createdCnt;
    }

    // insertOpTaskCheckByOpTaskIds
    private function insertOpTaskCheckByOpTaskIds (Array $optaskids, OpTaskCheckTpl $optaskCheckTpl, Auditor $auditor ) {
        if (!empty($optaskids)) {
            foreach ($optaskids as $optaskid) {
                $optask = OpTask::getById($optaskid);
                $row = array();
                $row['thedate'] = date('Y-m-d', strtotime($optask->donetime));
                $row["optaskchecktplid"] = $optaskCheckTpl->id;
                $row["auditor_id"] = $auditor->id;
                $row["optask_id"] = $optaskid;
                $row["status"] = 0;
                $row["woy"] = XDateTime::getWFromFirstDate($optask->donetime);

                OpTaskCheck::createByBiz($row);
            }
        }
    }


}

print_r($argv);
$create_Optask_Check = new Create_Optask_Check(__FILE__);

$create_Optask_Check->dowork();
