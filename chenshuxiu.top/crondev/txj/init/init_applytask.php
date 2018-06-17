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

class Init_apply
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $now = date("Y-m-d H:i:s", time());
        $sql = "select id from optasks where typestr='apply'";
        $ids = Dao::queryValues($sql);
        $i = 0;
        foreach ($ids as $id) {
            $optask = OpTask::getById($id);
            $patient = $optask->patient;
            if( $patient->hasOutPgroup() || $patient->hasDonePgroup() ){
                $optask->typestr = "applynotfirst";
            }else{
                $optask->typestr = "applyfirst";
            }
            echo "\n====[{$id}]===\n";
            $i ++;
            if ($i >= 50) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
        }

        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][init_apply.php]=====");

$process = new Init_apply();
$process->dowork();

Debug::trace("=====[cron][end][init_apply.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
