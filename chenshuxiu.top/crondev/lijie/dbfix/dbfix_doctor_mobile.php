<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class Dbfix_doctor_mobile extends CronBase
{
    // getRowForCronTab, 重载
    protected function getRowForCronTab() {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'rightnow';
        $row["title"] = '修复doctor表的mobile';
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

    public function doWorkImp() {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "SELECT id FROM doctors WHERE length(mobile) != 11 AND mobile != '';";

        $ids = Dao::queryValues($sql);

        foreach ($ids as $i => $id) {
            echo "===========[{$i}][userid:{$id}]=============\n";
            $doctor = Doctor::getById($id);

            if (0 == $i % 100) {
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }

            $mobile = $doctor->mobile;
            // 替换不可见字符   参考此处解释（外网）https://stackoverflow.com/questions/8781911/remove-non-ascii-characters-from-string
            $mobile = preg_replace('/[[:^print:]]/', '', $mobile);
            // 替换空格
            $mobile = str_replace(" ", "", $mobile);

            echo "将[userid={$id}]的mobile替换[{$doctor->mobile}=>{$mobile}]\n";
            $doctor->mobile = $mobile;
        }

        $unitofwork->commitAndInit();
    }

}

$dbfix_doctor_mobile = new Dbfix_doctor_mobile(__FILE__);
$dbfix_doctor_mobile->dowork();
