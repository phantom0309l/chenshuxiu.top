<?php
/**
 * Created by PhpStorm.
 * User: lijie
 * Date: 16-7-14
 * Time: 上午11:44
 */
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

class Output_wxtxtmsgs
{

    public function dowork () {
        $startdate = "2015-05-01";
        $startdate_add_twomonth = date("Y-m-d",strtotime("+2 months", strtotime($startdate)));
        $enddate = date("Y-m-d", time());

        // 两月以循环 导出数据
        do {
            $this->createByDate($startdate, $startdate_add_twomonth);
            $startdate = $startdate_add_twomonth;
            $startdate_add_twomonth = date("Y-m-d",strtotime("+2 months", strtotime($startdate)));
        } while ($startdate<$enddate);
    }

    private function createByDate($fromdate, $todate){

        $unitofwork = BeanFinder::get("UnitOfWork");

        //患者发送的所有文本消息，入流的 pipeid
        $sql = "select a.id as id from pipes a
            inner join patients b on b.id=a.patientid
            where a.objtype='WxTxtMsg' and b.diseaseid=1
            and left(a.createtime, 10)>='{$fromdate}'
            and left(a.createtime, 10)<'{$todate}' ";

        $ids = Dao::queryValues($sql);

        $headarr = array(
            "patientid",
            "医生姓名",
            "报到日期",
            "年龄",
            "性别",
            "患者发送文本的时间",
            "患者发送文本的详细内容",
            "医助回复时间",
            "距离此文本时间最近的一条医助回复内容（时间在患者发送文本时间之后的）"
        );

        $i = 0;

        $data = array();
        foreach($ids as $id){
            $pipe = Pipe::getById($id);
            echo "\n\n---------=========={$i}===========----- " . $id;

            $patientid = $pipe->patientid;
            $pipe_createtime = $pipe->createtime;
            $pipe_near_byauditor = PipeDao::getOneByPatientid($patientid, " and objtype='PushMsg' and objcode='byAuditor' and createtime>'{$pipe_createtime}' order by id ");

            $temp = array();
            $temp[] = $patientid;
            $temp[] = $pipe->patient->doctor->name;
            $temp[] = $pipe->patient->createtime;
            $temp[] = $pipe->patient->getAgeStr();
            $temp[] = $pipe->patient->getSexStrFix();
            $temp[] = $pipe->obj->createtime;
            $temp[] = $pipe->obj->content;
            if($pipe_near_byauditor instanceof Pipe){
                $temp[] = $pipe_near_byauditor->obj->createtime;
                $temp[] = $pipe_near_byauditor->obj->content;
            }else {
                $temp[] = "";
                $temp[] = "";
            }

            $i++;
            $data[] = $temp;
            $pipe_near_byauditor = null;
        }
        ExcelUtil::createForCron($data, $headarr, "/home/taoxiaojin/output/{$fromdate}~{$todate}.xlsx");
        $unitofwork->commitAndInit();
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Output_wxtxtmsgs.php]=====");

$process = new Output_wxtxtmsgs();
$process->dowork();

Debug::trace("=====[cron][end][Output_wxtxtmsgs.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
