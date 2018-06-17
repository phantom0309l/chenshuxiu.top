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
class Optask_nobuy_follow extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::optask;
        $row["when"] = 'daily';
        $row["title"] = '每天, 未购药跟进';
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
        // 已开通 sunflower 项目的医生的患者，打上 非 sunflower 项目标签后，次日10：00前生成；
        $this->createOptaskWhenDoctorInSunflower();

        // 未开通 sunflower 项目的医生的患者，报到第二天10：00前生成（日期计算规则为：第0天，第1天，第2天）
        $this->createOptaskWhenDoctorNotInSunflower();
    }

    private function createOptaskWhenDoctorInSunflower () {
        $time = time();
        $start_date = date("Y-m-d", $time - 1 * 86400);
        $end_date = date("Y-m-d", $time);

        $sql = "select a.id from patients a
                    inner join doctors b on b.id = a.doctorid
                    inner join doctor_hezuos c on c.doctorid = b.id
                    inner join (
                        select createtime, objid from tagrefs where objtype = 'Patient' and tagid in (176, 177, 178, 179) group by objid
                    )tt on tt.objid = a.id
                    where b.menzhen_offset_daycnt = 1 and a.diseaseid=1 and c.status = 1
                    and tt.createtime >= :start_date and tt.createtime < :end_date";
        $bind = [];
        $bind[":start_date"] = $start_date;
        $bind[":end_date"] = $end_date;
        $ids = Dao::queryValues($sql, $bind);
        $this->createOptaskImp($ids);
    }

    private function createOptaskWhenDoctorNotInSunflower () {
        $time = time();
        $start_date = date("Y-m-d", $time - 2 * 86400);
        $end_date = date("Y-m-d", $time - 1 * 86400);
        $sql = "select a.id
                    from patients a
                    inner join doctors b on b.id = a.doctorid
                    left join doctor_hezuos c on c.doctorid = b.id
                    where b.menzhen_offset_daycnt = 1 and a.diseaseid=1 and (c.id is null or c.status=0)
                    and a.createtime >= :start_date and a.createtime < :end_date";
        $bind = [];
        $bind[":start_date"] = $start_date;
        $bind[":end_date"] = $end_date;
        $ids = Dao::queryValues($sql, $bind);
        $this->createOptaskImp($ids);
    }

    private function createOptaskImp ($ids) {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $i = 0;
        foreach ($ids as $id) {
            $i ++;
            if ($i >= 50) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }

            $patient = Patient::getById($id);
            if ($patient instanceof Patient) {
                // 生成任务: 未购药跟进 (患者唯一)
                OpTaskService::tryCreateOpTaskByPatient($patient, 'nobuy:follow', null, '', 1);
            }
        }
        $unitofwork->commitAndInit();
    }
}

// //////////////////////////////////////////////////////

$process = new Optask_nobuy_follow(__FILE__);
$process->dowork();
