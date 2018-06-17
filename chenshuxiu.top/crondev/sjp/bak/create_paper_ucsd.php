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

class create_paper_ucsd
{

    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $qsheet = XQuestionSheet::getBySn('ucsd');

        $op_contents[] = '没有';
        $op_contents[] = '轻微';
        $op_contents[] = '有些';
        $op_contents[] = '较重';
        $op_contents[] = '重度';
        $op_contents[] = '极度';

        foreach ($qsheet->getQuestions() as $a) {

            $options = $a->getOptions();
            foreach ($options as $i => $op) {
                $op->content = $op_contents[$i];
            }

        }

        $unitofwork->commitAndInit();
    }
}

$process = new create_paper_ucsd();
$process->dowork();