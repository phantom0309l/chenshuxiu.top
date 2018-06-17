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
class Prescription_auto_refuse extends CronBase
{

    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::dbfix;
        $row["when"] = 'daily';
        $row["title"] = '每天，医生开通了延伸处方(续方)审核，但是48小时以上没有审核的，自动拒绝';
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

        $sql = "select a.id
                    from prescriptions a
                    inner join doctors b on b.id = a.doctorid
                    where a.status = 1 and a.doctor_is_audit = 0 and a.shoporderid > 0 and b.is_audit_chufang = 1 and a.createtime < :createtime and a.createtime > b.audit_chufang_pass_time";
        $bind = array();
        $bind[":createtime"] = date("Y-m-d H:i:s", time() - 2*86400);
        $ids = Dao::queryValues($sql, $bind);

        foreach ($ids as $id) {
            $prescription = Prescription::getById($id);
            $prescription->refuseBySys();

            $this->cronlog_content .= "{$prescription->id}\n";
        }

        $this->cronlog_brief = count($ids);
        $this->cronlog_content = trim($this->cronlog_content);

        $unitofwork->commitAndInit();
    }
}

// //////////////////////////////////////////////////////

$process = new Prescription_auto_refuse(__FILE__);
$process->dowork();
