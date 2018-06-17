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

// 将 from_doctor 的患者,迁移到 to_doctor 名下
// WxUser, Patient, Pcard
// 其他的表
class Dbfix_mv_patient_doctor2doctor_20170906
{

    public function dowork ($patientid, $to_doctorid, $fix_pcard = 0, $fix_pushmsg = 1) {
        echo "\n==== mv patientid[{$patientid}] to doctorid[{$to_doctorid}] ====\n";

        $this->mv_wxuser($patientid, $to_doctorid);

        $this->mv_patient('Patient', 'first_doctorid', 'id', $patientid, $to_doctorid);
        $this->mv_patient('Patient', 'doctorid', 'id', $patientid, $to_doctorid);

        if ($fix_pcard) {
            $this->mv_patient('Pcard', 'doctorid', 'patientid', $patientid, $to_doctorid);
        }

        $this->fix_patient_tables($patientid, $to_doctorid, $fix_pushmsg);
    }

    public function mv_wxuser ($patientid, $to_doctorid) {
        $sql = "select a.id from wxusers a
            inner join users b on b.id=a.userid
            where b.patientid={$patientid}";

        $ids = Dao::queryValues($sql);

        $cnt = count($ids);

        echo "\n\n==== WxUser->doctorid  : cnt = {$cnt} ==== \n";

        $unitofwork = BeanFinder::get("UnitOfWork");

        $i = 0;
        foreach ($ids as $id) {
            $i ++;
            $unitofwork = BeanFinder::get("UnitOfWork");
            $entity = Dao::getEntityById('WxUser', $id);
            $entity->set4lock('doctorid', $to_doctorid);
            echo "\n {$i} / {$cnt} : WxUser : {$id} ";
        }

        $unitofwork->commitAndInit();
    }

    public function mv_patient ($objType, $field = 'doctorid', $key, $patientid, $to_doctorid) {
        $table = strtolower($objType);
        $table .= "s";

        $sql = "select id from {$table} where {$key}={$patientid} ";

        $ids = Dao::queryValues($sql);

        $cnt = count($ids);

        echo "\n\n==== {$objType}->{$field} : cnt = {$cnt} ==== \n";

        $unitofwork = BeanFinder::get("UnitOfWork");

        $i = 0;
        foreach ($ids as $id) {
            $i ++;
            $unitofwork = BeanFinder::get("UnitOfWork");
            $entity = Dao::getEntityById($objType, $id);
            $entity->set4lock($field, $to_doctorid);
            echo "\n {$i} / {$cnt} : {$objType} : {$id} ";
        }

        $unitofwork->commitAndInit();
    }

    public function fix_patient_tables ($patientid, $to_doctorid, $fix_pushmsg = 1) {
        echo "\n==== doctorid=[{$to_doctorid}], fix_pushmsg=[{$fix_pushmsg}] ====\n";

        $jumpTables_str = "'pcardhistorys', 'pcards'";

        // 是否跳过 pushmsgs
        if (false == $fix_pushmsg) {
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
            where a.patientid = {$patientid} and a.doctorid <> {$to_doctorid} ";

            $entityType = XObjLog::table2entityType($table_name);

            if(empty($entityType))
            {
                echo "\n==== {$db_name}.{$table_name} == jump ==== \n";
                continue;
            }

            $entitys = Dao::loadEntityList($entityType, $sql, [], $db_name);

            $cnt = count($entitys);

            echo "\n==== {$db_name}.{$table_name} == {$cnt} ==== \n";

            if ('revisittkts' == $table_name) {
                echo "continue";
                continue;
            }

            foreach ($entitys as $a) {

                echo "\n{$entityType} [{$a->id}] : ";

                // PushMsg 跳过不修
                if ($a instanceof Pipe) {
                    echo " {$a->objtype} [{$a->objid}] : ";

                    if ($a->objtype == 'RevisitTkt') {
                        echo " jump ";
                        continue;
                    }

                    if (false == $fix_pushmsg && $a->objtype == 'PushMsg') {
                        echo " jump ";
                        continue;
                    }
                }

                $old_doctorid = $a->doctorid;
                $a->set4lock('doctorid', $to_doctorid);

                echo " {$old_doctorid} => {$to_doctorid}";
            }

            $unitofwork->commitAndInit();
        }
    }
}

echo "\n";
$patientids = $argv[1];
$to_doctorid = $argv[2];
if (empty($patientids) || empty($to_doctorid)) {
    echo "\nplease input patientids to_doctorid fix_pcard fix_pushmsg , 格式: 12,13 34 0|1 0|1 \n\n";
    exit();
}

$patientids = explode(',', $patientids);

$fix_pcard = isset($argv[3]) ? $argv[3] : 0;
$fix_pushmsg = isset($argv[4]) ? $argv[4] : 0;

echo "\n===begin===\n";
foreach ($patientids as $patientid) {
    $process = new Dbfix_mv_patient_doctor2doctor_20170906();
    $process->dowork($patientid, $to_doctorid, $fix_pcard, $fix_pushmsg);
}
echo "\n===end===\n";
