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
Debug::trace("=====[cron][beg][sendcuipgtemp.php]=====");

class Sendcuipgtemp
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $allsql = "select id from patients where doctorid=5 and status=1 and subscribe_cnt>0 ";
        $activesql = "select a.id as id from patients a
                        inner join
                        (select objid from comments where objtype='Patient' and typestr='Urge[Assess]' and createtime > '2016-05-02' group by objid)t
                        on a.id=t.objid
                        where a.doctorid=5";
        $allids = Dao::queryValues($allsql);
        // $allids = array(103142233);
        $activeids = Dao::queryValues($activesql);
        $i = 0;
        foreach ($allids as $id) {
            $patient = Patient::getById($id);
            if ($patient instanceof Patient && in_array($id, $activeids) == false) {
                $arr = array(
                    "patient" => $patient,
                    "objtype" => "",
                    "objid" => "");
                PushMsgService::send_scale_template($arr);
            }
            echo "\n====[{$id}]===\n";
            $i ++;
            if ($i >= 50) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
        }
        $unitofwork->commitAndInit();
    }
}

Debug::trace("=====[cron][end][sendcuipgtemp.php]=====");
$process = new Sendcuipgtemp();
$process->dowork();
Debug::flushXworklog();
echo "\n\n-----end----- \n" . XDateTime::now();
