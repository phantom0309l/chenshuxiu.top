<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "3048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class Dbfix_init_xobjlog extends DbFixBase
{

    public function dowork () {

        echo "\n [Dbfix_init_xobjlog] [start]";
        $unitofwork = BeanFinder::get("UnitOfWork");
        $unitofworkid = Debug::getUnitofworkId();

        $tableNames = $this->allTables;

        $threeidarr = array();

        foreach ($tableNames as $tableName) {

            if (in_array($tableName, array(
                'xcodes'))) {
                continue;
            }

            $this->tryCreateLogsByTable($tableName);
            $this->tryFixLogsByTable($tableName);
        }

        echo "\n [Dbfix_init_xobjlog] [end]";

    }

    private function tryFixLogsByTable ($table) {
        $objtype = $this->table2entityType($table);
        if (null == $objtype) {
            return;
        }
        echo "\n=============\n";
        echo "\n$table ";
        echo "\n=============\n";

        echo $sql = "select a.id
            from {$table} a
            inner join xworkdb.xobjlogs b on ( b.objid=a.id and b.objtype = '{$objtype}')
            where b.objtype = '{$objtype}' and b.type=3 and a.version > b.objver ";

        $ids = Dao::queryValues($sql);
        $cnt = count($ids);
        echo "\n======={$cnt} begin======\n";

        $unitofwork = BeanFinder::get("UnitOfWork");
        $i = 0;
        foreach ($ids as $id) {
            $i ++;
            $unitofwork = BeanFinder::get("UnitOfWork");
            $entity = Dao::getEntityById($objtype, $id);
            $this->entityFixXObjlog($entity, $objtype);

            echo ".";

            if ($i % 100 == 0) {
                echo "\n[{$table}] {$i}/{$cnt}";
                $unitofwork->commitAndInit();
            }
        }

        $unitofwork->commitAndInit();
        echo "\n======={$cnt} end======\n";
    }

    private function tryCreateLogsByTable ($table) {
        $objtype = $this->table2entityType($table);
        if (null == $objtype) {
            return;
        }

        echo "\n$table ";
        echo "\n=============\n";
        // echo $sql = "select id from {$table}";
        echo "\n";
        echo $sql = "select a.id
                from {$table} a
                left join xworkdb.xobjlogs b on ( b.objid=a.id and b.objtype = '{$objtype}')
                where b.id is null ";
        $ids = Dao::queryValues($sql);
        $cnt = count($ids);

        $unitofwork = BeanFinder::get("UnitOfWork");

        $openXObjLog = true;

        $i = 0;
        foreach ($ids as $id) {
            $i ++;

            if (! $openXObjLog) {
                break;
            }

            $unitofwork = BeanFinder::get("UnitOfWork");

            $entity = Dao::getEntityById($objtype, $id);
            if ($openXObjLog && $entity->notXObjLog()) {
                $openXObjLog = false;
                echo "\nopenXObjLog = false\n";
            }

            if ($openXObjLog) {
                $this->entity2xobjlog($entity, $objtype);
            }

            if ($i % 100 == 0) {
                echo "\n[{$table}] {$i}/{$cnt}";
                $unitofwork->commitAndInit();
            }
        }

        $unitofwork->commitAndInit();
    }

    private function entity2xobjlog ($entity, $objtype) {

        $content = json_encode($entity->get_cols(), JSON_UNESCAPED_UNICODE);
        $row = array();
        $row["xunitofworkid"] = Debug::getUnitofworkId();
        $row["type"] = 3;
        $row["objtype"] = $objtype;
        $row["objid"] = $entity->id;
        $row["objver"] = $entity->version;
        $row["content"] = $content;

        return XObjLog::createByBiz($row);
    }

    private function entityFixXObjlog ($entity, $objtype) {

        $sql = "select *
            from xworkdb.xobjlogs
            where objtype='{$objtype}' and objid='{$entity->id}' and type=3 ";

        $xobjlog = Dao::loadEntity('XObjLog', $sql);

        if (empty($xobjlog)) {
            return false;
        }

        $content = json_encode($entity->get_cols(), JSON_UNESCAPED_UNICODE);

        $xobjlog->set4lock('objver', $entity->version);
        $xobjlog->set4lock('content', $content);

        return true;
    }
}

echo '===init===';
$process = new Dbfix_init_xobjlog();
$process->dowork();
