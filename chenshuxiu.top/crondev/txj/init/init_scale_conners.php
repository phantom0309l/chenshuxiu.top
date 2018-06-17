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

class Conner_initer
{

    public function dowork () {
        $ids = $this->getAnswersheetids();
        $ename = "conners";
        $papertpl = PaperTplDao::getByEname($ename);

        foreach ($ids as $id) {
            echo "\n\n{$id}";
            $paper = Paper::getById($id);
            if ($paper instanceof Paper) {
                echo " ==jump";
                continue;
            }
            $unitofwork = BeanFinder::get("UnitOfWork");
            $answersheet = AnswerSheet::getById($id);
            $userid = $answersheet->userid;
            $user = User::getById($userid);
            echo " [{$user->id}]";
            if (($user instanceof User)) {

                // 生成paper
                $paper = $this->createPaper($answersheet, $user, $papertpl);
                echo " +";

                // 生成paper后替换掉流里对应的AnswerSheet;
                $this->fixPipe($answersheet, $paper);
                echo "+";

                // 把 answers的数据导入xanswers
                $objtype = "Paper";
                $objid = $paper->id;
                $xanswersheet = $this->createXAnswerSheet($answersheet, $user, $papertpl, $objtype, $objid);
                echo "+";

                // 把paper的xanswersheetid补上
                $paper->set4lock("xanswersheetid", $xanswersheet->id);

                // 生成xanswers;
                $this->createXAnswers($xanswersheet, $answersheet);

            }
            $unitofwork->commitAndInit();

        }
    }

    public function getAnswersheetids () {
        return Dao::queryValues("select id from answersheets where qsheet_name='conners' and qsheet_ver in ('2.0')");
    }

    public function createPaper ($answersheet, $user, $papertpl) {
        $row = array();
        $row["id"] = $answersheet->id;
        $row["createtime"] = $answersheet->createtime;
        $row["updatetime"] = $answersheet->updatetime;
        $row["patientid"] = $user->patientid;
        $row["userid"] = $user->id;
        $row["wxuserid"] = $answersheet->wxuserid;
        $row["papertplid"] = $papertpl->id;
        $row["groupstr"] = $papertpl->groupstr;
        $row["ename"] = $papertpl->ename;
        $row["writer"] = $answersheet->writer;

        $paper = Paper::createByBiz($row);
        return $paper;
    }

    public function fixPipe ($answersheet, $paper) {
        $pipe = PipeDao::getByEntity($answersheet);
        if ($pipe instanceof Pipe) {
            // $pipe->objtype = "Paper";
            // $pipe->set4lock("objid", $paper->id);
            $pipe->objcode = 'scale';
        }
    }

    public function createXAnswerSheet ($answersheet, $user, $papertpl, $objtype, $objid) {
        $row = array();
        $row["id"] = $answersheet->id;
        $row["createtime"] = $answersheet->createtime;
        $row["updatetime"] = $answersheet->updatetime;
        $row["patientid"] = $user->patientid;
        $row["userid"] = $user->id;
        $row["wxuserid"] = $answersheet->wxuserid;
        $row["xquestionsheetid"] = $papertpl->xquestionsheetid;
        $row["objtype"] = $objtype;
        $row["objid"] = $objid;
        $xanswersheet = XAnswerSheet::createByBiz($row);
        return $xanswersheet;
    }

    public function createXAnswers ($xanswersheet, $answersheet) {
        $xquestionsheet = $xanswersheet->xquestionsheet;
        foreach ($xquestionsheet->getQuestions() as $q) {
            $pos = $q->pos;
            $answer = $this->getAnswer($answersheet->id, $pos - 1);
            $row = array();
            $row["createtime"] = $answersheet->createtime;
            $row["updatetime"] = $answersheet->updatetime;
            $row["xanswersheetid"] = $xanswersheet->id;
            $row["xquestionid"] = $q->id;
            $row["pos"] = $pos;
            $row["content"] = $this->getXAnswerContent($answer);
            $xanswer = XAnswer::createByBiz($row);
            echo "-";

            $this->createXAnswerOptionRef($xanswer, $answer);
        }
    }

    public function getAnswer ($answersheetid, $question_index) {
        return Dao::getEntityByCond("Answer", "AND answersheetid = {$answersheetid} AND question_index = {$question_index}");
    }

    public function getXAnswerContent ($answer) {
        $content = "";
        if ($answer instanceof Answer) {
            if ($answer->qtype == 'text') {
                $content = $answer->text;
            }
        }
        return $content;
    }

    public function createXAnswerOptionRef ($xanswer, $answer) {
        if (false == $answer instanceof Answer) {
            echo " ";
            return;
        }

        if ($answer->qtype != 'single') {
            echo "s";
            return;
        }
        $xquestion = $xanswer->xquestion;
        $text = $answer->text;
        $xoption = XOption::getByXQuestionidAndContent($xquestion->id, $text);
        if (false == $xoption instanceof XOption) {
            echo "x";
            return;
        }

        $row = array();
        $row["createtime"] = $answer->createtime;
        $row["updatetime"] = $answer->updatetime;
        $row["xanswerid"] = $xanswer->id;
        $row["xoptionid"] = $xoption->id;
        $row["content"] = $text;
        $xansweroptionref = XAnswerOptionRef::createByBiz($row);
        echo ".";
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][init_scale_conners.php]=====");

$process = new Conner_initer();
$process->dowork();

Debug::trace("=====[cron][end][init_scale_conners.php]=====");

Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
