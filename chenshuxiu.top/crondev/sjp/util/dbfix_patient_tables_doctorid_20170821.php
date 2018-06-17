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

// 修正 patient.doctorid = 1296, 的各个患者表(table<patientid, doctorid>)的doctorid
class Dbfix_patient_tables_doctorid
{

    public function dowork () {
        $new_doctorid = 1296;
        $jumpPushMsg = false;

        echo "\n==== doctorid=[{$new_doctorid}], jumpPushMsg=[{$jumpPushMsg}] ====\n";

        $jumpTables_str = "'pcardhistorys', 'pcards', 'revisittkts'";
        if ($jumpPushMsg) {
            $jumpTables_str .= ",'pushmsgs'";
        }

        $sql = "select a.TABLE_SCHEMA as db_name, a.TABLE_NAME as table_name
            from information_schema.COLUMNS a
            inner join information_schema.COLUMNS b on b.TABLE_NAME = a.TABLE_NAME
            where a.COLUMN_NAME='doctorid' and b.COLUMN_NAME = 'patientid' and a. TABLE_NAME not in ({$jumpTables_str})";

        $rows = Dao::queryRows($sql);

        foreach ($rows as $row) {
            $unitofwork = BeanFinder::get("UnitOfWork");

            $db_name = $row['db_name'];
            $table_name = $row['table_name'];

            $sql = "select a.*
                from {$db_name}.{$table_name} a
                inner join fcqxdb.patients b on b.id = a.patientid
                where b.doctorid = {$new_doctorid} and a.doctorid <> {$new_doctorid} ";

            $entityType = XObjLog::table2entityType($table_name);

            $entitys = Dao::loadEntityList($entityType, $sql, [], $db_name);

            $cnt = count($entitys);

            echo "\n==== {$db_name}.{$table_name} == {$cnt} ==== \n";

            foreach ($entitys as $a) {

                echo "\n{$entityType} [{$a->id}] : ";

                // PushMsg 跳过不修
                if ($a instanceof Pipe) {
                    echo " {$a->objtype} [{$a->objid}] : ";

                    if ($a->objtype == 'RevisitTkt') {
                        echo " jump ";
                        continue;
                    }

                    if ($jumpPushMsg && $a->objtype == 'PushMsg') {
                        echo " jump ";
                        continue;
                    }
                }

                $old_doctorid = $a->doctorid;
                $a->set4lock('doctorid', $new_doctorid);

                echo " {$old_doctorid} => {$new_doctorid}";
            }

            $unitofwork->commitAndInit();
        }
    }
}

echo "\n===begin===\n";
$process = new Dbfix_patient_tables_doctorid();
$process->dowork();
echo "\n===end===\n";
