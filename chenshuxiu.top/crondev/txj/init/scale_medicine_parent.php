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

XContext::setValue("dtpl", ROOT_TOP_PATH . "/domain/tpl");

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][fill_base_msg.php]=====");

function createQuestion ($pos, $type, $content, $units = "") {
    $row = array();
    $row["xquestionsheetid"] = 100996551;
    $row["pos"] = $pos;
    $row["type"] = $type;
    $row["ename"] = "";
    $row["content"] = $content;
    $row["tip"] = "";
    $row["units"] = $units;
    $row["ismust"] = 0;
    $row["minvalue"] = 0;
    $row["maxvalue"] = 0;

    $a = XQuestion::createByBiz($row);
    return $a;
}

function createOption ($xquestionid, $content) {
    $row = array();
    $row["xquestionid"] = $xquestionid;
    $row["content"] = $content;
    $row["checked"] = 0;
    XOption::createByBiz($row);
}

$medicine_parent = AnswerSheet::loadYamlImp("medicine_parent", "2.0");
$num = 0;
$questions = $medicine_parent['sections'][0]['questions'];
$unitofwork = BeanFinder::get("UnitOfWork");

foreach ($questions as $i => $q) {
    $desc = $q['desc'];
    $type = $q['type'];
    $subs = $q['subs'];
    $unit = $q['unit'];

    if (count($subs) > 0) {
        createQuestion(0, "Section", $desc, "");

        foreach ($subs as $s) {
            $sdesc = $s['desc'];
            $stype = $s['type'];
            $soptions = $s['options'];
            $sunit = $s['unit'];

            if ($stype == "single") {
                $ntype = "Radio";
                $sunit = "";
            }
            if ($stype == "number") {
                $ntype = "Num";
            }
            if ($stype == "text") {
                $ntype = "TextArea";
                $sunit = "";
            }
            $num ++;
            $question = createQuestion($num, $ntype, $sdesc, $sunit);

            $xquestionid = $question->id;
            foreach ($soptions as $option) {
                // 获取选项内容
                $optionContent = $option['desc'];
                createOption($xquestionid, $optionContent);
            }

        }

    } else {
        if ($type == "blood_presure") {
            $ntype = "TwoText";
        }
        if ($type == "number") {
            $ntype = "Num";
        }
        if ($type == "text") {
            $ntype = "TextArea";
            $unit = "";
        }
        $num ++;
        $question = createQuestion($num, $ntype, $desc, $unit);
    }
}

$unitofwork->commitAndInit();

Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
