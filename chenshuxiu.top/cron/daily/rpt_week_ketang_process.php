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

// Debug::$debug = 'Dev';

class Rpt_week_ketang_process extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::rpt;
        $row["when"] = 'daily';
        $row["title"] = '每天, 02:41 rpt_week_ketang 数据报表汇总';
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

        $begintime = XDateTime::now();

        $begin_date_time = strtotime('2015-08-17');

        $nowtime = time();

        $unitofwork = BeanFinder::get("UnitOfWork");

        while ($begin_date_time < $nowtime) {

            $begindate = date("Y-m-d", $begin_date_time);
            $enddate = date("Y-m-d", $begin_date_time + 86400 * 6);
            echo "\n===== $begindate - $enddate ";

            $next_begindate_time = $begin_date_time + 86400 * 7;

            $rpt = $this->calcKeTangOneWeek($begin_date_time);
            echo "\n";
            echo $rpt->hwkactivecnt;
            echo " - ";
            echo $rpt->ketang_newcnt;
            echo " - ";
            echo $rpt->ketang_allcnt;
            echo " - ";
            echo $rpt->adhd_newcnt;
            echo " - ";
            echo $rpt->adhd_allcnt;

            $begin_date_time = $next_begindate_time;

            $unitofwork->commitAndInit();
            $unitofwork = BeanFinder::get("UnitOfWork");
        }

        $unitofwork->commitAndInit();
    }

    public function calcKeTangOneWeek ($begin_date_time) {

        $next_begindate_time = $begin_date_time + 86400 * 7;

        $begindate = date("Y-m-d", $begin_date_time);
        $enddate = date("Y-m-d", $begin_date_time + 86400 * 6);
        $next_begindate = date("Y-m-d", $next_begindate_time);

        $adhd_newcnt = $this->get_adhd_newcnt($begindate, $next_begindate);
        $adhd_allcnt = $this->get_adhd_allcnt($next_begindate);

        $ketang_newcnt = $this->get_ketang_newcnt($begindate, $next_begindate);
        $ketang_allcnt = $this->get_ketang_allcnt($next_begindate);

        $hwkactivecnt = $this->getHwkActiveCnt($begindate, $next_begindate);

        $rpt = Rpt_week_ketangDao::getOne($begindate, $enddate);
        if ($rpt instanceof Rpt_week_ketang) {
            echo " == ";
            $rpt->hwkactivecnt = $hwkactivecnt;
            $rpt->ketang_newcnt = $ketang_newcnt;
            $rpt->ketang_allcnt = $ketang_allcnt;
            $rpt->adhd_newcnt = $adhd_newcnt;
            $rpt->adhd_allcnt = $adhd_allcnt;
        } else {
            echo " ++ ";
            $row = array();
            $row["begindate"] = $begindate;
            $row["enddate"] = $enddate;
            $row["hwkactivecnt"] = $hwkactivecnt;
            $row["ketang_newcnt"] = $ketang_newcnt;
            $row["ketang_allcnt"] = $ketang_allcnt;
            $row["adhd_newcnt"] = $adhd_newcnt;
            $row["adhd_allcnt"] = $adhd_allcnt;
            $rpt = Rpt_week_ketang::createByBiz($row);
        }
        return $rpt;
    }

    public function get_ketang_newcnt ($begindate, $next_begindate) {

        $bind = [];
        $bind[':begindate'] = $begindate;
        $bind[':next_begindate'] = $next_begindate;

        $sql = "select count(*) as cnt from wxusers where wxshopid=3 and subscribe=1 and createtime >= :begindate and  createtime < :next_begindate";

        return Dao::queryValue($sql, $bind);
    }

    public function get_ketang_allcnt ($next_begindate) {

        $bind = [];
        $bind[':next_begindate'] = $next_begindate;

        $sql = "select count(*) as cnt from wxusers where wxshopid=3 and subscribe=1 and createtime < :next_begindate";

        return Dao::queryValue($sql, $bind);
    }

    public function get_adhd_newcnt ($begindate, $next_begindate) {
        $bind = [];
        $bind[':begindate'] = $begindate;
        $bind[':next_begindate'] = $next_begindate;

        $sql = "select count(a.userid) as cnt
                from courseuserrefs a
                inner join users b on b.id=a.userid
                inner join wxusers c on c.userid=b.id and c.wxshopid=1
                left join wxusers d on d.userid=b.id and d.wxshopid=3
                where a.courseid=100839705 and a.createtime > :begindate and a.createtime < :next_begindate
                and d.id is null";
        return Dao::queryValue($sql, $bind);
    }

    public function get_adhd_allcnt ($next_begindate) {
        $bind = [];
        $bind[':next_begindate'] = $next_begindate;

        $sql = "select count(a.userid) as cnt
                from courseuserrefs a
                inner join users b on b.id=a.userid
                inner join wxusers c on c.userid=b.id and c.wxshopid=1
                left join wxusers d on d.userid=b.id and d.wxshopid=3
                where a.courseid=100839705 and a.createtime < :next_begindate
                and d.id is null";
        return Dao::queryValue($sql, $bind);
    }

    public function getHwkActiveCnt ($begindate, $next_begindate) {
        $bind = [];
        $bind[':begindate'] = $begindate;
        $bind[':next_begindate'] = $next_begindate;

        $sql = "select count(*)
            from (select *
                from lessonuserrefs
                where courseid=100839705 and createtime > :begindate and createtime < :next_begindate
                group by userid) tt";

        return Dao::queryValue($sql, $bind);
    }

}

// //////////////////////////////////////////////////////

$process = new Rpt_week_ketang_process(__FILE__);
$process->dowork();
