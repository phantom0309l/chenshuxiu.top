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
class Dbfix_pipetplid_in_pipe
{

    public function dowork () {
        $cronbegintime = XDateTime::now();
        $unitofwork = BeanFinder::get("UnitOfWork");
        $i = 0;

        $sql = " SELECT id from pipes where pipetplid=0";

        //取出pipe中所有pipetplid为0的pipeid
        $ids = Dao::queryValues($sql);

        $pipetplid = $pipetpl->id;

        foreach($ids as $id){
            $pipe = Pipe::getById($id);
            $objtype = $pipe->objtype;
            $objcode = $pipe->objcode;

            $pipetplid = $this->getFromCache($objtype, $objcode);
            if(false == $pipetplid){
                $pipetpl = PipeTplDao::getOneByObjtypeAndObjcode($objtype, $objcode);

                if(false == $pipetpl instanceof PipeTpl){
                    continue;
                }
                $pipetplid = $pipetpl->id;
                $this->pushCache($objtype, $objcode, $pipetplid);
            }

            echo "========={$id}============{$i}=\n";
            $pipe->pipetplid = $pipetplid;

            if($i == 1000){
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
                $i = 0;
            }

            $i++;
        }

        $unitofwork->commitAndInit();
    }

    // 内存缓存
    private static $_pipetplCache = array();

    private function pushCache($objtype, $objcode, $pipetplid){
        $key = "$objtype . '_' . $objcode";
        self::$_pipetplCache[$key] = $pipetplid;
    }

    private function getFromCache($objtype, $objcode){
        $key = "$objtype . '_' . $objcode";
        if (isset(self::$_pipetplCache[$key])) {
            return self::$_pipetplCache[$key];
        } else {
            return false;
        }
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
// Debug::trace("=====[cron][beg][Dbfix_pipetplid_in_pipe.php]=====");

$process = new Dbfix_pipetplid_in_pipe();
$process->dowork();

// Debug::trace("=====[cron][end][Dbfix_pipetplid_in_pipe.php]=====");
// Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
