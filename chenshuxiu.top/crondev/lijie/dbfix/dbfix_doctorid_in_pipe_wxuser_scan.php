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

// 修复pipes表中doctorid为０的数据
class Dbfix_doctorid_in_pipe_wxuser_scan
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = " SELECT a.id FROM pipes a
            INNER JOIN wxusers b ON b.id=a.wxuserid
            WHERE a.doctorid=0 AND b.wxshopid!=3
            AND a.objtype='WxUser' AND a.objcode='scan'
            ORDER BY a.id DESC ";

        //取出pipe中所有doctorid为0且objtype='WxUser',objcode='scan'的pipeid
        $ids = Dao::queryValues($sql);

        foreach($ids as $id){
            echo "========={$id}=============\n";
            $pipe = Pipe::getById($id);

            $wxuser = $pipe->wxuser;
            if($wxuser->user->patientid == 0){
                $pipe->set4lock('doctorid', $wxuser->doctorid);
            }else{
                $pipe->set4lock('doctorid', $pipe->patient->doctorid);
            }

            $unitofwork->commitAndInit();
            $unitofwork = BeanFinder::get("UnitOfWork");
        }

        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Dbfix_doctorid_in_pipe_wxuser_scan.php]=====");

$process = new Dbfix_doctorid_in_pipe_wxuser_scan();
$process->dowork();

Debug::trace("=====[cron][end][Dbfix_doctorid_in_pipe_wxuser_scan.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
