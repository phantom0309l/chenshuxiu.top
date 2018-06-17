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
class Dbfix_doctorid_in_pipe
{

    public function dowork () {
        $cronbegintime = XDateTime::now();
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = " SELECT id FROM pipes WHERE doctorid=0 AND patientid!=0 ";
        //取出pipe中所有doctorid为0的pipeid
        $ids = Dao::queryValues($sql);

        $i = 0;
        foreach($ids as $id){
            $pipe = Pipe::getById($id);
            $patient = $pipe->patient;
            //pcard 为1 和 大于1 修法不同

            // 如果是创建wxuser和关注的流，不修改
            if(($pipe->objtype=='WxUser' && $pipe->objcode=='create') || ($pipe->objtype=='WxUser' && $pipe->objcode=='subscribe')){
                continue;
            }

            if($patient instanceof Patient){
                $pcards = $patient->getPcards();
                if(count($pcards) == 1){
                    $pcard = $pcards[0];
                    $doctorid = $pcard->doctorid;
                    $pipe->set4lock('doctorid', $doctorid);
                    echo "=========pcard1[{$pipe->id}]=========\n";
                }else{
                    $this->fixWhenPcardMore( $pipe );
                }
            }

            $i++;
            if($i >= 1000){
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
        }

        $unitofwork->commitAndInit();
    }

    private function fixWhenPcardMore( $pipe ){
        $patient = $pipe->patient;

        if( $patient instanceof Patient ){
            $cond = " and objtype='WxUser' and objcode='scan' and doctorid!=0 and patientid = :patientid and createtime <= :createtime order by id desc ";
            $bind = [];
            $bind[":patientid"] = $patient->id;
            $bind[":createtime"] = $pipe->createtime;
            $pipe_scan = Dao::getEntityByCond("Pipe", $cond, $bind);
            // 先找最近的一条扫码 doctorid 来修
            if($pipe_scan instanceof Pipe){
                $pipe->set4lock('doctorid', $pipe_scan->doctorid);
                echo "=========pcardMore[{$pipe->id}]===by---subscribe======\n";
            }else {
                // 找不到最近的一条扫码，就拿第一次的关注 doctorid 来修
                $cond = " and objtype='WxUser' and objcode='subscribe' and doctorid!=0 and patientid = :patientid and createtime <= :createtime order by id desc ";
                $pipe_subscribe = Dao::getEntityByCond("Pipe", $cond, $bind);
                if($pipe_subscribe instanceof Pipe){
                    $pipe->set4lock('doctorid', $pipe_subscribe->doctorid);
                    echo "=========pcardMore[{$pipe->id}]===by---scan======\n";
                }
            }
        }
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Dbfix_doctorid_in_pipe.php]=====");

$process = new Dbfix_doctorid_in_pipe();
$process->dowork();

Debug::trace("=====[cron][end][Dbfix_doctorid_in_pipe.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
