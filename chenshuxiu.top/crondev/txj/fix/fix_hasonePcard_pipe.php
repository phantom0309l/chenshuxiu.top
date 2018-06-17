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
class Fix_hasonePcard_pipe
{

    public function dowork () {
        $cronbegintime = XDateTime::now();
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = " SELECT id FROM pipes where doctorid=0 and createtime >= '2015-12-10'";
        //取出pipe中所有doctorid为0的pipeid
        $ids = Dao::queryValues($sql);

        $i = 0;
        foreach($ids as $id){
            $pipe = Pipe::getById($id);
            $patient = $pipe->patient;
            $wxuser = $pipe->wxuser;
            if($patient instanceof Patient){
                //pcard为1和大于一修法不同
                $pcards = $patient->getPcards();
                if(count($pcards) == 1){
                    $pcard = $pcards[0];
                    $doctorid = $pcard->doctorid;
                    $pipe->set4lock('doctorid', $doctorid);
                    echo "=========pcard1[{$pipe->id}]=========\n";

                    if( $wxuser instanceof WxUser ){
                        $wx_doctorid = $wxuser->doctorid;
                        if( $wx_doctorid == 0 ){
                            echo "=========wxuser[{$pipe->id}]============\n";
                            $wxuser->doctorid = $doctorid;
                        }
                    }

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
            if($pipe_scan instanceof Pipe){
                $pipe->set4lock('doctorid', $pipe_scan->doctorid);
                echo "=========pcardMore[{$pipe->id}]=========\n";
            }else{
                $this->fixByWxuser($pipe);
            }
        }
    }

    private function fixWhenNoPatient( $pipe ){
        $wxuser = $pipe->wxuser;
        if( $wxuser instanceof WxUser ){
            $cond = " and objtype='WxUser' and objcode='scan' and doctorid!=0 and wxuserid = :wxuserid and createtime <= :createtime order by id desc ";
            $bind = [];
            $bind[":wxuserid"] = $wxuser->id;
            $bind[":createtime"] = $pipe->createtime;
            $pipe_scan = Dao::getEntityByCond("Pipe", $cond, $bind);
            if($pipe_scan instanceof Pipe){
                $pipe->set4lock('doctorid', $pipe_scan->doctorid);
                echo "=========noPatient[{$pipe->id}]=========\n";
            }else{
                $this->fixByWxuser($pipe);
            }
        }
    }

    private function fixByWxuser( $pipe ){
        $wxuser = $pipe->wxuser;
        if( $wxuser instanceof WxUser ){
            $doctorid = $wxuser->doctorid;
            if( $doctorid > 0 ){
                echo "=========wxuser[{$pipe->id}]============\n";
                $pipe->set4lock('doctorid', $doctorid);
            }
        }
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Dbfix_doctorid_in_pipe.php]=====");

$process = new Fix_hasonePcard_pipe();
$process->dowork();

Debug::trace("=====[cron][end][Dbfix_doctorid_in_pipe.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
