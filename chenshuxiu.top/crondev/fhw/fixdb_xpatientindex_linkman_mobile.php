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

// fhw address
class Fixdb_xpatientindex_linkman_mobile
{
    public function doWork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select id from patients ";
        $ids = Dao::queryValues($sql);
        $cnt = count($ids);

        $i = 0;
        $k = 0;
        foreach ($ids as $id) {
            // 删除旧的
            $sql = "delete from xpatientindexs where patientid = {$id} and type = 'mobile' ";
            Dao::executeNoQuery($sql);

            // 获取新的
            $sql = "select mobile from linkmans where patientid = {$id} ";
            $mobiles = Dao::queryValues($sql);
            foreach ($mobiles as $mobile) {
                $mobile4 = substr($mobile, -4);

                $row = [];
                $row["word"] = $mobile;
                $row["type"] = 'mobile';
                $row["patientid"] = $id;
                $row["refresh_time"] = date("Y-m-d H:i:s");
                XPatientIndex::createByBiz($row);

                $row = [];
                $row["word"] = $mobile4;
                $row["type"] = 'mobile';
                $row["patientid"] = $id;
                $row["refresh_time"] = date("Y-m-d H:i:s");
                XPatientIndex::createByBiz($row);
            }

            $i++;
            if ($i % 100 == 0) {
                $k += 100;
                echo $k . "/" . $cnt . "\n";
                $unitofwork->commitAndInit();
            } else {
                echo ".";
            }
        }
        echo "{$cnt}/{$cnt}\n";

        $unitofwork->commitAndInit();
    }
}

$test = new Fixdb_xpatientindex_linkman_mobile();
$test->doWork();
