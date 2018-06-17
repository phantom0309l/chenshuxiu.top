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

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][fill_base_msg.php]=====");

function createQuestion ($pos, $type, $content) {
    $row = array();
    $row["xquestionsheetid"] = 100935385;
    $row["pos"] = $pos;
    $row["type"] = $type;
    $row["ename"] = "";
    $row["content"] = $content;
    $row["tip"] = "";
    $row["units"] = "";
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

$sideeffect = AnswerSheet::loadYamlImp("sideeffect", "3.0");
$data = $sideeffect['sections'][0]['questions'];
$unitofwork = BeanFinder::get("UnitOfWork");

foreach ($data as $i => $a) {
    if ($i > 0) {
        // 获取问题内容
        $content = $a['desc'];
        $pos = $i + 1;
        $options = $a['options'];
        $type = "Radio";
        if (empty($options)) {
            $type = "TextArea";
        }
        $question = createQuestion($pos, $type, $content);
        $xquestionid = $question->id;

        if (! empty($options)) {
            foreach ($options as $option) {
                // 获取选项内容
                $optionContent = $option['desc'];
                createOption($xquestionid, $optionContent);
            }
        }
    }
}

$unitofwork->commitAndInit();

Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
