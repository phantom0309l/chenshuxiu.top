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

// Debug::$debug_mergexworklog = false;
// Debug::$debug = 'Dev';
class Rpt_date_db_process extends CronBase
{

    private $tables = array();

    private $table_mindates = array();

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::rpt;
        $row["when"] = 'daily';
        $row["title"] = '每天, 00:15 统计各表数据情况';
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
        $this->init_tables();
        $this->init_table_mindates();
        // $this->doAllDate();

        $thetime = strtotime(date('Y-m-d')) - 86400;
        $thedate = date('Y-m-d', $thetime);
        $this->doOneDate($thedate);
    }

    // 初始化 需要处理的表名
    public function init_tables () {
        $dbExecuter = BeanFinder::get("DbExecuter");

        $sql = "show tables";
        $tableNames = $dbExecuter->queryValues($sql);

        $jumpTables = array(
            'idgenerator',
            'patientid_userids',
            'userid_wxuserids',
            'xcodes');

        $arr = array();
        foreach ($tableNames as $a) {

            if (in_array($a, $jumpTables)) {
                continue;
            }

            if (strpos($a, 'rpt_') === 0) {
                continue;
            }

            if (strpos($a, 'historys') > 0) {
                continue;
            }

            $sql = "select count(*) from $a ";
            $rowcnt = 0 + Dao::queryValue($sql, []);

            if ($rowcnt > 0) {
                $this->tables[] = $a;
            }
        }
    }

    // 初始化 各表的最小创建日期
    public function init_table_mindates () {
        foreach ($this->tables as $table) {
            $sql = "select left(createtime, 10) as thedate
                from {$table}
                where createtime != '0000-00-00 00:00:00'
                order by createtime
                limit 1";
            $thedate = Dao::queryValue($sql, []);

            $this->table_mindates[$table] = $thedate;
        }
    }

    // 旧数据
    public function doAllDate () {
        $thetime = strtotime('2015-03-31');

        // 昨天
        $totime = strtotime(date('Y-m-d')) - 86400;

        while ($thetime < $totime) {
            $thetime += 86400;
            $thedate = date('Y-m-d', $thetime);

            echo "\n===[{$thedate}]===";

            $this->doOneDate($thedate);
        }
    }

    public function doOneDate ($thedate) {
        $unitofwork = BeanFinder::get("UnitOfWork");

        foreach ($this->tables as $table) {
            $minDate = $this->table_mindates[$table];

            echo "\n $thedate $table $thedate";

            // 跳过不该生成的数据
            if (strtotime($minDate) > strtotime($thedate)) {
                continue;
            }

            $nextDate = date('Y-m-d', strtotime($thedate) + 86400);

            $sql = "select max(id) as maxid, count(*) as rowcnt
                from {$table}
                where createtime >= :fromdate and createtime < :todate ";

            $bind = [];
            $bind[':fromdate'] = $thedate;
            $bind[':todate'] = $nextDate;

            $row = Dao::queryRow($sql, $bind);

            $rowcnt = $row['rowcnt'];
            $maxid = 0 + $row['maxid'];

            $sql = "select count(*) as total_rowcnt
            from {$table}
            where createtime < :todate ";

            $bind = [];
            $bind[':todate'] = $nextDate;

            $total_rowcnt = Dao::queryValue($sql, $bind);

            echo " ======== $total_rowcnt += $rowcnt ";

            $row = array();
            $row["thedate"] = $thedate;
            $row["tablename"] = $table;
            $row["rowcnt"] = $rowcnt;
            $row["total_rowcnt"] = $total_rowcnt;
            $row["maxid"] = $maxid;
            Rpt_date_table::createByBiz($row);
        }

        $unitofwork->commitAndInit();

        $sql = "select thedate,
                count(*) as tablecnt,
                sum(rowcnt) as rowcnt,
                sum(total_rowcnt) as total_rowcnt,
                max(maxid) as maxid,
                sum(if(rowcnt>0,1,0)) as tablecnt_hasdata
            from rpt_date_tables
            where thedate = :thedate ";

        $bind = [];
        $bind[':thedate'] = $thedate;

        $row = Dao::queryRow($sql, $bind, 'statdb');

        $row['thedate'] = $thedate;
        $row['tablecnt'] += 0;
        $row['rowcnt'] += 0;
        $row['total_rowcnt'] += 0;
        $row['maxid'] += 0;
        $row['tablecnt_hasdata'] += 0;
        Rpt_date_db::createByBiz($row);

        $unitofwork->commitAndInit();
    }
}

// //////////////////////////////////////////////////////

$process = new Rpt_date_db_process(__FILE__);
$process->dowork();