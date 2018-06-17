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

class XAnswerSheet_split_fix
{

    public function dowork () {

        $sql = "select distinct a.id
from xanswersheets a
inner join xanswers b on b.xanswersheetid = a.id
inner join xquestions c on c.id = b.xquestionid
inner join xquestionsheets d on d.id=c.xquestionsheetid
inner join checkuptpls x on x.xquestionsheetid = d.id
where x.groupstr = 'fenqi' and a.xquestionsheetid <> d.id
order by a.id;";
        $ids = Dao::queryValues($sql);

        foreach ($ids as $id) {
            $this->doOne($id);
        }
    }

    public function doOne ($xanswersheetid) {

        echo "\n ==========={$xanswersheetid}===========\n";

        $unitofwork = BeanFinder::get("UnitOfWork");

        $asheet = XAnswerSheet::getById($xanswersheetid);
        $checkupBak = $asheet->obj;

        $xanswers = $asheet->getAnswers();

        $xquestionsheetid_xanswers = array();

        foreach ($xanswers as $a) {
            $xquestionsheetid_xanswers[$a->xquestion->xquestionsheetid][] = $a;
        }

        foreach ($xquestionsheetid_xanswers as $xquestionsheetid => $xanswers) {
            $xquestionsheet = XQuestionSheet::getById($xquestionsheetid);

            $sql = "select distinct a.*
            from xanswersheets a
            inner join checkups b on a.objid = b.id
            where a.xquestionsheetid = $xquestionsheetid
                and b.check_date = '{$checkupBak->check_date}'
                and a.patientid = $asheet->patientid
            ";

            $asheets = Dao::loadEntityList('XAnswerSheet', $sql);

            $cnt = count($asheets);
            if ($cnt != 1) {
                echo "\n===[{$cnt}]===\n";
                continue;
            } else {
                $xanswersheet = array_shift($asheets);
            }

            foreach ($xanswers as $a) {
                $a->set4lock('xanswersheetid', $xanswersheet->id);

                echo "\n {$a->id}->xanswersheetid = {$xanswersheetid} => {$xanswersheet->id}";
            }
        }

        echo "\n";

        $unitofwork->commitAndInit();
    }
}

$process = new XAnswerSheet_split_fix();
$process->dowork();

Debug::flushXworklog();