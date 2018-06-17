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

class Dbfix_ismust extends CronBase
{
    // getRowForCronTab, 重载
    protected function getRowForCronTab () {
        $row = array();
        $row["type"] = CronTab::pushmsg;
        $row["when"] = 'rightnow';
        $row["title"] = '修改所有李斐定制的量表模版中的题为必选！';
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

        //李斐
        $doctorid = 31;
        $doctor = Doctor::getById($doctorid);
        $papertpls = PaperTplDao::getListByDiseaseid(24, " and b.doctorid = {$doctorid} ");

        if(count($papertpls) == 0){
            echo "====没有找到！！！===\n\n";
            return;
        }

        foreach ($papertpls as $papertpl) {
            echo "====当前处理【papertplid = {$papertpl->id}】===\n";
            $xquestionsheet = $papertpl->xquestionsheet;
            if($xquestionsheet instanceof XQuestionSheet){
                //当前问卷的所有问题
                $xquestions = $xquestionsheet->getQuestions();
                foreach ($xquestions as $xquestion) {
                    //标题 或 段落 跳过
                    if('Caption' == $xquestion->type || 'Section' == $xquestion->type){
                        continue;
                    }
                    $xquestion->ismust = 1;
                }
            }
        }

        $unitofwork->commitAndInit();
    }

}

$dbfix_ismust = new Dbfix_ismust(__FILE__);
$dbfix_ismust->dowork();
