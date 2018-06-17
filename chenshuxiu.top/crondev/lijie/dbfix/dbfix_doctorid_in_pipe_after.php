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
class Dbfix_doctorid_in_pipe_after
{

    public function dowork () {
        $cronbegintime = XDateTime::now();
        $unitofwork = BeanFinder::get("UnitOfWork");
        $i = 0;

        $sql = " SELECT id FROM pipes WHERE doctorid=0 AND patientid!=0 ";

        //取出pipe中所有doctorid为0的pipeid
        $ids = Dao::queryValues($sql);

        foreach($ids as $id){
            $pipe = Pipe::getById($id);
            $patient = $pipe->patient;

            echo "========={$id}============{$i}=\n";
            if($patient instanceof Patient){
                //本条数据有patientid按照pcard来修
                $pcards = $patient->getPcards();
                if(count($pcards) > 1){
                    //拿离此条流数据最近的Pcard
                    $pcard_near = Dao::getEntityByCond("Pcard", " and patientid={$patient->id} and createtime<='{$pipe->createtime}' order by id desc ");
                    if($pcard_near instanceof Pcard){
                        echo "====================================={$pcard_near->id}=已修\n";
                        $pipe->set4lock('doctorid', $pcard_near->doctorid);
                    }
                }
            }

            if($i == 3000){
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
                $i = 0;
            }

            $i++;
        }

        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Dbfix_doctorid_in_pipe_after.php]=====");

$process = new Dbfix_doctorid_in_pipe_after();
$process->dowork();

Debug::trace("=====[cron][end][Dbfix_doctorid_in_pipe_after.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
