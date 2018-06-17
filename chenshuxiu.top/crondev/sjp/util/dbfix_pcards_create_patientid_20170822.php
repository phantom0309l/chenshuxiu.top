<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "3048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// 修正 pcard->create_patientid
class Dbfix_pcards_create_patientid
{

    public function dowork () {
        $this->fix_create_patientid();
    }

    public function check_xobjlog () {
        $sql = "select id from pcards";

        $pcardids = Dao::queryValues($sql);

        $cnt = count($pcardids);

        foreach ($pcardids as $i => $pcardid) {

            if ($i % 100 == 0) {
                echo "\n {$i} / {$cnt} : ";
            }

            $pcard = Pcard::getById($pcardid);

            $arr = XObjLog::getSnapByObj('Pcard', $pcardid, 1000);

            $patientid = $arr['patientid'];
            $create_patientid = $arr['create_patientid'];

            if (isset($create_patientid) && $patientid > 0 && $pcard->create_patientid != $create_patientid) {

                echo "\n [{$pcardid}] : {$pcard->create_patientid} -> {$create_patientid} \n";
                $sql = "update pcards set create_patientid={$create_patientid} where id={$pcardid} ";
                Dao::executeNoQuery($sql);
            } else {
                echo ".";
            }
        }
    }

    public function fix_create_patientid () {
        $sql = "select id from pcards ";

        $pcardids = Dao::queryValues($sql);

        $cnt = count($pcardids);

        $unitofwork = BeanFinder::get("UnitOfWork");

        foreach ($pcardids as $i => $pcardid) {

            $unitofwork = BeanFinder::get("UnitOfWork");

            if ($i % 100 == 0) {
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
                echo "\n {$i} / {$cnt} ";
            }

            $pcard = Pcard::getById($pcardid);

            $cond = "and objtype='Pcard' and objid={$pcardid} order by objver limit 1 ";

            $xobjlog = Dao::getEntityByCond('XObjLog', $cond, [], 'xworkdb');

            if ($xobjlog instanceof XObjLog) {
                $patientid = $xobjlog->getValueByKey('patientid');
                if ($pcard->create_patientid != $patientid && $patientid > 0) {
                    echo "\n {$i} / {$cnt} , {$pcardid} : ";
                    echo "{$pcard->create_patientid} <> {$patientid} \n";

                    $pcard->set4lock('create_patientid', $patientid);
                } elseif ($patientid == 0) {
                    echo "\n {$i} / {$cnt} , {$pcardid} : patientid == 0 \n";
                } else {
                    echo ".";
                }
            } else {
                echo "\n {$i} / {$cnt} , {$pcardid} : xobjlog is null \n";
            }
        }

        $unitofwork->commitAndInit();
    }
}

echo "\n===begin===\n";
$process = new Dbfix_pcards_create_patientid();
$process->dowork();
echo "\n===end===\n";
