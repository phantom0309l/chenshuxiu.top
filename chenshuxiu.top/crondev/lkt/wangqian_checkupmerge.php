<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2017/6/5
 * Time: 15:56
 */
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

echo "\n\n-----begin----- " . XDateTime::now() . "\n\n";
$unitofwork = BeanFinder::get("UnitOfWork");

/*
 * 炎性指标：checkuptplid = 257137226
 * IGg：xquestionid = 104372439
 * IGM：xquestionid = 104372469
 * IGA：xquestionid = 104372481
 * C3：xquestionid=104372603
 * C4：xquestionid=104372613
 *
 * ESR：xquestionid = 257139386
 * CRP: xquestionid = 257139756
 * 铁蛋白：xquestionid = 257140646
 */

// 炎性指标
const CHECKUPTPLID = 257137226; // 测试库
//const CHECKUPTPLID = 257137226;   // 线上库
$checkuptpl = CheckupTpl::getById(CHECKUPTPLID);
$xquestionsheetid = $checkuptpl->xquestionsheetid;

$xquestions = $checkuptpl->xquestionsheet->getQuestions();

$xquestionids = [];
foreach ($xquestions as $xquestion) {
    $xquestionids[$xquestion->content] = $xquestion->id;
}

// 抗体：104372403
// 补体：104372573
$sql = "SELECT *
        FROM checkups
        WHERE checkuptplid IN (104372573, 104372403)
        AND doctorid = 32";
$checkups = Dao::loadEntityList("Checkup", $sql);

$checkup_groups = [];
foreach ($checkups as $checkup) {
    $patientid = $checkup->patientid;
    $check_date = $checkup->check_date;

    $arr_p = $checkup_groups[$patientid] ?? [];
    $arr_c = $arr_p[$check_date] ?? [];

    $arr_c[] = $checkup;

    $arr_p[$check_date] = $arr_c;
    $checkup_groups[$patientid] = $arr_p;
}

foreach ($checkup_groups as $patientid => $checkup_p) {
    foreach ($checkup_p as $check_date => $checkup_c) {
        $arr = [];
        foreach ($checkup_c as $checkup) {
            //
            $old_questionsheet = $checkup->checkuptpl->xquestionsheet;
            if (false == $old_questionsheet instanceof XQuestionSheet) {
                echo "\n找不到问卷 checkupid = {$checkup->id}";
                continue;
            }

            $old_questions = $old_questionsheet->getQuestions();
            if (empty($old_questions)) {
                echo "\n找不到问题 checkupid = {$checkup->id}";
                continue;
            }

            $xanswersheet = $checkup->xanswersheet;
            if (false == $checkup->xanswersheet instanceof XAnswerSheet) {
                echo "\n找不到答卷 checkupid = {$checkup->id}";
                continue;
            }

            foreach ($old_questions as $old_question) {
                $answer = $xanswersheet->getAnswer($old_question->id);
                if (false == $answer instanceof XAnswer) {
                    echo "\n找不到答案 checkupid = {$checkup->id}";
                    continue;
                }
                $content = $answer->content;

                $xquestionid = $xquestionids[$old_question->content];
                $arr[$xquestionid] = [
                    "content" => $content
                ];
            }
        }
        if (empty($arr)) {
            continue;
        }
        $sheets = [
            "XQuestionSheet" => [
                $xquestionsheetid => $arr
            ]
        ];

        $row = array();
        $row["doctorid"] = 32;
        $row["checkuptplid"] = CHECKUPTPLID;
        $row["patientid"] = $patientid;
        $row["check_date"] = $check_date;

        $new_checkup = Checkup::createByBiz($row);
        echo "\n new checkup {$new_checkup->id}";
        $pipe = Pipe::createByEntity($new_checkup);

        $maxXAnswer = XWendaService::doPost($sheets, $new_checkup->patient->createuser, 'Checkup', $new_checkup->id);
        $new_checkup->xanswersheetid = $maxXAnswer->xanswersheetid;
    }
}

echo "\n\n";
$unitofwork->commitAndInit();
