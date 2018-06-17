<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// Debug::$debug = 'Dev';

class Init_diseasepapertplref
{
    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");
        $diseasePaperTplRef = DiseasePaperTplRef::getById(614662407);
        $diseasePaperTplRef->set4lock("diseaseid", 24);
        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Init_diseasepapertplref.php]=====");

$process = new Init_diseasepapertplref();
$process->dowork();

Debug::trace("=====[cron][end][Init_diseasepapertplref.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
