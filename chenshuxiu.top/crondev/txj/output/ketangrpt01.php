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

class KeTangRpt01
{
    // public $starttime = "2015-03-01 00:00:00";
    // public $endtime = "2016-04-21 00:00:00";
    public $arr = array();

    public function dowork () {
        $begintime = XDateTime::now();
        $begin_date_time = strtotime('2016-02-29');
        $nowtime = time();
        while ($begin_date_time < $nowtime) {
            $begindate = date("Y-m-d", $begin_date_time);
            $enddate = date("Y-m-d", $begin_date_time + 86400 * 6);
            echo "\n===== $begindate - $enddate ";
            $next_begindate_time = $begin_date_time + 86400 * 7;

            $rpt = $this->calcKeTangOneWeek($begin_date_time);
            $begin_date_time = $next_begindate_time;

        }
        $aa = json_encode($this->arr, true);
        echo "\n=====[arr]{$aa}\n ";
    }

    public function calcKeTangOneWeek ($begin_date_time) {

        $next_begindate_time = $begin_date_time + 86400 * 7;

        $begindate = date("Y-m-d", $begin_date_time);
        $enddate = date("Y-m-d", $begin_date_time + 86400 * 6);
        $next_begindate = date("Y-m-d", $next_begindate_time);

        $allnum = $this->getAllNum($next_begindate);
        $adhdActiveNum = $this->getADHDActiveNum($begindate, $next_begindate);
        $ketangActiveNum = $this->getKeTangActiveNum($begindate, $next_begindate);

        $activeNum = $adhdActiveNum + $ketangActiveNum;
        $per = round($activeNum / $allnum, 2);
        echo "\n====[{$per}]===\n";
        echo "\n====allnum[{$allnum}]===\n";
        echo "\n====activeNum[{$activeNum}]===\n";
        echo "\n====adhdActiveNum[{$adhdActiveNum}]===\n";
        echo "\n====ketangActiveNum[{$ketangActiveNum}]===\n";
        $temp = array();
        $temp['begindate'] = $begindate;
        $temp['enddate'] = $enddate;
        $temp['allnum'] = $allnum;
        $temp['adhdActiveNum'] = $adhdActiveNum;
        $temp['ketangActiveNum'] = $ketangActiveNum;
        $temp['activeNum'] = $activeNum;
        $temp['per'] = $per;
        $this->arr[] = $temp;
    }

    public function getAllNum ($endtime) {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $sql = "select count(*) from wxusers where wxshopid=3 and subscribe=1 and createtime < '{$endtime}'";
        $cnt1 = Dao::queryValue($sql);
        $cnt2 = $this->get_adhd_allcnt($endtime);
        return $cnt1 + $cnt2;
        $unitofwork->commitAndInit();
    }

    public function getADHDActiveNum ($starttime, $endtime) {
        $num = 0;
        $i = 0;
        $unitofwork = BeanFinder::get("UnitOfWork");
        $ids = $this->get_adhd_wxuserids($endtime);
        foreach ($ids as $i => $id) {
            echo "\n====[{$id}]===\n";
            $i ++;
            if ($i >= 100) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
            $bind = [];
            $bind[':wxuserid'] = $id;
            $bind[':endtime'] = $endtime;
            $bind[':starttime'] = $starttime;
            $sql = " and wxuserid=:wxuserid and createtime<:endtime and createtime>:starttime";
            $a = Dao::getEntityByCond("CourseUserRef", $sql, $bind);
            if (count($a) > 0) {
                $num ++;
            }
        }
        $unitofwork->commitAndInit();
        return $num;
    }

    public function getKeTangActiveNum ($starttime, $endtime) {
        $num = 0;
        $i = 0;
        $unitofwork = BeanFinder::get("UnitOfWork");
        $ids = $this->get_ketang_wxuserids($endtime);
        foreach ($ids as $id) {
            echo "\n====[{$id}]===\n";
            $i ++;
            if ($i >= 100) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
            // 培训课人数，用户发的消息
            $bind = [];
            $bind[':wxuserid'] = $id;
            $bind[':endtime'] = $endtime;
            $bind[':starttime'] = $starttime;
            $sql = " and wxuserid=:wxuserid and createtime<:endtime and createtime>:starttime and objtype in ('LessonUserRef','WxTxtMsg','WxVoiceMsg')";
            // $a = getEntityByCond("CourseUserRef", $sql, $bind);
            $a = Dao::getEntityByCond("Pipe", $sql, $bind);
            if (count($a) > 0) {
                $num ++;
                continue;
            }

            // 睡前谈心人数
            $sql = " and wxuserid=:wxuserid and signtime<:endtime and signtime>:starttime";
            $a = Dao::getEntityByCond("WxTaskItem", $sql, $bind);
            if (count($a) > 0) {
                $num ++;
                continue;
            }

            // 做SNAP-IV评估量表的
            $sql = " and wxuserid=:wxuserid and createtime<:endtime and createtime>:starttime";
            $a = Dao::getEntityByCond("Paper", $sql, $bind);
            if (count($a) > 0) {
                $num ++;
                continue;
            }

            // 玩游戏，现在主要是钱英的测试用户
            $sql = " and wxuserid=:wxuserid and createtime<:endtime and createtime>:starttime";
            $a = Dao::getEntityByCond("GamePlay", $sql, $bind);
            if (count($a) > 0) {
                $num ++;
                continue;
            }

            // 好妈妈，3.8活动
            $sql = " and wxuserid=:wxuserid and createtime<:endtime and createtime>:starttime";
            $a = Dao::getEntityByCond("GuestRecord", $sql, $bind);
            if (count($a) > 0) {
                $num ++;
                continue;
            }

            // 玩舒尔特方格的
            $wxuser = WxUser::getById($id);
            $bind = [];
            $bind[':openid'] = $wxuser->openid;
            $bind[':endtime'] = $endtime;
            $bind[':starttime'] = $starttime;
            $sql = " and openid=:openid and createtime<:endtime and createtime>:starttime";
            $a = Dao::getEntityByCond("Guest_schulterecord", $sql, $bind);
            if (count($a) > 0) {
                $num ++;
                continue;
            }

            // face异父不同母活动
            $openid = $wxuser->openid;
            $guest = GuestDao::getByOpenid($openid);
            if ($guest instanceof Guest) {
                $bind = [];
                $bind[':guestid'] = $guest->id;
                $bind[':endtime'] = $endtime;
                $bind[':starttime'] = $starttime;
                $sql = " and guestid=:guestid and createtime<:endtime and createtime>:starttime";
                $a = Dao::getEntityByCond("Guest_face", $sql, $bind);
                if (count($a) > 0) {
                    $num ++;
                    continue;
                }
            }

            // 读课堂贴士，第一次读
            $courses = CourseDao::getListByGroupstr("sfbt");
            $bind = [];
            $bind[':wxuserid'] = $id;
            $bind[':courseid'] = $courses[0]->id;
            $bind[':endtime'] = $endtime;
            $bind[':starttime'] = $starttime;

            $sql = " and wxuserid=:wxuserid and courseid=:courseid and createtime<:endtime and createtime>:starttime";
            $a = Dao::getEntityByCond("LessonUserRef", $sql, $bind);
            if (count($a) > 0) {
                $num ++;
                continue;
            }

        }
        $unitofwork->commitAndInit();
        return $num;
    }

    public function get_ketang_wxuserids ($endtime) {
        $sql = "select id from wxusers where wxshopid=3 and subscribe=1 and createtime < '{$endtime}'";
        return Dao::queryValues($sql);
    }

    public function get_adhd_wxuserids ($endtime) {
        $bind = [];
        $bind[':next_begindate'] = $endtime;

        $sql = "select a.wxuserid as cnt
                from courseuserrefs a
                inner join users b on b.id=a.userid
                inner join wxusers c on c.userid=b.id and c.wxshopid=1
                left join wxusers d on d.userid=b.id and d.wxshopid=3
                where a.courseid=100839705 and a.createtime < :next_begindate
                and d.id is null";
        return Dao::queryValues($sql, $bind);
    }

    public function get_adhd_allcnt ($endtime) {
        $bind = [];
        $bind[':next_begindate'] = $endtime;

        $sql = "select count(a.userid) as cnt
                from courseuserrefs a
                inner join users b on b.id=a.userid
                inner join wxusers c on c.userid=b.id and c.wxshopid=1
                left join wxusers d on d.userid=b.id and d.wxshopid=3
                where a.courseid=100839705 and a.createtime < :next_begindate
                and d.id is null";
        return Dao::queryValue($sql, $bind);
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][ketangrpt.php]=====");

$process = new KeTangRpt01();
$process->dowork();

Debug::trace("=====[cron][end][ketangrpt.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
