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

class Output_5531
{

    public function dowork () {
        $paperid_arr = [497501456,538838036,538825796,497510496,497508136,541916806,539553856,539549956,
        538792036,497482646,497481546,522091986,497492426,541913526,497483146,539476856,539538376,
        539399096,497517476,497525746,539407586,541918496,539517166,539416526,541914936,539523526,497489556,539544666];
        foreach($paperid_arr as $paperid){
            $unitofwork = BeanFinder::get("UnitOfWork");
            $paper = Paper::getById($paperid);
            $writer = $paper->writer;
            $patient = $paper->patient;
            $xanswersheet = $paper->xanswersheet;
            $question_arr = array();
            $xanswer_arr = array();

            if ($paper->hasAnswerSheet()) {
                foreach ($paper->getAnswerSheet()->getAnswers() as $xanswer) {
                    if (false == $xanswer->isDefaultHide()) {
                        $question_arr[] = $xanswer->getQuestionCtr()->getQaHtmlQuestionContent();
                        $xanswer_arr[] = $xanswer->getQuestionCtr()->getQaHtmlAnswerContent();
                    }
                }
            }


            $headarr = array();
            $headarr[] = "填写时间";
            $headarr[] = "儿童姓名";
            $headarr[] = "出生日期";
            $headarr[] = "年龄";
            $headarr[] = "性别";
            $headarr[] = "量表id";
            $headarr[] = "填写人";
            $headarr = array_merge($headarr, $question_arr);


            $data = array();
            $temp = array();
            $temp[] = $paper->createtime;
            $temp[] = $patient->name;
            $temp[] = $patient->birthday;
            $temp[] = $patient->getAgeStr();
            $temp[] = $patient->getSexStrFix();
            $temp[] = $paper->id;
            $temp[] = $writer;
            $temp = array_merge($temp, $xanswer_arr);
            $data[] = $temp;
            echo "\n======{$patient->name}=======\n";

            $papertpl_title = $paper->papertpl->title;
            ExcelUtil::createForCron($data, $headarr, "/home/taoxiaojin/scale/asd/output_5531_{$papertpl_title}.xlsx");
            $unitofwork->commitAndInit();
        }
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Output_5531.php]=====");

$process = new Output_5531();
$process->dowork();

Debug::trace("=====[cron][end][Output_5531.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
