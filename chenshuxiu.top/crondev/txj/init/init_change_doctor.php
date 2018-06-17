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

class Init_change_doctor extends DbFixBase
{
    const from_doctorid = 67;
    const to_doctorid = 1606;

    public function dowork () {

        /*$tables = $this->getTablesByCondArr(["doctorid"], ["doctorwxshoprefs", "doctorshopproductrefs", "doctordiseaserefs"]);

        foreach($tables as $table_name){
            $this->changeDoctorid($table_name);
        }*/

        $tables1 = $this->getObjtypeObjidTables();

        foreach($tables1 as $table_name){
            $this->changeObjid($table_name);
        }
    }

    private function changeDoctorid($table_name){
        $unitofwork = BeanFinder::get("UnitOfWork");
        $entityType = $this->table2entityType($table_name);
        if(empty($entityType)){
            return;
        }
        $sql = "select id from {$table_name} where doctorid = :doctorid order by id desc";
        $bind = [];
        $bind[":doctorid"] = self::from_doctorid;
        $ids = Dao::queryValues($sql, $bind);
        $cnt = count($ids);
        echo "\n-----cnt[{$cnt}][{$table_name}]-----\n";

        foreach($ids as $id){
            $entity = Dao::getEntityById($entityType, $id);
            $entity->set4lock("doctorid", self::to_doctorid);
        }
        $unitofwork->commitAndInit();
    }

    private function changeObjid($table_name){
        $unitofwork = BeanFinder::get("UnitOfWork");
        $entityType = $this->table2entityType($table_name);
        if(empty($entityType)){
            return;
        }
        $sql = "select id from {$table_name} where objtype = 'Doctor' and objid = :objid order by id desc";
        $bind = [];
        $bind[":objid"] = self::from_doctorid;
        $ids = Dao::queryValues($sql, $bind);
        $cnt = count($ids);
        echo "\n-----cnt[{$cnt}][{$table_name}]-----\n";

        foreach($ids as $id){
            $entity = Dao::getEntityById($entityType, $id);
            $entity->set4lock("objid", self::to_doctorid);
        }
        $unitofwork->commitAndInit();
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Init_change_doctor.php]=====");

$process = new Init_change_doctor();
$process->dowork();

Debug::trace("=====[cron][end][Init_change_doctor.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
