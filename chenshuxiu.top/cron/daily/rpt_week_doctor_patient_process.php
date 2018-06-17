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
class Rpt_week_doctor_patient_process extends CronBase
{

    private $doctorid_createtime_array = array();

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::rpt;
        $row["when"] = 'daily';
        $row["title"] = '每天, 02:30 rpt_week_doctor_patient 数据报表汇总';
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
        $begintime = XDateTime::now();

        $this->initDoctors();

        $begin_date_time = strtotime('2015-04-13');

        $nowtime = time();

        $unitofwork = BeanFinder::get("UnitOfWork");

        while ($begin_date_time < $nowtime) {

            $begindate = date("Y-m-d", $begin_date_time);
            $enddate = date("Y-m-d", $begin_date_time + 86400 * 6);
            echo "\n===== $begindate - $enddate ";

            $next_begindate_time = $begin_date_time + 86400 * 7;

            $doctorids = $this->getDoctorIdsAfterDate($begin_date_time);

            foreach ($doctorids as $doctorid) {
                $rpt = $this->calcDoctorOneWeek($doctorid, $begin_date_time);

                echo "\n";
                echo $rpt->doctorid;
                echo " : ";
                echo $rpt->scancnt;
                echo " - ";
                echo $rpt->baodaocnt;
                echo " - ";
                echo $rpt->pipe_pcnt;
            }

            $begin_date_time = $next_begindate_time;

            $unitofwork->commitAndInit();
            $unitofwork = BeanFinder::get("UnitOfWork");
        }

        $unitofwork->commitAndInit();
    }

    public function calcDoctorOneWeek ($doctorid, $begin_date_time) {
        $next_begindate_time = $begin_date_time + 86400 * 7;

        $begindate = date("Y-m-d", $begin_date_time);
        $enddate = date("Y-m-d", $begin_date_time + 86400 * 6);
        $next_begindate = date("Y-m-d", $next_begindate_time);

        $bind = [];
        $bind[':doctorid'] = $doctorid;
        $bind[':begindate'] = $begindate;
        $bind[':next_begindate'] = $next_begindate;

        $sql = "select count(*)
                from wxusers
                where doctorid=:doctorid and createtime >= :begindate and  createtime < :next_begindate  ";

        $scancnt = Dao::queryValue($sql, $bind);

        $sql = "select count(*)
                from pcards
                where doctorid=:doctorid and createtime >= :begindate and  createtime < :next_begindate  ";

        $baodaocnt = Dao::queryValue($sql, $bind);

        $sql = "select count(*) as cnt
                from ( select a.patientid,count(*) as cnt
                    from pipes a
                    inner join pcards b on b.patientid=a.patientid
                    where b.doctorid=:doctorid and a.createtime > :begindate and a.createtime < :next_begindate
                    group by a.patientid
                ) tt;";

        $pipe_pcnt = Dao::queryValue($sql, $bind);

        $rpt = Rpt_week_doctor_patientDao::getOneByDoctor($doctorid, $begindate, $enddate);
        if ($rpt instanceof Rpt_week_doctor_patient) {
            echo " == ";
            $rpt->scancnt = $scancnt;
            $rpt->baodaocnt = $baodaocnt;
            $rpt->pipe_pcnt = $pipe_pcnt;
        } else {
            echo " ++ ";
            $row = array();
            $row["doctorid"] = $doctorid;
            $row["begindate"] = $begindate;
            $row["enddate"] = $enddate;
            $row["scancnt"] = $scancnt;
            $row["baodaocnt"] = $baodaocnt;
            $row["pipe_pcnt"] = $pipe_pcnt;
            $rpt = Rpt_week_doctor_patient::createByBiz($row);
        }

        return $rpt;
    }

    // 初始化医生和创建时间
    public function initDoctors () {
        $nowtime = time();

        $sql = "select doctorid,min(createtime) as min_createtime,count(*) as cnt
            from pcards
            group by doctorid";

        $rows = Dao::queryRows($sql, []);

        foreach ($rows as $a) {
            $this->doctorid_createtime_array[$a['doctorid']] = strtotime($a['min_createtime']);
        }
    }

    // 获取某日前活跃的医生
    public function getDoctorIdsAfterDate ($thedate_time) {
        $doctorids = array();
        foreach ($this->doctorid_createtime_array as $id => $createtime) {

            if ($thedate_time > $createtime) {
                $doctorids[] = $id;
            }
        }

        return $doctorids;
    }
}

// //////////////////////////////////////////////////////

$process = new Rpt_week_doctor_patient_process(__FILE__);
$process->dowork();
