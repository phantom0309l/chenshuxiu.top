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

class Fixdb_xanswersheet
{

    public function dowork () {
        $sql = "select id from xanswersheets";

        $xanswersheetids = Dao::queryValues($sql);

        $cnt = count($xanswersheetids);
        $scorecnt = 0;
        $unitofwork = BeanFinder::get("UnitOfWork");
        foreach ($xanswersheetids as $k => $xanswersheetid) {
            $unitofwork = BeanFinder::get("UnitOfWork");
            echo "\n[{$k}]/[{$cnt}] - [{$scorecnt}] ";
            $xanswersheet = XAnswerSheet::getById($xanswersheetid);
            $score = 0;

            $xanswers = $xanswersheet->getAnswers();

            foreach ($xanswers as $xanswer) {
                $options = $xanswer->getTheXOptions();
                foreach ($options as $option) {
                    $score += $option->score;
                }
            }
            if( $xanswersheet->score != $score ){
                echo " {$xanswersheet->score} => {$score}";
                $scorecnt += 1;
                $xanswersheet->score = $score;
            }

            if( $k % 1000 == 0 ){
                $unitofwork->commitAndInit();
                $xanswersheet = null;
                $xanswers = null;
                $options = null;
                unset($xanswersheet);
                unset($xanswers);
                unset($options);
            }
        }
        $unitofwork->commitAndInit();

    }
}

$process = new Fixdb_xanswersheet();
$process->dowork();
