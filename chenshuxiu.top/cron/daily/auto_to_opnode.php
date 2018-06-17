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

// fhw address
class Auto_to_opnode extends CronBase
{

    protected function getRowForCronTab() {
        $row = array();
        $row["type"] = CronTab::optask;
        $row["when"] = 'daily';
        $row["title"] = '每天, 01:45, 自动关闭任务';
        return $row;
    }

    protected function needFlushXworklog() {
        return true;
    }

    protected function needCronlog() {
        return true;
    }

    /**
     * @throws SystemException
     */
    protected function doworkImp() {
        $unitofwork = BeanFinder::get("UnitOfWork");

        // 法定放假日不发送消息
        $today = date('Y-m-d');
        if (false == FUtil::isHoliday($today)) {
            $sql = "select id from optasktpls where is_auto_to_opnode = 1 ";
            $optasktplids = Dao::queryValues($sql);

            $brief = 0;
            $logcontent = '';

            foreach ($optasktplids as $optasktplid) {
                echo "\noptasktplid:{$optasktplid}\n";

                $optasktpl = OpTaskTpl::getById($optasktplid);
                $endtime = date('Y-m-d', time() - 86400 * $optasktpl->auto_to_opnode_daycnt) . " 23:59:59";

                $sql = "select id,first_plantime
                    from optasks
                    where status in (0, 2) and optasktplid = {$optasktplid} and first_plantime <= '{$endtime}' ";
                $rows = Dao::queryRows($sql);

                $optaskids = $this->check_holiday($rows, $optasktpl->auto_to_opnode_daycnt);

                $cnt = count($optaskids);

                echo "optaskcnt:{$cnt}\n";
                exit;

                foreach ($optaskids as $optaskid) {

                    echo "optaskid:{$optaskid}\n";

                    $optask = OpTask::getById($optaskid);

                    if ($optasktpl->auto_to_opnode_code == 'unfinish') {
                        if (true == $this->haveMessage($optask->patient)) {
                            continue;
                        }
                    }

                    $from_opnode = $optask->opnode;
                    $to_opnode = OpNodeDao::getByCodeOpTaskTplId($optasktpl->auto_to_opnode_code, $optask->optasktplid);
                    $opnodeflow = OpNodeFlowDao::getByFrom_opnodeTo_opnode($from_opnode, $to_opnode);

                    if (false == $opnodeflow instanceof OpNodeFlow) {
                        continue;
                    }

                    OpTaskEngine::flow_to_opnode($optask, $optasktpl->auto_to_opnode_code, 1);

                    $brief++;
                    $logcontent .= "{$optaskid},";

                    if ($brief % 100 == 0) {
                        $unitofwork->commitAndInit();
                    }
                }
            }
        }

        $this->cronlog_brief = $brief;
        $this->cronlog_content = $logcontent;

        echo "{$this->cronlog_brief} {$this->cronlog_content} \n";

        $unitofwork->commitAndInit();
    }

    // 检查是否有节假日
    private function check_holiday($rows, $close_day_cnt) {
        $list = [];

        $today = date('Y-m-d');
        foreach ($rows as $row) {
            $optaskid = $row['id'];
            $first_plantime = $row['first_plantime'];
            print_r("{$optaskid} | {$first_plantime} | [{$close_day_cnt}] |");

            $start_date = date('Y-m-d', strtotime($first_plantime) + 86400); // 当天不算
            $day_cnt = 0;
            while ($start_date <= $today) {
                if (!FUtil::isHoliday($start_date)) {
                    $day_cnt++;
                    echo " {$start_date} 有效 |";
                } else {
                    echo " {$start_date} 跳过 |";
                }

                $start_date = date('Y-m-d', strtotime($start_date) + 86400);
            }

            print_r(" [{$day_cnt}]\n");
            if ($day_cnt >= $close_day_cnt) {
                echo "{$first_plantime} {$today} {$day_cnt} {$close_day_cnt} \n";

                $list[] = $optaskid;
            }
        }

        return $list;
    }

    // 是否有进行中的消息任务
    private function haveMessage($patient) {
        $sql = "select count(*)
                from optasks
                where optasktplid in (123261855, 605631006) and status = 0 and patientid = {$patient->id} ";
        $cnt = Dao::queryValue($sql);

        return $cnt > 0;
    }
}

$test = new Auto_to_opnode(__FILE__);
$test->dowork();
