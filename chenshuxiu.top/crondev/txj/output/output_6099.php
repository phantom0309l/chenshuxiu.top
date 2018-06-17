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

class Output_6099
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select id from auditors";

        $ids = Dao::queryValues($sql);
        $i = 0;
        $data = array();
        foreach ($ids as $id) {
            $i ++;
            if ($i >= 40) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
            $auditor = Auditor::getById($id);
            if( $auditor instanceof Auditor ){
                if(false == $auditor->isHasRole(['yunying', 'yunyingmgr'])){
                    continue;
                }

                if(in_array($id, [10001,10002,10003,10004,10007,10013,10014,10052,10104,10049])){
                    continue;
                }
                $temp = array();

                //运营姓名
                $temp[] = $auditor->name;

                //所属疾病组
                $temp[] = $auditor->diseasegroup->name;

                //4月2号-4月8号回复消息
                $temp[] = $this->getPushMsgCnt($auditor, "2018-04-02", "2018-04-09");

                //4月2号-4月8号电话
                $temp[] = $this->getCdrMeetingCnt($auditor, "2018-04-02", "2018-04-09");

                //4月9号-4月15号回复消息
                $temp[] = $this->getPushMsgCnt($auditor, "2018-04-09", "2018-04-16");


                //4月9号-4月15号电话
                $temp[] = $this->getCdrMeetingCnt($auditor, "2018-04-09", "2018-04-16");


                //4月16号-4月22号回复消息
                $temp[] = $this->getPushMsgCnt($auditor, "2018-04-16", "2018-04-23");


                //4月16号-4月22号电话
                $temp[] = $this->getCdrMeetingCnt($auditor, "2018-04-16", "2018-04-23");


                //4月23号-4月29号回复消息
                $temp[] = $this->getPushMsgCnt($auditor, "2018-04-23", "2018-04-30");


                //4月23号-4月29号电话
                $temp[] = $this->getCdrMeetingCnt($auditor, "2018-04-23", "2018-04-30");


                $data[] = $temp;
            }
        }
        $headarr = array(
            "姓名",
            "所属疾病组",
            "4月2号-4月8号回复消息",
            "4月2号-4月8号电话",
            "4月9号-4月15号回复消息",
            "4月9号-4月15号电话",
            "4月16号-4月22号回复消息",
            "4月16号-4月22号电话",
            "4月23号-4月29号回复消息",
            "4月23号-4月29号电话",
        );
        ExcelUtil::createForCron($data, $headarr, "/home/taoxiaojin/scale/output_6099.xlsx");
        $unitofwork->commitAndInit();
    }

    private function getPushMsgCnt($auditor, $startdate, $enddate){
        $sql = "select
                    count(*) as cnt
                from pushmsgs where send_by_objtype = 'Auditor' and send_by_objid = :auditorid 
                and createtime >= :startdate and createtime < :enddate";

        $bind = [];
        $bind[":auditorid"] = $auditor->id;
        $bind[":startdate"] = $startdate;
        $bind[":enddate"] = $enddate;
        return ( Dao::queryValue($sql, $bind) + 0 );
    }

    private function getCdrMeetingCnt($auditor, $startdate, $enddate){
        $sql = "select
                    count(*) as cnt
                from cdrmeetings where auditorid = :auditorid 
                and createtime >= :startdate and createtime < :enddate";

        $bind = [];
        $bind[":auditorid"] = $auditor->id;
        $bind[":startdate"] = $startdate;
        $bind[":enddate"] = $enddate;
        return ( Dao::queryValue($sql, $bind) + 0 );
    }


}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Output_6099.php]=====");

$process = new Output_6099();
$process->dowork();

Debug::trace("=====[cron][end][Output_6099.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
