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

class XAnswerSheet_split
{

    public function dowork () {

        $sql = "select id from xanswersheets where xquestionsheetid = 109273860";
        $ids = Dao::queryValues($sql);

        foreach ($ids as $id) {
            $this->doOne($id);
        }
    }

    public function doOne ($xanswersheetid) {

        echo "\n ==========={$xanswersheetid}===========";

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
            $checkuptpl = $xquestionsheet->obj;

            $row_checkup = $checkupBak->get_cols();
            $row_checkup['checkuptplid'] = $checkuptpl->id;
            $row_checkup['xanswersheetid'] = 0;
            $checkup = Checkup::createByBiz($row_checkup);

            $row = array();
            $row["wxuserid"] = $checkup->wxuserid;
            $row["userid"] = $checkup->userid;
            $row["patientid"] = $checkup->patientid;
            $row["doctorid"] = $checkup->doctorid;
            $row["xquestionsheetid"] = $xquestionsheetid;
            $row["objtype"] = 'Checkup';
            $row["objid"] = $checkup->id;
            $xanswersheet = XAnswerSheet::createByBiz($row);

            $checkup->set4lock('xanswersheetid', $xanswersheet->id);

            echo "\n";
            print_r($row_checkup);

            echo "\n";
            print_r($row);

            foreach ($xanswers as $a) {
                $a->set4lock('xanswersheetid', $xanswersheet->id);

                echo "\n {$a->id}->xanswersheetid = {$xanswersheetid} => {$xanswersheet->id}";
            }
        }

        echo "\n";

        $unitofwork->commitAndInit();
    }
}

$process = new XAnswerSheet_split();
$process->dowork();

Debug::flushXworklog();