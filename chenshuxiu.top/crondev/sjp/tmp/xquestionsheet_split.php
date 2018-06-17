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

class XQuestionSheet_split
{

    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $qsheet = XQuestionSheet::getById(109273860);
        $q = $qsheet->getQuestionByEname('cancer_radio');

        echo '<pre>';

        $options = $q->getOptions();
        foreach ($options as $a) {
            $title = '分期-' . $a->content;
            $disease_name = PinyinUtil::Pinyin($a->content);
            $sn = "fenqi-" . $disease_name;

            echo "\n {$title} => $sn";

            $qsheet_try = XQuestionSheet::getBySn($sn);
            if ($qsheet_try instanceof XQuestionSheet) {

                echo " jump!";
                continue;
            }

            $row = array();
            $row["sn"] = $sn;
            $row["title"] = $title;
            $row["ishidepos"] = 1;

            // 新问卷 XQuestionSheet
            $qsheet_new = XQuestionSheet::createByBiz($row);

            // 新模板 CheckupTpl
            $row = array();
            $row["xquestionsheetid"] = $qsheet_new->id;
            $row["groupstr"] = 'fenqi';
            $row["ename"] = $sn;
            $row["title"] = $title;
            $row["status"] = 1;
            $checkupTpl = CheckupTpl::createByBiz($row);

            $qsheet_new->set4lock('objtype', 'CheckupTpl');
            $qsheet_new->set4lock('objid', $checkupTpl->id);

            $showenames = $a->showenames;
            $showenames = explode(',', $showenames);

            foreach ($showenames as $str) {
                echo "\n\t";
                $str = trim($str);
                $q = $qsheet->getQuestionByEname($str);
                echo " {$str} => {$q->id} ";

                if ($q instanceof XQuestion) {
                    $q->set4lock('xquestionsheetid', $qsheet_new->id);
                }
            }
        }

        $unitofwork->commitAndInit();
    }
}

$process = new XQuestionSheet_split();
$process->dowork();

Debug::flushXworklog();