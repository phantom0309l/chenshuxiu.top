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
class Init_marketer_name
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = " SELECT id FROM doctor_hezuos
            WHERE marketer_name='' ";

        $ids = Dao::queryValues($sql);

        foreach($ids as $id){
            $doctor_hezuo = Doctor_hezuo::getById($id);
            $json = $doctor_hezuo->json;

            if($json == ""){
                continue;
            }

            $json_obj = json_decode($json);
            $territory_name = $json_obj->territory_name;

            $doctor_hezuo->marketer_name = $this->getMarketer_name($territory_name);

            $unitofwork->commitAndInit();
            $unitofwork = BeanFinder::get("UnitOfWork");
        }

        $unitofwork->commitAndInit();
    }

    private function getMarketer_name ($territory_name) {
        $marketer_name = $territory_name;
        if(strstr($territory_name, "STR")){
            preg_match_all("/(?:\()(.*)(?:\))/i",$territory_name, $result);
            $marketer_name = $result[1][0];
        }
        return $marketer_name;
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Init_marketer_name.php]=====");

$process = new Init_marketer_name();
$process->dowork();

Debug::trace("=====[cron][end][Init_marketer_name.php]=====");
// Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
