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

class Fixdb_checkuptpl
{

    public function dowork () {

        echo "\n [Fixdb_checkuptpl] begin ";
        $unitofwork = BeanFinder::get("UnitOfWork");
        $checkuptpls = Dao::getEntityListByCond('CheckupTpl'," and doctorid=32 ");

        foreach ($checkuptpls as $checkuptpl) {
            echo "\nCheckupTplid {$checkuptpl->id}";

            $cond = ' AND title = :title AND diseaseid = :diseaseid AND doctorid = 0 ';
            $bind = array(
                ':title' => $checkuptpl->title,
                ':diseaseid' => $checkuptpl->diseaseid);
            $checkuptplNew = Dao::getEntityByCond('CheckupTpl', $cond, $bind);

            if ($checkuptplNew instanceof CheckupTpl) {
                continue;
            } else {
                $checkuptpl->copyOne($checkuptpl->diseaseid, 0);
            }

        }
        $unitofwork->commitAndInit();

        echo "\n [Fixdb_checkuptpl] finished \n";

    }
}

$process = new Fixdb_checkuptpl();
$process->dowork();
