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
class Fix_pipe_by_nearlypipe
{

    public function dowork () {
        $cronbegintime = XDateTime::now();
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = " SELECT id FROM pipes where doctorid=0";
        //取出pipe中所有doctorid为0的pipeid
        $ids = Dao::queryValues($sql);

        $i = 0;
        foreach($ids as $id){
            $pipe = Pipe::getById($id);
            $patient = $pipe->patient;
            $wxuser = $pipe->wxuser;

            if($wxuser instanceof WxUser){
                $this->fixByWxuser($pipe);
            }else if($patient instanceof Patient){
                $this->fixByPatient($pipe);
            }

            $i++;
            if($i >= 100){
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
        }

        $unitofwork->commitAndInit();
    }

    private function fixByWxuser( $pipe ){
        $wxuser = $pipe->wxuser;
        if( $wxuser instanceof WxUser ){
            $cond = " and doctorid!=0 and wxuserid = :wxuserid and createtime <= :createtime order by createtime desc ";
            $bind = [];
            $bind[":wxuserid"] = $wxuser->id;
            $bind[":createtime"] = $pipe->createtime;
            $pipe_nearly = Dao::getEntityByCond("Pipe", $cond, $bind);
            if($pipe_nearly instanceof Pipe){
                $pipe->set4lock('doctorid', $pipe_nearly->doctorid);
                echo "=========wxuser[{$pipe->id}]=========\n";
            }
        }
    }

    private function fixByPatient( $pipe ){
        $patient = $pipe->patient;

        if( $patient instanceof Patient ){
            $cond = " and doctorid!=0 and patientid = :patientid and createtime <= :createtime order by createtime desc ";
            $bind = [];
            $bind[":patientid"] = $patient->id;
            $bind[":createtime"] = $pipe->createtime;
            $pipe_nearly = Dao::getEntityByCond("Pipe", $cond, $bind);
            if($pipe_nearly instanceof Pipe){
                $pipe->set4lock('doctorid', $pipe_nearly->doctorid);
                echo "=========patient[{$pipe->id}]=========\n";
            }
        }
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Fix_pipe_by_nearlypipe.php]=====");

$process = new Fix_pipe_by_nearlypipe();
$process->dowork();

Debug::trace("=====[cron][end][Fix_pipe_by_nearlypipe.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
