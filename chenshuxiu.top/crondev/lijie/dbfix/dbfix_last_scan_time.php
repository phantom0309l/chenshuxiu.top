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

// 修复pcards表中last_scan_time字段
class Dbfix_last_scan_time
{

    public function dowork () {
        $cronbegintime = XDateTime::now();
        $unitofwork = BeanFinder::get("UnitOfWork");
        $i = 0;

        $sql = " SELECT id FROM pcards WHERE last_scan_time='0000-00-00 00:00:00' ";

        //取出pcards表中last_scan_time字段为'0000-00-00 00:00:00'的id
        $ids = Dao::queryValues($sql);

        foreach($ids as $id){
            $pcard = Pcard::getById($id);
            $patientid = $pcard->patientid;
            $doctorid = $pcard->doctorid;

            if($i == 1000){
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
                $i = 0;
            }

            $i++;

            // 取最近一次pcard生效时间
            $pipe = PipeDao::getOneByPatientid($patientid, " and doctorid={$doctorid} and ((objtype='Patient' and objcode='baodao') or (objtype='WxUser' and objcode='scan') or (objtype='WxUser' and objcode='subscribe') or (objtype='Patient' and objcode='pass')) order by id desc ");
            if($pipe instanceof Pipe){
                $pcard->last_scan_time = $pipe->createtime;
                echo "========={$id}============{$i}=fixed\n";
            }

            echo "========={$id}============{$i}=no_fix\n";
        }

        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Dbfix_last_scan_time.php]=====");

$process = new Dbfix_last_scan_time();
$process->dowork();

Debug::trace("=====[cron][end][Dbfix_last_scan_time.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
