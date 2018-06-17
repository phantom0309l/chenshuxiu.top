<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class Delete_one_papertpl extends CronBase
{
    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'rightnow';
        $row["title"] = '删除一个没有量表的量表模版';
        return $row;
    }

    // 是否记xworklog, 重载
    protected function needFlushXworklog () {
        return true;
    }

    // 是否记cronlog, 重载
    protected function needCronlog () {
        return true;
    }

    public function doWorkImp()
    {
        $unitofwork = BeanFinder::get("UnitOfWork");

        //要删除的量表模版  《BAPQ问卷（母评父废弃）》
        $papertpl = PaperTpl::getById("653173936");
        $papers = PaperDao::getListByPaperTpl($papertpl, 1);

        if(count($papers) > 0){
            echo "====此量表模版存在已填写的量表，不可删除！！！===\n";
            return;
        }

        $this->deletePapertpl($papertpl);

        $unitofwork->commitAndInit();
    }

    //删除量表模版
    private function deletePapertpl ($papertpl) {
        $this->deleteXQuestionSheet($papertpl->xquestionsheet);
        $papertpl->remove();
    }

    //删除问卷
    private function deleteXQuestionSheet ($xquestionsheet) {
        if($xquestionsheet instanceof XQuestionSheet){
            $xquestions = $xquestionsheet->getQuestions();
            foreach ($xquestions as $xquestion) {
                $this->deleteXQuestion($xquestion);
            }
            $xquestionsheet->remove();
        }
    }

    //删除问题
    private function deleteXQuestion ($xquestion) {
        if($xquestion instanceof XQuestion){
            $xoptions = $xquestion->getOptions();
            foreach ($xoptions as $xoption) {
                $xoption->remove();
            }
            $xquestion->remove();
        }
    }
}

$delete_one_papertpl = new Delete_one_papertpl(__FILE__);
$delete_one_papertpl->dowork();
