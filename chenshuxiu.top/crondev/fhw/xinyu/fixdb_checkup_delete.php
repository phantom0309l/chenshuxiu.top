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

class Fixdb_checkup_delete
{

    public function dowork () {
        echo "[fixdb_checkup_delete begin]\n";
        $checkups = Dao::getEntityListByCond('Checkup', ' and checkuptplid > 0 and status > 0 order by updatetime desc ');

        foreach ($checkups as $a) {
            $unitofwork = BeanFinder::get("UnitOfWork");

            echo "[delete checkup id = {$a->id} status = {$a->status}]\n";
            $a->remove();
            echo "----------------------------------------------------\n";

            $unitofwork->commitAndInit();
        }

        echo "[fixdb_checkup_merge end]";
    }
}

$fixdb_checkup_delete = new Fixdb_checkup_delete();
$fixdb_checkup_delete->dowork();